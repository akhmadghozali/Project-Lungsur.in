<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>ADMIN - Verifikasi Biaya Pembukaan Lelang | Lungsur.in</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>[x-cloak]{display:none !important;}</style>
</head>
<body class="bg-slate-100 text-slate-800 antialiased">
    <x-flash-message />
@php
    $admin = auth()->user();
    $adminRoleLabel = match ($admin->role ?? 'admin') {
        'admin' => 'Administrator',
        default => ucfirst($admin->role ?? 'Admin'),
    };
    $adminInitial = strtoupper(mb_substr($admin->name ?? 'A', 0, 1));
    $pendingUsers = \App\Models\User::where('verification_status', 'pending')->count();
    $pendingBoostCount = \App\Models\BoostPayment::where('status', 'pending')->count();
@endphp

<div class="flex min-h-screen" 
     x-data="{ 
        modalImage: null, 
        rejectModalOpen: false, 
        rejectActionUrl: '', 
        rejectInvoice: '',
        profileOpen: false,
        openReject(url, invoice) {
            this.rejectActionUrl = url;
            this.rejectInvoice = invoice;
            this.rejectModalOpen = true;
        }
     }">

    <x-admin-sidebar active="payments.auction" />

    <div class="flex min-w-0 flex-1 flex-col">

        <header class="flex items-center justify-between gap-4 border-b bg-white px-4 sm:px-6 py-3">
            <div class="flex items-center gap-2">
                <span class="text-xs font-bold text-slate-600">Panel Admin</span>
                <span class="text-slate-400">/</span>
                <span class="text-xs font-bold text-slate-900">Verifikasi Pembayaran Lelang</span>
            </div>

            <div class="relative" @click.outside="profileOpen = false">
                <button type="button" @click="profileOpen = !profileOpen"
                        class="min-h-[44px] flex items-center gap-3 rounded-full py-1 pl-3 pr-1 text-right transition hover:bg-slate-100 cursor-pointer">
                    <div class="hidden leading-tight sm:block">
                        <div class="text-sm font-semibold text-slate-900">{{ $admin->name }}</div>
                    </div>
                    <div class="flex h-9 w-9 items-center justify-center rounded-full bg-blue-600 text-sm font-bold text-white shadow-xs">
                        {{ $adminInitial }}
                    </div>
                </button>

                <div x-show="profileOpen" x-cloak x-transition
                     class="absolute right-0 z-40 mt-2 w-60 overflow-hidden rounded-xl border bg-white shadow-lg">
                    <div class="border-b px-4 py-3">
                        <div class="text-sm font-semibold text-slate-900">{{ $admin->name }}</div>
                        <div class="truncate text-xs text-slate-600">{{ $admin->email }}</div>
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="min-h-[44px] flex w-full items-center gap-2 px-4 py-3 text-left text-sm font-semibold text-red-600 hover:bg-red-50 cursor-pointer">
                            ⎋ Keluar Akun
                        </button>
                    </form>
                </div>
            </div>
        </header>

        <nav aria-label="Menu Admin Mobile" class="lg:hidden bg-[#1c2541] px-3 py-2.5 flex items-center gap-2 overflow-x-auto no-scrollbar border-b border-slate-800">
            <a href="{{ route('admin.dashboard') }}"
               class="min-h-[44px] shrink-0 inline-flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs font-bold text-slate-200 bg-white/10 hover:bg-white/15">
                <span>📊 Dashboard</span>
            </a>
            <a href="{{ route('admin.verifications.index') }}"
               class="min-h-[44px] shrink-0 inline-flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs font-bold text-slate-200 bg-white/10 hover:bg-white/15">
                <span>🛡 Verifikasi KTM</span>
                @if ($pendingUsers > 0)
                    <span class="rounded-full bg-red-500 px-1.5 py-0.5 text-[10px] font-extrabold text-white">{{ $pendingUsers }}</span>
                @endif
            </a>
            <a href="{{ route('admin.payments.boost') }}"
               class="min-h-[44px] shrink-0 inline-flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs font-bold text-slate-200 bg-white/10 hover:bg-white/15">
                <span>⚡ Boost Iklan</span>
                @if ($pendingBoostCount > 0)
                    <span class="rounded-full bg-amber-500 px-1.5 py-0.5 text-[10px] font-extrabold text-slate-950">{{ $pendingBoostCount }}</span>
                @endif
            </a>
            <a href="{{ route('admin.payments.auction') }}"
               class="min-h-[44px] shrink-0 inline-flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs font-bold bg-emerald-700 text-white shadow-xs">
                <span>🔨 Biaya Lelang</span>
                @if ($stats['pending'] > 0)
                    <span class="rounded-full bg-white px-1.5 py-0.5 text-[10px] font-extrabold text-emerald-800">{{ $stats['pending'] }}</span>
                @endif
            </a>
            <a href="{{ route('admin.reports.index') }}"
               class="min-h-[44px] shrink-0 inline-flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs font-bold text-slate-200 bg-white/10 hover:bg-white/15">
                <span>⚑ Laporan Iklan</span>
            </a>
        </nav>

        <main class="p-4 sm:p-6 space-y-6">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight flex items-center gap-2">
                        <span>🔨 Verifikasi Biaya Pembukaan Sesi Lelang</span>
                    </h1>
                    <p class="text-xs sm:text-sm text-slate-600 mt-1">
                        Tinjau bukti transfer biaya administrasi pembukaan lelang (Rp 5.000). Begitu disetujui, sesi lelang resmi dipublikasikan dan dibuka untuk penawaran.
                    </p>
                </div>
            </div>

            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
                <div class="bg-white p-3.5 sm:p-4 rounded-2xl border border-slate-200/80 shadow-xs">
                    <span class="text-[11px] font-bold text-amber-700 uppercase tracking-wider">Antrean Pending</span>
                    <p class="text-2xl font-black text-slate-900 mt-1">{{ $stats['pending'] }}</p>
                    <span class="text-[11px] text-slate-600">Menunggu verifikasi lelang</span>
                </div>
                <div class="bg-white p-3.5 sm:p-4 rounded-2xl border border-slate-200/80 shadow-xs">
                    <span class="text-[11px] font-bold text-emerald-700 uppercase tracking-wider">Disetujui Hari Ini</span>
                    <p class="text-2xl font-black text-slate-900 mt-1">{{ $stats['approved_today'] }}</p>
                    <span class="text-[11px] text-slate-600">Sesi lelang aktif hari ini</span>
                </div>
                <div class="bg-white p-3.5 sm:p-4 rounded-2xl border border-slate-200/80 shadow-xs">
                    <span class="text-[11px] font-bold text-blue-700 uppercase tracking-wider">Total Lelang Terverifikasi</span>
                    <p class="text-2xl font-black text-slate-900 mt-1">{{ $stats['total_verified'] }}</p>
                    <span class="text-[11px] text-slate-600">Biaya pembukaan lunas</span>
                </div>
                <div class="bg-white p-3.5 sm:p-4 rounded-2xl border border-slate-200/80 shadow-xs">
                    <span class="text-[11px] font-bold text-purple-700 uppercase tracking-wider">Total Pendapatan Lelang</span>
                    <p class="text-2xl font-black text-slate-900 mt-1">Rp {{ number_format($stats['total_revenue'], 0, ',', '.') }}</p>
                    <span class="text-[11px] text-slate-600">Kas masuk monetisasi lelang</span>
                </div>
            </div>

            <div class="flex items-center gap-2 border-b border-slate-200 pb-2 overflow-x-auto no-scrollbar text-xs font-bold">
                <a href="{{ route('admin.payments.auction', ['status' => 'all']) }}" 
                   class="min-h-[40px] inline-flex items-center px-4 py-2 rounded-xl shrink-0 transition {{ $statusFilter === 'all' ? 'bg-[#1c2541] text-white shadow-xs' : 'text-slate-700 hover:bg-slate-200' }}">
                    Semua ({{ $payments->total() }})
                </a>
                <a href="{{ route('admin.payments.auction', ['status' => 'pending']) }}" 
                   class="min-h-[40px] inline-flex items-center px-4 py-2 rounded-xl shrink-0 transition gap-1.5 {{ $statusFilter === 'pending' ? 'bg-amber-600 text-white shadow-xs' : 'text-slate-700 hover:bg-slate-200' }}">
                    <span>Menunggu (Pending)</span>
                    @if ($stats['pending'] > 0)
                        <span class="px-1.5 py-0.5 rounded-full text-[10px] {{ $statusFilter === 'pending' ? 'bg-white text-amber-800' : 'bg-amber-100 text-amber-800' }}">
                            {{ $stats['pending'] }}
                        </span>
                    @endif
                </a>
                <a href="{{ route('admin.payments.auction', ['status' => 'verified']) }}" 
                   class="min-h-[40px] inline-flex items-center px-4 py-2 rounded-xl shrink-0 transition {{ $statusFilter === 'verified' ? 'bg-emerald-700 text-white shadow-xs' : 'text-slate-700 hover:bg-slate-200' }}">
                    Disetujui (Verified)
                </a>
                <a href="{{ route('admin.payments.auction', ['status' => 'rejected']) }}" 
                   class="min-h-[40px] inline-flex items-center px-4 py-2 rounded-xl shrink-0 transition {{ $statusFilter === 'rejected' ? 'bg-rose-600 text-white shadow-xs' : 'text-slate-700 hover:bg-slate-200' }}">
                    Ditolak (Rejected)
                </a>
            </div>

            <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-50 border-b border-slate-200 text-slate-600 uppercase tracking-wider text-[10px] font-bold">
                            <tr>
                                <th class="py-3.5 px-4">Invoice &amp; Tanggal</th>
                                <th class="py-3.5 px-4">Penyelenggara Lelang</th>
                                <th class="py-3.5 px-4">Barang Lelang</th>
                                <th class="py-3.5 px-4">Metode &amp; Biaya</th>
                                <th class="py-3.5 px-4">Bukti Transfer</th>
                                <th class="py-3.5 px-4">Status</th>
                                <th class="py-3.5 px-4 text-center">Aksi Verifikasi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-slate-700">
                            @forelse ($payments as $payment)
                                <tr class="hover:bg-slate-50/60 transition {{ $payment->isPending() ? 'bg-amber-50/30' : '' }}">
                                    <td class="py-4 px-4 whitespace-nowrap">
                                        <span class="font-mono font-bold text-slate-900 block">{{ $payment->invoice_code }}</span>
                                        <span class="text-[11px] text-slate-600">{{ $payment->created_at->translatedFormat('d M Y, H:i') }}</span>
                                    </td>
                                    <td class="py-4 px-4">
                                        <div class="font-bold text-slate-900">{{ $payment->user->name ?? 'User Terhapus' }}</div>
                                        <div class="text-[11px] text-slate-600">{{ $payment->user->campus ?? $payment->user->email }}</div>
                                    </td>
                                    <td class="py-4 px-4">
                                        @if ($payment->auction && $payment->auction->listing)
                                            <a href="{{ route('auctions.show', $payment->auction->id) }}" target="_blank" class="font-bold text-blue-700 hover:underline line-clamp-1">
                                                {{ $payment->auction->listing->title }}
                                            </a>
                                            <span class="text-[11px] text-slate-600 block">
                                                Awal Rp {{ number_format($payment->auction->starting_price, 0, ',', '.') }} · Kelipatan Rp {{ number_format($payment->auction->bid_increment, 0, ',', '.') }}
                                            </span>
                                        @else
                                            <span class="text-slate-600 italic">Sesi lelang #{{ $payment->auction_id }}</span>
                                        @endif
                                    </td>
                                    <td class="py-4 px-4 whitespace-nowrap">
                                        <span class="font-bold text-slate-900 block">Rp {{ number_format($payment->amount, 0, ',', '.') }}</span>
                                        <span class="inline-block text-[10px] font-bold uppercase px-1.5 py-0.5 rounded bg-slate-100 text-slate-700">
                                            {{ strtoupper($payment->payment_channel) }}
                                        </span>
                                    </td>
                                    <td class="py-4 px-4 whitespace-nowrap">
                                        @if ($payment->proof_image)
                                            @php
                                                $proofUrl = asset('storage/' . ltrim($payment->proof_image, '/'));
                                            @endphp
                                            <button type="button" 
                                                    @click="modalImage = '{{ $proofUrl }}'" 
                                                    class="min-h-[38px] inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-800 font-bold transition shadow-2xs cursor-pointer">
                                                <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                </svg>
                                                <span>Lihat Struk</span>
                                            </button>
                                        @else
                                            <span class="text-slate-600 italic">Tidak ada bukti</span>
                                        @endif
                                    </td>
                                    <td class="py-4 px-4 whitespace-nowrap">
                                        @if ($payment->status === 'verified')
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-800 border border-emerald-200">
                                                ✓ Disetujui
                                            </span>
                                        @elseif ($payment->status === 'rejected')
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-rose-50 text-rose-800 border border-rose-200">
                                                ✕ Ditolak
                                            </span>
                                            @if ($payment->rejection_note)
                                                <div class="text-[10px] text-rose-600 mt-1 max-w-xs truncate" title="{{ $payment->rejection_note }}">
                                                    {{ $payment->rejection_note }}
                                                </div>
                                            @endif
                                        @else
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-amber-50 text-amber-800 border border-amber-200">
                                                Menunggu Admin
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-4 px-4 text-center whitespace-nowrap">
                                        @if ($payment->isPending())
                                            <div class="inline-flex items-center gap-1.5">
                                                <form action="{{ route('admin.payments.auction.approve', $payment->id) }}" method="POST" onsubmit="return confirm('Setujui pembayaran lelang {{ $payment->invoice_code }}? Sesi lelang akan langsung dibuka.');">
                                                    @csrf
                                                    <button type="submit" 
                                                            class="min-h-[38px] px-3 py-1.5 rounded-xl bg-emerald-700 hover:bg-emerald-800 text-white font-bold transition shadow-2xs cursor-pointer flex items-center gap-1">
                                                        <span>✓ Setujui</span>
                                                    </button>
                                                </form>

                                                <button type="button" 
                                                        @click="openReject('{{ route('admin.payments.auction.reject', $payment->id) }}', '{{ $payment->invoice_code }}')" 
                                                        class="min-h-[38px] px-3 py-1.5 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 font-bold transition shadow-2xs cursor-pointer">
                                                    Tolak
                                                </button>
                                            </div>
                                        @else
                                            <span class="text-slate-600 text-[11px]">
                                                Diverifikasi oleh {{ $payment->verifier->name ?? 'Admin' }}
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="py-12 text-center text-slate-600 font-medium">
                                        Tidak ada transaksi pembayaran lelang dalam antrean ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($payments->hasPages())
                    <div class="p-4 border-t border-slate-100">
                        {{ $payments->links() }}
                    </div>
                @endif
            </div>
        </main>
    </div>

    <div x-show="modalImage" 
         x-cloak 
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/75 backdrop-blur-xs"
         @click.self="modalImage = null">
        <div class="bg-white rounded-3xl max-w-lg w-full p-6 shadow-2xl relative">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-bold text-slate-900">Bukti Struk Pembayaran Biaya Lelang</h3>
                <button type="button" @click="modalImage = null" aria-label="Tutup pratinjau struk" class="w-11 h-11 flex items-center justify-center text-slate-500 hover:text-slate-900 font-bold text-lg cursor-pointer">✕</button>
            </div>
            <div class="max-h-[70vh] overflow-auto rounded-xl border border-slate-200 bg-slate-50 flex items-center justify-center p-2">
                <img :src="modalImage" alt="Struk Transfer" class="max-w-full max-h-[60vh] object-contain rounded-lg">
            </div>
            <div class="mt-4 flex justify-end">
                <button type="button" @click="modalImage = null" class="min-h-[44px] px-5 py-2 rounded-xl bg-slate-100 text-slate-800 text-xs font-bold hover:bg-slate-200 cursor-pointer">
                    Tutup
                </button>
            </div>
        </div>
    </div>

    <div x-show="rejectModalOpen" 
         x-cloak 
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/75 backdrop-blur-xs"
         @click.self="rejectModalOpen = false">
        <div class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl">
            <h3 class="text-base font-bold text-slate-900 mb-1">
                Tolak Pembayaran Lelang
            </h3>
            <p class="text-xs text-slate-600 mb-4">
                Invoice: <strong x-text="rejectInvoice" class="font-mono text-slate-900"></strong>
            </p>

            <form :action="rejectActionUrl" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label for="rejection_note" class="block text-xs font-bold text-slate-800 mb-1.5">
                        Alasan Penolakan Transaksi <span class="text-rose-600">*</span>
                    </label>
                    <textarea name="rejection_note" 
                              id="rejection_note" 
                              rows="3" 
                              required 
                              placeholder="Contoh: Bukti pembayaran QRIS buram/tidak terbaca, nominal kurang dari Rp 5.000, atau transaksi belum tercatat..." 
                              class="w-full rounded-xl border border-slate-300 p-3 text-base sm:text-xs text-slate-900 placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-rose-500 bg-white"></textarea>
                </div>

                <div class="flex items-center justify-end gap-2 pt-2">
                    <button type="button" @click="rejectModalOpen = false" class="min-h-[44px] px-4 py-2 rounded-xl border border-slate-200 text-slate-700 text-xs font-bold hover:bg-slate-50 cursor-pointer">
                        Batal
                    </button>
                    <button type="submit" class="min-h-[44px] px-4 py-2 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold shadow-xs cursor-pointer">
                        Tolak Pembayaran
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
</body>
</html>
