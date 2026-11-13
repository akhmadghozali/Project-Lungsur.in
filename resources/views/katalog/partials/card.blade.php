@props(['listing', 'isBoosted' => false])

@php
    $boostedActive = $isBoosted || ($listing->is_boosted && ($listing->boosted_until === null || $listing->boosted_until->isFuture()));

    // Singkatan nama kampus populer di Malang
    $rawCampus = $listing->user?->campus ?? '';
    $campusShort = match(true) {
        str_contains(strtolower($rawCampus), 'polinema') || str_contains(strtolower($rawCampus), 'politeknik negeri malang') => 'Polinema',
        str_contains(strtolower($rawCampus), 'brawijaya') || str_contains(strtolower($rawCampus), 'ub') => 'UB',
        str_contains(strtolower($rawCampus), 'negeri malang') || str_contains(strtolower($rawCampus), 'um') => 'UM',
        str_contains(strtolower($rawCampus), 'muhammadiyah') || str_contains(strtolower($rawCampus), 'umm') => 'UMM',
        str_contains(strtolower($rawCampus), 'maliki') || str_contains(strtolower($rawCampus), 'uin') => 'UIN Maliki',
        str_contains(strtolower($rawCampus), 'itn') => 'ITN Malang',
        str_contains(strtolower($rawCampus), 'unisma') => 'Unisma',
        str_contains(strtolower($rawCampus), 'machung') => 'Ma Chung',
        default => $rawCampus ?: 'Malang',
    };

    // Kondisi barang
    $rawCondition = $listing->condition_checklist['kondisi'] ?? 'bekas';
    $conditionLabel = match(strtolower($rawCondition)) {
        'baru' => 'Baru',
        'bekas_seperti_baru', 'bekas' => 'Bekas baik',
        'layak_pakai', 'layak' => 'Layak pakai',
        default => 'Bekas baik',
    };

    // Gambar barang pertama
    $firstImg = $listing->images->first();
    $imgSrc = $firstImg 
        ? (\Illuminate\Support\Str::startsWith($firstImg->image_url, ['http://', 'https://']) 
            ? $firstImg->image_url 
            : asset('storage/' . ltrim($firstImg->image_url, '/')))
        : asset('images/placeholder.svg');
@endphp

<div class="bg-white rounded-3xl border border-slate-200/80 hover:border-blue-200 shadow-xs hover:shadow-lg transition-all duration-300 p-3.5 flex flex-col justify-between group">
    <div>
        <!-- Kotak Gambar (Aspect 4/3) -->
        <div class="aspect-[4/3] rounded-2xl bg-slate-100 overflow-hidden relative border border-slate-100/90 mb-3 group/img">
            <!-- Pill Kategori (Pojok Kanan Atas) -->
            <div class="absolute top-2.5 right-2.5 bg-slate-900/70 backdrop-blur-xs text-white text-[10px] font-bold px-2.5 py-0.5 rounded-full z-10 truncate max-w-[120px]">
                {{ $listing->category->name ?? 'Lainnya' }}
            </div>

            <!-- Gambar Barang -->
            <img src="{{ $imgSrc }}" 
                 alt="{{ $listing->title }}" 
                 loading="lazy"
                 onerror="this.onerror=null; this.src='{{ asset('images/placeholder.svg') }}';"
                 class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
        </div>

        <!-- Judul Iklan (Maksimal 2 baris) -->
        <h3 class="font-bold text-slate-900 text-xs sm:text-sm line-clamp-2 leading-snug group-hover:text-blue-600 transition min-h-[2.5rem]" 
            title="{{ $listing->title }}">
            {{ $listing->title }}
        </h3>

        <!-- Baris Info Sejajar (Kiri: Kampus & Kondisi, Kanan: Harga Bold) -->
        <div class="flex items-center justify-between text-xs mt-2.5 pt-2 border-t border-slate-100/80">
            <div class="flex items-center gap-1.5 min-w-0">
                <span class="inline-flex items-center gap-1 text-[10px] font-semibold text-slate-700 bg-slate-100 px-2 py-0.5 rounded-md truncate max-w-[90px]" 
                      title="{{ $rawCampus }}">
                    {{ $campusShort }}
                </span>
                <span class="inline-block text-[10px] font-medium text-slate-500 bg-slate-50 border border-slate-200/60 px-1.5 py-0.5 rounded-md">
                    {{ $conditionLabel }}
                </span>
            </div>

            <div class="font-black text-slate-900 text-xs sm:text-sm shrink-0 ml-1">
                Rp {{ number_format($listing->price, 0, ',', '.') }}
            </div>
        </div>
    </div>

    <!-- Bagian Bawah Kartu: 2 Tombol Kapsul Sejajar (Chat & Lihat Detail) -->
    <div class="grid grid-cols-2 gap-2 mt-3.5 pt-1">
        <!-- Tombol Kiri: Outline "Chat" -->
        <a href="{{ route('chat.index', ['listing_id' => $listing->id]) }}" 
           class="w-full py-2 px-2.5 rounded-full border border-slate-300 hover:border-blue-600 hover:bg-blue-50 text-slate-700 hover:text-blue-600 font-bold text-xs text-center transition flex items-center justify-center gap-1.5 cursor-pointer shadow-2xs">
            <svg class="w-3.5 h-3.5 text-slate-500 group-hover:text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
            </svg>
            <span>Chat</span>
        </a>

        <!-- Tombol Kanan: Solid Biru "Lihat Detail" -->
        <a href="{{ Route::has('listings.show') ? route('listings.show', $listing->id) : url('/katalog/'.$listing->id) }}" 
           class="w-full py-2 px-2.5 rounded-full bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs text-center transition flex items-center justify-center gap-1 cursor-pointer shadow-xs">
            <span>Lihat Detail</span>
        </a>
    </div>
</div>
