<?php

use App\Models\Attendance;
use App\Models\Shift;
use App\Models\User;
use App\Services\AttendanceCalculator;
use App\Services\ShiftSchedule;
use Illuminate\Support\Carbon;

beforeEach(function () {
    $this->user = User::factory()->create(['group' => 'user']);
    $this->calculator = app(AttendanceCalculator::class);
});

test('night shift overtime is counted after midnight', function () {
    $shift = Shift::factory()->create([
        'start_time' => '23:00:00',
        'end_time' => '07:00:00',
        'is_cross_midnight' => true,
    ]);

    $attendance = Attendance::create([
        'user_id' => $this->user->id,
        'shift_id' => $shift->id,
        'date' => '2026-10-28',
        'time_in' => '22:55:00',
        'time_out' => '07:40:00',
        'status' => 'present',
    ]);

    $this->calculator->apply($attendance->refresh());

    expect($attendance->refresh())
        ->overtime_minutes->toBe(40)
        ->early_leave_minutes->toBe(0);
});

test('night shift early leave is counted before shift end', function () {
    $shift = Shift::factory()->create([
        'start_time' => '23:00:00',
        'end_time' => '07:00:00',
        'is_cross_midnight' => true,
    ]);

    $attendance = Attendance::create([
        'user_id' => $this->user->id,
        'shift_id' => $shift->id,
        'date' => '2026-10-28',
        'time_in' => '22:50:00',
        'time_out' => '06:15:00',
        'status' => 'incomplete',
    ]);

    $this->calculator->apply($attendance->refresh());

    expect($attendance->refresh())
        ->early_leave_minutes->toBe(45)
        ->overtime_minutes->toBe(0);
});

test('day shift overtime respects late tolerance for lateness only', function () {
    $shift = Shift::factory()->create([
        'start_time' => '08:00:00',
        'end_time' => '17:00:00',
        'is_cross_midnight' => false,
        'late_tolerance_minutes' => 10,
    ]);

    $attendance = Attendance::create([
        'user_id' => $this->user->id,
        'shift_id' => $shift->id,
        'date' => '2026-10-28',
        'time_in' => '08:07:00',
        'time_out' => '17:30:00',
        'status' => 'present',
    ]);

    $this->calculator->apply($attendance->refresh());

    expect($attendance->refresh())
        ->overtime_minutes->toBe(30)
        ->early_leave_minutes->toBe(0);
});

test('cross midnight shift end is next day', function () {
    $shift = Shift::factory()->make([
        'start_time' => '22:00:00',
        'end_time' => '06:00:00',
    ]);

    $end = ShiftSchedule::endAt($shift, Carbon::parse('2026-10-28'));

    expect($end->format('Y-m-d H:i'))->toBe('2026-10-29 06:00');
});
