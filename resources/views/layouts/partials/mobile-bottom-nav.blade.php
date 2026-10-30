@auth
  @if (!(Auth::user()->isAdmin ?? false))
    <nav class="fixed inset-x-0 bottom-0 z-40 border-t border-[#E6EDF5] bg-white/95 backdrop-blur-md dark:border-gray-800 dark:bg-[#1E293B]/95 lg:hidden"
      style="padding-bottom: env(safe-area-inset-bottom)">
      <div class="grid grid-cols-5">
        @php
          $items = [
              ['route' => 'home', 'label' => 'Scan', 'icon' => 'qr-code'],
              ['route' => 'attendance-history', 'label' => 'Riwayat', 'icon' => 'clock'],
              ['route' => 'apply-leave', 'label' => 'Izin', 'icon' => 'document-plus'],
              ['route' => 'leave-history', 'label' => 'Cuti', 'icon' => 'clipboard-document-list'],
              ['route' => 'attendance-correction', 'label' => 'Koreksi', 'icon' => 'pencil-square'],
          ];
        @endphp
        @foreach ($items as $item)
          @php $active = request()->routeIs($item['route']); @endphp
          <a href="{{ route($item['route']) }}" @if($active) aria-current="page" @endif
            class="flex flex-col items-center gap-1 py-2.5 text-[11px] font-semibold transition {{ $active ? 'text-primary dark:text-blue-400' : 'text-gray-500 dark:text-gray-400' }}">
            @if ($item['icon'] === 'qr-code')
              <x-heroicon-o-qr-code class="h-5 w-5" />
            @elseif ($item['icon'] === 'clock')
              <x-heroicon-o-clock class="h-5 w-5" />
            @elseif ($item['icon'] === 'document-plus')
              <x-heroicon-o-document-plus class="h-5 w-5" />
            @elseif ($item['icon'] === 'pencil-square')
              <x-heroicon-o-pencil-square class="h-5 w-5" />
            @else
              <x-heroicon-o-clipboard-document-list class="h-5 w-5" />
            @endif
            <span>{{ $item['label'] }}</span>
          </a>
        @endforeach
      </div>
    </nav>
  @endif
@endauth
