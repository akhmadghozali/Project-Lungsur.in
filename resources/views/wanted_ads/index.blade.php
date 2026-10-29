<x-layouts.app>
    <x-slot:title>
        Papan Barang Dicari (Wanted Ads) - Lungsur.in
    </x-slot:title>

    <div class="space-y-8 sm:space-y-10" x-data="{ createModalOpen: {{ $errors->any() ? 'true' : 'false' }} }">

        <!-- ========================================== -->
        <!-- 1. HERO BANNER PAPAN BARANG DICARI         -->
        <!-- ========================================== -->
        <section class="bg-gradient-to-br from-indigo-50/90 via-sky-50/50 to-blue-50/70 rounded-3xl border border-indigo-100/60 p-6 sm:p-10 relative overflow-hidden shadow-xs">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center relative z-10">
                <div class="lg:col-span-8 space-y-4">
                    <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full text-xs font-semibold bg-indigo-100/80 text-indigo-700 border border-indigo-200/60">
                        <svg class="w-3.5 h-3.5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span>Papan Permintaan Kebutuhan Mahasiswa (Wanted Board)</span>
                    </div>

                    <h1 class="text-2xl sm:text-4xl font-black text-slate-900 tracking-tight leading-snug">
                        Lagi Cari Perlengkapan Kos? <br class="hidden sm:inline">
                        <span class="text-blue-600">Umumkan di Sini</span> dan Temukan Penjualnya!
                    </h1>

                    <p class="text-xs sm:text-sm text-slate-600 leading-relaxed max-w-2xl">
                        Tidak menemukan perabot atau perlengkapan kos yang cocok di katalog? Pasang pengumuman barang yang kamu cari secara <strong>gratis</strong>. Sesama mahasiswa yang punya barang tersebut akan langsung menghubungimu.
                    </p>

                    <div class="pt-2 flex flex-wrap items-center gap-3">
                        @auth
                            <button type="button" 
                                    @click="createModalOpen = true"
                                    class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-xs sm:text-sm font-bold px-6 py-3 rounded-2xl shadow-md shadow-blue-500/25 transition cursor-pointer">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                                </svg>
                                <span>+ Pasang Kebutuhan Baru</span>
                            </button>
                        @else
                            <a href="{{ route('login') }}" 
                               class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-xs sm:text-sm font-bold px-6 py-3 rounded-2xl shadow-md shadow-blue-500/25 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                                </svg>
                                <span>Masuk untuk Pasang Kebutuhan</span>
                            </a>
                        @endauth
                        <a href="{{ url('/') }}" 
                           class="inline-flex items-center gap-2 bg-white hover:bg-slate-50 text-slate-700 text-xs sm:text-sm font-bold px-5 py-3 rounded-2xl border border-slate-200 shadow-2xs transition">
                            <span>Jelajahi Katalog Barang Dijual</span>
                            <span>&rarr;</span>
                        </a>
                    </div>
                </div>

                <div class="lg:col-span-4 hidden lg:flex justify-end">
                    <div class="bg-white/90 backdrop-blur-xs p-5 rounded-3xl border border-indigo-100 shadow-sm space-y-3.5 max-w-xs">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-2xl bg-indigo-100 text-indigo-600 flex items-center justify-center font-bold text-lg shrink-0">
                                📢
                            </div>
                            <div class="text-xs">
                                <div class="font-bold text-slate-900">Publikasi Terbuka</div>
                                <div class="text-slate-500">Bisa dilihat oleh seluruh penjual kos</div>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-2xl bg-emerald-100 text-emerald-600 flex items-center justify-center font-bold text-lg shrink-0">
                                💰
                            </div>
                            <div class="text-xs">
                                <div class="font-bold text-slate-900">Tentukan Pagu Anggaran</div>
                                <div class="text-slate-500">Sesuai kemampuan kantong mahasiswa</div>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-2xl bg-blue-100 text-blue-600 flex items-center justify-center font-bold text-lg shrink-0">
                                🔒
                            </div>
                            <div class="text-xs">
                                <div class="font-bold text-slate-900">100% Tanpa Biaya</div>
                                <div class="text-slate-500">Pemasangan gratis tanpa potongan</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ========================================== -->
        <!-- 2. PENCARIAN & FILTER KATEGORI KEBUTUHAN   -->
        <!-- ========================================== -->
        <section class="space-y-4">
            <div class="bg-white rounded-2xl border border-slate-200/80 p-4 sm:p-5 shadow-xs">
                <form action="{{ route('wanted-ads.index') }}" method="GET" class="flex flex-col md:flex-row items-center gap-3">
                    <!-- Search Input -->
                    <div class="relative flex-1 w-full">
                        <svg class="w-4 h-4 text-slate-400 absolute left-3.5 top-3 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <input type="text" 
                               name="q" 
                               value="{{ $search ?? '' }}" 
                               placeholder="Cari barang yang sedang dicari mahasiswa (contoh: meja lipat, kasur busa)..." 
                               class="w-full pl-10 pr-4 py-2.5 text-xs sm:text-sm rounded-xl border border-slate-200 bg-slate-50/70 text-slate-800 placeholder-slate-400 focus:bg-white focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-100">
                    </div>

                    <!-- Category Select -->
                    <div class="w-full md:w-56 shrink-0">
                        <select name="category" class="w-full py-2.5 px-3 text-xs sm:text-sm rounded-xl border border-slate-200 bg-slate-50/70 text-slate-700 focus:bg-white focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-100">
                            <option value="">Semua Kategori</option>
                            @foreach ($categories as $cat)
                                <option value="{{ $cat->slug }}" {{ ($categoryParam ?? '') === $cat->slug ? 'selected' : '' }}>
                                    {{ $cat->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex items-center gap-2 w-full md:w-auto shrink-0">
                        <button type="submit" 
                                class="flex-1 md:flex-initial inline-flex items-center justify-center gap-2 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs sm:text-sm px-6 py-2.5 rounded-xl shadow-xs transition cursor-pointer">
                            <span>Cari</span>
                        </button>
                        @if (!empty($search) || !empty($categoryParam))
                            <a href="{{ route('wanted-ads.index') }}" 
                               class="inline-flex items-center justify-center text-xs font-semibold text-rose-600 hover:text-rose-700 hover:bg-rose-50 px-3.5 py-2.5 rounded-xl border border-rose-200 transition">
                                Reset
                            </a>
                        @endif
                    </div>
                </form>
            </div>
        </section>

        <!-- ========================================== -->
        <!-- 3. DAFTAR KARTU BARANG DICARI              -->
        <!-- ========================================== -->
        <section class="space-y-6">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div>
                    <h2 class="text-xl font-black text-slate-900 tracking-tight flex items-center gap-2">
                        <span>Daftar Kebutuhan Terkini</span>
                        <span class="text-xs font-semibold px-2.5 py-0.5 rounded-full bg-blue-100 text-blue-700">
                            {{ $wantedAds->total() }} Permintaan
                        </span>
                    </h2>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Punya barang yang dicari di bawah ini? Hubungi pencarinya untuk menawarkan barang jualanmu!
                    </p>
                </div>

                @auth
                    <button type="button" 
                            @click="createModalOpen = true" 
                            class="inline-flex items-center gap-1.5 text-xs font-bold text-blue-600 hover:text-blue-700 bg-blue-50 hover:bg-blue-100/80 px-3.5 py-2 rounded-xl transition self-start sm:self-auto cursor-pointer">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                        </svg>
                        <span>Pasang Iklan Kebutuhan</span>
                    </button>
                @endauth
            </div>

            <!-- Grid Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                @forelse ($wantedAds as $ad)
                    <article class="bg-white rounded-2xl border border-slate-200/80 shadow-2xs hover:shadow-md transition p-5 flex flex-col justify-between space-y-4 group">
                        <!-- Top Metadata -->
                        <div class="space-y-2.5">
                            <div class="flex items-center justify-between gap-2 text-xs">
                                <span class="inline-flex items-center gap-1 font-semibold text-blue-700 bg-blue-50 px-2.5 py-0.5 rounded-lg border border-blue-200/50 text-[11px]">
                                    {{ $ad->category->name ?? 'Lainnya' }}
                                </span>
                                <span class="text-slate-400 text-[11px]">
                                    {{ $ad->created_at->diffForHumans() }}
                                </span>
                            </div>

                            <!-- Judul Barang yang Dicari -->
                            <h3 class="text-base font-bold text-slate-900 group-hover:text-blue-600 transition line-clamp-2">
                                {{ $ad->title }}
                            </h3>

                            <!-- Deskripsi Kriteria -->
                            <p class="text-xs text-slate-600 leading-relaxed line-clamp-3">
                                {{ $ad->description }}
                            </p>
                        </div>

                        <!-- Budget Box & Pencari Info -->
                        <div class="space-y-3 pt-3 border-t border-slate-100">
                            <!-- Budget Max Box -->
                            <div class="bg-emerald-50/70 border border-emerald-200/60 rounded-xl px-3.5 py-2 flex items-center justify-between">
                                <span class="text-[11px] font-semibold text-emerald-800">Pagu Anggaran Maks:</span>
                                <span class="text-sm font-black text-emerald-700">
                                    Rp {{ number_format($ad->budget_max, 0, ',', '.') }}
                                </span>
                            </div>

                            <!-- User Info & Area Kos -->
                            <div class="flex items-center justify-between text-xs text-slate-500 gap-2">
                                <div class="flex items-center gap-2 min-w-0">
                                    <div class="w-7 h-7 rounded-full bg-slate-900 text-white font-bold flex items-center justify-center text-[11px] shrink-0">
                                        {{ strtoupper(substr($ad->user->name ?? 'U', 0, 1)) }}
                                    </div>
                                    <div class="min-w-0">
                                        <div class="font-bold text-slate-800 truncate text-[11px]">{{ $ad->user->name ?? 'Mahasiswa' }}</div>
                                        <div class="text-[10px] text-blue-600 truncate">{{ $ad->user->campus ?? 'Mahasiswa Malang' }}</div>
                                    </div>
                                </div>

                                <div class="text-right text-[10px] text-slate-400 shrink-0 max-w-[120px]" title="{{ $ad->addresses }}">
                                    📍 <span class="truncate block">{{ $ad->addresses }}</span>
                                </div>
                            </div>

                            <!-- Action Button -->
                            <div class="pt-1 flex items-center gap-2">
                                @if (auth()->id() === $ad->user_id || (auth()->check() && auth()->user()->role === 'admin'))
                                    <form action="{{ route('wanted-ads.destroy', $ad->id) }}" method="POST" class="w-full" onsubmit="return confirm('Apakah Anda yakin ingin menghapus postingan kebutuhan ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="w-full py-2 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-600 font-bold text-xs transition cursor-pointer">
                                            Hapus Postingan Ini
                                        </button>
                                    </form>
                                @else
                                    <a href="{{ route('home', ['q' => $ad->title]) }}" 
                                       class="w-full text-center py-2 rounded-xl bg-blue-50 hover:bg-blue-600 text-blue-600 hover:text-white font-bold text-xs transition flex items-center justify-center gap-1.5 shadow-2xs">
                                        <span>Cek Barang Serupa di Katalog</span>
                                        <span>&rarr;</span>
                                    </a>
                                @endif
                            </div>
                        </div>
                    </article>
                @empty
                    <div class="col-span-full rounded-2xl border border-dashed border-slate-300 bg-white p-12 text-center space-y-3">
                        <div class="w-12 h-12 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center mx-auto text-xl font-bold">
                            📦
                        </div>
                        <h3 class="text-base font-bold text-slate-800">Belum Ada Permintaan Barang</h3>
                        <p class="text-xs text-slate-500 max-w-md mx-auto">
                            Saat ini belum ada pengumuman barang yang dicari mahasiswa. Jadilah yang pertama memasang kebutuhan perlengkapan kos!
                        </p>
                        @auth
                            <button type="button" 
                                    @click="createModalOpen = true" 
                                    class="inline-flex items-center gap-1.5 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs px-5 py-2.5 rounded-xl shadow-xs transition cursor-pointer">
                                <span>+ Pasang Kebutuhan Sekarang</span>
                            </button>
                        @endauth
                    </div>
                @endforelse
            </div>

            <!-- Pagination -->
            <div class="pt-4">
                {{ $wantedAds->links() }}
            </div>
        </section>

        <!-- ========================================== -->
        <!-- 4. MODAL FORM PASANG KEBUTUHAN BARU        -->
        <!-- ========================================== -->
        <div x-show="createModalOpen" 
             x-cloak 
             class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 p-4 overflow-y-auto"
             @keydown.escape.window="createModalOpen = false">
            <div class="bg-white rounded-3xl max-w-xl w-full p-6 sm:p-8 shadow-2xl relative my-8" 
                 @click.outside="createModalOpen = false">
                
                <!-- Close Button -->
                <button type="button" 
                        @click="createModalOpen = false" 
                        class="absolute right-5 top-5 text-slate-400 hover:text-slate-600 p-1.5 rounded-full hover:bg-slate-100 transition cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>

                <div class="mb-5">
                    <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-bold bg-blue-50 text-blue-700 mb-2">
                        <span>📢 Wanted Ads Mahasiswa</span>
                    </div>
                    <h3 class="text-xl font-black text-slate-900 tracking-tight">Pasang Iklan Kebutuhan Barang</h3>
                    <p class="text-xs text-slate-500 mt-1">
                        Tuliskan perlengkapan kos yang sedang kamu cari beserta batas pagu anggaran maksimalmu.
                    </p>
                </div>

                <form action="{{ route('wanted-ads.store') }}" method="POST" class="space-y-4">
                    @csrf

                    <!-- Judul Barang -->
                    <div>
                        <label for="title" class="block text-xs font-bold text-slate-700 mb-1">
                            Nama Barang yang Dicari <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" 
                               id="title" 
                               name="title" 
                               value="{{ old('title') }}" 
                               required 
                               maxlength="200" 
                               placeholder="Contoh: Meja Belajar Lipat Kayu Minimalis" 
                               class="w-full text-xs sm:text-sm px-3.5 py-2.5 rounded-xl border {{ $errors->has('title') ? 'border-rose-400 bg-rose-50/40' : 'border-slate-200 bg-slate-50/60' }} focus:bg-white focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-100">
                        @error('title')
                            <p class="text-rose-500 text-[11px] mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Kategori & Budget Max -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                        <!-- Kategori -->
                        <div>
                            <label for="category_id" class="block text-xs font-bold text-slate-700 mb-1">
                                Kategori <span class="text-rose-500">*</span>
                            </label>
                            <select id="category_id" 
                                    name="category_id" 
                                    required 
                                    class="w-full text-xs sm:text-sm px-3.5 py-2.5 rounded-xl border {{ $errors->has('category_id') ? 'border-rose-400 bg-rose-50/40' : 'border-slate-200 bg-slate-50/60' }} focus:bg-white focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-100">
                                <option value="">-- Pilih Kategori --</option>
                                @foreach ($categories as $cat)
                                    <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>
                                        {{ $cat->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <p class="text-rose-500 text-[11px] mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Pagu Anggaran Maksimal -->
                        <div>
                            <label for="budget_max" class="block text-xs font-bold text-slate-700 mb-1">
                                Pagu Anggaran Maks (Rp) <span class="text-rose-500">*</span>
                            </label>
                            <input type="number" 
                                   id="budget_max" 
                                   name="budget_max" 
                                   value="{{ old('budget_max') }}" 
                                   required 
                                   min="1000" 
                                   step="1000" 
                                   placeholder="Contoh: 150000" 
                                   class="w-full text-xs sm:text-sm px-3.5 py-2.5 rounded-xl border {{ $errors->has('budget_max') ? 'border-rose-400 bg-rose-50/40' : 'border-slate-200 bg-slate-50/60' }} focus:bg-white focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-100">
                            <span class="text-[10px] text-slate-400 mt-1 block">Wajib kelipatan bulat Rp1.000</span>
                            @error('budget_max')
                                <p class="text-rose-500 text-[11px] mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Area / Lokasi Kos -->
                    <div>
                        <label for="addresses" class="block text-xs font-bold text-slate-700 mb-1">
                            Area / Lokasi Kos Serah Terima <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" 
                               id="addresses" 
                               name="addresses" 
                               value="{{ old('addresses') }}" 
                               required 
                               maxlength="255" 
                               placeholder="Contoh: Kawasan Sigura-gura, dekat Gerbang Belakang UB / Polinema" 
                               class="w-full text-xs sm:text-sm px-3.5 py-2.5 rounded-xl border {{ $errors->has('addresses') ? 'border-rose-400 bg-rose-50/40' : 'border-slate-200 bg-slate-50/60' }} focus:bg-white focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-100">
                        @error('addresses')
                            <p class="text-rose-500 text-[11px] mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Deskripsi Spesifikasi / Kriteria -->
                    <div>
                        <label for="description" class="block text-xs font-bold text-slate-700 mb-1">
                            Deskripsi Spesifikasi yang Diinginkan <span class="text-rose-500">*</span>
                        </label>
                        <textarea id="description" 
                                  name="description" 
                                  rows="3" 
                                  required 
                                  placeholder="Jelaskan kriteria barang, seperti warna, ukuran, kondisi minimal yang bisa diterima, dsb..." 
                                  class="w-full text-xs sm:text-sm px-3.5 py-2.5 rounded-xl border {{ $errors->has('description') ? 'border-rose-400 bg-rose-50/40' : 'border-slate-200 bg-slate-50/60' }} focus:bg-white focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-100">{{ old('description') }}</textarea>
                        @error('description')
                            <p class="text-rose-500 text-[11px] mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-100">
                        <button type="button" 
                                @click="createModalOpen = false" 
                                class="px-4 py-2.5 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-100 transition cursor-pointer">
                            Batal
                        </button>
                        <button type="submit" 
                                class="px-6 py-2.5 rounded-xl text-xs font-bold text-white bg-blue-600 hover:bg-blue-700 shadow-md shadow-blue-500/25 transition cursor-pointer">
                            Publikasikan Kebutuhan (Gratis)
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</x-layouts.app>
