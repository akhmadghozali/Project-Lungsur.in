<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>ADMIN - Manajemen Pengguna & Pemblokiran | Lungsur.in</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>[x-cloak] { display: none !important; }</style>
</head>
<body class="bg-slate-100 text-slate-800 antialiased font-sans">
    <x-flash-message />

    <div class="flex min-h-screen" x-data="{
        actionModalOpen: false,
        actionType: '',
        targetUserName: '',
        actionUrl: '',
        reason: '',
        openActionModal(type, name, url) {
            this.actionType = type;
            this.targetUserName = name;
            this.actionUrl = url;
            this.reason = '';
            this.actionModalOpen = true;
        }
    }">
        <!-- Sidebar Terpadu -->
        <x-admin-sidebar active="users" />

        <!-- Main Content -->
        <main class="flex-1 min-w-0 p-4 sm:p-8 overflow-y-auto">
            <!-- Header Page -->
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-8">
                <div>
                    <div class="flex items-center gap-3">
                        <span class="inline-flex items-center justify-center w-10 h-10 rounded-2xl bg-indigo-600 text-white font-black text-lg shadow-sm">
                            👥
                        </span>
                        <div>
                            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Manajemen Pengguna & Pemblokiran</h1>
                            <p class="text-xs text-slate-500 font-medium">Pengawasan data civitas akademika, penangguhan hak akses, dan sanksi pemblokiran</p>
                        </div>
                    </div>
                </div>

                <!-- Form Pencarian -->
                <form method="GET" action="{{ route('admin.users.index') }}" class="flex items-center gap-2">
                    <input type="hidden" name="status" value="{{ $statusFilter }}">
                    <div class="relative">
                        <input type="text" 
                               name="q" 
                               value="{{ $search }}" 
                               placeholder="Cari nama, NIM, email, kampus..."
                               class="w-64 sm:w-80 px-3.5 py-2 pl-9 bg-white border border-slate-300 rounded-xl text-xs text-slate-800 placeholder-slate-400 focus:outline-hidden focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 shadow-2xs">
                        <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                    <button type="submit" class="px-3.5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-xl shadow-2xs transition">
                        Cari
                    </button>
                    @if(!empty($search))
                        <a href="{{ route('admin.users.index', ['status' => $statusFilter]) }}" class="px-3 py-2 bg-slate-200 hover:bg-slate-300 text-slate-700 text-xs font-bold rounded-xl transition">
                            Reset
                        </a>
                    @endif
                </form>
            </div>

            <!-- Filter Status Tabs -->
            <div class="flex flex-wrap items-center gap-2 mb-6 border-b border-slate-200 pb-3">
                <a href="{{ route('admin.users.index', ['status' => 'all', 'q' => $search]) }}"
                   class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs font-bold transition {{ $statusFilter === 'all' ? 'bg-indigo-600 text-white shadow-xs' : 'bg-white text-slate-600 hover:bg-slate-50 border border-slate-200' }}">
                    <span>Semua Akun</span>
                    <span class="px-1.5 py-0.5 rounded-full text-[10px] {{ $statusFilter === 'all' ? 'bg-indigo-500 text-white' : 'bg-slate-100 text-slate-600' }}">
                        {{ $tabCounts['all'] }}
                    </span>
                </a>

                <a href="{{ route('admin.users.index', ['status' => 'active', 'q' => $search]) }}"
                   class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs font-bold transition {{ $statusFilter === 'active' ? 'bg-emerald-600 text-white shadow-xs' : 'bg-white text-slate-600 hover:bg-slate-50 border border-slate-200' }}">
                    <span>Aktif Terverifikasi</span>
                    <span class="px-1.5 py-0.5 rounded-full text-[10px] {{ $statusFilter === 'active' ? 'bg-emerald-500 text-white' : 'bg-slate-100 text-slate-600' }}">
                        {{ $tabCounts['active'] }}
                    </span>
                </a>

                <a href="{{ route('admin.users.index', ['status' => 'pending_ktm', 'q' => $search]) }}"
                   class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs font-bold transition {{ $statusFilter === 'pending_ktm' ? 'bg-amber-600 text-white shadow-xs' : 'bg-white text-slate-600 hover:bg-slate-50 border border-slate-200' }}">
                    <span>Pending Verifikasi KTM</span>
                    <span class="px-1.5 py-0.5 rounded-full text-[10px] {{ $statusFilter === 'pending_ktm' ? 'bg-amber-500 text-white' : 'bg-slate-100 text-slate-600' }}">
                        {{ $tabCounts['pending_ktm'] }}
                    </span>
                </a>

                <a href="{{ route('admin.users.index', ['status' => 'suspended', 'q' => $search]) }}"
                   class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs font-bold transition {{ $statusFilter === 'suspended' ? 'bg-orange-600 text-white shadow-xs' : 'bg-white text-slate-600 hover:bg-slate-50 border border-slate-200' }}">
                    <span>Ditangguhkan (Suspended)</span>
                    <span class="px-1.5 py-0.5 rounded-full text-[10px] {{ $statusFilter === 'suspended' ? 'bg-orange-500 text-white' : 'bg-slate-100 text-slate-600' }}">
                        {{ $tabCounts['suspended'] }}
                    </span>
                </a>

                <a href="{{ route('admin.users.index', ['status' => 'banned', 'q' => $search]) }}"
                   class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs font-bold transition {{ $statusFilter === 'banned' ? 'bg-rose-600 text-white shadow-xs' : 'bg-white text-slate-600 hover:bg-slate-50 border border-slate-200' }}">
                    <span>Diblokir (Banned)</span>
                    <span class="px-1.5 py-0.5 rounded-full text-[10px] {{ $statusFilter === 'banned' ? 'bg-rose-500 text-white' : 'bg-slate-100 text-slate-600' }}">
                        {{ $tabCounts['banned'] }}
                    </span>
                </a>
            </div>

            <!-- Users Table Card -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-2xs overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-50 border-b border-slate-200 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                            <tr>
                                <th class="px-6 py-3.5">Mahasiswa</th>
                                <th class="px-4 py-3.5">Asal Kampus / NIM</th>
                                <th class="px-4 py-3.5">Jalur Akun</th>
                                <th class="px-4 py-3.5">Status Akun</th>
                                <th class="px-4 py-3.5 text-center">Iklan Aktif</th>
                                <th class="px-6 py-3.5 text-right">Tindakan Admin</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 font-medium">
                            @forelse($users as $user)
                                <tr class="hover:bg-slate-50/80 transition">
                                    <!-- Mahasiswa -->
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-9 h-9 rounded-full bg-slate-900 text-white font-extrabold flex items-center justify-center shrink-0 text-xs">
                                                {{ strtoupper(mb_substr($user->name, 0, 1)) }}
                                            </div>
                                            <div class="min-w-0">
                                                <a href="{{ route('admin.users.show', $user) }}" class="font-bold text-slate-900 hover:text-indigo-600 hover:underline">
                                                    {{ $user->name }}
                                                </a>
                                                <div class="text-[11px] text-slate-400 truncate">{{ $user->email }}</div>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Kampus & NIM -->
                                    <td class="px-4 py-4">
                                        <div class="font-semibold text-slate-800">{{ $user->campus ?? '-' }}</div>
                                        <div class="text-[11px] text-slate-400">NIM: {{ $user->nim ?? 'Belum ada' }}</div>
                                    </td>

                                    <!-- Jalur Akun -->
                                    <td class="px-4 py-4">
                                        @if($user->email_type === 'campus')
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-200">
                                                <span>🎓</span> Email Kampus
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[10px] font-bold bg-slate-100 text-slate-700 border border-slate-200">
                                                <span>📄</span> Email Publik (KTM)
                                            </span>
                                        @endif
                                    </td>

                                    <!-- Status Akun -->
                                    <td class="px-4 py-4">
                                        @if($user->status === 'banned')
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-rose-100 text-rose-700 border border-rose-200">
                                                Diblokir Permanen
                                            </span>
                                            @if($user->rejection_reason)
                                                <div class="text-[10px] text-rose-500 mt-1 max-w-[200px] truncate" title="{{ $user->rejection_reason }}">
                                                    Alasan: {{ $user->rejection_reason }}
                                                </div>
                                            @endif
                                        @elseif($user->status === 'suspended')
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-orange-100 text-orange-700 border border-orange-200">
                                                Ditangguhkan
                                            </span>
                                            @if($user->rejection_reason)
                                                <div class="text-[10px] text-orange-500 mt-1 max-w-[200px] truncate" title="{{ $user->rejection_reason }}">
                                                    Alasan: {{ $user->rejection_reason }}
                                                </div>
                                            @endif
                                        @elseif($user->verification_status === 'pending')
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                                Pending KTM
                                            </span>
                                        @elseif($user->verification_status === 'rejected')
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-slate-100 text-slate-700 border border-slate-200">
                                                KTM Ditolak
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                Aktif
                                            </span>
                                        @endif
                                    </td>

                                    <!-- Iklan -->
                                    <td class="px-4 py-4 text-center">
                                        <span class="font-extrabold text-slate-700">{{ $user->listings_count }}</span>
                                    </td>

                                    <!-- Tindakan -->
                                    <td class="px-6 py-4 text-right">
                                        <div class="inline-flex items-center gap-1.5">
                                            <a href="{{ route('admin.users.show', $user) }}" 
                                               class="px-2.5 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 text-[11px] font-bold transition">
                                                Detail
                                            </a>

                                            @if($user->status === 'suspended' || $user->status === 'banned')
                                                <!-- Form Pulihkan Akun -->
                                                <form method="POST" action="{{ route('admin.users.reactivate', $user) }}" onsubmit="return confirm('Pulihkan kembali akun {{ $user->name }} menjadi aktif?')">
                                                    @csrf
                                                    <button type="submit" class="px-2.5 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-[11px] font-bold transition cursor-pointer">
                                                        Pulihkan
                                                    </button>
                                                </form>
                                            @else
                                                <!-- Tombol Tangguhkan -->
                                                <button type="button" 
                                                        @click="openActionModal('suspend', '{{ addslashes($user->name) }}', '{{ route('admin.users.suspend', $user) }}')"
                                                        class="px-2.5 py-1.5 rounded-lg bg-amber-500 hover:bg-amber-600 text-slate-950 text-[11px] font-bold transition cursor-pointer">
                                                    Suspend
                                                </button>

                                                <!-- Tombol Blokir -->
                                                <button type="button" 
                                                        @click="openActionModal('ban', '{{ addslashes($user->name) }}', '{{ route('admin.users.ban', $user) }}')"
                                                        class="px-2.5 py-1.5 rounded-lg bg-rose-600 hover:bg-rose-700 text-white text-[11px] font-bold transition cursor-pointer">
                                                    Blokir
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-12 text-center text-slate-400">
                                        Tidak ditemukan data mahasiswa yang sesuai dengan filter atau kata kunci pencarian.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($users->hasPages())
                    <div class="px-6 py-4 border-t border-slate-200 bg-slate-50">
                        {{ $users->links() }}
                    </div>
                @endif
            </div>
        </main>

        <!-- Modal Dialog Suspend / Ban (Alpine.js) -->
        <div x-show="actionModalOpen" 
             x-cloak 
             class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
            <div @click.away="actionModalOpen = false" 
                 class="w-full max-w-md bg-white rounded-2xl shadow-xl border border-slate-200 overflow-hidden">
                <!-- Modal Header -->
                <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between"
                     :class="actionType === 'ban' ? 'bg-rose-50' : 'bg-amber-50'">
                    <div class="flex items-center gap-2.5">
                        <span class="text-lg" x-text="actionType === 'ban' ? '🚫' : '⚠️'"></span>
                        <h2 class="text-sm font-extrabold text-slate-900" 
                            x-text="actionType === 'ban' ? 'Konfirmasi Pemblokiran Permanen' : 'Konfirmasi Penangguhan Akun'">
                        </h2>
                    </div>
                    <button type="button" @click="actionModalOpen = false" class="text-slate-400 hover:text-slate-600">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <!-- Modal Body Form -->
                <form :action="actionUrl" method="POST" class="p-6">
                    @csrf
                    <p class="text-xs text-slate-600 mb-3">
                        Anda akan <span class="font-bold" x-text="actionType === 'ban' ? 'memblokir permanen' : 'menangguhkan'"></span> akun:
                        <strong class="text-slate-900 block mt-0.5 text-sm" x-text="targetUserName"></strong>
                    </p>

                    <template x-if="actionType === 'ban'">
                        <div class="p-3 mb-4 rounded-xl bg-rose-50 border border-rose-200 text-[11px] text-rose-700">
                            <strong>Peringatan Sistem (UC 15):</strong> Seluruh postingan iklan aktif milik mahasiswa ini akan otomatis diturunkan dari katalog publik, dan akses masuk akan dikunci.
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
                                  placeholder="Contoh: Terbukti melakukan troll bidding berulang / penipuan transfer / laporan aduan terverifikasi..."
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
