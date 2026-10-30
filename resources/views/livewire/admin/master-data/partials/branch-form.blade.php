<div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
  <div>
    <x-label for="branch_name" value="Nama Cabang" />
    <x-input id="branch_name" class="mt-1 block w-full" type="text" wire:model="name" />
    @error('name')<x-input-error for="name" class="mt-2" message="{{ $message }}" />@enderror
  </div>
  <div>
    <x-label for="branch_code" value="Kode Cabang" />
    <x-input id="branch_code" class="mt-1 block w-full" type="text" wire:model="code" />
    @error('code')<x-input-error for="code" class="mt-2" message="{{ $message }}" />@enderror
  </div>
  <div class="sm:col-span-2">
    <x-label for="branch_address" value="Alamat" />
    <x-input id="branch_address" class="mt-1 block w-full" type="text" wire:model="address" />
    @error('address')<x-input-error for="address" class="mt-2" message="{{ $message }}" />@enderror
  </div>
  <div>
    <x-label for="branch_lat" value="Latitude" />
    <x-input id="branch_lat" class="mt-1 block w-full" type="number" step="any" wire:model="latitude" />
    @error('latitude')<x-input-error for="latitude" class="mt-2" message="{{ $message }}" />@enderror
  </div>
  <div>
    <x-label for="branch_lng" value="Longitude" />
    <x-input id="branch_lng" class="mt-1 block w-full" type="number" step="any" wire:model="longitude" />
    @error('longitude')<x-input-error for="longitude" class="mt-2" message="{{ $message }}" />@enderror
  </div>
  <div>
    <x-label for="branch_radius" value="Radius Absen (meter)" />
    <x-input id="branch_radius" class="mt-1 block w-full" type="number" wire:model="radius" />
    @error('radius')<x-input-error for="radius" class="mt-2" message="{{ $message }}" />@enderror
  </div>
</div>
