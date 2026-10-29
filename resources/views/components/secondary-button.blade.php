@php
  $class =
      'inline-flex items-center justify-center px-4 py-2.5 bg-white dark:bg-gray-800 border border-[#E6EDF5] dark:border-gray-700 rounded-xl font-medium text-xs text-[#64748B] dark:text-gray-300 shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700/60 hover:text-[#14264A] dark:hover:text-white focus:outline-none focus:ring-2 focus:ring-[#3988F5]/30 focus:ring-offset-2 dark:focus:ring-offset-gray-900 disabled:opacity-25 transition duration-150 ease-in-out cursor-pointer';
@endphp

@if (!isset($attributes['href']))
  <button {{ $attributes->merge(['type' => 'submit', 'class' => $class]) }}>
    {{ $slot }}
  </button>
@else
  <a {{ $attributes->merge(['class' => $class]) }}>
    {{ $slot }}
  </a>
@endif
