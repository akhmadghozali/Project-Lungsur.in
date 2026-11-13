@php
    $boostedCollection = $boostedListings ?? collect();
    $activeCategory = $filters['category'] ?? '';
    $activeKampus = $filters['kampus'] ?? '';
    $activeKondisi = $filters['kondisi'] ?? '';
    $minPrice = $filters['min'] ?? '';
    $maxPrice = $filters['max'] ?? '';

    // Total hitungan
    $regularTotal = $listings instanceof \Illuminate\Pagination\LengthAwarePaginator
        ? $listings->total()
        : $listings->count();
    $boostedTotal = $boostedCollection->count();
    $totalFound = $regularTotal + $boostedTotal;
    $allCount = $allListingsCount ?? $totalFound;

    // Kategori standar dengan ikon visual
    $sidebarCategories = [
        ['slug' => '', 'name' => 'Semua Iklan', 'count' => $allCount, 'icon' => 'M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z'],
        ['slug' => 'elektronik', 'name' => 'Elektronik', 'icon' => 'M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z'],
        ['slug' => 'meja', 'name' => 'Meja & Kursi', 'icon' => 'M4 6h16M4 10h16M4 14h16M4 18h16'],
        ['slug' => 'kasur', 'name' => 'Kasur & Lemari', 'icon' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3'],
        ['slug' => 'dapur', 'name' => 'Alat Masak', 'icon' => 'M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
        ['slug' => 'belajar', 'name' => 'Buku & Tulis', 'icon' => 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253'],
        ['slug' => 'pakaian', 'name' => 'Fashion', 'icon' => 'M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z'],
        ['slug' => 'olahraga', 'name' => 'Olahraga', 'icon' => 'M13 10V3L4 14h7v7l9-11h-7z'],
        ['slug' => 'lain', 'name' => 'Lainnya', 'icon' => 'M5 12h.01M12 12h.01M19 12h.01M6 12a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0z'],
    ];
@endphp

<div class="space-y-8 sm:space-y-12">

    <!-- ======================================================== -->
    <!-- 1. HERO BANNER TYPOGRAPHY OVERSIZED & OVERLAPPING SEARCH -->
    <!-- ======================================================== -->
    <section class="relative w-full">
        <!-- Hero Image Card -->
        <div class="relative w-full h-[300px] sm:h-[380px] md:h-[430px] rounded-3xl overflow-hidden shadow-lg bg-slate-900">
            <!-- Background Image -->
            <img src="{{ asset('images/katalog-hero.jpg') }}" 
                 alt="Suasana Kamar Kos Mahasiswa Malang" 
                 class="w-full h-full object-cover object-center filter brightness-[0.80] contrast-[1.05]">
            
            <!-- Dark Overlay -->
            <div class="absolute inset-0 bg-gradient-to-t from-slate-950/85 via-slate-950/40 to-slate-900/25"></div>

            <!-- Giant Display Typography "Katalog" -->
            <div class="absolute inset-x-0 bottom-4 sm:bottom-8 md:bottom-12 flex items-center justify-center pointer-events-none select-none">
                <h1 class="text-7xl sm:text-8xl md:text-9xl lg:text-[10.5rem] font-black text-white/95 tracking-tight drop-shadow-xl text-center">
                    Katalog
                </h1>
            </div>
        </div>

        <!-- Overlapping Curved White Search Transition Bar -->
        <div class="relative z-10 -mt-8 sm:-mt-12 md:-mt-14 mx-2 sm:mx-6 md:mx-8 bg-white rounded-t-3xl sm:rounded-t-[2.5rem] rounded-b-3xl border border-slate-200/80 shadow-md p-5 sm:p-7 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h2 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">Cari Kebutuhan Kosmu</h2>
                <p class="text-xs text-slate-500 mt-0.5 font-medium">Beli barang lungsuran kos, buku kuliah, dan elektronik langsung deal sesama mahasiswa Malang.</p>
            </div>

            <!-- Search Bar Kapsul -->
            <form action="{{ route('listings.index') }}" method="GET" class="w-full md:w-auto">
                @if (!empty($activeCategory))
                    <input type="hidden" name="category" value="{{ $activeCategory }}">
                @endif
                @if (!empty($activeKampus))
                    <input type="hidden" name="kampus" value="{{ $activeKampus }}">
                @endif
                @if (!empty($activeKondisi))
                    <input type="hidden" name="kondisi" value="{{ $activeKondisi }}">
                @endif
                @if (!empty($minPrice))
                    <input type="hidden" name="min" value="{{ $minPrice }}">
                @endif
                @if (!empty($maxPrice))
                    <input type="hidden" name="max" value="{{ $maxPrice }}">
                @endif
                <input type="hidden" name="sort" value="{{ $sort }}">

                <div class="bg-slate-50 border border-slate-200/90 rounded-full pl-4 pr-1.5 py-1.5 flex items-center justify-between gap-2 shadow-inner w-full md:w-[420px] focus-within:ring-2 focus-within:ring-blue-500/20 focus-within:border-blue-500 focus-within:bg-white transition">
                    <div class="flex items-center gap-2.5 w-full min-w-0">
                        <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <input type="text" 
                               name="q" 
                               value="{{ $search }}"
                               placeholder="Cari iklan barang kos, buku, elektronik..." 
                               class="w-full text-xs sm:text-sm text-slate-800 placeholder-slate-400 focus:outline-none bg-transparent">
                    </div>
                    <button type="submit" 
                            class="shrink-0 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs sm:text-sm px-5 py-2 rounded-full shadow-xs transition cursor-pointer">
                        Cari
                    </button>
                </div>
            </form>
        </div>
    </section>

    <!-- ======================================================== -->
    <!-- 2. SECTION IKLAN BOOST (ADAPTASI "EXPLORE RECOMMENDATIONS")-->
    <!-- ======================================================== -->
    @if ($boostedCollection->isNotEmpty())
    <section class="mt-4 pt-2"
             x-data="{
                 isPaused: false,
                 pauseTimer: null,
                 scrollLeft() {
                     const el = this.$refs.boostTrack;
                     if (el) {
                         this.pauseMarquee();
                         el.scrollBy({ left: -320, behavior: 'smooth' });
                     }
                 },
                 scrollRight() {
                     const el = this.$refs.boostTrack;
                     if (el) {
                         this.pauseMarquee();
                         el.scrollBy({ left: 320, behavior: 'smooth' });
                     }
                 },
                 pauseMarquee() {
                     this.isPaused = true;
                     clearTimeout(this.pauseTimer);
                     this.pauseTimer = setTimeout(() => { this.isPaused = false; }, 3500);
                 }
             }">
        
        <!-- Header Section Cek Iklan Pilihan -->
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center gap-2.5">
                <h2 class="text-xl font-black text-slate-900 tracking-tight">Cek Iklan Pilihan</h2>
            </div>

            <div class="flex items-center gap-3">
                <a href="{{ auth()->check() ? (auth()->user()->isSeller() ? route('dashboard', ['tab' => 'dagangan']) : route('dashboard')) : route('login') }}" 
                   class="text-xs font-bold text-blue-600 hover:text-blue-700 hover:underline transition">
                    + Boost Iklanmu
                </a>
                
                @if ($boostedCollection->count() > 3)
                    <div class="flex items-center gap-1.5">
                        <button type="button" 
                                @click="scrollLeft()" 
                                class="w-8 h-8 rounded-full border border-slate-200 hover:border-slate-300 hover:bg-slate-50 flex items-center justify-center text-slate-600 transition shadow-2xs cursor-pointer" 
                                aria-label="Iklan boost sebelumnya">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                        </button>
                        <button type="button" 
                                @click="scrollRight()" 
                                class="w-8 h-8 rounded-full border border-slate-200 hover:border-slate-300 hover:bg-slate-50 flex items-center justify-center text-slate-600 transition shadow-2xs cursor-pointer" 
                                aria-label="Iklan boost berikutnya">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </button>
                    </div>
                @endif
            </div>
        </div>

        @if ($boostedCollection->count() <= 3)
            <!-- Jika <= 3 item: Tampilkan Berjajar Statis Rapi -->
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
                @foreach ($boostedCollection as $listing)
                    @include('katalog.partials.card', ['listing' => $listing, 'isBoosted' => true])
                @endforeach
            </div>
        @else
            <!-- Jika > 3 item: Continuous Horizontal Running Marquee & Touch Swipe -->
            <div class="relative overflow-hidden w-full group-marquee group/marquee py-1">
                <!-- Subtle Gradient Fades -->
                <div class="pointer-events-none absolute left-0 top-0 bottom-0 w-8 sm:w-16 bg-gradient-to-r from-white via-white/80 to-transparent z-10"></div>
                <div class="pointer-events-none absolute right-0 top-0 bottom-0 w-8 sm:w-16 bg-gradient-to-l from-white via-white/80 to-transparent z-10"></div>

                <div class="overflow-x-auto no-scrollbar scroll-smooth py-1" x-ref="boostTrack">
                    <div class="flex w-max animate-marquee hover:[animation-play-state:paused]"
                         :style="isPaused ? 'animation-play-state: paused !important;' : ''"
                         @mouseenter="isPaused = true"
                         @mouseleave="isPaused = false"
                         @touchstart="pauseMarquee()">
                        <!-- Track 1: Original Items -->
                        <div class="flex gap-5 shrink-0 pr-5">
                            @foreach ($boostedCollection as $listing)
                                <div class="w-64 sm:w-72 shrink-0">
                                    @include('katalog.partials.card', ['listing' => $listing, 'isBoosted' => true])
                                </div>
                            @endforeach
                        </div>
                        <!-- Track 2: Duplicate Items for Seamless Loop -->
                        <div class="flex gap-5 shrink-0 pr-5" aria-hidden="true">
                            @foreach ($boostedCollection as $listing)
                                <div class="w-64 sm:w-72 shrink-0">
                                    @include('katalog.partials.card', ['listing' => $listing, 'isBoosted' => true])
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </section>
    @endif

    <!-- ======================================================== -->
    <!-- 3. LAYOUT UTAMA 2 KOLOM (ADAPTASI GAMBAR 3)               -->
    <!-- ======================================================== -->
    <section class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        
        <!-- ==================================================== -->
        <!-- KOLOM KIRI: SIDEBAR FILTER (±260px)                 -->
        <!-- ==================================================== -->
        <aside class="col-span-12 lg:col-span-3 space-y-6">
            <div class="bg-white rounded-3xl border border-slate-200/80 p-5 shadow-xs space-y-6 sticky top-24">
                
                <!-- 1. Header & Daftar Kategori Vertikal -->
                <div>
                    <h3 class="text-xs font-black text-slate-900 uppercase tracking-wider mb-3">
                        Kategori
                    </h3>

                    <div class="space-y-1">
                        @foreach ($sidebarCategories as $cat)
                            @php
                                $isSelected = ($cat['slug'] === '' && empty($activeCategory)) || 
                                              ($cat['slug'] !== '' && !empty($activeCategory) && str_contains(strtolower($activeCategory), strtolower($cat['slug'])));
                                $catUrl = request()->fullUrlWithQuery(['category' => $cat['slug'] ?: null, 'page' => null]);
                            @endphp
                            <a href="{{ $catUrl }}" 
                               class="flex items-center justify-between px-3 py-2 rounded-xl text-xs font-bold transition group {{ $isSelected ? 'bg-blue-50 text-blue-600 font-extrabold' : 'text-slate-600 hover:bg-slate-50 hover:text-blue-600' }}">
                                <div class="flex items-center gap-2.5 min-w-0">
                                    <svg class="w-4 h-4 {{ $isSelected ? 'text-blue-600' : 'text-slate-400 group-hover:text-blue-600' }} shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $cat['icon'] }}"/>
                                    </svg>
                                    <span class="truncate">{{ $cat['name'] }}</span>
                                </div>

                                @if (!empty($cat['count']))
                                    <span class="text-[10px] px-2 py-0.5 rounded-full font-bold ml-1 shrink-0 {{ $isSelected ? 'bg-blue-600 text-white' : 'bg-slate-100 text-slate-500 group-hover:bg-blue-100 group-hover:text-blue-600' }}">
                                        {{ $cat['count'] }}
                                    </span>
                                @endif
                            </a>
                        @endforeach
                    </div>
                </div>

                <!-- 2. Form Accordion Filter Tambahan -->
                <form action="{{ route('listings.index') }}" method="GET" class="border-t border-slate-100 pt-5 space-y-4"
                      x-data="{
                          campusOpen: true,
                          conditionOpen: true,
                          priceOpen: true
                      }">
                    
                    @if (!empty($search))
                        <input type="hidden" name="q" value="{{ $search }}">
                    @endif
                    @if (!empty($activeCategory))
                        <input type="hidden" name="category" value="{{ $activeCategory }}">
                    @endif
                    <input type="hidden" name="sort" value="{{ $sort }}">

                    <!-- Accordion 1: Asal Kampus -->
                    <div class="border-b border-slate-100 pb-4">
                        <button type="button" 
                                @click="campusOpen = !campusOpen" 
                                class="w-full flex items-center justify-between text-xs font-bold text-slate-900 mb-2 cursor-pointer">
                            <span>Asal Kampus</span>
                            <svg class="w-3.5 h-3.5 text-slate-400 transition-transform duration-200" :class="campusOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                        
                        <div x-show="campusOpen" x-transition class="space-y-1.5 pt-1">
                            @foreach ([
                                '' => 'Semua Kampus',
                                'Polinema' => 'Polinema',
                                'UB' => 'Universitas Brawijaya (UB)',
                                'UM' => 'Universitas Negeri Malang (UM)',
                                'UMM' => 'UMM (Muhammadiyah)',
                                'UIN' => 'UIN Maliki Malang'
                            ] as $val => $lbl)
                                <label class="flex items-center gap-2 text-xs text-slate-600 hover:text-slate-900 cursor-pointer select-none">
                                    <input type="radio" 
                                           name="kampus" 
                                           value="{{ $val }}" 
                                           {{ $activeKampus === $val ? 'checked' : '' }}
                                           @change="$el.form.submit()"
                                           class="text-blue-600 focus:ring-blue-500 border-slate-300">
                                    <span class="{{ $activeKampus === $val ? 'font-bold text-blue-600' : '' }}">{{ $lbl }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <!-- Accordion 2: Kondisi Barang -->
                    <div class="border-b border-slate-100 pb-4">
                        <button type="button" 
                                @click="conditionOpen = !conditionOpen" 
                                class="w-full flex items-center justify-between text-xs font-bold text-slate-900 mb-2 cursor-pointer">
                            <span>Kondisi Barang</span>
                            <svg class="w-3.5 h-3.5 text-slate-400 transition-transform duration-200" :class="conditionOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                        
                        <div x-show="conditionOpen" x-transition class="space-y-1.5 pt-1">
                            @foreach ([
                                '' => 'Semua Kondisi',
                                'baru' => 'Baru',
                                'bekas' => 'Bekas Baik',
                                'layak' => 'Layak Pakai'
                            ] as $val => $lbl)
                                <label class="flex items-center gap-2 text-xs text-slate-600 hover:text-slate-900 cursor-pointer select-none">
                                    <input type="radio" 
                                           name="kondisi" 
                                           value="{{ $val }}" 
                                           {{ $activeKondisi === $val ? 'checked' : '' }}
                                           @change="$el.form.submit()"
                                           class="text-blue-600 focus:ring-blue-500 border-slate-300">
                                    <span class="{{ $activeKondisi === $val ? 'font-bold text-blue-600' : '' }}">{{ $lbl }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <!-- Accordion 3: Rentang Harga -->
                    <div class="pb-1">
                        <button type="button" 
                                @click="priceOpen = !priceOpen" 
                                class="w-full flex items-center justify-between text-xs font-bold text-slate-900 mb-2 cursor-pointer">
                            <span>Rentang Harga</span>
                            <svg class="w-3.5 h-3.5 text-slate-400 transition-transform duration-200" :class="priceOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                        
                        <div x-show="priceOpen" x-transition class="space-y-2 pt-1">
                            <input type="number" 
                                   name="min" 
                                   value="{{ $minPrice }}" 
                                   placeholder="Harga Min (Rp)" 
                                   class="w-full text-xs rounded-xl border border-slate-200 bg-slate-50/60 px-3 py-2 text-slate-700 focus:bg-white focus:outline-none focus:border-blue-600">
                            <input type="number" 
                                   name="max" 
                                   value="{{ $maxPrice }}" 
                                   placeholder="Harga Maks (Rp)" 
                                   class="w-full text-xs rounded-xl border border-slate-200 bg-slate-50/60 px-3 py-2 text-slate-700 focus:bg-white focus:outline-none focus:border-blue-600">
                            <button type="submit" 
                                    class="w-full py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-xl transition shadow-xs cursor-pointer">
                                Terapkan
                            </button>
                        </div>
                    </div>

                    <!-- Link Reset Filter -->
                    @if ($hasFilter ?? false)
                        <div class="pt-2 text-center">
                            <a href="{{ route('listings.index') }}" 
                               class="text-xs font-bold text-rose-600 hover:text-rose-700 hover:underline">
                                Reset Semua Filter
                            </a>
                        </div>
                    @endif

                </form>

            </div>
        </aside>

        <!-- ==================================================== -->
        <!-- KOLOM KANAN: GRID KATALOG 3 KOLOM                   -->
        <!-- ==================================================== -->
        <main class="col-span-12 lg:col-span-9 space-y-6">
            
            <!-- Header Bar Grid: Info Jumlah & Dropdown Urutkan -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-slate-200/80">
                <div class="text-xs sm:text-sm text-slate-500 font-medium">
                    @if ($listings instanceof \Illuminate\Pagination\LengthAwarePaginator && $listings->total() > 0)
                        Menampilkan <span class="font-bold text-slate-800">{{ $listings->firstItem() }}–{{ $listings->lastItem() }}</span> dari <span class="font-bold text-slate-800">{{ $listings->total() }}</span> iklan
                    @else
                        Menampilkan <span class="font-bold text-slate-800">{{ $listings->count() }}</span> iklan
                    @endif

                    @if (!empty($activeCategory))
                        <span class="ml-1 text-blue-600 font-bold">• Kategori: {{ ucfirst($activeCategory) }}</span>
                    @endif
                </div>

                <!-- Dropdown Urutkan -->
                <div class="flex items-center gap-2 self-end sm:self-auto" x-data="{ sortOpen: false }">
                    <span class="text-xs text-slate-400 font-semibold">Urutkan:</span>
                    <div class="relative">
                        <button type="button" 
                                @click="sortOpen = !sortOpen" 
                                @click.away="sortOpen = false"
                                class="bg-white border border-slate-200 text-slate-800 text-xs font-bold rounded-xl px-3.5 py-1.5 flex items-center gap-2 hover:border-slate-300 transition shadow-2xs cursor-pointer">
                            <span>
                                {{ $sort === 'lowest' ? 'Harga Terendah' : ($sort === 'highest' ? 'Harga Tertinggi' : 'Terbaru') }}
                            </span>
                            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>

                        <div x-show="sortOpen" 
                             x-cloak 
                             x-transition 
                             class="absolute right-0 top-full mt-1.5 w-44 bg-white rounded-xl shadow-xl border border-slate-100 p-1.5 z-30">
                            @foreach ([
                                'latest' => 'Terbaru',
                                'lowest' => 'Harga Terendah',
                                'highest' => 'Harga Tertinggi'
                            ] as $val => $lbl)
                                <a href="{{ request()->fullUrlWithQuery(['sort' => $val, 'page' => null]) }}" 
                                   class="block px-3 py-2 rounded-lg text-xs font-semibold transition {{ $sort === $val ? 'bg-blue-50 text-blue-600 font-bold' : 'text-slate-700 hover:bg-slate-50 hover:text-blue-600' }}">
                                    {{ $lbl }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <!-- Grid 3 Kolom Kartu Iklan Minimalis (Gambar 3) -->
            @if ($listings->isNotEmpty())
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach ($listings as $listing)
                        @include('katalog.partials.card', ['listing' => $listing, 'isBoosted' => false])
                    @endforeach
                </div>
            @else
                <!-- Empty State -->
                <div class="rounded-3xl border border-dashed border-slate-300 bg-white p-12 text-center space-y-3">
                    <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center mx-auto">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                    <div class="space-y-1">
                        <h4 class="text-sm font-bold text-slate-800">Iklan Tidak Ditemukan</h4>
                        <p class="text-xs text-slate-500 max-w-sm mx-auto">
                            Belum ada iklan barang kos yang sesuai dengan filter atau pencarian Anda.
                        </p>
                    </div>
                    <a href="{{ route('listings.index') }}" 
                       class="inline-block mt-2 text-xs font-bold text-blue-600 hover:underline">
                        &larr; Lihat Semua Iklan di Katalog
                    </a>
                </div>
            @endif

            <!-- Pagination Bersih di Bawah Grid (Gambar 2) -->
            @if ($listings instanceof \Illuminate\Pagination\LengthAwarePaginator && $listings->hasPages())
                <div class="flex items-center justify-between pt-6 border-t border-slate-200/80">
                    <!-- Tombol Sebelumnya -->
                    <div>
                        @if ($listings->onFirstPage())
                            <span class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-bold text-slate-300 border border-slate-100 bg-slate-50 cursor-not-allowed select-none">
                                &larr; Sebelumnya
                            </span>
                        @else
                            <a href="{{ $listings->previousPageUrl() }}" 
                               class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-bold text-slate-700 hover:text-blue-600 border border-slate-200 bg-white hover:bg-slate-50 transition shadow-2xs">
                                &larr; Sebelumnya
                            </a>
                        @endif
                    </div>

                    <!-- Nomor Halaman -->
                    <div class="flex items-center gap-1.5">
                        @foreach ($listings->getUrlRange(1, $listings->lastPage()) as $page => $url)
                            <a href="{{ $url }}" 
                               class="w-8 h-8 rounded-xl flex items-center justify-center text-xs font-bold transition {{ $listings->currentPage() == $page ? 'bg-blue-600 text-white shadow-xs' : 'text-slate-600 hover:bg-slate-100' }}">
                                {{ $page }}
                            </a>
                        @endforeach
                    </div>

                    <!-- Tombol Berikutnya -->
                    <div>
                        @if ($listings->hasMorePages())
                            <a href="{{ $listings->nextPageUrl() }}" 
                               class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-bold text-slate-700 hover:text-blue-600 border border-slate-200 bg-white hover:bg-slate-50 transition shadow-2xs">
                                Berikutnya &rarr;
                            </a>
                        @else
                            <span class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-bold text-slate-300 border border-slate-100 bg-slate-50 cursor-not-allowed select-none">
                                Berikutnya &rarr;
                            </span>
                        @endif
                    </div>
                </div>
            @endif

        </main>
    </section>

    <!-- ======================================================== -->
    <!-- 4. BANNER CTA BAWAH DARK CARD (ADAPTASI GAMBAR 4)        -->
    <!-- ======================================================== -->
    <section class="rounded-3xl bg-slate-900 text-white p-8 sm:p-12 md:p-14 border border-slate-800 shadow-xl grid grid-cols-1 md:grid-cols-12 gap-8 items-center">
        <!-- Kolom Kiri -->
        <div class="col-span-12 md:col-span-7 space-y-3">
            <h2 class="text-2xl sm:text-3xl lg:text-4xl font-black text-white tracking-tight leading-tight">
                Barang Kos Menumpuk? Lungsurkan Aja!
            </h2>
            <p class="text-xs sm:text-sm text-slate-400 max-w-md leading-relaxed">
                Pasang iklan gratis sekarang. Negosiasi dan COD dilakukan langsung sesama mahasiswa Malang tanpa potongan admin aplikasi.
            </p>
            <div class="pt-2">
                @if (auth()->check() && !auth()->user()->isSeller())
                    <form method="POST" action="{{ route('user.switch_mode') }}" class="inline-block">
                        @csrf
                        <input type="hidden" name="mode" value="seller">
                        <button type="submit" 
                                class="inline-flex items-center gap-2 px-7 py-3.5 rounded-full bg-blue-600 hover:bg-blue-700 text-white text-xs sm:text-sm font-extrabold shadow-lg shadow-blue-600/30 transition cursor-pointer">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                            </svg>
                            <span>+ Pasang Iklan Gratis</span>
                        </button>
                    </form>
                @else
                    <a href="{{ Route::has('listings.create') ? route('listings.create') : url('/katalog/pasang') }}" 
                       class="inline-flex items-center gap-2 px-7 py-3.5 rounded-full bg-blue-600 hover:bg-blue-700 text-white text-xs sm:text-sm font-extrabold shadow-lg shadow-blue-600/30 transition cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                        </svg>
                        <span>+ Pasang Iklan Gratis</span>
                    </a>
                @endif
            </div>
        </div>

        <!-- Kolom Kanan -->
        <div class="col-span-12 md:col-span-5 space-y-2 border-t md:border-t-0 md:border-l border-slate-800 pt-6 md:pt-0 md:pl-8">
            <h3 class="text-base sm:text-lg font-bold text-blue-400">
                Lungsur.in untuk Mahasiswa Malang
            </h3>
            <p class="text-xs sm:text-sm text-slate-300 leading-relaxed">
                Ubah buku lama, perabot kos, atau elektronik yang nggak terpakai jadi uang tambahan — atau pasang wanted ads kalau lagi cari barang murah.
            </p>
        </div>
    </section>

</div>
