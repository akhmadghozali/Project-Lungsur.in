<x-layouts.app>
    <x-slot:title>
        Pasang Iklan Barang – Lungsur.in
    </x-slot:title>

    <div class="max-w-2xl mx-auto space-y-6 py-4 sm:py-6">

        {{-- ===================================== --}}
        {{-- Breadcrumb / Page Header              --}}
        {{-- ===================================== --}}
        <div class="space-y-1">
            <div class="flex items-center gap-2 text-xs text-slate-400 font-medium">
                <a href="{{ url('/') }}" class="hover:text-blue-600 transition">Beranda</a>
                <svg class="w-3.5 h-3.5 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
                <span class="text-slate-600">Pasang Iklan</span>
            </div>
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-blue-600 flex items-center justify-center shadow-md shadow-blue-500/20 shrink-0">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">Pasang Iklan Barang</h1>
                    <p class="text-xs text-slate-400 mt-0.5">Isi detail barang yang ingin kamu jual atau tukarkan</p>
                </div>
            </div>
        </div>

        {{-- ===================================== --}}
        {{-- Main Form (Alpine.js Component)       --}}
        {{-- ===================================== --}}
        <div
            x-data="{
                /* ── Reactive form state (digunakan di modal preview) ── */
                title: '{{ old('title', '') }}',
                price: '{{ old('price', '') }}',
                condition: '{{ old('condition', '') }}',
                category_id: '{{ old('category_id', '') }}',
                addresses: '{{ old('addresses', auth()->user()->addresses ?? '') }}',
                description: '{{ old('description', '') }}',

                /* ── Image Preview State ── */
                previewUrl: null,
                previewName: '',

                handleFileChange(event) {
                    const file = event.target.files[0];
                    if (!file) return;
                    this.previewName = file.name;
                    const reader = new FileReader();
                    reader.onload = (e) => { this.previewUrl = e.target.result; };
                    reader.readAsDataURL(file);
                },

                removeImage() {
                    this.previewUrl  = null;
                    this.previewName = '';
                    this.$refs.fotoInput.value = '';
                },

                /* ── Price Validation State ── */
                priceError: '',

                get priceNum() {
                    return parseInt(this.price, 10) || 0;
                },

                validatePrice() {
                    const val = this.priceNum;
                    if (this.price === '') {
                        this.priceError = '';
                        return;
                    }
                    if (val < 1000) {
                        this.priceError = 'Harga minimal adalah Rp 1.000.';
                        return;
                    }
                    if (val % 1000 !== 0) {
                        this.priceError = 'Harga harus merupakan kelipatan Rp 1.000.';
                        return;
                    }
                    this.priceError = '';
                },

                get isPriceValid() {
                    return this.price !== '' && this.priceNum >= 1000 && this.priceNum % 1000 === 0;
                },

                get canSubmit() {
                    return this.isPriceValid && this.priceError === '';
                },

                /* ── Formatted display ── */
                get formattedPrice() {
                    if (!this.priceNum) return '';
                    return 'Rp ' + this.priceNum.toLocaleString('id-ID');
                },

                /* ── Modal Preview State ── */
                showModal: false,

                openPreview() {
                    /* Jika harga belum valid, fokuskan ke input harga dan beri feedback */
                    if (!this.isPriceValid) {
                        this.validatePrice();
                        this.$nextTick(() => this.$refs.hargaInput?.focus());
                        return;
                    }
                    this.showModal = true;
                    document.body.style.overflow = 'hidden';
                },

                closePreview() {
                    this.showModal = false;
                    document.body.style.overflow = '';
                },

                submitForm() {
                    this.closePreview();
                    this.$nextTick(() => this.$refs.iklanForm.submit());
                },

                /* ── Helper: label kondisi ── */
                kondisiLabel(val) {
                    const map = {
                        baru: 'Baru',
                        bekas_mulus: 'Bekas – Mulus (95–99%)',
                        bekas_baik: 'Bekas – Baik (80–94%)',
                        bekas_normal: 'Bekas – Normal (60–79%)',
                        bekas_perlu_perbaikan: 'Bekas – Perlu Perbaikan',
                    };
                    return map[val] || val;
                },

                /* ── Helper: nama kategori dari select ── */
                get categoryLabel() {
                    const sel = this.$refs.categorySelect;
                    if (!sel || !this.category_id) return 'Belum dipilih';
                    const opt = sel.options[sel.selectedIndex];
                    return opt ? opt.text : 'Belum dipilih';
                }
            }"
            class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden"
        >
            <form
                x-ref="iklanForm"
                method="POST"
                action="{{ route('iklan.store') }}"
                enctype="multipart/form-data"
                class="divide-y divide-slate-100"
            >
                @csrf

                {{-- ══════════════════════════════════ --}}
                {{-- SECTION 1: Informasi Barang        --}}
                {{-- ══════════════════════════════════ --}}
                <div class="px-5 sm:px-7 py-5 space-y-5">
                    <h2 class="text-sm font-black text-slate-700 uppercase tracking-widest flex items-center gap-2">
                        <span class="w-5 h-5 rounded-md bg-blue-100 text-blue-600 flex items-center justify-center text-[10px] font-extrabold shrink-0">1</span>
                        Informasi Barang
                    </h2>

                    {{-- Judul Barang --}}
                    <div class="space-y-1.5">
                        <label for="judul_barang" class="block text-sm font-bold text-slate-700">
                            Judul Barang
                            <span class="text-rose-500 ml-0.5">*</span>
                        </label>
                        <input
                            id="judul_barang"
                            type="text"
                            name="title"
                            maxlength="200"
                            required
                            placeholder="Contoh: Rice Cooker Philips 2L Bekas Mulus"
                            x-model="title"
                            class="w-full px-4 py-2.5 text-sm rounded-xl border border-slate-200 bg-slate-50/60 text-slate-800 placeholder-slate-400 focus:bg-white focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition"
                        >
                        @error('title')
                            <p class="text-xs text-rose-600 font-medium flex items-center gap-1">
                                <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Harga + Kondisi --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                        {{-- Harga Barang --}}
                        <div class="space-y-1.5">
                            <label for="harga_barang" class="block text-sm font-bold text-slate-700">
                                Harga (Rp)
                                <span class="text-rose-500 ml-0.5">*</span>
                            </label>
                            <div class="relative">
                                <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-sm font-bold text-slate-500 pointer-events-none select-none">Rp</span>
                                <input
                                    id="harga_barang"
                                    type="number"
                                    name="price"
                                    x-ref="hargaInput"
                                    min="1000"
                                    step="1000"
                                    required
                                    placeholder="1000"
                                    x-model="price"
                                    @input="validatePrice()"
                                    @blur="validatePrice()"
                                    :class="priceError ? 'border-rose-400 focus:border-rose-500 focus:ring-rose-100' : 'border-slate-200 focus:border-blue-500 focus:ring-blue-100'"
                                    class="w-full pl-10 pr-4 py-2.5 text-sm rounded-xl border bg-slate-50/60 text-slate-800 placeholder-slate-400 focus:bg-white focus:outline-none focus:ring-2 transition"
                                >
                            </div>
                            {{-- Real-time price error --}}
                            <div x-cloak x-show="priceError" x-transition.opacity class="text-xs text-rose-600 font-medium flex items-center gap-1">
                                <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <span x-text="priceError"></span>
                            </div>
                            {{-- Formatted preview --}}
                            <div x-cloak x-show="isPriceValid" x-transition.opacity class="text-xs text-emerald-600 font-semibold flex items-center gap-1">
                                <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <span x-text="formattedPrice"></span>
                            </div>
                            @error('price')
                                <p class="text-xs text-rose-600 font-medium flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        {{-- Kondisi Barang --}}
                        <div class="space-y-1.5">
                            <label for="kondisi_barang" class="block text-sm font-bold text-slate-700">
                                Kondisi Barang
                                <span class="text-rose-500 ml-0.5">*</span>
                            </label>
                            <div class="relative">
                                <select
                                    id="kondisi_barang"
                                    name="condition"
                                    required
                                    x-model="condition"
                                    class="w-full appearance-none px-4 py-2.5 pr-9 text-sm rounded-xl border border-slate-200 bg-slate-50/60 text-slate-800 focus:bg-white focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition cursor-pointer"
                                >
                                    <option value="" disabled>Pilih kondisi...</option>
                                    <option value="baru">Baru</option>
                                    <option value="bekas_mulus">Bekas – Mulus (95–99%)</option>
                                    <option value="bekas_baik">Bekas – Baik (80–94%)</option>
                                    <option value="bekas_normal">Bekas – Normal (60–79%)</option>
                                    <option value="bekas_perlu_perbaikan">Bekas – Perlu Perbaikan</option>
                                </select>
                                <svg class="w-4 h-4 text-slate-400 absolute right-3 top-3.5 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </div>
                            @error('condition')
                                <p class="text-xs text-rose-600 font-medium flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>
                    </div>

                    {{-- Kategori + Lokasi --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                        {{-- Kategori Barang --}}
                        <div class="space-y-1.5">
                            <label for="kategori_barang" class="block text-sm font-bold text-slate-700">
                                Kategori
                                <span class="text-rose-500 ml-0.5">*</span>
                            </label>
                            <div class="relative">
                                <select
                                    id="kategori_barang"
                                    name="category_id"
                                    x-ref="categorySelect"
                                    required
                                    x-model="category_id"
                                    class="w-full appearance-none px-4 py-2.5 pr-9 text-sm rounded-xl border border-slate-200 bg-slate-50/60 text-slate-800 focus:bg-white focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition cursor-pointer"
                                >
                                    <option value="" disabled>Pilih kategori...</option>
                                    @foreach ($categories as $cat)
                                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                    @endforeach
                                </select>
                                <svg class="w-4 h-4 text-slate-400 absolute right-3 top-3.5 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </div>
                            @error('category_id')
                                <p class="text-xs text-rose-600 font-medium flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        {{-- Lokasi / Area Kampus --}}
                        <div class="space-y-1.5">
                            <label for="lokasi_barang" class="block text-sm font-bold text-slate-700">
                                Lokasi / Area Kampus
                                <span class="text-rose-500 ml-0.5">*</span>
                            </label>
                            <input
                                id="lokasi_barang"
                                type="text"
                                name="addresses"
                                maxlength="255"
                                required
                                placeholder="Contoh: Polinema, Sumbersari, Malang"
                                x-model="addresses"
                                class="w-full px-4 py-2.5 text-sm rounded-xl border border-slate-200 bg-slate-50/60 text-slate-800 placeholder-slate-400 focus:bg-white focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition"
                            >
                            @error('addresses')
                                <p class="text-xs text-rose-600 font-medium flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- ══════════════════════════════════ --}}
                {{-- SECTION 2: Foto Barang             --}}
                {{-- ══════════════════════════════════ --}}
                <div class="px-5 sm:px-7 py-5 space-y-4">
                    <h2 class="text-sm font-black text-slate-700 uppercase tracking-widest flex items-center gap-2">
                        <span class="w-5 h-5 rounded-md bg-blue-100 text-blue-600 flex items-center justify-center text-[10px] font-extrabold shrink-0">2</span>
                        Foto Barang
                    </h2>

                    {{-- Dropzone / File Input --}}
                    <div>
                        {{-- Image Preview (shown when a file is selected) --}}
                        <div x-cloak x-show="previewUrl" x-transition class="relative mb-3">
                            <div class="relative rounded-xl overflow-hidden bg-slate-100 border border-slate-200 aspect-video flex items-center justify-center">
                                <img
                                    :src="previewUrl"
                                    :alt="previewName"
                                    class="max-h-64 max-w-full object-contain"
                                >
                                {{-- Badge nama file --}}
                                <div class="absolute bottom-2 left-2 bg-black/50 backdrop-blur-sm text-white text-[11px] font-semibold px-2.5 py-1 rounded-full truncate max-w-[60%]" x-text="previewName"></div>
                            </div>
                            {{-- Tombol Hapus Foto --}}
                            <button
                                type="button"
                                @click="removeImage()"
                                class="absolute top-2 right-2 w-8 h-8 bg-rose-600 hover:bg-rose-700 text-white rounded-full flex items-center justify-center shadow-md transition"
                                title="Hapus foto"
                                aria-label="Hapus foto yang dipilih"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>

                        {{-- Upload Area (shown when no file is selected) --}}
                        <label
                            for="foto_barang"
                            x-show="!previewUrl"
                            x-transition
                            class="flex flex-col items-center justify-center gap-3 border-2 border-dashed border-slate-200 hover:border-blue-400 bg-slate-50/50 hover:bg-blue-50/30 rounded-xl p-8 cursor-pointer transition group"
                        >
                            <div class="w-12 h-12 rounded-2xl bg-blue-100 group-hover:bg-blue-600 text-blue-600 group-hover:text-white flex items-center justify-center transition">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <div class="text-center">
                                <p class="text-sm font-bold text-slate-700 group-hover:text-blue-700 transition">
                                    Klik untuk unggah foto
                                </p>
                                <p class="text-xs text-slate-400 mt-0.5">PNG, JPG, WEBP – Maks. 2 MB</p>
                            </div>
                        </label>

                        {{-- Hidden file input --}}
                        <input
                            id="foto_barang"
                            type="file"
                            name="image"
                            accept="image/png,image/jpeg,image/webp"
                            x-ref="fotoInput"
                            @change="handleFileChange($event)"
                            class="sr-only"
                        >
                    </div>

                    @error('image')
                        <p class="text-xs text-rose-600 font-medium flex items-center gap-1">
                            <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            {{ $message }}
                        </p>
                    @enderror

                    {{-- Tips --}}
                    <div class="flex items-start gap-2.5 text-xs text-slate-500 bg-blue-50/60 border border-blue-100 rounded-xl px-4 py-3">
                        <svg class="w-4 h-4 text-blue-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span>Gunakan foto asli dan berkualitas baik. Iklan dengan foto menarik lebih cepat terjual!</span>
                    </div>
                </div>

                {{-- ══════════════════════════════════ --}}
                {{-- SECTION 3: Deskripsi Barang        --}}
                {{-- ══════════════════════════════════ --}}
                <div class="px-5 sm:px-7 py-5 space-y-4">
                    <h2 class="text-sm font-black text-slate-700 uppercase tracking-widest flex items-center gap-2">
                        <span class="w-5 h-5 rounded-md bg-blue-100 text-blue-600 flex items-center justify-center text-[10px] font-extrabold shrink-0">3</span>
                        Deskripsi Barang
                    </h2>

                    <div class="space-y-1.5">
                        <label for="deskripsi_barang" class="block text-sm font-bold text-slate-700">
                            Deskripsi
                            <span class="text-rose-500 ml-0.5">*</span>
                        </label>
                        <textarea
                            id="deskripsi_barang"
                            name="description"
                            rows="5"
                            required
                            maxlength="2000"
                            placeholder="Jelaskan kondisi barang secara detail: merek, tahun beli, alasan jual, kelengkapan, dll. Semakin lengkap semakin cepat laku!"
                            x-model="description"
                            class="w-full px-4 py-3 text-sm rounded-xl border border-slate-200 bg-slate-50/60 text-slate-800 placeholder-slate-400 focus:bg-white focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition resize-none leading-relaxed"
                        ></textarea>
                        @error('description')
                            <p class="text-xs text-rose-600 font-medium flex items-center gap-1">
                                <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                {{ $message }}
                            </p>
                        @enderror
                        <p class="text-[11px] text-slate-400 text-right">Maks. 2.000 karakter</p>
                    </div>
                </div>

                {{-- ══════════════════════════════════ --}}
                {{-- FOOTER: Action Buttons             --}}
                {{-- ══════════════════════════════════ --}}
                <div class="px-5 sm:px-7 py-5 bg-slate-50/50 flex flex-col sm:flex-row items-center justify-between gap-3">
                    <a
                        href="{{ url('/') }}"
                        class="w-full sm:w-auto text-center px-6 py-2.5 text-sm font-bold text-slate-600 hover:text-slate-900 bg-white hover:bg-slate-100 border border-slate-200 rounded-xl transition"
                    >
                        Batal
                    </a>

                    {{-- Tombol Preview Iklan --}}
                    <button
                        id="btn-preview-iklan"
                        type="button"
                        @click="openPreview()"
                        :title="canSubmit ? 'Lihat pratinjau iklan sebelum dipasang' : 'Isi harga yang valid (minimal Rp1.000, kelipatan Rp1.000)'"
                        class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-8 py-2.5 text-sm font-black rounded-xl transition bg-blue-600 hover:bg-blue-700 text-white shadow-md shadow-blue-500/25 cursor-pointer"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        <span>Preview Iklan</span>
                    </button>
                </div>

            </form>

            {{-- ═══════════════════════════════════════════════════════════ --}}
            {{-- MODAL PREVIEW TAMPILAN PUBLIK                               --}}
            {{-- ═══════════════════════════════════════════════════════════ --}}
            <div
                x-cloak
                x-show="showModal"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                @keydown.escape.window="closePreview()"
                class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6"
                role="dialog"
                aria-modal="true"
                aria-labelledby="modal-preview-title"
            >
                {{-- Overlay gelap --}}
                <div
                    class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm"
                    @click="closePreview()"
                ></div>

                {{-- Panel modal --}}
                <div
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                    x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                    x-transition:leave-end="opacity-0 scale-95 translate-y-4"
                    class="relative z-10 bg-white rounded-2xl shadow-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto"
                >
                    {{-- Header modal --}}
                    <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100">
                        <div class="flex items-center gap-2.5">
                            <div class="w-7 h-7 rounded-lg bg-blue-100 text-blue-600 flex items-center justify-center">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </div>
                            <div>
                                <h2 id="modal-preview-title" class="text-sm font-black text-slate-900">Pratinjau Iklan</h2>
                                <p class="text-[11px] text-slate-400">Begini tampilan iklanmu di halaman publik</p>
                            </div>
                        </div>
                        <button
                            type="button"
                            @click="closePreview()"
                            class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-500 hover:text-slate-700 flex items-center justify-center transition"
                            aria-label="Tutup pratinjau"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>

                    {{-- Konten preview --}}
                    <div class="p-5 space-y-4">

                        {{-- Foto barang --}}
                        <div class="rounded-xl overflow-hidden bg-slate-100 aspect-video flex items-center justify-center border border-slate-200">
                            <template x-if="previewUrl">
                                <img :src="previewUrl" :alt="title" class="w-full h-full object-cover">
                            </template>
                            <template x-if="!previewUrl">
                                <div class="flex flex-col items-center gap-2 text-slate-400">
                                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    <span class="text-xs font-medium">Belum ada foto</span>
                                </div>
                            </template>
                        </div>

                        {{-- Detail barang --}}
                        <div class="space-y-3">

                            {{-- Judul & Harga --}}
                            <div class="flex items-start justify-between gap-3">
                                <h3
                                    class="text-base font-black text-slate-900 leading-snug flex-1"
                                    x-text="title || '(Judul belum diisi)'"
                                ></h3>
                                <span class="text-lg font-black text-blue-600 shrink-0" x-text="formattedPrice || '—'"></span>
                            </div>

                            {{-- Badge Kondisi + Kategori --}}
                            <div class="flex flex-wrap gap-2">
                                <span
                                    x-show="condition"
                                    class="inline-flex items-center gap-1.5 text-xs font-bold px-2.5 py-1 rounded-full"
                                    :class="condition === 'baru'
                                        ? 'bg-emerald-100 text-emerald-700'
                                        : 'bg-amber-100 text-amber-700'"
                                    x-text="kondisiLabel(condition)"
                                ></span>
                                <span
                                    x-show="!condition"
                                    class="inline-flex items-center text-xs font-bold px-2.5 py-1 rounded-full bg-slate-100 text-slate-500"
                                >Kondisi belum dipilih</span>

                                <span
                                    x-show="category_id"
                                    class="inline-flex items-center text-xs font-semibold px-2.5 py-1 rounded-full bg-blue-50 text-blue-600"
                                    x-text="categoryLabel"
                                ></span>
                            </div>

                            {{-- Lokasi --}}
                            <div x-show="addresses" class="flex items-center gap-1.5 text-xs text-slate-500">
                                <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                <span x-text="addresses"></span>
                            </div>

                            {{-- Divider --}}
                            <hr class="border-slate-100">

                            {{-- Deskripsi --}}
                            <div class="space-y-1">
                                <p class="text-xs font-bold text-slate-500 uppercase tracking-wide">Deskripsi</p>
                                <p
                                    class="text-sm text-slate-700 leading-relaxed whitespace-pre-line line-clamp-6"
                                    x-text="description || '(Deskripsi belum diisi)'"
                                ></p>
                            </div>

                            {{-- Info penjual (simulasi) --}}
                            <div class="flex items-center gap-2.5 pt-1 border-t border-slate-100">
                                <div class="w-8 h-8 rounded-full bg-slate-900 text-white text-xs font-black flex items-center justify-center shrink-0">
                                    {{ strtoupper(substr(auth()->user()?->name ?? 'U', 0, 2)) }}
                                </div>
                                <div>
                                    <p class="text-xs font-bold text-slate-800">{{ auth()->user()?->name ?? 'Pengguna Lungsur.in' }}</p>
                                    <p class="text-[11px] text-slate-400">Baru saja</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Footer modal: 2 tombol aksi --}}
                    <div class="px-5 pb-5 flex flex-col sm:flex-row gap-3">
                        {{-- Kembali Edit --}}
                        <button
                            id="btn-modal-kembali"
                            type="button"
                            @click="closePreview()"
                            class="flex-1 py-2.5 text-sm font-bold text-slate-600 hover:text-slate-900 bg-slate-100 hover:bg-slate-200 rounded-xl transition text-center"
                        >
                            ← Kembali Edit
                        </button>

                        {{-- Konfirmasi & Pasang --}}
                        <button
                            id="btn-modal-pasang"
                            type="button"
                            @click="submitForm()"
                            class="flex-1 inline-flex items-center justify-center gap-2 py-2.5 text-sm font-black bg-blue-600 hover:bg-blue-700 text-white rounded-xl shadow-md shadow-blue-500/25 transition"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                            </svg>
                            Konfirmasi &amp; Pasang Iklan
                        </button>
                    </div>
                </div>
            </div>

        </div>

        {{-- ===================================== --}}
        {{-- Info bawah halaman                    --}}
        {{-- ===================================== --}}
        <p class="text-center text-xs text-slate-400 pb-2">
            Dengan memasang iklan, kamu menyetujui
            <a href="#" class="text-blue-600 font-semibold hover:underline">Syarat &amp; Ketentuan</a>
            Lungsur.in.
        </p>

    </div>
</x-layouts.app>
