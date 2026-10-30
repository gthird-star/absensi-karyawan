@once
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
    integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
@endonce
<x-modal wire:model="showDetail" onclose="removeMap()">
  <div class="px-6 py-4">
    @if ($currentAttendance)
      @php
        $isExcused = $currentAttendance['status'] == 'excused' || $currentAttendance['status'] == 'sick';
        $showMap = $currentAttendance['latitude'] && $currentAttendance['longitude'] && !$isExcused;
      @endphp
      <h3 class="mb-3 text-xl font-semibold dark:text-white">{{ $currentAttendance['name'] }}</h3>
      @if ($currentAttendance['corrected_at'] ?? false)
        <p class="mb-3 text-sm text-amber-600 dark:text-amber-400">
          Dikoreksi oleh {{ $currentAttendance['corrected_by_name'] ?? '-' }}.
          @if ($currentAttendance['correction_note'] ?? false)
            Alasan: {{ $currentAttendance['correction_note'] }}
          @endif
        </p>
      @endif
      <div class="mb-3 grid grid-cols-2 gap-3 text-sm">
        <div>
          <p class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">NIP</p>
          <p class="font-semibold text-[#14264A] dark:text-white">{{ $currentAttendance['nip'] ?? '-' }}</p>
        </div>
        <div>
          <p class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Status</p>
          <p class="font-semibold text-[#14264A] dark:text-white">{{ __('status_' . $currentAttendance['status']) }}</p>
        </div>
        <div>
          <p class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Tanggal</p>
          <p class="font-semibold text-[#14264A] dark:text-white">{{ $currentAttendance['date'] }}</p>
        </div>
        <div>
          <p class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Shift</p>
          <p class="font-semibold text-[#14264A] dark:text-white">{{ $currentAttendance['shift']['name'] ?? '-' }}</p>
        </div>
      </div>
      @if ($isExcused)
        <div class="mb-3">
          <p class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Alamat</p>
          <p class="text-sm text-[#14264A] dark:text-white">{{ $currentAttendance['address'] ?? '-' }}</p>
        </div>
      @endif
      <div class="flex flex-col gap-3">
        @if ($currentAttendance['attachment'])
          <x-label for="attachment" value="{{ __('Attachment') }}"></x-label>
          <img src="{{ $currentAttendance['attachment'] }}" alt="Attachment"
            class="max-h-48 object-contain sm:max-h-64 md:max-h-72">
        @endif
        @if ($currentAttendance['note'])
          <div>
            <x-label value="Keterangan" />
            <p class="text-sm text-gray-700 dark:text-gray-300">{{ $currentAttendance['note'] }}</p>
          </div>
        @endif
        @if ($showMap)
          <div>
            <x-label for="map" value="Koordinat Lokasi Absen"></x-label>
            <a href="https://maps.google.com/maps?q={{ $currentAttendance['latitude'] }},{{ $currentAttendance['longitude'] }}"
              target="_blank" rel="noopener"
              class="text-sm font-semibold text-primary underline dark:text-blue-400">
              {{ $currentAttendance['latitude'] }}, {{ $currentAttendance['longitude'] }} — Buka di Google Maps
            </a>
            <div class="my-2 h-52 w-full md:h-64" id="map"></div>
          </div>
        @endif
        <div class="grid grid-cols-2 gap-3 text-sm">
          <div>
            <p class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Waktu Masuk</p>
            <p class="font-mono font-semibold text-[#14264A] dark:text-white">{{ $currentAttendance['time_in'] ?? '-' }}</p>
          </div>
          <div>
            <p class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Waktu Keluar</p>
            <p class="font-mono font-semibold text-[#14264A] dark:text-white">{{ $currentAttendance['time_out'] ?? '-' }}</p>
          </div>
          @if (($currentAttendance['overtime_minutes'] ?? 0) > 0 || ($currentAttendance['early_leave_minutes'] ?? 0) > 0)
            <div>
              <p class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Lembur</p>
              <p class="font-semibold text-emerald-600 dark:text-emerald-400">
                {{ intdiv($currentAttendance['overtime_minutes'] ?? 0, 60) }} jam {{ ($currentAttendance['overtime_minutes'] ?? 0) % 60 }} menit
              </p>
            </div>
            <div>
              <p class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Pulang Cepat</p>
              <p class="font-semibold text-rose-600 dark:text-rose-400">
                {{ intdiv($currentAttendance['early_leave_minutes'] ?? 0, 60) }} jam {{ ($currentAttendance['early_leave_minutes'] ?? 0) % 60 }} menit
              </p>
            </div>
          @endif
          @if ($currentAttendance['barcode'] ?? false)
            <div>
              <p class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Barcode</p>
              <p class="font-semibold text-[#14264A] dark:text-white">{{ $currentAttendance['barcode']['name'] }}</p>
            </div>
          @endif
        </div>

        @if (($currentAttendance['edit_logs'] ?? []) !== [])
          <div>
            <x-label value="Riwayat Koreksi" />
            <ul class="mt-1 space-y-1 text-sm text-gray-600 dark:text-gray-300">
              @foreach ($currentAttendance['edit_logs'] as $log)
                <li>{{ $log['created_at'] }} - {{ $log['editor'] }}: {{ $log['reason'] }}</li>
              @endforeach
            </ul>
          </div>
        @endif

        @if (auth()->user()?->isAdmin)
          <div>
            <x-secondary-button type="button" wire:click="openCorrection">
              Koreksi Absensi
            </x-secondary-button>
          </div>
        @endif
      </div>
    @endif
  </div>
</x-modal>

@if (auth()->user()?->isAdmin)
  <x-dialog-modal wire:model="showCorrection">
    <x-slot name="title">Koreksi Absensi</x-slot>

    <x-slot name="content">
      <div class="grid grid-cols-2 gap-4">
        <div>
          <x-label for="correction_date" value="Tanggal" />
          <x-input id="correction_date" class="mt-1 block w-full" type="date" wire:model="correction.date" />
          @error('correction.date')
            <x-input-error for="correction.date" class="mt-2" message="{{ $message }}" />
          @enderror
        </div>
        <div>
          <x-label for="correction_shift" value="Shift" />
          <x-select id="correction_shift" class="mt-1 block w-full" wire:model="correction.shift_id">
            <option value="">Tanpa shift</option>
            @foreach (\App\Models\Shift::orderBy('name')->get(['id', 'name', 'start_time', 'end_time']) as $shiftOption)
              <option value="{{ $shiftOption->id }}">
                {{ $shiftOption->name }} | {{ $shiftOption->start_time }} - {{ $shiftOption->end_time }}
              </option>
            @endforeach
          </x-select>
          @error('correction.shift_id')
            <x-input-error for="correction.shift_id" class="mt-2" message="{{ $message }}" />
          @enderror
        </div>
        <div>
          <x-label for="correction_time_in" value="Waktu Masuk" />
          <x-input id="correction_time_in" class="mt-1 block w-full" type="time" wire:model="correction.time_in" />
          @error('correction.time_in')
            <x-input-error for="correction.time_in" class="mt-2" message="{{ $message }}" />
          @enderror
        </div>
        <div>
          <x-label for="correction_time_out" value="Waktu Keluar" />
          <x-input id="correction_time_out" class="mt-1 block w-full" type="time" wire:model="correction.time_out" />
          @error('correction.time_out')
            <x-input-error for="correction.time_out" class="mt-2" message="{{ $message }}" />
          @enderror
        </div>
        <div class="col-span-2">
          <x-label for="correction_status" value="Status" />
          <x-select id="correction_status" class="mt-1 block w-full" wire:model="correction.status">
            @foreach (['present', 'late', 'incomplete', 'excused', 'sick', 'absent'] as $statusOption)
              <option value="{{ $statusOption }}">{{ __('status_'.$statusOption) }}</option>
            @endforeach
          </x-select>
          @error('correction.status')
            <x-input-error for="correction.status" class="mt-2" message="{{ $message }}" />
          @enderror
        </div>
        <div class="col-span-2">
          <x-label for="correction_reason" value="Alasan Koreksi" />
          <x-textarea id="correction_reason" class="mt-1 block w-full" wire:model="correction.reason" />
          @error('correction.reason')
            <x-input-error for="correction.reason" class="mt-2" message="{{ $message }}" />
          @enderror
        </div>
      </div>
    </x-slot>

    <x-slot name="footer">
      <x-secondary-button wire:click="$set('showCorrection', false)" wire:loading.attr="disabled">
        {{ __('Cancel') }}
      </x-secondary-button>
      <x-button class="ml-2" wire:click="saveCorrection" wire:loading.attr="disabled">
        <span wire:loading.remove wire:target="saveCorrection">Simpan Koreksi</span>
        <span wire:loading wire:target="saveCorrection">Menyimpan…</span>
      </x-button>
    </x-slot>
  </x-dialog-modal>
@endif

@push('attendance-detail-scripts')
  <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
    integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
  <script>
    let map = null;

    function setLocation(lat, lng) {
      removeMap();
      setTimeout(() => {
        map = L.map('map').setView([Number(lat), Number(lng)], 19);
        L.marker([Number(lat), Number(lng)]).addTo(map);
        L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
          maxZoom: 21,
        }).addTo(map);
      }, 500);
    }

    function removeMap() {
      if (map !== null) map.remove();
      map = null;
    }
  </script>
@endpush
