<?php

use App\Models\User;

use function Pest\Laravel\actingAs;

test('admin pages render without errors', function () {
    $admin = User::factory()->admin()->create();

    actingAs($admin);

    $this->get('/admin/dashboard')->assertOk();
    $this->get('/admin/attendances')->assertOk();
    $this->get('/admin/overtime')->assertOk();
    $this->get('/admin/employees')->assertOk();
    $this->get('/admin/masterdata/branch')->assertOk();
    $this->get('/admin/masterdata/holiday')->assertOk();
    $this->get('/admin/masterdata/shift')->assertOk();
});

test('employee scan page renders without errors', function () {
    $employee = User::factory()->create(['group' => 'user']);

    actingAs($employee)
        ->get('/home')
        ->assertOk();
});
