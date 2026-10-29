@php
  $class =
      'inline-flex items-center justify-center px-5 py-2.5 bg-[#3988F5] hover:bg-[#2675E5] active:bg-[#1D4ED8] text-white rounded-xl font-semibold text-xs tracking-wide shadow-sm hover:shadow focus:outline-none focus:ring-2 focus:ring-[#3988F5]/30 focus:ring-offset-2 dark:focus:ring-offset-gray-900 disabled:opacity-50 transition duration-150 ease-in-out cursor-pointer';
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
