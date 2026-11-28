<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>ADMIN - Account Reports | Lungsur.in</title>
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

    $pendingListingReports = \App\Models\Report::where('report_type', 'listing')
        ->where('status', 'pending')
        ->count();

    $pendingAccountReports = \App\Models\Report::where('report_type', 'account')
        ->where('status', 'pending')
        ->count();
@endphp

<div class="flex min-h-screen">

    {{-- ===== SIDEBAR UTAMA ===== --}}
    @include('admin.layouts.sidebar')

    {{-- ===== KONTEN UTAMA ===== --}}
    <div class="flex min-w-0 flex-1 flex-col">

        {{-- Topbar --}}
        <header class="flex items-center justify-between gap-4 border-b bg-white px-6 py-3">

            <div class="text-sm font-semibold text-slate-600">
                Admin / Laporan / Laporan Akun
            </div>

            {{-- Profil Admin --}}
            <div class="flex items-center gap-3">

                <div class="text-right">
                    <div class="text-sm font-semibold">
                        {{ $admin->name }}
                    </div>

                    <div class="text-[11px] text-blue-600">
                        Campus Trust &amp; Safety
                    </div>
                </div>

                <div class="flex h-9 w-9 items-center justify-center rounded-full bg-blue-600 text-sm font-bold text-white">
                    {{ $adminInitial }}
                </div>

            </div>
        </header>

        {{-- Isi Halaman --}}
        <main class="space-y-6 p-6">

            {{-- Judul Halaman --}}
            <div>

                <div class="text-[11px] font-bold tracking-widest text-emerald-600">
                    ● CAMPUS TRUST &amp; SAFETY MODULE
                </div>

                <h1 class="mt-1 text-3xl font-extrabold">
                    Laporan Akun
                </h1>

                <p class="mt-2 max-w-xl text-sm text-slate-500">
                    Kelola laporan pengguna terhadap akun yang diduga melakukan
                    pelanggaran melalui percakapan di platform Lungsur.in.
                </p>

            </div>

            {{-- Pesan Berhasil --}}
            @if (session('success'))
                <div class="rounded-xl bg-emerald-100 px-4 py-3 text-sm text-emerald-800">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Pesan Error --}}
            @if (session('error'))
                <div class="rounded-xl bg-red-100 px-4 py-3 text-sm text-red-800">
                    {{ session('error') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="rounded-xl bg-red-100 px-4 py-3 text-sm text-red-800">
                    {{ $errors->first() }}
                </div>
            @endif

            {{-- Tabel Laporan Akun --}}
            <section class="overflow-hidden rounded-2xl border bg-white">

                {{-- Header Tabel --}}
                <div class="flex flex-wrap items-center justify-between gap-3 border-b p-5">

                    <div>
                        <h2 class="text-lg font-bold">
                            Daftar Laporan Akun
                        </h2>

                        <p class="text-xs text-slate-500">
                            Laporan terbaru ditampilkan terlebih dahulu.
                        </p>
                    </div>

                    <span class="rounded-lg bg-blue-50 px-3 py-2 text-xs font-semibold text-blue-700">
                        Total: {{ $reports->total() }} laporan
                    </span>

                </div>

                {{-- Isi Tabel --}}
                <div class="overflow-x-auto">

                    <table class="w-full text-left text-sm">

                        <thead class="bg-slate-50 text-[11px] uppercase tracking-wider text-slate-500">
                            <tr>
                                <th class="px-5 py-4">Pelapor</th>
                                <th class="px-5 py-4">Akun Dilaporkan</th>
                                <th class="px-5 py-4">Alasan</th>
                                <th class="px-5 py-4">Tanggal</th>
                                <th class="px-5 py-4">Status</th>
                                <th class="px-5 py-4 text-center">Aksi</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-slate-100">

                            @forelse ($reports as $report)

                                <tr class="hover:bg-slate-50">

                                    {{-- Pelapor --}}
                                    <td class="px-5 py-4">

                                        <div class="font-semibold">
                                            {{ $report->reporter?->name ?? 'Pengguna tidak tersedia' }}
                                        </div>

                                        <div class="text-xs text-slate-400">
                                            {{ $report->reporter?->email ?? '-' }}
                                        </div>

                                    </td>

                                    {{-- Akun yang Dilaporkan --}}
                                    <td class="px-5 py-4">

                                        @if ($report->reportedUser)

                                            <div class="font-semibold">
                                                {{ $report->reportedUser->name }}
                                            </div>

                                            <div class="text-xs text-slate-400">
                                                {{ $report->reportedUser->email }}
                                            </div>

                                            @if ($report->reportedUser->status === 'suspended')

                                                <span class="mt-1 inline-block rounded-full bg-amber-100 px-2 py-0.5 text-[10px] font-semibold text-amber-700">
                                                    Sedang diblokir sementara
                                                </span>

                                            @elseif ($report->reportedUser->status === 'banned')

                                                <span class="mt-1 inline-block rounded-full bg-red-100 px-2 py-0.5 text-[10px] font-semibold text-red-700">
                                                    Diblokir permanen
                                                </span>

                                            @endif

                                        @else

                                            <span class="text-slate-400">
                                                Akun tidak tersedia
                                            </span>

                                        @endif

                                    </td>

                                    {{-- Alasan --}}
                                    <td class="px-5 py-4">
                                        <span class="font-medium">
                                            {{ $report->reason }}
                                        </span>
                                    </td>

                                    {{-- Tanggal --}}
                                    <td class="whitespace-nowrap px-5 py-4 text-xs text-slate-500">
                                        {{ $report->created_at?->format('d M Y, H:i') }}
                                    </td>

                                    {{-- Status Laporan --}}
                                    <td class="px-5 py-4">

                                        @if ($report->status === 'pending')

                                            <span class="rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-700">
                                                Pending
                                            </span>

                                        @elseif ($report->status === 'reviewed')

                                            <span class="rounded-full bg-slate-200 px-3 py-1 text-xs font-semibold text-slate-700">
                                                Diabaikan
                                            </span>

                                        @else

                                            <span class="rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-700">
                                                Ditangani
                                            </span>

                                        @endif

                                    </td>

                                    {{-- Aksi --}}
                                    <td class="px-5 py-4 text-center">

                                        <a href="{{ route('admin.account_reports.show', $report) }}"
                                           class="inline-block rounded-lg bg-blue-600 px-3 py-2 text-xs font-semibold text-white hover:bg-blue-700">
                                            Detail
                                        </a>

                                    </td>

                                </tr>

                            @empty

                                <tr>
                                    <td colspan="6" class="px-5 py-12 text-center">

                                        <div class="text-4xl">
                                            📭
                                        </div>

                                        <div class="mt-2 font-semibold">
                                            Belum ada laporan akun
                                        </div>

                                        <p class="mt-1 text-sm text-slate-400">
                                            Laporan akun dari pengguna akan muncul di halaman ini.
                                        </p>

                                    </td>
                                </tr>

                            @endforelse

                        </tbody>

                    </table>

                </div>

                {{-- Pagination --}}
                @if ($reports->hasPages())
                    <div class="border-t p-5">
                        {{ $reports->links() }}
                    </div>
                @endif

            </section>

        </main>
    </div>
</div>

</body>
</html>