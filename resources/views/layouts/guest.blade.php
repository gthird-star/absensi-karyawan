<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <title>{{ config('app.name', 'Soerabaja45 Group - Absensi') }}</title>

  <!-- Fonts: Inter -->
  <link rel="preconnect" href="https://fonts.bunny.net">
  <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

  <!-- Scripts -->
  @vite(['resources/css/app.css', 'resources/js/app.js'])

  <!-- Styles -->
  @livewireStyles
</head>

<body class="font-sans antialiased bg-[#F3F7FC] dark:bg-[#0B1120] text-[#14264A] dark:text-gray-100 min-h-screen">
  <div class="relative min-h-screen">
    <div class="absolute right-4 top-4 z-20">
      <x-theme-toggle x-data />
    </div>

    {{ $slot }}
  </div>

  @livewireScripts

  @stack('scripts')
</body>

</html>
