<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>ADMIN - Detail Mahasiswa: {{ $user->name }} | Lungsur.in</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>[x-cloak] { display: none !important; }</style>
</head>
<body class="bg-slate-100 text-slate-800 antialiased font-sans">
    <x-flash-message />

    <div class="flex min-h-screen" x-data="{
        actionModalOpen: false,
        actionType: '',
        targetUserName: '{{ addslashes($user->name) }}',
        actionUrl: '',
        reason: '',
        openActionModal(type, url) {
            this.actionType = type;
            this.actionUrl = url;
            this.reason = '';
            this.actionModalOpen = true;
        }
    }">
        <!-- Sidebar Terpadu -->
        <x-admin-sidebar active="users" />

        <!-- Main Content -->
        <main class="flex-1 min-w-0 p-4 sm:p-8 overflow-y-auto">
            <!-- Back navigation & Actions Header -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
                <div>
                    <a href="{{ route('admin.users.index') }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-slate-500 hover:text-slate-800 mb-2 transition">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                        <span>Kembali ke Daftar Pengguna</span>
                    </a>
                    <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Detail Profil Mahasiswa</h1>
                </div>

                <div class="flex items-center gap-2">
                    @if($user->status === 'suspended' || $user->status === 'banned')
                        <form method="POST" action="{{ route('admin.users.reactivate', $user) }}" onsubmit="return confirm('Pulihkan status akun {{ $user->name }} menjadi aktif?')">
                            @csrf
                            <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold transition shadow-xs cursor-pointer">
                                <span>✓</span> Pulihkan Status Akun
                            </button>
                        </form>
                    @else
                        <button type="button" 
                                @click="openActionModal('suspend', '{{ route('admin.users.suspend', $user) }}')"
                                class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-amber-500 hover:bg-amber-600 text-slate-950 text-xs font-bold transition shadow-xs cursor-pointer">
                            <span>⚠️</span> Tangguhkan (Suspend)
                        </button>
                        <button type="button" 
                                @click="openActionModal('ban', '{{ route('admin.users.ban', $user) }}')"
                                class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold transition shadow-xs cursor-pointer">
                            <span>🚫</span> Blokir Permanen (Ban)
                        </button>
                    @endif
                </div>
            </div>

            <!-- Profile Summary Card -->
            <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-2xs mb-8">
                <div class="flex flex-col md:flex-row md:items-start justify-between gap-6">
                    <div class="flex items-start gap-4">
                        <div class="w-16 h-16 rounded-2xl bg-slate-900 text-white font-extrabold text-2xl flex items-center justify-center shrink-0 shadow-sm">
                            {{ strtoupper(mb_substr($user->name, 0, 1)) }}
                        </div>
                        <div>
                            <div class="flex items-center gap-2.5">
                                <h2 class="text-xl font-extrabold text-slate-900">{{ $user->name }}</h2>
                                @if($user->status === 'banned')
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-rose-100 text-rose-700 border border-rose-200">Diblokir</span>
                                @elseif($user->status === 'suspended')
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-orange-100 text-orange-700 border border-orange-200">Ditangguhkan</span>
                                @elseif($user->verification_status === 'pending')
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200">Pending KTM</span>
                                @else
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">Aktif</span>
                                @endif
                            </div>
                            <p class="text-xs text-slate-500 mt-0.5">{{ $user->email }}</p>

                            <div class="mt-3 flex flex-wrap items-center gap-3 text-xs text-slate-600">
                                <div><strong>Kampus:</strong> {{ $user->campus ?? '-' }}</div>
                                <div>•</div>
                                <div><strong>NIM:</strong> {{ $user->nim ?? '-' }}</div>
                                <div>•</div>
                                <div><strong>Jalur:</strong> {{ $user->email_type === 'campus' ? 'Email Kampus Resmi' : 'Email Umum + KTM' }}</div>
                                <div>•</div>
                                <div><strong>Bergabung:</strong> {{ $user->created_at->translatedFormat('d M Y') }}</div>
                            </div>

                            @if($user->rejection_reason)
                                <div class="mt-4 p-3.5 rounded-xl bg-rose-50 border border-rose-200 text-xs text-rose-800">
                                    <strong>Catatan Sanksi / Alasan:</strong> {{ $user->rejection_reason }}
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Ringkasan Statistik Pengguna -->
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 bg-slate-50 p-4 rounded-xl border border-slate-200/80 shrink-0">
                        <div class="text-center px-2">
                            <span class="block text-lg font-black text-slate-900">{{ $user->listings_count }}</span>
                            <span class="text-[10px] uppercase font-bold text-slate-400">Total Iklan</span>
                        </div>
                        <div class="text-center px-2 border-l border-slate-200">
                            <span class="block text-lg font-black text-slate-900">{{ $user->bids_count }}</span>
                            <span class="text-[10px] uppercase font-bold text-slate-400">Tawaran Lelang</span>
                        </div>
                        <div class="text-center px-2 border-l border-slate-200">
                            <span class="block text-lg font-black text-slate-900">{{ $user->boost_payments_count }}</span>
                            <span class="text-[10px] uppercase font-bold text-slate-400">Boost Bayar</span>
                        </div>
                        <div class="text-center px-2 border-l border-slate-200">
                            <span class="block text-lg font-black text-slate-900">{{ $user->auction_payments_count }}</span>
                            <span class="text-[10px] uppercase font-bold text-slate-400">Lelang Bayar</span>
                        </div>
                    </div>
                </div>

                <!-- Foto KTM jika pendaftar email umum -->
                @if($user->ktm_image_path)
                    <div class="mt-6 pt-6 border-t border-slate-200">
                        <h3 class="text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Berkas Kartu Tanda Mahasiswa (KTM)</h3>
                        <div class="inline-block rounded-xl overflow-hidden border border-slate-300 max-w-sm">
                            <img src="{{ asset('storage/' . $user->ktm_image_path) }}" alt="Foto KTM {{ $user->name }}" class="w-full h-auto object-cover max-h-56">
                        </div>
                    </div>
                @endif
            </div>

            <!-- Grid 2 Kolom: Riwayat Iklan & Riwayat Laporan -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Kolom 1: Riwayat Iklan Barang -->
                <div class="bg-white rounded-2xl border border-slate-200 shadow-2xs overflow-hidden">
                    <div class="px-6 py-4 border-b border-slate-200/80">
                        <h3 class="text-sm font-extrabold text-slate-900">Riwayat Postingan Iklan</h3>
                        <p class="text-xs text-slate-400">Katalog barang yang pernah/sedang diiklankan</p>
                    </div>

                    <div class="divide-y divide-slate-100">
                        @forelse($recentListings as $listing)
                            <div class="p-4 flex items-center justify-between hover:bg-slate-50/80">
                                <div>
                                    <div class="font-bold text-xs text-slate-800">{{ $listing->title }}</div>
                                    <div class="text-[11px] text-slate-500 mt-0.5">
                                        Rp{{ number_format($listing->price, 0, ',', '.') }} • {{ $listing->category?->name ?? 'Kategori' }}
                                    </div>
                                </div>
                                <div class="text-right">
                                    @if($listing->status === 'available')
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">Tersedia</span>
                                    @elseif($listing->status === 'archived')
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-600 border border-slate-200">Ditugaskan/Arsip</span>
                                    @else
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-700">{{ ucfirst($listing->status) }}</span>
                                    @endif
                                    <div class="text-[10px] text-slate-400 mt-0.5">{{ $listing->created_at->format('d M Y') }}</div>
                                </div>
                            </div>
                        @empty
                            <div class="p-8 text-center text-xs text-slate-400">
                                Mahasiswa ini belum pernah memposting barang di katalog.
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- Kolom 2: Riwayat Laporan / Aduan Terhadap Akun Ini -->
                <div class="bg-white rounded-2xl border border-slate-200 shadow-2xs overflow-hidden">
                    <div class="px-6 py-4 border-b border-slate-200/80">
                        <h3 class="text-sm font-extrabold text-slate-900">Riwayat Laporan Aduan Masuk</h3>
                        <p class="text-xs text-slate-400">Tiket pengaduan dari mahasiswa lain terkait akun ini</p>
                    </div>

                    <div class="divide-y divide-slate-100">
                        @forelse($reportsReceived as $report)
                            <div class="p-4 hover:bg-slate-50/80">
                                <div class="flex items-center justify-between mb-1">
                                    <span class="font-bold text-xs text-rose-700">🚩 {{ $report->reason }}</span>
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $report->status === 'resolved' ? 'bg-emerald-50 text-emerald-700' : ($report->status === 'reviewed' ? 'bg-slate-100 text-slate-700' : 'bg-amber-50 text-amber-700') }}">
                                        {{ ucfirst($report->status) }}
                                    </span>
                                </div>
                                <p class="text-[11px] text-slate-600">{{ $report->details ?? 'Tidak ada rincian tambahan' }}</p>
                                <div class="mt-2 text-[10px] text-slate-400 flex items-center justify-between">
                                    <span>Pelapor: {{ $report->reporter?->name ?? 'Anonim' }}</span>
                                    <span>{{ $report->created_at->translatedFormat('d M Y H:i') }}</span>
                                </div>
                            </div>
                        @empty
                            <div class="p-8 text-center text-xs text-slate-400">
                                Bersih — Tidak ada laporan pengaduan terhadap akun mahasiswa ini.
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </main>

        <!-- Modal Dialog Suspend / Ban (Alpine.js) -->
        <div x-show="actionModalOpen" 
             x-cloak 
             class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
            <div @click.away="actionModalOpen = false" 
                 class="w-full max-w-md bg-white rounded-2xl shadow-xl border border-slate-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between"
                     :class="actionType === 'ban' ? 'bg-rose-50' : 'bg-amber-50'">
                    <div class="flex items-center gap-2.5">
                        <span class="text-lg" x-text="actionType === 'ban' ? '🚫' : '⚠️'"></span>
                        <h2 class="text-sm font-extrabold text-slate-900" 
                            x-text="actionType === 'ban' ? 'Konfirmasi Pemblokiran Permanen' : 'Konfirmasi Penangguhan Akun'">
                        </h2>
                    </div>
                    <button type="button" @click="actionModalOpen = false" class="text-slate-400 hover:text-slate-600">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <form :action="actionUrl" method="POST" class="p-6">
                    @csrf
                    <p class="text-xs text-slate-600 mb-3">
                        Anda akan <span class="font-bold" x-text="actionType === 'ban' ? 'memblokir permanen' : 'menangguhkan'"></span> akun:
                        <strong class="text-slate-900 block mt-0.5 text-sm" x-text="targetUserName"></strong>
                    </p>

                    <template x-if="actionType === 'ban'">
                        <div class="p-3 mb-4 rounded-xl bg-rose-50 border border-rose-200 text-[11px] text-rose-700">
                            <strong>Peringatan Sistem:</strong> Seluruh iklan aktif milik mahasiswa ini akan otomatis diturunkan dari katalog publik.
                        </div>
                    </template>

                    <div class="mb-4">
                        <label for="reason" class="block text-xs font-bold text-slate-700 mb-1.5">
                            Alasan Tindakan Disiplin <span class="text-rose-500">*</span>
                        </label>
                        <textarea name="reason" 
                                  id="reason" 
                                  rows="3" 
                                  required 
                                  x-model="reason"
                                  placeholder="Tuliskan alasan penangguhan atau pemblokiran..."
                                  class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs text-slate-800 placeholder-slate-400 focus:outline-hidden focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500"></textarea>
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100">
                        <button type="button" 
                                @click="actionModalOpen = false" 
                                class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition">
                            Batal
                        </button>
                        <button type="submit" 
                                class="px-4 py-2 rounded-xl text-white text-xs font-bold transition shadow-xs"
                                :class="actionType === 'ban' ? 'bg-rose-600 hover:bg-rose-700' : 'bg-amber-600 hover:bg-amber-700'"
                                x-text="actionType === 'ban' ? 'Blokir & Turunkan Iklan' : 'Tangguhkan Akun'">
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
