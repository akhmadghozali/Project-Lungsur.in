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
@endphp

<div class="flex min-h-screen">

    {{-- ===== SIDEBAR ===== --}}
    <x-admin-sidebar active="reports" />

    {{-- ===== KONTEN ===== --}}
    <div class="flex min-w-0 flex-1 flex-col">

        <header class="flex items-center justify-between gap-4 border-b bg-white px-6 py-3">
            <div class="text-sm font-semibold text-slate-600">Admin / Reported Items</div>

            <div class="flex items-center gap-3">
                <div class="text-right">
                    <div class="text-sm font-semibold">{{ $admin->name }}</div>
                    <div class="text-[11px] text-blue-600">Campus Trust &amp; Safety</div>
                </div>
                <div class="flex h-9 w-9 items-center justify-center rounded-full bg-blue-600 text-sm font-bold text-white">
                    {{ $adminInitial }}
                </div>
            </div>
        </header>

        <main class="space-y-6 p-6">

            <div>
                <div class="text-[11px] font-bold tracking-widest text-emerald-600">● CAMPUS TRUST &amp; SAFETY MODULE</div>
                <h1 class="mt-1 text-3xl font-extrabold">Reports &amp; Moderation</h1>
                <p class="mt-2 max-w-xl text-sm text-slate-500">
                    Kelola laporan pengguna terhadap iklan atau akun yang dianggap
                    melanggar aturan platform Lungsur.in.
                </p>
            </div>

            @if (session('success'))
                <div class="rounded-xl bg-emerald-100 px-4 py-3 text-sm text-emerald-800">{{ session('success') }}</div>
            @endif
            @if ($errors->any())
                <div class="rounded-xl bg-red-100 px-4 py-3 text-sm text-red-800">{{ $errors->first() }}</div>
            @endif

            <section class="overflow-hidden rounded-2xl border bg-white">

                <div class="flex flex-wrap items-center justify-between gap-3 border-b p-5">
                    <div>
                        <h2 class="text-lg font-bold">Daftar Laporan Pengguna</h2>
                        <p class="text-xs text-slate-500">Laporan terbaru ditampilkan terlebih dahulu.</p>
                    </div>
                    <span class="rounded-lg bg-blue-50 px-3 py-2 text-xs font-semibold text-blue-700">
                        Total: {{ $reports->total() }} laporan
                    </span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-slate-50 text-[11px] uppercase tracking-wider text-slate-500">
                            <tr>
                                <th class="px-5 py-4">Pelapor</th>
                                <th class="px-5 py-4">Iklan / Pengguna</th>
                                <th class="px-5 py-4">Alasan</th>
                                <th class="px-5 py-4">Tanggal</th>
                                <th class="px-5 py-4">Status</th>
                                <th class="px-5 py-4 text-center">Aksi</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-slate-100">
                            @forelse ($reports as $report)
                                <tr class="hover:bg-slate-50">

                                    <td class="px-5 py-4">
                                        <div class="font-semibold">
                                            {{ $report->reporter?->name ?? 'Pengguna tidak tersedia' }}
                                        </div>
                                    </td>

                                    <td class="px-5 py-4">
                                        @if ($report->reportedListing)
                                            <div class="font-semibold">{{ $report->reportedListing->title }}</div>
                                            <div class="text-xs text-slate-400">
                                                Iklan
                                                @if ($report->reportedListing->trashed())
                                                    · <span class="font-semibold text-red-500">Sudah diturunkan</span>
                                                @endif
                                            </div>
                                        @elseif ($report->reportedUser)
                                            <div class="font-semibold">{{ $report->reportedUser->name }}</div>
                                            <div class="text-xs text-slate-400">Pengguna</div>
                                        @else
                                            <span class="text-slate-400">Data tidak tersedia</span>
                                        @endif
                                    </td>

                                    <td class="px-5 py-4">
                                        <span class="font-medium">{{ $report->reason }}</span>
                                    </td>

                                    <td class="whitespace-nowrap px-5 py-4 text-xs text-slate-500">
                                        {{ $report->created_at?->format('d M Y, H:i') }}
                                    </td>

                                    <td class="px-5 py-4">
                                        @if ($report->status === 'pending')
                                            <span class="rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-700">Pending</span>
                                        @elseif ($report->status === 'reviewed')
                                            <span class="rounded-full bg-slate-200 px-3 py-1 text-xs font-semibold text-slate-700">Diabaikan</span>
                                        @else
                                            <span class="rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-700">Diturunkan</span>
                                        @endif
                                    </td>

                                    <td class="px-5 py-4 text-center">
                                        <a href="{{ route('admin.reports.show', $report) }}"
                                           class="inline-block rounded-lg bg-blue-600 px-3 py-2 text-xs font-semibold text-white hover:bg-blue-700">
                                            Detail
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-5 py-12 text-center">
                                        <div class="text-4xl">📭</div>
                                        <div class="mt-2 font-semibold">Belum ada laporan</div>
                                        <p class="mt-1 text-sm text-slate-400">Laporan dari pengguna akan muncul di halaman ini.</p>
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