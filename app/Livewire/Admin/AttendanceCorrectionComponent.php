<?php

namespace App\Livewire\Admin;

use App\Models\Attendance;
use App\Models\AttendanceCorrectionRequest;
use App\Models\AttendanceEditLog;
use App\Services\AttendanceCalculator;
use App\Services\ShiftSchedule;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Laravel\Jetstream\InteractsWithBanner;
use Livewire\Component;
use Livewire\WithPagination;

class AttendanceCorrectionComponent extends Component
{
    use InteractsWithBanner, WithPagination;

    public string $filter = 'pending';

    public bool $confirmingApproval = false;

    public ?int $approvingId = null;

    public string $approvingName = '';

    public ?int $rejectingId = null;

    public ?string $rejectReason = null;

    public function confirmApprove($id)
    {
        $request = AttendanceCorrectionRequest::with('user')->findOrFail($id);
        $this->approvingId = $id;
        $this->approvingName = $request->user->name;
        $this->confirmingApproval = true;
    }

    public function executeApprove()
    {
        $request = AttendanceCorrectionRequest::with('user')->findOrFail($this->approvingId);
        $admin = Auth::user();

        DB::transaction(function () use ($request, $admin) {
            $attendance = Attendance::where('user_id', $request->user_id)
                ->whereDate('date', $request->date)
                ->first();

            $before = $attendance?->only(['date', 'time_in', 'time_out', 'status']) ?? [];

            if (! $attendance) {
                $attendance = Attendance::create([
                    'user_id' => $request->user_id,
                    'date' => $request->date->toDateString(),
                    'time_in' => $request->time_in,
                    'time_out' => $request->time_out,
                    'status' => 'present',
                    'note' => 'Koreksi absensi: '.$request->reason,
                    'corrected_by' => $admin->id,
                    'corrected_at' => now(),
                    'correction_note' => $request->reason,
                ]);
            } else {
                $attendance->fill([
                    'time_in' => $request->time_in ?: $attendance->getRawOriginal('time_in'),
                    'time_out' => $request->time_out ?: $attendance->getRawOriginal('time_out'),
                    'corrected_by' => $admin->id,
                    'corrected_at' => now(),
                    'correction_note' => $request->reason,
                ]);

                $attendance->load('shift');
                $status = $attendance->status;

                if ($attendance->shift && $request->time_in) {
                    $calculator = app(AttendanceCalculator::class);
                    $late = $calculator->lateMinutes($attendance) > 0;

                    if ($attendance->time_out) {
                        $end = ShiftSchedule::endAt($attendance->shift, $attendance->date);
                        $out = Carbon::parse($attendance->date->format('Y-m-d').' '.$attendance->getRawOriginal('time_out'));
                        if ($out->lessThan($end)) {
                            $status = 'incomplete';
                        } else {
                            $status = $late ? 'late' : 'present';
                        }
                    } else {
                        $status = $late ? 'late' : 'present';
                    }
                } elseif ($request->time_in || $request->time_out) {
                    $status = 'present';
                }

                $attendance->status = $status;
                $attendance->save();
                app(AttendanceCalculator::class)->apply($attendance);
            }

            AttendanceEditLog::create([
                'attendance_id' => $attendance->id,
                'edited_by' => $admin->id,
                'changes' => [
                    'before' => $before,
                    'after' => $attendance->only(['date', 'time_in', 'time_out', 'status']),
                ],
                'reason' => $request->reason,
            ]);

            $request->update([
                'status' => 'approved',
                'reviewed_by' => $admin->id,
                'reviewed_at' => now(),
            ]);

            Attendance::clearUserAttendanceCache($request->user, Carbon::parse($request->date));
        });

        $this->confirmingApproval = false;
        $this->approvingId = null;
        $this->banner('Koreksi disetujui dan absensi diperbarui.');
    }

    public function confirmReject($id)
    {
        $this->rejectingId = $id;
        $this->rejectReason = null;
    }

    public function reject()
    {
        $this->validate(['rejectReason' => 'required|string|max:255']);

        AttendanceCorrectionRequest::where('id', $this->rejectingId)->update([
            'status' => 'rejected',
            'reviewed_by' => Auth::user()->id,
            'reviewed_at' => now(),
            'rejection_reason' => $this->rejectReason,
        ]);

        $this->rejectingId = null;
        $this->rejectReason = null;
        $this->banner('Pengajuan koreksi ditolak.');
    }

    public function updatingFilter()
    {
        $this->resetPage();
    }

    public function render()
    {
        return view('livewire.admin.attendance-corrections', [
            'requests' => AttendanceCorrectionRequest::with('user')
                ->when($this->filter !== 'all', fn ($query) => $query->where('status', $this->filter))
                ->latest()
                ->paginate(20),
            'pendingCount' => AttendanceCorrectionRequest::where('status', 'pending')->count(),
        ]);
    }
}
