<x-app-layout>
  <div
    class="bg-white dark:bg-[#1E293B] shadow-card border border-[#E6EDF5] dark:border-gray-800 rounded-2xl p-6 sm:p-8">
          <div class="mb-4 flex items-center gap-3">
            <x-label for="filter" value="Filter Status"></x-label>
            <select id="filter" onchange="window.location.href = this.value"
              class="rounded-md border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300">
              <option value="{{ route('leave-history', ['filter' => 'all']) }}" {{ $currentFilter === 'all' ? 'selected' : '' }}>Semua</option>
              <option value="{{ route('leave-history', ['filter' => 'pending']) }}" {{ $currentFilter === 'pending' ? 'selected' : '' }}>Menunggu ({{ $pendingCount }})</option>
              <option value="{{ route('leave-history', ['filter' => 'approved']) }}" {{ $currentFilter === 'approved' ? 'selected' : '' }}>Disetujui</option>
              <option value="{{ route('leave-history', ['filter' => 'rejected']) }}" {{ $currentFilter === 'rejected' ? 'selected' : '' }}>Ditolak</option>
            </select>
          </div>
          <div class="mb-4">
            <a href="{{ route('apply-leave') }}">
              <x-button>
                <x-heroicon-o-plus class="mr-2 h-4 w-4" />
                Ajukan Izin Baru
              </x-button>
            </a>
          </div>
          <div class="mb-4 overflow-x-auto rounded-2xl border border-[#E6EDF5] dark:border-gray-700">
            <table class="w-full divide-y divide-gray-200 dark:divide-gray-700">
              <thead class="rounded-t-2xl bg-gray-50 dark:bg-gray-900">
                <tr>
                  <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300">Tipe</th>
                  <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300">Tanggal</th>
                  <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300">Keterangan</th>
                  <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300">Status</th>
                  <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300">Detail</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-200 bg-white dark:divide-gray-700 dark:bg-gray-800">
              @forelse ($leaveRequests as $req)
                  <tr class="group">
                    <td class="px-4 py-3 text-sm font-medium text-gray-900 dark:text-white">
                      {{ $req->type_label }}
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-900 dark:text-white text-nowrap">
                      {{ $req->from_date->format('d/m/Y') }}
                      @if ($req->to_date != $req->from_date)
                        - {{ $req->to_date->format('d/m/Y') }}
                      @endif
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400 max-w-xs truncate">
                      {{ $req->note }}
                    </td>
                    <td class="px-4 py-3 text-sm">
                      @switch($req->status)
                        @case('pending')
                          <span class="rounded-full bg-amber-50 px-2.5 py-1 text-xs font-bold text-amber-700 dark:bg-amber-950/40 dark:text-amber-400">Menunggu</span>
                          @break
                        @case('approved')
                          <span class="rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-bold text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-400">Disetujui</span>
                          @break
                        @case('rejected')
                          <span class="rounded-full bg-rose-50 px-2.5 py-1 text-xs font-bold text-rose-700 dark:bg-rose-950/40 dark:text-rose-400">Ditolak</span>
                          @break
                      @endswitch
                    </td>
                    <td class="px-4 py-3 text-sm">
                      <x-button @click="$dispatch('detail-leave', {{ $req->id }})">Lihat</x-button>
                    </td>
                  </tr>
              @empty
                  <tr>
                    <td colspan="5" class="px-4 py-8 text-center text-sm text-gray-500 dark:text-gray-400">
                      Belum ada pengajuan izin.
                    </td>
                  </tr>
              @endforelse
              </tbody>
            </table>
          </div>
          <div class="mt-4">
            {{ $leaveRequests->links() }}
          </div>
        </div>

    <div x-data="{ open: false, selectedId: null }"
      x-cloak
      x-show="open"
      @detail-leave.window="selectedId = $event.detail; open = true"
      role="dialog" aria-modal="true" aria-labelledby="leave-detail-title"
      class="fixed inset-0 z-50 overflow-y-auto overscroll-contain px-4 py-6"
      @keydown.escape.window="open = false">
      <div x-show="open" class="fixed inset-0 bg-gray-900/50" @click="open = false"></div>

      <div x-show="open"
        class="relative mx-auto my-6 w-full max-w-md rounded-2xl bg-white shadow-xl dark:bg-gray-800">
        <div class="flex items-center justify-between border-b border-[#E6EDF5] px-6 py-4 dark:border-gray-700">
          <h3 id="leave-detail-title" class="text-lg font-semibold text-gray-900 dark:text-white">Detail Pengajuan</h3>
          <button @click="open = false" aria-label="Tutup detail pengajuan"
            class="rounded-lg p-1.5 text-gray-400 transition hover:bg-gray-100 hover:text-gray-600 dark:hover:bg-gray-700 dark:hover:text-gray-200">
            <x-heroicon-o-x-mark class="h-5 w-5" />
          </button>
        </div>

        <div class="max-h-[70vh] overflow-y-auto overscroll-contain px-6 py-4">
          @foreach ($leaveRequests as $req)
            <div x-show="selectedId === {{ $req->id }}" x-cloak>
              <div class="space-y-3 text-sm text-gray-600 dark:text-gray-400">
              <div>
                <span class="font-medium text-gray-900 dark:text-white">Tipe:</span>
                {{ $req->type_label }}
              </div>
              <div>
                <span class="font-medium text-gray-900 dark:text-white">Tanggal:</span>
                {{ $req->from_date->format('d/m/Y') }}
                @if ($req->to_date != $req->from_date)
                  - {{ $req->to_date->format('d/m/Y') }}
                @endif
              </div>
              <div>
                <span class="font-medium text-gray-900 dark:text-white">Keterangan:</span>
                <p class="mt-1">{{ $req->note }}</p>
              </div>
              <div>
                <span class="font-medium text-gray-900 dark:text-white">Status:</span>
                @switch($req->status)
                  @case('pending')
                    <span class="text-amber-600 dark:text-amber-400">Menunggu</span>
                    @break
                  @case('approved')
                    <span class="text-emerald-600 dark:text-emerald-400">Disetujui</span>
                    @break
                  @case('rejected')
                    <span class="text-red-600 dark:text-red-400">Ditolak</span>
                    @if ($req->rejection_reason)
                      <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Alasan: {{ $req->rejection_reason }}</p>
                    @endif
                    @break
                @endswitch
              </div>
              @if ($req->attachment)
                <div>
                  <span class="font-medium text-gray-900 dark:text-white">Lampiran:</span>
                  @php
                    $ext = pathinfo($req->attachment, PATHINFO_EXTENSION);
                    $isImage = in_array(strtolower($ext), ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp']);
                  @endphp
                  @if ($isImage)
                    <div class="mt-1">
                      <img src="{{ Storage::url($req->attachment) }}" alt="Lampiran"
                        class="max-h-48 rounded object-contain">
                    </div>
                  @else
                    <a href="{{ Storage::url($req->attachment) }}" target="_blank"
                      class="ml-1 text-blue-500 hover:underline">Lihat</a>
                  @endif
                </div>
              @endif
              <div>
                <span class="font-medium text-gray-900 dark:text-white">Diajukan:</span>
                {{ $req->created_at->format('d/m/Y H:i') }}
              </div>
            </div>
          </div>
        @endforeach

        <div class="mt-6 flex justify-end">
          <button @click="open = false"
            class="rounded-md bg-gray-200 px-4 py-2 text-sm font-medium text-gray-800 hover:bg-gray-300 dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600">
            Tutup
          </button>
        </div>
      </div>
    </div>
  </div>
</x-app-layout>
