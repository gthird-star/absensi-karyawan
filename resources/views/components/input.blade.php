@props(['disabled' => false])

<input {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge([
    'class' =>
        'border-[#E6EDF5] dark:border-gray-700 bg-white dark:bg-gray-800 text-[#14264A] dark:text-gray-100 placeholder-gray-400 focus:border-[#3988F5] focus:ring-2 focus:ring-[#3988F5]/20 rounded-xl shadow-sm text-sm transition dark:[color-scheme:dark]',
]) !!}>
