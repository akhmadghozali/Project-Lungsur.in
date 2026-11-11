<x-layouts.app>
    <x-slot:title>
        Buka Sesi Lelang - {{ $listing->title }} - Lungsur.in
    </x-slot:title>

    @php
        $initialStartPrice = old('starting_price', (int) $listing->price);
        $initialBidIncrement = old('bid_increment', 5000);
        $initialBidCap = old('bid_cap', '');
    @endphp

    <div class="max-w-4xl mx-auto py-4 sm:py-8 px-4"
         x-data="{
            startingPrice: @js((string) $initialStartPrice),
            bidIncrement: @js((string) $initialBidIncrement),
            previewUrl: null
         }">

        {{-- Breadcrumb --}}
        <nav class="flex items-center gap-2 text-xs text-slate-500 mb-6">
            <a href="{{ route('dashboard') }}" class="hover:text-blue-600 transition">Dashboard</a>
            <span>/</span>
            <a href="{{ route('auctions.index') }}" class="hover:text-blue-600 transition">Lelang</a>
            <span>/</span>
            <a href="{{ route('auctions.create') }}" class="hover:text-blue-600 transition">Pilih Barang</a>
            <span>/</span>
            <span class="text-slate-800 font-semibold truncate">{{ $listing->title }}</span>
        </nav>

        {{-- Header Banner --}}
        <div class="bg-gradient-to-r from-emerald-600 via-teal-600 to-emerald-700 rounded-3xl p-6 sm:p-8 text-white shadow-xl shadow-emerald-600/20 mb-8 relative overflow-hidden">
            <div class="relative z-10 max-w-xl">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-white/20 backdrop-blur-md text-white text-xs font-bold mb-3">
                    🔨 Fitur Lelang Komunitas Mahasiswa &bull; Durasi {{ $durationDays }} Hari
                </span>
                <h1 class="text-2xl sm:text-3xl font-black tracking-tight mb-2">
                    Buka Sesi Lelang Barangmu!
                </h1>
                <p class="text-emerald-50 text-sm leading-relaxed">
                    Biarkan sesama mahasiswa menawar barangmu dengan harga terbaik. Cukup biaya buka sesi lelang sebesar <strong>Rp {{ number_format($openingFee, 0, ',', '.') }}</strong> via QRIS Statis.
                </p>
            </div>
            <div class="absolute right-4 -bottom-6 text-white/10 text-9xl font-black select-none pointer-events-none hidden md:block">
                🔨
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            {{-- Kolom Kiri: Preview Barang & Info Paket Lelang --}}
            <div class="lg:col-span-5 space-y-6">
                {{-- Preview Barang --}}
                <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs">
                    <div class="flex items-center justify-between mb-3">
                        <h2 class="text-xs font-bold uppercase tracking-wider text-slate-400">
                            Barang yang Akan Dilelang
                        </h2>
                        <a href="{{ route('auctions.create') }}" class="text-[11px] font-bold text-blue-600 hover:underline">
                            Ganti Produk &rarr;
                        </a>
                    </div>

                    <div class="flex gap-3.5 items-center">
                        <div class="w-16 h-16 rounded-xl bg-slate-100 border border-slate-200 overflow-hidden shrink-0">
                            @if ($listing->photo_url)
                                <img src="{{ $listing->photo_url }}" alt="{{ $listing->title }}" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full flex items-center justify-center text-slate-400 text-xs">No Foto</div>
                            @endif
                        </div>
                        <div class="min-w-0 flex-1">
                            <span class="inline-block text-[10px] font-bold px-2 py-0.5 rounded bg-blue-50 text-blue-700 mb-1">
                                {{ $listing->category->name ?? 'Kategori' }}
                            </span>
                            <h3 class="font-bold text-sm text-slate-800 truncate" title="{{ $listing->title }}">
                                {{ $listing->title }}
                            </h3>
                            <p class="text-xs font-black text-blue-600 mt-0.5">
                                Harga Jual: Rp {{ number_format($listing->price, 0, ',', '.') }}
                            </p>
                        </div>
                    </div>

                    @if ($listing->auction?->latestPayment?->isRejected())
                        <div class="mt-4 rounded-xl border border-rose-200 bg-rose-50 p-3 text-xs text-rose-800">
                            <div class="font-bold mb-0.5">Pembayaran sebelumnya ditolak Admin:</div>
                            <p class="text-[11px] text-rose-700">{{ $listing->auction->latestPayment->rejection_note }}</p>
                        </div>
                    @endif
                </div>

                {{-- Rincian Biaya & Keuntungan Lelang --}}
                <div class="bg-gradient-to-br from-slate-900 to-slate-800 text-white rounded-2xl p-6 shadow-md">
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <span class="text-xs font-bold text-emerald-400 uppercase tracking-wider">Biaya Pembukaan Sesi</span>
                            <h3 class="text-xl font-black mt-1">Buka Lelang {{ $durationDays }} Hari</h3>
                        </div>
                        <span class="px-2.5 py-1 rounded-full bg-emerald-400 text-slate-950 text-xs font-black">
                            Fix {{ $durationDays }} Hari
                        </span>
                    </div>

                    <div class="mb-6">
                        <span class="text-3xl font-black text-white">Rp {{ number_format($openingFee, 0, ',', '.') }}</span>
                        <span class="text-slate-400 text-xs">/ 1 sesi lelang ({{ $durationDays }} hari)</span>
                    </div>

                    <ul class="space-y-2.5 text-xs text-slate-300">
                        <li class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                            </svg>
                            <span>Durasi tetap sistem <strong>{{ $durationDays }} hari penuh</strong></span>
                        </li>
                        <li class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                            </svg>
                            <span>Tampil di halaman Papan Lelang Mahasiswa</span>
                        </li>
                        <li class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                            </svg>
                            <span>Sistem penawaran real-time &amp; fitur Anti-Sniping</span>
                        </li>
                        <li class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                            </svg>
                            <span>Countdown dimulai setelah diverifikasi Admin</span>
                        </li>
                    </ul>
                </div>
            </div>

            {{-- Kolom Kanan: Form Aturan Lelang & Pembayaran QRIS --}}
            <div class="lg:col-span-7">
                <div class="bg-white rounded-3xl border border-slate-200/90 p-6 sm:p-8 shadow-sm">
                    <form action="{{ route('auctions.payment.store', $listing->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                        @csrf
                        <input type="hidden" name="payment_channel" value="qris">

                        {{-- Step 1: Pengaturan Sesi Lelang --}}
                        <div class="space-y-4">
                            <div class="border-b border-slate-100 pb-3">
                                <h2 class="text-lg font-bold text-slate-900">1. Pengaturan Sesi Lelang</h2>
                                <p class="text-xs text-slate-500 mt-0.5">Tentukan harga pembuka, kelipatan penawaran, dan harga Buy-Out.</p>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                {{-- Harga Awal --}}
                                <div>
                                    <label for="starting_price" class="block text-xs font-bold text-slate-700 mb-1.5">
                                        Harga Awal Lelang (Rp) <span class="text-rose-500">*</span>
                                    </label>
                                    <input type="number"
                                           name="starting_price"
                                           id="starting_price"
                                           x-model="startingPrice"
                                           min="1000"
                                           step="1000"
                                           required
                                           placeholder="Contoh: 50000"
                                           class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-xs font-bold text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                                    <p class="text-[11px] text-slate-400 mt-1">Kelipatan Rp 1.000 (min. Rp 1.000)</p>
                                    @error('starting_price')
                                        <p class="text-rose-600 text-xs mt-1 font-semibold">{{ $message }}</p>
                                    @enderror
                                </div>

                                {{-- Kelipatan Bid --}}
                                <div>
                                    <label for="bid_increment" class="block text-xs font-bold text-slate-700 mb-1.5">
                                        Kelipatan Penawaran (Rp) <span class="text-rose-500">*</span>
                                    </label>
                                    <input type="number"
                                           name="bid_increment"
                                           id="bid_increment"
                                           x-model="bidIncrement"
                                           min="1000"
                                           step="1000"
                                           required
                                           placeholder="Contoh: 5000"
                                           class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-xs font-bold text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                                    <div class="flex flex-wrap gap-1.5 mt-1.5">
                                        @foreach ([1000, 5000, 10000, 25000] as $inc)
                                            <button type="button"
                                                    @click="bidIncrement = '{{ $inc }}'"
                                                    class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-slate-100 hover:bg-emerald-100 text-slate-600 hover:text-emerald-700 transition cursor-pointer">
                                                Rp {{ number_format($inc, 0, ',', '.') }}
                                            </button>
                                        @endforeach
                                    </div>
                                    @error('bid_increment')
                                        <p class="text-rose-600 text-xs mt-1 font-semibold">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                {{-- Harga Buy-Out (Wajib Diisi) --}}
                                <div>
                                    <label for="bid_cap" class="block text-xs font-bold text-slate-700 mb-1.5">
                                        Harga Buy-Out / Maksimal (Rp) <span class="text-rose-500">*</span>
                                    </label>
                                    <input type="number"
                                           name="bid_cap"
                                           id="bid_cap"
                                           value="{{ $initialBidCap }}"
                                           min="2000"
                                           step="1000"
                                           required
                                           placeholder="Contoh: 250000"
                                           class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-xs font-bold text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                                    <p class="text-[11px] text-slate-400 mt-1">Wajib diisi &gt; harga awal (kelipatan Rp 1.000).</p>
                                    @error('bid_cap')
                                        <p class="text-rose-600 text-xs mt-1 font-semibold">{{ $message }}</p>
                                    @enderror
                                </div>

                                {{-- Durasi Lelang (Ditentukan Sistem Fix 7 Hari) --}}
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-1.5">
                                        Durasi Sesi Lelang
                                    </label>
                                    <div class="w-full rounded-xl border border-emerald-200 bg-emerald-50/70 px-3.5 py-2.5 text-xs font-black text-emerald-800 flex items-center justify-between">
                                        <span>⏱ {{ $durationDays }} Hari Penuh</span>
                                        <span class="text-[10px] font-bold uppercase px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-700">Fix Sistem</span>
                                    </div>
                                    <p class="text-[11px] text-slate-400 mt-1">Ditentukan sistem sejak disetujui Admin.</p>
                                </div>
                            </div>
                        </div>

                        {{-- Step 2: Instruksi Pembayaran QRIS Statis (Rp 5.000) --}}
                        <div class="space-y-4 pt-2 border-t border-slate-100">
                            <div>
                                <h2 class="text-lg font-bold text-slate-900">2. Pembayaran Biaya Buka Lelang (QRIS Statis)</h2>
                                <p class="text-xs text-slate-500 mt-0.5">
                                    Scan kode QRIS di bawah ini dan bayar tepat senilai <strong>Rp {{ number_format($openingFee, 0, ',', '.') }}</strong>.
                                </p>
                            </div>

                            <x-qris-static-box :amount="5000" accent="emerald" />

                            {{-- Banner Metode Pembayaran --}}
                            <div class="rounded-2xl bg-emerald-50/80 border border-emerald-200/70 p-3.5 flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-xl bg-emerald-600 text-white flex items-center justify-center font-bold text-sm shadow-xs">
                                        🔨
                                    </div>
                                    <div>
                                        <div class="text-xs font-bold text-slate-800">Metode Pembayaran</div>
                                        <div class="text-[11px] text-slate-600 font-medium">QRIS Statis Lungsur.in (Semua Bank &amp; e-Wallet)</div>
                                    </div>
                                </div>
                                <span class="px-2.5 py-1 rounded-full bg-emerald-100 text-emerald-700 text-[10px] font-black uppercase tracking-wider">
                                    QRIS Statis
                                </span>
                            </div>

                            {{-- Upload Bukti Pembayaran --}}
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-2">
                                    Unggah Foto Struk / Screenshot Bukti Pembayaran QRIS <span class="text-rose-500">*</span>
                                </label>

                                <div class="border-2 border-dashed border-slate-300 rounded-2xl p-4 sm:p-6 text-center hover:border-emerald-400 transition bg-slate-50/50">
                                    <template x-if="previewUrl">
                                        <div class="mb-4">
                                            <img :src="previewUrl" alt="Pratinjau Struk" class="max-h-48 mx-auto rounded-xl shadow-md border border-slate-200 object-contain">
                                            <button type="button" @click="previewUrl = null; $refs.fileInput.value = ''" class="mt-2 text-xs font-bold text-rose-600 hover:text-rose-700 hover:underline">
                                                Ganti Foto
                                            </button>
                                        </div>
                                    </template>

                                    <div x-show="!previewUrl">
                                        <svg class="w-10 h-10 mx-auto text-slate-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                        <div class="text-xs text-slate-600 font-medium mb-1">
                                            Klik untuk memilih berkas struk pembayaran QRIS
                                        </div>
                                        <div class="text-[11px] text-slate-400 mb-3">
                                            JPG, PNG, atau WEBP (Maksimal 2MB)
                                        </div>
                                    </div>

                                    <input type="file"
                                           name="proof_image"
                                           id="proof_image"
                                           x-ref="fileInput"
                                           accept="image/png,image/jpeg,image/webp"
                                           required
                                           @change="const file = $event.target.files[0]; if(file) { previewUrl = URL.createObjectURL(file); }"
                                           class="text-xs text-slate-500 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-emerald-100 file:text-emerald-800 hover:file:bg-emerald-200 cursor-pointer">
                                </div>
                                @error('proof_image')
                                    <p class="text-rose-600 text-xs mt-1 font-semibold">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        {{-- Submit Button --}}
                        <div class="pt-2 flex flex-col sm:flex-row items-center gap-3">
                            <button type="submit"
                                    class="w-full sm:flex-1 py-3 px-6 rounded-2xl bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white font-bold text-sm tracking-wide shadow-md shadow-emerald-600/20 transition cursor-pointer flex items-center justify-center gap-2">
                                <span>🔨 Kirim Bukti QRIS &amp; Ajukan Buka Lelang (Rp {{ number_format($openingFee, 0, ',', '.') }})</span>
                            </button>
                            <a href="{{ route('auctions.create') }}"
                               class="w-full sm:w-auto py-3 px-5 rounded-2xl border border-slate-200 text-slate-600 hover:bg-slate-50 font-bold text-xs text-center transition">
                                Kembali
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
