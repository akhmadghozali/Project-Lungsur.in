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
    $listing = $report->reportedListing;
@endphp

<div class="flex min-h-screen">

    {{-- ===== SIDEBAR ===== --}}
    <aside class="hidden lg:flex w-60 shrink-0 flex-col bg-[#1c2541] text-slate-300">
        <div class="px-6 py-6">
            <div class="text-xl font-extrabold text-white">Lungsur.in</div>
            <div class="text-[10px] font-semibold tracking-widest text-slate-400">STAFF OPERATIONS</div>
        </div>

        <div class="px-6 pb-2 pt-4 text-[10px] font-bold tracking-widest text-slate-500">PORTAL MODERASI</div>
        <nav class="space-y-1 px-3 text-sm">
            <a href="{{ route('admin.verifications.index') }}"
               class="flex items-center justify-between rounded-xl px-3 py-2.5 text-slate-300 hover:bg-white/5 hover:text-white transition">
                <span class="flex items-center gap-3"><span>🛡</span> User Verification</span>
                @if ($pendingVerifications > 0)
                    <span class="rounded-full bg-red-500 px-2 py-0.5 text-[11px] font-bold text-white">{{ $pendingVerifications }}</span>
                @endif
            </a>

            <div class="px-3 pb-1 pt-4 text-[10px] font-bold tracking-widest text-slate-500">MONETISASI</div>

            {{-- Menu Verifikasi Boost Iklan (Rp5.000 / 30 Hari) --}}
            <a href="{{ route('admin.payments.boost') }}"
               class="flex items-center justify-between rounded-xl px-3 py-2.5 text-slate-300 hover:bg-white/5 hover:text-white transition">
                <span class="flex items-center gap-3">
                    <span class="text-amber-400">⚡</span> Verifikasi Boost Iklan
                </span>
                @php
                    $pendingBoostCount = \App\Models\BoostPayment::where('status', 'pending')->count();
                @endphp
                @if ($pendingBoostCount > 0)
                    <span class="rounded-full bg-amber-500 text-slate-950 font-bold px-2 py-0.5 text-[11px]">{{ $pendingBoostCount }}</span>
                @endif
            </a>

            {{-- Menu Verifikasi Pembukaan Sesi Lelang (Rp5.000) --}}
            <a href="{{ route('admin.payments.auction') }}"
               class="flex items-center justify-between rounded-xl px-3 py-2.5 text-slate-300 hover:bg-white/5 hover:text-white transition">
                <span class="flex items-center gap-3">
                    <span class="text-emerald-400">🔨</span> Verifikasi Biaya Lelang
                </span>
                @php
                    $pendingAuctionCount = \App\Models\AuctionPayment::where('status', 'pending')->count();
                @endphp
                @if ($pendingAuctionCount > 0)
                    <span class="rounded-full bg-emerald-500 text-white font-bold px-2 py-0.5 text-[11px]">{{ $pendingAuctionCount }}</span>
                @endif
            </a>

            <div class="px-3 pb-1 pt-4 text-[10px] font-bold tracking-widest text-slate-500">SISTEM & ADUAN</div>
            <a href="#" class="flex items-center gap-3 rounded-lg px-3 py-2.5 hover:bg-white/5 text-slate-400"><span>▤</span> Content Moderation</a>
            <a href="{{ route('admin.reports.index') }}"
               class="flex items-center gap-3 rounded-xl bg-blue-600 px-3 py-2.5 font-semibold text-white"><span>⚑</span> Reported Items</a>
            <a href="#" class="flex items-center gap-3 rounded-lg px-3 py-2.5 hover:bg-white/5 text-slate-400"><span>☰</span> System Logs</a>
            <a href="#" class="flex items-center gap-3 rounded-lg px-3 py-2.5 hover:bg-white/5 text-slate-400"><span>⚙</span> Admin Settings</a>
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
                            class="w-full rounded-lg bg-white/10 px-3 py-2 text-xs font-semibold text-slate-200 transition hover:bg-red-500 hover:text-white">
                        ⎋ Keluar
                    </button>
                </form>
            </div>
            <div class="mt-3 text-[11px] text-slate-500">© {{ date('Y') }} Lungsur Campus</div>
        </div>
    </aside>

    {{-- ===== KONTEN ===== --}}
    <div class="flex min-w-0 flex-1 flex-col">

        <header class="flex items-center justify-between gap-4 border-b bg-white px-6 py-3">
            <div class="text-sm font-semibold text-slate-600">Admin / Reported Items / Detail Laporan</div>

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
                <a href="{{ route('admin.reports.index') }}" class="text-sm font-semibold text-blue-600 hover:underline">
                    ← Kembali ke Daftar Laporan
                </a>
                <div class="mt-4 text-[11px] font-bold tracking-widest text-emerald-600">● CAMPUS TRUST &amp; SAFETY MODULE</div>
                <h1 class="mt-1 text-3xl font-extrabold">Detail Laporan</h1>
                <p class="mt-2 text-sm text-slate-500">Tinjau informasi laporan sebelum mengambil tindakan moderasi.</p>
            </div>

            @if (session('success'))
                <div class="rounded-xl bg-emerald-100 px-4 py-3 text-sm text-emerald-800">{{ session('success') }}</div>
            @endif
            @if ($errors->any())
                <div class="rounded-xl bg-red-100 px-4 py-3 text-sm text-red-800">{{ $errors->first() }}</div>
            @endif

            {{-- INFORMASI LAPORAN --}}
            <section class="rounded-2xl border bg-white p-6">
                <div class="flex flex-wrap items-center justify-between gap-3 border-b pb-4">
                    <div>
                        <div class="text-xs text-slate-400">Nomor Laporan</div>
                        <div class="text-xl font-bold">#{{ $report->id }}</div>
                    </div>

                    @if ($report->status === 'pending')
                        <span class="rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-700">Pending</span>
                    @elseif ($report->status === 'reviewed')
                        <span class="rounded-full bg-slate-200 px-3 py-1 text-xs font-semibold text-slate-700">Diabaikan</span>
                    @else
                        <span class="rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-700">Diturunkan</span>
                    @endif
                </div>

                <div class="mt-5 grid gap-6 md:grid-cols-2">
                    <div>
                        <h2 class="mb-3 text-sm font-bold">INFORMASI PELAPOR</h2>
                        <div class="space-y-2 rounded-xl bg-slate-50 p-4 text-sm">
                            <div><span class="text-slate-500">Nama:</span> <span class="font-semibold">{{ $report->reporter?->name ?? '-' }}</span></div>
                            <div><span class="text-slate-500">Email:</span> <span class="font-semibold">{{ $report->reporter?->email ?? '-' }}</span></div>
                            <div><span class="text-slate-500">Tanggal laporan:</span> <span class="font-semibold">{{ $report->created_at?->format('d M Y, H:i') }}</span></div>
                        </div>
                    </div>

                    <div>
                        <h2 class="mb-3 text-sm font-bold">INFORMASI LAPORAN</h2>
                        <div class="space-y-2 rounded-xl bg-slate-50 p-4 text-sm">
                            <div><span class="text-slate-500">Alasan:</span> <span class="font-semibold">{{ $report->reason }}</span></div>
                            <div>
                                <span class="text-slate-500">Detail:</span>
                                <p class="mt-1 whitespace-pre-line">{{ $report->details ?: 'Tidak ada detail tambahan.' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            {{-- OBJEK YANG DILAPORKAN --}}
            <section class="rounded-2xl border bg-white p-6">
                <h2 class="mb-4 text-sm font-bold">OBJEK YANG DILAPORKAN</h2>

                @if ($listing)
                    <div class="flex flex-col gap-5 rounded-xl bg-slate-50 p-4 md:flex-row">

                        {{-- Gambar utama --}}
                        <div class="shrink-0 md:w-64">
                            @if ($listing->photo_url)
                                <img src="{{ $listing->photo_url }}"
                                     alt="{{ $listing->title }}"
                                     class="h-48 w-full rounded-lg object-cover md:h-44">
                            @else
                                <div class="flex h-48 w-full items-center justify-center rounded-lg bg-slate-200 text-sm text-slate-400 md:h-44">
                                    Tidak ada foto
                                </div>
                            @endif
                        </div>

                        {{-- Info iklan --}}
                        <div class="min-w-0 flex-1">
                            <div class="text-xs text-slate-400">Iklan</div>
                            <div class="mt-1 text-lg font-bold">{{ $listing->title }}</div>

                            <div class="mt-2 text-sm text-slate-600">
                                Harga: Rp {{ number_format($listing->price, 0, ',', '.') }}
                            </div>
                            <div class="mt-1 text-sm text-slate-600">
                                Pemilik: {{ $report->reportedUser?->name ?? $listing->user?->name ?? '-' }}
                            </div>
                            <div class="mt-1 text-sm text-slate-600">
                                Diunggah: {{ $listing->created_at?->format('d M Y, H:i') }}
                                <span class="text-slate-400">({{ $listing->created_at?->diffForHumans() }})</span>
                            </div>
                            <div class="mt-1 text-sm text-slate-600">
                                Total laporan untuk iklan ini:
                                <span class="font-semibold {{ $sameListingReports > 1 ? 'text-red-600' : '' }}">{{ $sameListingReports }}</span>
                            </div>
                            <div class="mt-1 text-sm text-slate-600">
                                Status iklan:
                                @if ($listing->trashed())
                                    <span class="font-semibold text-red-600">Diturunkan</span>
                                @else
                                    {{ $listing->status }}
                                @endif
                            </div>

                            @unless ($listing->trashed())
                                <a href="{{ route('listings.show', $listing->id) }}" target="_blank"
                                   class="mt-3 inline-block text-sm font-semibold text-blue-600 hover:underline">
                                    Buka halaman iklan 
                                </a>
                            @endunless
                        </div>
                    </div>

                    {{-- Deskripsi barang (full width) --}}
                    <div class="mt-4 rounded-xl bg-slate-50 p-4">
                        <div class="text-[10px] font-bold tracking-widest text-slate-500">DESKRIPSI BARANG</div>
                        <p class="mt-1 max-h-40 overflow-y-auto whitespace-pre-line text-sm text-slate-700">{{ $listing->description ?: 'Tidak ada deskripsi.' }}</p>
                    </div>

                    {{-- Galeri foto lainnya --}}
                    @if ($listing->images->count() > 1)
                        <div class="mt-4">
                            <div class="mb-2 text-[10px] font-bold tracking-widest text-slate-500">
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
                        <div class="text-xs text-slate-400">Pengguna</div>
                        <div class="mt-1 font-bold">{{ $report->reportedUser->name }}</div>
                        <div class="text-sm text-slate-500">{{ $report->reportedUser->email }}</div>
                    </div>
                @else
                    <p class="text-sm text-slate-400">Data objek yang dilaporkan tidak tersedia.</p>
                @endif
            </section>

            {{-- RIWAYAT PENANGANAN --}}
            @if ($report->resolved_by)
                <section class="rounded-2xl border bg-white p-6">
                    <h2 class="mb-3 text-sm font-bold">RIWAYAT PENANGANAN</h2>
                    <p class="text-sm text-slate-600">Ditangani oleh: {{ $report->resolver?->name ?? '-' }}</p>
                    <p class="mt-1 text-sm text-slate-600">
                        Waktu penanganan: {{ $report->resolved_at?->format('d M Y, H:i') ?? '-' }}
                    </p>
                </section>
            @endif

            
            {{-- AKSI MODERASI --}}
            @if ($report->status === 'pending')
                <section class="rounded-2xl border bg-white p-6">
                    <h2 class="text-lg font-bold">Tindakan Moderasi</h2>
                    <p class="mt-1 text-sm text-slate-500">Pilih tindakan berdasarkan hasil pemeriksaan laporan.</p>

                    <div class="mt-5 flex flex-wrap gap-3">
                        <button type="button" onclick="openModerationModal('ignore')"
                                class="rounded-xl border border-slate-300 px-5 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-100">
                            Abaikan Laporan
                        </button>

                        @if ($listing && !$listing->trashed())
                            <button type="button" onclick="openModerationModal('take-down')"
                                    class="rounded-xl bg-red-600 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-red-700">
                                Turunkan Iklan
                            </button>
                        @endif
                    </div>
                </section>

                {{-- MODAL KONFIRMASI --}}
                <div id="moderationModal"
                    class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/50 p-4 backdrop-blur-sm">

                    <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-2xl">
                        <div id="modalIcon"
                            class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-blue-100 text-2xl text-blue-600">
                            ?
                        </div>

                        <h3 id="modalTitle" class="mt-4 text-center text-xl font-bold text-slate-800">
                            Konfirmasi Tindakan
                        </h3>

                        <p id="modalMessage" class="mt-2 text-center text-sm leading-relaxed text-slate-500">
                            Apakah kamu yakin ingin melanjutkan tindakan ini?
                        </p>

                        <div class="mt-6 flex justify-center gap-3">
                            <button type="button" onclick="closeModerationModal()"
                                    class="rounded-xl border border-slate-300 px-5 py-2.5 text-sm font-semibold text-slate-600 transition hover:bg-slate-100">
                                Batal
                            </button>

                            <form id="moderationForm" method="POST">
                                @csrf
                                <button id="modalConfirmButton" type="submit"
                                        class="rounded-xl bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-700">
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
                            confirmButton.className = 'rounded-xl bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-700';
                        } else {
                            form.action = "{{ route('admin.reports.take_down', $report) }}";
                            title.textContent = 'Turunkan Iklan?';
                            message.textContent = 'Apakah kamu yakin ingin menurunkan iklan ini? Iklan tidak akan tampil lagi di halaman publik.';
                            icon.textContent = '!';
                            icon.className = 'mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-red-100 text-2xl font-bold text-red-600';
                            confirmButton.textContent = 'Ya, Turunkan';
                            confirmButton.className = 'rounded-xl bg-red-600 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-red-700';
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