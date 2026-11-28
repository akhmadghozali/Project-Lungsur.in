
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Detail Laporan Akun | Lungsur.in</title>
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

    $pendingListingReports = \App\Models\Report::where('report_type', 'listing')
        ->where('status', 'pending')
        ->count();

    $pendingAccountReports = \App\Models\Report::where('report_type', 'account')
        ->where('status', 'pending')
        ->count();

    $pendingBoostCount = \App\Models\BoostPayment::where('status', 'pending')->count();
    $pendingAuctionCount = \App\Models\AuctionPayment::where('status', 'pending')->count();

    $reportedUser = $report->reportedUser;
    $conversation = $report->conversation;
    $messages = $conversation?->messages ?? collect();
    $reportedMessage = $report->reportedMessage;
@endphp

<div class="flex min-h-screen">

    {{-- ===== SIDEBAR UTAMA ===== --}}
    @include('admin.layouts.sidebar')

    {{-- ===== KONTEN ===== --}}
    <div class="flex min-w-0 flex-1 flex-col">

        <header class="flex items-center justify-between gap-4 border-b bg-white px-6 py-3">
            <div class="text-sm font-semibold text-slate-600">
                Admin / Laporan / Laporan Akun / Detail
            </div>

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

            {{-- JUDUL HALAMAN --}}
            <div>
                <a href="{{ route('admin.account_reports.index') }}"
                   class="text-sm font-semibold text-blue-600 hover:underline">
                    ← Kembali ke Daftar Laporan Akun
                </a>

                <div class="mt-4 text-[11px] font-bold tracking-widest text-emerald-600">
                    ● CAMPUS TRUST &amp; SAFETY MODULE
                </div>

                <h1 class="mt-1 text-3xl font-extrabold">Detail Laporan Akun</h1>

                <p class="mt-2 text-sm text-slate-500">
                    Tinjau informasi akun dan bukti percakapan sebelum mengambil tindakan.
                </p>
            </div>

            @if (session('success'))
                <div class="rounded-xl bg-emerald-100 px-4 py-3 text-sm text-emerald-800">
                    {{ session('success') }}
                </div>
            @endif

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

            {{-- INFORMASI LAPORAN --}}
            <section class="rounded-2xl border bg-white p-6">
                <div class="flex flex-wrap items-center justify-between gap-3 border-b pb-4">
                    <div>
                        <div class="text-xs text-slate-400">Nomor Laporan</div>
                        <div class="text-xl font-bold">#{{ $report->id }}</div>
                    </div>

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
                </div>

                <div class="mt-5 grid gap-6 md:grid-cols-2">

                    {{-- INFORMASI PELAPOR --}}
                    <div>
                        <h2 class="mb-3 text-sm font-bold">INFORMASI PELAPOR</h2>

                        <div class="space-y-2 rounded-xl bg-slate-50 p-4 text-sm">
                            <div>
                                <span class="text-slate-500">Nama:</span>
                                <span class="font-semibold">{{ $report->reporter?->name ?? '-' }}</span>
                            </div>

                            <div>
                                <span class="text-slate-500">Email:</span>
                                <span class="font-semibold">{{ $report->reporter?->email ?? '-' }}</span>
                            </div>

                            <div>
                                <span class="text-slate-500">Tanggal laporan:</span>
                                <span class="font-semibold">
                                    {{ $report->created_at?->format('d M Y, H:i') }}
                                </span>
                            </div>
                        </div>
                    </div>

                    {{-- INFORMASI LAPORAN --}}
                    <div>
                        <h2 class="mb-3 text-sm font-bold">INFORMASI LAPORAN</h2>

                        <div class="space-y-2 rounded-xl bg-slate-50 p-4 text-sm">
                            <div>
                                <span class="text-slate-500">Alasan:</span>
                                <span class="font-semibold">{{ $report->reason }}</span>
                            </div>

                            <div>
                                <span class="text-slate-500">Detail laporan:</span>
                                <p class="mt-1 whitespace-pre-line">
                                    {{ $report->details ?: 'Tidak ada detail tambahan.' }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            {{-- INFORMASI AKUN YANG DILAPORKAN --}}
            <section class="rounded-2xl border bg-white p-6">
                <h2 class="mb-4 text-sm font-bold">AKUN YANG DILAPORKAN</h2>

                @if ($reportedUser)
                    <div class="rounded-xl bg-slate-50 p-5">
                        <div class="flex flex-wrap items-start justify-between gap-4">

                            <div>
                                <div class="text-xs text-slate-400">Nama pengguna</div>
                                <div class="mt-1 text-xl font-bold">{{ $reportedUser->name }}</div>
                                <div class="mt-1 text-sm text-slate-500">{{ $reportedUser->email }}</div>
                            </div>

                            @if ($reportedUser->status === 'suspended')
                                <span class="rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-700">
                                    Diblokir sementara
                                </span>
                            @elseif ($reportedUser->status === 'banned')
                                <span class="rounded-full bg-red-100 px-3 py-1 text-xs font-semibold text-red-700">
                                    Diblokir permanen
                                </span>
                            @else
                                <span class="rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-700">
                                    Aktif
                                </span>
                            @endif
                        </div>

                        <div class="mt-4 grid gap-3 border-t border-slate-200 pt-4 text-sm md:grid-cols-2">
                            <div>
                                <span class="text-slate-500">Status verifikasi:</span>
                                <span class="font-semibold">
                                    {{ ucfirst($reportedUser->verification_status ?? '-') }}
                                </span>
                            </div>

                            <div>
                                <span class="text-slate-500">Tanggal bergabung:</span>
                                <span class="font-semibold">
                                    {{ $reportedUser->created_at?->format('d M Y') ?? '-' }}
                                </span>
                            </div>

                            @if ($reportedUser->status === 'suspended' || $reportedUser->status === 'banned')
                                <div>
                                    <span class="text-slate-500">Alasan pemblokiran:</span>
                                    <span class="font-semibold">{{ $reportedUser->ban_reason ?? '-' }}</span>
                                </div>

                                <div>
                                    <span class="text-slate-500">Mulai diblokir:</span>
                                    <span class="font-semibold">
                                        {{ $reportedUser->banned_at?->format('d M Y, H:i') ?? '-' }}
                                    </span>
                                </div>

                                @if ($reportedUser->status === 'suspended')
                                    <div>
                                        <span class="text-slate-500">Masa blokir berakhir:</span>
                                        <span class="font-semibold">
                                            {{ $reportedUser->ban_expires_at?->format('d M Y, H:i') ?? '-' }}
                                        </span>
                                    </div>
                                @else
                                    <div>
                                        <span class="text-slate-500">Masa blokir:</span>
                                        <span class="font-semibold text-red-600">Permanen</span>
                                    </div>
                                @endif
                            @endif
                        </div>
                    </div>
                @else
                    <p class="text-sm text-slate-400">Data akun yang dilaporkan tidak tersedia.</p>
                @endif
            </section>

            {{-- KONTEKS PERCAKAPAN --}}
            <section class="rounded-2xl border bg-white p-6">
                <div class="flex flex-wrap items-center justify-between gap-3 border-b pb-4">
                    <div>
                        <h2 class="text-lg font-bold">Bukti Percakapan</h2>
                        <p class="mt-1 text-xs text-slate-500">
                            Riwayat percakapan yang menjadi dasar laporan.
                        </p>
                    </div>

                    @if ($conversation)
                        <span class="rounded-lg bg-blue-50 px-3 py-2 text-xs font-semibold text-blue-700">
                            Percakapan #{{ $conversation->id }}
                        </span>
                    @endif
                </div>

                @if ($conversation)

                    {{-- INFORMASI PIHAK DALAM CHAT --}}
                    <div class="mt-4 grid gap-3 md:grid-cols-2">

                        <div class="rounded-xl bg-slate-50 p-4">
                            <div class="text-[10px] font-bold tracking-widest text-slate-400">
                                PEMBELI
                            </div>
                            <div class="mt-1 font-semibold">
                                {{ $conversation->buyer?->name ?? '-' }}
                            </div>
                        </div>

                        <div class="rounded-xl bg-slate-50 p-4">
                            <div class="text-[10px] font-bold tracking-widest text-slate-400">
                                PENJUAL
                            </div>
                            <div class="mt-1 font-semibold">
                                {{ $conversation->seller?->name ?? '-' }}
                            </div>
                        </div>
                    </div>

                    {{-- KONTEKS IKLAN --}}
                    @if ($conversation->listing)
                        <div class="mt-3 rounded-xl border border-slate-200 p-4">
                            <div class="text-[10px] font-bold tracking-widest text-slate-400">
                                KONTEKS IKLAN
                            </div>
                            <div class="mt-1 font-semibold">
                                {{ $conversation->listing->title }}
                            </div>
                            <div class="mt-1 text-xs text-slate-500">
                                Harga: Rp {{ number_format($conversation->listing->price, 0, ',', '.') }}
                            </div>
                        </div>
                    @endif

                    {{-- RIWAYAT PESAN --}}
                    <div class="mt-5">
                        <div class="mb-3 flex flex-wrap items-center justify-between gap-2">
                            <h3 class="text-sm font-bold">RIWAYAT PESAN</h3>
                            <span class="text-xs text-slate-400">
                                {{ $messages->count() }} pesan
                            </span>
                        </div>

                        @if ($messages->isNotEmpty())
                            <div class="max-h-[600px] space-y-3 overflow-y-auto rounded-xl bg-slate-100 p-4">

                                @foreach ($messages as $message)
                                    @php
                                        $isReportedMessage = $reportedMessage
                                            && (int) $message->id === (int) $reportedMessage->id;

                                        $isReportedUserMessage = $reportedUser
                                            && (int) $message->sender_id === (int) $reportedUser->id;
                                    @endphp

                                    <div id="message-{{ $message->id }}"
                                         class="rounded-xl border p-4
                                         {{ $isReportedMessage
                                            ? 'border-red-400 bg-red-50 ring-2 ring-red-100'
                                            : 'border-slate-200 bg-white' }}">

                                        <div class="flex flex-wrap items-center justify-between gap-2">

                                            <div class="flex flex-wrap items-center gap-2">
                                                <span class="text-sm font-bold">
                                                    {{ $message->sender?->name ?? 'Pengguna tidak tersedia' }}
                                                </span>

                                                @if ($isReportedUserMessage)
                                                    <span class="rounded-full bg-amber-100 px-2 py-0.5 text-[10px] font-semibold text-amber-700">
                                                        Akun terlapor
                                                    </span>
                                                @endif
                                            </div>

                                            <span class="text-[11px] text-slate-400">
                                                {{ $message->created_at?->format('d M Y, H:i') }}
                                            </span>
                                        </div>

                                        <div class="mt-3 whitespace-pre-line break-words text-sm text-slate-700">
                                            {{ $message->message_text }}
                                        </div>

                                        @if ($isReportedMessage)
                                            <div class="mt-3 border-t border-red-200 pt-2">
                                                <span class="text-xs font-bold text-red-600">
                                                    ⚑ PESAN YANG DILAPORKAN
                                                </span>
                                            </div>
                                        @endif
                                    </div>
                                @endforeach

                            </div>
                        @else
                            <div class="rounded-xl bg-slate-50 p-8 text-center">
                                <div class="text-3xl">💬</div>
                                <p class="mt-2 text-sm font-semibold">Belum ada pesan</p>
                                <p class="mt-1 text-xs text-slate-400">
                                    Riwayat percakapan tidak tersedia.
                                </p>
                            </div>
                        @endif
                    </div>

                    @if (!$reportedMessage)
                        <div class="mt-4 rounded-lg bg-amber-50 p-3 text-xs text-amber-800">
                            Pesan spesifik yang dilaporkan tidak tersedia. Admin dapat meninjau
                            riwayat percakapan sebagai konteks laporan.
                        </div>
                    @endif

                @else
                    <div class="mt-4 rounded-xl bg-slate-50 p-8 text-center">
                        <div class="text-3xl">📭</div>
                        <p class="mt-2 text-sm font-semibold">Data percakapan tidak tersedia</p>
                        <p class="mt-1 text-xs text-slate-400">
                            Percakapan yang terkait dengan laporan ini tidak ditemukan.
                        </p>
                    </div>
                @endif
            </section>

            {{-- RIWAYAT PENANGANAN --}}
            @if ($report->resolved_by)
                <section class="rounded-2xl border bg-white p-6">
                    <h2 class="mb-3 text-sm font-bold">RIWAYAT PENANGANAN</h2>

                    <p class="text-sm text-slate-600">
                        Ditangani oleh:
                        <span class="font-semibold">{{ $report->resolver?->name ?? '-' }}</span>
                    </p>

                    <p class="mt-1 text-sm text-slate-600">
                        Waktu penanganan:
                        <span class="font-semibold">
                            {{ $report->resolved_at?->format('d M Y, H:i') ?? '-' }}
                        </span>
                    </p>
                </section>
            @endif

            {{-- AKSI MODERASI --}}
            @if ($report->status === 'pending' && $reportedUser)
                <section class="rounded-2xl border bg-white p-6">
                    <h2 class="text-lg font-bold">Tindakan Moderasi</h2>

                    <p class="mt-1 text-sm text-slate-500">
                        Pilih tindakan berdasarkan hasil pemeriksaan laporan.
                    </p>

                    @if ($reportedUser->role === 'admin')
                        <div class="mt-4 rounded-xl bg-amber-50 p-4 text-sm text-amber-800">
                            Akun admin tidak dapat diblokir melalui fitur laporan akun.
                        </div>
                    @elseif ((int) $reportedUser->id === (int) $admin->id)
                        <div class="mt-4 rounded-xl bg-amber-50 p-4 text-sm text-amber-800">
                            Admin tidak dapat memblokir akun sendiri.
                        </div>
                    @else
                        <div class="mt-5 grid gap-5 lg:grid-cols-3">

                            {{-- ABAIKAN LAPORAN --}}
                            <div class="rounded-xl border border-slate-200 p-4">
                                <div class="text-2xl">✓</div>
                                <h3 class="mt-2 font-bold">Abaikan Laporan</h3>
                                <p class="mt-1 min-h-12 text-xs text-slate-500">
                                    Laporan tidak ditindaklanjuti dan ditandai sudah ditinjau.
                                </p>

                                <form method="POST"
                                      action="{{ route('admin.account_reports.ignore', $report) }}"
                                      class="mt-4">
                                    @csrf

                                    <button type="submit"
                                            onclick="return confirm('Yakin ingin mengabaikan laporan ini?')"
                                            class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-100">
                                        Abaikan Laporan
                                    </button>
                                </form>
                            </div>

                            {{-- BAN SEMENTARA --}}
                            <div class="rounded-xl border border-amber-200 p-4">
                                <div class="text-2xl">⏳</div>
                                <h3 class="mt-2 font-bold">Ban Sementara</h3>
                                <p class="mt-1 text-xs text-slate-500">
                                    Batasi akses akun untuk jangka waktu tertentu.
                                </p>

                                <form id="temporaryBanForm"
                                      method="POST"
                                      action="{{ route('admin.account_reports.temporary_ban', $report) }}"
                                      class="mt-4 space-y-3">
                                    @csrf

                                    <div>
                                        <label class="mb-1 block text-xs font-semibold text-slate-600">
                                            Alasan pemblokiran
                                        </label>
                                        <textarea name="ban_reason"
                                                  rows="3"
                                                  required
                                                  maxlength="1000"
                                                  class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm outline-none focus:border-amber-500 focus:ring-2 focus:ring-amber-100"
                                                  placeholder="Masukkan alasan pemblokiran..."></textarea>
                                    </div>

                                    <div>
                                        <label class="mb-1 block text-xs font-semibold text-slate-600">
                                            Durasi pemblokiran
                                        </label>
                                        <div class="flex items-center gap-2">
                                            <input type="number"
                                                   name="ban_days"
                                                   min="1"
                                                   max="365"
                                                   value="7"
                                                   required
                                                   class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm outline-none focus:border-amber-500 focus:ring-2 focus:ring-amber-100">
                                            <span class="text-xs text-slate-500">Hari</span>
                                        </div>
                                    </div>

                                    <button type="submit"
                                            class="w-full rounded-xl bg-amber-500 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-amber-600">
                                        Ban Sementara
                                    </button>
                                </form>
                            </div>

                            {{-- BAN PERMANEN --}}
                            <div class="rounded-xl border border-red-200 p-4">
                                <div class="text-2xl">🚫</div>
                                <h3 class="mt-2 font-bold">Ban Permanen</h3>
                                <p class="mt-1 text-xs text-slate-500">
                                    Blokir akun secara permanen tanpa batas waktu.
                                </p>

                                <form id="permanentBanForm"
                                      method="POST"
                                      action="{{ route('admin.account_reports.permanent_ban', $report) }}"
                                      class="mt-4 space-y-3">
                                    @csrf

                                    <div>
                                        <label class="mb-1 block text-xs font-semibold text-slate-600">
                                            Alasan pemblokiran
                                        </label>
                                        <textarea name="ban_reason"
                                                  rows="3"
                                                  required
                                                  maxlength="1000"
                                                  class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm outline-none focus:border-red-500 focus:ring-2 focus:ring-red-100"
                                                  placeholder="Masukkan alasan pemblokiran..."></textarea>
                                    </div>

                                    <button type="submit"
                                            class="w-full rounded-xl bg-red-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-red-700">
                                        Ban Permanen
                                    </button>
                                </form>
                            </div>

                        </div>
                    @endif
                </section>
            @endif

            {{-- MODAL KONFIRMASI BAN SEMENTARA --}}
            <div id="temporaryBanModal"
                 class="fixed inset-0 z-[100] hidden items-center justify-center bg-slate-900/50 p-4">

                <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-2xl">

                    <div class="flex items-start gap-4">

                        <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-amber-100 text-amber-600">
                            <svg class="h-6 w-6" fill="none"
                                 viewBox="0 0 24 24" stroke="currentColor"
                                 stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M12 9v3m0 4h.01M10.29 3.86 1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                            </svg>
                        </div>

                        <div>
                            <h2 class="text-lg font-bold text-slate-900">
                                Konfirmasi Ban Sementara
                            </h2>

                            <p class="mt-2 text-sm leading-relaxed text-slate-500">
                                Apakah kamu yakin ingin memblokir akun ini untuk sementara?
                            </p>

                            <p class="mt-3 rounded-lg bg-amber-50 px-3 py-2 text-xs font-medium text-amber-700">
                                Akun akan diblokir selama <span id="temporaryBanDuration" class="font-bold">-</span> hari dan dapat kembali mengakses platform setelah masa pemblokiran berakhir.
                            </p>
                        </div>

                    </div>

                    <div class="mt-6 flex justify-end gap-2">

                        <button type="button"
                                id="cancelTemporaryBan"
                                class="rounded-xl border border-slate-200 px-4 py-2.5 text-xs font-semibold text-slate-600 transition hover:bg-slate-50">
                            Batal
                        </button>

                        <button type="button"
                                id="confirmTemporaryBan"
                                class="rounded-xl bg-amber-500 px-4 py-2.5 text-xs font-semibold text-white transition hover:bg-amber-600">
                            Ya, Ban Sementara
                        </button>

                    </div>

                </div>
            </div>

            {{-- MODAL KONFIRMASI BAN PERMANEN --}}
            <div id="permanentBanModal"
                 class="fixed inset-0 z-[100] hidden items-center justify-center bg-slate-900/50 p-4">

                <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-2xl">

                    <div class="flex items-start gap-4">

                        <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-red-100 text-red-600">
                            <svg class="h-6 w-6" fill="none"
                                 viewBox="0 0 24 24" stroke="currentColor"
                                 stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M12 9v3m0 4h.01M10.29 3.86 1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                            </svg>
                        </div>

                        <div>
                            <h2 class="text-lg font-bold text-slate-900">
                                Konfirmasi Ban Permanen
                            </h2>

                            <p class="mt-2 text-sm leading-relaxed text-slate-500">
                                Apakah kamu yakin ingin memblokir akun ini secara permanen?
                                Akun tidak akan dapat mengakses platform kembali melalui mekanisme pemblokiran ini.
                            </p>

                            <p class="mt-3 rounded-lg bg-red-50 px-3 py-2 text-xs font-medium text-red-700">
                                Perhatian: Ban permanen tidak memiliki masa kedaluwarsa.
                            </p>
                        </div>

                    </div>

                    <div class="mt-6 flex justify-end gap-2">

                        <button type="button"
                                id="cancelPermanentBan"
                                class="rounded-xl border border-slate-200 px-4 py-2.5 text-xs font-semibold text-slate-600 transition hover:bg-slate-50">
                            Batal
                        </button>

                        <button type="button"
                                id="confirmPermanentBan"
                                class="rounded-xl bg-red-600 px-4 py-2.5 text-xs font-semibold text-white transition hover:bg-red-700">
                            Ya, Ban Permanen
                        </button>

                    </div>

                </div>
            </div>

        </main>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const permanentBanForm = document.getElementById('permanentBanForm');
    const permanentBanModal = document.getElementById('permanentBanModal');
    const cancelPermanentBan = document.getElementById('cancelPermanentBan');
    const confirmPermanentBan = document.getElementById('confirmPermanentBan');

    const temporaryBanForm = document.getElementById('temporaryBanForm');
    const temporaryBanModal = document.getElementById('temporaryBanModal');
    const cancelTemporaryBan = document.getElementById('cancelTemporaryBan');
    const confirmTemporaryBan = document.getElementById('confirmTemporaryBan');
    const temporaryBanDuration = document.getElementById('temporaryBanDuration');

    if (permanentBanForm && permanentBanModal) {
        let isPermanentBanConfirmed = false;

        function closePermanentBanModal() {
            permanentBanModal.classList.add('hidden');
            permanentBanModal.classList.remove('flex');
        }

        permanentBanForm.addEventListener('submit', function (event) {
            if (isPermanentBanConfirmed) {
                isPermanentBanConfirmed = false;
                return;
            }

            event.preventDefault();
            permanentBanModal.classList.remove('hidden');
            permanentBanModal.classList.add('flex');
        });

        cancelPermanentBan.addEventListener('click', function () {
            closePermanentBanModal();
        });

        confirmPermanentBan.addEventListener('click', function () {
            if (!permanentBanForm.checkValidity()) {
                permanentBanForm.reportValidity();
                return;
            }

            isPermanentBanConfirmed = true;
            permanentBanForm.requestSubmit();
        });

        permanentBanModal.addEventListener('click', function (event) {
            if (event.target === permanentBanModal) {
                closePermanentBanModal();
            }
        });
    }

    if (temporaryBanForm && temporaryBanModal) {
        let isTemporaryBanConfirmed = false;

        function closeTemporaryBanModal() {
            temporaryBanModal.classList.add('hidden');
            temporaryBanModal.classList.remove('flex');
        }

        temporaryBanForm.addEventListener('submit', function (event) {
            if (isTemporaryBanConfirmed) {
                isTemporaryBanConfirmed = false;
                return;
            }

            event.preventDefault();

            const banDays = temporaryBanForm.querySelector('[name="ban_days"]').value;
            temporaryBanDuration.textContent = banDays;

            temporaryBanModal.classList.remove('hidden');
            temporaryBanModal.classList.add('flex');
        });

        cancelTemporaryBan.addEventListener('click', function () {
            closeTemporaryBanModal();
        });

        confirmTemporaryBan.addEventListener('click', function () {
            if (!temporaryBanForm.checkValidity()) {
                temporaryBanForm.reportValidity();
                return;
            }

            isTemporaryBanConfirmed = true;
            temporaryBanForm.requestSubmit();
        });

        temporaryBanModal.addEventListener('click', function (event) {
            if (event.target === temporaryBanModal) {
                closeTemporaryBanModal();
            }
        });

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape' && !temporaryBanModal.classList.contains('hidden')) {
                closeTemporaryBanModal();
            }
        });
    }

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape' && permanentBanModal && !permanentBanModal.classList.contains('hidden')) {
            permanentBanModal.classList.add('hidden');
            permanentBanModal.classList.remove('flex');
        }
    });
});
</script>

</body>
</html>