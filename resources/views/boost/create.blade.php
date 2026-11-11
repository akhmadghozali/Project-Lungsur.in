@extends('layouts.app')

@section('title', 'Boost Iklan - ' . $listing->title . ' - Lungsur.in')

@section('content')
<div class="max-w-4xl mx-auto py-6 sm:py-10 px-4">
    {{-- Breadcrumb --}}
    <nav class="flex items-center gap-2 text-xs text-slate-500 mb-6">
        <a href="{{ route('dashboard') }}" class="hover:text-blue-600 transition">Dashboard</a>
        <span>/</span>
        <span class="text-slate-800 font-semibold truncate">Boost Iklan</span>
    </nav>

    {{-- Header Banner --}}
    <div class="bg-gradient-to-r from-amber-500 via-orange-500 to-amber-600 rounded-3xl p-6 sm:p-8 text-white shadow-xl shadow-amber-500/20 mb-8 relative overflow-hidden">
        <div class="relative z-10 max-w-xl">
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-white/20 backdrop-blur-md text-white text-xs font-bold mb-3">
                ⚡ Fitur Promosi Prioritas Mahasiswa
            </span>
            <h1 class="text-2xl sm:text-3xl font-black tracking-tight mb-2">
                Naikkan Iklanmu ke Posisi Teratas!
            </h1>
            <p class="text-amber-100 text-sm leading-relaxed">
                Buat barang kosmu lebih cepat laku. Iklan yang di-boost otomatis meloncat ke posisi paling atas di halaman utama dan katalog pencarian mahasiswa selama <strong>30 Hari Penuh</strong>.
            </p>
        </div>
        {{-- Decorative Icon --}}
        <div class="absolute right-4 -bottom-6 text-white/10 text-9xl font-black select-none pointer-events-none hidden md:block">
            ⚡
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        {{-- Kolom Kiri: Rincian Iklan & Paket --}}
        <div class="lg:col-span-5 space-y-6">
            {{-- Kartu Preview Iklan --}}
            <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs">
                <h2 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-3">
                    Barang yang Dipromosikan
                </h2>
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
                            Rp {{ number_format($listing->price, 0, ',', '.') }}
                        </p>
                    </div>
                </div>
            </div>

            {{-- Kartu Rincian Paket Boost --}}
            <div class="bg-gradient-to-br from-slate-900 to-slate-800 text-white rounded-2xl p-6 shadow-md relative overflow-hidden">
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <span class="text-xs font-bold text-amber-400 uppercase tracking-wider">Paket Spesial Mahasiswa</span>
                        <h3 class="text-xl font-black mt-1">Boost 30 Hari</h3>
                    </div>
                    <span class="px-2.5 py-1 rounded-full bg-amber-400 text-slate-900 text-xs font-black">
                        Hemat 80%
                    </span>
                </div>

                <div class="mb-6">
                    <span class="text-3xl font-black text-white">Rp 5.000</span>
                    <span class="text-slate-400 text-xs">/ 30 hari penuh</span>
                </div>

                <ul class="space-y-2.5 text-xs text-slate-300">
                    <li class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                        </svg>
                        <span>Prioritas posisi #1 di halaman Katalog & Pencarian</span>
                    </li>
                    <li class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                        </svg>
                        <span>Lencana kilat khusus <strong>⚡ Iklan Unggulan</strong></span>
                    </li>
                    <li class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                        </svg>
                        <span>Dilihat lebih banyak mahasiswa pencari perlengkapan kos</span>
                    </li>
                    <li class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                        </svg>
                        <span>Masa aktif 30 hari sejak disetujui Admin</span>
                    </li>
                </ul>
            </div>
        </div>

        {{-- Kolom Kanan: Form Pembayaran & Upload Bukti --}}
        <div class="lg:col-span-7">
            <div class="bg-white rounded-3xl border border-slate-200/90 p-6 sm:p-8 shadow-sm">
                <h2 class="text-lg font-bold text-slate-900 mb-1">
                    Instruksi Pembayaran QRIS Statis
                </h2>
                <p class="text-xs text-slate-500 mb-6">
                    Silakan scan kode QRIS resmi Lungsur.in di bawah ini dan bayar tepat senilai <strong>Rp 5.000</strong>. Mendukung semua aplikasi e-Wallet dan Mobile Banking.
                </p>

                {{-- Kotak QRIS Statis --}}
                <div class="mb-6">
                    <x-qris-static-box :amount="5000" accent="amber" />
                </div>

                {{-- Formulir Upload Bukti Transfer --}}
                <form action="{{ route('boost.store', $listing->id) }}" method="POST" enctype="multipart/form-data" class="space-y-5" x-data="{ previewUrl: null }">
                    @csrf
                    <input type="hidden" name="payment_channel" value="qris">

                    {{-- Banner Metode Pembayaran --}}
                    <div class="rounded-2xl bg-amber-50/80 border border-amber-200/70 p-3.5 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-xl bg-amber-500 text-white flex items-center justify-center font-bold text-sm shadow-xs">
                                ⚡
                            </div>
                            <div>
                                <div class="text-xs font-bold text-slate-800">Metode Pembayaran</div>
                                <div class="text-[11px] text-slate-600 font-medium">QRIS Statis Lungsur.in (Semua Bank & e-Wallet)</div>
                            </div>
                        </div>
                        <span class="px-2.5 py-1 rounded-full bg-emerald-100 text-emerald-700 text-[10px] font-black uppercase tracking-wider">
                            QRIS Statis
                        </span>
                    </div>

                    {{-- Unggah Gambar Bukti Transfer --}}
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-2">
                            Unggah Foto Struk / Screenshot Bukti Pembayaran QRIS <span class="text-rose-500">*</span>
                        </label>
                        
                        <div class="border-2 border-dashed border-slate-300 rounded-2xl p-4 sm:p-6 text-center hover:border-amber-400 transition bg-slate-50/50">
                            {{-- Preview Gambar --}}
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
                                    Klik untuk memilih berkas struk pembayaran
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
                                   class="text-xs text-slate-500 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-amber-100 file:text-amber-800 hover:file:bg-amber-200 cursor-pointer">
                        </div>
                        @error('proof_image')
                            <p class="text-rose-600 text-xs mt-1 font-semibold">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Tombol Submit --}}
                    <div class="pt-2 flex flex-col sm:flex-row items-center gap-3">
                        <button type="submit" 
                                class="w-full sm:flex-1 py-3 px-6 rounded-2xl bg-amber-500 hover:bg-amber-600 active:bg-amber-700 text-white font-bold text-sm tracking-wide shadow-md shadow-amber-500/20 transition cursor-pointer flex items-center justify-center gap-2">
                            <span>⚡ Kirim Bukti QRIS & Ajukan Boost (Rp 5.000)</span>
                        </button>
                        <a href="{{ route('dashboard') }}" 
                           class="w-full sm:w-auto py-3 px-5 rounded-2xl border border-slate-200 text-slate-600 hover:bg-slate-50 font-bold text-xs text-center transition">
                            Batal
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

