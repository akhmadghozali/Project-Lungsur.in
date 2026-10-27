<x-layouts.app>
    <x-slot:title>
        {{ $auction->listing?->title ?? 'Detail Lelang' }} - Lungsur.in
    </x-slot:title>

    @php
        $listing = $auction->listing;
        $highestBid = $auction->highestBid;

        $isExpired = $auction->isExpired();
        $isClosed = $auction->status !== 'active' || $isExpired || ! $auction->end_time;

        $isNotStarted = $auction->status === 'active'
            && $auction->start_time
            && now()->lt($auction->start_time);

        $canBid = ! $isClosed && ! $isNotStarted;

        $remaining = $canBid
            ? $auction->timeRemainingSeconds()
            : 0;

        $currentPrice = $auction->currentPrice();
        $minimumBid = $auction->minNextBid();

        $isOwner = auth()->check()
            && $listing
            && (int) auth()->id() === (int) $listing->user_id;

        $isAdmin = auth()->check()
            && auth()->user()->role === 'admin';

        $isVerified = auth()->check()
            && auth()->user()->verification_status === 'active';

        $canContactSeller = $isClosed
            && auth()->check()
            && $auction->winner_id !== null
            && (int) $auction->winner_id === (int) auth()->id();

        $isHighestBidder = auth()->check()
            && $highestBid
            && (int) $highestBid->user_id === (int) auth()->id();

        $showBidModal = $canBid
            && auth()->check()
            && $isVerified
            && ! $isAdmin
            && ! $isOwner
            && ! $isHighestBidder;

        $bidCap = (float) $auction->bid_cap;
    @endphp

    <div
        id="auction-detail"
        class="space-y-6"
        data-auction-active="{{ $canBid ? 'true' : 'false' }}"
        data-remaining-seconds="{{ $remaining }}"
        data-expire-url="{{ route('auctions.expire', $auction->id) }}"
    >
        {{-- Kembali --}}
        <a
            href="{{ route('auctions.index') }}"
            class="inline-flex items-center gap-2 text-sm font-semibold text-slate-500 transition hover:text-blue-600"
        >
            <span aria-hidden="true">←</span>
            Kembali ke daftar lelang
        </a>

        {{-- Notifikasi --}}
        <div
            id="bid-feedback"
            class="hidden rounded-xl border px-4 py-3 text-sm"
            role="alert"
            aria-live="polite"
        ></div>

        @if (session('error'))
            <div class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
                {{ session('error') }}
            </div>
        @endif

        @if (session('success'))
            <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                {{ session('success') }}
            </div>
        @endif

        <div class="grid grid-cols-1 items-start gap-6 lg:grid-cols-5">

            {{-- KIRI: FOTO DAN DESKRIPSI BARANG --}}
            <section class="overflow-hidden rounded-3xl border border-slate-100 bg-white shadow-sm lg:col-span-3">
                <div class="relative h-48 overflow-hidden bg-slate-100 sm:h-56 lg:h-64">
                    @if ($listing?->photo_url)
                        <img
                            src="{{ $listing->photo_url }}"
                            alt="{{ $listing->title }}"
                            class="h-full w-full object-cover"
                        >
                    @else
                        <div class="flex h-full w-full items-center justify-center text-sm text-slate-400">
                            Belum ada foto barang
                        </div>
                    @endif

                    <span
                        id="auction-status-badge"
                        class="absolute left-4 top-4 rounded-full px-3 py-1.5 text-xs font-bold {{ $isClosed ? 'bg-slate-800/90 text-white' : ($isNotStarted ? 'bg-amber-100 text-amber-800' : 'bg-emerald-100 text-emerald-700') }}"
                    >
                        {{ $isClosed ? 'Lelang Ditutup' : ($isNotStarted ? 'Belum Dimulai' : 'Sedang Berlangsung') }}
                    </span>
                </div>

                <div class="space-y-4 p-5 sm:p-7">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="rounded-lg bg-blue-50 px-2.5 py-1 text-xs font-bold text-blue-700">
                            {{ $listing?->category?->name ?? 'Perlengkapan Kos' }}
                        </span>

                        <span class="text-xs text-slate-400">
                            Dipublikasikan
                            {{ $auction->created_at?->format('d M Y') }}
                        </span>
                    </div>

                    <h1 class="text-2xl font-black tracking-tight text-slate-900 sm:text-3xl">
                        {{ $listing?->title ?? 'Barang lelang' }}
                    </h1>

                    @auth
                        @if ($listing?->user)
                            <div class="flex items-center gap-3 rounded-xl border border-slate-100 bg-slate-50 px-4 py-3">
                                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-blue-100 text-sm font-bold text-blue-700">
                                    {{ strtoupper(substr($listing->user->name, 0, 1)) }}
                                </div>

                                <div class="min-w-0">
                                    <p class="text-xs font-semibold text-slate-500">
                                        Penjual
                                    </p>

                                    <p class="truncate text-sm font-bold text-slate-900">
                                        {{ $listing->user->name }}
                                    </p>
                                </div>
                            </div>
                        @endif
                    @endauth

                    <div>
                        <h2 class="text-sm font-bold text-slate-900">
                            Deskripsi barang
                        </h2>

                        <p class="mt-2 whitespace-pre-line text-sm leading-7 text-slate-600">
                            {{ $listing?->description ?: 'Tidak ada deskripsi barang.' }}
                        </p>
                    </div>

                    @if ($listing?->addresses)
                        <div class="border-t border-slate-100 pt-4">
                            <p class="text-xs font-semibold text-slate-500">
                                Lokasi / area kampus
                            </p>

                            <p class="mt-1 text-sm font-medium text-slate-800">
                                {{ $listing->addresses }}
                            </p>
                        </div>
                    @endif
                </div>
            </section>

            {{-- KANAN: HARGA, COUNTDOWN, DAN PENAWARAN --}}
            <aside class="space-y-5 lg:col-span-2">

                {{-- Informasi lelang --}}
                <section class="rounded-3xl border border-slate-100 bg-white p-5 shadow-sm sm:p-6">
                    <p class="text-sm font-medium text-slate-500">
                        Harga tertinggi saat ini
                    </p>

                    <p
                        id="current-price"
                        class="mt-1 text-3xl font-black tracking-tight text-blue-700"
                    >
                        Rp {{ number_format($currentPrice, 0, ',', '.') }}
                    </p>

                    <p class="mt-2 text-xs text-slate-500">
                        Harga awal:
                        Rp {{ number_format($auction->starting_price, 0, ',', '.') }}
                    </p>

                    {{-- COUNTDOWN --}}
                    <div class="mt-5 rounded-2xl bg-slate-50 p-4">
                        <div class="mb-3 flex items-center justify-between gap-2">
                            <span class="text-sm font-bold text-slate-700">
                                Sisa waktu lelang
                            </span>

                            <span class="text-[11px] font-semibold text-slate-400">
                                Berdasarkan waktu server
                            </span>
                        </div>

                        <div
                            id="auction-countdown"
                            class="grid grid-cols-4 gap-2 text-center"
                        >
                            @foreach ([
                                ['days', 'Hari'],
                                ['hours', 'Jam'],
                                ['minutes', 'Menit'],
                                ['seconds', 'Detik'],
                            ] as [$unit, $label])
                                <div class="rounded-xl border border-slate-100 bg-white px-1 py-3">
                                    <span
                                        data-unit="{{ $unit }}"
                                        class="block text-xl font-black tabular-nums text-slate-900"
                                    >00</span>

                                    <span class="mt-1 block text-[10px] font-semibold text-slate-500">
                                        {{ $label }}
                                    </span>
                                </div>
                            @endforeach
                        </div>

                        <p
                            id="closed-message"
                            class="mt-3 hidden text-center text-sm font-bold text-rose-600"
                        >
                            Lelang Ditutup
                        </p>

                        <p
                            id="not-started-message"
                            class="mt-3 {{ $isNotStarted ? '' : 'hidden' }} text-center text-sm font-bold text-amber-700"
                        >
                            Lelang belum dimulai.
                        </p>
                    </div>

                    {{-- Informasi bid --}}
                    <div class="mt-5 grid grid-cols-2 gap-3 text-xs">
                        <div class="rounded-xl border border-slate-100 p-3">
                            <p class="text-slate-500">
                                Kelipatan tawaran
                            </p>

                            <p class="mt-1 font-bold text-slate-900">
                                Rp {{ number_format($auction->bid_increment, 0, ',', '.') }}
                            </p>
                        </div>

                        <div class="rounded-xl border border-slate-100 p-3">
                            <p class="text-slate-500">
                                Jumlah penawaran
                            </p>

                            <p id="bid-count" class="mt-1 font-bold text-slate-900">
                                {{ $auction->bids->count() }} penawaran
                            </p>
                        </div>
                    </div>

                    {{-- TOMBOL SESUAI STATUS PENGGUNA --}}
                    <div id="bid-action-area" class="mt-5">

                    @if ($isClosed)
                        <div class="space-y-3">
                            <div class="rounded-xl bg-slate-100 px-4 py-3 text-center text-sm font-bold text-slate-500">
                                Lelang Ditutup
                            </div>

                            @if ($canContactSeller)
                                <button
                                    type="button"
                                    id="contact-seller-button"
                                    class="w-full rounded-xl bg-blue-600 px-4 py-3.5 text-sm font-bold text-white transition hover:bg-blue-700"
                                >
                                    Hubungi Penjual →
                                </button>
                            @endif
                        </div>

                        @elseif ($isNotStarted)
                            <div class="rounded-xl bg-amber-50 px-4 py-3 text-center text-sm font-semibold text-amber-800">
                                Lelang belum dimulai.
                            </div>

                        @elseif (auth()->guest())
                            <div class="space-y-3">
                                <p class="text-center text-sm text-slate-500">
                                    Masuk atau daftar akun mahasiswa untuk ikut menawar.
                                </p>

                                <a
                                    href="{{ route('login') }}"
                                    class="flex w-full items-center justify-center rounded-xl bg-blue-600 px-4 py-3.5 text-sm font-bold text-white transition hover:bg-blue-700"
                                >
                                    Masuk untuk Menawar
                                </a>

                                <a
                                    href="{{ route('register') }}"
                                    class="block text-center text-sm font-semibold text-blue-600 hover:text-blue-700"
                                >
                                    Belum punya akun? Daftar
                                </a>
                            </div>

                        @elseif ($isAdmin)
                            <div class="rounded-xl bg-slate-100 px-4 py-3 text-center text-sm font-semibold text-slate-600">
                                Administrator tidak dapat ikut menawar.
                            </div>

                        @elseif (! $isVerified)
                            <div class="rounded-xl bg-amber-50 px-4 py-3 text-center text-sm font-semibold text-amber-800">
                                Akun harus terverifikasi aktif untuk ikut menawar.
                            </div>

                        @elseif ($isOwner)
                            <div class="rounded-xl bg-amber-50 px-4 py-3 text-center text-sm font-semibold text-amber-800">
                                Kamu tidak dapat menawar barang lelang milik sendiri.
                            </div>

                        @elseif ($isHighestBidder)
                            <div class="rounded-xl bg-blue-50 px-4 py-3 text-center text-sm font-semibold text-blue-800">
                                Kamu sedang menjadi penawar tertinggi. Tunggu penawaran peserta lain.
                            </div>

                        @else
                            <button
                                id="open-bid-modal"
                                type="button"
                                class="w-full rounded-xl bg-blue-600 px-4 py-3.5 text-sm font-bold text-white shadow-md shadow-blue-500/20 transition hover:bg-blue-700"
                            >
                                Tawar Sekarang
                            </button>
                        @endif
                    </div>

                    <p class="mt-3 text-center text-[11px] leading-relaxed text-slate-400">
                        Pembayaran barang hasil lelang dilakukan langsung antara pemenang dan penjual saat serah terima (COD).
                    </p>
                </section>

                {{-- RIWAYAT PENAWARAN --}}
                <section class="rounded-3xl border border-slate-100 bg-white p-5 shadow-sm sm:p-6">
                    <div class="flex items-center justify-between gap-3">
                        <h2 class="font-black text-slate-900">
                            Riwayat penawaran
                        </h2>

                        <span class="rounded-lg bg-blue-50 px-2 py-1 text-xs font-bold text-blue-700">
                            {{ $auction->bids->count() }}
                        </span>
                    </div>

                    <div id="bid-history" class="mt-4 space-y-3">
                        @forelse ($auction->bids as $bid)
                            <div class="bid-history-item flex items-center justify-between gap-3 border-b border-slate-100 pb-3 last:border-0 last:pb-0">
                                <div class="min-w-0">
                                    <p class="truncate text-sm font-bold text-slate-800">
                                        {{ $bid->user?->name ?? 'Mahasiswa' }}
                                    </p>

                                    <p class="mt-0.5 text-[11px] text-slate-400">
                                        {{ $bid->created_at?->format('d M Y, H:i') }}
                                    </p>
                                </div>

                                <p class="shrink-0 text-sm font-black text-blue-700">
                                    Rp {{ number_format($bid->bid_amount, 0, ',', '.') }}
                                </p>
                            </div>
                        @empty
                            <p
                                id="no-bids-message"
                                class="rounded-xl bg-slate-50 px-3 py-5 text-center text-sm text-slate-500"
                            >
                                Belum ada penawaran. Jadilah yang pertama!
                            </p>
                        @endforelse
                    </div>
                </section>
            </aside>
        </div>
    </div>

    {{-- MODAL PENAWARAN --}}
    @if ($showBidModal)
        <div
            id="bid-modal"
            class="fixed inset-0 z-[60] hidden items-center justify-center overflow-y-auto bg-slate-950/50 p-4"
            role="dialog"
            aria-modal="true"
            aria-labelledby="bid-modal-title"
        >
            <div class="my-auto w-full max-w-md rounded-3xl bg-white p-5 shadow-2xl sm:p-7">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-wider text-blue-600">
                            Penawaran lelang
                        </p>

                        <h2 id="bid-modal-title" class="mt-1 text-xl font-black text-slate-900">
                            Ajukan harga tawaran
                        </h2>

                        <p class="mt-1 text-sm text-slate-500">
                            Masukkan nominal sesuai ketentuan lelang.
                        </p>
                    </div>

                    <button
                        type="button"
                        id="close-bid-modal"
                        class="rounded-xl p-2 text-slate-400 transition hover:bg-slate-100 hover:text-slate-700"
                        aria-label="Tutup modal"
                    >
                        ✕
                    </button>
                </div>

                <div class="mt-5 rounded-2xl bg-blue-50 p-4">
                    <p class="text-xs text-blue-700">
                        Penawaran minimum berikutnya
                    </p>

                    <p id="minimum-bid-label" class="mt-1 text-lg font-black text-blue-800">
                        Rp {{ number_format($minimumBid, 0, ',', '.') }}
                    </p>
                </div>

                <form
                    id="bid-form"
                    action="{{ route('auctions.bids.store', $auction->id) }}"
                    method="POST"
                    class="mt-5 space-y-4"
                >
                    @csrf

                    <div>
                        <label for="bid_amount" class="block text-sm font-bold text-slate-700">
                            Nominal tawaran (Rp)
                        </label>

                        <input
                            id="bid_amount"
                            name="bid_amount"
                            type="number"
                            min="{{ $minimumBid }}"
                            step="{{ $auction->bid_increment }}"
                            @if ($bidCap > 0)
                                max="{{ $bidCap }}"
                            @endif
                            value="{{ $minimumBid }}"
                            required
                            class="mt-2 w-full rounded-xl border border-slate-200 px-4 py-3 text-base font-semibold text-slate-900 outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100"
                        >
                    </div>

                    <p class="text-xs leading-relaxed text-slate-500">
                        Kelipatan tawaran:
                        Rp {{ number_format($auction->bid_increment, 0, ',', '.') }}.
                    </p>

                    @if ($bidCap > 0)
                        <p class="text-xs leading-relaxed text-slate-500">
                            Batas maksimal / buy-out:
                            Rp {{ number_format($bidCap, 0, ',', '.') }}.
                        </p>
                    @endif

                    <p
                        id="bid-form-error"
                        class="hidden rounded-xl bg-rose-50 px-3 py-2 text-sm text-rose-700"
                        role="alert"
                    ></p>

                    <div class="flex gap-3 pt-1">
                        <button
                            type="button"
                            id="cancel-bid-modal"
                            class="flex-1 rounded-xl border border-slate-200 px-4 py-3 text-sm font-bold text-slate-600 transition hover:bg-slate-50"
                        >
                            Batal
                        </button>

                        <button
                            type="submit"
                            id="submit-bid-button"
                            class="flex-1 rounded-xl bg-blue-600 px-4 py-3 text-sm font-bold text-white transition hover:bg-blue-700 disabled:cursor-not-allowed disabled:opacity-60"
                        >
                            Kirim Penawaran
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- JAVASCRIPT COUNTDOWN DAN MODAL --}}
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const root = document.getElementById('auction-detail');

            if (!root) return;

            let remainingSeconds = Math.max(
                0,
                Number(root.dataset.remainingSeconds || 0)
            );

            let timerStartedAt = performance.now();
            let timerInterval = null;
            let expirationRequestSent = false;

            const modal = document.getElementById('bid-modal');
            const openButton = document.getElementById('open-bid-modal');
            const form = document.getElementById('bid-form');
            const input = document.getElementById('bid_amount');
            const submitButton = document.getElementById('submit-bid-button');
            const errorBox = document.getElementById('bid-form-error');
            const feedback = document.getElementById('bid-feedback');

            const closeModal = () => {
                if (!modal) return;

                modal.classList.add('hidden');
                modal.classList.remove('flex');
                document.body.classList.remove('overflow-hidden');
            };

            const openModal = () => {
                if (
                    !modal ||
                    root.dataset.auctionActive !== 'true'
                ) {
                    return;
                }

                modal.classList.remove('hidden');
                modal.classList.add('flex');
                document.body.classList.add('overflow-hidden');

                input?.focus();
            };

            const showFeedback = (message, success = false) => {
                if (!feedback) return;

                feedback.textContent = message;
                feedback.className = success
                    ? 'rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700'
                    : 'rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700';
            };

            const closeAuctionUI = () => {
                root.dataset.auctionActive = 'false';

                const badge = document.getElementById('auction-status-badge');

                if (badge) {
                    badge.textContent = 'Lelang Ditutup';
                    badge.className =
                        'absolute left-4 top-4 rounded-full bg-slate-800/90 px-3 py-1.5 text-xs font-bold text-white';
                }

                document.getElementById('closed-message')
                    ?.classList.remove('hidden');

                document.getElementById('not-started-message')
                    ?.classList.add('hidden');

                document.querySelectorAll('[data-unit]').forEach((node) => {
                    node.textContent = '00';
                });

                const actionArea = document.getElementById('bid-action-area');

                if (actionArea) {
                    actionArea.innerHTML =
                        '<div class="rounded-xl bg-slate-100 px-4 py-3 text-center text-sm font-bold text-slate-500">Lelang Ditutup</div>';
                }

                closeModal();
            };

            // Minta server memperbarui status setelah countdown habis.
            const notifyServerExpired = async () => {
                if (expirationRequestSent) return;

                expirationRequestSent = true;

                const csrfToken =
                    document.querySelector('meta[name="csrf-token"]')?.content;

                try {
                    const response = await fetch(root.dataset.expireUrl, {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                            ...(csrfToken ? { 'X-CSRF-TOKEN': csrfToken } : {}),
                        },
                    });

                    const result = await response.json();

                    // Jika timer browser sedikit lebih cepat daripada waktu server,
                    // coba sinkronkan kembali setelah satu detik.
                    if (
                        response.status === 409 &&
                        result.status === 'active'
                    ) {
                        expirationRequestSent = false;

                        window.setTimeout(() => {
                            expirationRequestSent = false;
                            notifyServerExpired();
                        }, 1000);
                    }
                } catch (error) {
                    console.error('Gagal memperbarui status lelang:', error);
                    expirationRequestSent = false;
                }
            };

            const renderCountdown = () => {
                const elapsed = Math.floor(
                    (performance.now() - timerStartedAt) / 1000
                );

                const remaining = Math.max(0, remainingSeconds - elapsed);

                const units = {
                    days: Math.floor(remaining / 86400),
                    hours: Math.floor((remaining % 86400) / 3600),
                    minutes: Math.floor((remaining % 3600) / 60),
                    seconds: remaining % 60,
                };

                Object.entries(units).forEach(([unit, value]) => {
                    const node = document.querySelector(
                        `[data-unit="${unit}"]`
                    );

                    if (node) {
                        node.textContent = String(value).padStart(2, '0');
                    }
                });

                if (remaining <= 0) {
                    closeAuctionUI();
                    notifyServerExpired();

                    if (timerInterval) {
                        window.clearInterval(timerInterval);
                    }

                    return false;
                }

                return true;
            };

            if (root.dataset.auctionActive === 'true') {
                if (renderCountdown()) {
                    timerInterval = window.setInterval(() => {
                        renderCountdown();
                    }, 1000);
                }
            } 
            }

            openButton?.addEventListener('click', openModal);

            document.getElementById('close-bid-modal')
                ?.addEventListener('click', closeModal);

            document.getElementById('cancel-bid-modal')
                ?.addEventListener('click', closeModal);

            modal?.addEventListener('click', (event) => {
                if (event.target === modal) {
                    closeModal();
                }
            });

            document.addEventListener('keydown', (event) => {
                if (event.key === 'Escape') {
                    closeModal();
                }
            });

            // Kirim penawaran menggunakan format JSON dari AuctionController.
            if (form) {
                form.addEventListener('submit', async (event) => {
                    event.preventDefault();

                    if (root.dataset.auctionActive !== 'true') {
                        closeAuctionUI();
                        return;
                    }

                    errorBox.classList.add('hidden');
                    submitButton.disabled = true;
                    submitButton.textContent = 'Mengirim...';

                    try {
                        const response = await fetch(form.action, {
                            method: 'POST',
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest',
                            },
                            body: new FormData(form),
                        });

                        const payload = await response.json();

                        if (!response.ok || payload.status !== 'success') {
                            if (
                                payload.message?.includes(
                                    'Waktu lelang telah berakhir'
                                )
                            ) {
                                closeAuctionUI();
                            }

                            throw new Error(
                                payload.errors?.bid_amount?.[0] ||
                                payload.message ||
                                'Penawaran gagal dikirim.'
                            );
                        }

                        const result = payload.data;

                        document.getElementById('current-price').textContent =
                            'Rp ' + Number(result.highest_bid)
                                .toLocaleString('id-ID');

                        document.getElementById('minimum-bid-label').textContent =
                            'Rp ' + Number(result.min_next_bid)
                                .toLocaleString('id-ID');

                        input.min = result.min_next_bid;
                        input.value = result.min_next_bid;

                        // Perbarui countdown menggunakan sisa waktu yang dikirim server.
                        if (result.time_remaining_seconds !== undefined) {
                            remainingSeconds = Math.max(
                                0,
                                Number(result.time_remaining_seconds)
                            );

                            timerStartedAt = performance.now();

                            if (remainingSeconds > 0 && timerInterval === null) {
                                timerInterval = window.setInterval(() => {
                                    renderCountdown();
                                }, 1000);
                            }
                        }

                        const history = document.getElementById('bid-history');

                        document.getElementById('no-bids-message')?.remove();

                        const item = document.createElement('div');

                        item.className =
                            'bid-history-item flex items-center justify-between gap-3 border-b border-slate-100 pb-3';

                        const left = document.createElement('div');
                        left.className = 'min-w-0';

                        const name = document.createElement('p');
                        name.className =
                            'truncate text-sm font-bold text-slate-800';
                        name.textContent = result.bidder?.name || 'Mahasiswa';

                        const date = document.createElement('p');
                        date.className = 'mt-0.5 text-[11px] text-slate-400';
                        date.textContent = 'Baru saja';

                        left.append(name, date);

                        const amount = document.createElement('p');
                        amount.className =
                            'shrink-0 text-sm font-black text-blue-700';
                        amount.textContent =
                            'Rp ' + Number(result.bid_amount)
                                .toLocaleString('id-ID');

                        item.append(left, amount);
                        history.prepend(item);

                        const total = history.querySelectorAll(
                            '.bid-history-item'
                        ).length;

                        document.getElementById('bid-count').textContent =
                            total + ' penawaran';

                        showFeedback(payload.message, true);
                        closeModal();

                        // Perbarui tombol bila user baru saja memenangkan buy-out.
                        if (result.is_closed) {
                            closeAuctionUI();
                        } else {
                            const actionArea =
                                document.getElementById('bid-action-area');

                            if (actionArea) {
                                actionArea.innerHTML =
                                    '<div class="rounded-xl bg-blue-50 px-4 py-3 text-center text-sm font-semibold text-blue-800">Kamu sedang menjadi penawar tertinggi. Tunggu penawaran peserta lain.</div>';
                            }

                            openButton?.remove();
                        }
                    } catch (error) {
                        errorBox.textContent = error.message;
                        errorBox.classList.remove('hidden');
                    } finally {
                        submitButton.disabled = false;
                        submitButton.textContent = 'Kirim Penawaran';
                    }
                });
            }
        });
    </script>
</x-layouts.app>