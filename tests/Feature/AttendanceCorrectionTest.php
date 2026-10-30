<?php

use App\Livewire\Admin\AttendanceComponent;
use App\Models\Attendance;
use App\Models\AttendanceEditLog;
use App\Models\Shift;
use App\Models\User;
use Livewire\Livewire;

test('admin can correct attendance and the change is audited', function () {
    $admin = User::factory()->admin()->create();
    $user = User::factory()->create(['group' => 'user']);
    $shift = Shift::factory()->create([
        'start_time' => '08:00:00',
        'end_time' => '17:00:00',
        'is_cross_midnight' => false,
    ]);

    $attendance = Attendance::create([
        'user_id' => $user->id,
        'shift_id' => $shift->id,
        'date' => now()->toDateString(),
        'time_in' => '08:00:00',
        'time_out' => '17:00:00',
        'status' => 'present',
    ]);

    Livewire::actingAs($admin)
        ->test(AttendanceComponent::class)
        ->call('show', $attendance->id)
        ->call('openCorrection')
        ->set('correction.time_out', '19:30')
        ->set('correction.reason', 'Lembur produksi')
        ->call('saveCorrection')
        ->assertHasNoErrors();

    expect($attendance->refresh())
        ->overtime_minutes->toBe(150)
        ->corrected_by->toBe($admin->id);

    expect(AttendanceEditLog::count())->toBe(1);
    expect(AttendanceEditLog::first()->reason)->toBe('Lembur produksi');
});

test('employee cannot correct attendance', function () {
    $employee = User::factory()->create(['group' => 'user']);
    $attendance = Attendance::create([
        'user_id' => $employee->id,
        'date' => now()->toDateString(),
        'time_in' => '08:00:00',
        'status' => 'present',
    ]);

    $component = Livewire::actingAs($employee)
        ->test(AttendanceComponent::class)
        ->call('show', $attendance->id);

    expect(fn () => $component->instance()->openCorrection())
        ->toThrow(Symfony\Component\HttpKernel\Exception\HttpException::class);
});
