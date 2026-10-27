<x-layouts.app>
    <x-slot:title>Daftar Lelang - Lungsur.in</x-slot:title>

    <div class="space-y-8">
        {{-- Header halaman --}}
        <section class="rounded-3xl border border-blue-100/70 bg-gradient-to-br from-blue-50 via-sky-50/60 to-white p-6 sm:p-9">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <div class="mb-3 inline-flex items-center gap-2 rounded-full border border-blue-100 bg-white px-3 py-1.5 text-xs font-bold text-blue-700">
                        <span class="h-2 w-2 rounded-full bg-blue-600"></span>
                        Papan Lelang Mahasiswa
                    </div>

                    <h1 class="text-2xl font-black tracking-tight text-slate-900 sm:text-3xl">
                        Lelang Perlengkapan Kos
                    </h1>

                    <p class="mt-2 max-w-2xl text-sm leading-relaxed text-slate-500">
                        Temukan barang pilihan dan ajukan penawaran sebelum waktu lelang berakhir.
                    </p>
                </div>

                <div class="rounded-2xl border border-blue-100 bg-white/90 px-4 py-3 text-sm text-slate-600 shadow-sm">
                    <span class="font-bold text-blue-700">
                        {{ $auctions->total() + ($spotlight ? 1 : 0) }}
                    </span>
                    sesi lelang
                </div>
            </div>
        </section>

        {{-- Daftar lelang --}}
        {{-- Spotlight Lelang Unggulan --}}

        @if ($spotlight)
            @php
                $spotlightListing = $spotlight->listing;
                $spotlightRemaining = $spotlight->timeRemainingSeconds();
            @endphp

            <section class="space-y-4">
                <div class="flex items-center justify-between gap-3">
                    <h2 class="flex items-center gap-2 text-xl font-black tracking-tight text-slate-900">
                        <span class="text-blue-600">✦</span>
                        Spotlight Lelang Unggulan
                    </h2>

                    <span class="shrink-0 rounded-lg bg-blue-50 px-3 py-1.5 text-xs font-bold text-blue-700">
                        Lelang Pilihan
                    </span>
                </div>

                <article class="grid overflow-hidden rounded-2xl border border-blue-100 bg-white shadow-sm lg:grid-cols-[0.9fr_1.1fr]">

                    {{-- Foto barang unggulan --}}
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

                    {{-- Informasi lelang unggulan --}}
                    <div class="flex flex-col justify-center p-5 sm:p-7">
                        <div class="mb-3 flex flex-wrap items-center gap-2">
                            <span class="rounded-md bg-blue-50 px-2 py-1 text-xs font-semibold text-blue-700">
                                {{ $spotlightListing?->category?->name ?? 'Perlengkapan Kos' }}
                            </span>

                            <span class="text-xs font-semibold text-emerald-600">
                                ● Sedang Berlangsung
                            </span>
                        </div>

                        <h3 class="text-xl font-black leading-snug text-slate-900 sm:text-2xl">
                            {{ $spotlightListing?->title ?? 'Barang lelang' }}
                        </h3>

                        <p class="mt-2 line-clamp-3 text-sm leading-relaxed text-slate-500">
                            {{ $spotlightListing?->description ?? 'Lihat detail barang dan ajukan penawaran selama lelang masih berlangsung.' }}
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
                                    {{ $spotlight->bids()->count() }} total penawaran
                                </p>
                            </div>

                            <div>
                                <p class="flex items-center gap-1 text-xs font-bold text-rose-600">
                                    <span>◷</span>
                                    Sisa waktu
                                </p>

                                <div
                                    data-auction-countdown
                                    data-remaining-seconds="{{ $spotlightRemaining }}"
                                    class="mt-1 text-lg font-black tabular-nums text-slate-900"
                                >
                                    <span data-countdown-text>
                                        Memuat waktu...
                                    </span>
                                </div>

                                <p class="mt-1 text-xs text-slate-400">
                                    Waktu lelang berjalan
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

        <section class="space-y-5">
            <div>
                <h2 class="text-xl font-black tracking-tight text-slate-900">
                    Daftar Lelang Lainnya
                </h2>

                <p class="mt-1 text-sm text-slate-500">
                    Temukan barang kebutuhan kos lainnya dan cek detail lelangnya.
                </p>
            </div>

        <div id="auction-list-content">
            <div class="grid grid-cols-2 gap-3 sm:gap-5 lg:grid-cols-4">
                @forelse ($auctions as $auction)
                    @php
                        $listing = $auction->listing;

                        $remaining = $auction->status === 'active'
                            ? $auction->timeRemainingSeconds()
                            : 0;

                        $isClosed = $auction->status !== 'active'
                            || $remaining <= 0
                            || ! $auction->end_time;
                    @endphp

                    <article class="group flex flex-col overflow-hidden rounded-2xl border border-slate-100 bg-white shadow-sm transition hover:-translate-y-0.5 hover:shadow-lg">

                        {{-- Foto barang --}}
                        <a
                            href="{{ route('auctions.show', $auction->id) }}"
                            class="relative block aspect-square overflow-hidden bg-slate-100 sm:aspect-[4/3]"
                        >
                            @if ($listing?->photo_url)
                                <img
                                    src="{{ $listing->photo_url }}"
                                    alt="{{ $listing->title }}"
                                    loading="lazy"
                                    class="h-full w-full object-cover transition duration-300 group-hover:scale-105"
                                >
                            @else
                                <div class="flex h-full w-full items-center justify-center text-sm text-slate-400">
                                    Belum ada foto
                                </div>
                            @endif

                            <span class="absolute left-3 top-3 rounded-full px-3 py-1 text-[11px] font-bold {{ $isClosed ? 'bg-slate-800/90 text-white' : 'bg-emerald-100 text-emerald-700' }}">
                                {{ $isClosed ? 'Lelang Ditutup' : 'Sedang Berlangsung' }}
                            </span>
                        </a>

                        {{-- Informasi barang --}}
                        <div class="flex flex-1 flex-col p-4">
                            <div class="mb-2 flex items-center justify-between gap-2">
                                <span class="max-w-[60%] truncate rounded-md bg-blue-50 px-2 py-1 text-[11px] font-semibold text-blue-700">
                                    {{ $listing?->category?->name ?? 'Perlengkapan Kos' }}
                                </span>

                                <span class="text-[11px] text-slate-400">
                                    {{ $auction->created_at?->diffForHumans() }}
                                </span>
                            </div>

                            <h3 class="line-clamp-2 font-bold leading-snug text-slate-900">
                                {{ $listing?->title ?? 'Barang lelang' }}
                            </h3>

                            {{-- Harga dan countdown --}}
                            <div class="mt-2 rounded-xl bg-slate-50 p-3">
                                <p class="text-xs text-slate-500">
                                    Harga tertinggi saat ini
                                </p>

                                <p class="mt-1 text-lg font-black text-blue-700">
                                    Rp {{ number_format($auction->currentPrice(), 0, ',', '.') }}
                                </p>

                                <div class="mt-3 flex items-center justify-between gap-2 border-t border-slate-200 pt-3">
                                    <span class="text-xs text-slate-500">
                                        {{ $auction->bids()->count() }} penawaran
                                    </span>

                                    @if (! $isClosed)
                                        <div
                                            data-auction-countdown
                                            data-remaining-seconds="{{ $remaining }}"
                                            class="text-right text-xs font-bold tabular-nums text-rose-600"
                                        >
                                            <span data-countdown-text>
                                                Memuat waktu...
                                            </span>
                                        </div>
                                    @else
                                        <span class="text-xs font-bold text-slate-500">
                                            Lelang Ditutup
                                        </span>
                                    @endif
                                </div>
                            </div>

                            {{-- Tombol detail --}}
                            <a
                                href="{{ route('auctions.show', $auction->id) }}"
                                class="mt-4 inline-flex w-full items-center justify-center gap-2 rounded-xl bg-blue-600 px-4 py-3 text-sm font-bold text-white transition hover:bg-blue-700"
                            >
                                Lihat Detail Lelang
                                <span aria-hidden="true">→</span>
                            </a>
                        </div>
                    </article>
                @empty
                    <div class="col-span-full rounded-2xl border border-dashed border-slate-300 bg-white px-6 py-14 text-center">
                        <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-2xl bg-blue-50 text-2xl">
                            ⚖️
                        </div>

                        <p class="mt-3 font-bold text-slate-900">
                            Belum ada lelang yang tersedia
                        </p>

                        <p class="mt-1 text-sm text-slate-500">
                            Lelang yang tersedia akan muncul di halaman ini.
                        </p>
                    </div>
                @endforelse
            </div>

            {{-- Pagination --}}
            
            {{-- Pagination --}}
            @if ($auctions->total() > 0)
                <div class="mt-10 flex flex-col items-center gap-3">

                    <div class="flex items-center justify-center gap-2">

                        {{-- Sebelumnya --}}
                        @if ($auctions->onFirstPage())
                            <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-slate-100 text-slate-300">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                                </svg>
                            </span>
                        @else
                            <a href="{{ $auctions->previousPageUrl() }}"
                            class="js-auction-pagination flex h-10 w-10 items-center justify-center rounded-xl bg-slate-100 text-slate-600 transition hover:bg-blue-100 hover:text-blue-600">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                                </svg>
                            </a>
                        @endif

                        {{-- Nomor halaman --}}
                        @foreach ($auctions->getUrlRange(1, $auctions->lastPage()) as $page => $url)
                            <a href="{{ $url }}"
                            class="js-auction-pagination flex h-10 w-10 items-center justify-center rounded-xl text-sm font-semibold transition {{ $auctions->currentPage() == $page ? 'bg-blue-600 text-white shadow-md shadow-blue-500/20' : 'bg-blue-50 text-blue-600 hover:bg-blue-100' }}">
                                {{ $page }}
                            </a>
                        @endforeach

                        {{-- Selanjutnya --}}
                        @if ($auctions->hasMorePages())
                            <a href="{{ $auctions->nextPageUrl() }}"
                            class="js-auction-pagination flex h-10 w-10 items-center justify-center rounded-xl bg-slate-100 text-slate-600 transition hover:bg-blue-100 hover:text-blue-600">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </a>
                        @else
                            <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-slate-100 text-slate-300">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </span>
                        @endif

                    </div>

                    {{-- Jumlah lelang --}}
                    <p class="text-xs text-slate-400 sm:text-sm">
                        Menampilkan
                        <span class="font-semibold text-slate-700">{{ $auctions->firstItem() }}–{{ $auctions->lastItem() }}</span>
                        dari
                        <span class="font-semibold text-slate-700">{{ $auctions->total() }}</span>
                        lelang
                    </p>

                </div>
            @endif


        </div>
        </section>
    </div>

    {{-- Countdown pada kartu lelang --}}
    
    <script>
    document.addEventListener('DOMContentLoaded', () => {
        // Countdown untuk kartu lelang yang sedang ditampilkan.
        function initializeCountdowns(root = document) {
            root.querySelectorAll('[data-auction-countdown]').forEach((timer) => {
                if (timer.dataset.countdownInitialized === 'true') return;

                const output = timer.querySelector('[data-countdown-text]');
                if (!output) return;

                timer.dataset.countdownInitialized = 'true';

                const initial = Math.max(
                    0,
                    Number(timer.dataset.remainingSeconds || 0)
                );

                const startedAt = performance.now();

                const render = () => {
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
                        timer.classList.remove('text-rose-600');
                        timer.classList.add('text-slate-500');
                        return false;
                    }

                    output.textContent = days > 0
                        ? `${days}h ${pad(hours)}:${pad(minutes)}:${pad(seconds)}`
                        : `${pad(hours)}:${pad(minutes)}:${pad(seconds)}`;

                    return true;
                };

                if (render()) {
                    const interval = window.setInterval(() => {
                        if (!render()) window.clearInterval(interval);
                    }, 1000);
                }
            });
        }

        initializeCountdowns();

        // Pagination responsif: laptop 20, HP 10.
        function syncAuctionPageSize() {
            const url = new URL(window.location.href);
            const target = window.matchMedia('(min-width: 1024px)').matches
                ? 20
                : 10;

            const current = Number(url.searchParams.get('per_page') || 20);

            if (current === target) return false;

            const page = Math.max(
                1,
                Number(url.searchParams.get('page') || 1)
            );

            const firstItem = (page - 1) * current;
            const targetPage = Math.floor(firstItem / target) + 1;

            url.searchParams.set('per_page', target);

            if (targetPage > 1) {
                url.searchParams.set('page', targetPage);
            } else {
                url.searchParams.delete('page');
            }

            window.location.replace(url.toString());
            return true;
        }

        if (syncAuctionPageSize()) return;

        // Muat ulang daftar kartu dan pagination saja.
        async function loadAuctionPage(url, updateHistory = true) {
            const container = document.getElementById('auction-list-content');

            if (!container) {
                window.location.href = url;
                return;
            }

            try {
                const response = await fetch(url, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                if (!response.ok) {
                    throw new Error('Gagal memuat daftar lelang.');
                }

                const html = await response.text();
                const parsed = new DOMParser().parseFromString(html, 'text/html');
                const newContainer = parsed.getElementById('auction-list-content');

                if (!newContainer) {
                    window.location.href = url;
                    return;
                }

                container.innerHTML = newContainer.innerHTML;

                if (updateHistory) {
                    window.history.pushState({}, '', url);
                }

                initializeCountdowns(container);

            } catch (error) {
                console.error(error);
                window.location.href = url;
            }
        }

        // Tangani klik pagination.
        document.addEventListener('click', (event) => {
            const link = event.target.closest('.js-auction-pagination');

            if (!link) return;

            if (
                event.ctrlKey ||
                event.metaKey ||
                event.shiftKey ||
                event.altKey ||
                link.target === '_blank'
            ) {
                return;
            }

            event.preventDefault();
            loadAuctionPage(link.href);
        });

        // Tetap dukung tombol Back dan Forward browser.
        window.addEventListener('popstate', () => {
            loadAuctionPage(window.location.href, false);
        });

        // Sesuaikan jumlah kartu ketika ukuran layar berubah.
        let resizeTimeout;

        window.addEventListener('resize', () => {
            window.clearTimeout(resizeTimeout);

            resizeTimeout = window.setTimeout(() => {
                syncAuctionPageSize();
            }, 250);
        });
    });
    </script>
</x-layouts.app>