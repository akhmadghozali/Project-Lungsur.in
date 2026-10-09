<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'Autentikasi Akun - Lungsur.in' }}</title>

    <!-- Vite Assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-full flex flex-col justify-center items-center py-12 px-4 sm:px-6 lg:px-8 font-sans antialiased text-slate-800 bg-slate-50 selection:bg-blue-600 selection:text-white">
    <!-- Reusable Flash Message -->
    <x-flash-message />

    <!-- Brand Header Sesuai Desain Figma -->
    <div class="w-full max-w-md text-center mb-8">
        <a href="{{ url('/') }}" class="inline-flex items-center gap-2.5 group">
            <div class="w-10 h-10 rounded-2xl bg-blue-600 flex items-center justify-center shadow-md shadow-blue-500/20 group-hover:bg-blue-700 transition">
                <svg class="w-6 h-6 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M21 12a9 9 0 0 0-9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/>
                    <path d="M3 3v5h5"/>
                    <path d="M3 12a9 9 0 0 0 9 9 9.75 9.75 0 0 0 6.74-2.74L21 16"/>
                    <path d="M21 21v-5h-5"/>
                    <path d="M12 9l5 2.5-5 2.5-5-2.5 5-2.5z" fill="#FBBF24" stroke="#FBBF24" stroke-width="1.5"/>
                    <path d="M9.5 13v2.2c0 .8 1.1 1.5 2.5 1.5s2.5-.7 2.5-1.5V13" stroke="#FBBF24" stroke-width="1.5"/>
                </svg>
            </div>
            <span class="font-black text-2xl text-slate-900 tracking-tight">Lungsur<span class="text-blue-600">.in</span></span>
        </a>
        <p class="mt-2 text-xs sm:text-sm text-slate-500 font-medium">
            Pasar Perlengkapan Kos & Lelang Komunitas Mahasiswa Malang
        </p>
    </div>

    <!-- Auth Form Card Container -->
    <div class="w-full max-w-md bg-white border border-slate-200/80 rounded-3xl shadow-sm p-6 sm:p-8">
        {{ $slot ?? '' }}
        @yield('content')
    </div>

    <!-- Footer Note -->
    <div class="mt-8 text-center text-xs text-slate-400 max-w-sm leading-relaxed">
        <p>&copy; {{ date('Y') }} Lungsur.in &bull; PBL Kelompok 5 SIB Polinema</p>
        <p class="mt-1 text-[11px] text-slate-400">Khusus Mahasiswa Perguruan Tinggi di Kota Malang</p>
    </div>
</body>
</html>
