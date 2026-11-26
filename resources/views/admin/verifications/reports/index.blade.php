<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>ADMIN - Reported Items | Lungsur.in</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-100 text-slate-800 antialiased">

@php
    $admin = auth()->user();
    $adminRoleLabel = match ($admin->role) {
        'admin' => 'Administrator',
        default => ucfirst($admin->role),
    };
    $adminInitial = strtoupper(mb_substr($admin->name, 0, 1));
    $pendingVerifications = \App\Models\User::where('verification_status', 'pending')->count();
    $pendingBoostCount = \App\Models\BoostPayment::where('status', 'pending')->count();
    $pendingAuctionCount = \App\Models\AuctionPayment::where('status', 'pending')->count();
@endphp

<div class="flex min-h-screen">

    <x-admin-sidebar active="reports" />

    <div class="flex min-w-0 flex-1 flex-col">

        <header class="flex items-center justify-between gap-4 border-b bg-white px-4 sm:px-6 py-3">
            <div class="text-xs sm:text-sm font-bold text-slate-700">Admin / Reported Items</div>

            <div class="flex items-center gap-3">
                <div class="text-right hidden sm:block">
                    <div class="text-sm font-semibold text-slate-900">{{ $admin->name }}</div>
                    <div class="text-[11px] text-blue-700 font-semibold">Campus Trust &amp; Safety</div>
                </div>
                <div class="flex h-9 w-9 items-center justify-center rounded-full bg-blue-600 text-sm font-bold text-white">
                    {{ $adminInitial }}
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
                @if ($pendingVerifications > 0)
                    <span class="rounded-full bg-red-500 px-1.5 py-0.5 text-[10px] font-extrabold text-white">{{ $pendingVerifications }}</span>
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
               class="min-h-[44px] shrink-0 inline-flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs font-bold bg-blue-600 text-white shadow-xs">
                <span>⚑ Laporan Iklan</span>
            </a>
        </nav>

        <main class="space-y-6 p-4 sm:p-6">

            <div>
                <div class="text-[11px] font-bold tracking-widest text-emerald-700">● CAMPUS TRUST &amp; SAFETY MODULE</div>
                <h1 class="mt-1 text-2xl sm:text-3xl font-extrabold text-slate-900">Reports &amp; Moderation</h1>
                <p class="mt-2 max-w-xl text-xs sm:text-sm text-slate-600">
                    Kelola laporan pengguna terhadap iklan atau akun yang dianggap
                    melanggar aturan platform Lungsur.in.
                </p>
            </div>

            @if (session('success'))
                <div class="rounded-xl bg-emerald-100 px-4 py-3 text-sm font-semibold text-emerald-900">{{ session('success') }}</div>
            @endif
            @if ($errors->any())
                <div class="rounded-xl bg-red-100 px-4 py-3 text-sm font-semibold text-red-900">{{ $errors->first() }}</div>
            @endif

            <section class="overflow-hidden rounded-2xl border bg-white">

                <div class="flex flex-wrap items-center justify-between gap-3 border-b p-4 sm:p-5">
                    <div>
                        <h2 class="text-base sm:text-lg font-bold text-slate-900">Daftar Laporan Pengguna</h2>
                        <p class="text-xs text-slate-600">Laporan terbaru ditampilkan terlebih dahulu.</p>
                    </div>
                    <span class="rounded-lg bg-blue-50 px-3 py-2 text-xs font-bold text-blue-700">
                        Total: {{ $reports->total() }} laporan
                    </span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-slate-50 text-[11px] uppercase tracking-wider text-slate-600 font-bold">
                            <tr>
                                <th class="px-4 sm:px-5 py-3.5">Pelapor</th>
                                <th class="px-4 sm:px-5 py-3.5">Iklan / Pengguna</th>
                                <th class="px-4 sm:px-5 py-3.5">Alasan</th>
                                <th class="px-4 sm:px-5 py-3.5">Tanggal</th>
                                <th class="px-4 sm:px-5 py-3.5">Status</th>
                                <th class="px-4 sm:px-5 py-3.5 text-center">Aksi</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-slate-100">
                            @forelse ($reports as $report)
                                <tr class="hover:bg-slate-50">

                                    <td class="px-4 sm:px-5 py-4">
                                        <div class="font-bold text-slate-900">
                                            {{ $report->reporter?->name ?? 'Pengguna tidak tersedia' }}
                                        </div>
                                    </td>

                                    <td class="px-4 sm:px-5 py-4">
                                        @if ($report->reportedListing)
                                            <div class="font-bold text-slate-900">{{ $report->reportedListing->title }}</div>
                                            <div class="text-xs text-slate-600">
                                                Iklan
                                                @if ($report->reportedListing->trashed())
                                                    · <span class="font-bold text-red-600">Sudah diturunkan</span>
                                                @endif
                                            </div>
                                        @elseif ($report->reportedUser)
                                            <div class="font-bold text-slate-900">{{ $report->reportedUser->name }}</div>
                                            <div class="text-xs text-slate-600">Pengguna</div>
                                        @else
                                            <span class="text-slate-600">Data tidak tersedia</span>
                                        @endif
                                    </td>

                                    <td class="px-4 sm:px-5 py-4">
                                        <span class="font-semibold text-slate-800">{{ $report->reason }}</span>
                                    </td>

                                    <td class="whitespace-nowrap px-4 sm:px-5 py-4 text-xs text-slate-600">
                                        {{ $report->created_at?->format('d M Y, H:i') }}
                                    </td>

                                    <td class="px-4 sm:px-5 py-4">
                                        @if ($report->status === 'pending')
                                            <span class="rounded-full bg-amber-100 px-3 py-1 text-xs font-bold text-amber-800">Pending</span>
                                        @elseif ($report->status === 'reviewed')
                                            <span class="rounded-full bg-slate-200 px-3 py-1 text-xs font-bold text-slate-800">Diabaikan</span>
                                        @else
                                            <span class="rounded-full bg-emerald-100 px-3 py-1 text-xs font-bold text-emerald-800">Diturunkan</span>
                                        @endif
                                    </td>

                                    <td class="px-4 sm:px-5 py-4 text-center">
                                        <a href="{{ route('admin.reports.show', $report) }}"
                                           class="min-h-[40px] inline-flex items-center justify-center rounded-xl bg-blue-600 px-3.5 py-2 text-xs font-bold text-white hover:bg-blue-700">
                                            Detail
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-5 py-12 text-center">
                                        <div class="mt-2 font-bold text-slate-900">Belum ada laporan</div>
                                        <p class="mt-1 text-sm text-slate-600">Laporan dari pengguna akan muncul di halaman ini.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($reports->hasPages())
                    <div class="border-t p-5">{{ $reports->links() }}</div>
                @endif
            </section>
        </main>
    </div>
</div>

</body>
</html>
