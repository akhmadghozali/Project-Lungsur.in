
<x-layouts.app>
    <x-slot name="title">Riwayat Transaksi – Lungsur.in</x-slot>

    <div class="max-w-7xl mx-auto px-4 py-8">
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-2xl font-bold text-slate-800">
                    Riwayat Transaksi
                </h1>
                <p class="text-slate-500 mt-1">
                    Daftar barang yang pernah kamu beli dan menangkan melalui lelang.
                </p>
            </div>
        </div>

        {{-- BARANG YANG DIBELI --}}
        <section class="mb-10">
            <h2 class="text-xl font-bold text-slate-800 mb-4">
                Barang yang Dibeli
            </h2>

            @forelse ($purchasedListings as $listing)
                <div class="bg-white rounded-2xl border border-slate-200 p-5 mb-4 flex flex-col sm:flex-row gap-5">
                    @if ($listing->images->isNotEmpty())
                        <img src="{{ asset('storage/' . $listing->images->first()->image_path) }}"
                             alt="{{ $listing->title }}"
                             class="w-full sm:w-32 h-32 object-cover rounded-xl">
                    @endif

                    <div>
                        <h3 class="font-bold text-slate-800">
                            {{ $listing->title }}
                        </h3>
                        <p class="text-blue-600 font-semibold mt-2">
                            Rp{{ number_format($listing->price, 0, ',', '.') }}
                        </p>
                        <p class="text-sm text-slate-500 mt-1">
                            Penjual: {{ $listing->user->name ?? '-' }}
                        </p>
                        <span class="inline-block mt-3 text-xs bg-green-100 text-green-700 px-3 py-1 rounded-full">
                            Terjual
                        </span>
                    </div>
                </div>
            @empty
                <div class="bg-white border border-slate-200 rounded-2xl p-8 text-center text-slate-500">
                    Belum ada riwayat pembelian.
                </div>
            @endforelse
        </section>

        {{-- LELANG YANG DIMENANGKAN --}}
        <section>
            <h2 class="text-xl font-bold text-slate-800 mb-4">
                Lelang yang Dimenangkan
            </h2>

            @forelse ($wonAuctions as $auction)
                <div class="bg-white rounded-2xl border border-slate-200 p-5 mb-4 flex flex-col sm:flex-row gap-5">
                    @if ($auction->listing && $auction->listing->images->isNotEmpty())
                        <img src="{{ asset('storage/' . $auction->listing->images->first()->image_path) }}"
                             alt="{{ $auction->listing->title }}"
                             class="w-full sm:w-32 h-32 object-cover rounded-xl">
                    @endif

                    <div>
                        <h3 class="font-bold text-slate-800">
                            {{ $auction->listing->title ?? 'Barang tidak tersedia' }}
                        </h3>

                        <p class="text-blue-600 font-semibold mt-2">
                            Rp{{ number_format($auction->highestBid->bid_amount ?? $auction->starting_price, 0, ',', '.') }}
                        </p>

                        <span class="inline-block mt-3 text-xs bg-green-100 text-green-700 px-3 py-1 rounded-full">
                            Pemenang Lelang
                        </span>
                    </div>
                </div>
            @empty
                <div class="bg-white border border-slate-200 rounded-2xl p-8 text-center text-slate-500">
                    Belum ada lelang yang dimenangkan.
                </div>
            @endforelse
        </section>
    </div>
</x-layouts.app>
