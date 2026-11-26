@props(['listing', 'isBoosted' => false])

@php
    $boostedActive = $isBoosted || ($listing->is_boosted && ($listing->boosted_until === null || $listing->boosted_until->isFuture()));

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

    $rawCondition = $listing->condition_checklist['kondisi'] ?? 'bekas';
    $conditionLabel = match(strtolower($rawCondition)) {
        'baru' => 'Baru',
        'bekas_seperti_baru', 'bekas' => 'Bekas baik',
        'layak_pakai', 'layak' => 'Layak pakai',
        default => 'Bekas baik',
    };

    $firstImg = $listing->images->first();
    $imgSrc = $firstImg
        ? (\Illuminate\Support\Str::startsWith($firstImg->image_url, ['http://', 'https://'])
            ? $firstImg->image_url
            : asset('storage/' . ltrim($firstImg->image_url, '/')))
        : asset('images/placeholder.svg');

    $detailUrl = Route::has('listings.show') ? route('listings.show', $listing->id) : url('/katalog/'.$listing->id);
@endphp

<article class="bg-white rounded-3xl border border-slate-200/90 hover:border-blue-300 shadow-2xs hover:shadow-md transition-all duration-200 p-3.5 sm:p-4 flex flex-col justify-between group">
    <div>
        <a href="{{ $detailUrl }}" class="block aspect-[4/3] rounded-2xl bg-slate-100 overflow-hidden relative border border-slate-200/60 mb-3 group/img focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600">
            @if ($boostedActive)
                <div class="absolute top-2.5 left-2.5 bg-amber-400 text-slate-950 text-[10px] font-black uppercase tracking-wider px-2 py-0.5 rounded-lg shadow-2xs flex items-center gap-1 z-10">
                    <svg class="w-3 h-3 text-slate-950 fill-current" viewBox="0 0 20 20" aria-hidden="true">
                        <path d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z"/>
                    </svg>
                    <span>Pilihan</span>
                </div>
            @endif

            <div class="absolute top-2.5 right-2.5 bg-slate-900/80 text-white text-[10px] font-bold px-2.5 py-0.5 rounded-full z-10 truncate max-w-[125px]">
                {{ $listing->category->name ?? 'Lainnya' }}
            </div>

            <img src="{{ $imgSrc }}"
                 alt="{{ $listing->title }}"
                 loading="lazy"
                 onerror="this.onerror=null; this.src='{{ asset('images/placeholder.svg') }}';"
                 class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
        </a>

        <h3 class="font-bold text-slate-900 text-sm line-clamp-2 leading-snug group-hover:text-blue-700 transition min-h-[2.5rem]"
            title="{{ $listing->title }}">
            <a href="{{ $detailUrl }}" class="focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600 rounded">
                {{ $listing->title }}
            </a>
        </h3>

        <div class="flex flex-wrap items-center justify-between gap-1.5 text-xs mt-2.5 pt-2.5 border-t border-slate-100">
            <div class="flex items-center gap-1.5 min-w-0 flex-wrap">
                <span class="inline-flex items-center gap-1 text-[10px] font-bold text-slate-800 bg-slate-100 px-2 py-0.5 rounded-md truncate max-w-[95px]"
                      title="{{ $rawCampus }}">
                    {{ $campusShort }}
                </span>
                <span class="inline-block text-[10px] font-semibold text-slate-700 bg-slate-50 border border-slate-200 px-1.5 py-0.5 rounded-md">
                    {{ $conditionLabel }}
                </span>
            </div>

            <div class="font-black text-blue-700 text-sm sm:text-base shrink-0">
                Rp {{ number_format($listing->price, 0, ',', '.') }}
            </div>
        </div>
    </div>

    <div class="grid grid-cols-2 gap-2 mt-3.5 pt-1">
        <a href="{{ route('chat.index', ['listing_id' => $listing->id]) }}"
           class="min-h-[44px] w-full py-2 px-2.5 rounded-xl sm:rounded-full border border-slate-300 hover:border-blue-600 hover:bg-blue-50 text-slate-800 hover:text-blue-700 font-bold text-xs text-center transition flex items-center justify-center gap-1.5 cursor-pointer shadow-2xs focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600">
            <svg class="w-3.5 h-3.5 text-slate-600 group-hover:text-blue-700 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
            </svg>
            <span>Chat</span>
        </a>

        <a href="{{ $detailUrl }}"
           class="min-h-[44px] w-full py-2 px-2.5 rounded-xl sm:rounded-full bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs text-center transition flex items-center justify-center gap-1 cursor-pointer shadow-2xs focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600">
            <span>Lihat Detail</span>
        </a>
    </div>
</article>
