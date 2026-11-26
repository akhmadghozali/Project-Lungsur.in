<x-layouts.app>
    <x-slot:title>
        {{ $auction->listing?->title ?? 'Detail Lelang' }} - Lungsur.in
    </x-slot:title>

    @php
        $listing = $auction->listing;
        $highestBid = $auction->highestBid;

        $isPendingVerification = in_array($auction->status, ['pending_payment', 'pending_approval'], true);
        $isExpired = $auction->isExpired();
        $isClosed = $isPendingVerification || $auction->status !== 'active' || $isExpired || ! $auction->end_time;

        $isNotStarted = $auction->status === 'active'
            && $auction->start_time
            && now()->lt($auction->start_time);

        $canBid = ! $isPendingVerification && ! $isClosed && ! $isNotStarted;

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

        $isSeller = auth()->check()
            && auth()->user()->isSeller();

        $showBidModal = $canBid
            && auth()->check()
            && $isVerified
            && ! $isAdmin
            && ! $isOwner
            && ! $isSeller
            && ! $isHighestBidder;

        $bidCap = (float) $auction->bid_cap;
        $inc = (int) ($auction->bid_increment ?: 10000);
        $quickPreset1 = $minimumBid;
        $quickPreset2 = $bidCap > 0 ? min($minimumBid + $inc, (int) $bidCap) : ($minimumBid + $inc);
        $quickPreset3 = $bidCap > 0 ? (int) $bidCap : ($minimumBid + ($inc * 2));
    @endphp

    <div
        id="auction-detail"
        class="space-y-5 sm:space-y-6 pb-20 sm:pb-0"
        data-auction-active="{{ $canBid ? 'true' : 'false' }}"
        data-remaining-seconds="{{ $remaining }}"
        data-expire-url="{{ route('auctions.expire', $auction->id) }}"
    >
        <a
            href="{{ route('auctions.index') }}"
            class="min-h-[44px] inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-white border border-slate-200 text-xs sm:text-sm font-bold text-slate-700 shadow-2xs transition hover:border-blue-300 hover:text-blue-600 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600"
        >
            <span aria-hidden="true">&larr;</span>
            <span>Kembali ke Daftar Lelang</span>
        </a>

        <div
            id="bid-feedback"
            class="hidden rounded-xl border px-4 py-3 text-sm font-semibold"
            role="alert"
            aria-live="polite"
        ></div>

        @if (session('error'))
            <div class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-semibold text-rose-800">
                {{ session('error') }}
            </div>
        @endif

        @if (session('success'))
            <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-800">
                {{ session('success') }}
            </div>
        @endif

        <div class="grid grid-cols-1 items-start gap-5 sm:gap-6 lg:grid-cols-5">

            <section class="overflow-hidden rounded-3xl border border-slate-200/80 bg-white shadow-xs lg:col-span-3">
                <div class="relative h-60 overflow-hidden bg-slate-100 sm:h-72 lg:h-80">
                    @if ($listing?->photo_url)
                        <img
                            src="{{ $listing->photo_url }}"
                            alt="{{ $listing->title }}"
                            class="h-full w-full object-cover"
                        >
                    @else
                        <div class="flex h-full w-full items-center justify-center text-sm font-medium text-slate-500">
                            Belum ada foto barang
                        </div>
                    @endif

                    <span
                        id="auction-status-badge"
                        class="absolute left-4 top-4 rounded-full px-3.5 py-1.5 text-xs font-bold shadow-2xs {{ $isPendingVerification ? 'bg-amber-50 text-amber-800 border border-amber-200' : ($isClosed ? 'bg-slate-800/95 text-white' : ($isNotStarted ? 'bg-amber-50 text-amber-800 border border-amber-200' : 'bg-emerald-50 text-emerald-800 border border-emerald-200')) }}"
                    >
                        {{ $isPendingVerification ? 'Menunggu Verifikasi Admin' : ($isClosed ? 'Lelang Ditutup' : ($isNotStarted ? 'Belum Dimulai' : 'Sedang Berlangsung')) }}
                    </span>
                </div>

                <div class="space-y-4 p-5 sm:p-7">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="rounded-lg bg-blue-50 border border-blue-100 px-2.5 py-1 text-xs font-bold text-blue-700">
                            {{ $listing?->category?->name ?? 'Perlengkapan Kos' }}
                        </span>

                        <span class="text-xs font-medium text-slate-600">
                            Dipublikasikan {{ $auction->created_at?->format('d M Y') }}
                        </span>
                    </div>

                    <h1 class="text-xl sm:text-3xl font-black tracking-tight text-slate-900">
                        {{ $listing?->title ?? 'Barang lelang' }}
                    </h1>

                    @auth
                        @if ($listing?->user)
                            <div class="flex items-center gap-3 rounded-xl border border-slate-200/80 bg-slate-50 px-4 py-3">
                                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-blue-100 text-sm font-bold text-blue-700">
                                    {{ strtoupper(substr($listing->user->name, 0, 1)) }}
                                </div>

                                <div class="min-w-0">
                                    <p class="text-xs font-semibold text-slate-600">
                                        Penjual Mahasiswa
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
                            Deskripsi Barang
                        </h2>

                        <p class="mt-2 whitespace-pre-line text-xs sm:text-sm leading-relaxed text-slate-700">
                            {{ $listing?->description ?: 'Penjual belum menambahkan deskripsi barang.' }}
                        </p>
                    </div>

                    @if ($listing?->addresses)
                        <div class="border-t border-slate-100 pt-4">
                            <p class="text-xs font-semibold text-slate-600">
                                Titik COD / Area Kampus
                            </p>

                            <p class="mt-1 text-sm font-bold text-slate-900">
                                {{ $listing->addresses }}
                            </p>
                        </div>
                    @endif
                </div>
            </section>

            <aside class="space-y-5 lg:col-span-2">

                <section class="rounded-3xl border border-slate-200/80 bg-white p-5 shadow-xs sm:p-6">
                    <p class="text-xs sm:text-sm font-semibold text-slate-600">
                        Harga tertinggi saat ini
                    </p>

                    <p
                        id="current-price"
                        class="mt-1 text-2xl sm:text-3xl font-black tracking-tight text-blue-700"
                    >
                        Rp {{ number_format($currentPrice, 0, ',', '.') }}
                    </p>

                    <p class="mt-1.5 text-xs font-medium text-slate-600">
                        Harga awal: Rp {{ number_format($auction->starting_price, 0, ',', '.') }}
                        @if ($bidCap > 0)
                            • Batas Menang Instan: <strong class="text-slate-900">Rp {{ number_format($bidCap, 0, ',', '.') }}</strong>
                        @endif
                    </p>

                    <div class="mt-4 sm:mt-5 rounded-2xl bg-slate-50 border border-slate-100 p-4">
                        <div class="mb-3 flex items-center justify-between gap-2">
                            <span class="text-xs sm:text-sm font-bold text-slate-800">
                                Sisa waktu lelang
                            </span>

                            <span class="text-[11px] font-semibold text-slate-600">
                                Waktu server
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
                                <div class="rounded-xl border border-slate-200/80 bg-white px-1 py-2.5 sm:py-3">
                                    <span
                                        data-unit="{{ $unit }}"
                                        class="block text-lg sm:text-xl font-black tabular-nums text-slate-900"
                                    >00</span>

                                    <span class="mt-0.5 block text-[11px] font-semibold text-slate-600">
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
                            class="mt-3 {{ $isNotStarted ? '' : 'hidden' }} text-center text-sm font-bold text-amber-800"
                        >
                            Lelang belum dimulai.
                        </p>
                    </div>

                    <div class="mt-4 sm:mt-5 grid grid-cols-2 gap-3 text-xs">
                        <div class="rounded-xl border border-slate-200/80 bg-slate-50/50 p-3">
                            <p class="font-medium text-slate-600">
                                Kelipatan tawaran
                            </p>

                            <p class="mt-1 font-bold text-slate-900">
                                Rp {{ number_format($auction->bid_increment, 0, ',', '.') }}
                            </p>
                        </div>

                        <div class="rounded-xl border border-slate-200/80 bg-slate-50/50 p-3">
                            <p class="font-medium text-slate-600">
                                Jumlah penawaran
                            </p>

                            <p id="bid-count" class="mt-1 font-bold text-slate-900">
                                {{ $auction->bids->count() }} penawaran
                            </p>
                        </div>
                    </div>

                    <div id="bid-action-area" class="mt-5">

                        @if ($isClosed)
                            <div class="space-y-3">
                                <div class="rounded-xl bg-slate-100 border border-slate-200 px-4 py-3 text-center text-sm font-bold text-slate-700">
                                    Lelang Ditutup
                                </div>

                                @if ($canContactSeller)
                                    <div class="rounded-xl bg-emerald-50 border border-emerald-200 p-3 text-center text-xs font-semibold text-emerald-800">
                                        Selamat! Kamu adalah pemenang lelang ini. Silakan hubungi penjual untuk berkoordinasi COD.
                                    </div>
                                    <a
                                        href="{{ route('chat.index', ['listing_id' => $auction->listing_id]) }}"
                                        id="contact-seller-button"
                                        class="min-h-[48px] flex w-full items-center justify-center gap-2 rounded-xl bg-blue-600 px-4 py-3.5 text-sm font-bold text-white transition hover:bg-blue-700 shadow-md shadow-blue-500/20 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                                        <span>Hubungi Penjual &rarr;</span>
                                    </a>
                                @endif
                            </div>

                        @elseif ($isNotStarted)
                            <div class="rounded-xl bg-amber-50 border border-amber-200 px-4 py-3 text-center text-sm font-semibold text-amber-800">
                                Lelang belum dimulai.
                            </div>

                        @elseif (auth()->guest())
                            <div class="space-y-3">
                                <p class="text-center text-xs sm:text-sm text-slate-600">
                                    Masuk atau daftar akun mahasiswa untuk ikut menawar.
                                </p>

                                <a
                                    href="{{ route('login') }}"
                                    class="min-h-[48px] flex w-full items-center justify-center rounded-xl bg-blue-600 px-4 py-3.5 text-sm font-bold text-white transition hover:bg-blue-700 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600"
                                >
                                    Masuk untuk Menawar
                                </a>

                                <a
                                    href="{{ route('register') }}"
                                    class="min-h-[44px] flex items-center justify-center text-center text-xs sm:text-sm font-bold text-blue-600 hover:text-blue-700 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600 rounded-xl"
                                >
                                    Belum punya akun? Daftar sekarang
                                </a>
                            </div>

                        @elseif ($isAdmin)
                            <div class="rounded-xl bg-slate-100 border border-slate-200 px-4 py-3 text-center text-sm font-semibold text-slate-700">
                                Administrator tidak dapat ikut menawar.
                            </div>

                        @elseif (! $isVerified)
                            <div class="rounded-xl bg-amber-50 border border-amber-200 px-4 py-3 text-center text-sm font-semibold text-amber-800">
                                Akun harus terverifikasi aktif untuk ikut menawar.
                            </div>

                        @elseif ($isOwner)
                            <div class="rounded-xl bg-amber-50 border border-amber-200 px-4 py-3 text-center text-sm font-semibold text-amber-800">
                                Kamu tidak dapat menawar barang lelang milik sendiri.
                            </div>

                        @elseif ($isHighestBidder)
                            <div class="rounded-xl bg-blue-50 border border-blue-200 px-4 py-3 text-center text-sm font-semibold text-blue-800">
                                Kamu sedang menjadi penawar tertinggi. Tunggu penawaran peserta lain.
                            </div>

                        @elseif ($isSeller)
                            <div class="rounded-2xl border border-amber-200 bg-amber-50/90 p-4 text-center space-y-2.5">
                                <p class="text-xs font-semibold text-amber-900 leading-relaxed">
                                    Kamu sedang dalam <span class="font-bold">Mode Penjual</span>. Beralih ke <span class="font-bold">Mode Pembeli</span> untuk mengajukan penawaran lelang ini.
                                </p>
                                <form method="POST" action="{{ route('user.switch_mode') }}">
                                    @csrf
                                    <input type="hidden" name="mode" value="buyer">
                                    <button type="submit" class="w-full min-h-[44px] py-2.5 px-4 rounded-xl bg-amber-700 hover:bg-amber-800 text-white font-bold text-xs shadow-xs transition cursor-pointer focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-amber-700">
                                        Beralih ke Mode Pembeli &amp; Tawar
                                    </button>
                                </form>
                            </div>

                        @else
                            <button
                                id="open-bid-modal"
                                type="button"
                                class="w-full min-h-[48px] rounded-xl bg-blue-600 px-4 py-3.5 text-sm font-bold text-white shadow-md shadow-blue-500/20 transition hover:bg-blue-700 cursor-pointer focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600"
                            >
                                Tawar Sekarang
                            </button>
                        @endif
                    </div>

                    <p class="mt-3 text-center text-[11px] leading-relaxed text-slate-600">
                        Pembayaran barang hasil lelang dilakukan langsung antara pemenang dan penjual saat serah terima (COD).
                    </p>
                </section>

                <section class="rounded-3xl border border-slate-200/80 bg-white p-5 shadow-xs sm:p-6">
                    <div class="flex items-center justify-between gap-3">
                        <h2 class="text-base font-black text-slate-900">
                            Riwayat Penawaran
                        </h2>

                        <span class="rounded-lg bg-blue-50 border border-blue-100 px-2.5 py-1 text-xs font-bold text-blue-700">
                            {{ $auction->bids->count() }}
                        </span>
                    </div>

                    <div id="bid-history" class="mt-4 space-y-3">
                        @forelse ($auction->bids as $bid)
                            <div class="bid-history-item flex items-center justify-between gap-3 border-b border-slate-100 pb-3 last:border-0 last:pb-0">
                                <div class="min-w-0">
                                    <p class="truncate text-sm font-bold text-slate-900">
                                        {{ $bid->user?->name ?? 'Mahasiswa' }}
                                    </p>

                                    <p class="mt-0.5 text-[11px] font-medium text-slate-600">
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
                                class="rounded-xl bg-slate-50 border border-slate-100 px-3 py-5 text-center text-xs sm:text-sm font-medium text-slate-600"
                            >
                                Belum ada penawaran. Jadilah penawar pertama!
                            </p>
                        @endforelse
                    </div>
                </section>
            </aside>
        </div>
    </div>

    <div id="mobile-auction-sticky-bar" class="sm:hidden fixed bottom-0 inset-x-0 z-40 bg-white/95 backdrop-blur-md border-t border-slate-200 px-4 py-2.5 shadow-[0_-4px_16px_rgba(15,23,42,0.06)]">
        <div class="flex items-center justify-between gap-3 max-w-xl mx-auto">
            <div class="min-w-0">
                <span class="block text-[11px] font-semibold text-slate-600">Harga tertinggi</span>
                <span id="mobile-sticky-price" class="block text-base font-black text-blue-700">Rp {{ number_format($currentPrice, 0, ',', '.') }}</span>
            </div>

            <div id="mobile-sticky-action">
                @if ($showBidModal)
                    <button type="button"
                            id="open-bid-modal-mobile"
                            class="shrink-0 min-h-[44px] px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-extrabold text-xs inline-flex items-center justify-center shadow-sm transition cursor-pointer focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600">
                        Tawar Sekarang
                    </button>
                @elseif ($canContactSeller)
                    <a href="{{ route('chat.index', ['listing_id' => $auction->listing_id]) }}"
                       class="shrink-0 min-h-[44px] px-4 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-extrabold text-xs inline-flex items-center justify-center shadow-sm transition">
                        Hubungi Penjual
                    </a>
                @elseif ($canBid && auth()->guest())
                    <a href="{{ route('login') }}"
                       class="shrink-0 min-h-[44px] px-4 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-extrabold text-xs inline-flex items-center justify-center shadow-sm transition">
                        Masuk &amp; Tawar
                    </a>
                @elseif ($canBid && $isSeller)
                    <form method="POST" action="{{ route('user.switch_mode') }}">
                        @csrf
                        <input type="hidden" name="mode" value="buyer">
                        <button type="submit" class="shrink-0 min-h-[44px] px-3.5 py-2 rounded-xl bg-amber-700 hover:bg-amber-800 text-white font-bold text-xs shadow-xs transition cursor-pointer">
                            Mode Pembeli
                        </button>
                    </form>
                @elseif ($isHighestBidder && !$isClosed)
                    <span class="shrink-0 px-3 py-2 rounded-xl bg-blue-50 border border-blue-200 text-blue-800 font-bold text-xs">
                        Penawar Tertinggi
                    </span>
                @elseif ($isClosed)
                    <span class="shrink-0 px-3 py-2 rounded-xl bg-slate-100 border border-slate-200 text-slate-700 font-bold text-xs">
                        Lelang Ditutup
                    </span>
                @endif
            </div>
        </div>
    </div>

    @if ($showBidModal)
        <div
            id="bid-modal"
            class="fixed inset-0 z-[60] hidden items-center justify-center overflow-y-auto bg-slate-950/60 p-4 backdrop-blur-xs"
            role="dialog"
            aria-modal="true"
            aria-labelledby="bid-modal-title"
        >
            <div class="my-auto w-full max-w-md rounded-3xl bg-white p-5 shadow-2xl sm:p-7">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-wider text-blue-700">
                            Penawaran Lelang
                        </p>

                        <h2 id="bid-modal-title" class="mt-1 text-xl font-black text-slate-900">
                            Ajukan Harga Tawaran
                        </h2>

                        <p class="mt-1 text-xs sm:text-sm text-slate-600">
                            Pilih nominal cepat atau ketik tawaran sesuai kelipatan lelang.
                        </p>
                    </div>

                    <button
                        type="button"
                        id="close-bid-modal"
                        class="w-11 h-11 shrink-0 rounded-xl flex items-center justify-center text-slate-500 transition hover:bg-slate-100 hover:text-slate-900 cursor-pointer focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600"
                        aria-label="Tutup modal penawaran"
                    >
                        ✕
                    </button>
                </div>

                <div class="mt-4 rounded-2xl bg-blue-50 border border-blue-100 p-4">
                    <p class="text-xs font-semibold text-blue-800">
                        Penawaran minimum berikutnya
                    </p>

                    <p id="minimum-bid-label" class="mt-1 text-lg font-black text-blue-900">
                        Rp {{ number_format($minimumBid, 0, ',', '.') }}
                    </p>
                </div>

                <form
                    id="bid-form"
                    action="{{ route('auctions.bids.store', $auction->id) }}"
                    method="POST"
                    class="mt-4 space-y-4"
                >
                    @csrf

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-2">
                            Pilih Cepat Nominal
                        </label>
                        <div class="grid grid-cols-3 gap-2">
                            <button type="button"
                                    data-quick-bid="{{ $quickPreset1 }}"
                                    class="js-quick-bid min-h-[44px] px-2 py-2 rounded-xl border border-slate-200 bg-slate-50 hover:bg-blue-50 hover:border-blue-300 text-slate-800 hover:text-blue-700 text-xs font-bold transition cursor-pointer focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600">
                                <span class="block text-[10px] text-slate-600 font-semibold">Minimum</span>
                                <span class="js-quick-bid-label">Rp {{ number_format($quickPreset1, 0, ',', '.') }}</span>
                            </button>
                            <button type="button"
                                    data-quick-bid="{{ $quickPreset2 }}"
                                    class="js-quick-bid min-h-[44px] px-2 py-2 rounded-xl border border-slate-200 bg-slate-50 hover:bg-blue-50 hover:border-blue-300 text-slate-800 hover:text-blue-700 text-xs font-bold transition cursor-pointer focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600">
                                <span class="block text-[10px] text-slate-600 font-semibold">+1 Kelipatan</span>
                                <span class="js-quick-bid-label">Rp {{ number_format($quickPreset2, 0, ',', '.') }}</span>
                            </button>
                            <button type="button"
                                    data-quick-bid="{{ $quickPreset3 }}"
                                    class="js-quick-bid min-h-[44px] px-2 py-2 rounded-xl border border-blue-200 bg-blue-50/60 hover:bg-blue-100 text-blue-800 text-xs font-bold transition cursor-pointer focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600">
                                <span class="block text-[10px] text-blue-700 font-semibold">{{ $bidCap > 0 ? 'Menang Instan' : '+2 Kelipatan' }}</span>
                                <span class="js-quick-bid-label">Rp {{ number_format($quickPreset3, 0, ',', '.') }}</span>
                            </button>
                        </div>
                    </div>

                    <div>
                        <label for="bid_amount" class="block text-xs sm:text-sm font-bold text-slate-800">
                            Nominal tawaran (Rp)
                        </label>

                        <input
                            id="bid_amount"
                            name="bid_amount"
                            type="number"
                            inputmode="numeric"
                            min="{{ $minimumBid }}"
                            step="{{ $auction->bid_increment }}"
                            @if ($bidCap > 0)
                                max="{{ $bidCap }}"
                            @endif
                            value="{{ $minimumBid }}"
                            required
                            class="mt-1.5 w-full min-h-[46px] rounded-xl border border-slate-300 px-4 py-3 text-base font-bold text-slate-900 outline-none transition focus:border-blue-600 focus:ring-4 focus:ring-blue-100"
                        >
                    </div>

                    <div class="space-y-1 text-xs leading-relaxed text-slate-600">
                        <p>
                            Kelipatan tawaran: <strong class="text-slate-800">Rp {{ number_format($auction->bid_increment, 0, ',', '.') }}</strong>.
                        </p>

                        @if ($bidCap > 0)
                            <p>
                                Batas maksimal / menang instan (buy-out): <strong class="text-slate-800">Rp {{ number_format($bidCap, 0, ',', '.') }}</strong>.
                            </p>
                        @endif
                    </div>

                    <p
                        id="bid-form-error"
                        class="hidden rounded-xl bg-rose-50 border border-rose-200 px-3 py-2.5 text-xs sm:text-sm font-semibold text-rose-800"
                        role="alert"
                    ></p>

                    <div class="flex gap-3 pt-1">
                        <button
                            type="button"
                            id="cancel-bid-modal"
                            class="flex-1 min-h-[46px] rounded-xl border border-slate-300 px-4 py-3 text-sm font-bold text-slate-700 transition hover:bg-slate-50 cursor-pointer focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-slate-600"
                        >
                            Batal
                        </button>

                        <button
                            type="submit"
                            id="submit-bid-button"
                            class="flex-1 min-h-[46px] rounded-xl bg-blue-600 px-4 py-3 text-sm font-bold text-white transition hover:bg-blue-700 disabled:cursor-not-allowed disabled:opacity-60 cursor-pointer focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600"
                        >
                            Kirim Penawaran
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

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
            const openButtonMobile = document.getElementById('open-bid-modal-mobile');
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

            document.querySelectorAll('.js-quick-bid').forEach((btn) => {
                btn.addEventListener('click', () => {
                    if (input && btn.dataset.quickBid) {
                        input.value = btn.dataset.quickBid;
                        input.focus();
                    }
                });
            });

            const showFeedback = (message, success = false) => {
                if (!feedback) return;

                feedback.textContent = message;
                feedback.className = success
                    ? 'rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-800'
                    : 'rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-semibold text-rose-800';
            };

            const closeAuctionUI = () => {
                root.dataset.auctionActive = 'false';

                const badge = document.getElementById('auction-status-badge');

                if (badge) {
                    badge.textContent = 'Lelang Ditutup';
                    badge.className =
                        'absolute left-4 top-4 rounded-full bg-slate-800/95 px-3.5 py-1.5 text-xs font-bold text-white shadow-2xs';
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
                        '<div class="rounded-xl bg-slate-100 border border-slate-200 px-4 py-3 text-center text-sm font-bold text-slate-700">Lelang Ditutup</div>';
                }

                const mobileStickyAction = document.getElementById('mobile-sticky-action');
                if (mobileStickyAction) {
                    mobileStickyAction.innerHTML =
                        '<span class="shrink-0 px-3 py-2 rounded-xl bg-slate-100 border border-slate-200 text-slate-700 font-bold text-xs">Lelang Ditutup</span>';
                }

                closeModal();
            };

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

            openButton?.addEventListener('click', openModal);
            openButtonMobile?.addEventListener('click', openModal);

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
                        const formattedHighest = 'Rp ' + Number(result.highest_bid).toLocaleString('id-ID');

                        document.getElementById('current-price').textContent = formattedHighest;
                        const mobilePriceEl = document.getElementById('mobile-sticky-price');
                        if (mobilePriceEl) {
                            mobilePriceEl.textContent = formattedHighest;
                        }

                        document.getElementById('minimum-bid-label').textContent =
                            'Rp ' + Number(result.min_next_bid)
                                .toLocaleString('id-ID');

                        input.min = result.min_next_bid;
                        input.value = result.min_next_bid;

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
                            'truncate text-sm font-bold text-slate-900';
                        name.textContent = result.bidder?.name || 'Mahasiswa';

                        const date = document.createElement('p');
                        date.className = 'mt-0.5 text-[11px] font-medium text-slate-600';
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

                        if (result.is_closed) {
                            closeAuctionUI();
                        } else {
                            const actionArea =
                                document.getElementById('bid-action-area');

                            if (actionArea) {
                                actionArea.innerHTML =
                                    '<div class="rounded-xl bg-blue-50 border border-blue-200 px-4 py-3 text-center text-sm font-semibold text-blue-800">Kamu sedang menjadi penawar tertinggi. Tunggu penawaran peserta lain.</div>';
                            }

                            const mobileStickyAction = document.getElementById('mobile-sticky-action');
                            if (mobileStickyAction) {
                                mobileStickyAction.innerHTML =
                                    '<span class="shrink-0 px-3 py-2 rounded-xl bg-blue-50 border border-blue-200 text-blue-800 font-bold text-xs">Penawar Tertinggi</span>';
                            }

                            openButton?.remove();
                            openButtonMobile?.remove();
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
