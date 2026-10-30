@php
  use Illuminate\Support\Carbon;
  $showUserDetail = !$month || $week || $date; // is week or day filter
  $isPerDayFilter = isset($date);
@endphp
<div>
  @pushOnce('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
      integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
  @endpushOnce
  <h3 class="col-span-2 mb-4 text-lg font-semibold leading-tight text-gray-800 dark:text-gray-200">
    Data Absensi
  </h3>
  <div class="mb-1 text-sm dark:text-white">Filter:</div>
  <div class="mb-4 grid grid-cols-2 flex-wrap items-center gap-5 md:gap-8 lg:flex">
    <div class="col-span-2 inline-flex rounded-xl border border-[#E6EDF5] bg-white p-1 dark:border-gray-700 dark:bg-gray-800">
      @foreach (['day' => 'Harian', 'week' => 'Mingguan', 'month' => 'Bulanan'] as $value => $label)
        <button type="button" wire:click="setMode('{{ $value }}')"
          class="rounded-lg px-4 py-1.5 text-xs font-semibold transition {{ $mode === $value ? 'bg-primary text-white' : 'text-[#64748B] hover:bg-[#F3F7FC] dark:text-gray-400 dark:hover:bg-gray-700' }}">
          {{ $label }}
        </button>
      @endforeach
    </div>
    <div class="flex flex-col gap-3 lg:flex-row lg:items-center">
      @if ($mode === 'day')
        <x-label for="day_filter" value="Tanggal"></x-label>
        <x-input type="date" name="day_filter" id="day_filter" wire:model.live="date" />
      @elseif ($mode === 'week')
        <x-label for="week_filter" value="Minggu"></x-label>
        <x-input type="week" name="week_filter" id="week_filter" wire:model.live="week" />
      @else
        <x-label for="month_filter" value="Bulan"></x-label>
        <x-input type="month" name="month_filter" id="month_filter" wire:model.live="month" />
      @endif
    </div>
    <x-select id="branch" wire:model.live="branch">
      <option value="">Semua Cabang</option>
      @foreach (App\Models\Branch::orderBy('name')->get() as $_branch)
        <option value="{{ $_branch->id }}" {{ $_branch->id == $branch ? 'selected' : '' }}>
          {{ $_branch->name }}
        </option>
      @endforeach
    </x-select>
    <x-select id="division" wire:model.live="division">
      <option value="">Semua Divisi</option>
      @foreach (App\Models\Division::all() as $_division)
        <option value="{{ $_division->id }}" {{ $_division->id == $division ? 'selected' : '' }}>
          {{ $_division->name }}
        </option>
      @endforeach
    </x-select>
    <x-select id="jobTitle" wire:model.live="jobTitle">
      <option value="">Semua Jabatan</option>
      @foreach (App\Models\JobTitle::all() as $_jobTitle)
        <option value="{{ $_jobTitle->id }}" {{ $_jobTitle->id == $jobTitle ? 'selected' : '' }}>
          {{ $_jobTitle->name }}
        </option>
      @endforeach
    </x-select>
    <div class="col-span-2 flex items-center gap-2 lg:w-96">
      <x-input type="text" class="w-full" name="search" id="search" wire:model.live.debounce.400ms="search"
        placeholder="{{ __('Search') }}" aria-label="Cari karyawan" />
      @if ($search)
        <x-secondary-button type="button" wire:click="$set('search', '')" wire:loading.attr="disabled">
          {{ __('Reset') }}
        </x-secondary-button>
      @endif
    </div>
    <div class="lg:hidden"></div>
    <x-secondary-button
      href="{{ route('admin.attendances.report', ['month' => $month, 'week' => $week, 'date' => $date, 'division' => $division, 'jobTitle' => $jobTitle, 'branch' => $branch]) }}"
      class="flex justify-center gap-2">
      Cetak Laporan
      <x-heroicon-o-printer class="h-5 w-5" />
    </x-secondary-button>
  </div>
  <div class="mb-2 flex flex-wrap items-center gap-2 text-[11px] font-semibold">
    <span class="rounded-full bg-green-100 px-2.5 py-1 text-green-800 dark:bg-green-900/40 dark:text-green-300">H Hadir</span>
    <span class="rounded-full bg-amber-100 px-2.5 py-1 text-amber-800 dark:bg-amber-900/40 dark:text-amber-300">T Terlambat</span>
    <span class="rounded-full bg-blue-100 px-2.5 py-1 text-blue-800 dark:bg-blue-900/40 dark:text-blue-300">I Izin</span>
    <span class="rounded-full bg-gray-100 px-2.5 py-1 text-gray-700 dark:bg-gray-700 dark:text-gray-300">S Sakit</span>
    <span class="rounded-full bg-orange-100 px-2.5 py-1 text-orange-800 dark:bg-orange-900/40 dark:text-orange-300">TT Tidak Tuntas</span>
    <span class="rounded-full bg-red-100 px-2.5 py-1 text-red-800 dark:bg-red-900/40 dark:text-red-300">A Alpa</span>
  </div>
  <div class="max-h-[70vh] overflow-auto rounded-xl border border-[#E6EDF5] dark:border-gray-800">
    <table class="w-full divide-y divide-gray-200 dark:divide-gray-700">
      <thead class="sticky top-0 z-20 bg-gray-50 dark:bg-gray-900">
        <tr>
          <th scope="col" class="sticky left-0 z-30 bg-gray-50 px-4 py-3 text-left text-xs font-medium text-gray-500 dark:bg-gray-900 dark:text-gray-300">
            {{ $showUserDetail ? __('Name') : __('Name') . '/' . __('Date') }}
          </th>
          @if ($showUserDetail)
            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300">
              {{ __('NIP') }}
            </th>
            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300">
              {{ __('Division') }}
            </th>
            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300">
              {{ __('Job Title') }}
            </th>
            @if ($isPerDayFilter)
              <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300">
                {{ __('Shift') }}
              </th>
            @endif
          @endif
          @foreach ($dates as $date)
            @php
              if (!$isPerDayFilter && $date->isSunday()) {
                  // Minggu merah
                  $textClass = 'text-red-500 dark:text-red-300';
              } elseif (!$isPerDayFilter && $date->isFriday()) {
                  // Jumat hijau
                  $textClass = 'text-green-500 dark:text-green-300';
              } else {
                  $textClass = 'text-gray-500 dark:text-gray-300';
              }
            @endphp
            <th scope="col"
              class="{{ $textClass }} text-nowrap border border-gray-300 px-1 py-3 text-center text-xs font-medium dark:border-gray-600">
              @if ($isPerDayFilter)
                Status
              @else
                {{ $date->format('d/m') }}
              @endif
            </th>
          @endforeach
          @if ($isPerDayFilter)
            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300">
              {{ __('Time In') }}
            </th>
            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300">
              {{ __('Time Out') }}
            </th>
          @endif
          @if (!$isPerDayFilter)
            @foreach (['H', 'T', 'I', 'S', 'TT', 'A'] as $_st)
              <th scope="col"
                class="text-nowrap border border-gray-300 px-1 py-3 text-center text-xs font-medium text-gray-500 dark:border-gray-600 dark:text-gray-300">
                {{ $_st }}
              </th>
            @endforeach
          @endif
          @if ($isPerDayFilter)
            <th scope="col" class="relative">
              <span class="sr-only">Actions</span>
            </th>
          @endif
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-200 bg-white dark:divide-gray-700 dark:bg-gray-800">
        @php
          $class = 'cursor-pointer px-4 py-3 text-sm font-medium text-gray-900 dark:text-white';
        @endphp
        @foreach ($employees as $employee)
          @php
            $attendances = $employee->attendances;
          @endphp
          <tr wire:key="{{ $employee->id }}" class="group">
            {{-- Detail karyawan --}}
            <td class="{{ $class }} text-nowrap group-hover:bg-gray-100 dark:group-hover:bg-gray-700">
              {{ $employee->name }}
            </td>
            @if ($showUserDetail)
              <td class="{{ $class }} group-hover:bg-gray-100 dark:group-hover:bg-gray-700">
                {{ $employee->nip }}
              </td>
              <td class="{{ $class }} text-nowrap group-hover:bg-gray-100 dark:group-hover:bg-gray-700">
                {{ $employee->division?->name ?? '-' }}
              </td>
              <td class="{{ $class }} text-nowrap group-hover:bg-gray-100 dark:group-hover:bg-gray-700">
                {{ $employee->jobTitle?->name ?? '-' }}
              </td>
              @if ($isPerDayFilter)
                @php
                  $attendance = $employee->attendances->isEmpty() ? null : $employee->attendances->first();
                  $timeIn = $attendance ? $attendance['time_in'] : null;
                  $timeOut = $attendance ? $attendance['time_out'] : null;
                @endphp
            <td class="{{ $class }} sticky left-0 z-10 bg-white text-nowrap group-hover:bg-gray-100 dark:bg-gray-800 dark:group-hover:bg-gray-700">

                  {{ $attendance['shift'] ?? '-' }}
                </td>
              @endif
            @endif

            {{-- Absensi --}}
            @php
              $presentCount = 0;
              $lateCount = 0;
              $excusedCount = 0;
              $sickCount = 0;
              $absentCount = 0;
              $incompleteCount = 0;
            @endphp
            @foreach ($dates as $date)
              @php
                $isWeekend = $date->isWeekend() || in_array($date->format('Y-m-d'), $holidayDates ?? [], true);
                $attendance = $attendances->firstWhere(fn($v, $k) => $v['date'] === $date->format('Y-m-d'));
                $status = ($attendance ?? [
                    'status' => $isWeekend || !$date->isPast() ? '-' : 'absent',
                ])['status'];
                switch ($status) {
                    case 'present':
                        $shortStatus = 'H';
                        $bgColor =
                            'bg-green-200 dark:bg-green-800 hover:bg-green-300 dark:hover:bg-green-700 border border-green-300 dark:border-green-600';
                        $presentCount++;
                        break;
                    case 'late':
                        $shortStatus = 'T';
                        $bgColor =
                            'bg-amber-200 dark:bg-amber-800 hover:bg-amber-300 dark:hover:bg-amber-700 border border-amber-300 dark:border-amber-600';
                        $lateCount++;
                        break;
                    case 'excused':
                        $shortStatus = 'I';
                        $bgColor =
                            'bg-blue-200 dark:bg-blue-800 hover:bg-blue-300 dark:hover:bg-blue-700 border border-blue-300 dark:border-blue-600';
                        $excusedCount++;
                        break;
                    case 'sick':
                        $shortStatus = 'S';
                        $bgColor =
                            'hover:bg-gray-100 dark:hover:bg-gray-700 border border-gray-300 dark:border-gray-600';
                        $sickCount++;
                        break;
                    case 'incomplete':
                        $shortStatus = 'TT';
                        $bgColor =
                            'bg-orange-200 dark:bg-orange-800 hover:bg-orange-300 dark:hover:bg-orange-700 border border-orange-300 dark:border-orange-600';
                        $incompleteCount++;
                        break;
                    case 'absent':
                        $shortStatus = 'A';
                        $bgColor =
                            'bg-red-200 dark:bg-red-800 hover:bg-red-300 dark:hover:bg-red-700 border border-red-300 dark:border-red-600';
                        $absentCount++;
                        break;
                    default:
                        $shortStatus = '-';
                        $bgColor =
                            'hover:bg-gray-100 dark:hover:bg-gray-700 border border-gray-300 dark:border-gray-600';
                        break;
                }
              @endphp
              @if (!$isPerDayFilter && $attendance)
                <td
                  class="{{ $bgColor }} cursor-pointer text-center text-sm font-medium text-gray-900 dark:text-white">
                  <button class="w-full px-1 py-3" wire:click="show({{ $attendance['id'] }})"
                    @if ($attendance['coordinates'])
                      onclick="setLocation({{ $attendance['lat'] ?? 0 }}, {{ $attendance['lng'] ?? 0 }})"
                    @endif>
                    {{ $isPerDayFilter && $status != '-' ? __("status_{$status}") : $shortStatus }}
                  </button>
                </td>
              @else
                <td
                  class="{{ $bgColor }} text-nowrap cursor-pointer px-1 py-3 text-center text-sm font-medium text-gray-900 dark:text-white">
                  {{ $isPerDayFilter && $status != '-' ? __("status_{$status}") : $shortStatus }}
                </td>
              @endif
            @endforeach

            {{-- Waktu masuk/keluar --}}
            @if ($isPerDayFilter)
              <td class="{{ $class }} group-hover:bg-gray-100 dark:group-hover:bg-gray-700">
                {{ $timeIn ?? '-' }}
              </td>
              <td class="{{ $class }} group-hover:bg-gray-100 dark:group-hover:bg-gray-700">
                {{ $timeOut ?? '-' }}
              </td>
            @endif

            {{-- Total --}}
            @if (!$isPerDayFilter)
              @foreach ([$presentCount, $lateCount, $excusedCount, $sickCount, $incompleteCount, $absentCount] as $statusCount)
                <td
                  class="cursor-pointer border border-gray-300 px-1 py-3 text-center text-sm font-medium text-gray-900 group-hover:bg-gray-100 dark:border-gray-600 dark:text-white dark:group-hover:bg-gray-700">
                  {{ $statusCount }}
                </td>
              @endforeach
            @endif

            {{-- Action --}}
            @if ($isPerDayFilter)
              @php
                $attendance = $employee->attendances->isEmpty() ? null : $employee->attendances->first();
              @endphp
              <td
                class="cursor-pointer text-center text-sm font-medium text-gray-900 group-hover:bg-gray-100 dark:text-white dark:group-hover:bg-gray-700">
                <div class="flex items-center justify-center gap-3">
                  @if ($attendance)
                    <button type="button"
                      class="inline-flex items-center justify-center rounded-lg border border-[#E6EDF5] bg-white px-3.5 py-2 text-xs font-semibold text-primary transition hover:bg-[#F3F7FC] dark:border-gray-700 dark:bg-gray-800 dark:text-blue-300 dark:hover:bg-gray-700"
                      wire:click="show({{ $attendance['id'] }})"
                      @if ($attendance['coordinates'])
                        onclick="setLocation({{ $attendance['lat'] ?? 0 }}, {{ $attendance['lng'] ?? 0 }})"
                      @endif>
                      {{ __('Detail') }}
                    </button>
                  @else
                    -
                  @endif
                </div>
              </td>
            @endif
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>
  @if ($employees->isEmpty())
    <div class="my-2 text-center text-sm font-medium text-gray-900 dark:text-gray-100">
      Tidak ada data
    </div>
  @endif
  <div class="mt-3">
    {{ $employees->links() }}
  </div>

  <x-attendance-detail-modal :current-attendance="$currentAttendance" />
  @stack('attendance-detail-scripts')
</div>
