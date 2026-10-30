<?php

namespace App\Livewire\Admin\MasterData;

use App\Models\Branch;
use Illuminate\Support\Facades\Auth;
use Laravel\Jetstream\InteractsWithBanner;
use Livewire\Component;

class BranchComponent extends Component
{
    use InteractsWithBanner;

    public $name;

    public $code;

    public $address;

    public $latitude = 0;

    public $longitude = 0;

    public $radius = 100;

    public $deleteName = null;

    public $creating = false;

    public $editing = false;

    public $confirmingDeletion = false;

    public $selectedId = null;

    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:50', 'unique:branches,code,'.($this->selectedId ?? 'NULL')],
            'address' => ['nullable', 'string', 'max:255'],
            'latitude' => ['required', 'numeric'],
            'longitude' => ['required', 'numeric'],
            'radius' => ['required', 'numeric', 'min:10', 'max:5000'],
        ];
    }

    public function showCreating()
    {
        $this->reset(['name', 'code', 'address', 'latitude', 'longitude', 'radius', 'selectedId']);
        $this->latitude = 0;
        $this->longitude = 0;
        $this->radius = 100;
        $this->resetErrorBag();
        $this->creating = true;
    }

    public function create()
    {
        if (Auth::user()->isNotAdmin) {
            return abort(403);
        }
        $this->validate();
        Branch::create($this->only(['name', 'code', 'address', 'latitude', 'longitude', 'radius']));
        $this->creating = false;
        $this->reset(['name', 'code', 'address', 'selectedId']);
        $this->banner(__('Created successfully.'));
    }

    public function edit($id)
    {
        $this->resetErrorBag();
        $branch = Branch::findOrFail($id);
        $this->selectedId = $branch->id;
        $this->name = $branch->name;
        $this->code = $branch->code;
        $this->address = $branch->address;
        $this->latitude = $branch->latitude;
        $this->longitude = $branch->longitude;
        $this->radius = $branch->radius;
        $this->editing = true;
    }

    public function update()
    {
        if (Auth::user()->isNotAdmin) {
            return abort(403);
        }
        $this->validate();
        Branch::findOrFail($this->selectedId)->update(
            $this->only(['name', 'code', 'address', 'latitude', 'longitude', 'radius'])
        );
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
        Branch::findOrFail($this->selectedId)->delete();
        $this->confirmingDeletion = false;
        $this->selectedId = null;
        $this->banner(__('Deleted successfully.'));
    }

    public function render()
    {
        return view('livewire.admin.master-data.branch', [
            'branches' => Branch::orderBy('name')->get(),
        ]);
    }
}
