<div>
  <div class="mb-4 flex-col items-center gap-5 sm:flex-row md:flex md:justify-between lg:mr-4">
    <h3 class="mb-4 text-lg font-semibold leading-tight text-gray-800 dark:text-gray-200 md:mb-0">
      Data Cabang
    </h3>
    <x-button wire:click="showCreating">
      <x-heroicon-o-plus class="mr-2 h-4 w-4" /> Tambah Cabang
    </x-button>
  </div>

  <table class="w-full divide-y divide-gray-200 dark:divide-gray-700">
    <thead class="bg-gray-50 dark:bg-gray-900">
      <tr>
        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300">Cabang</th>
        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300">Kode</th>
        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300">Koordinat</th>
        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300">Radius</th>
        <th class="relative px-6 py-3"><span class="sr-only">Actions</span></th>
      </tr>
    </thead>
    <tbody class="divide-y divide-gray-200 bg-white dark:divide-gray-700 dark:bg-gray-800">
      @forelse ($branches as $branch)
        <tr>
          <td class="px-6 py-4 text-sm font-medium text-gray-900 dark:text-white">
            {{ $branch->name }}
            <p class="text-xs font-normal text-gray-500 dark:text-gray-400">{{ $branch->address ?? '-' }}</p>
          </td>
          <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300">{{ $branch->code }}</td>
          <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300">
            {{ $branch->latitude }}, {{ $branch->longitude }}
          </td>
          <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300">{{ $branch->radius }} m</td>
          <td class="relative flex justify-end gap-2 px-6 py-4">
            <x-button wire:click="edit({{ $branch->id }})">Edit</x-button>
            <x-danger-button wire:click="confirmDeletion({{ $branch->id }}, '{{ $branch->name }}')">Delete</x-danger-button>
          </td>
        </tr>
      @empty
        <tr>
          <td colspan="5" class="px-6 py-6 text-center text-sm text-gray-500 dark:text-gray-400">
            Belum ada cabang.
          </td>
        </tr>
      @endforelse
    </tbody>
  </table>

  <x-confirmation-modal wire:model="confirmingDeletion">
    <x-slot name="title">Hapus Cabang</x-slot>
    <x-slot name="content">Apakah Anda yakin ingin menghapus <b>{{ $deleteName }}</b>?</x-slot>
    <x-slot name="footer">
      <x-secondary-button wire:click="$toggle('confirmingDeletion')" wire:loading.attr="disabled">Cancel</x-secondary-button>
      <x-danger-button class="ml-2" wire:click="delete" wire:loading.attr="disabled">Confirm</x-danger-button>
    </x-slot>
  </x-confirmation-modal>

  <x-dialog-modal wire:model="creating">
    <x-slot name="title">Cabang Baru</x-slot>
    <x-slot name="content">
      @include('livewire.admin.master-data.partials.branch-form')
    </x-slot>
    <x-slot name="footer">
      <x-secondary-button wire:click="$toggle('creating')" wire:loading.attr="disabled">Cancel</x-secondary-button>
      <x-button class="ml-2" wire:click="create" wire:loading.attr="disabled">Confirm</x-button>
    </x-slot>
  </x-dialog-modal>

  <x-dialog-modal wire:model="editing">
    <x-slot name="title">Edit Cabang</x-slot>
    <x-slot name="content">
      @include('livewire.admin.master-data.partials.branch-form')
    </x-slot>
    <x-slot name="footer">
      <x-secondary-button wire:click="$toggle('editing')" wire:loading.attr="disabled">Cancel</x-secondary-button>
      <x-button class="ml-2" wire:click="update" wire:loading.attr="disabled">Confirm</x-button>
    </x-slot>
  </x-dialog-modal>
</div>
