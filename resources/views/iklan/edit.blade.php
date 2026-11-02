<x-layouts.app>
    <x-slot:title>
        Edit Iklan Barang – Lungsur.in
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
                <a href="{{ route('dashboard') }}" class="hover:text-blue-600 transition">Dasbor</a>
                <svg class="w-3.5 h-3.5 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
                <span class="text-slate-600">Edit Iklan</span>
            </div>
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-blue-600 flex items-center justify-center shadow-md shadow-blue-500/20 shrink-0">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">Edit Iklan Barang</h1>
                    <p class="text-xs text-slate-400 mt-0.5">Perbarui rincian barang yang kamu iklankan di marketplace Lungsur.in</p>
                </div>
            </div>
        </div>

        {{-- ===================================== --}}
        {{-- Main Form (Alpine.js Component)       --}}
        {{-- ===================================== --}}
        <div
            x-data="editListingForm()"
            class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden"
        >
            <form
                x-ref="iklanForm"
                method="POST"
                action="{{ route('iklan.update', $listing->id) }}"
                enctype="multipart/form-data"
                class="divide-y divide-slate-100"
            >
                @csrf
                @method('PUT')

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
                    <div class="flex items-center justify-between">
                        <h2 class="text-sm font-black text-slate-700 uppercase tracking-widest flex items-center gap-2">
                            <span class="w-5 h-5 rounded-md bg-blue-100 text-blue-600 flex items-center justify-center text-[10px] font-extrabold shrink-0">2</span>
                            Foto Barang
                            <span class="text-rose-500 ml-0.5">*</span>
                        </h2>
                        <span class="text-xs font-semibold text-slate-400" x-text="totalPhotosCount + ' / 5 Foto'"></span>
                    </div>

                    {{-- Upload Area & Thumbnails --}}
                    <div>
                        {{-- Hidden input for deleted existing images --}}
                        <template x-for="delId in deletedImageIds" :key="'del-' + delId">
                            <input type="hidden" name="deleted_image_ids[]" :value="delId">
                        </template>

                        {{-- Hidden real file input submitted with form --}}
                        <input
                            type="file"
                            name="images[]"
                            multiple
                            x-ref="realInput"
                            class="hidden"
                        >

                        {{-- Hidden file selector trigger --}}
                        <input
                            id="foto_barang_edit_input"
                            type="file"
                            multiple
                            accept="image/png,image/jpeg,image/webp"
                            x-ref="fileSelector"
                            @change="handleFileChange($event)"
                            class="hidden"
                        >

                        {{-- Thumbnails Grid --}}
                        <div class="space-y-3">
                            <div class="grid grid-cols-2 sm:grid-cols-5 gap-3">
                                {{-- 1. Foto Lama yang Belum Dihapus --}}
                                <template x-for="(photo, index) in activeExistingPhotos" :key="'existing-' + photo.id">
                                    <div class="aspect-square rounded-2xl overflow-hidden bg-slate-100 border border-slate-200 relative group shadow-xs">
                                        <img
                                            :src="photo.url"
                                            alt="Foto Barang Lama"
                                            class="w-full h-full object-cover"
                                        >

                                        {{-- Badge Nomor Urut --}}
                                        <div class="absolute top-2 left-2 bg-slate-900/60 backdrop-blur-xs text-white text-[10px] font-bold px-1.5 py-0.5 rounded-md" x-text="index + 1"></div>

                                        {{-- Badge Foto Utama / Tersimpan --}}
                                        <div class="absolute bottom-2 left-2 flex flex-col gap-1">
                                            <span x-show="index === 0" class="bg-blue-600/90 backdrop-blur-xs text-white text-[9px] font-black uppercase tracking-wider px-2 py-0.5 rounded-md shadow-xs">
                                                Utama
                                            </span>
                                            <span class="bg-slate-800/80 backdrop-blur-xs text-white text-[9px] font-semibold px-1.5 py-0.5 rounded-md shadow-xs">
                                                Tersimpan
                                            </span>
                                        </div>

                                        {{-- Tombol Hapus Foto Lama --}}
                                        <button
                                            type="button"
                                            @click="removeExistingPhoto(photo.id)"
                                            class="absolute top-2 right-2 w-7 h-7 bg-rose-600 hover:bg-rose-700 text-white rounded-full flex items-center justify-center shadow-md transition cursor-pointer"
                                            title="Hapus foto ini"
                                            aria-label="Hapus foto ini"
                                        >
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                                            </svg>
                                        </button>
                                    </div>
                                </template>

                                {{-- 2. Foto Baru yang Baru Dipilih --}}
                                <template x-for="(photo, nIndex) in newPhotos" :key="photo.id">
                                    <div class="aspect-square rounded-2xl overflow-hidden bg-slate-100 border-2 border-emerald-400 relative group shadow-xs">
                                        <img
                                            :src="photo.url"
                                            :alt="photo.name"
                                            class="w-full h-full object-cover"
                                        >

                                        {{-- Badge Nomor Urut --}}
                                        <div class="absolute top-2 left-2 bg-slate-900/60 backdrop-blur-xs text-white text-[10px] font-bold px-1.5 py-0.5 rounded-md" x-text="activeExistingPhotos.length + nIndex + 1"></div>

                                        {{-- Badge Foto Baru --}}
                                        <div class="absolute bottom-2 left-2 flex flex-col gap-1">
                                            <span x-show="activeExistingPhotos.length === 0 && nIndex === 0" class="bg-blue-600/90 backdrop-blur-xs text-white text-[9px] font-black uppercase tracking-wider px-2 py-0.5 rounded-md shadow-xs">
                                                Utama
                                            </span>
                                            <span class="bg-emerald-600/90 backdrop-blur-xs text-white text-[9px] font-bold px-1.5 py-0.5 rounded-md shadow-xs">
                                                Baru
                                            </span>
                                        </div>

                                        {{-- Tombol Hapus Foto Baru --}}
                                        <button
                                            type="button"
                                            @click="removeNewPhoto(nIndex)"
                                            class="absolute top-2 right-2 w-7 h-7 bg-rose-600 hover:bg-rose-700 text-white rounded-full flex items-center justify-center shadow-md transition cursor-pointer"
                                            title="Batalkan foto baru ini"
                                            aria-label="Batalkan foto baru ini"
                                        >
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                                            </svg>
                                        </button>
                                    </div>
                                </template>

                                {{-- 3. Tombol Tambah (+) berikutnya (hilang saat total foto mencapai 5) --}}
                                <div
                                    x-show="totalPhotosCount < 5"
                                    @click="$refs.fileSelector.click()"
                                    class="aspect-square rounded-2xl border-2 border-dashed border-slate-300 hover:border-blue-500 bg-slate-50 hover:bg-blue-50/40 flex flex-col items-center justify-center gap-1.5 cursor-pointer transition text-slate-500 hover:text-blue-600 group"
                                    title="Tambah foto berikutnya"
                                >
                                    <div class="w-9 h-9 rounded-xl bg-white border border-slate-200 group-hover:border-blue-300 group-hover:bg-blue-600 group-hover:text-white flex items-center justify-center transition shadow-xs text-slate-600">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                                        </svg>
                                    </div>
                                    <span class="text-xs font-bold">Tambah Foto</span>
                                    <span class="text-[10px] text-slate-400" x-text="'(' + totalPhotosCount + '/5)'"></span>
                                </div>
                            </div>
                        </div>

                        {{-- Pesan Validasi Foto --}}
                        <div x-cloak x-show="uploadError" class="mt-2.5 text-xs text-rose-600 font-semibold flex items-center gap-1">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span x-text="uploadError"></span>
                        </div>

                        @error('images')
                            <p class="mt-2 text-xs text-rose-600 font-medium flex items-center gap-1">
                                <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                {{ $message }}
                            </p>
                        @enderror
                        @error('images.*')
                            <p class="mt-1 text-xs text-rose-600 font-medium flex items-center gap-1">
                                <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                {{ $message }}
                            </p>
                        @enderror
                        @error('deleted_image_ids.*')
                            <p class="mt-1 text-xs text-rose-600 font-medium flex items-center gap-1">
                                <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Tips --}}
                    <div class="flex items-start gap-2.5 text-xs text-slate-500 bg-blue-50/60 border border-blue-100 rounded-xl px-4 py-3">
                        <svg class="w-4 h-4 text-blue-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span>Kamu dapat mempertahankan foto lama, menghapus foto yang tidak diinginkan, atau menambah foto baru hingga maksimal 5 foto. Iklan harus memiliki minimal 1 foto aktif.</span>
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
                            placeholder="Jelaskan kondisi barang secara detail: merek, tahun beli, alasan jual, kelengkapan, dll."
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
                        href="{{ route('dashboard') }}"
                        class="w-full sm:w-auto text-center px-6 py-2.5 text-sm font-bold text-slate-600 hover:text-slate-900 bg-white hover:bg-slate-100 border border-slate-200 rounded-xl transition"
                    >
                        Batal
                    </a>

                    {{-- Tombol Preview Iklan --}}
                    <button
                        id="btn-preview-iklan"
                        type="button"
                        @click="openPreview()"
                        :title="canSubmit ? 'Lihat pratinjau perubahan iklan sebelum disimpan' : 'Isi harga yang valid (minimal Rp1.000, kelipatan Rp1.000)'"
                        class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-8 py-2.5 text-sm font-black rounded-xl transition bg-blue-600 hover:bg-blue-700 text-white shadow-md shadow-blue-500/25 cursor-pointer"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        <span>Preview Perubahan</span>
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
                                <p class="text-[11px] text-slate-400">Begini tampilan iklanmu setelah diperbarui</p>
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

                        {{-- Foto barang - Slider / Carousel --}}
                        <div class="rounded-xl overflow-hidden bg-slate-100 aspect-video flex items-center justify-center border border-slate-200 relative group">
                            <template x-if="allPreviewPhotos.length > 0">
                                <div class="w-full h-full relative">
                                    <template x-for="(photo, idx) in allPreviewPhotos" :key="photo.id">
                                        <div
                                            x-show="activePreviewIndex === idx"
                                            x-transition:enter="transition ease-out duration-200"
                                            x-transition:enter-start="opacity-0 scale-98"
                                            x-transition:enter-end="opacity-100 scale-100"
                                            class="w-full h-full flex items-center justify-center"
                                        >
                                            <img :src="photo.url" :alt="title" class="w-full h-full object-cover">
                                        </div>
                                    </template>

                                    {{-- Tombol Navigasi Kiri --}}
                                    <button
                                        type="button"
                                        x-show="allPreviewPhotos.length > 1"
                                        @click="activePreviewIndex = (activePreviewIndex === 0) ? allPreviewPhotos.length - 1 : activePreviewIndex - 1"
                                        class="absolute left-2.5 top-1/2 -translate-y-1/2 w-8 h-8 rounded-full bg-slate-900/60 hover:bg-slate-900/80 text-white flex items-center justify-center backdrop-blur-xs transition shadow-md cursor-pointer"
                                        aria-label="Foto sebelumnya"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/>
                                        </svg>
                                    </button>

                                    {{-- Tombol Navigasi Kanan --}}
                                    <button
                                        type="button"
                                        x-show="allPreviewPhotos.length > 1"
                                        @click="activePreviewIndex = (activePreviewIndex === allPreviewPhotos.length - 1) ? 0 : activePreviewIndex + 1"
                                        class="absolute right-2.5 top-1/2 -translate-y-1/2 w-8 h-8 rounded-full bg-slate-900/60 hover:bg-slate-900/80 text-white flex items-center justify-center backdrop-blur-xs transition shadow-md cursor-pointer"
                                        aria-label="Foto berikutnya"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
                                        </svg>
                                    </button>

                                    {{-- Titik Indikator Slider --}}
                                    <div x-show="allPreviewPhotos.length > 1" class="absolute bottom-2.5 left-0 right-0 flex items-center justify-center gap-1.5 z-10">
                                        <template x-for="(photo, idx) in allPreviewPhotos" :key="'dot-' + idx">
                                            <button
                                                type="button"
                                                @click="activePreviewIndex = idx"
                                                class="h-1.5 rounded-full transition-all duration-200 cursor-pointer"
                                                :class="activePreviewIndex === idx ? 'w-5 bg-blue-600' : 'w-1.5 bg-white/80 hover:bg-white'"
                                            ></button>
                                        </template>
                                    </div>

                                    {{-- Badge Indikator Posisi --}}
                                    <div x-show="allPreviewPhotos.length > 1" class="absolute top-2.5 right-2.5 bg-slate-900/60 backdrop-blur-xs text-white text-[10px] font-bold px-2 py-0.5 rounded-md">
                                        <span x-text="(activePreviewIndex + 1) + ' / ' + allPreviewPhotos.length"></span>
                                    </div>
                                </div>
                            </template>

                            <template x-if="allPreviewPhotos.length === 0">
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

                            {{-- Info penjual --}}
                            <div class="flex items-center gap-2.5 pt-1 border-t border-slate-100">
                                <div class="w-8 h-8 rounded-full bg-slate-900 text-white text-xs font-black flex items-center justify-center shrink-0">
                                    {{ strtoupper(substr(auth()->user()?->name ?? 'U', 0, 2)) }}
                                </div>
                                <div>
                                    <p class="text-xs font-bold text-slate-800">{{ auth()->user()?->name ?? 'Pengguna Lungsur.in' }}</p>
                                    <p class="text-[11px] text-slate-400">Pemilik Iklan</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Footer modal: 2 tombol aksi --}}
                    <div class="px-5 pb-5 flex flex-col sm:flex-row items-center gap-3">
                        {{-- Kembali Edit --}}
                        <button
                            id="btn-modal-kembali"
                            type="button"
                            @click="closePreview()"
                            class="w-full sm:w-auto px-6 h-11 inline-flex items-center justify-center text-sm font-bold text-slate-600 hover:text-slate-900 bg-slate-100 hover:bg-slate-200 rounded-xl transition text-center cursor-pointer shrink-0"
                        >
                            ← Kembali Edit
                        </button>

                        {{-- Konfirmasi & Simpan Perubahan --}}
                        <button
                            id="btn-modal-pasang"
                            type="button"
                            @click="submitForm()"
                            class="flex-1 w-full h-11 inline-flex items-center justify-center gap-2 px-5 text-sm font-black bg-blue-600 hover:bg-blue-700 text-white rounded-xl shadow-md shadow-blue-500/25 transition cursor-pointer"
                        >
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                            </svg>
                            <span class="whitespace-normal sm:whitespace-nowrap">Konfirmasi &amp; Simpan Perubahan</span>
                        </button>
                    </div>
                </div>
            </div>

        </div>

        {{-- ===================================== --}}
        {{-- Info bawah halaman                    --}}
        {{-- ===================================== --}}
        <p class="text-center text-xs text-slate-400 pb-2">
            Dengan memperbarui iklan, kamu menyetujui
            <a href="#" class="text-blue-600 font-semibold hover:underline">Syarat &amp; Ketentuan</a>
            Lungsur.in.
        </p>

    </div>

    {{-- ===================================== --}}
    {{-- Alpine.js Edit Form Component Script  --}}
    {{-- ===================================== --}}
    @php
        $initialExistingPhotos = $listing->images->map(function ($img) {
            $url = $img->image_url;
            return [
                'id' => $img->id,
                'url' => \Illuminate\Support\Str::startsWith($url, ['http://', 'https://'])
                    ? $url
                    : asset('storage/' . ltrim($url, '/')),
                'is_primary' => (bool) $img->is_primary,
            ];
        })->values()->all();
    @endphp

    <script>
        function editListingForm() {
            return {
                /* ── Reactive form state (digunakan di modal preview) ── */
                title: @json(old('title', $listing->title)),
                price: @json(old('price', (int) $listing->price)),
                condition: @json(old('condition', is_array($listing->condition_checklist) ? ($listing->condition_checklist['kondisi'] ?? '') : '')),
                category_id: @json(old('category_id', (string) $listing->category_id)),
                addresses: @json(old('addresses', $listing->addresses)),
                description: @json(old('description', $listing->description)),

                /* ── Image State for Edit (Multi-photo max 5) ── */
                existingPhotos: @json($initialExistingPhotos),
                deletedImageIds: [],
                newPhotos: [],
                uploadError: '',
                activePreviewIndex: 0,

                get activeExistingPhotos() {
                    return this.existingPhotos.filter(p => !this.deletedImageIds.includes(p.id));
                },

                get totalPhotosCount() {
                    return this.activeExistingPhotos.length + this.newPhotos.length;
                },

                get allPreviewPhotos() {
                    const photos = [];
                    this.activeExistingPhotos.forEach(p => {
                        photos.push({ id: 'existing-' + p.id, url: p.url, is_existing: true });
                    });
                    this.newPhotos.forEach(p => {
                        photos.push({ id: 'new-' + p.id, url: p.url, is_existing: false });
                    });
                    return photos;
                },

                handleFileChange(event) {
                    const newFiles = Array.from(event.target.files);
                    if (!newFiles.length) return;

                    this.uploadError = '';

                    if (this.totalPhotosCount + newFiles.length > 5) {
                        this.uploadError = 'Unggah maksimal 5 foto.';
                        event.target.value = '';
                        return;
                    }

                    const allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
                    const maxSize = 5 * 1024 * 1024; // 5 MB

                    for (const file of newFiles) {
                        if (!allowedTypes.includes(file.type)) {
                            this.uploadError = 'Format foto harus JPG, JPEG, PNG, atau WEBP.';
                            event.target.value = '';
                            return;
                        }
                        if (file.size > maxSize) {
                            this.uploadError = 'Ukuran setiap foto maksimal 5 MB.';
                            event.target.value = '';
                            return;
                        }
                    }

                    newFiles.forEach(file => {
                        this.newPhotos.push({
                            id: Math.random().toString(36).substring(2, 9),
                            file: file,
                            url: URL.createObjectURL(file),
                            name: file.name
                        });
                    });

                    this.syncNewFiles();
                    event.target.value = '';
                },

                removeExistingPhoto(id) {
                    if (this.totalPhotosCount <= 1) {
                        this.uploadError = 'Iklan wajib memiliki minimal 1 foto barang.';
                        return;
                    }
                    this.uploadError = '';
                    if (!this.deletedImageIds.includes(id)) {
                        this.deletedImageIds.push(id);
                    }
                    if (this.activePreviewIndex >= this.allPreviewPhotos.length) {
                        this.activePreviewIndex = Math.max(0, this.allPreviewPhotos.length - 1);
                    }
                },

                removeNewPhoto(index) {
                    if (this.totalPhotosCount <= 1) {
                        this.uploadError = 'Iklan wajib memiliki minimal 1 foto barang.';
                        return;
                    }
                    this.uploadError = '';
                    URL.revokeObjectURL(this.newPhotos[index].url);
                    this.newPhotos.splice(index, 1);
                    if (this.activePreviewIndex >= this.allPreviewPhotos.length) {
                        this.activePreviewIndex = Math.max(0, this.allPreviewPhotos.length - 1);
                    }
                    this.syncNewFiles();
                },

                syncNewFiles() {
                    const dt = new DataTransfer();
                    this.newPhotos.forEach(item => dt.items.add(item.file));
                    if (this.$refs.realInput) {
                        this.$refs.realInput.files = dt.files;
                    }
                },

                /* ── Price Validation State ── */
                priceError: '',

                get priceNum() {
                    return parseInt(this.price, 10) || 0;
                },

                validatePrice() {
                    const val = this.priceNum;
                    if (this.price === '' || this.price === null) {
                        this.priceError = 'Harga barang wajib diisi.';
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
                    return this.price !== '' && this.price !== null && this.priceNum >= 1000 && this.priceNum % 1000 === 0;
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
            };
        }

        if (window.Alpine) {
            Alpine.data('editListingForm', editListingForm);
        } else {
            document.addEventListener('alpine:init', () => {
                Alpine.data('editListingForm', editListingForm);
            });
        }
    </script>
</x-layouts.app>
