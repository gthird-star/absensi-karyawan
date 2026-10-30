<?php

use App\Livewire\ScanComponent;
use App\Models\Attendance;
use App\Models\Barcode;
use App\Models\Branch;
use App\Models\Shift;
use App\Models\User;
use Illuminate\Support\Carbon;
use Livewire\Livewire;

test('night shift checkout finds attendance from previous day', function () {
    $branch = Branch::factory()->create();
    $shift = Shift::factory()->create([
        'start_time' => '23:00:00',
        'end_time' => '07:00:00',
        'is_cross_midnight' => true,
    ]);
    $barcode = Barcode::create([
        'branch_id' => $branch->id,
        'name' => 'QR Test',
        'value' => 'TEST-NIGHT',
        'latitude' => $branch->latitude,
        'longitude' => $branch->longitude,
        'radius' => 200,
    ]);
    $user = User::factory()->create(['group' => 'user', 'branch_id' => $branch->id]);

    $attendance = Attendance::create([
        'user_id' => $user->id,
        'barcode_id' => $barcode->id,
        'shift_id' => $shift->id,
        'date' => '2026-10-28',
        'time_in' => '22:55:00',
        'time_out' => null,
        'status' => 'present',
    ]);

    Carbon::setTestNow(Carbon::parse('2026-10-29 07:15:00'));

    Livewire::actingAs($user)
        ->test(ScanComponent::class)
        ->set('currentLiveCoords', [$barcode->latitude, $barcode->longitude])
        ->call('scan', $barcode->value)
        ->assertHasNoErrors();

    expect($attendance->refresh())
        ->time_out->not->toBeNull()
        ->overtime_minutes->toBe(15)
        ->status->toBe('present');
});

test('employee cannot scan barcode from another branch', function () {
    $branch = Branch::factory()->create();
    $other = Branch::factory()->create();
    $shift = Shift::factory()->create();
    $barcode = Barcode::create([
        'branch_id' => $other->id,
        'name' => 'QR Other',
        'value' => 'TEST-OTHER',
        'latitude' => $other->latitude,
        'longitude' => $other->longitude,
        'radius' => 200,
    ]);
    $user = User::factory()->create(['group' => 'user', 'branch_id' => $branch->id]);

    Livewire::actingAs($user)
        ->test(ScanComponent::class)
        ->set('shift_id', $shift->id)
        ->set('currentLiveCoords', [$barcode->latitude, $barcode->longitude])
        ->call('scan', $barcode->value);

    expect(Attendance::count())->toBe(0);
});
