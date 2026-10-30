<?php

namespace App\Livewire\Traits;

use App\Models\Attendance;
use App\Models\AttendanceEditLog;
use App\Services\AttendanceCalculator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

trait AttendanceDetailTrait
{
    public bool $showDetail = false;

    public $currentAttendance = [];

    public bool $showCorrection = false;

    public $correction = [
        'date' => null,
        'shift_id' => null,
        'time_in' => null,
        'time_out' => null,
        'status' => null,
        'reason' => null,
    ];

    public function show($attendanceId)
    {
        /** @var Attendance|null $attendance */
        $attendance = Attendance::with(['user', 'barcode', 'shift', 'corrector', 'editLogs.editor'])->find($attendanceId);
        if (! $attendance) {
            return;
        }

        $this->showDetail = true;
        $this->currentAttendance = $attendance->getAttributes();
        $this->currentAttendance['name'] = $attendance->user->name;
        $this->currentAttendance['nip'] = $attendance->user->nip;
        $this->currentAttendance['address'] = $attendance->user->address;
        $this->currentAttendance['corrected_by_name'] = $attendance->corrector?->name;
        $this->currentAttendance['edit_logs'] = $attendance->editLogs
            ->sortByDesc('created_at')
            ->values()
            ->map(fn (AttendanceEditLog $log) => [
                'editor' => $log->editor?->name ?? '-',
                'reason' => $log->reason,
                'created_at' => $log->created_at?->format('d/m/Y H:i'),
            ])
            ->all();

        if ($attendance->attachment) {
            $this->currentAttendance['attachment'] = $attendance->attachment_url;
        }
        if ($attendance->barcode_id) {
            $this->currentAttendance['barcode'] = $attendance->barcode;
        }
        if ($attendance->shift_id) {
            $this->currentAttendance['shift'] = $attendance->shift;
        }
    }

    public function openCorrection()
    {
        $this->ensureAdmin();

        $attendance = Attendance::findOrFail($this->currentAttendance['id']);
        $this->correction = [
            'date' => $attendance->date?->format('Y-m-d'),
            'shift_id' => $attendance->shift_id,
            'time_in' => $this->timeForInput($attendance->getRawOriginal('time_in')),
            'time_out' => $this->timeForInput($attendance->getRawOriginal('time_out')),
            'status' => $attendance->status,
            'reason' => $attendance->correction_note,
        ];
        $this->resetErrorBag();
        $this->showCorrection = true;
    }

    public function saveCorrection()
    {
        $this->ensureAdmin();

        $this->validate([
            'correction.date' => ['required', 'date_format:Y-m-d'],
            'correction.shift_id' => ['nullable', 'exists:shifts,id'],
            'correction.time_in' => ['nullable', 'date_format:H:i'],
            'correction.time_out' => ['nullable', 'date_format:H:i'],
            'correction.status' => ['required', 'in:present,late,excused,sick,absent,incomplete'],
            'correction.reason' => ['required', 'string', 'max:255'],
        ]);

        $attendance = Attendance::findOrFail($this->currentAttendance['id']);
        $oldDate = $attendance->date?->format('Y-m-d');

        $before = [
            'date' => $oldDate,
            'shift_id' => $attendance->shift_id,
            'time_in' => $attendance->getRawOriginal('time_in'),
            'time_out' => $attendance->getRawOriginal('time_out'),
            'status' => $attendance->status,
        ];

        DB::transaction(function () use ($attendance, $before) {
            $attendance->forceFill([
                'date' => $this->correction['date'],
                'shift_id' => $this->correction['shift_id'] ?: null,
                'time_in' => $this->timeForStorage($this->correction['time_in']),
                'time_out' => $this->timeForStorage($this->correction['time_out']),
                'status' => $this->correction['status'],
                'corrected_by' => Auth::user()->id,
                'corrected_at' => now(),
                'correction_note' => $this->correction['reason'],
            ])->save();

            $attendance->load('shift');
            app(AttendanceCalculator::class)->apply($attendance);

            AttendanceEditLog::create([
                'attendance_id' => $attendance->id,
                'edited_by' => Auth::user()->id,
                'changes' => [
                    'before' => $before,
                    'after' => [
                        'date' => $attendance->date?->format('Y-m-d'),
                        'shift_id' => $attendance->shift_id,
                        'time_in' => $attendance->getRawOriginal('time_in'),
                        'time_out' => $attendance->getRawOriginal('time_out'),
                        'status' => $attendance->status,
                    ],
                ],
                'reason' => $this->correction['reason'],
            ]);
        });

        if ($oldDate) {
            Attendance::clearUserAttendanceCache($attendance->user, Carbon::parse($oldDate));
        }
        Attendance::clearUserAttendanceCache($attendance->user, Carbon::parse($this->correction['date']));

        $this->showCorrection = false;
        $this->show($attendance->id);

        if (method_exists($this, 'banner')) {
            $this->banner('Koreksi absensi tersimpan.');
        }
    }

    private function ensureAdmin(): void
    {
        abort_unless(Auth::user()?->isAdmin, 403);
    }

    private function timeForInput(?string $time): ?string
    {
        return $time ? substr($time, 0, 5) : null;
    }

    private function timeForStorage(?string $time): ?string
    {
        return $time ? substr($time, 0, 5).':00' : null;
    }
}
