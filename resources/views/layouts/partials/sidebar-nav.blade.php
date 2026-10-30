@php
  $isAdmin = Auth::user()->isAdmin ?? false;
  $pendingLeaves = $isAdmin ? \App\Models\LeaveRequest::where('status', 'pending')->count() : 0;
  $pendingCorrections = $isAdmin ? \App\Models\AttendanceCorrectionRequest::where('status', 'pending')->count() : 0;

  $activeClass = 'flex items-center justify-between px-3.5 py-2.5 rounded-xl bg-[#E8F2FF] text-[#3988F5] dark:bg-blue-900/40 dark:text-blue-300 font-semibold text-xs tracking-wide transition shadow-xs';
  $inactiveClass = 'flex items-center justify-between px-3.5 py-2.5 rounded-xl text-[#64748B] hover:text-[#14264A] hover:bg-[#F3F7FC] dark:text-gray-400 dark:hover:text-white dark:hover:bg-gray-800/60 font-medium text-xs tracking-wide transition';
@endphp

@if ($isAdmin)
  <!-- Admin Navigation -->
  <div class="space-y-1">
    <p class="px-3.5 text-[10px] font-bold uppercase tracking-wider text-gray-400 dark:text-gray-500 mb-2">
      Menu Utama
    </p>

    <!-- Dashboard -->
    <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? $activeClass : $inactiveClass }}" @if (request()->routeIs('admin.dashboard')) aria-current="page" @endif>
      <div class="flex items-center gap-3">
        <x-heroicon-o-squares-2x2 class="w-5 h-5 shrink-0" />
        <span>Dashboard</span>
      </div>
    </a>

    <!-- Attendance -->
    <a href="{{ route('admin.attendances') }}" class="{{ request()->routeIs('admin.attendances*') ? $activeClass : $inactiveClass }}" @if (request()->routeIs('admin.attendances*')) aria-current="page" @endif>
      <div class="flex items-center gap-3">
        <x-heroicon-o-clipboard-document-check class="w-5 h-5 shrink-0" />
        <span>Kehadiran</span>
      </div>
    </a>

    <!-- Overtime -->
    <a href="{{ route('admin.overtime') }}" class="{{ request()->routeIs('admin.overtime') ? $activeClass : $inactiveClass }}" @if (request()->routeIs('admin.overtime')) aria-current="page" @endif>
      <div class="flex items-center gap-3">
        <x-heroicon-o-clock class="w-5 h-5 shrink-0" />
        <span>Lembur</span>
      </div>
    </a>

    <!-- Corrections -->
    <a href="{{ route('admin.corrections') }}" class="{{ request()->routeIs('admin.corrections') ? $activeClass : $inactiveClass }}" @if (request()->routeIs('admin.corrections')) aria-current="page" @endif>
      <div class="flex items-center gap-3">
        <x-heroicon-o-pencil-square class="w-5 h-5 shrink-0" />
        <span>Koreksi Absen</span>
      </div>
      @if ($pendingCorrections > 0)
        <span class="inline-flex items-center justify-center px-2 py-0.5 text-[10px] font-bold rounded-full bg-amber-500 text-white shadow-xs">
          {{ $pendingCorrections }}
        </span>
      @endif
    </a>

    <!-- Barcode -->
    <a href="{{ route('admin.barcodes') }}" class="{{ request()->routeIs('admin.barcodes*') ? $activeClass : $inactiveClass }}" @if (request()->routeIs('admin.barcodes*')) aria-current="page" @endif>
      <div class="flex items-center gap-3">
        <x-heroicon-o-qr-code class="w-5 h-5 shrink-0" />
        <span>Barcode Presensi</span>
      </div>
    </a>

    <!-- Employees -->
    <a href="{{ route('admin.employees') }}" class="{{ request()->routeIs('admin.employees*') ? $activeClass : $inactiveClass }}" @if (request()->routeIs('admin.employees*')) aria-current="page" @endif>
      <div class="flex items-center gap-3">
        <x-heroicon-o-users class="w-5 h-5 shrink-0" />
        <span>Data Karyawan</span>
      </div>
    </a>

    <!-- Leave Requests -->
    <a href="{{ route('admin.leave-requests') }}" class="{{ request()->routeIs('admin.leave-requests*') ? $activeClass : $inactiveClass }}" @if (request()->routeIs('admin.leave-requests*')) aria-current="page" @endif>
      <div class="flex items-center gap-3">
        <x-heroicon-o-document-text class="w-5 h-5 shrink-0" />
        <span>Pengajuan Izin</span>
      </div>
      @if ($pendingLeaves > 0)
        <span class="inline-flex items-center justify-center px-2 py-0.5 text-[10px] font-bold rounded-full bg-rose-500 text-white shadow-xs">
          {{ $pendingLeaves }}
        </span>
      @endif
    </a>
  </div>

  <!-- Master Data Group -->
  <div class="space-y-1 pt-3" x-data="{ open: {{ request()->routeIs('admin.masters.*') ? 'true' : 'true' }} }">
    <div class="flex items-center justify-between px-3.5 mb-2 cursor-pointer" @click="open = !open">
      <p class="text-[10px] font-bold uppercase tracking-wider text-gray-400 dark:text-gray-500">
        Master Data
      </p>
      <x-heroicon-o-chevron-down class="w-3.5 h-3.5 text-gray-400 transition-transform duration-200" ::class="open ? 'rotate-180' : ''" />
    </div>

    <div x-show="open" class="space-y-1 pl-1">
      <a href="{{ route('admin.masters.division') }}" class="{{ request()->routeIs('admin.masters.division') ? $activeClass : $inactiveClass }}" @if (request()->routeIs('admin.masters.division')) aria-current="page" @endif>
        <div class="flex items-center gap-3">
          <x-heroicon-o-building-office-2 class="w-4 h-4 shrink-0" />
          <span>Divisi</span>
        </div>
      </a>

      <a href="{{ route('admin.masters.job-title') }}" class="{{ request()->routeIs('admin.masters.job-title') ? $activeClass : $inactiveClass }}" @if (request()->routeIs('admin.masters.job-title')) aria-current="page" @endif>
        <div class="flex items-center gap-3">
          <x-heroicon-o-briefcase class="w-4 h-4 shrink-0" />
          <span>Jabatan</span>
        </div>
      </a>

      <a href="{{ route('admin.masters.education') }}" class="{{ request()->routeIs('admin.masters.education') ? $activeClass : $inactiveClass }}" @if (request()->routeIs('admin.masters.education')) aria-current="page" @endif>
        <div class="flex items-center gap-3">
          <x-heroicon-o-academic-cap class="w-4 h-4 shrink-0" />
          <span>Pendidikan</span>
        </div>
      </a>

      <a href="{{ route('admin.masters.shift') }}" class="{{ request()->routeIs('admin.masters.shift') ? $activeClass : $inactiveClass }}" @if (request()->routeIs('admin.masters.shift')) aria-current="page" @endif>
        <div class="flex items-center gap-3">
          <x-heroicon-o-clock class="w-4 h-4 shrink-0" />
          <span>Shift Kerja</span>
        </div>
      </a>

      <a href="{{ route('admin.masters.branch') }}" class="{{ request()->routeIs('admin.masters.branch') ? $activeClass : $inactiveClass }}" @if (request()->routeIs('admin.masters.branch')) aria-current="page" @endif>
        <div class="flex items-center gap-3">
          <x-heroicon-o-building-office class="w-4 h-4 shrink-0" />
          <span>Cabang</span>
        </div>
      </a>

      <a href="{{ route('admin.masters.holiday') }}" class="{{ request()->routeIs('admin.masters.holiday') ? $activeClass : $inactiveClass }}" @if (request()->routeIs('admin.masters.holiday')) aria-current="page" @endif>
        <div class="flex items-center gap-3">
          <x-heroicon-o-calendar-days class="w-4 h-4 shrink-0" />
          <span>Hari Libur</span>
        </div>
      </a>

      <a href="{{ route('admin.masters.admin') }}" class="{{ request()->routeIs('admin.masters.admin') ? $activeClass : $inactiveClass }}" @if (request()->routeIs('admin.masters.admin')) aria-current="page" @endif>
        <div class="flex items-center gap-3">
          <x-heroicon-o-shield-check class="w-4 h-4 shrink-0" />
          <span>Kelola Admin</span>
        </div>
      </a>
    </div>
  </div>

  <!-- Import Export Group -->
  <div class="space-y-1 pt-3" x-data="{ open: {{ request()->routeIs('admin.import-export.*') ? 'true' : 'false' }} }">
    <div class="flex items-center justify-between px-3.5 mb-2 cursor-pointer" @click="open = !open">
      <p class="text-[10px] font-bold uppercase tracking-wider text-gray-400 dark:text-gray-500">
        Import & Export
      </p>
      <x-heroicon-o-chevron-down class="w-3.5 h-3.5 text-gray-400 transition-transform duration-200" ::class="open ? 'rotate-180' : ''" />
    </div>

    <div x-show="open" class="space-y-1 pl-1">
      <a href="{{ route('admin.import-export.users') }}" class="{{ request()->routeIs('admin.import-export.users') ? $activeClass : $inactiveClass }}" @if (request()->routeIs('admin.import-export.users')) aria-current="page" @endif>
        <div class="flex items-center gap-3">
          <x-heroicon-o-arrow-up-tray class="w-4 h-4 shrink-0" />
          <span>Karyawan / Admin</span>
        </div>
      </a>

      <a href="{{ route('admin.import-export.attendances') }}" class="{{ request()->routeIs('admin.import-export.attendances') ? $activeClass : $inactiveClass }}" @if (request()->routeIs('admin.import-export.attendances')) aria-current="page" @endif>
        <div class="flex items-center gap-3">
          <x-heroicon-o-arrow-down-tray class="w-4 h-4 shrink-0" />
          <span>Data Absensi</span>
        </div>
      </a>
    </div>
  </div>
@else
  <!-- Regular Employee Navigation -->
  <div class="space-y-1">
    <p class="px-3.5 text-[10px] font-bold uppercase tracking-wider text-gray-400 dark:text-gray-500 mb-2">
      Menu Karyawan
    </p>

    <!-- Home / Scan Presensi -->
    <a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? $activeClass : $inactiveClass }}" @if (request()->routeIs('home')) aria-current="page" @endif>
      <div class="flex items-center gap-3">
        <x-heroicon-o-qr-code class="w-5 h-5 shrink-0" />
        <span>Scan Presensi</span>
      </div>
    </a>

    <!-- Apply Leave -->
    <a href="{{ route('apply-leave') }}" class="{{ request()->routeIs('apply-leave') ? $activeClass : $inactiveClass }}" @if (request()->routeIs('apply-leave')) aria-current="page" @endif>
      <div class="flex items-center gap-3">
        <x-heroicon-o-document-plus class="w-5 h-5 shrink-0" />
        <span>Pengajuan Izin</span>
      </div>
    </a>

    <!-- Attendance History -->
    <a href="{{ route('attendance-history') }}" class="{{ request()->routeIs('attendance-history') ? $activeClass : $inactiveClass }}" @if (request()->routeIs('attendance-history')) aria-current="page" @endif>
      <div class="flex items-center gap-3">
        <x-heroicon-o-clock class="w-5 h-5 shrink-0" />
        <span>Riwayat Presensi</span>
      </div>
    </a>

    <!-- Attendance Correction -->
    <a href="{{ route('attendance-correction') }}" class="{{ request()->routeIs('attendance-correction') ? $activeClass : $inactiveClass }}" @if (request()->routeIs('attendance-correction')) aria-current="page" @endif>
      <div class="flex items-center gap-3">
        <x-heroicon-o-pencil-square class="w-5 h-5 shrink-0" />
        <span>Koreksi Absen</span>
      </div>
    </a>

    <!-- Leave History -->
    <a href="{{ route('leave-history') }}" class="{{ request()->routeIs('leave-history') ? $activeClass : $inactiveClass }}" @if (request()->routeIs('leave-history')) aria-current="page" @endif>
      <div class="flex items-center gap-3">
        <x-heroicon-o-clipboard-document-list class="w-5 h-5 shrink-0" />
        <span>Riwayat Izin</span>
      </div>
    </a>
  </div>
@endif
