@extends('layouts.app')

@section('content')
<div class="space-y-8 py-4">

    <!-- Top Greeting & Verification Banner -->
    <div class="bg-gradient-to-r from-blue-600 to-indigo-700 rounded-3xl p-6 sm:p-8 text-white shadow-xl shadow-blue-500/10 relative overflow-hidden">
        <!-- Background Decoration Rings -->
        <div class="absolute -right-10 -bottom-10 w-64 h-64 rounded-full border border-white/10 pointer-events-none"></div>
        <div class="absolute -right-4 -bottom-4 w-48 h-48 rounded-full border border-white/15 pointer-events-none"></div>

        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="space-y-2">
                <div class="inline-flex items-center gap-2 bg-white/15 backdrop-blur-xs px-3 py-1 rounded-full text-xs font-semibold text-blue-100">
                    <svg class="w-4 h-4 text-emerald-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                    </svg>
                    <span>Mahasiswa Terverifikasi &bull; {{ auth()->user()->campus ?? 'Politeknik Negeri Malang' }}</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-black tracking-tight">
                    Halo, {{ auth()->user()->name }}! 👋
                </h1>
                <p class="text-sm text-blue-100 max-w-xl leading-relaxed">
                    Selamat datang di Dasbor Lungsur.in. Akun Anda telah aktif dan memiliki akses penuh untuk jual, beli, dan lelang perabot kos antar mahasiswa Malang.
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-3 shrink-0">
                <a href="{{ url('/') }}" 
                   class="px-5 py-3 rounded-2xl bg-white text-blue-600 font-bold text-sm shadow-md hover:bg-blue-50 transition">
                    Jelajahi Beranda
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" 
                            class="px-5 py-3 rounded-2xl bg-white/10 hover:bg-white/20 text-white font-bold text-sm transition border border-white/20">
                        Keluar
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- User Profile & Status Details -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Kartu Identitas Akun Mahasiswa -->
        <div class="lg:col-span-1 bg-white border border-slate-200/80 rounded-3xl p-6 shadow-sm space-y-5">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-2xl bg-blue-600 text-white font-black text-xl flex items-center justify-center shadow-md shadow-blue-500/20">
                    {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                </div>
                <div>
                    <h3 class="font-bold text-slate-900 text-base">{{ auth()->user()->name }}</h3>
                    <p class="text-xs text-slate-500 font-medium">{{ auth()->user()->email }}</p>
                    <span class="inline-block mt-1 text-[11px] font-extrabold uppercase px-2 py-0.5 rounded-md bg-emerald-100 text-emerald-800">
                        {{ auth()->user()->verification_status === 'active' ? 'Akun Aktif' : auth()->user()->verification_status }}
                    </span>
                </div>
            </div>

            <div class="border-t border-slate-100 pt-4 space-y-3 text-xs">
                <div class="flex justify-between py-1 border-b border-slate-50">
                    <span class="text-slate-500 font-medium">Asal Kampus</span>
                    <span class="font-bold text-slate-800 text-right">{{ auth()->user()->campus ?? '-' }}</span>
                </div>
                <div class="flex justify-between py-1 border-b border-slate-50">
                    <span class="text-slate-500 font-medium">Jalur Validasi</span>
                    <span class="font-bold text-slate-800">
                        {{ auth()->user()->email_type === 'campus' ? 'Email Kampus Otomatis' : 'Verifikasi KTM Admin' }}
                    </span>
                </div>
                <div class="flex justify-between py-1 border-b border-slate-50">
                    <span class="text-slate-500 font-medium">Nomor WhatsApp</span>
                    <span class="font-bold text-slate-800">+62 {{ auth()->user()->phone ?? '-' }}</span>
                </div>
                <div class="flex justify-between py-1">
                    <span class="text-slate-500 font-medium">Bergabung Sejak</span>
                    <span class="font-bold text-slate-800">{{ auth()->user()->created_at?->translatedFormat('d F Y') ?? date('d M Y') }}</span>
                </div>
            </div>
        </div>

        <!-- Statistik Ringkas Aktivitas -->
        <div class="lg:col-span-2 grid grid-cols-1 sm:grid-cols-3 gap-4">
            
            <div class="bg-white border border-slate-200/80 rounded-3xl p-6 shadow-sm flex flex-col justify-between">
                <div>
                    <div class="w-10 h-10 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center mb-3">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                    </div>
                    <div class="text-xs font-semibold text-slate-500">Iklan Barang Saya</div>
                    <div class="text-2xl font-black text-slate-900 mt-1">0</div>
                </div>
                <span class="text-[11px] text-blue-600 font-semibold mt-4">Belum ada iklan aktif</span>
            </div>

            <div class="bg-white border border-slate-200/80 rounded-3xl p-6 shadow-sm flex flex-col justify-between">
                <div>
                    <div class="w-10 h-10 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center mb-3">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="text-xs font-semibold text-slate-500">Lelang Diikuti</div>
                    <div class="text-2xl font-black text-slate-900 mt-1">0</div>
                </div>
                <span class="text-[11px] text-amber-600 font-semibold mt-4">Tidak ada tawaran berjalan</span>
            </div>

            <div class="bg-white border border-slate-200/80 rounded-3xl p-6 shadow-sm flex flex-col justify-between">
                <div>
                    <div class="w-10 h-10 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center mb-3">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                        </svg>
                    </div>
                    <div class="text-xs font-semibold text-slate-500">Pesan Chat Masuk</div>
                    <div class="text-2xl font-black text-slate-900 mt-1">0</div>
                </div>
                <span class="text-[11px] text-emerald-600 font-semibold mt-4">Kotak masuk bersih</span>
            </div>

        </div>

    </div>

</div>
@endsection
