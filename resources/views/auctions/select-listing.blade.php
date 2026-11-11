<x-layouts.app>
    <x-slot:title>
        Pilih Produk untuk Dilelang - Lungsur.in
    </x-slot:title>

    <div class="max-w-6xl mx-auto py-4 sm:py-8 px-4 space-y-6">
        {{-- Breadcrumb --}}
        <nav class="flex items-center gap-2 text-xs text-slate-500">
            <a href="{{ route('auctions.index') }}" class="hover:text-blue-600 transition">Lelang</a>
            <span>/</span>
            <span class="text-slate-800 font-semibold">Pilih Barang untuk Dilelang</span>
        </nav>

        {{-- Header Banner --}}
        <div class="bg-gradient-to-r from-emerald-600 via-teal-600 to-emerald-700 rounded-3xl p-6 sm:p-8 text-white shadow-xl shadow-emerald-600/20 flex flex-col sm:flex-row sm:items-center justify-between gap-6 relative overflow-hidden">
            <div class="relative z-10 max-w-xl">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-white/20 backdrop-blur-md text-white text-xs font-bold mb-3">
                    🔨 Langkah 1 dari 2 &bull; Biaya Buka Lelang Rp {{ number_format($openingFee, 0, ',', '.') }}
                </span>
                <h1 class="text-2xl sm:text-3xl font-black tracking-tight mb-2">
                    Pilih Barang yang Ingin Kamu Lelang
                </h1>
                <p class="text-emerald-50 text-xs sm:text-sm leading-relaxed">
                    Pilih salah satu barang dagangan yang sudah kamu unggah di bawah ini untuk dijadikan sesi lelang selama <strong>{{ $durationDays }} hari penuh</strong>.
                </p>
            </div>

            <div class="relative z-10 shrink-0">
                <a href="{{ route('katalog.pasang') }}"
                   class="inline-flex items-center gap-2 px-5 py-3 rounded-2xl bg-white text-emerald-700 hover:bg-emerald-50 font-black text-xs sm:text-sm shadow-md transition">
                    <span>+ Upload Barang Baru</span>
                </a>
            </div>
        </div>

        @if ($myListings->isEmpty())
            <div class="bg-white rounded-3xl border border-dashed border-slate-300 p-12 text-center space-y-4">
                <div class="w-14 h-14 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center mx-auto text-2xl">
                    📦
                </div>
                <div class="space-y-1 max-w-md mx-auto">
                    <h2 class="text-lg font-black text-slate-900">Kamu Belum Mengunggah Barang</h2>
                    <p class="text-xs sm:text-sm text-slate-500">
                        Unggah iklan barangmu terlebih dahulu, lalu pilih barang tersebut untuk membuka sesi lelang.
                    </p>
                </div>
                <div class="pt-2">
                    <a href="{{ route('katalog.pasang') }}"
                       class="inline-flex items-center gap-2 px-6 py-3 rounded-2xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-md transition">
                        <span>+ Pasang Barang Sekarang</span>
                    </a>
                </div>
            </div>
        @else
            <div class="flex flex-wrap items-center justify-between gap-2 text-xs text-slate-500">
                <span>
                    Menampilkan <strong class="text-slate-800">{{ $myListings->count() }}</strong> barang yang sudah diupload
                    (<strong class="text-emerald-700">{{ $eligibleCount }}</strong> siap dilelang)
                </span>
                <a href="{{ route('auctions.index') }}" class="font-bold text-blue-600 hover:underline">
                    &larr; Kembali ke Papan Lelang
                </a>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                @foreach ($myListings as $item)
                    @php
                        $auction = $item->auction;
                        $latestPayment = $auction?->latestPayment;
                        $isActiveAuction = $auction && $auction->status === 'active';
                        $isPendingApproval = $auction && ($auction->status === 'pending_approval' || ($latestPayment && $latestPayment->isPending()));
                        $isClosedAuction = $auction && $auction->status === 'closed';
                        $isRejectedPayment = $auction && $latestPayment && $latestPayment->isRejected();
                        $isEligible = $item->status === 'available' && ! $isActiveAuction && ! $isPendingApproval && ! $isClosedAuction;
                    @endphp

                    <div class="bg-white rounded-2xl border {{ $isEligible ? 'border-slate-200 hover:border-emerald-400 hover:shadow-md' : 'border-slate-200/70 opacity-85' }} overflow-hidden flex flex-col justify-between transition">
                        <div>
                            {{-- Foto Barang --}}
                            <div class="aspect-[4/3] bg-slate-100 relative overflow-hidden">
                                @if ($item->photo_url)
                                    <img src="{{ $item->photo_url }}" alt="{{ $item->title }}" class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-xs text-slate-400">
                                        Belum ada foto
                                    </div>
                                @endif

                                {{-- Status Pill --}}
                                <div class="absolute top-3 left-3">
                                    @if ($isActiveAuction)
                                        <span class="px-2.5 py-1 rounded-full bg-teal-600 text-white text-[10px] font-black uppercase shadow-xs">
                                            🔨 Sedang Dilelang
                                        </span>
                                    @elseif ($isPendingApproval)
                                        <span class="px-2.5 py-1 rounded-full bg-amber-500 text-white text-[10px] font-black uppercase shadow-xs">
                                            ⏳ Menunggu Verifikasi Admin
                                        </span>
                                    @elseif ($isClosedAuction)
                                        <span class="px-2.5 py-1 rounded-full bg-slate-700 text-white text-[10px] font-black uppercase shadow-xs">
                                            Lelang Selesai
                                        </span>
                                    @elseif ($item->status === 'available')
                                        <span class="px-2.5 py-1 rounded-full bg-emerald-600 text-white text-[10px] font-black uppercase shadow-xs">
                                            Siap Dilelang
                                        </span>
                                    @else
                                        <span class="px-2.5 py-1 rounded-full bg-slate-600 text-white text-[10px] font-black uppercase shadow-xs">
                                            {{ ucfirst($item->status) }}
                                        </span>
                                    @endif
                                </div>
                            </div>

                            {{-- Detail Barang --}}
                            <div class="p-4 space-y-2">
                                <div class="flex items-center justify-between text-[11px] text-slate-400">
                                    <span class="font-semibold text-blue-600 bg-blue-50 px-2 py-0.5 rounded-md">
                                        {{ $item->category->name ?? 'Kategori' }}
                                    </span>
                                    <span>{{ $item->created_at->diffForHumans() }}</span>
                                </div>

                                <h3 class="font-bold text-sm text-slate-900 line-clamp-2 leading-snug">
                                    {{ $item->title }}
                                </h3>

                                <p class="text-base font-black text-blue-600">
                                    Rp {{ number_format($item->price, 0, ',', '.') }}
                                </p>

                                @if ($isRejectedPayment && $isEligible)
                                    <div class="rounded-xl bg-rose-50 border border-rose-200 px-3 py-2 text-[11px] text-rose-700">
                                        <span class="font-bold">Ditolak:</span> {{ $latestPayment->rejection_note }}
                                    </div>
                                @endif
                            </div>
                        </div>

                        {{-- Tombol Aksi --}}
                        <div class="p-4 pt-0">
                            @if ($isEligible)
                                <a href="{{ route('auctions.payment.create', $item->id) }}"
                                   class="w-full py-2.5 px-4 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-xs transition flex items-center justify-center gap-1.5">
                                    <span>🔨 {{ $isRejectedPayment ? 'Ajukan Ulang Lelang (Rp5.000)' : 'Pilih & Buka Lelang (Rp5.000)' }}</span>
                                    <span>&rarr;</span>
                                </a>
                            @elseif ($isActiveAuction || $isClosedAuction)
                                <a href="{{ route('auctions.show', $auction->id) }}"
                                   class="w-full py-2.5 px-4 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition flex items-center justify-center gap-1.5">
                                    <span>Lihat Sesi Lelang</span>
                                    <span>&rarr;</span>
                                </a>
                            @elseif ($isPendingApproval)
                                <div class="w-full py-2.5 px-4 rounded-xl bg-amber-50 border border-amber-200 text-amber-800 font-bold text-xs text-center">
                                    ⏳ Sedang Diverifikasi Admin
                                </div>
                            @else
                                <div class="w-full py-2.5 px-4 rounded-xl bg-slate-100 text-slate-400 font-bold text-xs text-center">
                                    Tidak Tersedia ({{ ucfirst($item->status) }})
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</x-layouts.app>

