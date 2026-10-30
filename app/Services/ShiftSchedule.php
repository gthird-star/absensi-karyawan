<?php

namespace App\Services;

use App\Models\Shift;
use Carbon\CarbonInterface;
use Illuminate\Support\Carbon;

class ShiftSchedule
{
    public static function startAt(Shift $shift, CarbonInterface $date): Carbon
    {
        return Carbon::parse($date->toDateString())
            ->setTimeFromTimeString(self::time($shift->start_time));
    }

    public static function endAt(Shift $shift, CarbonInterface $date): Carbon
    {
        $start = self::startAt($shift, $date);

        if (! $shift->end_time) {
            return $start->copy();
        }

        $end = $start->copy()->setTimeFromTimeString($shift->end_time);

        if ($end->lessThanOrEqualTo($start)) {
            $end->addDay();
        }

        return $end;
    }

    public static function isCrossMidnight(Shift $shift): bool
    {
        if (! $shift->end_time) {
            return false;
        }

        return self::time($shift->end_time) <= self::time($shift->start_time);
    }

    private static function time(?string $time): string
    {
        return substr((string) $time, 0, 8);
    }
}
