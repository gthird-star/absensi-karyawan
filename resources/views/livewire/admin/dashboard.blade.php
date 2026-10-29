@php
  $date = Carbon\Carbon::now();
  $totalAttendance = $presentCount + $lateCount;
  $attendanceRate = $employeesCount > 0 ? round(($totalAttendance / $employeesCount) * 100) : 0;
@endphp

<div class="space-y-6">
  <!-- Top Greeting & Header bar ala Finora -->
  <div class="flex flex-col md:flex-row justify-between items-start md:items-center bg-white dark:bg-[#1E293B] p-6 sm:p-7 rounded-2xl shadow-card border border-[#E6EDF5] dark:border-gray-800 transition">
    <div>
      <div class="flex items-center gap-2">
        <h1 class="text-xl sm:text-2xl font-bold text-[#14264A] dark:text-white">
          Selamat Datang, {{ Auth::user()->name }}! 👋
        </h1>
      </div>
      <p class="text-xs sm:text-sm text-[#64748B] dark:text-gray-400 mt-1">
        Ringkasan absensi dan operasional tim Soerabaja45 Group hari ini.
      </p>
    </div>

    <div class="mt-4 md:mt-0 flex items-center gap-3">
      <div class="inline-flex items-center gap-2 px-4 py-2 bg-[#F3F7FC] dark:bg-gray-800 border border-[#E6EDF5] dark:border-gray-700 text-primary dark:text-blue-400 rounded-xl text-xs sm:text-sm font-semibold shadow-xs">
        <x-heroicon-o-calendar class="w-4 h-4" />
        <span>{{ $date->translatedFormat('l, d F Y') }}</span>
      </div>

      <a href="{{ route('admin.attendances') }}" class="px-4 py-2 bg-primary hover:bg-primary-hover text-white rounded-xl text-xs sm:text-sm font-semibold transition shadow-xs flex items-center gap-1.5">
        <x-heroicon-o-clipboard-document-check class="w-4 h-4" />
        <span>Data Absensi</span>
      </a>
    </div>
  </div>

  <!-- Metric Cards ala Finora (4 Cards Row) -->
  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5">
    <!-- Card 1: Total Karyawan -->
    <div class="bg-white dark:bg-[#1E293B] p-6 rounded-2xl shadow-card border border-[#E6EDF5] dark:border-gray-800 flex flex-col justify-between hover:shadow-card-hover transition">
      <div class="flex justify-between items-start">
        <div class="p-3 bg-blue-50 dark:bg-blue-900/30 text-primary dark:text-blue-400 rounded-2xl">
          <x-heroicon-o-users class="w-6 h-6" />
        </div>
        <span class="inline-flex items-center text-xs font-bold text-emerald-600 bg-emerald-50 dark:bg-emerald-950/40 dark:text-emerald-400 px-2.5 py-1 rounded-full border border-emerald-200/50 dark:border-emerald-800/40">
          Aktif
        </span>
      </div>
      <div class="mt-5">
        <p class="text-xs font-semibold text-[#64748B] dark:text-gray-400 uppercase tracking-wider">Total Karyawan</p>
        <h3 class="text-3xl font-extrabold text-[#14264A] dark:text-white mt-1">{{ $employeesCount }}</h3>
        <p class="text-[11px] text-[#8A9AB0] dark:text-gray-400 mt-1">Terdaftar di sistem presensi</p>
      </div>
    </div>

    <!-- Card 2: Hadir Hari Ini -->
    <div class="bg-white dark:bg-[#1E293B] p-6 rounded-2xl shadow-card border border-[#E6EDF5] dark:border-gray-800 flex flex-col justify-between hover:shadow-card-hover transition">
      <div class="flex justify-between items-start">
        <div class="p-3 bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 rounded-2xl">
          <x-heroicon-o-check-badge class="w-6 h-6" />
        </div>
        <span class="inline-flex items-center text-xs font-bold text-emerald-600 bg-emerald-50 dark:bg-emerald-950/40 dark:text-emerald-400 px-2.5 py-1 rounded-full border border-emerald-200/50 dark:border-emerald-800/40">
          {{ $presentCount }} Tepat Waktu
        </span>
      </div>
      <div class="mt-5">
        <p class="text-xs font-semibold text-[#64748B] dark:text-gray-400 uppercase tracking-wider">Hadir & Terlambat</p>
        <h3 class="text-3xl font-extrabold text-[#14264A] dark:text-white mt-1">
          {{ $totalAttendance }}
        </h3>
        <p class="text-[11px] text-[#8A9AB0] dark:text-gray-400 mt-1">
          @if ($lateCount > 0)
            <span class="text-amber-600 font-semibold">{{ $lateCount }} terlambat</span> hari ini
          @else
            Semua hadir tepat waktu
          @endif
        </p>
      </div>
    </div>

    <!-- Card 3: Izin & Sakit -->
    <div class="bg-white dark:bg-[#1E293B] p-6 rounded-2xl shadow-card border border-[#E6EDF5] dark:border-gray-800 flex flex-col justify-between hover:shadow-card-hover transition">
      <div class="flex justify-between items-start">
        <div class="p-3 bg-amber-50 dark:bg-amber-950/40 text-amber-600 dark:text-amber-400 rounded-2xl">
          <x-heroicon-o-document-text class="w-6 h-6" />
        </div>
        <span class="inline-flex items-center text-xs font-bold text-purple-600 bg-purple-50 dark:bg-purple-950/40 dark:text-purple-400 px-2.5 py-1 rounded-full border border-purple-200/50 dark:border-purple-800/40">
          {{ $sickCount }} Sakit
        </span>
      </div>
      <div class="mt-5">
        <p class="text-xs font-semibold text-[#64748B] dark:text-gray-400 uppercase tracking-wider">Izin & Sakit</p>
        <h3 class="text-3xl font-extrabold text-[#14264A] dark:text-white mt-1">{{ $excusedCount + $sickCount }}</h3>
        <p class="text-[11px] text-[#8A9AB0] dark:text-gray-400 mt-1">
          {{ $excusedCount }} izin resmi disetujui
        </p>
      </div>
    </div>

    <!-- Card 4: Alpha / Belum Hadir -->
    <div class="bg-white dark:bg-[#1E293B] p-6 rounded-2xl shadow-card border border-[#E6EDF5] dark:border-gray-800 flex flex-col justify-between hover:shadow-card-hover transition">
      <div class="flex justify-between items-start">
        <div class="p-3 bg-rose-50 dark:bg-rose-950/40 text-rose-600 dark:text-rose-400 rounded-2xl">
          <x-heroicon-o-x-circle class="w-6 h-6" />
        </div>
        <span class="inline-flex items-center text-xs font-bold text-rose-600 bg-rose-50 dark:bg-rose-950/40 dark:text-rose-400 px-2.5 py-1 rounded-full border border-rose-200/50 dark:border-rose-800/40">
          Belum Hadir
        </span>
      </div>
      <div class="mt-5">
        <p class="text-xs font-semibold text-[#64748B] dark:text-gray-400 uppercase tracking-wider">Tanpa Keterangan</p>
        <h3 class="text-3xl font-extrabold text-[#14264A] dark:text-white mt-1">{{ max(0, $absentCount) }}</h3>
        <p class="text-[11px] text-[#8A9AB0] dark:text-gray-400 mt-1">Belum melakukan presensi</p>
      </div>
    </div>
  </div>

  <!-- Attendance Rate Progress Bar -->
  <div class="bg-white dark:bg-[#1E293B] p-5 rounded-2xl shadow-card border border-[#E6EDF5] dark:border-gray-800">
    <div class="flex justify-between items-center mb-2">
      <div class="flex items-center gap-2">
        <span class="text-xs font-bold uppercase tracking-wider text-[#14264A] dark:text-white">Tingkat Kehadiran Hari Ini</span>
        <span class="text-xs font-bold px-2 py-0.5 rounded-full {{ $attendanceRate >= 80 ? 'bg-emerald-50 text-emerald-600' : ($attendanceRate >= 50 ? 'bg-amber-50 text-amber-600' : 'bg-rose-50 text-rose-600') }}">
          {{ $attendanceRate }}%
        </span>
      </div>
      <span class="text-xs text-[#64748B] dark:text-gray-400">
        {{ $totalAttendance }} dari {{ $employeesCount }} karyawan hadir
      </span>
    </div>
    <div class="w-full bg-gray-100 dark:bg-gray-800 rounded-full h-3 overflow-hidden">
      <div class="bg-gradient-to-r from-primary to-emerald-500 h-3 rounded-full transition-all duration-500" style="width: {{ min(100, $attendanceRate) }}%"></div>
    </div>
  </div>

  <!-- Pending Leave Request Alert Banner -->
  @if($pendingLeaveCount > 0)
    <div class="bg-gradient-to-r from-amber-500 to-orange-500 text-white p-5 rounded-2xl shadow-card flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
      <div class="flex items-center gap-3.5">
        <div class="p-2.5 bg-white/20 rounded-xl shrink-0">
          <x-heroicon-o-bell class="w-6 h-6 text-white" />
        </div>
        <div>
          <h4 class="font-bold text-base sm:text-lg">Ada {{ $pendingLeaveCount }} Pengajuan Izin/Cuti Menunggu Konfirmasi</h4>
          <p class="text-xs text-amber-100 mt-0.5">Harap segera periksa dan konfirmasi pengajuan dari karyawan.</p>
        </div>
      </div>
      <a href="{{ route('admin.leave-requests') }}" class="px-5 py-2.5 bg-white hover:bg-amber-50 text-amber-800 font-bold rounded-xl text-xs sm:text-sm shadow-sm transition shrink-0">
        Tinjau Sekarang
      </a>
    </div>
  @endif

  <!-- Main Table Section ala Finora -->
  <div class="bg-white dark:bg-[#1E293B] rounded-2xl shadow-card border border-[#E6EDF5] dark:border-gray-800 overflow-hidden">
    <!-- Table Header -->
    <div class="p-6 border-b border-[#E6EDF5] dark:border-gray-800 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
      <div>
        <div class="flex items-center gap-2.5">
          <h3 class="text-base sm:text-lg font-bold text-[#14264A] dark:text-white">Daftar Kehadiran Karyawan</h3>
          <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-50 text-primary dark:bg-blue-900/40 dark:text-blue-300">
            Total {{ $employees->total() }} Data
          </span>
        </div>
        <p class="text-xs text-[#64748B] dark:text-gray-400 mt-0.5">Monitoring absensi real-time seluruh pegawai Soerabaja45 Group.</p>
      </div>

      <div class="flex items-center gap-2">
        <a href="{{ route('admin.attendances') }}" class="px-3.5 py-2 bg-gray-50 hover:bg-gray-100 dark:bg-gray-800 dark:hover:bg-gray-700/80 text-gray-700 dark:text-gray-300 rounded-xl text-xs font-semibold border border-[#E6EDF5] dark:border-gray-700 transition flex items-center gap-1.5">
          <x-heroicon-o-eye class="w-4 h-4" />
          <span>Lihat Semua</span>
        </a>
      </div>
    </div>

    <!-- Table Responsive Container -->
    <div class="overflow-x-auto">
      <table class="w-full text-left border-collapse">
        <thead>
          <tr class="bg-[#F8FAFD] dark:bg-gray-800/60 border-b border-[#E6EDF5] dark:border-gray-800 text-[11px] font-bold uppercase tracking-wider text-[#64748B] dark:text-gray-400">
            <th scope="col" class="px-6 py-4">Karyawan</th>
            <th scope="col" class="px-6 py-4">Divisi & Jabatan</th>
            <th scope="col" class="px-6 py-4">Shift</th>
            <th scope="col" class="px-6 py-4 text-center">Status</th>
            <th scope="col" class="px-6 py-4">Jam Masuk</th>
            <th scope="col" class="px-6 py-4">Jam Pulang</th>
            <th scope="col" class="px-6 py-4 text-center">Aksi</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-[#E6EDF5] dark:divide-gray-800 text-xs">
          @forelse ($employees as $employee)
            @php
              $attendance = $employee->attendance;
              $timeIn = $attendance ? $attendance?->time_in?->format('H:i:s') : null;
              $timeOut = $attendance ? $attendance?->time_out?->format('H:i:s') : null;
              $isWeekend = $date->isWeekend();
              $status = ($attendance ?? [
                  'status' => $isWeekend || !$date->isPast() ? '-' : 'absent',
              ])['status'];

              switch ($status) {
                  case 'present':
                      $badgeClass = 'bg-emerald-50 text-emerald-700 border border-emerald-200/60 dark:bg-emerald-950/40 dark:text-emerald-400 dark:border-emerald-800/40';
                      break;
                  case 'late':
                      $badgeClass = 'bg-amber-50 text-amber-700 border border-amber-200/60 dark:bg-amber-950/40 dark:text-amber-400 dark:border-amber-800/40';
                      break;
                  case 'excused':
                      $badgeClass = 'bg-blue-50 text-blue-700 border border-blue-200/60 dark:bg-blue-950/40 dark:text-blue-400 dark:border-blue-800/40';
                      break;
                  case 'sick':
                      $badgeClass = 'bg-purple-50 text-purple-700 border border-purple-200/60 dark:bg-purple-950/40 dark:text-purple-400 dark:border-purple-800/40';
                      break;
                  case 'absent':
                      $badgeClass = 'bg-rose-50 text-rose-700 border border-rose-200/60 dark:bg-rose-950/40 dark:text-rose-400 dark:border-rose-800/40';
                      break;
                  default:
                      $badgeClass = 'bg-gray-50 text-gray-600 border border-gray-200/60 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-700';
                      break;
              }
            @endphp
            <tr wire:key="{{ $employee->id }}" class="hover:bg-[#F8FAFD]/70 dark:hover:bg-gray-800/40 transition">
              <!-- Name & NIP with Initials Avatar -->
              <td class="px-6 py-4">
                <div class="flex items-center gap-3">
                  <div class="w-8 h-8 rounded-full bg-blue-50 dark:bg-blue-900/40 text-primary dark:text-blue-300 font-bold text-xs flex items-center justify-center shrink-0 border border-blue-100 dark:border-blue-800/40">
                    {{ strtoupper(substr($employee->name, 0, 2)) }}
                  </div>
                  <div>
                    <span class="font-bold text-[#14264A] dark:text-white block">{{ $employee->name }}</span>
                    <span class="text-[11px] text-gray-400 font-mono">{{ $employee->nip ?? '-' }}</span>
                  </div>
                </div>
              </td>

              <!-- Divisi & Jabatan -->
              <td class="px-6 py-4">
                <div>
                  <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300">
                    {{ $employee->division?->name ?? 'Tanpa Divisi' }}
                  </span>
                  <p class="text-[11px] text-[#64748B] dark:text-gray-400 mt-0.5">{{ $employee->jobTitle?->name ?? '-' }}</p>
                </div>
              </td>

              <!-- Shift -->
              <td class="px-6 py-4 text-[#64748B] dark:text-gray-400 font-medium">
                {{ $attendance->shift?->name ?? '-' }}
              </td>

              <!-- Status Badge ala Finora -->
              <td class="px-6 py-4 text-center">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold {{ $badgeClass }}">
                  {{ $status != '-' ? __("status_{$status}") : '-' }}
                </span>
              </td>

              <!-- Time In -->
              <td class="px-6 py-4 font-mono text-[#14264A] dark:text-gray-300">
                @if ($timeIn)
                  <span class="font-semibold">{{ $timeIn }}</span>
                @else
                  <span class="text-gray-300 dark:text-gray-600">-</span>
                @endif
              </td>

              <!-- Time Out -->
              <td class="px-6 py-4 font-mono text-[#14264A] dark:text-gray-300">
                @if ($timeOut)
                  <span class="font-semibold">{{ $timeOut }}</span>
                @else
                  <span class="text-gray-300 dark:text-gray-600">-</span>
                @endif
              </td>

              <!-- Actions -->
              <td class="px-6 py-4 text-center">
                @if ($attendance && ($attendance->attachment || $attendance->note || $attendance->lat_lng))
                  <button type="button" wire:click="show({{ $attendance->id }})"
                    onclick="setLocation({{ $attendance->latitude ?? 0 }}, {{ $attendance->longitude ?? 0 }})"
                    class="px-3 py-1.5 bg-blue-50 hover:bg-blue-100 text-primary dark:bg-blue-900/40 dark:text-blue-300 dark:hover:bg-blue-900/60 rounded-xl text-xs font-semibold transition cursor-pointer">
                    Detail
                  </button>
                @else
                  <span class="text-gray-300 dark:text-gray-600">-</span>
                @endif
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="7" class="px-6 py-12 text-center text-gray-400">
                <x-heroicon-o-users class="w-10 h-10 mx-auto text-gray-300 dark:text-gray-600 mb-2" />
                <p class="font-medium">Belum ada data karyawan.</p>
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <!-- Table Pagination -->
    <div class="p-6 border-t border-[#E6EDF5] dark:border-gray-800 bg-[#F8FAFD]/50 dark:bg-gray-800/30">
      {{ $employees->links() }}
    </div>
  </div>

  <x-attendance-detail-modal :current-attendance="$currentAttendance" />
  @stack('attendance-detail-scripts')
</div>
