@props(['listing'])

@php
    $isBoosted = $listing->is_boosted && ($listing->boosted_until === null || $listing->boosted_until->isFuture());

    $diffText = $listing->created_at ? $listing->created_at->locale('id')->diffForHumans() : '';
    $diffFormatted = str_ireplace([' yang lalu', 'yang lalu'], ' lalu', $diffText);

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
        default => $rawCampus,
    };
@endphp

<div class="bg-white rounded-2xl border border-slate-200/80 shadow-2xs hover:shadow-md transition overflow-hidden flex flex-col group relative">
    <div class="aspect-[4/3] bg-slate-100 overflow-hidden relative group/img"
         @if ($listing->images->count() > 1)
             x-data="{ current: 0, total: {{ $listing->images->count() }} }"
         @endif
    >
        @if ($isBoosted)
            <div class="absolute top-2.5 left-2.5 bg-amber-400 text-slate-950 text-[10px] font-black uppercase tracking-wider px-2 py-0.5 rounded-lg shadow-xs flex items-center gap-1 z-10">
                <svg class="w-3 h-3 text-slate-950 fill-current" viewBox="0 0 20 20" aria-hidden="true">
                    <path d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z"/>
                </svg>
                <span>BOOST</span>
            </div>
        @endif

        @if ($listing->images->count() > 0)
            @foreach ($listing->images as $imgIdx => $img)
                @php
                    $imgSrc = \Illuminate\Support\Str::startsWith($img->image_url, ['http://', 'https://'])
                        ? $img->image_url
                        : asset('storage/' . ltrim($img->image_url, '/'));
                @endphp
                <div
                    @if ($listing->images->count() > 1)
                        x-show="current === {{ $imgIdx }}"
                        x-transition.opacity.duration.200ms
                    @endif
                    class="w-full h-full absolute inset-0"
                >
                    <img src="{{ $imgSrc }}"
                         alt="{{ $listing->title }}"
                         loading="lazy"
                         onerror="this.onerror=null; this.src='{{ asset('images/placeholder.svg') }}';"
                         class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                </div>
            @endforeach

            @if ($listing->images->count() > 1)
                <button
                    type="button"
                    @click.stop.prevent="current = (current === 0) ? total - 1 : current - 1"
                    class="absolute left-1.5 sm:left-2 top-1/2 -translate-y-1/2 w-8 h-8 sm:w-7 sm:h-7 rounded-full bg-slate-900/75 hover:bg-slate-900 text-white flex items-center justify-center transition opacity-100 sm:opacity-0 sm:group-hover/img:opacity-100 z-10 shadow-xs cursor-pointer focus-visible:opacity-100 focus-visible:outline-2 focus-visible:outline-white"
                    aria-label="Foto sebelumnya"
                >
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
                </button>

                <button
                    type="button"
                    @click.stop.prevent="current = (current === total - 1) ? 0 : current + 1"
                    class="absolute right-1.5 sm:right-2 top-1/2 -translate-y-1/2 w-8 h-8 sm:w-7 sm:h-7 rounded-full bg-slate-900/75 hover:bg-slate-900 text-white flex items-center justify-center transition opacity-100 sm:opacity-0 sm:group-hover/img:opacity-100 z-10 shadow-xs cursor-pointer focus-visible:opacity-100 focus-visible:outline-2 focus-visible:outline-white"
                    aria-label="Foto berikutnya"
                >
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                </button>

                <div class="absolute bottom-2 left-0 right-0 flex items-center justify-center gap-1 z-10 pointer-events-none">
                    @for ($d = 0; $d < $listing->images->count(); $d++)
                        <span class="h-1 rounded-full transition-all duration-200" :class="current === {{ $d }} ? 'w-3.5 bg-white' : 'w-1 bg-white/60'"></span>
                    @endfor
                </div>

                <div class="absolute top-2.5 right-2.5 bg-slate-900/80 text-white text-[10px] font-bold px-1.5 py-0.5 rounded-md z-10">
                    <span x-text="(current + 1) + '/' + total"></span>
                </div>
            @endif
        @else
            <div class="w-full h-full flex items-center justify-center text-[11px] font-medium text-slate-600">
                <img src="{{ asset('images/placeholder.svg') }}" alt="Placeholder" class="w-full h-full object-cover">
            </div>
        @endif
    </div>

    <div class="p-3 sm:p-4 flex-1 flex flex-col justify-between space-y-2.5 sm:space-y-3">
        <div>
            <div class="flex items-center justify-between gap-1 text-[11px] mb-1.5 sm:mb-2">
                <span class="font-semibold text-blue-700 bg-blue-50 px-2 py-0.5 rounded-md truncate max-w-[100px] sm:max-w-[115px]">
                    {{ $listing->category->name ?? 'Lainnya' }}
                </span>
                <span class="text-slate-600 text-[10px] sm:text-[11px] shrink-0">
                    {{ $diffFormatted }}
                </span>
            </div>

            <div class="flex items-center gap-1 sm:gap-1.5 flex-wrap mb-1.5">
                @if (!empty($listing->condition_checklist['kondisi']))
                    <span class="inline-block text-[10px] font-bold px-1.5 sm:px-2 py-0.5 rounded-md {{ $listing->condition_checklist['kondisi'] === 'baru' ? 'bg-emerald-50 text-emerald-800' : 'bg-slate-100 text-slate-700' }}">
                        {{ ucfirst(str_replace('_', ' ', $listing->condition_checklist['kondisi'])) }}
                    </span>
                @endif

                @if (!empty($campusShort))
                    <span class="inline-flex items-center gap-1 text-[10px] font-semibold text-slate-700 bg-slate-50 border border-slate-200/80 px-1.5 py-0.5 rounded-md truncate max-w-[115px] sm:max-w-[130px]" title="{{ $rawCampus }}">
                        <svg class="w-2.5 h-2.5 text-blue-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                        <span class="truncate">{{ $campusShort }}</span>
                    </span>
                @endif
            </div>

            <h3 class="font-bold text-xs sm:text-sm text-slate-900 line-clamp-2 leading-snug group-hover:text-blue-700 transition">
                {{ $listing->title }}
            </h3>

            <div class="mt-1.5 sm:mt-2 flex items-baseline gap-2">
                <span class="text-sm sm:text-lg font-black text-blue-700 tracking-tight">
                    Rp {{ number_format($listing->price, 0, ',', '.') }}
                </span>
            </div>
        </div>

        <a href="{{ Route::has('listings.show') ? route('listings.show', $listing->id) : url('/katalog/'.$listing->id) }}"
           class="min-h-[44px] w-full text-center px-3 py-2.5 rounded-xl bg-blue-50 hover:bg-blue-600 text-blue-700 hover:text-white font-bold text-xs transition flex items-center justify-center gap-1.5 shadow-2xs focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600">
            <span>Lihat Detail</span>
            <span aria-hidden="true">&rarr;</span>
        </a>
    </div>
</div>
