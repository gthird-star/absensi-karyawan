<?php

namespace App\Livewire\Admin;

use App\Models\Attendance;
use App\Models\Division;
use Illuminate\Support\Carbon;
use Livewire\Component;

class OvertimeComponent extends Component
{
    public string $month = '';

    public ?string $division = null;

    public function mount()
    {
        $this->month = now()->format('Y-m');
    }

    public function render()
    {
        $month = Carbon::parse($this->month ?: now()->format('Y-m'));

        $rows = Attendance::with('user.division')
            ->whereBetween('date', [$month->copy()->startOfMonth()->toDateString(), $month->copy()->endOfMonth()->toDateString()])
            ->where(function ($query) {
                $query->where('overtime_minutes', '>', 0)
                    ->orWhere('early_leave_minutes', '>', 0);
            })
            ->when($this->division, fn ($query) => $query->whereHas(
                'user',
                fn ($query) => $query->where('division_id', $this->division)
            ))
            ->get()
            ->groupBy('user_id')
            ->map(function ($attendances) {
                $user = $attendances->first()->user;

                return [
                    'name' => $user->name,
                    'nip' => $user->nip,
                    'division' => $user->division?->name ?? '-',
                    'days' => $attendances->where('overtime_minutes', '>', 0)->count(),
                    'overtime_minutes' => $attendances->sum('overtime_minutes'),
                    'early_leave_minutes' => $attendances->sum('early_leave_minutes'),
                ];
            })
            ->sortByDesc('overtime_minutes')
            ->values();

        return view('livewire.admin.overtime', [
            'rows' => $rows,
            'divisions' => Division::orderBy('name')->get(),
            'totalOvertime' => $rows->sum('overtime_minutes'),
            'totalEarlyLeave' => $rows->sum('early_leave_minutes'),
        ]);
    }
}
