<div>
  <div class="mb-4 flex-col items-center gap-5 sm:flex-row md:flex md:justify-between lg:mr-4">
    <h3 class="mb-4 text-lg font-semibold leading-tight text-gray-800 dark:text-gray-200 md:mb-0">
      Data Hari Libur
    </h3>
    <x-button wire:click="showCreating">
      <x-heroicon-o-plus class="mr-2 h-4 w-4" /> Tambah Hari Libur
    </x-button>
  </div>

  <table class="w-full divide-y divide-gray-200 dark:divide-gray-700">
    <thead class="bg-gray-50 dark:bg-gray-900">
      <tr>
        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300">Tanggal</th>
        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300">Keterangan</th>
        <th class="relative px-6 py-3"><span class="sr-only">Actions</span></th>
      </tr>
    </thead>
    <tbody class="divide-y divide-gray-200 bg-white dark:divide-gray-700 dark:bg-gray-800">
      @forelse ($holidays as $holiday)
        <tr>
          <td class="px-6 py-4 text-sm font-medium text-gray-900 dark:text-white">
            {{ $holiday->date?->format('d/m/Y') }}
          </td>
          <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300">{{ $holiday->name }}</td>
          <td class="relative flex justify-end gap-2 px-6 py-4">
            <x-button wire:click="edit({{ $holiday->id }})">Edit</x-button>
            <x-danger-button wire:click="confirmDeletion({{ $holiday->id }}, '{{ $holiday->name }}')">Delete</x-danger-button>
          </td>
        </tr>
      @empty
        <tr>
          <td colspan="3" class="px-6 py-6 text-center text-sm text-gray-500 dark:text-gray-400">
            Belum ada hari libur.
          </td>
        </tr>
      @endforelse
    </tbody>
  </table>

  <x-confirmation-modal wire:model="confirmingDeletion">
    <x-slot name="title">Hapus Hari Libur</x-slot>
    <x-slot name="content">Apakah Anda yakin ingin menghapus <b>{{ $deleteName }}</b>?</x-slot>
    <x-slot name="footer">
      <x-secondary-button wire:click="$toggle('confirmingDeletion')" wire:loading.attr="disabled">Cancel</x-secondary-button>
      <x-danger-button class="ml-2" wire:click="delete" wire:loading.attr="disabled">Confirm</x-danger-button>
    </x-slot>
  </x-confirmation-modal>

  <x-dialog-modal wire:model="creating">
    <x-slot name="title">Hari Libur Baru</x-slot>
    <x-slot name="content">
      @include('livewire.admin.master-data.partials.holiday-form')
    </x-slot>
    <x-slot name="footer">
      <x-secondary-button wire:click="$toggle('creating')" wire:loading.attr="disabled">Cancel</x-secondary-button>
      <x-button class="ml-2" wire:click="create" wire:loading.attr="disabled">Confirm</x-button>
    </x-slot>
  </x-dialog-modal>

  <x-dialog-modal wire:model="editing">
    <x-slot name="title">Edit Hari Libur</x-slot>
    <x-slot name="content">
      @include('livewire.admin.master-data.partials.holiday-form')
    </x-slot>
    <x-slot name="footer">
      <x-secondary-button wire:click="$toggle('editing')" wire:loading.attr="disabled">Cancel</x-secondary-button>
      <x-button class="ml-2" wire:click="update" wire:loading.attr="disabled">Confirm</x-button>
    </x-slot>
  </x-dialog-modal>
</div>
