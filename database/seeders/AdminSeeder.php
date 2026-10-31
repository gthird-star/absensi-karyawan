<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $superadmin = User::factory()->admin(superadmin: true)->make([
            'name' => 'Super Admin',
            'email' => 'superadmin@example.com',
            'password' => Hash::make('superadmin'),
            'profile_photo_path' => 'https://picsum.photos/400/400?random=1001',
        ])->getAttributes();

        User::firstOrCreate(['email' => 'superadmin@example.com'], $superadmin);

        $admin = User::factory()->admin()->make([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('admin'),
            'profile_photo_path' => 'https://picsum.photos/400/400?random=1002',
        ])->getAttributes();

        User::firstOrCreate(['email' => 'admin@example.com'], $admin);
    }
}
