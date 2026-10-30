<?php

namespace App\Services;

use App\Models\Attendance;
use Illuminate\Support\Carbon;

class AttendanceCalculator
{
    public function apply(Attendance $attendance): Attendance
    {
        $end = $this->expectedEnd($attendance);
        $out = $this->timeOutAt($attendance);

        $earlyLeave = 0;
        $overtime = 0;
        if ($end && $out) {
            $earlyLeave = max(0, (int) ceil(-$end->diffInMinutes($out, false)));
            $overtime = max(0, (int) floor($end->diffInMinutes($out, false)));
        }

        $attendance->overtime_minutes = $overtime;
        $attendance->early_leave_minutes = $earlyLeave;
        $attendance->save();

        return $attendance;
    }

    public function expectedStart(Attendance $attendance): ?Carbon
    {
        if (! $attendance->shift || ! $attendance->date) {
            return null;
        }

        return ShiftSchedule::startAt($attendance->shift, $attendance->date);
    }

    public function expectedEnd(Attendance $attendance): ?Carbon
    {
        if (! $attendance->shift || ! $attendance->date) {
            return null;
        }

        return ShiftSchedule::endAt($attendance->shift, $attendance->date);
    }

    public function timeInAt(Attendance $attendance): ?Carbon
    {
        return $this->combine($attendance, $attendance->getRawOriginal('time_in'));
    }

    public function lateMinutes(Attendance $attendance): int
    {
        $start = $this->expectedStart($attendance);
        $in = $this->timeInAt($attendance);

        if (! $start || ! $in) {
            return 0;
        }

        return max(0, (int) ceil($start->diffInMinutes($in, false)) - (int) ($attendance->shift?->late_tolerance_minutes ?? 0));
    }

    public function timeOutAt(Attendance $attendance): ?Carbon
    {
        $in = $this->timeInAt($attendance);
        $out = $this->combine($attendance, $attendance->getRawOriginal('time_out'));

        if ($in && $out && $out->lessThanOrEqualTo($in)) {
            $out->addDay();
        }

        return $out;
    }

    private function combine(Attendance $attendance, ?string $time): ?Carbon
    {
        if (! $time || ! $attendance->date) {
            return null;
        }

        return Carbon::parse($attendance->date->format('Y-m-d').' '.$time);
    }
}
