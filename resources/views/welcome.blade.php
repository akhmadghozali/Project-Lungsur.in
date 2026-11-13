<x-layouts.app>
    <x-slot:title>
        {{ request()->is('katalog*') ? 'Katalog Iklan Mahasiswa - Lungsur.in' : 'Lungsur.in - Platform Iklan & Lelang Komunitas Mahasiswa Malang' }}
    </x-slot:title>

    @if ($isCatalog ?? request()->is('katalog*'))
        {{-- ======================================================== --}}
        {{-- 1. TAMPILAN MODERN HALAMAN KATALOG (SESUAI GAMBAR 2, 3 & 4) --}}
        {{-- ======================================================== --}}
        @include('katalog.partials.catalog-page')
    @else
        {{-- ======================================================== --}}
        {{-- 2. TAMPILAN HALAMAN BERANDA / LANDING PAGE               --}}
        {{-- ======================================================== --}}
        <div class="space-y-12 sm:space-y-16">
            <!-- ========================================== -->
            <!-- 1. HERO SECTION (Sesuai Desain Figma)      -->
            <!-- ========================================== -->
            <section class="bg-white rounded-[2.5rem] border border-slate-200 p-6 sm:p-10 lg:p-12 relative overflow-hidden shadow-xl shadow-slate-200/60 text-center text-slate-900">
            <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_top,_var(--tw-gradient-stops))] from-blue-50/80 via-white to-white pointer-events-none"></div>
            <div class="relative z-10 max-w-5xl mx-auto space-y-6 sm:space-y-7">
                
                <!-- Badge Header -->
                <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full text-xs font-bold bg-[#FFEE58] text-slate-950 shadow-sm mx-auto">
                    <svg class="w-3.5 h-3.5 text-slate-950" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                    <span>Platform Iklan &amp; Lelang Mahasiswa Malang</span>
                </div>

                <!-- Heading Utama -->
                <h1 class="text-3xl sm:text-5xl lg:text-6xl font-white text-black tracking-tight leading-[1.15] max-w-4xl mx-auto">
                    Pasang Iklan, <span class="inline-block bg-[#ea580c] text-[#EFF6FF] px-3 sm:px-4 py-0.5 sm:py-1 rounded-2xl font-black shadow-lg mx-1 border border-orange-400/40 align-middle">Wanted Ads</span>, dan Lelang Barang Mahasiswa
                </h1>

                <!-- Deskripsi Singkat -->
                <p class="text-sm sm:text-base text-slate-600 leading-relaxed max-w-2xl mx-auto font-normal">
                    Pasang iklan barang yang mau kamu lungsurkan, buat iklan dicari saat butuh barang, atau ikut lelang terbuka. Nego dan COD langsung sesama mahasiswa Malang.
                </p>

                <!-- Search Box Pill -->
                <form action="{{ Route::has('listings.index') ? route('listings.index') : url('/katalog') }}" method="GET" class="max-w-xl mx-auto w-full pt-1">
                    <div class="searchbox-glow bg-slate-50 p-1.5 sm:p-2 pl-4 sm:pl-5 rounded-full border border-slate-200 shadow-inner flex items-center justify-between gap-3">
                        
                        <div class="flex items-center gap-3 w-full">
                            <svg class="w-5 h-5 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                            <input type="text" 
                                   name="q" 
                                   placeholder="Cari iklan barang kos, buku, elektronik, atau cek wanted ads..." 
                                   class="w-full text-xs sm:text-sm text-white placeholder-slate-400 focus:outline-none bg-transparent">
                        </div>
                        <button type="submit" 
                                class="shrink-0 bg-blue-600 hover:bg-blue-500 text-white font-bold text-xs sm:text-sm px-6 py-2.5 rounded-full shadow-md shadow-blue-600/30 transition flex items-center justify-center gap-2 cursor-pointer">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                            <span>Cari</span>
                        </button>
                    </div>
                </form>


                <!-- 3 Badge Jaminan Kepercayaan -->
                <div class="pt-1 flex flex-wrap items-center justify-center gap-2.5 sm:gap-4 text-xs font-semibold text-slate-700">
                  <div class="trust-badge inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-blue-50 border border-blue-100 shadow-xs">  
                <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-blue-50 border border-blue-100 shadow-xs">
                        <div class="w-4 h-4 rounded-full bg-emerald-500/20 text-emerald-400 flex items-center justify-center">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                        <span>Khusus Mahasiswa Malang</span>
                    </div>
                    <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-blue-50 border border-blue-100 shadow-xs">
                        <div class="w-4 h-4 rounded-full bg-blue-500/20 text-blue-400 flex items-center justify-center">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                            </svg>
                        </div>
                        <span>Iklan, Wanted Ads &amp; Lelang</span>
                    </div>
                    <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-blue-50 border border-blue-100 shadow-xs">
                        <div class="w-4 h-4 rounded-full bg-amber-500/20 text-amber-400 flex items-center justify-center">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </div>
                        <span>Deal &amp; COD Langsung</span>
                    </div>
                </div>
                
                

                <!-- Clothesline & 5 Hanging Cards Section -->
                <div class="relative w-full pt-8 sm:pt-10">           
<!-- Banner Kuning di Atas Jemuran -->
<div class="relative z-20 text-center mb-8 sm:mb-10">
    <div class="inline-block bg-[#FFF5B8] text-[#5C4825] font-black
    text-sm sm:text-base
    px-7 sm:px-10 py-3 sm:py-4
    rounded-xl sm:rounded-2xl
    shadow-lg border border-[#F0D77A]
    animate-banner-float">

        Lungsurkan Barang, Temukan Kebutuhan!
    </div>
</div>
<style>
    @keyframes bannerFloat {
        0%, 100% {
            transform: translateY(0) rotate(-1deg);
        }

        50% {
            transform: translateY(-7px) rotate(1deg);
        }
    }

    .animate-banner-float {
        animation: bannerFloat 3s ease-in-out infinite;
    }
</style>


                    <!-- Clothesline Curved Wire SVG -->
                      <!-- AREA JEMURAN -->
    <div class="relative">

        <!-- TALI RAMI REALISTIS -->
        <div class="absolute top-3 sm:top-4 left-2 right-2
                    h-12 pointer-events-none z-10">

            <svg class="w-full h-full"
                 viewBox="0 0 1000 70"
                 preserveAspectRatio="none">

                <!-- Bayangan tali -->
                <path d="M5 8 Q500 75 995 8"
                      stroke="#92400e"
                      stroke-width="10"
                      opacity=".15"
                      fill="none"
                      stroke-linecap="round"/>

                <!-- Badan tali -->
                <path d="M5 5 Q500 72 995 5"
                      stroke="#a87943"
                      stroke-width="6"
                      fill="none"
                      stroke-linecap="round"/>

                <!-- Serat tali -->
                <path d="M5 4 Q500 71 995 4"
                      stroke="#e6c18b"
                      stroke-width="2"
                      stroke-dasharray="5 4"
                      fill="none"
                      stroke-linecap="round"/>

                <!-- Highlight -->
                <path d="M5 2 Q500 69 995 2"
                      stroke="#f5deb3"
                      stroke-width="1.5"
                      opacity=".9"
                      fill="none"/>

            </svg>
        </div>
                    <!-- 5 Hanging Cards -->
                    <div class="flex items-end justify-start sm:justify-center gap-3 sm:gap-4 lg:gap-5 overflow-x-auto pb-4 pt-2 no-scrollbar snap-x relative z-20 px-2">
                        
                        <!-- Card 1: Sepatu (Olive Brown) -->
                        <a href="{{ Route::has('listings.index') ? route('listings.index', ['q' => 'sepatu']) : url('/katalog') }}" class="flex flex-col items-center shrink-0 snap-center group">
                            <!-- Clothespin Wooden Peg -->
                            <div class="w-3.5 h-10 sm:w-4 sm:h-7 bg-[#d59850] border border-[#a26829] rounded-xs mx-auto -mb-3 sm:-mb-3.5 relative z-30 shadow-xs flex flex-col items-center justify-center">
                                <div class="w-full h-1 bg-[#8c521b] opacity-80"></div>
                            </div>
                            <!-- Card Body -->
                           <div class="mt-3 w-36 sm:w-44 lg:w-48
                                h-48 sm:h-56 lg:h-60
                                rounded-2xl sm:rounded-3xl
                                bg-[#FFE7A3]
                                border border-[#E8C66D]/60
                                p-3 sm:p-4
                                flex flex-col items-center justify-center
                                shadow-xl
                                transition-all duration-300
                                group-hover:-translate-y-2
                                group-hover:shadow-2xl">
                                <svg class="w-24 h-24 sm:w-28 sm:h-28 filter drop-shadow-md" viewBox="0 0 120 120" fill="none">
                                    <path d="M22 76C24 64 36 60 48 60L72 58C78 52 86 42 94 40C98 39 104 43 103 49L99 74C98 77 95 78 90 78L22 78Z" fill="#ef4444"/>
                                    <path d="M22 76C23 68 28 64 36 63L35 78L22 78Z" fill="#f8fafc"/>
                                    <circle cx="86" cy="58" r="6" fill="#f8fafc"/>
                                    <path d="M86 54L87.5 57H90.5L88 59L89 62L86 60.5L83 62L84 59L81.5 57H84.5L86 54Z" fill="#dc2626"/>
                                    <line x1="48" y1="62" x2="56" y2="54" stroke="#f8fafc" stroke-width="2.5" stroke-linecap="round"/>
                                    <line x1="56" y1="63" x2="65" y2="53" stroke="#f8fafc" stroke-width="2.5" stroke-linecap="round"/>
                                    <line x1="65" y1="64" x2="74" y2="52" stroke="#f8fafc" stroke-width="2.5" stroke-linecap="round"/>
                                    <rect x="18" y="77" width="87" height="10" rx="4" fill="#f8fafc"/>
                                    <line x1="20" y1="82" x2="103" y2="82" stroke="#cbd5e1" stroke-width="1.5"/>
                                </svg>
                            </div>
                        </a>

                        <!-- Card 2: Laptop (Dark Blue) -->
                        <a href="{{ Route::has('listings.index') ? route('listings.index', ['q' => 'laptop']) : url('/katalog') }}" class="flex flex-col items-center shrink-0 snap-center group">
                            <!-- Clothespin Wooden Peg -->
                            <div class="w-3.5 h-6 sm:w-4 sm:h-7 bg-[#d59850] border border-[#a26829] rounded-xs mx-auto -mb-3 sm:-mb-3.5 relative z-30 shadow-xs flex flex-col items-center justify-center">
                                <div class="w-full h-1 bg-[#8c521b] opacity-80"></div>
                            </div>
                            <!-- Card Body -->
                             <div class="mt-3 w-36 sm:w-44 lg:w-48
                                h-48 sm:h-56 lg:h-60
                                rounded-2xl sm:rounded-3xl
                                bg-[#C2D7EA]
                                border border-[#4D6882]/60
                                p-3 sm:p-4
                                flex flex-col items-center justify-center
                                shadow-xl
                                transition-all duration-300
                                group-hover:-translate-y-2
                                group-hover:shadow-2xl">
                                <svg class="w-24 h-24 sm:w-28 sm:h-28 filter drop-shadow-md" viewBox="0 0 120 120" fill="none">
                                    <rect x="25" y="32" width="70" height="46" rx="4" fill="#334155" stroke="#475569" stroke-width="2"/>
                                    <rect x="29" y="36" width="62" height="38" rx="2" fill="#090d16"/>
                                    <path d="M44 51L37 55L44 59" stroke="#38bdf8" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
                                    <line x1="56" y1="49" x2="64" y2="61" stroke="#38bdf8" stroke-width="2.5" stroke-linecap="round"/>
                                    <path d="M76 51L83 55L76 59" stroke="#38bdf8" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M16 80C16 78.5 17.5 77 19 77H101C102.5 77 104 78.5 104 80L100 84C99.5 85 98 85.5 96 85.5H24C22 85.5 20.5 85 20 84L16 80Z" fill="#475569" stroke="#334155" stroke-width="1.5"/>
                                    <rect x="52" y="78" width="16" height="2" rx="1" fill="#1e293b"/>
                                </svg>
                            </div>
                        </a>

                        <!-- Card 3: Baju & Hanger (Charcoal Navy) -->
                        <a href="{{ Route::has('listings.index') ? route('listings.index', ['q' => 'baju']) : url('/katalog') }}" class="flex flex-col items-center shrink-0 snap-center group">
                            <!-- Clothespin Wooden Peg -->
                            <div class="w-3.5 h-6 sm:w-4 sm:h-7 bg-[#d59850] border border-[#a26829] rounded-xs mx-auto -mb-3 sm:-mb-3.5 relative z-30 shadow-xs flex flex-col items-center justify-center">
                                <div class="w-full h-1 bg-[#8c521b] opacity-80"></div>
                            </div>
                            <!-- Card Body -->
                             <div class="mt-3 w-36 sm:w-44 lg:w-48
                                h-48 sm:h-56 lg:h-60
                                rounded-2xl sm:rounded-3xl
                                bg-[#C7D9B7]
                                border border-[#526447]/60
                                p-3 sm:p-4
                                flex flex-col items-center justify-center
                                shadow-xl
                                transition-all duration-300
                                group-hover:-translate-y-2
                                group-hover:shadow-2xl">
                                <svg class="w-24 h-24 sm:w-28 sm:h-28 filter drop-shadow-md" viewBox="0 0 120 120" fill="none">
                                    <path d="M60 38C60 34 63 31 66 31C69 31 71 33 71 35C71 38 68 40 64 42L60 45" stroke="#cbd5e1" stroke-width="2.5" stroke-linecap="round"/>
                                    <path d="M60 45L34 56C32 57 32 59 34 60L60 63L86 60C88 59 88 57 86 56L60 45Z" stroke="#cbd5e1" stroke-width="2" fill="none"/>
                                    <path d="M37 58L22 72L31 80L40 73V92C40 93.5 41.5 95 43 95H77C78.5 95 80 93.5 80 92V73L89 80L98 72L83 58L69 63L60 60L51 63L37 58Z" fill="#2563eb"/>
                                    <path d="M51 63L60 69L69 63L64 57L60 59L56 57L51 63Z" fill="#bfdbfe"/>
                                    <line x1="60" y1="69" x2="60" y2="95" stroke="#1d4ed8" stroke-width="2"/>
                                </svg>
                            </div>
                        </a>

                        <!-- Card 4: Buku & Cangkir (Forest Green) -->
                        <a href="{{ Route::has('listings.index') ? route('listings.index', ['q' => 'buku']) : url('/katalog') }}" class="flex flex-col items-center shrink-0 snap-center group">
                            <!-- Clothespin Wooden Peg -->
                            <div class="w-3.5 h-6 sm:w-4 sm:h-7 bg-[#d59850] border border-[#a26829] rounded-xs mx-auto -mb-3 sm:-mb-3.5 relative z-30 shadow-xs flex flex-col items-center justify-center">
                                <div class="w-full h-1 bg-[#8c521b] opacity-80"></div>
                            </div>
                            <!-- Card Body -->
                            <div class="mt-3 w-36 sm:w-44 lg:w-48
                                h-48 sm:h-56 lg:h-60
                                rounded-2xl sm:rounded-3xl
                                bg-[#E9DDF8]
                                border border-[#A58BBE]/60
                                p-3 sm:p-4
                                flex flex-col items-center justify-center
                                shadow-xl
                                transition-all duration-300
                                group-hover:-translate-y-2
                                group-hover:shadow-2xl">
                                <svg class="w-24 h-24 sm:w-28 sm:h-28 filter drop-shadow-md" viewBox="0 0 120 120" fill="none">
                                    <rect x="70" y="37" width="14" height="15" rx="3" fill="#f8fafc"/>
                                    <path d="M84 41C87 41 89 43 89 45C89 47 87 49 84 49" stroke="#f8fafc" stroke-width="2.5" fill="none"/>
                                    <path d="M74 31C73 33 75 34 74 36" stroke="#94a3b8" stroke-width="1.5" stroke-linecap="round"/>
                                    <path d="M78 31C77 33 79 34 78 36" stroke="#94a3b8" stroke-width="1.5" stroke-linecap="round"/>
                                    <rect x="30" y="52" width="60" height="9" rx="2.5" fill="#06b6d4"/>
                                    <rect x="84" y="53.5" width="4" height="6" fill="#f8fafc"/>
                                    <rect x="25" y="62" width="70" height="10" rx="2.5" fill="#eab308"/>
                                    <rect x="88" y="63.5" width="5" height="7" fill="#f8fafc"/>
                                    <rect x="22" y="73" width="76" height="11" rx="3" fill="#ea580c"/>
                                    <rect x="91" y="74.5" width="5" height="8" fill="#f8fafc"/>
                                    <line x1="28" y1="78.5" x2="88" y2="78.5" stroke="#c2410c" stroke-width="1.5"/>
                                </svg>
                            </div>
                        </a>

                        <!-- Card 5: Tas Ransel (Deep Purple) -->
                        <a href="{{ Route::has('listings.index') ? route('listings.index', ['q' => 'tas']) : url('/katalog') }}" class="flex flex-col items-center shrink-0 snap-center group">
                            <!-- Clothespin Wooden Peg -->
                            <div class="w-3.5 h-6 sm:w-4 sm:h-7 bg-[#d59850] border border-[#a26829] rounded-xs mx-auto -mb-3 sm:-mb-3.5 relative z-30 shadow-xs flex flex-col items-center justify-center">
                                <div class="w-full h-1 bg-[#8c521b] opacity-80"></div>
                            </div>
                            <!-- Card Body -->
                             <div class="mt-3 w-36 sm:w-44 lg:w-48
                                h-48 sm:h-56 lg:h-60
                                rounded-2xl sm:rounded-3xl
                                bg-[#F2C6C6]
                                border border-[#875858]/60
                                p-3 sm:p-4
                                flex flex-col items-center justify-center
                                shadow-xl
                                transition-all duration-300
                                group-hover:-translate-y-2
                                group-hover:shadow-2xl">
                                <svg class="w-24 h-24 sm:w-28 sm:h-28 filter drop-shadow-md" viewBox="0 0 120 120" fill="none">
                                    <path d="M52 35C52 31 55 28 60 28C65 28 68 31 68 35" stroke="#c2410c" stroke-width="3" fill="none" stroke-linecap="round"/>
                                    <rect x="34" y="35" width="52" height="58" rx="14" fill="#f97316"/>
                                    <path d="M42 46C42 41 46 38 60 38C74 38 78 41 78 46" stroke="#ea580c" stroke-width="2" fill="none"/>
                                    <rect x="42" y="58" width="36" height="28" rx="8" fill="#facc15"/>
                                    <line x1="48" y1="64" x2="72" y2="64" stroke="#d97706" stroke-width="2" stroke-linecap="round"/>
                                    <rect x="30" y="54" width="4" height="20" rx="2" fill="#c2410c"/>
                                    <rect x="86" y="54" width="4" height="20" rx="2" fill="#c2410c"/>
                                </svg>
                            </div>
                        </a>

                    </div>

                    <!-- Slider Bar Cue -->
                    <div class="mt-6 sm:mt-8 flex items-center justify-center">
                        <div class="w-44 sm:w-56 h-1.5 bg-slate-800 rounded-full flex items-center p-0.5">
                            <div class="w-14 sm:w-16 h-1 bg-slate-500 rounded-full"></div>
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
                        <div class="flex h-8 w-8 items-center justify-center rounded-xl bg-blue-100 text-blue-600">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
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

                        <span class="absolute left-3 top-3 inline-flex items-center gap-1.5 rounded-lg bg-blue-600 px-3 py-1.5 text-xs font-bold text-white shadow">
                            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                            <span>SPOTLIGHT</span>
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
                                <p class="text-xs font-bold text-rose-600 inline-flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    <span>Sisa waktu</span>
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
                    <h3 class="text-lg sm:text-xl font-black text-slate-900 tracking-tight">Ada Barang Kos yang Nggak Kepakai?</h3>
                    <p class="text-xs sm:text-sm text-slate-500 mt-1">Pasang iklan gratis sekarang, atau pakai Boost biar iklanmu tampil paling atas dan makin cepat dilirik.</p>
                </div>
            </div>

            <!-- Tombol Pasang Iklan -->
            <div class="shrink-0 w-full md:w-auto">
                @if (auth()->check() && !auth()->user()->isSeller())
                    <form method="POST" action="{{ route('user.switch_mode') }}">
                        @csrf
                        <input type="hidden" name="mode" value="seller">
                        <button type="submit" 
                                class="inline-flex w-full md:w-auto items-center justify-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-bold px-7 py-3.5 rounded-2xl shadow-md shadow-emerald-500/25 transition cursor-pointer"
                                title="Beralih ke Mode Penjual untuk pasang iklan">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                            </svg>
                            <span>Ganti Mode Penjual untuk Jual</span>
                        </button>
                    </form>
                @else
                    <a href="{{ Route::has('listings.create') ? route('listings.create') : url('/katalog/pasang') }}" 
                       class="inline-flex w-full md:w-auto items-center justify-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold px-7 py-3.5 rounded-2xl shadow-md shadow-blue-500/25 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                        </svg>
                        <span>Pasang Iklan</span>
                    </a>
                @endif
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
                        @if (!($isCatalog ?? false) && empty($search))
                            <div class="w-6 h-6 rounded-full bg-amber-100 text-amber-700 flex items-center justify-center text-xs">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                </svg>
                            </div>
                            <h2 class="text-xl font-black text-slate-900 tracking-tight">
                                Iklan Unggulan Pilihan
                            </h2>
                        @else
                            <div class="w-6 h-6 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-xs font-bold">
                                @if (!empty($search))
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                    </svg>
                                @else
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                    </svg>
                                @endif
                            </div>
                            <h2 class="text-xl font-black text-slate-900 tracking-tight">
                                @if (!empty($search))
                                    Hasil Pencarian
                                @elseif ($isCatalog ?? false)
                                    Jelajahi Iklan Mahasiswa
                                @else
                                    Katalog Produk Mahasiswa
                                @endif
                            </h2>
                        @endif
                    </div>
                    <p class="text-xs sm:text-sm text-slate-500 mt-1">
                        @if (!empty($search))
                            Menampilkan barang yang cocok dengan kata kunci "{{ $search }}".
                        @elseif (!($isCatalog ?? false))
                            Menampilkan produk pilihan mahasiswa yang sedang aktif di-boost.
                            <a href="{{ Route::has('listings.index') ? route('listings.index') : url('/katalog') }}" class="font-bold text-blue-600 hover:underline ml-1">
                                Lihat semua di Katalog &rarr;
                            </a>
                        @else
                            Iklan Boost tampil paling atas biar gampang dilirik, disusul iklan gratis terbaru dari mahasiswa Malang.
                        @endif
                    </p>
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

            <!-- Bilah Filter Multi-Kriteria (Sesuai Proposal & Sprint 2) -->
            <div class="bg-white rounded-2xl border border-slate-200/80 p-4 sm:p-5 shadow-xs">
                <form action="{{ Route::has('listings.index') ? route('listings.index') : url('/katalog') }}" method="GET" class="space-y-4">
                    @if (!empty($search))
                        <input type="hidden" name="q" value="{{ $search }}">
                    @endif
                    <input type="hidden" name="sort" value="{{ $sort }}">

                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-3">
                        <!-- 1. Kampus Penjual -->
                        <div>
                            <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Asal Kampus</label>
                            <select name="kampus" class="w-full text-xs rounded-xl border border-slate-200 bg-slate-50/60 px-3 py-2 text-slate-700 focus:bg-white focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-100">
                                <option value="">Semua Kampus</option>
                                <option value="UB" {{ ($filters['kampus'] ?? '') === 'UB' ? 'selected' : '' }}>UB (Universitas Brawijaya)</option>
                                <option value="Polinema" {{ ($filters['kampus'] ?? '') === 'Polinema' ? 'selected' : '' }}>Politeknik Negeri Malang</option>
                                <option value="UM" {{ ($filters['kampus'] ?? '') === 'UM' ? 'selected' : '' }}>UM (Universitas Negeri Malang)</option>
                                <option value="UIN" {{ ($filters['kampus'] ?? '') === 'UIN' ? 'selected' : '' }}>UIN Maulana Malik Ibrahim</option>
                                <option value="UMM" {{ ($filters['kampus'] ?? '') === 'UMM' ? 'selected' : '' }}>UMM (Muhammadiyah)</option>
                                <option value="ITN" {{ ($filters['kampus'] ?? '') === 'ITN' ? 'selected' : '' }}>ITN Malang</option>
                            </select>
                        </div>

                        <!-- 2. Kondisi Barang -->
                        <div>
                            <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Kondisi Barang</label>
                            <select name="kondisi" class="w-full text-xs rounded-xl border border-slate-200 bg-slate-50/60 px-3 py-2 text-slate-700 focus:bg-white focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-100">
                                <option value="">Semua Kondisi</option>
                                <option value="bekas" {{ ($filters['kondisi'] ?? '') === 'bekas' ? 'selected' : '' }}>Bekas (Semua Varian)</option>
                                <option value="baru" {{ ($filters['kondisi'] ?? '') === 'baru' ? 'selected' : '' }}>Baru</option>
                            </select>
                        </div>

                        <!-- 3. Kategori Perlengkapan Kos -->
                        <div>
                            <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Kategori</label>
                            <select name="category" class="w-full text-xs rounded-xl border border-slate-200 bg-slate-50/60 px-3 py-2 text-slate-700 focus:bg-white focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-100">
                                <option value="">Semua Kategori</option>
                                @foreach ($allCategories ?? [] as $cat)
                                    <option value="{{ $cat->slug }}" {{ ($filters['category'] ?? '') === $cat->slug ? 'selected' : '' }}>{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- 4. Harga Minimum -->
                        <div>
                            <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Harga Min (Rp)</label>
                            <input type="number" step="1000" min="0" name="min" value="{{ $filters['min'] ?? '' }}" placeholder="Contoh: 50000" class="w-full text-xs rounded-xl border border-slate-200 bg-slate-50/60 px-3 py-2 text-slate-700 focus:bg-white focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-100">
                        </div>

                        <!-- 5. Harga Maksimum -->
                        <div>
                            <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Harga Maks (Rp)</label>
                            <input type="number" step="1000" min="0" name="max" value="{{ $filters['max'] ?? '' }}" placeholder="Contoh: 200000" class="w-full text-xs rounded-xl border border-slate-200 bg-slate-50/60 px-3 py-2 text-slate-700 focus:bg-white focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-100">
                        </div>
                    </div>

                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pt-3 border-t border-slate-100 text-xs">
                        <div class="flex flex-wrap items-center gap-1.5 text-slate-600">
                            @if ($hasFilter ?? false)
                                <span class="text-[11px] font-semibold text-slate-400">Filter aktif:</span>
                                @if (!empty($filters['kampus']))
                                    <span class="inline-flex items-center gap-1 bg-blue-50 text-blue-700 px-2 py-0.5 rounded-md font-semibold text-[11px] border border-blue-200/60">
                                        Kampus: {{ strtoupper($filters['kampus']) }}
                                    </span>
                                @endif
                                @if (!empty($filters['kondisi']))
                                    <span class="inline-flex items-center gap-1 bg-blue-50 text-blue-700 px-2 py-0.5 rounded-md font-semibold text-[11px] border border-blue-200/60">
                                        Kondisi: {{ ucfirst($filters['kondisi']) }}
                                    </span>
                                @endif
                                @if (!empty($filters['min']))
                                    <span class="inline-flex items-center gap-1 bg-blue-50 text-blue-700 px-2 py-0.5 rounded-md font-semibold text-[11px] border border-blue-200/60">
                                        Min: Rp{{ number_format($filters['min'], 0, ',', '.') }}
                                    </span>
                                @endif
                                @if (!empty($filters['max']))
                                    <span class="inline-flex items-center gap-1 bg-blue-50 text-blue-700 px-2 py-0.5 rounded-md font-semibold text-[11px] border border-blue-200/60">
                                        Maks: Rp{{ number_format($filters['max'], 0, ',', '.') }}
                                    </span>
                                @endif
                                @if (!empty($filters['category']))
                                    <span class="inline-flex items-center gap-1 bg-blue-50 text-blue-700 px-2 py-0.5 rounded-md font-semibold text-[11px] border border-blue-200/60">
                                        Kategori: {{ ucfirst($filters['category']) }}
                                    </span>
                                @endif
                            @else
                                <span class="text-[11px] text-slate-400">Filter iklan sesuai kampus, kondisi barang, atau budget kamu.</span>
                            @endif
                        </div>

                        <div class="flex items-center gap-2 self-end sm:self-auto">
                            @if ($hasFilter ?? false)
                                <a href="{{ Route::has('listings.index') ? route('listings.index') : url('/katalog') }}" class="text-xs font-semibold text-rose-600 hover:text-rose-700 hover:underline px-2.5 py-1.5 transition">
                                    Reset Filter
                                </a>
                            @endif
                            <button type="submit" class="inline-flex items-center gap-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold px-4 py-2 rounded-xl shadow-xs transition cursor-pointer">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                                </svg>
                                <span>Terapkan Filter</span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>

        <div id="listing-content">
            @php
                $isCatalogPage = $isCatalog ?? request()->is('katalog*');
                $regularTotal = $listings instanceof \Illuminate\Pagination\LengthAwarePaginator
                    ? $listings->total()
                    : $listings->count();
                $boostedCollection = $boostedListings ?? collect();
                $boostedTotal = $isCatalogPage ? $boostedCollection->count() : 0;
                $totalFound = $regularTotal + $boostedTotal;
                $resetUrl = $isCatalogPage
                    ? (Route::has('listings.index') ? route('listings.index') : url('/katalog'))
                    : url('/');
            @endphp

            @if ($hasFilter ?? false)
                <div class="mt-4 flex items-center justify-between bg-blue-50/70 border border-blue-100 rounded-2xl px-4 py-3 text-xs text-blue-900">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-blue-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <span>Menampilkan hasil filter spesifik (<strong>{{ $totalFound }}</strong> barang ditemukan)</span>
                    </div>
                    <a href="{{ $resetUrl }}" class="font-bold text-rose-600 hover:text-rose-700 hover:underline flex items-center gap-1 shrink-0">
                        <span>Reset Semua</span>
                        <span>&times;</span>
                    </a>
                </div>
            @elseif (!empty($search))
                <div class="mt-4 flex items-center justify-between bg-blue-50/70 border border-blue-100 rounded-2xl px-4 py-3 text-xs text-blue-900">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-blue-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <span>Hasil pencarian untuk: <strong class="font-bold text-blue-700">"{{ $search }}"</strong> ({{ $totalFound }} barang ditemukan)</span>
                    </div>
                    <a href="{{ $resetUrl }}" class="font-bold text-rose-600 hover:text-rose-700 hover:underline flex items-center gap-1 shrink-0">
                        <span>Reset Pencarian</span>
                        <span>&times;</span>
                    </a>
                </div>
            @endif

            @if ($isCatalogPage)
                {{-- ===================================================== --}}
                {{-- HALAMAN KATALOG: KOTAK BOOST (ATAS) & REGULER (BAWAH) --}}
                {{-- ===================================================== --}}
                @if ($boostedCollection->isEmpty() && $listings->isEmpty())
                    <div class="mt-6 rounded-2xl border border-dashed border-slate-300 bg-white px-6 py-14 text-center">
                        <div class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-3">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>
                        <p class="text-sm font-bold text-slate-800">
                            {{ ($hasFilter ?? false) || !empty($search) ? 'Barang tidak ditemukan' : 'Belum ada barang' }}
                        </p>
                        <p class="mt-1 text-xs text-slate-500">
                            {{ ($hasFilter ?? false) || !empty($search) ? 'Coba ubah filter atau kata kunci pencarianmu.' : 'Jadilah yang pertama memasang iklan di Lungsur.in.' }}
                        </p>
                        @if (($hasFilter ?? false) || !empty($search))
                            <a href="{{ $resetUrl }}" class="inline-block mt-4 text-xs font-bold text-blue-600 hover:underline">
                                &larr; Lihat Semua Barang
                            </a>
                        @endif
                    </div>
                @else
                    {{-- KOTAK 1 (ATAS): PRODUK BOOST / UNGGULAN --}}
                    @if ($boostedCollection->isNotEmpty())
                        <div class="mt-6 rounded-3xl border border-amber-200/90 bg-gradient-to-br from-amber-50/70 via-amber-50/30 to-orange-50/40 p-5 sm:p-6 shadow-xs overflow-hidden">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 pb-4 border-b border-amber-200/60">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-8 h-8 rounded-xl bg-amber-500 text-slate-950 flex items-center justify-center shadow-xs shrink-0 font-black">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <h3 class="text-base font-black text-slate-900 tracking-tight">Iklan Boost</h3>
                                        <p class="text-[11px] text-amber-900/80 font-medium">Iklan pilihan yang sedang dipromosikan di posisi teratas</p>
                                    </div>
                                </div>
                                <div class="self-start sm:self-auto flex items-center gap-2">
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-amber-100 text-amber-900 text-[11px] font-bold border border-amber-300/80">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                        {{ $boostedCollection->count() }} Iklan Aktif
                                    </span>
                                    <a href="{{ auth()->check() ? (auth()->user()->isSeller() ? route('dashboard', ['tab' => 'dagangan']) : route('dashboard')) : route('login') }}" 
                                       class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-amber-500 hover:bg-amber-600 text-slate-950 text-[11px] font-bold shadow-xs transition"
                                       title="Promosikan iklan barangmu agar muncul di posisi teratas">
                                        <svg class="w-3 h-3 text-slate-950" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                        <span>+ Boost Iklanmu</span>
                                    </a>
                                </div>
                            </div>

                            @if ($boostedCollection->count() <= 3)
                                {{-- Jika <= 3 item: Tampilkan berjajar statis --}}
                                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4 sm:gap-6 mt-5">
                                    @foreach ($boostedCollection as $listing)
                                        <x-listing-card :listing="$listing" />
                                    @endforeach
                                </div>
                            @else
                                {{-- Jika > 3 item: Continuous horizontal infinite marquee dengan pause-on-hover & touch swipe --}}
                                <div class="relative overflow-hidden w-full group/marquee mt-5 pt-1 pb-2">
                                    {{-- Left Gradient Fade --}}
                                    <div class="pointer-events-none absolute left-0 top-0 bottom-0 w-8 sm:w-16 bg-gradient-to-r from-amber-50/95 via-amber-50/80 to-transparent z-10"></div>
                                    {{-- Right Gradient Fade --}}
                                    <div class="pointer-events-none absolute right-0 top-0 bottom-0 w-8 sm:w-16 bg-gradient-to-l from-amber-50/95 via-amber-50/80 to-transparent z-10"></div>

                                    {{-- Horizontal Scrollable for Mobile & Marquee Track --}}
                                    <div class="overflow-x-auto no-scrollbar py-1">
                                        <div class="flex gap-4 sm:gap-6 w-max animate-marquee group-hover/marquee:[animation-play-state:paused] hover:[animation-play-state:paused]">
                                            {{-- Track 1: Original Items --}}
                                            <div class="flex gap-4 sm:gap-6 shrink-0">
                                                @foreach ($boostedCollection as $listing)
                                                    <div class="w-60 sm:w-64 md:w-72 shrink-0">
                                                        <x-listing-card :listing="$listing" />
                                                    </div>
                                                @endforeach
                                            </div>

                                            {{-- Track 2: Duplicate Items for Seamless Infinite Loop --}}
                                            <div class="flex gap-4 sm:gap-6 shrink-0" aria-hidden="true">
                                                @foreach ($boostedCollection as $listing)
                                                    <div class="w-60 sm:w-64 md:w-72 shrink-0">
                                                        <x-listing-card :listing="$listing" />
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endif

                    {{-- KOTAK 2 (BAWAH): IKLAN GRATIS / REGULER --}}
                    <div class="{{ $boostedCollection->isNotEmpty() ? 'mt-8' : 'mt-6' }} rounded-3xl border border-slate-200/80 bg-slate-50/70 p-5 sm:p-6 shadow-xs">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 pb-4 border-b border-slate-200/70">
                            <div class="flex items-center gap-2.5">
                                <div class="w-8 h-8 rounded-xl bg-blue-600 text-white flex items-center justify-center shadow-xs shrink-0">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-base font-black text-slate-900 tracking-tight">Iklan Terbaru</h3>
                                    <p class="text-[11px] text-slate-500 font-medium">Daftar iklan gratis dari mahasiswa di Malang</p>
                                </div>
                            </div>
                            <span class="self-start sm:self-auto inline-flex items-center px-3 py-1 rounded-full bg-white text-slate-700 text-[11px] font-bold border border-slate-200">
                                {{ $regularTotal }} Iklan
                            </span>
                        </div>

                        @if ($listings->isNotEmpty())
                            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 sm:gap-6 mt-5">
                                @foreach ($listings as $listing)
                                    <x-listing-card :listing="$listing" />
                                @endforeach
                            </div>
                        @else
                            <div class="py-10 text-center text-xs text-slate-500 font-medium">
                                Belum ada iklan gratis yang sesuai dengan kriteria ini.
                            </div>
                        @endif
                    </div>
                @endif
            @else
                {{-- ===================================================== --}}
                {{-- HALAMAN BERANDA: HANYA TAMPILKAN PRODUK YANG DI-BOOST --}}
                {{-- ===================================================== --}}
                @if ($listings->isNotEmpty())
                    <div class="mt-4 flex flex-wrap items-center justify-between gap-2 bg-amber-50 border border-amber-200 text-amber-800 text-xs font-semibold px-4 py-2 rounded-xl shadow-2xs">
                        <div class="inline-flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                            <span>Halaman Beranda menampilkan produk yang sedang aktif di-boost</span>
                        </div>
                        <a href="{{ Route::has('listings.index') ? route('listings.index') : url('/katalog') }}" class="font-bold text-amber-900 hover:underline">
                            Buka Semua Katalog &rarr;
                        </a>
                    </div>
                @endif

                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 sm:gap-6 mt-6">
                    @forelse ($listings as $listing)
                        <x-listing-card :listing="$listing" />
                    @empty
                        <div class="col-span-full rounded-2xl border border-dashed border-amber-300/80 bg-amber-50/30 px-6 py-14 text-center">
                            <div class="w-12 h-12 rounded-2xl bg-amber-100 text-amber-700 flex items-center justify-center mx-auto mb-3">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                </svg>
                            </div>
                            <p class="text-sm font-bold text-slate-800">
                                Belum ada iklan unggulan saat ini
                            </p>
                            <p class="mt-1 text-xs text-slate-500 max-w-md mx-auto">
                                Produk yang sedang aktif di-boost oleh mahasiswa akan tampil di beranda. Kamu tetap bisa melihat seluruh barang yang dijual di Katalog.
                            </p>
                            <a href="{{ Route::has('listings.index') ? route('listings.index') : url('/katalog') }}"
                               class="inline-flex items-center gap-1.5 mt-4 px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold shadow-xs transition">
                                <span>Lihat Semua Produk di Katalog</span>
                                <span>&rarr;</span>
                            </a>
                        </div>
                    @endforelse
                </div>

                @if ($listings->isNotEmpty())
                    <div class="mt-8 text-center">
                        <a href="{{ Route::has('listings.index') ? route('listings.index') : url('/katalog') }}"
                           class="inline-flex items-center gap-2 px-6 py-3 rounded-2xl bg-slate-100 hover:bg-blue-600 text-slate-700 hover:text-white font-bold text-xs sm:text-sm transition shadow-2xs">
                            <span>Jelajahi Semua Produk di Katalog</span>
                            <span>&rarr;</span>
                        </a>
                    </div>
                @endif
            @endif

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
    @endif
</x-layouts.app>
