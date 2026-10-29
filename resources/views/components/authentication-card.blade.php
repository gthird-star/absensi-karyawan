<div class="flex min-h-screen flex-col items-center justify-center bg-[#F3F7FC] dark:bg-[#0B1120] px-4 sm:px-6 lg:px-8 py-12">
  <div class="mb-8 flex flex-col items-center">
    {{ $logo }}
    <p class="text-xs text-gray-500 dark:text-gray-400 mt-2 font-medium tracking-wide">Sistem Presensi & Manajemen Karyawan</p>
  </div>

  <div class="w-full max-w-md overflow-hidden bg-white dark:bg-[#1E293B] px-8 py-8 shadow-card border border-[#E6EDF5] dark:border-gray-800 rounded-2xl sm:rounded-3xl">
    {{ $slot }}
  </div>

  <div class="mt-8 text-center text-xs text-gray-400 dark:text-gray-500 font-medium">
    &copy; {{ date('Y') }} Soerabaja45 Group. All Rights Reserved.
  </div>
</div>
