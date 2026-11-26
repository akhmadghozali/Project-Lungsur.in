<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Detail Laporan | Lungsur.in</title>
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
    $listing = $report->reportedListing;
@endphp

<div class="flex min-h-screen">

    <aside class="hidden lg:flex w-60 shrink-0 flex-col bg-[#1c2541] text-slate-300">
        <div class="px-6 py-6">
            <div class="text-xl font-extrabold text-white">Lungsur.in</div>
            <div class="text-[10px] font-semibold tracking-widest text-slate-400">STAFF OPERATIONS</div>
        </div>

        <div class="px-6 pb-2 pt-4 text-[10px] font-bold tracking-widest text-slate-400">PORTAL MODERASI</div>
        <nav class="space-y-1 px-3 text-sm">
            <a href="{{ route('admin.verifications.index') }}"
               class="flex items-center justify-between rounded-xl px-3 py-2.5 text-slate-300 hover:bg-white/5 hover:text-white transition">
                <span class="flex items-center gap-3"><span>🛡</span> User Verification</span>
                @if ($pendingVerifications > 0)
                    <span class="rounded-full bg-red-500 px-2 py-0.5 text-[11px] font-bold text-white">{{ $pendingVerifications }}</span>
                @endif
            </a>

            <div class="px-3 pb-1 pt-4 text-[10px] font-bold tracking-widest text-slate-400">MONETISASI</div>

            <a href="{{ route('admin.payments.boost') }}"
               class="flex items-center justify-between rounded-xl px-3 py-2.5 text-slate-300 hover:bg-white/5 hover:text-white transition">
                <span class="flex items-center gap-3">
                    <span class="text-amber-400">⚡</span> Verifikasi Boost Iklan
                </span>
                @if ($pendingBoostCount > 0)
                    <span class="rounded-full bg-amber-500 text-slate-950 font-bold px-2 py-0.5 text-[11px]">{{ $pendingBoostCount }}</span>
                @endif
            </a>

            <a href="{{ route('admin.payments.auction') }}"
               class="flex items-center justify-between rounded-xl px-3 py-2.5 text-slate-300 hover:bg-white/5 hover:text-white transition">
                <span class="flex items-center gap-3">
                    <span class="text-emerald-400">🔨</span> Verifikasi Biaya Lelang
                </span>
                @if ($pendingAuctionCount > 0)
                    <span class="rounded-full bg-emerald-500 text-white font-bold px-2 py-0.5 text-[11px]">{{ $pendingAuctionCount }}</span>
                @endif
            </a>

            <div class="px-3 pb-1 pt-4 text-[10px] font-bold tracking-widest text-slate-400">SISTEM &amp; ADUAN</div>
            <a href="{{ route('admin.reports.index') }}"
               class="flex items-center gap-3 rounded-xl bg-blue-600 px-3 py-2.5 font-semibold text-white">
                <span>⚑</span> Reported Items
            </a>
            <a href="{{ route('home') }}" class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-slate-300 hover:bg-white/5 hover:text-white transition">
                <span>🌐</span> Lihat Katalog Publik
            </a>
        </nav>

        <div class="mt-auto p-4">
            <div class="rounded-xl bg-white/5 p-3">
                <div class="flex items-center gap-3">
                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-blue-600 text-sm font-bold text-white">
                        {{ $adminInitial }}
                    </div>
                    <div class="min-w-0 leading-tight">
                        <div class="truncate text-sm font-semibold text-white">{{ $admin->name }}</div>
                        <div class="text-[11px] text-slate-400">{{ $adminRoleLabel }}</div>
                    </div>
                </div>
                <div class="mt-2 truncate text-[11px] text-slate-400">{{ $admin->email }}</div>

                <form method="POST" action="{{ route('logout') }}" class="mt-3">
                    @csrf
                    <button type="submit"
                            class="w-full min-h-[40px] rounded-lg bg-white/10 px-3 py-2 text-xs font-semibold text-slate-200 transition hover:bg-red-500 hover:text-white cursor-pointer">
                        ⎋ Keluar
                    </button>
                </form>
            </div>
            <div class="mt-3 text-[11px] text-slate-400">© {{ date('Y') }} Lungsur Campus</div>
        </div>
    </aside>

    <div class="flex min-w-0 flex-1 flex-col">

        <header class="flex items-center justify-between gap-4 border-b bg-white px-4 sm:px-6 py-3">
            <div class="text-xs sm:text-sm font-bold text-slate-700">Admin / Reported Items / Detail Laporan</div>

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
                <a href="{{ route('admin.reports.index') }}" class="min-h-[44px] inline-flex items-center text-sm font-bold text-blue-700 hover:underline">
                    &larr; Kembali ke Daftar Laporan
                </a>
                <div class="mt-2 text-[11px] font-bold tracking-widest text-emerald-700">● CAMPUS TRUST &amp; SAFETY MODULE</div>
                <h1 class="mt-1 text-2xl sm:text-3xl font-extrabold text-slate-900">Detail Laporan</h1>
                <p class="mt-2 text-xs sm:text-sm text-slate-600">Tinjau informasi laporan sebelum mengambil tindakan moderasi.</p>
            </div>

            @if (session('success'))
                <div class="rounded-xl bg-emerald-100 px-4 py-3 text-sm font-semibold text-emerald-900">{{ session('success') }}</div>
            @endif
            @if ($errors->any())
                <div class="rounded-xl bg-red-100 px-4 py-3 text-sm font-semibold text-red-900">{{ $errors->first() }}</div>
            @endif

            <section class="rounded-2xl border bg-white p-4 sm:p-6">
                <div class="flex flex-wrap items-center justify-between gap-3 border-b pb-4">
                    <div>
                        <div class="text-xs text-slate-600">Nomor Laporan</div>
                        <div class="text-xl font-bold text-slate-900">#{{ $report->id }}</div>
                    </div>

                    @if ($report->status === 'pending')
                        <span class="rounded-full bg-amber-100 px-3 py-1 text-xs font-bold text-amber-800">Pending</span>
                    @elseif ($report->status === 'reviewed')
                        <span class="rounded-full bg-slate-200 px-3 py-1 text-xs font-bold text-slate-800">Diabaikan</span>
                    @else
                        <span class="rounded-full bg-emerald-100 px-3 py-1 text-xs font-bold text-emerald-800">Diturunkan</span>
                    @endif
                </div>

                <div class="mt-5 grid gap-5 sm:gap-6 md:grid-cols-2">
                    <div>
                        <h2 class="mb-3 text-sm font-bold text-slate-900">INFORMASI PELAPOR</h2>
                        <div class="space-y-2 rounded-xl bg-slate-50 p-4 text-sm">
                            <div><span class="text-slate-600">Nama:</span> <span class="font-bold text-slate-900">{{ $report->reporter?->name ?? '-' }}</span></div>
                            <div><span class="text-slate-600">Email:</span> <span class="font-bold text-slate-900 break-all">{{ $report->reporter?->email ?? '-' }}</span></div>
                            <div><span class="text-slate-600">Tanggal laporan:</span> <span class="font-bold text-slate-900">{{ $report->created_at?->format('d M Y, H:i') }}</span></div>
                        </div>
                    </div>

                    <div>
                        <h2 class="mb-3 text-sm font-bold text-slate-900">INFORMASI LAPORAN</h2>
                        <div class="space-y-2 rounded-xl bg-slate-50 p-4 text-sm">
                            <div><span class="text-slate-600">Alasan:</span> <span class="font-bold text-slate-900">{{ $report->reason }}</span></div>
                            <div>
                                <span class="text-slate-600">Detail:</span>
                                <p class="mt-1 whitespace-pre-line text-slate-800">{{ $report->details ?: 'Tidak ada detail tambahan.' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section class="rounded-2xl border bg-white p-4 sm:p-6">
                <h2 class="mb-4 text-sm font-bold text-slate-900">OBJEK YANG DILAPORKAN</h2>

                @if ($listing)
                    <div class="flex flex-col gap-5 rounded-xl bg-slate-50 p-4 md:flex-row">

                        <div class="shrink-0 md:w-64">
                            @if ($listing->photo_url)
                                <img src="{{ $listing->photo_url }}"
                                     alt="{{ $listing->title }}"
                                     class="h-48 w-full rounded-lg object-cover md:h-44">
                            @else
                                <div class="flex h-48 w-full items-center justify-center rounded-lg bg-slate-200 text-sm text-slate-600 md:h-44">
                                    Tidak ada foto
                                </div>
                            @endif
                        </div>

                        <div class="min-w-0 flex-1">
                            <div class="text-xs font-semibold text-slate-600">Iklan</div>
                            <div class="mt-1 text-lg font-bold text-slate-900">{{ $listing->title }}</div>

                            <div class="mt-2 text-sm text-slate-700">
                                Harga: <strong class="text-slate-900">Rp {{ number_format($listing->price, 0, ',', '.') }}</strong>
                            </div>
                            <div class="mt-1 text-sm text-slate-700">
                                Pemilik: {{ $report->reportedUser?->name ?? $listing->user?->name ?? '-' }}
                            </div>
                            <div class="mt-1 text-sm text-slate-700">
                                Diunggah: {{ $listing->created_at?->format('d M Y, H:i') }}
                                <span class="text-slate-600">({{ $listing->created_at?->diffForHumans() }})</span>
                            </div>
                            <div class="mt-1 text-sm text-slate-700">
                                Total laporan untuk iklan ini:
                                <span class="font-bold {{ $sameListingReports > 1 ? 'text-red-600' : '' }}">{{ $sameListingReports }}</span>
                            </div>
                            <div class="mt-1 text-sm text-slate-700">
                                Status iklan:
                                @if ($listing->trashed())
                                    <span class="font-bold text-red-600">Diturunkan</span>
                                @else
                                    {{ $listing->status }}
                                @endif
                            </div>

                            @unless ($listing->trashed())
                                <a href="{{ route('listings.show', $listing->id) }}" target="_blank"
                                   class="mt-3 min-h-[40px] inline-flex items-center text-sm font-bold text-blue-700 hover:underline">
                                    Buka halaman iklan &rarr;
                                </a>
                            @endunless
                        </div>
                    </div>

                    <div class="mt-4 rounded-xl bg-slate-50 p-4">
                        <div class="text-[10px] font-bold tracking-widest text-slate-600">DESKRIPSI BARANG</div>
                        <p class="mt-1 max-h-40 overflow-y-auto whitespace-pre-line text-sm text-slate-800">{{ $listing->description ?: 'Tidak ada deskripsi.' }}</p>
                    </div>

                    @if ($listing->images->count() > 1)
                        <div class="mt-4">
                            <div class="mb-2 text-[10px] font-bold tracking-widest text-slate-600">
                                SEMUA FOTO ({{ $listing->images->count() }})
                            </div>
                            <div class="flex flex-wrap gap-2">
                                @foreach ($listing->images as $img)
                                    @php
                                        $src = \Illuminate\Support\Str::startsWith($img->image_url, ['http://', 'https://'])
                                            ? $img->image_url
                                            : asset('storage/' . ltrim($img->image_url, '/'));
                                    @endphp
                                    <a href="{{ $src }}" target="_blank">
                                        <img src="{{ $src }}" alt="Foto iklan"
                                             class="h-20 w-20 rounded-lg object-cover hover:opacity-80">
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif
                @elseif ($report->reportedUser)
                    <div class="rounded-xl bg-slate-50 p-4">
                        <div class="text-xs text-slate-600">Pengguna</div>
                        <div class="mt-1 font-bold text-slate-900">{{ $report->reportedUser->name }}</div>
                        <div class="text-sm text-slate-600">{{ $report->reportedUser->email }}</div>
                    </div>
                @else
                    <p class="text-sm text-slate-600">Data objek yang dilaporkan tidak tersedia.</p>
                @endif
            </section>

            @if ($report->resolved_by)
                <section class="rounded-2xl border bg-white p-4 sm:p-6">
                    <h2 class="mb-3 text-sm font-bold text-slate-900">RIWAYAT PENANGANAN</h2>
                    <p class="text-sm text-slate-700">Ditangani oleh: {{ $report->resolver?->name ?? '-' }}</p>
                    <p class="mt-1 text-sm text-slate-700">
                        Waktu penanganan: {{ $report->resolved_at?->format('d M Y, H:i') ?? '-' }}
                    </p>
                </section>
            @endif

            @if ($report->status === 'pending')
                <section class="rounded-2xl border bg-white p-4 sm:p-6">
                    <h2 class="text-lg font-bold text-slate-900">Tindakan Moderasi</h2>
                    <p class="mt-1 text-sm text-slate-600">Pilih tindakan berdasarkan hasil pemeriksaan laporan.</p>

                    <div class="mt-5 flex flex-wrap gap-3">
                        <button type="button" onclick="openModerationModal('ignore')"
                                class="min-h-[44px] rounded-xl border border-slate-300 px-5 py-2.5 text-sm font-bold text-slate-700 transition hover:bg-slate-100 cursor-pointer">
                            Abaikan Laporan
                        </button>

                        @if ($listing && !$listing->trashed())
                            <button type="button" onclick="openModerationModal('take-down')"
                                    class="min-h-[44px] rounded-xl bg-red-600 px-5 py-2.5 text-sm font-bold text-white transition hover:bg-red-700 cursor-pointer">
                                Turunkan Iklan
                            </button>
                        @endif
                    </div>
                </section>

                <div id="moderationModal"
                     class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/50 p-4 backdrop-blur-sm">

                    <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-2xl">
                        <div id="modalIcon"
                             class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-blue-100 text-2xl text-blue-600">
                            ?
                        </div>

                        <h3 id="modalTitle" class="mt-4 text-center text-xl font-bold text-slate-900">
                            Konfirmasi Tindakan
                        </h3>

                        <p id="modalMessage" class="mt-2 text-center text-sm leading-relaxed text-slate-600">
                            Apakah kamu yakin ingin melanjutkan tindakan ini?
                        </p>

                        <div class="mt-6 flex justify-center gap-3">
                            <button type="button" onclick="closeModerationModal()"
                                    class="min-h-[44px] rounded-xl border border-slate-300 px-5 py-2.5 text-sm font-bold text-slate-700 transition hover:bg-slate-100 cursor-pointer">
                                Batal
                            </button>

                            <form id="moderationForm" method="POST">
                                @csrf
                                <button id="modalConfirmButton" type="submit"
                                        class="min-h-[44px] rounded-xl bg-blue-600 px-5 py-2.5 text-sm font-bold text-white transition hover:bg-blue-700 cursor-pointer">
                                    Ya, Lanjutkan
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <script>
                    function openModerationModal(action) {
                        const modal = document.getElementById('moderationModal');
                        const form = document.getElementById('moderationForm');
                        const title = document.getElementById('modalTitle');
                        const message = document.getElementById('modalMessage');
                        const icon = document.getElementById('modalIcon');
                        const confirmButton = document.getElementById('modalConfirmButton');

                        if (action === 'ignore') {
                            form.action = "{{ route('admin.reports.ignore', $report) }}";
                            title.textContent = 'Abaikan Laporan?';
                            message.textContent = 'Apakah kamu yakin ingin mengabaikan laporan ini? Laporan akan ditandai sebagai sudah ditinjau.';
                            icon.textContent = '✓';
                            icon.className = 'mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-blue-100 text-2xl font-bold text-blue-600';
                            confirmButton.textContent = 'Ya, Abaikan';
                            confirmButton.className = 'min-h-[44px] rounded-xl bg-blue-600 px-5 py-2.5 text-sm font-bold text-white transition hover:bg-blue-700 cursor-pointer';
                        } else {
                            form.action = "{{ route('admin.reports.take_down', $report) }}";
                            title.textContent = 'Turunkan Iklan?';
                            message.textContent = 'Apakah kamu yakin ingin menurunkan iklan ini? Iklan tidak akan tampil lagi di halaman publik.';
                            icon.textContent = '!';
                            icon.className = 'mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-red-100 text-2xl font-bold text-red-600';
                            confirmButton.textContent = 'Ya, Turunkan';
                            confirmButton.className = 'min-h-[44px] rounded-xl bg-red-600 px-5 py-2.5 text-sm font-bold text-white transition hover:bg-red-700 cursor-pointer';
                        }

                        modal.classList.remove('hidden');
                        modal.classList.add('flex');
                    }

                    function closeModerationModal() {
                        const modal = document.getElementById('moderationModal');
                        modal.classList.add('hidden');
                        modal.classList.remove('flex');
                    }

                    document.getElementById('moderationModal').addEventListener('click', function(event) {
                        if (event.target === this) {
                            closeModerationModal();
                        }
                    });
                </script>
            @endif

        </main>
    </div>
</div>

</body>
</html>
