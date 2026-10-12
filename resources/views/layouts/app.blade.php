<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-white">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'Lungsur.in - Platform Marketplace & Lelang Komunitas Mahasiswa Malang' }}</title>

    <!-- Vite Assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="flex flex-col min-h-full font-sans antialiased text-slate-900 bg-white selection:bg-blue-600 selection:text-white">
    <!-- Reusable Flash Message Notification (Alpine.js) -->
    <x-flash-message />

    <!-- Top Navigation Bar Sesuai Desain Figma -->
    <x-navbar />

    <!-- Main Content Canvas -->
    <main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8">
        {{ $slot ?? '' }}
        @yield('content')
    </main>

    <!-- Global Footer -->
    <x-footer />
</body>
</html>
