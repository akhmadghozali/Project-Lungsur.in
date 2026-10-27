<x-layouts.app>
    <x-slot:title>
        Lungsur.in - Platform Marketplace & Lelang Komunitas Mahasiswa Malang
    </x-slot:title>

    <div class="space-y-12 sm:space-y-16">

        <!-- ========================================== -->
        <!-- 1. HERO SECTION (Sesuai Desain Figma)      -->
        <!-- ========================================== -->
        <section class="bg-gradient-to-br from-blue-50/90 via-sky-50/50 to-blue-50/70 rounded-3xl border border-blue-100/60 p-6 sm:p-10 lg:p-14 relative overflow-hidden shadow-xs">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center relative z-10">
                
                <!-- Teks Hero & Form Pencarian (Kiri) -->
                <div class="lg:col-span-7 space-y-5">
                    <!-- Badge Header -->
                    <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full text-xs font-semibold bg-blue-100/70 text-blue-700 border border-blue-200/50">
                        <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                        <span>Platform Marketplace Mahasiswa</span>
                    </div>

                    <!-- Heading Utama -->
                    <h1 class="text-3xl sm:text-5xl font-black text-slate-900 tracking-tight leading-[1.15]">
                        Jual, Beli, <span class="text-blue-600">Tukar</span>, dan Lelang dengan Mudah di Lungsur.in
                    </h1>

                    <!-- Deskripsi Singkat -->
                    <p class="text-sm sm:text-base text-slate-500 leading-relaxed max-w-xl">
                        Temukan barang, jasa, dan kebutuhan yang kamu cari atau tawarkan. Semua dalam satu platform untuk mahasiswa kampusmu!
                    </p>

                    <!-- Large Search Box (Sesuai Figma) -->
                    <form action="{{ Route::has('listings.index') ? route('listings.index') : url('/katalog') }}" method="GET" class="pt-2">
                        <div class="bg-white p-2 sm:p-2.5 rounded-2xl sm:rounded-full border border-slate-200 shadow-sm flex flex-col sm:flex-row items-center gap-2 max-w-xl">
                            <div class="flex items-center gap-3 w-full px-3 py-1">
                                <svg class="w-5 h-5 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                                <input type="text" 
                                       name="q" 
                                       placeholder="Cari barang, jasa, atau kebutuhan..." 
                                       class="w-full text-xs sm:text-sm text-slate-800 placeholder-slate-400 focus:outline-none bg-transparent">
                            </div>
                            <button type="submit" 
                                    class="w-full sm:w-auto shrink-0 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs sm:text-sm px-7 py-3 rounded-xl sm:rounded-full shadow-md shadow-blue-500/25 transition flex items-center justify-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                                <span>Cari</span>
                            </button>
                        </div>
                    </form>

                    <!-- 3 Badge Jaminan Kepercayaan -->
                    <div class="pt-2 flex flex-wrap items-center gap-5 sm:gap-6 text-xs font-semibold text-slate-700">
                        <div class="flex items-center gap-2">
                            <div class="w-5 h-5 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center text-xs">
                                ✓
                            </div>
                            <span>Aman & Terpercaya</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="w-5 h-5 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-xs font-bold">
                                🎓
                            </div>
                            <span>Hanya Mahasiswa</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="w-5 h-5 rounded-full bg-amber-100 text-amber-600 flex items-center justify-center text-xs">
                                ⚡
                            </div>
                            <span>Mudah Digunakan</span>
                        </div>
                    </div>
                </div>

                <!-- Visual Kolase Foto Mahasiswa (Kanan) -->
                <div class="lg:col-span-5 relative flex items-center justify-center mt-6 lg:mt-0">
                    <div class="relative w-full max-w-sm">
                        <!-- Foto Utama Mahasiswa Kampus -->
                        <div class="bg-white p-3 rounded-3xl shadow-xl border border-slate-100 relative z-10 overflow-hidden">
                            <div class="relative rounded-2xl overflow-hidden aspect-[4/5] bg-slate-100">
                                <img src="https://images.unsplash.com/photo-1523240795612-9a054b0db644?q=80&w=800&auto=format&fit=crop" 
                                     alt="Mahasiswa Kampus Lungsur.in" 
                                     class="w-full h-full object-cover">
                                
                                <!-- Floating Pill Tag: "Barang Bekas Jadi Berkah! ✨" -->
                                <div class="absolute top-4 left-4 bg-white/95 backdrop-blur-xs px-3.5 py-1.5 rounded-full shadow-md text-xs font-bold text-slate-800 border border-slate-100">
                                    Barang Bekas Jadi Berkah! ✨
                                </div>
                            </div>
                        </div>

                        <!-- Foto Miniatur Melayang 1 (Atas Kanan - Laptop Kos) -->
                        <div class="absolute -top-4 -right-4 w-28 h-24 bg-white p-1.5 rounded-2xl shadow-lg border border-slate-100 z-20 hidden sm:block transform rotate-6">
                            <img src="https://images.unsplash.com/photo-1498050108023-c5249f4df085?q=80&w=300&auto=format&fit=crop" 
                                 alt="Perlengkapan Kos" 
                                 class="w-full h-full object-cover rounded-xl">
                        </div>

                        <!-- Foto Miniatur Melayang 2 (Bawah Kiri - Sepatu) -->
                        <div class="absolute -bottom-4 -left-4 w-28 h-20 bg-white p-1.5 rounded-2xl shadow-lg border border-slate-100 z-20 hidden sm:block transform -rotate-6">
                            <img src="https://images.unsplash.com/photo-1542291026-7eec264c27ff?q=80&w=300&auto=format&fit=crop" 
                                 alt="Barang Mahasiswa" 
                                 class="w-full h-full object-cover rounded-xl">
                        </div>

                        <!-- Badge Ikon Biru Kanan Bawah (Kelinci/Ekonomi Cepat) -->
                        <div class="absolute -bottom-2 -right-2 w-12 h-12 bg-blue-600 rounded-2xl flex items-center justify-center text-white shadow-lg shadow-blue-500/30 z-30">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                            </svg>
                        </div>
                    </div>
                </div>

            </div>
        </section>


        {{-- Spotlight Lelang Unggulan --}}
        @if ($spotlight)
            @php
                $spotlightListing = $spotlight->listing;
                $spotlightRemaining = $spotlight->timeRemainingSeconds();
            @endphp

            <section class="space-y-5">
                <div class="flex items-center justify-between gap-3">
                    <div class="flex items-center gap-2.5">
                        <div class="flex h-8 w-8 items-center justify-center rounded-xl bg-blue-100 text-lg text-blue-600">
                            ✦
                        </div>

                        <div>
                            <h2 class="text-xl font-black tracking-tight text-slate-900">
                                Spotlight Lelang Unggulan
                            </h2>
                            <p class="mt-1 text-xs text-slate-500">
                                Barang yang sedang ramai diminati mahasiswa.
                            </p>
                        </div>
                    </div>

                    <a
                        href="{{ route('auctions.index') }}"
                        class="shrink-0 text-xs font-bold text-blue-600 transition hover:text-blue-700 sm:text-sm"
                    >
                        Lihat Semua →
                    </a>
                </div>

                <article class="grid overflow-hidden rounded-2xl border border-blue-100 bg-white shadow-sm lg:grid-cols-[0.9fr_1.1fr]">

                    {{-- Foto barang --}}
                    <a
                        href="{{ route('auctions.show', $spotlight->id) }}"
                        class="group relative block aspect-[4/3] overflow-hidden bg-slate-100 lg:aspect-auto lg:min-h-[320px]"
                    >
                        @if ($spotlightListing?->photo_url)
                            <img
                                src="{{ $spotlightListing->photo_url }}"
                                alt="{{ $spotlightListing->title }}"
                                loading="lazy"
                                class="h-full w-full object-cover transition duration-300 group-hover:scale-105"
                            >
                        @else
                            <div class="flex h-full min-h-56 items-center justify-center text-sm text-slate-400">
                                Belum ada foto
                            </div>
                        @endif

                        <span class="absolute left-3 top-3 rounded-lg bg-blue-600 px-3 py-1.5 text-xs font-bold text-white shadow">
                            ✦ SPOTLIGHT
                        </span>

                        <span class="absolute bottom-3 left-3 rounded-lg bg-white/95 px-3 py-1.5 text-xs font-bold text-slate-700">
                            {{ (int) ($spotlight->unique_bidders_count ?? 0) }} penawar berbeda
                        </span>
                    </a>

                    {{-- Informasi lelang --}}
                    <div class="flex flex-col justify-center p-5 sm:p-7">
                        <div class="mb-3 flex flex-wrap items-center gap-2">
                            <span class="rounded-md bg-blue-50 px-2 py-1 text-xs font-semibold text-blue-700">
                                {{ $spotlightListing?->category?->name ?? 'Perlengkapan Kos' }}
                            </span>

                            <span
                                data-home-auction-status
                                class="rounded-full bg-emerald-50 px-2 py-1 text-xs font-bold text-emerald-700"
                            >
                                ● Sedang Berlangsung
                            </span>
                        </div>

                        <h3 class="text-xl font-black leading-snug text-slate-900 sm:text-2xl">
                            {{ $spotlightListing?->title ?? 'Barang lelang' }}
                        </h3>

                        <p class="mt-2 line-clamp-3 text-sm leading-relaxed text-slate-500">
                            {{ $spotlightListing?->description ?? 'Lihat detail barang dan ajukan penawaran selama lelang berlangsung.' }}
                        </p>

                        <div class="my-5 grid grid-cols-2 gap-3 border-y border-slate-100 py-4">
                            <div>
                                <p class="text-xs font-medium text-slate-500">
                                    Harga tertinggi
                                </p>

                                <p class="mt-1 text-xl font-black text-blue-700">
                                    Rp {{ number_format($spotlight->currentPrice(), 0, ',', '.') }}
                                </p>

                                <p class="mt-1 text-xs text-slate-400">
                                    {{ $spotlight->bids()->count() }} penawaran
                                </p>
                            </div>

                            <div>
                                <p class="text-xs font-bold text-rose-600">
                                    ◷ Sisa waktu
                                </p>

                                <div
                                    data-home-auction-countdown
                                    data-remaining-seconds="{{ $spotlightRemaining }}"
                                    class="mt-1 text-lg font-black tabular-nums text-slate-900"
                                >
                                    <span data-home-countdown-text>
                                        Memuat waktu...
                                    </span>
                                </div>

                                <p class="mt-1 text-xs text-slate-400">
                                    Sebelum lelang berakhir
                                </p>
                            </div>
                        </div>

                        <a
                            href="{{ route('auctions.show', $spotlight->id) }}"
                            class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-blue-600 px-4 py-3 text-sm font-bold text-white transition hover:bg-blue-700"
                        >
                            Lihat & Ikut Lelang
                            <span aria-hidden="true">→</span>
                        </a>
                    </div>
                </article>
            </section>
        @endif


        <!-- ========================================== -->
        <!-- 2. KATEGORI BARANG (Sesuai Desain Figma)   -->
        <!-- ========================================== -->
        <section class="space-y-5">
            <div class="flex items-center justify-between">
                <!-- Header Kategori dengan Ikon -->
                <div class="flex items-center gap-2.5">
                    <div class="w-7 h-7 rounded-lg bg-blue-100 text-blue-600 flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                        </svg>
                    </div>
                    <h2 class="text-xl font-black text-slate-900 tracking-tight">Kategori Barang</h2>
                </div>

                <!-- Tautan Lihat Semua -->
                <a href="{{ Route::has('listings.index') ? route('listings.index') : url('/katalog') }}" 
                   class="text-xs sm:text-sm font-bold text-blue-600 hover:text-blue-700 flex items-center gap-1 transition">
                    <span>Lihat Semua Kategori</span>
                    <span>&rarr;</span>
                </a>
            </div>

            <!-- 8 Kartu Kategori Kotak Biru Muda Sesuai Gambar Figma -->
            <div class="grid grid-cols-4 sm:grid-cols-4 md:grid-cols-8 gap-3 sm:gap-4">
                @php
                    $categories = [
                        ['name' => 'Elektronik', 'icon' => 'M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z'],
                        ['name' => 'Meja & Kursi', 'icon' => 'M4 6h16M4 10h16M4 14h16M4 18h16'],
                        ['name' => 'Kasur & Lemari', 'icon' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3'],
                        ['name' => 'Alat Masak', 'icon' => 'M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
                        ['name' => 'Buku & Tulis', 'icon' => 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253'],
                        ['name' => 'Fashion', 'icon' => 'M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z'],
                        ['name' => 'Olahraga', 'icon' => 'M13 10V3L4 14h7v7l9-11h-7z'],
                        ['name' => 'Lainnya', 'icon' => 'M5 12h.01M12 12h.01M19 12h.01M6 12a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0z'],
                    ];
                @endphp

                @foreach ($categories as $cat)
                    <a href="{{ Route::has('listings.index') ? route('listings.index', ['category' => strtolower($cat['name'])]) : url('/katalog?category='.strtolower($cat['name'])) }}" 
                       class="group bg-blue-50/50 hover:bg-blue-100/70 border border-blue-100/40 rounded-2xl p-3.5 sm:p-4 flex flex-col items-center justify-center text-center transition">
                        <!-- Icon Circle -->
                        <div class="w-11 h-11 rounded-2xl bg-blue-100/80 group-hover:bg-blue-600 text-blue-600 group-hover:text-white flex items-center justify-center mb-2.5 transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $cat['icon'] }}"/>
                            </svg>
                        </div>
                        <span class="text-xs font-bold text-slate-800 group-hover:text-blue-700 transition leading-tight">
                            {{ $cat['name'] }}
                        </span>
                    </a>
                @endforeach
            </div>
        </section>


        <!-- ========================================== -->
        <!-- 3. BANNER AJAKAN PASANG IKLAN (Figma 5)    -->
        <!-- ========================================== -->
        <section class="bg-gradient-to-r from-blue-50 via-sky-50 to-blue-50/80 rounded-3xl border border-blue-100 p-6 sm:p-8 flex flex-col md:flex-row items-center justify-between gap-6 shadow-2xs">
            <div class="flex items-center gap-4 text-left">
                <!-- Megaphone Icon -->
                <div class="w-14 h-14 rounded-2xl bg-blue-600 text-white flex items-center justify-center shadow-md shadow-blue-500/20 shrink-0">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.684A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.316z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg sm:text-xl font-black text-slate-900 tracking-tight">Punya Barang yang Ingin Dijual?</h3>
                    <p class="text-xs sm:text-sm text-slate-500 mt-1">Pasang iklanmu sekarang dan jangkau lebih banyak mahasiswa!</p>
                </div>
            </div>

            <!-- Tombol Pasang Iklan -->
            <div class="shrink-0 w-full md:w-auto">
                <a href="{{ Route::has('listings.create') ? route('listings.create') : url('/katalog/pasang') }}" 
                   class="inline-flex w-full md:w-auto items-center justify-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold px-7 py-3.5 rounded-2xl shadow-md shadow-blue-500/25 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                    </svg>
                    <span>+ Pasang Iklan</span>
                </a>
            </div>
        </section>


        <!-- ========================================== -->
        <!-- 4. DAFTAR IKLAN TERBARU (Sesuai Figma 4)   -->
        <!-- ========================================== -->
        <section class="space-y-6">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <!-- Judul & Subtitle -->
                <div>
                    <div class="flex items-center gap-2">
                        <div class="w-5 h-5 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-xs font-bold">
                            i
                        </div>
                        <h2 class="text-xl font-black text-slate-900 tracking-tight">Daftar Iklan Terbaru</h2>
                    </div>
                    <p class="text-xs sm:text-sm text-slate-500 mt-1">Temukan barang dan kebutuhan menarik dari sesama mahasiswa.</p>
                </div>

                <!-- Kontrol Filter Dropdown & Grid Toggle Sesuai Figma -->
                <div class="flex items-center gap-1.5">
                    <!-- Custom Sort Dropdown -->
                    <div class="relative" id="sort-dropdown">

                        <!-- Tombol utama -->
                        <button
                            type="button"
                            id="sort-button"
                            class="appearance-none bg-white border border-slate-200 text-slate-700 text-xs font-bold rounded-xl pl-3.5 pr-8 py-2 focus:outline-none focus:border-blue-600 cursor-pointer shadow-sm relative"
                        >
                            <span id="sort-label">
                                {{ $sort === 'lowest' ? 'Harga Terendah' : ($sort === 'highest' ? 'Harga Tertinggi' : 'Terbaru') }}
                            </span>

                            <svg
                                class="w-3.5 h-3.5 text-slate-400 absolute right-3 top-2.5 pointer-events-none"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M19 9l-7 7-7-7"
                                />
                            </svg>
                        </button>

                        <!-- Menu dropdown -->
                        <div
                            id="sort-menu"
                            class="hidden absolute right-0 top-full mt-2 w-48 bg-white border border-slate-100 rounded-xl shadow-xl p-1.5 z-50"
                        >
                            @foreach ([
                                'latest' => 'Terbaru',
                                'lowest' => 'Harga Terendah',
                                'highest' => 'Harga Tertinggi'
                            ] as $value => $label)

                                <button
                                    type="button"
                                    data-sort="{{ $value }}"
                                    class="w-full flex items-center justify-between px-3 py-2.5 rounded-lg text-xs font-semibold transition
                                    {{ $sort === $value
                                        ? 'bg-blue-50 text-blue-600'
                                        : 'text-slate-600 hover:bg-slate-50 hover:text-blue-600' }}"
                                >
                                    {{ $label }}

                                    @if ($sort === $value)
                                        <svg
                                            class="w-4 h-4"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M5 13l4 4L19 7"
                                            />
                                        </svg>
                                    @endif
                                </button>

                            @endforeach
                        </div>
                    </div>

                    <!-- Layout Icon Button -->
                    <button type="button" class="p-2 rounded-xl bg-blue-600 text-white shadow-xs" title="Tampilan Grid">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M5 3a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2V5a2 2 0 00-2-2H5zM5 11a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2v-2a2 2 0 00-2-2H5zM11 5a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V5zM11 13a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                        </svg>
                    </button>
                </div>
            </div>

        <div id="listing-content">
            <!-- Kartu Produk (Listing Cards) dari database -->
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 sm:gap-6 mt-6">
                @forelse ($listings as $listing)
                    <div class="bg-white rounded-2xl border border-slate-100 shadow-2xs hover:shadow-md transition overflow-hidden flex flex-col group">
                        <!-- Foto Barang -->
                        <div class="aspect-[4/3] bg-slate-100 overflow-hidden relative">
                            @if ($listing->photo_url)
                                <img src="{{ $listing->photo_url }}"
                                     alt="{{ $listing->title }}"
                                     loading="lazy"
                                     class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                            @else
                                <div class="w-full h-full flex items-center justify-center text-[11px] font-medium text-slate-400">
                                    Belum ada foto
                                </div>
                            @endif
                        </div>

                        <!-- Card Body -->
                        <div class="p-3.5 sm:p-4 flex-1 flex flex-col justify-between space-y-3">
                            <div>
                                <!-- Tag Kategori & Waktu Unggah -->
                                <div class="flex items-center justify-between text-[11px] mb-2">
                                    <span class="font-semibold text-blue-600 bg-blue-50 px-2 py-0.5 rounded-md truncate max-w-[110px]">
                                        {{ $listing->category->name ?? 'Lainnya' }}
                                    </span>
                                    <span class="text-slate-400 shrink-0">
                                        {{ $listing->created_at->diffForHumans() }}
                                    </span>
                                </div>

                                <!-- Judul Barang -->
                                <h3 class="font-bold text-xs sm:text-sm text-slate-800 line-clamp-2 leading-snug group-hover:text-blue-600 transition">
                                    {{ $listing->title }}
                                </h3>

                                <!-- Harga Biru Tebal -->
                                <div class="mt-2 flex items-baseline gap-2">
                                    <span class="text-base sm:text-lg font-black text-blue-600 tracking-tight">
                                        Rp {{ number_format($listing->price, 0, ',', '.') }}
                                    </span>
                                </div>
                            </div>

                            <!-- Tombol Full-Width "Lihat Detail ->" Sesuai Figma -->
                            <a href="{{ Route::has('listings.show') ? route('listings.show', $listing->id) : url('/katalog/'.$listing->id) }}"
                               class="w-full text-center py-2.5 rounded-xl bg-blue-50 hover:bg-blue-600 text-blue-600 hover:text-white font-bold text-xs transition flex items-center justify-center gap-1.5 shadow-2xs">
                                <span>Lihat Detail</span>
                                <span>&rarr;</span>
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full rounded-2xl border border-dashed border-slate-300 bg-white px-6 py-14 text-center">
                        <p class="text-sm font-bold text-slate-800">Belum ada barang</p>
                        <p class="mt-1 text-xs text-slate-500">Jadilah yang pertama memasang iklan di Lungsur.in.</p>
                    </div>
                @endforelse
            </div>

            <div id="listing-results">
                @if ($listings instanceof \Illuminate\Pagination\LengthAwarePaginator)
                    @if ($listings->total() > 0)

                        <div class="mt-10 flex flex-col items-center gap-3">

                            {{-- Tombol Pagination --}}
                            <div class="flex items-center justify-center gap-2">

                                {{-- Tombol Sebelumnya --}}
                                @if ($listings->onFirstPage())
                                    <span class="w-10 h-10 flex items-center justify-center rounded-xl bg-slate-100 text-slate-300">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                stroke-width="2" d="M15 19l-7-7 7-7"/>
                                        </svg>
                                    </span>
                                @else
                                    <a href="{{ $listings->previousPageUrl() }}"
                                    class="js-pagination w-10 h-10 flex items-center justify-center rounded-xl bg-slate-100 text-slate-600 hover:bg-blue-100 hover:text-blue-600 transition">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                stroke-width="2" d="M15 19l-7-7 7-7"/>
                                        </svg>
                                    </a>
                                @endif

                                {{-- Nomor Halaman --}}
                                @foreach ($listings->getUrlRange(1, $listings->lastPage()) as $page => $url)
                                    <a href="{{ $url }}"
                                    class="js-pagination w-10 h-10 flex items-center justify-center rounded-xl text-sm font-semibold transition
                                    {{ $listings->currentPage() == $page
                                    ? 'bg-blue-600 text-white shadow-md shadow-blue-500/20'
                                    : 'bg-blue-50 text-blue-600 hover:bg-blue-100' }}">
                                        {{ $page }}
                                    </a>
                                @endforeach

                                {{-- Tombol Selanjutnya --}}
                                @if ($listings->hasMorePages())
                                    <a href="{{ $listings->nextPageUrl() }}"
                                    class="js-pagination w-10 h-10 flex items-center justify-center rounded-xl bg-slate-100 text-slate-600 hover:bg-blue-100 hover:text-blue-600 transition">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                stroke-width="2" d="M9 5l7 7-7 7"/>
                                        </svg>
                                    </a>
                                @else
                                    <span class="w-10 h-10 flex items-center justify-center rounded-xl bg-slate-100 text-slate-300">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                stroke-width="2" d="M9 5l7 7-7 7"/>
                                        </svg>
                                    </span>
                                @endif

                            </div>

                            {{-- Informasi Jumlah Iklan --}}
                            <p class="text-xs sm:text-sm text-slate-400">
                                Menampilkan
                                <span class="font-semibold text-slate-700">
                                    {{ $listings->firstItem() }}–{{ $listings->lastItem() }}
                                </span>
                                dari
                                <span class="font-semibold text-slate-700">
                                    {{ $listings->total() }}
                                </span>
                                iklan
                            </p>

                        </div>

                    @endif
                @endif
            </div>
        </div>
                    <!-- Notice Khusus Guest -->
            @guest
                <div class="mt-6 bg-blue-50/80 border border-blue-200 rounded-2xl p-4 flex items-center justify-between gap-4 text-xs">
                    <div class="flex items-center gap-2.5 text-blue-900 font-medium">
                        <span class="w-2 h-2 rounded-full bg-blue-500 animate-pulse"></span>
                        <span>Masuk dengan email kampus untuk melihat kontak penjual & menawar barang.</span>
                    </div>

                    <a href="{{ Route::has('login') ? route('login') : url('/login') }}"
                    class="font-bold text-blue-700 underline shrink-0 hover:text-blue-900">
                        Masuk Sekarang &rarr;
                    </a>
                </div>
            @endguest

        </section>

        <!-- ========================================== -->
        <!-- 5. BANNER PENUTUP CTA (Sesuai Figma 6)     -->
        <!-- ========================================== -->
        <section class="bg-gradient-to-br from-blue-50/80 via-indigo-50/40 to-blue-50/90 rounded-3xl border border-blue-100 p-8 sm:p-14 text-center space-y-4 shadow-xs">
            <!-- Tag Atas -->
            <div class="inline-flex items-center gap-1.5 text-blue-600 font-extrabold text-[11px] uppercase tracking-wider">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.684A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.316z"/>
                </svg>
                <span>Mulai Dalam 60 Detik</span>
            </div>

            <!-- Heading Besar -->
            <h2 class="text-2xl sm:text-4xl font-black text-slate-900 tracking-tight leading-tight max-w-2xl mx-auto">
                Barang Kost Menumpuk? Mau Lulus dan Mengosongkan Kamar?
            </h2>

            <!-- Subtitle -->
            <p class="text-xs sm:text-sm text-slate-500 max-w-xl mx-auto leading-relaxed">
                Ubah buku lama, pakaian, meja belajar, atau monitor lamamu jadi uang saku tambahan. Mahasiswa adik tingkat selalu siap menyambut barang lungsuranmu!
            </p>

            <!-- Tombol Utama Biru Besar -->
            <div class="pt-2">
                <a href="{{ Route::has('listings.create') ? route('listings.create') : url('/katalog/pasang') }}" 
                   class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white font-black text-sm px-8 py-3.5 rounded-2xl shadow-lg shadow-blue-500/30 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>Pasang Iklan Gratis Sekarang</span>
                </a>
            </div>
        </section>

    </div>
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const listingContent = document.getElementById('listing-content');
        const sortDropdown = document.getElementById('sort-dropdown');
        const sortButton = document.getElementById('sort-button');
        let sortMenu = document.getElementById('sort-menu');
        let sortLabel = document.getElementById('sort-label');
 
        // Countdown Spotlight Lelang Unggulan
        document.querySelectorAll('[data-home-auction-countdown]').forEach((timer) => {
            const initial = Math.max(
                0,
                Number(timer.dataset.remainingSeconds || 0)
            );

            const startedAt = performance.now();
            const output = timer.querySelector('[data-home-countdown-text]');
            const status = document.querySelector('[data-home-auction-status]');

            const renderCountdown = () => {
                const elapsed = Math.floor(
                    (performance.now() - startedAt) / 1000
                );

                const remaining = Math.max(0, initial - elapsed);

                const days = Math.floor(remaining / 86400);
                const hours = Math.floor((remaining % 86400) / 3600);
                const minutes = Math.floor((remaining % 3600) / 60);
                const seconds = remaining % 60;

                const pad = (value) => String(value).padStart(2, '0');

                if (remaining <= 0) {
                    output.textContent = 'Lelang Ditutup';

                    if (status) {
                        status.textContent = '● Lelang Ditutup';
                        status.classList.remove(
                            'bg-emerald-50',
                            'text-emerald-700'
                        );
                        status.classList.add(
                            'bg-slate-100',
                            'text-slate-600'
                        );
                    }

                    return false;
                }

                output.textContent = days > 0
                    ? `${days}h ${pad(hours)}:${pad(minutes)}:${pad(seconds)}`
                    : `${pad(hours)}:${pad(minutes)}:${pad(seconds)}`;

                return true;
            };

            if (renderCountdown()) {
                const interval = window.setInterval(() => {
                    if (!renderCountdown()) {
                        window.clearInterval(interval);
                    }
                }, 1000);
            }
        });

        // AJAX untuk mengambil daftar iklan
        function loadListings(url, updateHistory = true) {
            fetch(url, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Gagal memuat daftar iklan');
                }
                return response.text();
            })
            .then(html => {
                const parser = new DOMParser();
                const newPage = parser.parseFromString(html, 'text/html');

                // Ambil daftar iklan dan pagination terbaru
                const newListingContent = newPage.getElementById('listing-content');

                if (newListingContent && listingContent) {
                    listingContent.innerHTML = newListingContent.innerHTML;
                }

                // Perbarui isi dropdown berdasarkan pilihan terbaru
                const newSortMenu = newPage.getElementById('sort-menu');
                const newSortLabel = newPage.getElementById('sort-label');

                if (newSortMenu && sortMenu) {
                    sortMenu.innerHTML = newSortMenu.innerHTML;
                }

                if (newSortLabel && sortLabel) {
                    sortLabel.textContent = newSortLabel.textContent.trim();
                }

                // Simpan URL ke riwayat browser
                if (updateHistory) {
                    window.history.pushState({}, '', url);
                }
            })
            .catch(error => {
                console.error(error);
            });
        }

        // Klik tombol dropdown
        if (sortButton && sortMenu) {
            sortButton.addEventListener('click', function () {
                sortMenu.classList.toggle('hidden');
            });
        }

        // Klik pilihan pengurutan
        if (sortMenu) {
            sortMenu.addEventListener('click', function (event) {
                const option = event.target.closest('[data-sort]');

                if (!option) return;

                const selectedSort = option.dataset.sort;

                const url = new URL(window.location.href);

                url.searchParams.set('sort', selectedSort);
                url.searchParams.delete('page');

                sortMenu.classList.add('hidden');

                loadListings(url.toString());
            });
        }

        // Tutup dropdown jika klik di luar dropdown
        document.addEventListener('click', function (event) {
            if (
                sortDropdown &&
                !sortDropdown.contains(event.target) &&
                sortMenu
            ) {
                sortMenu.classList.add('hidden');
            }
        });

        // Pagination AJAX
        document.addEventListener('click', function (event) {
            const paginationLink = event.target.closest('.js-pagination');

            if (!paginationLink) return;

            event.preventDefault();

            loadListings(paginationLink.href);
        });

        // Tombol Back dan Forward browser
        window.addEventListener('popstate', function () {
            loadListings(window.location.href, false);
        });
    });
    </script>
</x-layouts.app>
