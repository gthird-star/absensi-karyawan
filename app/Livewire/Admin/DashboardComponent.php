<?php

namespace App\Livewire\Admin;

use App\Livewire\Traits\AttendanceDetailTrait;
use App\Models\Attendance;
use App\Models\Holiday;
use App\Models\LeaveRequest;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;
use Livewire\Component;

class DashboardComponent extends Component
{
    use AttendanceDetailTrait;

    public int $period = 30;

    public ?string $branch = null;

    public function setPeriod(int $days): void
    {
        $this->period = in_array($days, [7, 30, 90], true) ? $days : 30;
    }

    public function render()
    {
        /** @var Collection<Attendance> $attendances */
        $attendances = Attendance::where('date', date('Y-m-d'))
            ->when($this->branch, fn ($query) => $query->whereHas(
                'user',
                fn ($query) => $query->where('branch_id', $this->branch)
            ))
            ->get();

        $employees = User::where('group', 'user')
            ->when($this->branch, fn ($query) => $query->where('branch_id', $this->branch))
            ->paginate(20)
            ->through(function (User $user) use ($attendances) {
                return $user->setAttribute(
                    'attendance',
                    $attendances
                        ->where(fn (Attendance $attendance) => $attendance->user_id === $user->id)
                        ->first(),
                );
            });

        $employeesCount = User::where('group', 'user')
            ->when($this->branch, fn ($query) => $query->where('branch_id', $this->branch))
            ->count();
        $presentCount = $attendances->where('status', 'present')->count();
        $lateCount = $attendances->where('status', 'late')->count();
        $excusedCount = $attendances->where('status', 'excused')->count();
        $sickCount = $attendances->where('status', 'sick')->count();
        $incompleteCount = $attendances->where('status', 'incomplete')->count();
        $absentCount = $employeesCount - ($presentCount + $lateCount + $excusedCount + $sickCount + $incompleteCount);
        $pendingLeaveCount = LeaveRequest::where('status', 'pending')->count();
        $isHolidayToday = Holiday::whereDate('date', Carbon::today()->toDateString())->exists();

        if (Carbon::today()->isWeekend() || $isHolidayToday) {
            $absentCount = 0;
        }

        $notCheckedOut = Attendance::query()
            ->when($this->branch, fn ($query) => $query->whereHas(
                'user',
                fn ($query) => $query->where('branch_id', $this->branch)
            ))
            ->whereDate('date', Carbon::today()->toDateString())
            ->whereNotNull('time_in')
            ->whereNull('time_out')
            ->whereIn('status', ['present', 'late'])
            ->count();

        $pendingCorrectionCount = \App\Models\AttendanceCorrectionRequest::where('status', 'pending')
            ->when($this->branch, fn ($query) => $query->whereHas(
                'user',
                fn ($query) => $query->where('branch_id', $this->branch)
            ))
            ->count();

        $correctedToday = Attendance::query()
            ->when($this->branch, fn ($query) => $query->whereHas(
                'user',
                fn ($query) => $query->where('branch_id', $this->branch)
            ))
            ->whereDate('corrected_at', Carbon::today()->toDateString())
            ->count();

        $start = Carbon::today()->subDays($this->period - 1);
        $periodAttendances = Attendance::with('user.division')
            ->whereBetween('date', [$start->toDateString(), Carbon::today()->toDateString()])
            ->when($this->branch, fn ($query) => $query->whereHas(
                'user',
                fn ($query) => $query->where('branch_id', $this->branch)
            ))
            ->get(['id', 'user_id', 'date', 'status', 'overtime_minutes', 'early_leave_minutes']);

        $periodPresent = $periodAttendances->whereIn('status', ['present', 'late'])->count();
        $periodLate = $periodAttendances->where('status', 'late')->count();
        $punctuality = $periodPresent > 0 ? round((($periodPresent - $periodLate) / $periodPresent) * 100) : 0;
        $periodOvertime = $periodAttendances->sum('overtime_minutes');
        $periodEarlyLeave = $periodAttendances->sum('early_leave_minutes');

        $trend = collect(range(0, $this->period - 1))
            ->map(function (int $offset) use ($start, $periodAttendances) {
                $date = $start->copy()->addDays($offset);
                $daily = $periodAttendances->filter(
                    fn (Attendance $attendance) => $attendance->date?->toDateString() === $date->toDateString()
                );

                return [
                    'label' => $date->format('d/m'),
                    'present' => $daily->whereIn('status', ['present', 'late'])->count(),
                    'late' => $daily->where('status', 'late')->count(),
                    'is_weekend' => $date->isWeekend(),
                ];
            });

        $maxTrend = max(1, $trend->max('present'));

        /** @var Collection<Attendance> $branchStats */
        $branchStats = collect();
        if (class_exists(\App\Models\Branch::class)) {
            $branches = \App\Models\Branch::orderBy('name')->get();
            $branchStats = $branches
                ->map(function ($branch) use ($periodAttendances) {
                    $userIds = $branch->users->pluck('id');
                    $items = $periodAttendances->whereIn('user_id', $userIds);

                    return [
                        'name' => $branch->name,
                        'present' => $items->whereIn('status', ['present', 'late'])->count(),
                        'late' => $items->where('status', 'late')->count(),
                        'employees' => $userIds->count(),
                        'overtime_minutes' => $items->sum('overtime_minutes'),
                    ];
                });
        }

        $divisionStats = $periodAttendances
            ->groupBy(fn (Attendance $attendance) => $attendance->user?->division?->name ?? 'Tanpa Divisi')
            ->map(function ($items, $name) {
                $present = $items->whereIn('status', ['present', 'late'])->count();
                $late = $items->where('status', 'late')->count();

                return [
                    'name' => $name,
                    'present' => $present,
                    'late' => $late,
                    'employees' => $items->unique('user_id')->count(),
                    'overtime_minutes' => $items->sum('overtime_minutes'),
                ];
            })
            ->sortByDesc('present')
            ->values();

        $topLate = $periodAttendances
            ->groupBy('user_id')
            ->map(function ($items) {
                $user = $items->first()->user;

                return [
                    'name' => $user?->name ?? '-',
                    'division' => $user?->division?->name ?? '-',
                    'late_count' => $items->where('status', 'late')->count(),
                ];
            })
            ->filter(fn ($row) => $row['late_count'] > 0)
            ->sortByDesc('late_count')
            ->take(5)
            ->values();

        return view('livewire.admin.dashboard', [
            'employees' => $employees,
            'employeesCount' => $employeesCount,
            'presentCount' => $presentCount,
            'lateCount' => $lateCount,
            'excusedCount' => $excusedCount,
            'sickCount' => $sickCount,
            'incompleteCount' => $incompleteCount,
            'absentCount' => $absentCount,
            'pendingLeaveCount' => $pendingLeaveCount,
            'isHolidayToday' => $isHolidayToday,
            'periodPresent' => $periodPresent,
            'periodLate' => $periodLate,
            'punctuality' => $punctuality,
            'periodOvertime' => $periodOvertime,
            'periodEarlyLeave' => $periodEarlyLeave,
            'trend' => $trend,
            'maxTrend' => $maxTrend,
            'divisionStats' => $divisionStats,
            'topLate' => $topLate,
            'notCheckedOut' => $notCheckedOut,
            'pendingCorrectionCount' => $pendingCorrectionCount,
            'correctedToday' => $correctedToday,
            'branchStats' => $branchStats,
        ]);
    }
}
