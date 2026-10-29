@php
  $user = Auth::user();
  $isAdmin = $user->isAdmin ?? false;
@endphp

<div class="flex items-center justify-between">
  <a href="{{ route('profile.show') }}" class="flex items-center gap-3 group min-w-0 flex-1">
    @if (Laravel\Jetstream\Jetstream::managesProfilePhotos() && $user->profile_photo_url)
      <img class="h-9 w-9 rounded-full object-cover shrink-0 border border-gray-200 dark:border-gray-700 group-hover:border-primary transition" 
        src="{{ $user->profile_photo_url }}" alt="{{ $user->name }}" />
    @else
      <div class="h-9 w-9 rounded-full bg-primary/10 text-primary dark:bg-primary/20 flex items-center justify-center font-bold text-xs uppercase shrink-0 border border-transparent group-hover:border-primary/30 transition">
        {{ substr($user->name, 0, 2) }}
      </div>
    @endif
    <div class="min-w-0 flex-1">
      <p class="text-xs font-semibold text-[#14264A] dark:text-white truncate group-hover:text-primary transition">
        {{ $user->name }}
      </p>
      <span class="inline-block text-[10px] font-bold px-2 py-0.2 rounded-full {{ $isAdmin ? 'bg-blue-100 text-blue-700 dark:bg-blue-900/50 dark:text-blue-300' : 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/50 dark:text-emerald-300' }}">
        {{ $isAdmin ? 'Admin' : 'Karyawan' }}
      </span>
    </div>
  </a>

  <!-- Logout Button -->
  <form method="POST" action="{{ route('logout') }}" x-data>
    @csrf
    <button type="submit" title="Keluar" 
      class="p-2 text-gray-400 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-900/30 rounded-xl transition duration-150 cursor-pointer">
      <x-heroicon-o-arrow-right-on-rectangle class="w-5 h-5" />
    </button>
  </form>
</div>
