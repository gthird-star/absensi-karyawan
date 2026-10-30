<?php

namespace App\Livewire\Forms;

use App\Models\Shift;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Form;

class ShiftForm extends Form
{
    public ?Shift $shift;

    public $name = '';
    public $start_time = null;
    public $end_time = null;
    public $late_tolerance_minutes = 0;

    public function rules()
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('shifts')->ignore($this->shift)
            ],
            'start_time' => ['required'],
            'end_time' => ['nullable'],
            'late_tolerance_minutes' => ['nullable', 'integer', 'min:0', 'max:120'],
        ];
    }

    public function setShift(Shift $shift)
    {
        $this->shift = $shift;
        $this->name = $shift->name;
        $this->start_time = $shift->start_time;
        $this->end_time = $shift->end_time;
        $this->late_tolerance_minutes = $shift->late_tolerance_minutes;
        return $this;
    }

    private function payload(): array
    {
        return [
            ...$this->all(),
            'is_cross_midnight' => $this->end_time
                && substr((string) $this->end_time, 0, 8) <= substr((string) $this->start_time, 0, 8),
        ];
    }

    public function store()
    {
        if (Auth::user()->isNotAdmin) {
            return abort(403);
        }
        $this->validate();
        Shift::create($this->payload());
        $this->reset();
    }

    public function update()
    {
        if (Auth::user()->isNotAdmin) {
            return abort(403);
        }
        $this->validate();
        $this->shift->update($this->payload());
        $this->reset();
    }

    public function delete()
    {
        if (Auth::user()->isNotAdmin) {
            return abort(403);
        }
        $this->shift->delete();
        $this->reset();
    }
}
