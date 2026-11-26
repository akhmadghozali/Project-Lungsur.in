<x-layouts.app>
    <x-slot name="title">Riwayat Transaksi - Lungsur.in</x-slot>

    <div class="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8 py-5 sm:py-8">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6 sm:mb-8 pb-5 sm:pb-6 border-b border-slate-100">
            <div>
                <div class="flex items-center gap-2 text-xs font-semibold text-slate-600 mb-1">
                    <a href="{{ route('dashboard') }}" class="hover:text-blue-600 transition">Dashboard</a>
                    <span>/</span>
                    <span class="text-slate-900 font-bold">Riwayat Transaksi</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">
                    Riwayat Transaksi
                </h1>
                <p class="text-xs sm:text-sm text-slate-600 mt-1">
                    Daftar barang yang pernah kamu beli dan menangkan melalui lelang di platform Lungsur.in.
                </p>
            </div>
            <a href="{{ route('dashboard') }}" 
               class="min-h-[44px] inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold bg-slate-100 hover:bg-slate-200 text-slate-800 transition self-start sm:self-auto focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                <span>Kembali ke Dashboard</span>
            </a>
        </div>

        <section class="mb-10 sm:mb-12">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-xl bg-emerald-50 text-emerald-700 flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                    </div>
                    <h2 class="text-base sm:text-xl font-bold text-slate-900">
                        Barang yang Dibeli (Deal COD)
                    </h2>
                </div>
                <span class="text-xs font-bold px-2.5 py-1 rounded-full bg-slate-100 text-slate-700">
                    {{ $purchasedListings->count() }} Transaksi
                </span>
            </div>

            @forelse ($purchasedListings as $listing)
                <div class="bg-white rounded-2xl border border-slate-200/90 shadow-2xs hover:shadow-md transition p-4 sm:p-5 mb-4 flex flex-col md:flex-row md:items-center justify-between gap-4 sm:gap-5">
                    
                    <div class="flex items-center gap-3.5 sm:gap-5 min-w-0 flex-1">
                        @if ($listing->photo_url)
                            <img src="{{ $listing->photo_url }}"
                                 alt="{{ $listing->title }}"
                                 class="w-20 h-20 sm:w-24 sm:h-24 object-cover rounded-2xl border border-slate-100 shrink-0 shadow-2xs">
                        @else
                            <div class="w-20 h-20 sm:w-24 sm:h-24 bg-slate-100 rounded-2xl flex items-center justify-center text-slate-500 shrink-0">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            </div>
                        @endif

                        <div class="min-w-0 space-y-1.5 flex-1">
                            <div class="flex items-center gap-2 flex-wrap">
                                <span class="inline-flex items-center gap-1 text-[10px] font-extrabold uppercase px-2.5 py-0.5 rounded-full bg-emerald-50 text-emerald-800 border border-emerald-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>
                                    Terjual ke Kamu
                                </span>
                                @if($listing->category)
                                    <span class="text-xs text-slate-600 font-medium">
                                        &bull; {{ $listing->category->name }}
                                    </span>
                                @endif
                                <span class="text-xs text-slate-600 font-medium">
                                    &bull; {{ $listing->updated_at?->translatedFormat('d M Y') ?? 'Baru saja' }}
                                </span>
                            </div>

                            <h3 class="font-bold text-slate-900 text-base sm:text-lg truncate">
                                <a href="{{ route('listings.show', $listing->id) }}" class="hover:text-blue-600 transition">
                                    {{ $listing->title }}
                                </a>
                            </h3>

                            <div class="flex items-center gap-3 text-xs text-slate-600 flex-wrap">
                                <span class="flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5 text-slate-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                    <span>Penjual: <strong class="text-slate-900 font-semibold">{{ $listing->user->name ?? '-' }}</strong></span>
                                </span>
                                @if($listing->user?->campus)
                                    <span class="text-slate-400 hidden sm:inline">|</span>
                                    <span class="text-slate-600 truncate">{{ $listing->user->campus }}</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="flex sm:flex-row md:flex-col items-center sm:items-center md:items-end justify-between md:justify-center gap-3 shrink-0 pt-3 md:pt-0 border-t md:border-t-0 md:border-l border-slate-100 md:pl-6 text-left md:text-right">
                        <div>
                            <span class="text-[11px] text-slate-600 font-medium block">Total Pembelian</span>
                            <span class="text-lg sm:text-2xl font-black text-blue-700">
                                Rp {{ number_format($listing->price, 0, ',', '.') }}
                            </span>
                        </div>

                        <div class="flex items-center gap-2">
                            <a href="{{ route('listings.show', $listing->id) }}" 
                               class="min-h-[44px] inline-flex items-center justify-center gap-1 px-3.5 py-2 rounded-xl text-xs font-bold bg-slate-100 hover:bg-slate-200 text-slate-800 transition focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600">
                                <span>Lihat Detail</span>
                            </a>
                            <a href="{{ route('chat.index', ['listing_id' => $listing->id]) }}" 
                               class="min-h-[44px] inline-flex items-center justify-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-bold bg-blue-600 hover:bg-blue-700 text-white shadow-xs transition focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600">
                                <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                                <span>Chat Penjual</span>
                            </a>
                        </div>
                    </div>

                </div>
            @empty
                <div class="bg-white border border-dashed border-slate-200 rounded-3xl p-8 sm:p-10 text-center space-y-2">
                    <div class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-500 flex items-center justify-center mx-auto">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                    </div>
                    <p class="font-bold text-slate-900 text-sm">Belum ada riwayat pembelian</p>
                    <p class="text-xs text-slate-600 max-w-sm mx-auto">Ketika kamu bertransaksi COD dengan penjual dan penjual menandai barang terjual ke kamu, riwayat transaksi akan muncul di sini.</p>
                </div>
            @endforelse
        </section>

        <section>
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    </div>
                    <h2 class="text-base sm:text-xl font-bold text-slate-900">
                        Lelang yang Dimenangkan
                    </h2>
                </div>
                <span class="text-xs font-bold px-2.5 py-1 rounded-full bg-slate-100 text-slate-700">
                    {{ $wonAuctions->count() }} Lelang
                </span>
            </div>

            @forelse ($wonAuctions as $auction)
                <div class="bg-white rounded-2xl border border-slate-200/90 shadow-2xs hover:shadow-md transition p-4 sm:p-5 mb-4 flex flex-col md:flex-row md:items-center justify-between gap-4 sm:gap-5">
                    
                    <div class="flex items-center gap-3.5 sm:gap-5 min-w-0 flex-1">
                        @if ($auction->listing?->photo_url)
                            <img src="{{ $auction->listing->photo_url }}"
                                 alt="{{ $auction->listing->title }}"
                                 class="w-20 h-20 sm:w-24 sm:h-24 object-cover rounded-2xl border border-slate-100 shrink-0 shadow-2xs">
                        @else
                            <div class="w-20 h-20 sm:w-24 sm:h-24 bg-slate-100 rounded-2xl flex items-center justify-center text-slate-500 shrink-0">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            </div>
                        @endif

                        <div class="min-w-0 space-y-1.5 flex-1">
                            <div class="flex items-center gap-2 flex-wrap">
                                <span class="inline-flex items-center gap-1 text-[10px] font-extrabold uppercase px-2.5 py-0.5 rounded-full bg-blue-50 text-blue-700 border border-blue-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-blue-600"></span>
                                    Pemenang Lelang
                                </span>
                                @if($auction->listing?->category)
                                    <span class="text-xs text-slate-600 font-medium">
                                        &bull; {{ $auction->listing->category->name }}
                                    </span>
                                @endif
                                <span class="text-xs text-slate-600 font-medium">
                                    &bull; Selesai {{ $auction->end_time?->translatedFormat('d M Y') ?? 'Baru saja' }}
                                </span>
                            </div>

                            <h3 class="font-bold text-slate-900 text-base sm:text-lg truncate">
                                <a href="{{ route('auctions.show', $auction->id) }}" class="hover:text-blue-600 transition">
                                    {{ $auction->listing->title ?? 'Barang Lelang' }}
                                </a>
                            </h3>

                            <div class="flex items-center gap-3 text-xs text-slate-600 flex-wrap">
                                <span class="flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5 text-slate-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                    <span>Penjual: <strong class="text-slate-900 font-semibold">{{ $auction->listing?->user?->name ?? '-' }}</strong></span>
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="flex sm:flex-row md:flex-col items-center sm:items-center md:items-end justify-between md:justify-center gap-3 shrink-0 pt-3 md:pt-0 border-t md:border-t-0 md:border-l border-slate-100 md:pl-6 text-left md:text-right">
                        <div>
                            <span class="text-[11px] text-slate-600 font-medium block">Tawaran Menang</span>
                            <span class="text-lg sm:text-2xl font-black text-emerald-700">
                                Rp {{ number_format($auction->highestBid->bid_amount ?? $auction->starting_price, 0, ',', '.') }}
                            </span>
                        </div>

                        <div class="flex items-center gap-2">
                            <a href="{{ route('auctions.show', $auction->id) }}" 
                               class="min-h-[44px] inline-flex items-center justify-center gap-1 px-3.5 py-2 rounded-xl text-xs font-bold bg-slate-100 hover:bg-slate-200 text-slate-800 transition focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600">
                                <span>Detail Lelang</span>
                            </a>
                            @if($auction->listing_id)
                                <a href="{{ route('chat.index', ['listing_id' => $auction->listing_id]) }}" 
                                   class="min-h-[44px] inline-flex items-center justify-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-bold bg-blue-600 hover:bg-blue-700 text-white shadow-xs transition focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600">
                                    <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                                    <span>Chat Penjual</span>
                                </a>
                            @endif
                        </div>
                    </div>

                </div>
            @empty
                <div class="bg-white border border-dashed border-slate-200 rounded-3xl p-8 sm:p-10 text-center space-y-2">
                    <div class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-500 flex items-center justify-center mx-auto">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    </div>
                    <p class="font-bold text-slate-900 text-sm">Belum ada lelang yang dimenangkan</p>
                    <p class="text-xs text-slate-600 max-w-sm mx-auto">Ikuti lelang terbuka di marketplace dan pasang tawaran terbaikmu untuk memenangkan barang kos.</p>
                </div>
            @endforelse
        </section>
    </div>
</x-layouts.app>
