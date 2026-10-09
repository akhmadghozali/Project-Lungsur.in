<nav x-data="{ mobileMenuOpen: false, userDropdownOpen: false }" class="bg-white border-b border-slate-100 sticky top-0 z-40 shadow-xs">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-20 items-center gap-4">
            
            <!-- Logo Lungsur.in (Sesuai Desain Figma) -->
            <div class="flex items-center gap-8 shrink-0">
                <a href="{{ url('/') }}" class="flex items-center gap-2.5 group">
                    <!-- Icon Logo Biru Panah Sirkular + Toga Mahasiswa -->
                    <div class="w-10 h-10 rounded-2xl bg-blue-600 flex items-center justify-center shadow-md shadow-blue-500/20 relative overflow-hidden group-hover:bg-blue-700 transition">
                        <svg class="w-6 h-6 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <!-- Panah Memutar (Thrift/Circular Economy) -->
                            <path d="M21 12a9 9 0 0 0-9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/>
                            <path d="M3 3v5h5"/>
                            <path d="M3 12a9 9 0 0 0 9 9 9.75 9.75 0 0 0 6.74-2.74L21 16"/>
                            <path d="M21 21v-5h-5"/>
                            <!-- Topi Toga di Tengah -->
                            <path d="M12 9l5 2.5-5 2.5-5-2.5 5-2.5z" fill="#FBBF24" stroke="#FBBF24" stroke-width="1.5"/>
                            <path d="M9.5 13v2.2c0 .8 1.1 1.5 2.5 1.5s2.5-.7 2.5-1.5V13" stroke="#FBBF24" stroke-width="1.5"/>
                        </svg>
                    </div>
                    <!-- Brand Text -->
                    <span class="text-2xl font-black text-slate-900 tracking-tight">
                        Lungsur<span class="text-blue-600">.in</span>
                    </span>
                </a>

                <!-- Desktop Navigation Links dengan Ikon Sesuai Figma -->
                <div class="hidden xl:flex items-center gap-1 font-medium text-sm text-slate-600">
                    <!-- Beranda (Active State) -->
                    <a href="{{ url('/') }}" 
                       class="flex items-center gap-2 px-3 py-2 rounded-xl text-blue-600 font-semibold bg-blue-50/60 transition">
                        <svg class="w-4 h-4 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"/>
                        </svg>
                        <span>Beranda</span>
                    </a>

                    <!-- Kategori -->
                    <a href="{{ Route::has('listings.index') ? route('listings.index') : url('/katalog') }}" 
                       class="flex items-center gap-2 px-3 py-2 rounded-xl hover:text-blue-600 hover:bg-slate-50 transition">
                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                        </svg>
                        <span>Kategori</span>
                    </a>

                    @auth
                        <!-- Iklan Saya -->
                        <a href="{{ Route::has('dashboard') ? route('dashboard') : url('/dashboard') }}" 
                           class="flex items-center gap-2 px-3 py-2 rounded-xl hover:text-blue-600 hover:bg-slate-50 transition">
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            <span>Iklan Saya</span>
                        </a>
                    @endauth

                    <!-- Lelang -->
                    <a href="{{ Route::has('auctions.index') ? route('auctions.index') : url('/lelang') }}" 
                       class="flex items-center gap-2 px-3 py-2 rounded-xl hover:text-blue-600 hover:bg-slate-50 transition">
                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                        <span>Lelang</span>
                    </a>

                    <!-- Kebutuhan (Wanted Ads) -->
                    <a href="{{ Route::has('wanted-ads.index') ? route('wanted-ads.index') : url('/wanted-ads') }}" 
                       class="flex items-center gap-2 px-3 py-2 rounded-xl hover:text-blue-600 hover:bg-slate-50 transition">
                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span>Kebutuhan</span>
                    </a>
                </div>
            </div>

            <!-- Bagian Kanan: Search Bar Mini + Notifikasi + Profil/Auth -->
            <div class="flex items-center gap-4">
                <!-- Search Box di Navbar -->
                <div class="hidden md:flex relative w-64 lg:w-72">
                    <input type="text" 
                           placeholder="Cari barang, jasa, atau kebutuhan..." 
                           class="w-full pl-9 pr-4 py-2 text-xs rounded-full border border-slate-200 bg-slate-50/70 text-slate-700 placeholder-slate-400 focus:bg-white focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-100 transition">
                    <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>

                @auth
                    <!-- Bell Notifikasi -->
                    <button type="button" class="relative p-2 text-slate-600 hover:text-blue-600 hover:bg-slate-50 rounded-full transition" aria-label="Notifikasi">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                        <span class="absolute top-1.5 right-1.5 w-2 h-2 rounded-full bg-rose-500 ring-2 ring-white"></span>
                    </button>

                    <!-- Avatar Profile Dropdown -->
                    <div class="relative" @click.outside="userDropdownOpen = false">
                        <button @click="userDropdownOpen = !userDropdownOpen" 
                                type="button" 
                                class="flex items-center gap-1.5 p-1 rounded-full hover:bg-slate-100 focus:outline-none transition">
                            <div class="w-9 h-9 rounded-full bg-slate-900 text-white font-bold flex items-center justify-center text-xs shadow-xs">
                                {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 2)) }}
                            </div>
                            <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>

                        <div x-show="userDropdownOpen" 
                             x-cloak
                             x-transition:enter="transition ease-out duration-150"
                             x-transition:enter-start="opacity-0 scale-95"
                             x-transition:enter-end="opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-100"
                             x-transition:leave-start="opacity-100 scale-100"
                             x-transition:leave-end="opacity-0 scale-95"
                             class="absolute right-0 mt-2 w-56 bg-white border border-slate-100 rounded-2xl shadow-xl py-2 text-sm z-50">
                            <div class="px-4 py-2.5 border-b border-slate-100">
                                <p class="font-bold text-slate-900 truncate">{{ auth()->user()->name }}</p>
                                <p class="text-xs text-slate-500 truncate">{{ auth()->user()->email }}</p>
                            </div>
                            <a href="{{ Route::has('dashboard') ? route('dashboard') : url('/dashboard') }}" class="block px-4 py-2 text-slate-700 hover:bg-slate-50 font-medium">Iklan & Lelang Saya</a>
                            <a href="{{ Route::has('profile.edit') ? route('profile.edit') : url('/profil') }}" class="block px-4 py-2 text-slate-700 hover:bg-slate-50 font-medium">Pengaturan Profil</a>
                            @if((auth()->user()->role ?? '') === 'admin')
                                <a href="{{ Route::has('admin.dashboard') ? route('admin.dashboard') : url('/admin/dashboard') }}" class="block px-4 py-2 text-blue-600 font-bold hover:bg-blue-50">Panel Admin</a>
                            @endif
                            <form method="POST" action="{{ Route::has('logout') ? route('logout') : url('/logout') }}">
                                @csrf
                                <button type="submit" class="w-full text-left px-4 py-2 text-rose-600 hover:bg-rose-50 font-medium">Keluar</button>
                            </form>
                        </div>
                    </div>
                @else
                    <!-- Tampilan untuk Guest (Belum Login) -->
                    <div class="flex items-center gap-2">
                        <a href="{{ Route::has('login') ? route('login') : url('/login') }}" 
                           class="text-sm font-bold text-slate-700 hover:text-blue-600 px-3.5 py-2 rounded-xl transition">
                            Masuk
                        </a>
                        <a href="{{ Route::has('register') ? route('register') : url('/register') }}" 
                           class="bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold px-4 py-2.5 rounded-full shadow-md shadow-blue-500/20 transition">
                            Daftar Akun
                        </a>
                    </div>
                @endauth

                <!-- Hamburger Button (Mobile) -->
                <button @click="mobileMenuOpen = !mobileMenuOpen" 
                        type="button" 
                        class="xl:hidden p-2 rounded-xl text-slate-700 hover:bg-slate-100" 
                        aria-label="Menu navigasi">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile Navigation Drawer -->
    <div x-show="mobileMenuOpen" x-cloak class="xl:hidden border-t border-slate-100 bg-white px-4 pt-3 pb-6 space-y-2">
        <a href="{{ url('/') }}" class="block py-2 text-sm font-semibold text-blue-600">Beranda</a>
        <a href="{{ Route::has('listings.index') ? route('listings.index') : url('/katalog') }}" class="block py-2 text-sm font-semibold text-slate-700">Kategori</a>
        @auth
            <a href="{{ Route::has('dashboard') ? route('dashboard') : url('/dashboard') }}" class="block py-2 text-sm font-semibold text-slate-700">Iklan Saya</a>
        @endauth
        <a href="{{ Route::has('auctions.index') ? route('auctions.index') : url('/lelang') }}" class="block py-2 text-sm font-semibold text-slate-700">Lelang Terbuka</a>
        <a href="{{ Route::has('wanted-ads.index') ? route('wanted-ads.index') : url('/wanted-ads') }}" class="block py-2 text-sm font-semibold text-slate-700">Kebutuhan Mahasiswa</a>
        <div class="pt-2 border-t border-slate-100">
            <a href="{{ Route::has('listings.create') ? route('listings.create') : url('/katalog/pasang') }}" class="block text-center py-2.5 text-sm font-bold bg-blue-600 text-white rounded-xl shadow-sm">
                + Pasang Iklan Sekarang
            </a>
        </div>
    </div>
</nav>
