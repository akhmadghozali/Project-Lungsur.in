<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-white">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'Lungsur.in - Platform Marketplace & Lelang Komunitas Mahasiswa Malang' }}</title>

    <!-- Vite Assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Swiper CSS -->
    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.css">

    <!-- CSS khusus setiap halaman -->
    @stack('styles')
</head>

<body class="flex flex-col min-h-full font-sans antialiased text-slate-900 bg-white selection:bg-blue-600 selection:text-white">

    <!-- Flash Message -->
    <x-flash-message />

    <!-- Navbar -->
    <x-navbar />

    <!-- Main Content -->
    <main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8">
        {{ $slot ?? '' }}
        @yield('content')
    </main>

    <!-- Footer -->
    <x-footer />

    <!-- Swiper JS -->
    <script src="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            if (document.querySelector('.mySwiper')) {
                new Swiper('.mySwiper', {
                    loop: true,
                    pagination: {
                        el: '.swiper-pagination',
                        clickable: true
                    },
                    navigation: {
                        nextEl: '.swiper-button-next',
                        prevEl: '.swiper-button-prev'
                    },
                    slidesPerView: 1,
                    spaceBetween: 10,
                });
            }
        });
    </script>

    <!-- JavaScript khusus setiap halaman -->
    @stack('scripts')

</body>
</html>
```