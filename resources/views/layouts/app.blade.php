<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <title>{{ $title ?? config('app.name', 'Soerabaja45 Group - Absensi Karyawan') }}</title>

  <!-- Favicon -->
  <link rel="icon" type="image/png" href="{{ asset('logo.png') }}">

  <!-- Fonts: Inter -->
  <link rel="preconnect" href="https://fonts.bunny.net">
  <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />

  <!-- Scripts -->
  @vite(['resources/css/app.css', 'resources/js/app.js'])

  <!-- Styles -->
  @livewireStyles

  @stack('styles')
</head>

<body class="font-sans antialiased bg-[#F3F7FC] dark:bg-[#0B1120] text-[#14264A] dark:text-gray-100 min-h-screen selection:bg-[#3988F5] selection:text-white"
  x-data="{ sidebarOpen: false }">
  <x-banner />

  @php
    $isAdmin = Auth::user()->isAdmin ?? false;
    $pendingLeaveBadge = $isAdmin ? \App\Models\LeaveRequest::where('status', 'pending')->count() : 0;
    $todayDate = \Carbon\Carbon::now()->translatedFormat('l, d M Y');
  @endphp

  <!-- Mobile Sidebar Backdrop -->
  <div x-show="sidebarOpen" x-cloak
    x-transition:enter="transition-opacity ease-linear duration-200"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition-opacity ease-linear duration-200"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    @click="sidebarOpen = false"
    class="fixed inset-0 bg-gray-900/50 backdrop-blur-xs z-40 lg:hidden">
  </div>

  <!-- Mobile Sidebar Drawer -->
  <div x-show="sidebarOpen" x-cloak
    x-transition:enter="transition ease-in-out duration-300 transform"
    x-transition:enter-start="-translate-x-full"
    x-transition:enter-end="translate-x-0"
    x-transition:leave="transition ease-in-out duration-300 transform"
    x-transition:leave-start="translate-x-0"
    x-transition:leave-end="-translate-x-full"
    class="fixed inset-y-0 left-0 w-72 bg-white dark:bg-[#1E293B] shadow-2xl z-50 lg:hidden flex flex-col justify-between">
    
    <div>
      <!-- Drawer Header with Logo & Close -->
      <div class="h-20 flex items-center justify-between px-6 border-b border-[#E6EDF5] dark:border-gray-800">
        <a href="{{ $isAdmin ? route('admin.dashboard') : route('home') }}" class="flex items-center">
          <img src="{{ asset('logo.png') }}" alt="Soerabaja45 Group" class="h-8 max-w-[170px] object-contain">
        </a>
        <button @click="sidebarOpen = false" class="p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 rounded-lg">
          <x-heroicon-o-x-mark class="w-6 h-6" />
        </button>
      </div>

      <!-- Drawer Nav List -->
      <div class="px-4 py-6 space-y-6 overflow-y-auto max-h-[calc(100vh-10rem)]">
        @include('layouts.partials.sidebar-nav')
      </div>
    </div>

    <!-- Drawer User Footer -->
    <div class="p-4 border-t border-[#E6EDF5] dark:border-gray-800 bg-[#F8FAFD] dark:bg-gray-800/40">
      @include('layouts.partials.sidebar-user')
    </div>
  </div>

  <!-- Desktop Persistent Sidebar (Finora Layout) -->
  <aside class="hidden lg:flex lg:flex-col lg:w-64 lg:fixed lg:inset-y-0 bg-white dark:bg-[#1E293B] border-r border-[#E6EDF5] dark:border-gray-800 z-30 transition-all duration-200">
    <!-- Brand / Logo Area -->
    <div class="h-20 flex items-center px-6 border-b border-[#E6EDF5] dark:border-gray-800 bg-white dark:bg-[#1E293B]">
      <a href="{{ $isAdmin ? route('admin.dashboard') : route('home') }}" class="flex items-center group">
        <img src="{{ asset('logo.png') }}" alt="Soerabaja45 Group" class="h-8 max-w-[180px] object-contain transition-transform group-hover:scale-102">
      </a>
    </div>

    <!-- Navigation Menu (Scrollable) -->
    <div class="flex-1 overflow-y-auto px-4 py-6 space-y-6">
      @include('layouts.partials.sidebar-nav')
    </div>

    <!-- Bottom User Profile Area -->
    <div class="p-4 border-t border-[#E6EDF5] dark:border-gray-800 bg-[#F8FAFD] dark:bg-gray-800/40">
      @include('layouts.partials.sidebar-user')
    </div>
  </aside>

  <!-- Main Content Wrapper -->
  <div class="lg:pl-64 flex flex-col min-h-screen">
    <!-- Top Bar Header (Finora Style) -->
    <header class="sticky top-0 z-20 bg-white/90 dark:bg-[#1E293B]/90 backdrop-blur-md border-b border-[#E6EDF5] dark:border-gray-800 h-20 px-4 sm:px-6 lg:px-8 flex items-center justify-between transition-colors shadow-xs">
      
      <!-- Left side: Mobile Toggle & Page Context -->
      <div class="flex items-center gap-3">
        <button @click="sidebarOpen = true" class="lg:hidden p-2.5 rounded-xl text-gray-500 hover:text-gray-700 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-800 transition">
          <x-heroicon-o-bars-3 class="w-6 h-6" />
        </button>

        <div class="flex flex-col">
          <span class="text-xs font-semibold uppercase tracking-wider text-primary">Soerabaja45 Group</span>
          <span class="text-sm sm:text-base font-bold text-[#14264A] dark:text-white truncate">
            {{ $title ?? ($header ?? 'Dashboard Presensi') }}
          </span>
        </div>
      </div>

      <!-- Center: Finora Search Pill -->
      <div class="hidden md:flex items-center relative w-72 lg:w-80">
        <x-heroicon-o-magnifying-glass class="w-4 h-4 text-gray-400 absolute left-3.5 pointer-events-none" />
        <input type="text"
          placeholder="Cari data, menu, karyawan..."
          class="w-full pl-10 pr-4 py-2 bg-[#F8FAFD] dark:bg-gray-800/70 border border-[#E6EDF5] dark:border-gray-700/80 rounded-full text-xs text-gray-700 dark:text-gray-200 placeholder-gray-400 focus:outline-none focus:border-primary focus:bg-white dark:focus:bg-gray-800 transition shadow-inner/5">
      </div>

      <!-- Right side: Date Badge, Theme Toggle, Notification & Profile -->
      <div class="flex items-center gap-2.5 sm:gap-4">
        <!-- Date Badge -->
        <div class="hidden sm:inline-flex items-center gap-2 px-3.5 py-1.5 bg-[#F3F7FC] dark:bg-gray-800/80 border border-[#E6EDF5] dark:border-gray-700 rounded-full text-xs font-semibold text-gray-700 dark:text-gray-300">
          <x-heroicon-o-calendar class="w-4 h-4 text-primary" />
          <span>{{ $todayDate }}</span>
        </div>

        <!-- Theme Toggle -->
        <div class="p-1 rounded-xl bg-gray-50 dark:bg-gray-800 border border-[#E6EDF5] dark:border-gray-700 flex items-center">
          <x-theme-toggle />
        </div>

        <!-- Notification Bell (for Leave Requests) -->
        @if ($isAdmin)
          <a href="{{ route('admin.leave-requests') }}" title="Pengajuan Izin Pending"
            class="relative p-2 rounded-xl text-gray-500 hover:text-gray-700 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-800 transition">
            <x-heroicon-o-bell class="w-5 h-5" />
            @if ($pendingLeaveBadge > 0)
              <span class="absolute top-1.5 right-1.5 flex h-2.5 w-2.5">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-rose-400 opacity-75"></span>
                <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-rose-500"></span>
              </span>
            @endif
          </a>
        @endif

        <!-- Profile Dropdown -->
        <div class="relative ms-1">
          <x-dropdown align="right" width="48">
            <x-slot name="trigger">
              <button class="flex items-center gap-2 p-1 rounded-full border border-[#E6EDF5] dark:border-gray-700 hover:border-primary/50 focus:outline-none transition group">
                @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
                  <img class="h-8 w-8 rounded-full object-cover" src="{{ Auth::user()->profile_photo_url }}" alt="{{ Auth::user()->name }}" />
                @else
                  <div class="h-8 w-8 rounded-full bg-primary/10 text-primary dark:bg-primary/20 flex items-center justify-center font-bold text-xs uppercase">
                    {{ substr(Auth::user()->name, 0, 2) }}
                  </div>
                @endif
                <x-heroicon-o-chevron-down class="w-3.5 h-3.5 text-gray-400 me-1 hidden sm:block group-hover:text-primary transition" />
              </button>
            </x-slot>

            <x-slot name="content">
              <div class="px-4 py-2.5 border-b border-gray-100 dark:border-gray-600">
                <p class="text-xs font-semibold text-gray-800 dark:text-white truncate">{{ Auth::user()->name }}</p>
                <p class="text-[11px] text-gray-500 dark:text-gray-400 truncate">{{ Auth::user()->email }}</p>
                <span class="inline-block mt-1 text-[10px] font-bold px-2 py-0.5 rounded-full {{ $isAdmin ? 'bg-blue-50 text-blue-600 dark:bg-blue-900/40 dark:text-blue-300' : 'bg-emerald-50 text-emerald-600 dark:bg-emerald-900/40 dark:text-emerald-300' }}">
                  {{ $isAdmin ? 'Administrator' : 'Karyawan' }}
                </span>
              </div>

              <x-dropdown-link href="{{ route('profile.show') }}" class="flex items-center gap-2">
                <x-heroicon-o-user class="w-4 h-4 text-gray-400" />
                <span>{{ __('Profile') }}</span>
              </x-dropdown-link>

              <div class="border-t border-gray-100 dark:border-gray-600 my-1"></div>

              <form method="POST" action="{{ route('logout') }}" x-data>
                @csrf
                <x-dropdown-link href="{{ route('logout') }}" @click.prevent="$root.submit();" class="flex items-center gap-2 text-rose-600 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-900/20">
                  <x-heroicon-o-arrow-right-on-rectangle class="w-4 h-4 text-rose-500" />
                  <span>{{ __('Log Out') }}</span>
                </x-dropdown-link>
              </form>
            </x-slot>
          </x-dropdown>
        </div>
      </div>
    </header>

    <!-- Page Content Container -->
    <main class="flex-1 p-4 sm:p-6 lg:p-8 max-w-[1600px] w-full mx-auto">
      {{ $slot }}
    </main>

    <!-- Footer -->
    <x-sigsegv-core-dumped />
  </div>

  @stack('modals')

  @livewireScripts

  @stack('scripts')
</body>

</html>
