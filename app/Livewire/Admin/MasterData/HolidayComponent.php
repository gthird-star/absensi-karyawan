<?php

namespace App\Livewire\Admin\MasterData;

use App\Models\Holiday;
use Illuminate\Support\Facades\Auth;
use Laravel\Jetstream\InteractsWithBanner;
use Livewire\Component;

class HolidayComponent extends Component
{
    use InteractsWithBanner;

    public $date;

    public $name;

    public $deleteName = null;

    public $creating = false;

    public $editing = false;

    public $confirmingDeletion = false;

    public $selectedId = null;

    protected function rules(): array
    {
        return [
            'date' => ['required', 'date_format:Y-m-d', 'unique:holidays,date,'.($this->selectedId ?? 'NULL')],
            'name' => ['required', 'string', 'max:255'],
        ];
    }

    public function showCreating()
    {
        $this->reset(['date', 'name', 'selectedId']);
        $this->resetErrorBag();
        $this->creating = true;
    }

    public function create()
    {
        if (Auth::user()->isNotAdmin) {
            return abort(403);
        }
        $this->validate();
        Holiday::create($this->only(['date', 'name']));
        $this->creating = false;
        $this->reset(['date', 'name']);
        $this->banner(__('Created successfully.'));
    }

    public function edit($id)
    {
        $this->resetErrorBag();
        $holiday = Holiday::findOrFail($id);
        $this->selectedId = $holiday->id;
        $this->date = $holiday->date?->format('Y-m-d');
        $this->name = $holiday->name;
        $this->editing = true;
    }

    public function update()
    {
        if (Auth::user()->isNotAdmin) {
            return abort(403);
        }
        $this->validate();
        Holiday::findOrFail($this->selectedId)->update($this->only(['date', 'name']));
        $this->editing = false;
        $this->banner(__('Updated successfully.'));
    }

    public function confirmDeletion($id, $name)
    {
        $this->deleteName = $name;
        $this->confirmingDeletion = true;
        $this->selectedId = $id;
    }

    public function delete()
    {
        if (Auth::user()->isNotAdmin) {
            return abort(403);
        }
        Holiday::findOrFail($this->selectedId)->delete();
        $this->confirmingDeletion = false;
        $this->selectedId = null;
        $this->banner(__('Deleted successfully.'));
    }

    public function render()
    {
        return view('livewire.admin.master-data.holiday', [
            'holidays' => Holiday::orderByDesc('date')->get(),
        ]);
    }
}
