<div>
  @pushOnce('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
      integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
  @endpushOnce
  <h3 class="col-span-2 mb-4 text-lg font-semibold leading-tight text-gray-800 dark:text-gray-200">
    Data Absensi
  </h3>
  <div class="flex flex-col gap-3 lg:flex-row lg:items-center">
    <x-label for="month_filter" value="Bulan"></x-label>
    <x-input type="month" name="month_filter" id="month_filter" wire:model.live="month" />
  </div>
  <h4 class="mt-3 text-sm dark:text-gray-200">Tap tanggal untuk melihat detail absensi.</h4>
  <div class="mt-2 flex flex-wrap items-center gap-2 text-[11px] font-semibold">
    <span class="rounded-full bg-green-100 px-2.5 py-1 text-green-800 dark:bg-green-900/40 dark:text-green-300">H Hadir</span>
    <span class="rounded-full bg-amber-100 px-2.5 py-1 text-amber-800 dark:bg-amber-900/40 dark:text-amber-300">T Terlambat</span>
    <span class="rounded-full bg-blue-100 px-2.5 py-1 text-blue-800 dark:bg-blue-900/40 dark:text-blue-300">I Izin</span>
    <span class="rounded-full bg-gray-100 px-2.5 py-1 text-gray-700 dark:bg-gray-700 dark:text-gray-300">S Sakit</span>
    <span class="rounded-full bg-orange-100 px-2.5 py-1 text-orange-800 dark:bg-orange-900/40 dark:text-orange-300">TT Belum Lengkap</span>
    <span class="rounded-full bg-red-100 px-2.5 py-1 text-red-800 dark:bg-red-900/40 dark:text-red-300">A Alpa</span>
  </div>
  <div class="mt-4 flex w-full flex-col gap-3 lg:flex-row">
    <div class="grid w-96 grid-cols-7 overflow-x-scroll dark:text-white lg:w-[36rem]">
      @foreach (['M', 'S', 'S', 'R', 'K', 'J', 'S'] as $day)
        <div
          class="{{ $day === 'M' ? 'text-red-500' : '' }} {{ $day === 'J' ? 'text-green-600 dark:text-green-500' : '' }} flex h-10 items-center justify-center border border-gray-300 text-center dark:border-gray-600">
          {{ $day }}
        </div>
      @endforeach
      @if ($start->dayOfWeek !== 0)
        @foreach (range(1, $start->dayOfWeek) as $i)
          <div class="h-14 border border-gray-300 bg-gray-100 dark:border-gray-600 dark:bg-gray-700">
          </div>
        @endforeach
      @endif
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
          $isWeekend = $date->isWeekend();
          $attendance = $attendances->firstWhere(fn($v, $k) => $v['date'] === $date->format('Y-m-d'));
          $status = ($attendance ?? [
              'status' => $isWeekend || !$date->isPast() ? '-' : 'absent',
          ])['status'];

          switch ($status) {
              case 'present':
                  $shortStatus = 'H';
                  $bgColor =
                      'bg-green-200 dark:bg-green-800 hover:bg-green-300 dark:hover:bg-green-700 border border-green-600';
                  $presentCount++;
                  break;
              case 'late':
                  $shortStatus = 'T';
                  $bgColor =
                      'bg-amber-200 dark:bg-amber-800 hover:bg-amber-300 dark:hover:bg-amber-700 border border-amber-600';
                  $lateCount++;
                  break;
              case 'excused':
                  $shortStatus = 'I';
                  $bgColor =
                      'bg-blue-200 dark:bg-blue-800 hover:bg-blue-300 dark:hover:bg-blue-700 border border-blue-600';
                  $excusedCount++;
                  break;
              case 'sick':
                  $shortStatus = 'S';
                  $bgColor =
                      'bg-purple-200 dark:bg-purple-950 hover:bg-purple-100 dark:hover:bg-purple-700 border border-purple-600';
                  $sickCount++;
                  break;
              case 'incomplete':
                  $shortStatus = 'TT';
                  $bgColor =
                      'bg-orange-200 dark:bg-orange-800 hover:bg-orange-300 dark:hover:bg-orange-700 border border-orange-600';
                  $incompleteCount++;
                  break;
              case 'absent':
                  $shortStatus = 'A';
                  $bgColor =
                      'bg-red-200 dark:bg-red-950 text-red-500 dark:text-red-200 border border-red-300 dark:border-red-700';
                  $absentCount++;
                  break;
              default:
                  $shortStatus = '-';
                  $bgColor =
                      'bg-slate-200 text-slate-600 dark:text-slate-200 dark:bg-slate-800 border border-gray-400 dark:border-gray-700';
                  break;
          }
        @endphp
        @if ($attendance)
          <button class="{{ $bgColor }} h-14 w-full py-1 text-center" wire:click="show({{ $attendance['id'] }})"
            @if ($attendance['coordinates'])
              onclick="setLocation({{ $attendance['lat'] ?? 0 }}, {{ $attendance['lng'] ?? 0 }})"
            @endif>
            <span
              class="{{ $date->isSunday() ? 'text-red-500' : '' }} {{ $date->isFriday() ? 'text-green-600 dark:text-green-500' : '' }}">
              {{ $date->format('d') }}
            </span>
            <br>
            {{ $shortStatus }}
          </button>
        @else
          <div class="{{ $bgColor }} h-14 py-1 text-center">
            <span
              class="{{ $date->isSunday() ? 'text-red-500' : '' }} {{ $date->isFriday() ? 'text-green-600 dark:text-green-500' : '' }}">
              {{ $date->format('d') }}
            </span>
            <br>
            {{ $shortStatus }}
          </div>
        @endif
      @endforeach
      @if ($end->dayOfWeek !== 6)
        @foreach (range(5, $end->dayOfWeek) as $i)
          <div class="h-14 border border-gray-300 bg-gray-100 dark:border-gray-600 dark:bg-gray-700"></div>
        @endforeach
      @endif
    </div>
    <div class="grid h-fit w-full grid-cols-2 gap-3 md:grid-cols-4">
      <div
        class="flex items-center justify-between rounded-md bg-green-200 px-4 py-2 text-gray-800 dark:bg-green-900 dark:text-white dark:shadow-gray-700">
        <div>
          <h4 class="text-lg font-semibold md:text-xl">{{ __("status_present") . ': ' . ($presentCount + $lateCount) }}</h4>
          {{ __("status_late") . ': ' . $lateCount }}
        </div>
      </div>
      <div
        class="flex items-center justify-between rounded-md bg-blue-200 px-4 py-2 text-gray-800 dark:bg-blue-900 dark:text-white dark:shadow-gray-700">
        <div>
          <h4 class="text-lg font-semibold md:text-xl">{{ __("status_excused") . ': ' . $excusedCount }}</h4>
        </div>
      </div>
      <div
        class="flex items-center justify-between rounded-md bg-purple-200 px-4 py-2 text-gray-800 dark:bg-purple-900 dark:text-white dark:shadow-gray-700">
        <div>
          <h4 class="text-lg font-semibold md:text-xl">{{ __("status_sick") . ': ' . $sickCount }}</h4>
        </div>
      </div>
      <div
        class="flex items-center justify-between rounded-md bg-orange-200 px-4 py-2 text-gray-800 dark:bg-orange-900 dark:text-white dark:shadow-gray-700">
        <div>
          <h4 class="text-lg font-semibold md:text-xl">{{ __("status_incomplete") . ': ' . $incompleteCount }}</h4>
        </div>
      </div>
      <div
        class="flex items-center justify-between rounded-md bg-red-200 px-4 py-2 text-gray-800 dark:bg-red-900 dark:text-white dark:shadow-gray-700">
        <div>
          <h4 class="text-lg font-semibold md:text-xl">{{ __("status_absent") . ': ' . $absentCount }}</h4>
        </div>
      </div>
    </div>
  </div>

  <x-attendance-detail-modal :current-attendance="$currentAttendance" />
  @stack('attendance-detail-scripts')
</div>
