<div>
  <h3 class="mb-4 text-lg font-semibold leading-tight text-gray-800 dark:text-gray-200">
    Rekap Lembur & Pulang Cepat
  </h3>

  <div class="mb-4 grid grid-cols-1 gap-4 sm:grid-cols-3">
    <div class="rounded-2xl border border-[#E6EDF5] bg-white p-4 shadow-card dark:border-gray-800 dark:bg-[#1E293B]">
      <p class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Total Lembur</p>
      <p class="mt-1 text-2xl font-bold text-gray-900 dark:text-white">
        {{ intdiv($totalOvertime, 60) }} jam {{ $totalOvertime % 60 }} menit
      </p>
    </div>
    <div class="rounded-2xl border border-[#E6EDF5] bg-white p-4 shadow-card dark:border-gray-800 dark:bg-[#1E293B]">
      <p class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Total Pulang Cepat</p>
      <p class="mt-1 text-2xl font-bold text-gray-900 dark:text-white">
        {{ intdiv($totalEarlyLeave, 60) }} jam {{ $totalEarlyLeave % 60 }} menit
      </p>
    </div>
    <div class="rounded-2xl border border-[#E6EDF5] bg-white p-4 shadow-card dark:border-gray-800 dark:bg-[#1E293B]">
      <p class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Karyawan Terdampak</p>
      <p class="mt-1 text-2xl font-bold text-gray-900 dark:text-white">{{ $rows->count() }}</p>
    </div>
  </div>

  <div class="mb-4 flex flex-wrap items-end gap-4">
    <div>
      <x-label for="month" value="Per Bulan" />
      <x-input id="month" type="month" wire:model.live="month" />
    </div>
    <div>
      <x-label for="division" value="Divisi" />
      <x-select id="division" wire:model.live="division">
        <option value="">Semua Divisi</option>
        @foreach ($divisions as $item)
          <option value="{{ $item->id }}">{{ $item->name }}</option>
        @endforeach
      </x-select>
    </div>
  </div>

  <div class="overflow-x-auto">
    <table class="w-full divide-y divide-gray-200 dark:divide-gray-700">
      <thead class="bg-gray-50 dark:bg-gray-900">
        <tr>
          <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300">Nama</th>
          <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300">NIP</th>
          <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300">Divisi</th>
          <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300">Hari Lembur</th>
          <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300">Total Lembur</th>
          <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300">Pulang Cepat</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-200 bg-white dark:divide-gray-700 dark:bg-gray-800">
        @forelse ($rows as $row)
          <tr>
            <td class="px-4 py-3 text-sm font-medium text-gray-900 dark:text-white">{{ $row['name'] }}</td>
            <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">{{ $row['nip'] }}</td>
            <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">{{ $row['division'] }}</td>
            <td class="px-4 py-3 text-center text-sm text-gray-700 dark:text-gray-300">{{ $row['days'] }}</td>
            <td class="px-4 py-3 text-center text-sm font-semibold text-emerald-600 dark:text-emerald-400">
              {{ intdiv($row['overtime_minutes'], 60) }} jam {{ $row['overtime_minutes'] % 60 }} menit
            </td>
            <td class="px-4 py-3 text-center text-sm font-semibold text-rose-600 dark:text-rose-400">
              {{ intdiv($row['early_leave_minutes'], 60) }} jam {{ $row['early_leave_minutes'] % 60 }} menit
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="6" class="px-4 py-6 text-center text-sm text-gray-500 dark:text-gray-400">
              Tidak ada data lembur pada periode ini.
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
