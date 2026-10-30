<div>
  <div class="mb-4 flex flex-col items-start gap-3 sm:flex-row sm:items-center sm:justify-between lg:mr-4">
    <div>
      <h3 class="text-lg font-semibold leading-tight text-gray-800 dark:text-gray-200">
        Data Divisi
      </h3>
      <p class="mt-0.5 text-xs text-gray-500 dark:text-gray-400">Kelola divisi organisasi.</p>
    </div>
    <x-button wire:click="showCreating">
      <x-heroicon-o-plus class="mr-2 h-4 w-4" /> Tambah Divisi
    </x-button>
  </div>
  <div class="mb-4 overflow-x-auto rounded-2xl border border-[#E6EDF5] bg-white shadow-card dark:border-gray-800 dark:bg-gray-800 lg:mr-4">
  <table class="w-full divide-y divide-gray-200 dark:divide-gray-700">
    <thead class="bg-gray-50 dark:bg-gray-900">
      <tr>
        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300">
          Divisi
        </th>
        <th scope="col" class="relative px-6 py-3">
          <span class="sr-only">Actions</span>
        </th>
      </tr>
    </thead>
    <tbody class="divide-y divide-gray-200 bg-white dark:divide-gray-700 dark:bg-gray-800">
      @forelse ($divisions as $division)
        <tr>
          <td class="px-6 py-4 text-sm font-medium text-gray-900 dark:text-white">
            {{ $division->name }}
          </td>
          <td class="relative flex justify-end gap-2 px-6 py-4">
            <x-button wire:click="edit({{ $division->id }})">
              Edit
            </x-button>
            <x-danger-button wire:click="confirmDeletion({{ $division->id }}, '{{ $division->name }}')">
              Hapus
            </x-danger-button>
          </td>
        </tr>
      @empty
        <tr>
          <td colspan="2" class="px-6 py-8 text-center text-sm text-gray-500 dark:text-gray-400">
            Belum ada divisi. Klik "Tambah Divisi" untuk membuat.
          </td>
        </tr>
      @endforelse
    </tbody>
  </table>
  </div>

  <x-confirmation-modal wire:model="confirmingDeletion">
    <x-slot name="title">
      Hapus Divisi
    </x-slot>

    <x-slot name="content">
      Apakah Anda yakin ingin menghapus <b>{{ $deleteName }}</b>? Tindakan ini tidak dapat dibatalkan.
    </x-slot>

    <x-slot name="footer">
      <x-secondary-button wire:click="$toggle('confirmingDeletion')" wire:loading.attr="disabled">
        Batal
      </x-secondary-button>

      <x-danger-button class="ml-2" wire:click="delete" wire:loading.attr="disabled">
        Ya, Hapus
      </x-danger-button>
    </x-slot>
  </x-confirmation-modal>

  <x-dialog-modal wire:model="creating">
    <x-slot name="title">
      Divisi Baru
    </x-slot>

    <form wire:submit="create">
      <x-slot name="content">
        <x-label for="name">Nama Divisi</x-label>
        <x-input id="name" class="mt-1 block w-full" type="text" wire:model="name" />
        @error('name')
          <x-input-error for="name" class="mt-2" message="{{ $message }}" />
        @enderror
      </x-slot>

      <x-slot name="footer">
        <x-secondary-button wire:click="$toggle('creating')" wire:loading.attr="disabled">
          Batal
        </x-secondary-button>

        <x-button class="ml-2" wire:click="create" wire:loading.attr="disabled">
          Simpan
        </x-button>
      </x-slot>
    </form>
  </x-dialog-modal>

  <x-dialog-modal wire:model="editing">
    <x-slot name="title">
      Edit Divisi
    </x-slot>

    <form wire:submit.prevent="update">
      <x-slot name="content">
        <x-label for="name">Nama Divisi</x-label>
        <x-input id="name" class="mt-1 block w-full" type="text" wire:model="name" />
        @error('name')
          <x-input-error for="name" class="mt-2" message="{{ $message }}" />
        @enderror
      </x-slot>

      <x-slot name="footer">
        <x-secondary-button wire:click="$toggle('editing')" wire:loading.attr="disabled">
          Batal
        </x-secondary-button>

        <x-button class="ml-2" wire:click="update" wire:loading.attr="disabled">
          Simpan
        </x-button>
      </x-slot>
    </form>
  </x-dialog-modal>
</div>
