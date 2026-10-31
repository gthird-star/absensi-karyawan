<?php

namespace Database\Seeders;

use App\Models\Attendance;
use App\Models\AttendanceEditLog;
use App\Models\Barcode;
use App\Models\Branch;
use App\Models\Division;
use App\Models\Education;
use App\Models\Holiday;
use App\Models\JobTitle;
use App\Models\LeaveQuota;
use App\Models\LeaveRequest;
use App\Models\Shift;
use App\Models\User;
use App\Services\AttendanceCalculator;
use App\Services\ShiftSchedule;
use Database\Factories\DivisionFactory;
use Database\Factories\EducationFactory;
use Database\Factories\JobTitleFactory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;

class DemoSeeder extends Seeder
{
    private array $names = [
        'Ahmad Fauzi', 'Siti Nurhaliza', 'Budi Santoso', 'Dewi Lestari', 'Rizky Pratama',
        'Anisa Putri', 'Agus Setiawan', 'Maya Sari', 'Doni Kurniawan', 'Nur Aisyah',
        'Eko Prasetyo', 'Fitri Handayani', 'Hendra Wijaya', 'Indah Permata', 'Joko Susilo',
        'Kartika Dewi', 'Lukman Hakim', 'Mega Wulandari', 'Nanda Saputra', 'Oktavia Rahma',
        'Putra Maulana', 'Qori Amalia', 'Rudi Hartono', 'Sri Wahyuni', 'Taufik Hidayat',
        'Umi Kalsum', 'Vino Alfarizi', 'Wati Suryani', 'Yoga Pratama', 'Zahra Nabila',
        'Arif Rahman', 'Bella Anggraini', 'Cahyo Nugroho', 'Dian Puspita', 'Fajar Ramadhan',
        'Gita Savitri', 'Hadi Purnomo', 'Intan Maharani', 'Junaedi Saputra', 'Kiki Amelia',
        'Laila Fitriani', 'Miftahul Huda', 'Novi Andriani', 'Oki Wijaya', 'Prita Sari',
        'Rendy Setiawan', 'Siska Aprilia', 'Teguh Santoso', 'Vina Oktaviani', 'Yusuf Maulana',
    ];

    public function run(): void
    {
        $this->command?->info('Menyiapkan data demo Soerabaja45 Printing...');

        $this->seedMasterData();
        $branches = $this->seedBranches();
        $shifts = $this->seedShifts();
        $this->seedHolidays();

        (new AdminSeeder)->run();
        $admin = User::where('group', 'admin')->first();

        $this->seedEmployees($branches);
        $this->seedBarcodes($branches);
        $this->seedAttendance($shifts, $admin);
        $this->seedLeaveRequests();

        $this->command?->info('Selesai. Login admin@example.com / admin.');
    }

    private function seedMasterData(): void
    {
        foreach (DivisionFactory::$divisions as $name) {
            Division::firstOrCreate(['name' => $name]);
        }
        foreach (EducationFactory::$educations as $name) {
            Education::firstOrCreate(['name' => $name]);
        }
        foreach (JobTitleFactory::$jobTitles as $name) {
            JobTitle::firstOrCreate(['name' => $name]);
        }
    }

    /** @return array<int, Branch> */
    private function seedBranches(): array
    {
        $data = [
            ['name' => 'Soerabaja45 Pusat', 'code' => 'SBY', 'address' => 'Jl. Rungkut Industri, Surabaya', 'latitude' => -7.3195, 'longitude' => 112.7675, 'radius' => 150],
            ['name' => 'Soerabaja45 Sidoarjo', 'code' => 'SDA', 'address' => 'Jl. Raya Sidoarjo, Sidoarjo', 'latitude' => -7.4478, 'longitude' => 112.6988, 'radius' => 150],
            ['name' => 'Soerabaja45 Gresik', 'code' => 'GRS', 'address' => 'Jl. Raya Gresik, Gresik', 'latitude' => -7.1566, 'longitude' => 112.6550, 'radius' => 150],
        ];

        return collect($data)
            ->map(fn (array $branch) => Branch::updateOrCreate(['code' => $branch['code']], $branch))
            ->all();
    }

    /** @return array<int, Shift> */
    private function seedShifts(): array
    {
        $data = [
            ['name' => 'Pagi', 'start_time' => '07:00:00', 'end_time' => '15:00:00', 'is_cross_midnight' => false, 'late_tolerance_minutes' => 10],
            ['name' => 'Siang', 'start_time' => '15:00:00', 'end_time' => '23:00:00', 'is_cross_midnight' => false, 'late_tolerance_minutes' => 10],
            ['name' => 'Malam', 'start_time' => '23:00:00', 'end_time' => '07:00:00', 'is_cross_midnight' => true, 'late_tolerance_minutes' => 10],
        ];

        return collect($data)
            ->map(fn (array $shift) => Shift::updateOrCreate(['name' => $shift['name']], $shift))
            ->all();
    }

    private function seedHolidays(): void
    {
        $year = now()->year;
        $holidays = [
            "$year-01-01" => 'Tahun Baru',
            "$year-05-01" => 'Hari Buruh',
            "$year-06-01" => 'Hari Pancasila',
            "$year-08-17" => 'Hari Kemerdekaan',
            "$year-12-25" => 'Hari Natal',
        ];

        foreach ($holidays as $date => $name) {
            Holiday::updateOrCreate(['date' => $date], ['name' => $name]);
        }
    }

    /** @param array<int, Branch> $branches */
    private function seedEmployees(array $branches): void
    {
        $divisions = Division::pluck('id');
        $educations = Education::pluck('id');
        $jobTitles = JobTitle::pluck('id');

        foreach ($this->names as $index => $name) {
            $email = 'karyawan'.($index + 1).'@demo.test';

            $user = User::firstOrCreate(['email' => $email], [
                'nip' => '45'.str_pad((string) ($index + 1), 8, '0', STR_PAD_LEFT),
                'name' => $name,
                'email_verified_at' => now(),
                'phone' => '08'.rand(1000000000, 9999999999),
                'gender' => $index % 2 === 0 ? 'male' : 'female',
                'birth_date' => now()->subYears(rand(21, 45))->subDays(rand(0, 300))->toDateString(),
                'birth_place' => 'Surabaya',
                'address' => 'Jl. Contoh No. '.($index + 1),
                'city' => 'Surabaya',
                'group' => 'user',
                'password' => Hash::make('password'),
                'branch_id' => $branches[$index % count($branches)]->id,
                'division_id' => $divisions->random(),
                'education_id' => $educations->random(),
                'job_title_id' => $jobTitles->random(),
                'profile_photo_path' => 'https://picsum.photos/400/400?random='.($index + 1),
            ]);

            LeaveQuota::updateOrCreate(
                ['user_id' => $user->id, 'year' => now()->year],
                ['quota_days' => 12, 'used_days' => rand(0, 2)]
            );
        }

        User::firstOrCreate(['email' => 'user@example.com'], [
            'nip' => '4500009999',
            'name' => 'Karyawan Demo',
            'email_verified_at' => now(),
            'phone' => '081234567890',
            'gender' => 'male',
            'birth_date' => now()->subYears(30)->toDateString(),
            'birth_place' => 'Surabaya',
            'address' => 'Jl. Demo No. 1',
            'city' => 'Surabaya',
            'group' => 'user',
            'password' => Hash::make('password'),
            'branch_id' => $branches[0]->id,
            'division_id' => $divisions->first(),
            'education_id' => $educations->first(),
            'job_title_id' => $jobTitles->first(),
        ]);

        LeaveQuota::firstOrCreate(
            ['user_id' => User::where('email', 'user@example.com')->value('id'), 'year' => now()->year],
            ['quota_days' => 12, 'used_days' => 0]
        );
    }

    /** @param array<int, Branch> $branches */
    private function seedBarcodes(array $branches): void
    {
        foreach ($branches as $branch) {
            Barcode::updateOrCreate(['value' => 'ABSEN-'.$branch->code], [
                'name' => 'QR '.$branch->name,
                'branch_id' => $branch->id,
                'latitude' => $branch->latitude,
                'longitude' => $branch->longitude,
                'radius' => $branch->radius,
            ]);
        }
    }

    /** @param array<int, Shift> $shifts */
    private function seedAttendance(array $shifts, ?User $admin): void
    {
        $calculator = app(AttendanceCalculator::class);
        $users = User::where('group', 'user')->get();
        $holidayDates = Holiday::pluck('date')
            ->map(fn ($date) => Carbon::parse($date)->toDateString())
            ->all();

        $start = Carbon::parse('2026-10-01');
        $end = Carbon::parse('2026-12-31');
        $todayDone = false;

        $existing = Attendance::get(['user_id', 'date'])
            ->keyBy(fn (Attendance $attendance) => $attendance->user_id.'|'.$attendance->date?->format('Y-m-d'))
            ->keys()
            ->flip()
            ->all();

        for ($date = $start->copy(); $date->lte($end); $date->addDay()) {
            if ($date->isWeekend() || in_array($date->toDateString(), $holidayDates, true)) {
                continue;
            }

            foreach ($users as $user) {
                if (isset($existing[$user->id.'|'.$date->toDateString()])) {
                    continue;
                }
                $existing[$user->id.'|'.$date->toDateString()] = true;

                $shift = $shifts[array_rand($shifts)];
                $barcode = Barcode::where('branch_id', $user->branch_id)->first()
                    ?? Barcode::first();
                $roll = rand(1, 100);

                if ($roll > 97) {
                    $status = 'absent';
                } elseif ($roll > 94) {
                    $status = 'sick';
                } elseif ($roll > 91) {
                    $status = 'excused';
                } else {
                    $late = $roll <= 12;
                    $status = $late ? 'late' : 'present';
                }

                $isToday = $date->isSameDay(Carbon::today());
                $openShift = $isToday && ! $todayDone && $roll <= 85;

                if (in_array($status, ['absent', 'sick', 'excused'], true)) {
                    $attendance = Attendance::create([
                        'user_id' => $user->id,
                        'barcode_id' => $barcode?->id,
                        'date' => $date->toDateString(),
                        'shift_id' => $shift->id,
                        'status' => $status,
                        'note' => $status === 'sick' ? 'Surat sakit terlampir' : ($status === 'excused' ? 'Izin keperluan keluarga' : null),
                    ]);
                } else {
                    $startAt = ShiftSchedule::startAt($shift, $date);
                    $endAt = ShiftSchedule::endAt($shift, $date);
                    $lateMinutes = $status === 'late' ? rand(11, 45) : rand(-10, 0);
                    $timeIn = $startAt->copy()->addMinutes($lateMinutes);
                    $overtime = rand(1, 100) <= 35 ? rand(15, 120) : 0;
                    $earlyLeave = 0;

                    if (rand(1, 100) <= 4) {
                        $earlyLeave = rand(20, 90);
                        $status = 'incomplete';
                    }

                    $timeOut = $endAt->copy()->addMinutes($overtime - $earlyLeave);

                    $attendance = Attendance::create([
                        'user_id' => $user->id,
                        'barcode_id' => $barcode?->id,
                        'date' => $date->toDateString(),
                        'time_in' => $timeIn->format('H:i:s'),
                        'time_out' => $openShift ? null : $timeOut->format('H:i:s'),
                        'shift_id' => $shift->id,
                        'latitude' => $barcode?->latitude,
                        'longitude' => $barcode?->longitude,
                        'status' => $status,
                    ]);

                    $attendance->setRelation('shift', $shift);
                    $calculator->apply($attendance);

                    if ($openShift) {
                        $attendance->update(['time_out' => null, 'overtime_minutes' => 0, 'early_leave_minutes' => 0]);
                    }

                    if ($isToday && $openShift) {
                        $todayDone = true;
                    }
                }

                if ($admin && rand(1, 1000) <= 2) {
                    $previousStatus = $attendance->status;

                    $attendance->update([
                        'status' => 'present',
                        'note' => 'Dikoreksi: mesin absen bermasalah',
                        'corrected_by' => $admin->id,
                        'corrected_at' => now(),
                        'correction_note' => 'Koreksi data demo',
                    ]);

                    AttendanceEditLog::create([
                        'attendance_id' => $attendance->id,
                        'edited_by' => $admin->id,
                        'changes' => ['before' => ['status' => $previousStatus], 'after' => ['status' => 'present']],
                        'reason' => 'Koreksi data demo',
                    ]);
                }
            }
        }
    }

    private function seedLeaveRequests(): void
    {
        $users = User::where('group', 'user')->inRandomOrder()->take(8)->get();
        $types = ['annual', 'annual', 'excused', 'sick'];

        foreach ($users as $index => $user) {
            $from = Carbon::today()->addDays(rand(1, 10));

            if (LeaveRequest::where('user_id', $user->id)->whereDate('from_date', $from->toDateString())->exists()) {
                continue;
            }

            LeaveRequest::create([
                'user_id' => $user->id,
                'type' => $types[$index % count($types)],
                'from_date' => $from->toDateString(),
                'to_date' => $from->copy()->addDays(rand(0, 2))->toDateString(),
                'note' => 'Pengajuan demo untuk '.$user->name,
                'status' => 'pending',
            ]);
        }
    }
}
