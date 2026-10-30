<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Holiday;
use App\Models\LeaveQuota;
use App\Models\LeaveRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class UserAttendanceController extends Controller
{
    public function applyLeave()
    {
        $attendance = Attendance::where('user_id', Auth::user()->id)
            ->where('date', date('Y-m-d'))
            ->first();
        $quota = LeaveQuota::firstOrCreate(
            ['user_id' => Auth::user()->id, 'year' => now()->year],
            ['quota_days' => 12]
        );
        return view('attendances.apply-leave', [
            'attendance' => $attendance,
            'quotaRemaining' => $quota->remaining_days,
        ]);
    }

    public function storeLeaveRequest(Request $request)
    {
        $request->validate([
            'status' => ['required', 'in:excused,sick,annual'],
            'note' => ['required', 'string', 'max:255'],
            'from' => ['required', 'date'],
            'to' => ['nullable', 'date', 'after_or_equal:from'],
            'attachment' => ['nullable', 'file', 'max:3072'],
            'lat' => ['nullable', 'numeric'],
            'lng' => ['nullable', 'numeric'],
        ]);

        $from = Carbon::parse($request->from);
        $to = $request->to ? Carbon::parse($request->to) : $from->copy();
        $days = $this->workingDays($from, $to);

        if ($request->status === 'annual') {
            $quota = LeaveQuota::firstOrCreate(
                ['user_id' => Auth::user()->id, 'year' => now()->year],
                ['quota_days' => 12]
            );

            if ($days > $quota->remaining_days) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['status' => "Sisa kuota cuti Anda {$quota->remaining_days} hari, pengajuan {$days} hari."]);
            }
        }

        try {
            $newAttachment = null;
            if ($request->file('attachment')) {
                $newAttachment = $request->file('attachment')->storePublicly(
                    'attachments',
                    ['disk' => config('jetstream.attachment_disk')]
                );
            }

            LeaveRequest::create([
                'user_id' => Auth::user()->id,
                'type' => $request->status,
                'from_date' => $request->from,
                'to_date' => $request->to ?? $request->from,
                'note' => $request->note,
                'attachment' => $newAttachment,
                'latitude' => $request->lat ? doubleval($request->lat) : null,
                'longitude' => $request->lng ? doubleval($request->lng) : null,
                'status' => 'pending',
            ]);

            return redirect(route('home'))
                ->with('flash.banner', 'Pengajuan izin berhasil dikirim, menunggu persetujuan admin.');
        } catch (\Throwable $th) {
            return redirect()->back()
                ->with('flash.banner', $th->getMessage())
                ->with('flash.bannerStyle', 'danger');
        }
    }

    private function workingDays(Carbon $from, Carbon $to): int
    {
        $holidays = Holiday::whereBetween('date', [$from->toDateString(), $to->toDateString()])
            ->pluck('date')
            ->map(fn ($date) => Carbon::parse($date)->toDateString())
            ->all();

        return $from->range($to)
            ->filter(fn (Carbon $date) => ! $date->isWeekend() && ! in_array($date->toDateString(), $holidays, true))
            ->count();
    }

    public function history()
    {
        return view('attendances.history');
    }

    public function leaveHistory(Request $request)
    {
        $filter = $request->get('filter', 'all');

        $leaveRequests = LeaveRequest::where('user_id', Auth::user()->id)
            ->when($filter !== 'all', fn ($q) => $q->where('status', $filter))
            ->latest()
            ->paginate(20);

        $pendingCount = LeaveRequest::where('user_id', Auth::user()->id)
            ->where('status', 'pending')->count();

        return view('attendances.leave-history', [
            'leaveRequests' => $leaveRequests,
            'currentFilter' => $filter,
            'pendingCount' => $pendingCount,
        ]);
    }
}
