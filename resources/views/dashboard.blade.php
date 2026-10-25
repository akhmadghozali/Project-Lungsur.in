<x-layouts.app>
    <x-slot:title>
        Dasbor Mahasiswa – Lungsur.in
    </x-slot:title>

    <div class="space-y-8 py-4 sm:py-6 max-w-7xl mx-auto">

        {{-- ===================================== --}}
        {{-- TOP BANNER & MODE SWITCHER            --}}
        {{-- ===================================== --}}
        <div class="bg-gradient-to-r {{ $mode === 'seller' ? 'from-emerald-700 via-teal-700 to-blue-800' : 'from-blue-600 via-indigo-700 to-blue-800' }} rounded-3xl p-6 sm:p-8 text-white shadow-xl shadow-blue-500/10 relative overflow-hidden transition-colors duration-300">
            <!-- Background Rings Decoration -->
            <div class="absolute -right-10 -bottom-10 w-64 h-64 rounded-full border border-white/10 pointer-events-none"></div>
            <div class="absolute -right-4 -bottom-4 w-48 h-48 rounded-full border border-white/15 pointer-events-none"></div>

            <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div class="space-y-2">
                    <div class="inline-flex items-center gap-2 bg-white/15 backdrop-blur-xs px-3.5 py-1 rounded-full text-xs font-semibold text-white">
                        <span class="w-2 h-2 rounded-full {{ $mode === 'seller' ? 'bg-emerald-300 animate-pulse' : 'bg-blue-300' }}"></span>
                        <span>{{ $mode === 'seller' ? 'Mode Penjual Aktif' : 'Mode Pembeli Aktif' }}</span>
                        <span class="text-white/60">&bull;</span>
                        <span>{{ $user->campus ?? 'Mahasiswa Malang' }}</span>
                    </div>

                    <h1 class="text-2xl sm:text-3xl font-black tracking-tight">
                        Halo, {{ $user->name }}! 👋
                    </h1>
                    <p class="text-xs sm:text-sm text-blue-100 max-w-xl leading-relaxed">
                        @if ($mode === 'seller')
                            Kelola dagangan perlengkapan kos, pantau tawaran masuk, dan pasang iklan barang baru untuk sesama mahasiswa Malang.
                        @else
                            Jelajahi perabot kos murah, ikuti lelang hemat, dan temukan kebutuhan kuliah terbaik dari mahasiswa sekitar.
                        @endif
                    </p>
                </div>

                {{-- Action Switcher Buttons --}}
                <div class="flex flex-wrap items-center gap-3 shrink-0">
                    {{-- Toggle Mode Penjual ⇄ Pembeli --}}
                    <form method="POST" action="{{ route('user.switch_mode') }}">
                        @csrf
                        <input type="hidden" name="mode" value="{{ $mode === 'seller' ? 'buyer' : 'seller' }}">
                        <button type="submit" 
                                class="px-5 py-3 rounded-2xl bg-white/15 hover:bg-white/25 text-white font-bold text-xs sm:text-sm transition border border-white/20 flex items-center gap-2 cursor-pointer shadow-xs">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                            </svg>
                            <span>Ganti ke {{ $mode === 'seller' ? 'Mode Pembeli' : 'Mode Penjual' }}</span>
                        </button>
                    </form>

                    @if ($mode === 'seller')
                        <a href="{{ route('katalog.pasang') }}" 
                           class="px-5 py-3 rounded-2xl bg-white text-emerald-800 hover:bg-emerald-50 font-black text-xs sm:text-sm shadow-md transition flex items-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                            </svg>
                            <span>Pasang Iklan Baru</span>
                        </a>
                    @else
                        <a href="{{ url('/') }}" 
                           class="px-5 py-3 rounded-2xl bg-white text-blue-600 hover:bg-blue-50 font-bold text-xs sm:text-sm shadow-md transition">
                            Jelajahi Beranda &rarr;
                        </a>
                    @endif
                </div>
            </div>
        </div>

        {{-- ===================================== --}}
        {{-- PROFIL & STATISTIK AKTIVITAS          --}}
        {{-- ===================================== --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- 1. Kartu Identitas Profil Mahasiswa (Sinkron Database) --}}
            <div class="lg:col-span-1 bg-white border border-slate-200/80 rounded-3xl p-6 shadow-sm space-y-5">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 rounded-2xl {{ $mode === 'seller' ? 'bg-emerald-600' : 'bg-blue-600' }} text-white font-black text-xl flex items-center justify-center shadow-md shadow-blue-500/20 shrink-0">
                        {{ strtoupper(substr($user->name, 0, 2)) }}
                    </div>
                    <div class="overflow-hidden">
                        <h3 class="font-bold text-slate-900 text-base truncate">{{ $user->name }}</h3>
                        <p class="text-xs text-slate-500 font-medium truncate">{{ $user->email }}</p>
                        <div class="flex items-center gap-2 mt-1">
                            <span class="text-[10px] font-extrabold uppercase px-2 py-0.5 rounded-md bg-emerald-100 text-emerald-800">
                                {{ $user->verification_status === 'active' ? 'Akun Aktif' : $user->verification_status }}
                            </span>
                            <span class="text-[10px] font-extrabold uppercase px-2 py-0.5 rounded-md {{ $mode === 'seller' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-blue-50 text-blue-700 border border-blue-200' }}">
                                {{ $mode === 'seller' ? 'Penjual' : 'Pembeli' }}
                            </span>
                        </div>
                    </div>
                </div>

                <div class="border-t border-slate-100 pt-4 space-y-3 text-xs">
                    <div class="flex justify-between py-1 border-b border-slate-50">
                        <span class="text-slate-500 font-medium">Asal Kampus</span>
                        <span class="font-bold text-slate-800 text-right">{{ $user->campus ?? '-' }}</span>
                    </div>
                    <div class="flex justify-between py-1 border-b border-slate-50">
                        <span class="text-slate-500 font-medium">NIM Mahasiswa</span>
                        <span class="font-bold text-slate-800">{{ $user->nim ?? 'Belum diatur' }}</span>
                    </div>
                    <div class="flex justify-between py-1 border-b border-slate-50">
                        <span class="text-slate-500 font-medium">Nomor WhatsApp</span>
                        <span class="font-bold text-slate-800">{{ $user->phone ? (str_starts_with($user->phone, '0') ? '+62 ' . substr($user->phone, 1) : $user->phone) : '-' }}</span>
                    </div>
                    <div class="flex justify-between py-1 border-b border-slate-50">
                        <span class="text-slate-500 font-medium">Jalur Validasi</span>
                        <span class="font-bold text-slate-800">
                            {{ $user->email_type === 'campus' ? 'Email Kampus Otomatis' : 'Verifikasi KTM Manual' }}
                        </span>
                    </div>
                    <div class="flex justify-between py-1">
                        <span class="text-slate-500 font-medium">Bergabung Sejak</span>
                        <span class="font-bold text-slate-800">{{ $user->created_at?->translatedFormat('d F Y') ?? '-' }}</span>
                    </div>
                </div>
            </div>

            {{-- 2. Statistik Ringkas Aktivitas (Dinamis dari Database) --}}
            <div class="lg:col-span-2 grid grid-cols-1 sm:grid-cols-3 gap-4">

                @if ($mode === 'seller')
                    {{-- Stat 1: Total Iklan --}}
                    <div class="bg-white border border-slate-200/80 rounded-3xl p-6 shadow-sm flex flex-col justify-between">
                        <div>
                            <div class="w-10 h-10 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center mb-3">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                </svg>
                            </div>
                            <div class="text-xs font-semibold text-slate-500">Total Iklan Saya</div>
                            <div class="text-2xl font-black text-slate-900 mt-1">{{ $myListingsCount }}</div>
                        </div>
                        <span class="text-[11px] {{ $myListingsCount > 0 ? 'text-emerald-600' : 'text-slate-400' }} font-semibold mt-4">
                            {{ $myListingsCount > 0 ? $myListingsCount . ' iklan terdaftar' : 'Belum ada iklan' }}
                        </span>
                    </div>

                    {{-- Stat 2: Iklan Aktif Dijual --}}
                    <div class="bg-white border border-slate-200/80 rounded-3xl p-6 shadow-sm flex flex-col justify-between">
                        <div>
                            <div class="w-10 h-10 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center mb-3">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div class="text-xs font-semibold text-slate-500">Iklan Aktif Dijual</div>
                            <div class="text-2xl font-black text-slate-900 mt-1">{{ $activeListingsCount }}</div>
                        </div>
                        <span class="text-[11px] text-blue-600 font-semibold mt-4">Tersedia di katalog</span>
                    </div>

                    {{-- Stat 3: Barang Terjual --}}
                    <div class="bg-white border border-slate-200/80 rounded-3xl p-6 shadow-sm flex flex-col justify-between">
                        <div>
                            <div class="w-10 h-10 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center mb-3">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div class="text-xs font-semibold text-slate-500">Barang Terjual</div>
                            <div class="text-2xl font-black text-slate-900 mt-1">{{ $soldListingsCount }}</div>
                        </div>
                        <span class="text-[11px] text-amber-600 font-semibold mt-4">Transaksi selesai</span>
                    </div>

                @else
                    {{-- Mode Pembeli Stats --}}
                    {{-- Stat 1: Lelang Diikuti --}}
                    <div class="bg-white border border-slate-200/80 rounded-3xl p-6 shadow-sm flex flex-col justify-between">
                        <div>
                            <div class="w-10 h-10 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center mb-3">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                </svg>
                            </div>
                            <div class="text-xs font-semibold text-slate-500">Lelang Diikuti</div>
                            <div class="text-2xl font-black text-slate-900 mt-1">{{ $myBidsCount }}</div>
                        </div>
                        <span class="text-[11px] {{ $myBidsCount > 0 ? 'text-amber-600' : 'text-slate-400' }} font-semibold mt-4">
                            {{ $myBidsCount > 0 ? $myBidsCount . ' sesi lelang' : 'Belum mengikuti lelang' }}
                        </span>
                    </div>

                    {{-- Stat 2: Pesan Chat Belum Dibaca --}}
                    <div class="bg-white border border-slate-200/80 rounded-3xl p-6 shadow-sm flex flex-col justify-between">
                        <div>
                            <div class="w-10 h-10 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center mb-3">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                </svg>
                            </div>
                            <div class="text-xs font-semibold text-slate-500">Pesan Chat Masuk</div>
                            <div class="text-2xl font-black text-slate-900 mt-1">{{ $unreadMessagesCount }}</div>
                        </div>
                        <span class="text-[11px] {{ $unreadMessagesCount > 0 ? 'text-rose-600 font-bold' : 'text-emerald-600' }} mt-4">
                            {{ $unreadMessagesCount > 0 ? $unreadMessagesCount . ' pesan baru belum dibaca' : 'Kotak masuk bersih' }}
                        </span>
                    </div>

                    {{-- Stat 3: Iklan Saya Terdaftar --}}
                    <div class="bg-white border border-slate-200/80 rounded-3xl p-6 shadow-sm flex flex-col justify-between">
                        <div>
                            <div class="w-10 h-10 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center mb-3">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                </svg>
                            </div>
                            <div class="text-xs font-semibold text-slate-500">Iklan Barang Milikmu</div>
                            <div class="text-2xl font-black text-slate-900 mt-1">{{ $myListingsCount }}</div>
                        </div>
                        <form method="POST" action="{{ route('user.switch_mode') }}" class="mt-4">
                            @csrf
                            <input type="hidden" name="mode" value="seller">
                            <button type="submit" class="text-[11px] text-blue-600 font-bold hover:underline cursor-pointer flex items-center gap-1">
                                <span>Kelola di Mode Penjual</span>
                                <span>&rarr;</span>
                            </button>
                        </form>
                    </div>
                @endif

            </div>

        </div>

        {{-- ===================================== --}}
        {{-- KONTEN UTAMA: KELOLA IKLAN / LELANG   --}}
        {{-- ===================================== --}}
        @if ($mode === 'seller')
            {{-- Mode Penjual: Tabel / Grid Barang Dagangan Saya --}}
            <div class="bg-white border border-slate-200/80 rounded-3xl p-6 shadow-sm space-y-6">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-100 pb-4">
                    <div>
                        <h2 class="text-lg font-black text-slate-900 tracking-tight flex items-center gap-2">
                            <span>📦 Kelola Barang Dagangan Saya</span>
                            <span class="text-xs font-bold text-slate-400 bg-slate-100 px-2.5 py-0.5 rounded-full">{{ $myListingsCount }}</span>
                        </h2>
                        <p class="text-xs text-slate-500 mt-0.5">Daftar barang yang kamu iklankan di marketplace Lungsur.in</p>
                    </div>

                    <a href="{{ route('katalog.pasang') }}" 
                       class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-md shadow-blue-500/20 transition self-start sm:self-auto">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                        </svg>
                        <span>+ Pasang Iklan Baru</span>
                    </a>
                </div>

                @if ($myListings->count() > 0)
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                        @foreach ($myListings as $listing)
                            <div class="border border-slate-200 rounded-2xl overflow-hidden flex flex-col justify-between bg-white hover:border-slate-300 transition group">
                                <div>
                                    {{-- Foto Barang --}}
                                    <div class="aspect-[4/3] bg-slate-100 relative overflow-hidden">
                                        @if ($listing->photo_url)
                                            <img src="{{ $listing->photo_url }}" alt="{{ $listing->title }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center text-xs text-slate-400 font-medium">
                                                Belum ada foto
                                            </div>
                                        @endif

                                        {{-- Badge Status --}}
                                        <div class="absolute top-2.5 left-2.5">
                                            @if ($listing->status === 'available')
                                                <span class="bg-emerald-600 text-white text-[10px] font-black uppercase px-2 py-0.5 rounded-md shadow-xs">Tersedia</span>
                                            @elseif ($listing->status === 'sold')
                                                <span class="bg-slate-700 text-white text-[10px] font-black uppercase px-2 py-0.5 rounded-md shadow-xs">Terjual</span>
                                            @else
                                                <span class="bg-amber-600 text-white text-[10px] font-black uppercase px-2 py-0.5 rounded-md shadow-xs">{{ $listing->status }}</span>
                                            @endif
                                        </div>
                                    </div>

                                    {{-- Body --}}
                                    <div class="p-3.5 space-y-1.5">
                                        <div class="flex items-center justify-between text-[11px] text-slate-400">
                                            <span>{{ $listing->category->name ?? 'Lainnya' }}</span>
                                            <span>{{ $listing->created_at->diffForHumans() }}</span>
                                        </div>
                                        <h3 class="font-bold text-sm text-slate-900 line-clamp-1 group-hover:text-blue-600 transition">
                                            {{ $listing->title }}
                                        </h3>
                                        <p class="text-base font-black text-blue-600">
                                            Rp {{ number_format($listing->price, 0, ',', '.') }}
                                        </p>
                                    </div>
                                </div>

                                {{-- Aksi Penjual --}}
                                <div class="p-3 border-t border-slate-100 bg-slate-50/50 flex items-center justify-between gap-2">
                                    {{-- Toggle Status Terjual / Tersedia --}}
                                    <form method="POST" action="{{ route('dashboard.listings.status', $listing->id) }}" class="flex-1">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status" value="{{ $listing->status === 'available' ? 'sold' : 'available' }}">
                                        <button type="submit" 
                                                class="w-full py-1.5 px-2 rounded-lg text-xs font-bold transition text-center {{ $listing->status === 'available' ? 'bg-amber-50 hover:bg-amber-100 text-amber-700 border border-amber-200' : 'bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border border-emerald-200' }}">
                                            {{ $listing->status === 'available' ? 'Tandai Terjual' : 'Jadikan Tersedia' }}
                                        </button>
                                    </form>

                                    {{-- Tombol Hapus --}}
                                    <form method="POST" action="{{ route('dashboard.listings.destroy', $listing->id) }}" onsubmit="return confirm('Yakin ingin menghapus iklan barang ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="p-1.5 rounded-lg text-rose-600 hover:bg-rose-50 border border-rose-200 transition" 
                                                title="Hapus Iklan">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="pt-4 border-t border-slate-100">
                        {{ $myListings->links() }}
                    </div>
                @else
                    <div class="rounded-2xl border border-dashed border-slate-200 py-12 text-center space-y-3">
                        <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center mx-auto">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                        </div>
                        <div class="space-y-1">
                            <p class="text-sm font-bold text-slate-800">Belum ada barang yang kamu iklankan</p>
                            <p class="text-xs text-slate-500">Mulai jual perabot kos, buku, atau barang yang sudah tidak terpakai ke sesama mahasiswa.</p>
                        </div>
                        <a href="{{ route('katalog.pasang') }}" class="inline-flex items-center gap-1.5 px-6 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-md transition">
                            <span>Pasang Iklan Pertamamu</span>
                            <span>&rarr;</span>
                        </a>
                    </div>
                @endif
            </div>

        @else
            {{-- Mode Pembeli: Aktivitas Lelang & Belanja --}}
            <div class="bg-white border border-slate-200/80 rounded-3xl p-6 shadow-sm space-y-6">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-100 pb-4">
                    <div>
                        <h2 class="text-lg font-black text-slate-900 tracking-tight flex items-center gap-2">
                            <span>🏷️ Lelang yang Pernah Kamu Ikuti</span>
                            <span class="text-xs font-bold text-slate-400 bg-slate-100 px-2.5 py-0.5 rounded-full">{{ $myBids->count() }}</span>
                        </h2>
                        <p class="text-xs text-slate-500 mt-0.5">Pantau status penawaran harga pada lelang perabot kos mahasiswa</p>
                    </div>

                    <a href="{{ route('home') }}" 
                       class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition">
                        <span>Jelajahi Katalog Belanja &rarr;</span>
                    </a>
                </div>

                @if ($myBids->count() > 0)
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach ($myBids as $bid)
                            <div class="border border-slate-200 rounded-2xl p-4 flex flex-col justify-between bg-white hover:border-slate-300 transition">
                                <div class="space-y-2">
                                    <div class="flex items-center justify-between text-xs text-slate-400">
                                        <span>Status: <strong class="text-slate-700 uppercase">{{ $bid->auction->status ?? 'Aktif' }}</strong></span>
                                        <span>{{ $bid->created_at->diffForHumans() }}</span>
                                    </div>
                                    <h3 class="font-bold text-sm text-slate-900 line-clamp-1">
                                        {{ $bid->auction->listing->title ?? 'Sesi Lelang' }}
                                    </h3>
                                    <div class="p-2.5 rounded-xl bg-blue-50/60 border border-blue-100 space-y-1">
                                        <div class="flex justify-between text-xs">
                                            <span class="text-slate-500">Tawaran Terakhirmu:</span>
                                            <strong class="text-blue-700 font-black">Rp {{ number_format($bid->bid_amount, 0, ',', '.') }}</strong>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="rounded-2xl border border-dashed border-slate-200 py-12 text-center space-y-3">
                        <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center mx-auto">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                            </svg>
                        </div>
                        <div class="space-y-1">
                            <p class="text-sm font-bold text-slate-800">Kamu belum pernah mengajukan tawaran lelang</p>
                            <p class="text-xs text-slate-500">Ikuti lelang barang kos mahasiswa untuk mendapatkan harga terbaik dengan sistem bidding.</p>
                        </div>
                        <a href="{{ url('/') }}" class="inline-flex items-center gap-1.5 px-6 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-md transition">
                            <span>Lihat Barang di Beranda</span>
                            <span>&rarr;</span>
                        </a>
                    </div>
                @endif
            </div>
        @endif

    </div>
</x-layouts.app>
