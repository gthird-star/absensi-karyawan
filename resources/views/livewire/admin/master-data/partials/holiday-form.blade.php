<div class="grid grid-cols-1 gap-4">
  <div>
    <x-label for="holiday_date" value="Tanggal" />
    <x-input id="holiday_date" class="mt-1 block w-full" type="date" wire:model="date" />
    @error('date')<x-input-error for="date" class="mt-2" message="{{ $message }}" />@enderror
  </div>
  <div>
    <x-label for="holiday_name" value="Keterangan" />
    <x-input id="holiday_name" class="mt-1 block w-full" type="text" wire:model="name" />
    @error('name')<x-input-error for="name" class="mt-2" message="{{ $message }}" />@enderror
  </div>
</div>
