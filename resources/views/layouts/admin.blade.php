<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-100">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'Panel Moderasi Admin - Lungsur.in' }}</title>

    <!-- Vite Assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full font-sans antialiased text-slate-800 bg-slate-100" x-data="{ sidebarOpen: false }">
    <x-flash-message />

    <div class="h-full flex overflow-hidden">
        <!-- Sidebar Backdrop on Mobile -->
        <div x-show="sidebarOpen" 
             x-cloak 
             @click="sidebarOpen = false" 
             class="fixed inset-0 z-40 bg-slate-900/60 lg:hidden">
        </div>

        <!-- Sidebar Navigation -->
        <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'" 
               class="fixed lg:static inset-y-0 left-0 z-50 w-64 bg-slate-900 text-slate-300 flex flex-col shrink-0 transition-transform duration-200 ease-in-out border-r border-slate-800">
            <!-- Brand -->
            <div class="h-16 flex items-center justify-between px-6 border-b border-slate-800">
                <a href="{{ url('/') }}" class="font-extrabold text-white text-base tracking-tight flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-xl bg-blue-600 flex items-center justify-center shrink-0 shadow-sm">
                        <svg class="w-5 h-5 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 12a9 9 0 0 0-9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/>
                            <path d="M3 3v5h5"/>
                            <path d="M3 12a9 9 0 0 0 9 9 9.75 9.75 0 0 0 6.74-2.74L21 16"/>
                            <path d="M21 21v-5h-5"/>
                            <path d="M12 9l5 2.5-5 2.5-5-2.5 5-2.5z" fill="#FBBF24" stroke="#FBBF24" stroke-width="1.5"/>
                            <path d="M9.5 13v2.2c0 .8 1.1 1.5 2.5 1.5s2.5-.7 2.5-1.5V13" stroke="#FBBF24" stroke-width="1.5"/>
                        </svg>
                    </div>
                    <span>Lungsur<span class="text-blue-400">Admin</span></span>
                </a>
                <button type="button" @click="sidebarOpen = false" class="lg:hidden p-1 text-slate-400 hover:text-white" aria-label="Tutup sidebar">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <!-- Menu List -->
            <nav class="flex-1 px-4 py-4 space-y-1 text-xs font-semibold overflow-y-auto">
                <a href="{{ Route::has('admin.dashboard') ? route('admin.dashboard') : url('/admin/dashboard') }}" 
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-slate-800 text-slate-200 hover:text-white transition">
                    <svg class="w-5 h-5 text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                    <span>Dasbor Analitik Bisnis</span>
                </a>

                <div class="pt-5 pb-1 px-3 text-[10px] font-bold text-slate-500 uppercase tracking-widest">Antrean Verifikasi</div>

                <!-- Verifikasi KTM -->
                <a href="{{ Route::has('admin.ktm.index') ? route('admin.ktm.index') : url('/admin/verifikasi-ktm') }}" 
                   class="flex items-center justify-between px-3 py-2.5 rounded-xl hover:bg-slate-800 text-slate-300 hover:text-white transition">
                    <span class="flex items-center gap-3">
                        <svg class="w-5 h-5 text-sky-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"/>
                        </svg>
                        <span>Verifikasi Dokumen KTM</span>
                    </span>
                </a>

                <!-- Pembayaran Promosi Boost -->
                <a href="{{ Route::has('admin.payments.boost') ? route('admin.payments.boost') : url('/admin/pembayaran-boost') }}" 
                   class="flex items-center justify-between px-3 py-2.5 rounded-xl hover:bg-slate-800 text-slate-300 hover:text-white transition">
                    <span class="flex items-center gap-3">
                        <svg class="w-5 h-5 text-amber-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                        </svg>
                        <span>Pembayaran Boost (Rp5.000)</span>
                    </span>
                </a>

                <!-- Pembayaran Pembukaan Lelang -->
                <a href="{{ Route::has('admin.payments.auction') ? route('admin.payments.auction') : url('/admin/pembayaran-lelang') }}" 
                   class="flex items-center justify-between px-3 py-2.5 rounded-xl hover:bg-slate-800 text-slate-300 hover:text-white transition">
                    <span class="flex items-center gap-3">
                        <svg class="w-5 h-5 text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span>Pembayaran Buka Lelang</span>
                    </span>
                </a>

                <div class="pt-5 pb-1 px-3 text-[10px] font-bold text-slate-500 uppercase tracking-widest">Moderasi & Pengguna</div>

                <!-- Laporan Aduan -->
                <a href="{{ Route::has('admin.reports.index') ? route('admin.reports.index') : url('/admin/laporan') }}" 
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-slate-800 text-slate-300 hover:text-white transition">
                    <svg class="w-5 h-5 text-rose-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    <span>Laporan Pelanggaran</span>
                </a>

                <!-- Manajemen Akun Pengguna -->
                <a href="{{ Route::has('admin.users.index') ? route('admin.users.index') : url('/admin/pengguna') }}" 
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-slate-800 text-slate-300 hover:text-white transition">
                    <svg class="w-5 h-5 text-indigo-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                    <span>Manajemen Mahasiswa & Blokir</span>
                </a>
            </nav>

            <!-- Bottom Exit / Logout -->
            <div class="p-4 border-t border-slate-800">
                <a href="{{ url('/') }}" class="text-xs text-slate-400 hover:text-white flex items-center gap-2 mb-3 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    Kembali ke Web Publik
                </a>
                <form method="POST" action="{{ Route::has('logout') ? route('logout') : url('/logout') }}">
                    @csrf
                    <button type="submit" class="w-full text-left text-xs text-rose-400 hover:text-rose-300 font-bold transition">
                        Keluar Panel Admin
                    </button>
                </form>
            </div>
        </aside>

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col min-w-0 overflow-y-auto">
            <!-- Header Top Bar -->
            <header class="h-16 bg-white border-b border-slate-200 flex items-center justify-between px-4 sm:px-8 shrink-0">
                <div class="flex items-center gap-3">
                    <button type="button" @click="sidebarOpen = true" class="lg:hidden p-2 text-slate-600 hover:bg-slate-100 rounded-lg" aria-label="Buka menu admin">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </button>
                    <h1 class="text-base sm:text-lg font-bold text-slate-800 truncate">
                        {{ $header ?? 'Dasbor Manajemen Administrator' }}
                    </h1>
                </div>

                <div class="flex items-center gap-3">
                    <span class="hidden sm:inline-block px-3 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                        Admin PBL Kelompok 5
                    </span>
                    <div class="w-8 h-8 rounded-full bg-slate-900 text-white font-bold flex items-center justify-center text-xs">
                        AD
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="p-4 sm:p-8 flex-1">
                {{ $slot ?? '' }}
                @yield('content')
            </main>
        </div>
    </div>
</body>
</html>
