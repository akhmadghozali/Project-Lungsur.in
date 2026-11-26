<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>ADMIN - Dasbor Analitik Bisnis | Lungsur.in</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        [x-cloak] { display: none !important; }
        @media print {
            aside, header .no-print, .no-print {
                display: none !important;
            }
            body {
                background: white !important;
                color: #0f172a !important;
            }
            .print-only {
                display: block !important;
            }
            .page-break {
                page-break-before: always;
            }
            .shadow-xs, .shadow-sm, .shadow-md {
                box-shadow: none !important;
            }
        }
    </style>
</head>
<body class="bg-slate-100 text-slate-800 antialiased font-sans">
    <x-flash-message />

    <div class="flex min-h-screen">
        <x-admin-sidebar active="dashboard" />

        <div class="flex-1 min-w-0 flex flex-col">
            <nav aria-label="Menu Admin Mobile" class="no-print lg:hidden bg-[#1c2541] px-3 py-2.5 flex items-center gap-2 overflow-x-auto no-scrollbar border-b border-slate-800">
                <a href="{{ route('admin.dashboard') }}"
                   class="min-h-[44px] shrink-0 inline-flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs font-bold bg-blue-600 text-white shadow-xs">
                    <span>📊 Dashboard</span>
                </a>
                <a href="{{ route('admin.verifications.index') }}"
                   class="min-h-[44px] shrink-0 inline-flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs font-bold text-slate-200 bg-white/10 hover:bg-white/15">
                    <span>🛡 Verifikasi KTM</span>
                    @if ($pendingKtm > 0)
                        <span class="rounded-full bg-red-500 px-1.5 py-0.5 text-[10px] font-extrabold text-white">{{ $pendingKtm }}</span>
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
                   class="min-h-[44px] shrink-0 inline-flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs font-bold text-slate-200 bg-white/10 hover:bg-white/15">
                    <span>🔨 Biaya Lelang</span>
                    @if ($pendingAuctionCount > 0)
                        <span class="rounded-full bg-emerald-500 px-1.5 py-0.5 text-[10px] font-extrabold text-white">{{ $pendingAuctionCount }}</span>
                    @endif
                </a>
                <a href="{{ route('admin.reports.index') }}"
                   class="min-h-[44px] shrink-0 inline-flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs font-bold text-slate-200 bg-white/10 hover:bg-white/15">
                    <span>⚑ Laporan Iklan</span>
                </a>
            </nav>

            <!-- Main Content -->
            <main class="flex-1 min-w-0 p-4 sm:p-8">
            <!-- Kop Resmi Khusus Cetak / PDF -->
            <div class="hidden print-only mb-6 border-b-2 border-slate-900 pb-4">
                <div class="flex justify-between items-start">
                    <div>
                        <h1 class="text-2xl font-black tracking-tight text-slate-900">LUNGSUR.IN</h1>
                        <p class="text-xs uppercase tracking-widest font-semibold text-slate-600">Platform Katalog, Lelang Terbuka & Promosi Mahasiswa Malang</p>
                        <p class="text-sm font-bold text-slate-800 mt-1">LAPORAN ANALITIK BISNIS & TRANSAKSI MIKRO PLATFORM</p>
                    </div>
                    <div class="text-right text-xs text-slate-600 space-y-0.5">
                        <div><strong>Waktu Cetak:</strong> <span id="print-timestamp">{{ now()->timezone('Asia/Jakarta')->locale('id')->translatedFormat('d F Y, H:i') }} WIB</span></div>
                        <div><strong>Administrator:</strong> {{ auth()->user()->name }}</div>
                        <div><strong>Filter Periode:</strong> 
                            @if($period === '7d') 7 Hari Terakhir
                            @elseif($period === '30d') 30 Hari Terakhir
                            @elseif($period === 'month') Bulan Ini
                            @else Keseluruhan (All-Time)
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Header Halaman & Filter (Screen Only) -->
            <div class="no-print flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-8">
                <div>
                    <div class="flex items-center gap-3">
                        <span class="inline-flex items-center justify-center w-10 h-10 rounded-2xl bg-blue-600 text-white font-black text-lg shadow-sm">
                            📊
                        </span>
                        <div>
                            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Dasbor Analitik Bisnis</h1>
                            <p class="text-xs text-slate-500 font-medium">Monitoring performa platform, transaksi biaya mikro, dan sirkulasi perabot kos mahasiswa</p>
                        </div>
                    </div>
                </div>

                <div class="flex flex-wrap items-center gap-2.5">
                    <!-- Dropdown Filter Periode -->
                    <form method="GET" action="{{ route('admin.dashboard') }}" class="flex items-center gap-1.5 bg-white border border-slate-300 rounded-xl px-3 py-1.5 shadow-2xs">
                        <label for="period" class="text-xs font-semibold text-slate-500">Periode:</label>
                        <select name="period" id="period" onchange="this.form.submit()" class="text-xs font-bold text-slate-700 bg-transparent focus:outline-hidden cursor-pointer">
                            <option value="all" {{ $period === 'all' ? 'selected' : '' }}>Semua Waktu</option>
                            <option value="month" {{ $period === 'month' ? 'selected' : '' }}>Bulan Ini</option>
                            <option value="30d" {{ $period === '30d' ? 'selected' : '' }}>30 Hari Terakhir</option>
                            <option value="7d" {{ $period === '7d' ? 'selected' : '' }}>7 Hari Terakhir</option>
                        </select>
                    </form>

                    <!-- Tombol Cetak / PDF -->
                    <button type="button" onclick="updatePrintTimestamp(); window.print()" class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold shadow-xs hover:shadow-sm transition cursor-pointer">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                        </svg>
                        <span>Cetak Laporan (PDF)</span>
                    </button>
                </div>
            </div>

            <!-- KPI Cards Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
                <!-- Card 1: Mahasiswa Aktif -->
                <div class="bg-white rounded-2xl p-5 border border-slate-200/90 shadow-2xs">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Mahasiswa Aktif</span>
                        <span class="w-9 h-9 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center font-bold text-sm">🎓</span>
                    </div>
                    <div class="mt-3 flex items-baseline gap-2">
                        <span class="text-2xl font-black text-slate-900">{{ number_format($totalStudents, 0, ',', '.') }}</span>
                        <span class="text-xs text-slate-400 font-medium">terverifikasi</span>
                    </div>
                    <div class="mt-2 text-[11px] text-slate-500 flex items-center justify-between">
                        <span>Antrean KTM pending:</span>
                        <a href="{{ route('admin.verifications.index') }}" class="font-bold text-amber-600 hover:underline">
                            {{ $pendingKtm }} berkas
                        </a>
                    </div>
                </div>

                <!-- Card 2: Iklan Katalog Aktif -->
                <div class="bg-white rounded-2xl p-5 border border-slate-200/90 shadow-2xs">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Iklan Tersedia</span>
                        <span class="w-9 h-9 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold text-sm">📦</span>
                    </div>
                    <div class="mt-3 flex items-baseline gap-2">
                        <span class="text-2xl font-black text-slate-900">{{ number_format($activeListings, 0, ',', '.') }}</span>
                        <span class="text-xs text-emerald-600 font-bold">Katalog Aktif</span>
                    </div>
                    <div class="mt-2 text-[11px] text-slate-500 flex items-center justify-between">
                        <span>Pending Verif Boost:</span>
                        <a href="{{ route('admin.payments.boost') }}" class="font-bold text-amber-600 hover:underline">
                            {{ $pendingBoostCount }} iklan
                        </a>
                    </div>
                </div>

                <!-- Card 3: Lelang Terbuka Aktif -->
                <div class="bg-white rounded-2xl p-5 border border-slate-200/90 shadow-2xs">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Sesi Lelang Aktif</span>
                        <span class="w-9 h-9 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold text-sm">🔨</span>
                    </div>
                    <div class="mt-3 flex items-baseline gap-2">
                        <span class="text-2xl font-black text-slate-900">{{ number_format($activeAuctions, 0, ',', '.') }}</span>
                        <span class="text-xs text-indigo-600 font-bold">Open Bid</span>
                    </div>
                    <div class="mt-2 text-[11px] text-slate-500 flex items-center justify-between">
                        <span>Pending Biaya Lelang:</span>
                        <a href="{{ route('admin.payments.auction') }}" class="font-bold text-sky-600 hover:underline">
                            {{ $pendingAuctionCount }} pengajuan
                        </a>
                    </div>
                </div>

                <!-- Card 4: Total Pendapatan Mikro Platform -->
                <div class="bg-white rounded-2xl p-5 border border-emerald-200 bg-linear-to-br from-white via-white to-emerald-50/40 shadow-2xs">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-emerald-700 uppercase tracking-wider">Pendapatan Mikro</span>
                        <span class="w-9 h-9 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold text-sm">💰</span>
                    </div>
                    <div class="mt-3">
                        <span class="text-2xl font-black text-emerald-700">Rp{{ number_format($totalRevenue, 0, ',', '.') }}</span>
                    </div>
                    <div class="mt-2 text-[11px] text-slate-500 space-y-0.5">
                        <div class="flex justify-between">
                            <span>Boost: Rp{{ number_format($boostRevenue, 0, ',', '.') }}</span>
                            <span>Lelang: Rp{{ number_format($auctionRevenue, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                <!-- Chart 1: Kategori Terpopuler -->
                <div class="bg-white rounded-2xl p-6 border border-slate-200/90 shadow-2xs">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h2 class="text-sm font-extrabold text-slate-900 tracking-tight">Kategori Barang Paling Banyak Diiklankan</h2>
                            <p class="text-xs text-slate-400">Distribusi listing aktif berdasarkan komoditas perlengkapan kos</p>
                        </div>
                        <span class="text-xs font-semibold px-2.5 py-1 rounded-lg bg-slate-100 text-slate-600">Katalog</span>
                    </div>
                    <div class="relative h-64">
                        <canvas id="categoryChart"></canvas>
                    </div>
                </div>

                <!-- Chart 2: Tren Finansial Mikro -->
                <div class="bg-white rounded-2xl p-6 border border-slate-200/90 shadow-2xs">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h2 class="text-sm font-extrabold text-slate-900 tracking-tight">Tren Penerimaan Biaya Mikro (6 Bulan)</h2>
                            <p class="text-xs text-slate-400">Rekapitulasi biaya promosi boost dan pembukaan lelang (Rp5.000 / transaksi)</p>
                        </div>
                        <span class="text-xs font-semibold px-2.5 py-1 rounded-lg bg-emerald-50 text-emerald-700 border border-emerald-200">Freemium</span>
                    </div>
                    <div class="relative h-64">
                        <canvas id="revenueTrendChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Chart 3 & Rekap Keamanan -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
                <!-- Rata-Rata Harga Pasar -->
                <div class="lg:col-span-2 bg-white rounded-2xl p-6 border border-slate-200/90 shadow-2xs">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h2 class="text-sm font-extrabold text-slate-900 tracking-tight">Tren Rata-rata Harga Pasar Barang Bekas</h2>
                            <p class="text-xs text-slate-400">Panduan harga wajar perabot kos berdasarkan data agregasi postingan</p>
                        </div>
                        <span class="text-xs font-semibold px-2.5 py-1 rounded-lg bg-blue-50 text-blue-700 border border-blue-200">Smart Price</span>
                    </div>
                    <div class="relative h-60">
                        <canvas id="avgPriceChart"></canvas>
                    </div>
                </div>

                <!-- Status Keamanan Komunitas & Moderasi -->
                <div class="bg-white rounded-2xl p-6 border border-slate-200/90 shadow-2xs flex flex-col justify-between">
                    <div>
                        <h2 class="text-sm font-extrabold text-slate-900 tracking-tight mb-1">Status Keamanan Komunitas</h2>
                        <p class="text-xs text-slate-400 mb-4">Integritas akun dan tiket aduan masyarakat kampus</p>

                        <div class="space-y-3">
                            <div class="flex items-center justify-between p-3 rounded-xl bg-slate-50 border border-slate-200/60">
                                <div class="flex items-center gap-2.5">
                                    <span class="text-base leading-none">🚩</span>
                                    <div>
                                        <div class="text-xs font-bold text-slate-800">Tiket Aduan Pending</div>
                                        <div class="text-[10px] text-slate-400">Laporan iklan/penjual nakal</div>
                                    </div>
                                </div>
                                <a href="{{ route('admin.reports.index') }}" class="font-extrabold text-sm {{ $pendingReportsCount > 0 ? 'text-rose-600' : 'text-slate-500' }}">
                                    {{ $pendingReportsCount }} tiket
                                </a>
                            </div>

                            <div class="flex items-center justify-between p-3 rounded-xl bg-slate-50 border border-slate-200/60">
                                <div class="flex items-center gap-2.5">
                                    <span class="text-base leading-none">🚫</span>
                                    <div>
                                        <div class="text-xs font-bold text-slate-800">Akun Disanksi / Banned</div>
                                        <div class="text-[10px] text-slate-400">Pelanggaran troll / fraud</div>
                                    </div>
                                </div>
                                <span class="font-extrabold text-sm text-slate-800">
                                    {{ $suspendedUsers }} akun
                                </span>
                            </div>

                            <div class="flex items-center justify-between p-3 rounded-xl bg-slate-50 border border-slate-200/60">
                                <div class="flex items-center gap-2.5">
                                    <span class="text-base leading-none">🛡️</span>
                                    <div>
                                        <div class="text-xs font-bold text-slate-800">Antrean Verifikasi KTM</div>
                                        <div class="text-[10px] text-slate-400">Pendaftar email publik</div>
                                    </div>
                                </div>
                                <a href="{{ route('admin.verifications.index') }}" class="font-extrabold text-sm {{ $pendingKtm > 0 ? 'text-amber-600' : 'text-slate-500' }}">
                                    {{ $pendingKtm }} akun
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="pt-4 border-t border-slate-100 text-[11px] text-slate-400 text-center">
                        Sistem terproteksi model RBAC & White-box Rules
                    </div>
                </div>
            </div>

            <!-- Tables Section: Transaksi Mikro Terbaru & Pendaftar Terbaru -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Tabel 1: Transaksi Layanan Mikro Terbaru -->
                <div class="bg-white rounded-2xl border border-slate-200/90 shadow-2xs overflow-hidden">
                    <div class="px-6 py-4 border-b border-slate-200/80 flex items-center justify-between">
                        <div>
                            <h2 class="text-sm font-extrabold text-slate-900 tracking-tight">Transaksi Biaya Layanan Terbaru</h2>
                            <p class="text-xs text-slate-400">Catatan pembayaran manual Rp5.000 boost & lelang</p>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs">
                            <thead class="bg-slate-50 border-b border-slate-200/60 text-[11px] font-bold text-slate-500 uppercase">
                                <tr>
                                    <th class="px-5 py-3">Layanan</th>
                                    <th class="px-4 py-3">Mahasiswa</th>
                                    <th class="px-4 py-3">Nominal</th>
                                    <th class="px-4 py-3">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 font-medium">
                                @forelse($recentPayments as $payment)
                                    <tr class="hover:bg-slate-50/80">
                                        <td class="px-5 py-3.5">
                                            <span class="font-bold text-slate-800">{{ $payment->type }}</span>
                                            <div class="text-[11px] text-slate-400 truncate max-w-[180px]">{{ $payment->detail }}</div>
                                        </td>
                                        <td class="px-4 py-3.5">
                                            <div class="font-semibold text-slate-800">{{ $payment->user }}</div>
                                            <div class="text-[10px] text-slate-400">{{ $payment->campus }}</div>
                                        </td>
                                        <td class="px-4 py-3.5 font-bold text-emerald-600">
                                            Rp{{ number_format($payment->amount, 0, ',', '.') }}
                                        </td>
                                        <td class="px-4 py-3.5">
                                            @if($payment->status === 'verified')
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">Verified</span>
                                            @elseif($payment->status === 'pending')
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200">Pending</span>
                                            @else
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200">Rejected</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-5 py-8 text-center text-slate-400">
                                            Belum ada transaksi pembayaran mikro pada periode ini.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Tabel 2: Pendaftar Mahasiswa Terbaru -->
                <div class="bg-white rounded-2xl border border-slate-200/90 shadow-2xs overflow-hidden">
                    <div class="px-6 py-4 border-b border-slate-200/80 flex items-center justify-between">
                        <div>
                            <h2 class="text-sm font-extrabold text-slate-900 tracking-tight">Pendaftar Mahasiswa Terbaru</h2>
                            <p class="text-xs text-slate-400">Civitas akademika yang baru bergabung di ekosistem Lungsur.in</p>
                        </div>
                        <a href="{{ route('admin.verifications.index') }}" class="text-xs font-bold text-blue-600 hover:underline">
                            Verifikasi Mahasiswa →
                        </a>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs">
                            <thead class="bg-slate-50 border-b border-slate-200/60 text-[11px] font-bold text-slate-500 uppercase">
                                <tr>
                                    <th class="px-5 py-3">Nama Mahasiswa</th>
                                    <th class="px-4 py-3">Kampus / NIM</th>
                                    <th class="px-4 py-3">Jalur & Status</th>
                                    <th class="px-4 py-3 text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 font-medium">
                                @forelse($recentUsers as $user)
                                    <tr class="hover:bg-slate-50/80">
                                        <td class="px-5 py-3.5">
                                            <div class="font-bold text-slate-800">{{ $user->name }}</div>
                                            <div class="text-[11px] text-slate-400">{{ $user->email }}</div>
                                        </td>
                                        <td class="px-4 py-3.5">
                                            <div class="font-semibold text-slate-700">{{ $user->campus ?? '-' }}</div>
                                            <div class="text-[10px] text-slate-400">{{ $user->nim ?? 'NIM belum ada' }}</div>
                                        </td>
                                        <td class="px-4 py-3.5">
                                            @if($user->status === 'banned')
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-100 text-rose-700">Banned</span>
                                            @elseif($user->verification_status === 'pending')
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200">Pending KTM</span>
                                            @else
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">Aktif</span>
                                            @endif
                                            <span class="block text-[10px] text-slate-400 mt-0.5">{{ $user->email_type === 'campus' ? 'Email Kampus' : 'KTM Manual' }}</span>
                                        </td>
                                        <td class="px-4 py-3.5 text-right">
                                            @if($user->verification_status === 'pending')
                                                <a href="{{ route('admin.verifications.index') }}" class="inline-flex items-center px-2.5 py-1 rounded-lg bg-amber-50 hover:bg-amber-100 text-amber-800 text-[11px] font-bold border border-amber-200 transition">
                                                    Review KTM
                                                </a>
                                            @else
                                                <span class="text-[11px] text-slate-400 font-medium">
                                                    {{ $user->created_at ? $user->created_at->diffForHumans() : '-' }}
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-5 py-8 text-center text-slate-400">
                                            Belum ada mahasiswa terdaftar.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            </main>
        </div>
    </div>

    <!-- Script Render Chart.js -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // 1. Chart Kategori Terpopuler (Doughnut)
            const catCtx = document.getElementById('categoryChart');
            if (catCtx) {
                new Chart(catCtx, {
                    type: 'doughnut',
                    data: {
                        labels: {!! json_encode($categoryChart['labels']) !!},
                        datasets: [{
                            data: {!! json_encode($categoryChart['data']) !!},
                            backgroundColor: [
                                '#2563EB', '#3B82F6', '#60A5FA', '#93C5FD', '#F59E0B', '#10B981'
                            ],
                            borderWidth: 2,
                            borderColor: '#ffffff',
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    boxWidth: 12,
                                    font: { size: 11, family: 'Inter, sans-serif', weight: '600' },
                                    color: '#475569'
                                }
                            }
                        }
                    }
                });
            }

            // 2. Chart Tren Pendapatan Mikro 6 Bulan (Grouped Bar)
            const revCtx = document.getElementById('revenueTrendChart');
            if (revCtx) {
                new Chart(revCtx, {
                    type: 'bar',
                    data: {
                        labels: {!! json_encode($revenueTrendChart['labels']) !!},
                        datasets: [
                            {
                                label: 'Boost Iklan',
                                data: {!! json_encode($revenueTrendChart['boost']) !!},
                                backgroundColor: '#F59E0B',
                                borderRadius: 6,
                            },
                            {
                                label: 'Buka Lelang',
                                data: {!! json_encode($revenueTrendChart['auction']) !!},
                                backgroundColor: '#10B981',
                                borderRadius: 6,
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    callback: function(value) {
                                        return 'Rp' + value.toLocaleString('id-ID');
                                    },
                                    font: { size: 10 },
                                    color: '#64748B'
                                },
                                grid: { color: '#F1F5F9' }
                            },
                            x: {
                                grid: { display: false },
                                ticks: { font: { size: 11, weight: '600' }, color: '#475569' }
                            }
                        },
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    boxWidth: 12,
                                    font: { size: 11, family: 'Inter, sans-serif', weight: '600' },
                                    color: '#475569'
                                }
                            }
                        }
                    }
                });
            }

            // 3. Chart Tren Rata-Rata Harga Pasar (Horizontal Bar)
            const avgCtx = document.getElementById('avgPriceChart');
            if (avgCtx) {
                new Chart(avgCtx, {
                    type: 'bar',
                    data: {
                        labels: {!! json_encode($avgPriceChart['labels']) !!},
                        datasets: [{
                            label: 'Rata-rata Harga Pasar (Rp)',
                            data: {!! json_encode($avgPriceChart['data']) !!},
                            backgroundColor: '#3B82F6',
                            borderRadius: 6,
                        }]
                    },
                    options: {
                        indexAxis: 'y',
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            x: {
                                beginAtZero: true,
                                ticks: {
                                    callback: function(value) {
                                        return 'Rp' + (value / 1000).toLocaleString('id-ID') + 'k';
                                    },
                                    font: { size: 10 },
                                    color: '#64748B'
                                },
                                grid: { color: '#F1F5F9' }
                            },
                            y: {
                                grid: { display: false },
                                ticks: { font: { size: 11, weight: '600' }, color: '#475569' }
                            }
                        },
                        plugins: {
                            legend: { display: false }
                        }
                    }
                });
            }
        });

        function updatePrintTimestamp() {
            const el = document.getElementById('print-timestamp');
            if (!el) return;
            const now = new Date();
            const months = [
                'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
            ];
            const day = now.getDate();
            const month = months[now.getMonth()];
            const year = now.getFullYear();
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            el.textContent = `${day} ${month} ${year}, ${hours}:${minutes} WIB`;
        }

        window.addEventListener('beforeprint', updatePrintTimestamp);
    </script>
</body>
</html>
