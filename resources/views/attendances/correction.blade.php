<x-app-layout>
  <div class="space-y-6">
    <div class="rounded-2xl border border-[#E6EDF5] bg-white p-6 shadow-card dark:border-gray-800 dark:bg-[#1E293B] sm:p-8">
      <h3 class="text-lg font-bold text-[#14264A] dark:text-white">Ajukan Koreksi Absensi</h3>
      <p class="mt-1 text-sm text-[#64748B] dark:text-gray-400">
        Gunakan form ini jika lupa absen atau jam absen salah. HRD akan memverifikasi.
      </p>

      <form action="{{ route('store-attendance-correction') }}" method="post" class="mt-5">
        @csrf
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
          <div>
            <x-label for="date" value="Tanggal Absen" />
            <x-input id="date" name="date" type="date" class="mt-1 block w-full" max="{{ date('Y-m-d') }}"
              value="{{ old('date', date('Y-m-d')) }}" required />
            @error('date')<x-input-error for="date" class="mt-2" message="{{ $message }}" />@enderror
          </div>
          <div>
            <x-label for="time_in" value="Jam Masuk (opsional)" />
            <x-input id="time_in" name="time_in" type="time" class="mt-1 block w-full" value="{{ old('time_in') }}" />
            @error('time_in')<x-input-error for="time_in" class="mt-2" message="{{ $message }}" />@enderror
          </div>
          <div>
            <x-label for="time_out" value="Jam Keluar (opsional)" />
            <x-input id="time_out" name="time_out" type="time" class="mt-1 block w-full" value="{{ old('time_out') }}" />
            @error('time_out')<x-input-error for="time_out" class="mt-2" message="{{ $message }}" />@enderror
          </div>
        </div>

        <div class="mt-4">
          <x-label for="reason" value="Alasan" />
          <x-textarea id="reason" name="reason" class="mt-1 block w-full" required>{{ old('reason') }}</x-textarea>
          @error('reason')<x-input-error for="reason" class="mt-2" message="{{ $message }}" />@enderror
        </div>

        <div class="mt-4 flex justify-end">
          <x-button>Kirim Pengajuan</x-button>
        </div>
      </form>
    </div>

    <div class="rounded-2xl border border-[#E6EDF5] bg-white p-6 shadow-card dark:border-gray-800 dark:bg-[#1E293B] sm:p-8">
      <h3 class="mb-4 text-lg font-bold text-[#14264A] dark:text-white">Riwayat Pengajuan Koreksi</h3>
      <div class="overflow-x-auto">
        <table class="w-full text-left text-sm">
          <thead>
            <tr class="border-b border-[#E6EDF5] text-[11px] font-bold uppercase tracking-wider text-[#64748B] dark:border-gray-800 dark:text-gray-400">
              <th class="px-0 py-2">Tanggal</th>
              <th class="px-0 py-2">Jam Masuk</th>
              <th class="px-0 py-2">Jam Keluar</th>
              <th class="px-0 py-2">Alasan</th>
              <th class="px-0 py-2">Status</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-[#E6EDF5] dark:divide-gray-800">
            @forelse ($requests as $item)
              <tr>
                <td class="py-2 font-semibold text-[#14264A] dark:text-white">{{ $item->date?->format('d/m/Y') }}</td>
                <td class="py-2 text-gray-700 dark:text-gray-300">{{ $item->time_in ? substr($item->time_in, 0, 5) : '-' }}</td>
                <td class="py-2 text-gray-700 dark:text-gray-300">{{ $item->time_out ? substr($item->time_out, 0, 5) : '-' }}</td>
                <td class="py-2 text-gray-700 dark:text-gray-300">{{ $item->reason }}</td>
                <td class="py-2">
                  <span class="rounded-full px-2.5 py-1 text-xs font-bold {{ $item->status === 'approved' ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-400' : ($item->status === 'rejected' ? 'bg-rose-50 text-rose-700 dark:bg-rose-950/40 dark:text-rose-400' : 'bg-amber-50 text-amber-700 dark:bg-amber-950/40 dark:text-amber-400') }}">
                    {{ $item->status_label }}
                  </span>
                  @if ($item->rejection_reason)
                    <p class="mt-1 text-xs text-rose-600 dark:text-rose-400">{{ $item->rejection_reason }}</p>
                  @endif
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="5" class="py-6 text-center text-[#8A9AB0] dark:text-gray-400">Belum ada pengajuan koreksi.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
      <div class="mt-4">{{ $requests->links() }}</div>
    </div>
  </div>
</x-app-layout>
