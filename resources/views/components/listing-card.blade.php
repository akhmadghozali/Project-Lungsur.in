@props(['listing'])

@php
    $isBoosted = $listing->is_boosted && ($listing->boosted_until === null || $listing->boosted_until->isFuture());
@endphp

<div class="bg-white rounded-2xl border border-slate-100 shadow-2xs hover:shadow-md transition overflow-hidden flex flex-col group relative">
    <!-- Foto Barang - Slider / Carousel -->
    <div class="aspect-[4/3] bg-slate-100 overflow-hidden relative group/img"
         @if ($listing->images->count() > 1)
             x-data="{ current: 0, total: {{ $listing->images->count() }} }"
         @endif
    >
        @if ($isBoosted)
            <div class="absolute top-2.5 left-2.5 bg-amber-500/95 backdrop-blur-xs text-white text-[10px] font-black uppercase tracking-wider px-2 py-0.5 rounded-lg shadow-sm flex items-center gap-1 z-10">
                <svg class="w-3 h-3 text-white fill-current" viewBox="0 0 20 20">
                    <path d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z"/>
                </svg>
                <span>Unggulan</span>
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
                         class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                </div>
            @endforeach

            @if ($listing->images->count() > 1)
                {{-- Tombol Navigasi Kiri --}}
                <button
                    type="button"
                    @click.stop.prevent="current = (current === 0) ? total - 1 : current - 1"
                    class="absolute left-2 top-1/2 -translate-y-1/2 w-7 h-7 rounded-full bg-slate-900/60 hover:bg-slate-900/90 text-white flex items-center justify-center backdrop-blur-xs transition opacity-0 group-hover/img:opacity-100 z-10 shadow-sm cursor-pointer"
                    aria-label="Foto sebelumnya"
                >
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
                </button>

                {{-- Tombol Navigasi Kanan --}}
                <button
                    type="button"
                    @click.stop.prevent="current = (current === total - 1) ? 0 : current + 1"
                    class="absolute right-2 top-1/2 -translate-y-1/2 w-7 h-7 rounded-full bg-slate-900/60 hover:bg-slate-900/90 text-white flex items-center justify-center backdrop-blur-xs transition opacity-0 group-hover/img:opacity-100 z-10 shadow-sm cursor-pointer"
                    aria-label="Foto berikutnya"
                >
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                </button>

                {{-- Titik Indikator Dots --}}
                <div class="absolute bottom-2 left-0 right-0 flex items-center justify-center gap-1 z-10 pointer-events-none">
                    @for ($d = 0; $d < $listing->images->count(); $d++)
                        <span class="h-1 rounded-full transition-all duration-200" :class="current === {{ $d }} ? 'w-3.5 bg-white' : 'w-1 bg-white/60'"></span>
                    @endfor
                </div>

                {{-- Badge Counter --}}
                <div class="absolute top-2.5 right-2.5 bg-slate-900/70 backdrop-blur-xs text-white text-[10px] font-bold px-1.5 py-0.5 rounded-md z-10">
                    <span x-text="(current + 1) + '/' + total"></span>
                </div>
            @endif
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

            <div class="flex items-center gap-1.5 flex-wrap mb-1.5">
                @if (!empty($listing->condition_checklist['kondisi']))
                    <span class="inline-block text-[10px] font-bold px-2 py-0.5 rounded-md {{ $listing->condition_checklist['kondisi'] === 'baru' ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-600' }}">
                        {{ ucfirst(str_replace('_', ' ', $listing->condition_checklist['kondisi'])) }}
                    </span>
                @endif

                @if (!empty($listing->user?->campus))
                    <span class="inline-flex items-center gap-1 text-[10px] font-semibold text-slate-600 bg-slate-50 border border-slate-200/60 px-1.5 py-0.5 rounded-md truncate max-w-[140px]" title="{{ $listing->user->campus }}">
                        <svg class="w-2.5 h-2.5 text-blue-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                        <span class="truncate">{{ $listing->user->campus }}</span>
                    </span>
                @endif
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

