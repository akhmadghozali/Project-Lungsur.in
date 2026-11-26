<x-layouts.app>
    <x-slot:title>Daftar Lelang - Lungsur.in</x-slot:title>

    <div class="space-y-6 sm:space-y-8">
        <section class="rounded-3xl border border-blue-100/70 bg-gradient-to-br from-blue-50 via-sky-50/60 to-white p-5 sm:p-9">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <div class="mb-2.5 sm:mb-3 inline-flex items-center gap-2 rounded-full border border-blue-200 bg-white px-3 py-1.5 text-xs font-bold text-blue-700">
                        <span class="h-2 w-2 rounded-full bg-blue-600"></span>
                        <span>Papan Lelang Mahasiswa</span>
                    </div>

                    <h1 class="text-2xl font-black tracking-tight text-slate-900 sm:text-3xl">
                        Lelang Perlengkapan Kos
                    </h1>

                    <p class="mt-1.5 sm:mt-2 max-w-2xl text-xs sm:text-sm leading-relaxed text-slate-600">
                        Temukan barang pilihan mahasiswa Malang dan ajukan penawaran sebelum waktu lelang berakhir.
                    </p>
                </div>

                <div class="flex flex-wrap items-center gap-2.5 sm:gap-3">
                    <div class="min-h-[44px] inline-flex items-center gap-1.5 rounded-2xl border border-blue-200 bg-white/95 px-4 py-2.5 text-xs sm:text-sm text-slate-700 shadow-2xs">
                        <span class="font-extrabold text-blue-700">
                            {{ $auctions->total() + ($spotlight ? 1 : 0) }}
                        </span>
                        <span>sesi lelang</span>
                    </div>

                    @if (auth()->check() && !auth()->user()->isSeller())
                        <form method="POST" action="{{ route('user.switch_mode') }}" class="flex-1 sm:flex-initial">
                            @csrf
                            <input type="hidden" name="mode" value="seller">
                            <button type="submit"
                                    class="w-full sm:w-auto min-h-[44px] inline-flex items-center justify-center gap-2 rounded-2xl bg-emerald-700 hover:bg-emerald-800 px-4 sm:px-5 py-2.5 text-xs sm:text-sm font-bold text-white shadow-md shadow-emerald-700/20 transition cursor-pointer focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-emerald-700">
                                <span>Mode Penjual untuk Buka Lelang</span>
                            </button>
                        </form>
                    @else
                        <a href="{{ route('auctions.create') }}"
                           class="flex-1 sm:flex-initial min-h-[44px] inline-flex items-center justify-center gap-2 rounded-2xl bg-emerald-700 hover:bg-emerald-800 px-4 sm:px-5 py-2.5 text-xs sm:text-sm font-bold text-white shadow-md shadow-emerald-700/20 transition focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-emerald-700">
                            <span>+ Buka Lelang (Rp 5.000)</span>
                        </a>
                    @endif
                </div>
            </div>
        </section>

        @if ($spotlight)
            @php
                $spotlightListing = $spotlight->listing;
                $spotlightRemaining = $spotlight->timeRemainingSeconds();
            @endphp

            <section class="space-y-3.5 sm:space-y-4">
                <div class="flex items-center justify-between gap-3">
                    <h2 class="flex items-center gap-2 text-lg sm:text-xl font-black tracking-tight text-slate-900">
                        <span class="text-blue-600" aria-hidden="true">✦</span>
                        <span>Spotlight Lelang Unggulan</span>
                    </h2>

                    <span class="shrink-0 rounded-lg bg-blue-50 border border-blue-200 px-3 py-1 text-xs font-bold text-blue-700">
                        Lelang Pilihan
                    </span>
                </div>

                <article class="grid overflow-hidden rounded-2xl border border-blue-200/80 bg-white shadow-sm lg:grid-cols-[0.9fr_1.1fr]">

                    <a
                        href="{{ route('auctions.show', $spotlight->id) }}"
                        class="group relative block aspect-[4/3] overflow-hidden bg-slate-100 lg:aspect-auto lg:min-h-[320px] focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600"
                    >
                        @if ($spotlightListing?->photo_url)
                            <img
                                src="{{ $spotlightListing->photo_url }}"
                                alt="{{ $spotlightListing->title }}"
                                loading="lazy"
                                class="h-full w-full object-cover transition duration-300 group-hover:scale-105"
                            >
                        @else
                            <div class="flex h-full min-h-56 items-center justify-center text-sm font-medium text-slate-500">
                                Belum ada foto
                            </div>
                        @endif

                        <span class="absolute left-3 top-3 rounded-lg bg-blue-600 px-3 py-1.5 text-xs font-bold text-white shadow">
                            ✦ SPOTLIGHT
                        </span>

                        <span class="absolute bottom-3 left-3 rounded-lg bg-white/95 border border-slate-200 px-3 py-1.5 text-xs font-bold text-slate-800 shadow-2xs">
                            {{ (int) ($spotlight->unique_bidders_count ?? 0) }} penawar berbeda
                        </span>
                    </a>

                    <div class="flex flex-col justify-center p-4 sm:p-7">
                        <div class="mb-2.5 sm:mb-3 flex flex-wrap items-center gap-2">
                            <span class="rounded-md bg-blue-50 border border-blue-100 px-2.5 py-1 text-xs font-bold text-blue-700">
                                {{ $spotlightListing?->category?->name ?? 'Perlengkapan Kos' }}
                            </span>

                            <span class="text-xs font-bold text-emerald-700">
                                ● Sedang Berlangsung
                            </span>
                        </div>

                        <h3 class="text-lg sm:text-2xl font-black leading-snug text-slate-900">
                            <a href="{{ route('auctions.show', $spotlight->id) }}" class="hover:text-blue-600 transition focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600">
                                {{ $spotlightListing?->title ?? 'Barang lelang' }}
                            </a>
                        </h3>

                        <p class="mt-2 line-clamp-2 sm:line-clamp-3 text-xs sm:text-sm leading-relaxed text-slate-600">
                            {{ $spotlightListing?->description ?? 'Lihat detail barang dan ajukan penawaran selama lelang masih berlangsung.' }}
                        </p>

                        <div class="my-4 sm:my-5 grid grid-cols-2 gap-3 border-y border-slate-100 py-3.5 sm:py-4">
                            <div>
                                <p class="text-xs font-semibold text-slate-600">
                                    Harga tertinggi
                                </p>

                                <p class="mt-1 text-lg sm:text-xl font-black text-blue-700">
                                    Rp {{ number_format($spotlight->currentPrice(), 0, ',', '.') }}
                                </p>

                                <p class="mt-1 text-xs font-medium text-slate-600">
                                    {{ $spotlight->bids()->count() }} total penawaran
                                </p>
                            </div>

                            <div>
                                <p class="flex items-center gap-1 text-xs font-bold text-rose-600">
                                    <span aria-hidden="true">◷</span>
                                    <span>Sisa waktu</span>
                                </p>

                                <div
                                    data-auction-countdown
                                    data-remaining-seconds="{{ $spotlightRemaining }}"
                                    class="mt-1 text-base sm:text-lg font-black tabular-nums text-slate-900"
                                >
                                    <span data-countdown-text>
                                        Memuat waktu...
                                    </span>
                                </div>

                                <p class="mt-1 text-xs font-medium text-slate-600">
                                    Waktu lelang berjalan
                                </p>
                            </div>
                        </div>

                        <a
                            href="{{ route('auctions.show', $spotlight->id) }}"
                            class="min-h-[44px] inline-flex w-full items-center justify-center gap-2 rounded-xl bg-blue-600 px-4 py-3 text-xs sm:text-sm font-bold text-white transition hover:bg-blue-700 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600"
                        >
                            <span>Lihat &amp; Ikut Lelang</span>
                            <span aria-hidden="true">&rarr;</span>
                        </a>
                    </div>
                </article>
            </section>
        @endif

        <section class="space-y-4 sm:space-y-5">
            <div>
                <h2 class="text-lg sm:text-xl font-black tracking-tight text-slate-900">
                    Daftar Lelang Lainnya
                </h2>

                <p class="mt-1 text-xs sm:text-sm text-slate-600">
                    Temukan barang kebutuhan kos lainnya dan cek detail lelangnya.
                </p>
            </div>

            <div id="auction-list-content">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5">
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

                        <article class="group flex flex-col overflow-hidden rounded-2xl border border-slate-200/80 bg-white shadow-xs transition hover:-translate-y-0.5 hover:shadow-md">

                            <a
                                href="{{ route('auctions.show', $auction->id) }}"
                                class="relative block aspect-[16/10] sm:aspect-[4/3] overflow-hidden bg-slate-100 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600"
                            >
                                @if ($listing?->photo_url)
                                    <img
                                        src="{{ $listing->photo_url }}"
                                        alt="{{ $listing->title }}"
                                        loading="lazy"
                                        class="h-full w-full object-cover transition duration-300 group-hover:scale-105"
                                    >
                                @else
                                    <div class="flex h-full w-full items-center justify-center text-sm font-medium text-slate-500">
                                        Belum ada foto
                                    </div>
                                @endif

                                <span class="absolute left-3 top-3 rounded-full px-3 py-1 text-[11px] font-bold {{ $isClosed ? 'bg-slate-800/95 text-white' : 'bg-emerald-50 text-emerald-800 border border-emerald-200 shadow-2xs' }}">
                                    {{ $isClosed ? 'Lelang Ditutup' : 'Sedang Berlangsung' }}
                                </span>
                            </a>

                            <div class="flex flex-1 flex-col p-4">
                                <div class="mb-2 flex items-center justify-between gap-2">
                                    <span class="max-w-[65%] truncate rounded-md bg-blue-50 border border-blue-100 px-2 py-0.5 text-[11px] font-bold text-blue-700">
                                        {{ $listing?->category?->name ?? 'Perlengkapan Kos' }}
                                    </span>

                                    <span class="text-[11px] font-medium text-slate-600">
                                        {{ $auction->created_at?->diffForHumans() }}
                                    </span>
                                </div>

                                <h3 class="line-clamp-2 text-sm sm:text-base font-bold leading-snug text-slate-900">
                                    <a href="{{ route('auctions.show', $auction->id) }}" class="hover:text-blue-600 transition focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600">
                                        {{ $listing?->title ?? 'Barang lelang' }}
                                    </a>
                                </h3>

                                <div class="mt-3 rounded-xl bg-slate-50 border border-slate-100 p-3">
                                    <p class="text-xs font-medium text-slate-600">
                                        Harga tertinggi saat ini
                                    </p>

                                    <p class="mt-1 text-lg font-black text-blue-700">
                                        Rp {{ number_format($auction->currentPrice(), 0, ',', '.') }}
                                    </p>

                                    <div class="mt-2.5 flex items-center justify-between gap-2 border-t border-slate-200/80 pt-2.5">
                                        <span class="text-xs font-medium text-slate-600">
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
                                            <span class="text-xs font-bold text-slate-600">
                                                Lelang Ditutup
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                <a
                                    href="{{ route('auctions.show', $auction->id) }}"
                                    class="mt-3.5 min-h-[44px] inline-flex w-full items-center justify-center gap-2 rounded-xl bg-blue-600 px-4 py-2.5 text-xs sm:text-sm font-bold text-white transition hover:bg-blue-700 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600"
                                >
                                    <span>Lihat Detail Lelang</span>
                                    <span aria-hidden="true">&rarr;</span>
                                </a>
                            </div>
                        </article>
                    @empty
                        <div class="col-span-full rounded-2xl border border-dashed border-slate-300 bg-white px-6 py-12 text-center">
                            <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-2xl bg-blue-50 text-blue-600">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>

                            <p class="mt-3 text-sm font-bold text-slate-900">
                                Belum ada sesi lelang yang tersedia
                            </p>

                            <p class="mt-1 text-xs sm:text-sm text-slate-600">
                                Sesi lelang mahasiswa yang aktif akan muncul di halaman ini.
                            </p>
                        </div>
                    @endforelse
                </div>

                @if ($auctions->total() > 0)
                    <nav aria-label="Navigasi halaman lelang" class="mt-8 sm:mt-10 flex flex-col items-center gap-3">
                        <div class="flex flex-wrap items-center justify-center gap-2">
                            @if ($auctions->onFirstPage())
                                <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-slate-100 text-slate-400">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                                    </svg>
                                </span>
                            @else
                                <a href="{{ $auctions->previousPageUrl() }}"
                                   aria-label="Halaman lelang sebelumnya"
                                   class="js-auction-pagination flex h-11 w-11 items-center justify-center rounded-xl bg-slate-100 text-slate-700 transition hover:bg-blue-100 hover:text-blue-700 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                                    </svg>
                                </a>
                            @endif

                            @foreach ($auctions->getUrlRange(1, $auctions->lastPage()) as $page => $url)
                                <a href="{{ $url }}"
                                   aria-label="Halaman {{ $page }}"
                                   {{ $auctions->currentPage() == $page ? 'aria-current=page' : '' }}
                                   class="js-auction-pagination flex h-11 w-11 items-center justify-center rounded-xl text-sm font-bold transition focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600 {{ $auctions->currentPage() == $page ? 'bg-blue-600 text-white shadow-md shadow-blue-500/20' : 'bg-blue-50 text-blue-700 hover:bg-blue-100' }}">
                                    {{ $page }}
                                </a>
                            @endforeach

                            @if ($auctions->hasMorePages())
                                <a href="{{ $auctions->nextPageUrl() }}"
                                   aria-label="Halaman lelang berikutnya"
                                   class="js-auction-pagination flex h-11 w-11 items-center justify-center rounded-xl bg-slate-100 text-slate-700 transition hover:bg-blue-100 hover:text-blue-700 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>
                                </a>
                            @else
                                <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-slate-100 text-slate-400">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>
                                </span>
                            @endif
                        </div>

                        <p class="text-xs text-slate-600 sm:text-sm">
                            Menampilkan
                            <span class="font-bold text-slate-900">{{ $auctions->firstItem() }} sampai {{ $auctions->lastItem() }}</span>
                            dari
                            <span class="font-bold text-slate-900">{{ $auctions->total() }}</span>
                            lelang
                        </p>
                    </nav>
                @endif
            </div>
        </section>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', () => {
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
                        timer.classList.add('text-slate-600');
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

        window.addEventListener('popstate', () => {
            loadAuctionPage(window.location.href, false);
        });

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
