<?php

use Database\Seeders\DemoSeeder;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

Artisan::command('demo:seed', function () {
    $this->call('db:seed', ['--class' => DemoSeeder::class, '--force' => true]);
    $this->call('cache:clear');
    $this->info('Data demo Soerabaja45 berhasil dibuat.');
})->purpose('Isi data demo Soerabaja45 Printing tanpa menghapus data lama');

Artisan::command('demo:reset {--force}', function () {
    if (app()->environment('production') && ! $this->option('force')) {
        $this->error('Perintah ini menghapus seluruh data. Jalankan dengan --force bila benar-benar yakin.');

        return 1;
    }

    $this->call('migrate:fresh', [
        '--seed' => true,
        '--seeder' => DemoSeeder::class,
        '--force' => true,
    ]);
    $this->call('cache:clear');
    $this->info('Database direset dan diisi data demo Soerabaja45.');

    return 0;
})->purpose('Reset database lokal/staging lalu isi data demo');

