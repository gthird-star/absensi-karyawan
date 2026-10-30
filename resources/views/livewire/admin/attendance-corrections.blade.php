<div>
  <div class="mb-4 flex flex-col justify-between gap-3 sm:flex-row sm:items-center">
    <div>
      <h3 class="text-lg font-bold text-[#14264A] dark:text-white">Pengajuan Koreksi Absensi</h3>
      <p class="text-sm text-[#64748B] dark:text-gray-400">Verifikasi lupa absen atau jam absen yang salah.</p>
    </div>
    <div class="inline-flex rounded-xl border border-[#E6EDF5] bg-white p-1 dark:border-gray-700 dark:bg-gray-800">
      @foreach (['pending' => 'Menunggu', 'approved' => 'Disetujui', 'rejected' => 'Ditolak', 'all' => 'Semua'] as $value => $label)
        <button type="button" wire:click="$set('filter', '{{ $value }}')"
          class="rounded-lg px-3 py-1.5 text-xs font-semibold transition {{ $filter === $value ? 'bg-primary text-white' : 'text-[#64748B] hover:bg-[#F3F7FC] dark:text-gray-400 dark:hover:bg-gray-700' }}">
          {{ $label }}
        </button>
      @endforeach
    </div>
  </div>

  <div class="overflow-x-auto">
    <table class="w-full text-left text-sm">
      <thead>
        <tr class="border-b border-[#E6EDF5] text-[11px] font-bold uppercase tracking-wider text-[#64748B] dark:border-gray-800 dark:text-gray-400">
          <th class="px-0 py-2">Karyawan</th>
          <th class="px-0 py-2">Tanggal</th>
          <th class="px-0 py-2">Jam Diajukan</th>
          <th class="px-0 py-2">Alasan</th>
          <th class="px-0 py-2">Status</th>
          <th class="px-0 py-2 text-right">Aksi</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-[#E6EDF5] dark:divide-gray-800">
        @forelse ($requests as $item)
          <tr>
            <td class="py-3">
              <p class="font-semibold text-[#14264A] dark:text-white">{{ $item->user->name }}</p>
              <p class="text-xs text-gray-500 dark:text-gray-400">{{ $item->user->nip }}</p>
            </td>
            <td class="py-3 text-gray-700 dark:text-gray-300">{{ $item->date?->format('d/m/Y') }}</td>
            <td class="py-3 text-gray-700 dark:text-gray-300">
              Masuk: {{ $item->time_in ? substr($item->time_in, 0, 5) : '-' }}<br>
              Keluar: {{ $item->time_out ? substr($item->time_out, 0, 5) : '-' }}
            </td>
            <td class="max-w-xs py-3 text-gray-700 dark:text-gray-300">{{ $item->reason }}</td>
            <td class="py-3">
              <span class="rounded-full px-2.5 py-1 text-xs font-bold {{ $item->status === 'approved' ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-400' : ($item->status === 'rejected' ? 'bg-rose-50 text-rose-700 dark:bg-rose-950/40 dark:text-rose-400' : 'bg-amber-50 text-amber-700 dark:bg-amber-950/40 dark:text-amber-400') }}">
                {{ $item->status_label }}
              </span>
              @if ($item->rejection_reason)
                <p class="mt-1 text-xs text-rose-600 dark:text-rose-400">{{ $item->rejection_reason }}</p>
              @endif
            </td>
            <td class="py-3 text-right">
              @if ($item->status === 'pending')
                <div class="flex justify-end gap-2">
                  <x-button wire:click="confirmApprove({{ $item->id }})">Setujui</x-button>
                  <x-danger-button wire:click="confirmReject({{ $item->id }})">Tolak</x-danger-button>
                </div>
              @else
                <span class="text-xs text-gray-400">-</span>
              @endif
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="6" class="py-8 text-center text-[#8A9AB0] dark:text-gray-400">Tidak ada pengajuan koreksi.</td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <div class="mt-4">{{ $requests->links() }}</div>

  <x-confirmation-modal wire:model="confirmingApproval">
    <x-slot name="title">Setujui Koreksi</x-slot>
    <x-slot name="content">
      Setujui koreksi absensi untuk <b>{{ $approvingName }}</b>? Data absensi akan diperbarui dan dicatat di audit log.
    </x-slot>
    <x-slot name="footer">
      <x-secondary-button wire:click="$set('confirmingApproval', false)">Batal</x-secondary-button>
      <x-button class="ml-2" wire:click="executeApprove" wire:loading.attr="disabled">
        <span wire:loading.remove wire:target="executeApprove">Setujui</span>
        <span wire:loading wire:target="executeApprove">Menyimpan...</span>
      </x-button>
    </x-slot>
  </x-confirmation-modal>

  <x-dialog-modal wire:model="rejectingId">
    <x-slot name="title">Tolak Koreksi</x-slot>
    <x-slot name="content">
      <x-label for="rejectReason" value="Alasan Penolakan" />
      <x-textarea id="rejectReason" class="mt-1 block w-full" wire:model="rejectReason" />
      @error('rejectReason')<x-input-error for="rejectReason" class="mt-2" message="{{ $message }}" />@enderror
    </x-slot>
    <x-slot name="footer">
      <x-secondary-button wire:click="$set('rejectingId', null)">Batal</x-secondary-button>
      <x-danger-button class="ml-2" wire:click="reject">Tolak Pengajuan</x-danger-button>
    </x-slot>
  </x-dialog-modal>
</div>
