<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>ADMIN - User Verifikasi | Lungsur.in</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>[x-cloak]{display:none !important;}</style>
</head>
<body class="bg-slate-100 text-slate-800 antialiased">
    <x-flash-message />
@php
    $admin = auth()->user();
    $adminRoleLabel = match ($admin->role) {
        'admin' => 'Administrator',
        default => ucfirst($admin->role),
    };
    $adminInitial = strtoupper(mb_substr($admin->name, 0, 1));
@endphp

<div class="flex min-h-screen"
     x-data="{ imageOpen: false, rejectOpen: false, profileOpen: false, reason: '' }">

    {{-- ===== SIDEBAR ===== --}}
    <aside class="hidden lg:flex w-60 shrink-0 flex-col bg-[#1c2541] text-slate-300">
        <div class="px-6 py-6">
            <div class="text-xl font-extrabold text-white">Lungsur.in</div>
            <div class="text-[10px] font-semibold tracking-widest text-slate-400">STAFF OPERATIONS</div>
        </div>

        <div class="px-6 pb-2 pt-4 text-[10px] font-bold tracking-widest text-slate-500">PORTAL MODERASI</div>
        <nav class="space-y-1 px-3 text-sm">
            <a href="{{ route('admin.verifications.index') }}" class="flex items-center gap-3 rounded-lg px-3 py-2.5 hover:bg-white/5">
                <span>▦</span> Dashboard
            </a>
            <a href="{{ route('admin.verifications.index') }}"
               class="flex items-center justify-between rounded-xl bg-blue-600 px-3 py-2.5 font-semibold text-white">
                <span class="flex items-center gap-3"><span>🛡</span> User Verification</span>
                @if ($stats['pending'] > 0)
                    <span class="rounded-full bg-red-500 px-2 py-0.5 text-[11px]">{{ $stats['pending'] }}</span>
                @endif
            </a>
            <a href="#" class="flex items-center gap-3 rounded-lg px-3 py-2.5 hover:bg-white/5"><span>▤</span> Content Moderation</a>
            <a href="#" class="flex items-center gap-3 rounded-lg px-3 py-2.5 hover:bg-white/5"><span>⚑</span> Reported Items</a>
            <a href="#" class="flex items-center gap-3 rounded-lg px-3 py-2.5 hover:bg-white/5"><span>☰</span> System Logs</a>
            <a href="#" class="flex items-center gap-3 rounded-lg px-3 py-2.5 hover:bg-white/5"><span>⚙</span> Admin Settings</a>
        </nav>

        {{-- Info admin + logout --}}
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

        {{-- Topbar --}}
        <header class="flex items-center justify-between gap-4 border-b bg-white px-6 py-3">
            <div class="w-full max-w-md rounded-full bg-slate-100 px-4 py-2 text-sm text-slate-400">
                Cari nama, NIM, atau email mahasiswa...
            </div>

            {{-- Profil admin (dropdown) --}}
            <div class="relative" @click.outside="profileOpen = false" @keydown.escape.window="profileOpen = false">
                <button type="button" @click="profileOpen = !profileOpen"
                        class="flex items-center gap-3 rounded-full py-1 pl-3 pr-1 text-right transition hover:bg-slate-100">
                    <div class="hidden leading-tight sm:block">
                        <div class="text-sm font-semibold">{{ $admin->name }}</div>
                        <div class="text-[11px] font-medium text-blue-600">Campus Trust &amp; Safety</div>
                    </div>
                    <div class="flex h-9 w-9 items-center justify-center rounded-full bg-blue-600 text-sm font-bold text-white">
                        {{ $adminInitial }}
                    </div>
                </button>

                <div x-show="profileOpen" x-cloak x-transition
                     class="absolute right-0 z-40 mt-2 w-60 overflow-hidden rounded-xl border bg-white shadow-lg">
                    <div class="border-b px-4 py-3">
                        <div class="text-sm font-semibold">{{ $admin->name }}</div>
                        <div class="truncate text-xs text-slate-500">{{ $admin->email }}</div>
                        <div class="mt-1 inline-block rounded-full bg-blue-50 px-2 py-0.5 text-[10px] font-semibold text-blue-700">
                            {{ $adminRoleLabel }}
                        </div>
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                                class="flex w-full items-center gap-2 px-4 py-3 text-left text-sm font-medium text-red-600 hover:bg-red-50">
                            ⎋ Keluar
                        </button>
                    </form>
                </div>
            </div>
        </header>

        <main class="space-y-6 p-6">

            {{-- Judul --}}
            <div class="flex flex-wrap items-start justify-between gap-4">
                <div>
                    <div class="text-[11px] font-bold tracking-widest text-emerald-600">● CAMPUS TRUST &amp; SAFETY MODULE</div>
                    <h1 class="mt-1 text-3xl font-extrabold leading-tight">Verifikasi Identitas<br>Mahasiswa (KTM)</h1>
                    <p class="mt-2 max-w-xl text-sm text-slate-500">
                        Pendaftar dengan email kampus (.ac.id) otomatis lolos. Halaman ini untuk meninjau
                        KTM pendaftar yang memakai email non-kampus agar hanya mahasiswa asli
                        yang bisa berjualan dan bertransaksi di Lungsur.in.
                    </p>
                </div>
                <a href="{{ route('admin.verifications.index') }}"
                   class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">
                    ⟳ Refresh Antrean
                </a>
            </div>

            {{-- Flash --}}
            @if (session('success'))
                <div class="rounded-xl bg-emerald-100 px-4 py-3 text-sm text-emerald-800">{{ session('success') }}</div>
            @endif
            @if ($errors->any())
                <div class="rounded-xl bg-red-100 px-4 py-3 text-sm text-red-800">{{ $errors->first() }}</div>
            @endif

            {{-- Statistik --}}
            <div class="grid grid-cols-2 gap-4 xl:grid-cols-4">
                @php
                    $cards = [
                        ['label' => 'ANTREAN KTM', 'value' => $stats['pending'], 'sub' => 'Menunggu verifikasi', 'color' => 'text-blue-600'],
                        ['label' => 'DISETUJUI HARI INI', 'value' => $stats['approved_today'], 'sub' => 'Akun diaktifkan', 'color' => 'text-emerald-600'],
                        ['label' => 'DITOLAK HARI INI', 'value' => $stats['rejected_today'], 'sub' => 'Perlu daftar ulang', 'color' => 'text-red-600'],
                        ['label' => 'TOTAL TERVERIFIKASI', 'value' => $stats['total_verified'], 'sub' => 'Semua waktu', 'color' => 'text-slate-700'],
                    ];
                @endphp
                @foreach ($cards as $c)
                    <div class="rounded-2xl border bg-white p-4">
                        <div class="text-[10px] font-bold tracking-wider text-slate-500">{{ $c['label'] }}</div>
                        <div class="mt-1 text-3xl font-extrabold {{ $c['color'] }}">{{ $c['value'] }}</div>
                        <div class="mt-1 text-xs text-slate-400">{{ $c['sub'] }}</div>
                    </div>
                @endforeach
            </div>

            <div class="grid gap-6 xl:grid-cols-3">

                {{-- ===== KOLOM KIRI: DETAIL + ANTREAN ===== --}}
                <div class="space-y-6 xl:col-span-2">

                    @if ($selected)
                        @php
                            $isCampusEmail = str_ends_with(strtolower($selected->email), '.ac.id');
                            $checks = [
                                [
                                    'ok' => filled($selected->nim),
                                    'label_ok' => 'NIM terisi',
                                    'label_fail' => 'NIM belum diisi',
                                ],
                                [
                                    'ok' => filled($selected->ktm_image_path),
                                    'label_ok' => 'Foto KTM tersedia',
                                    'label_fail' => 'Foto KTM belum diunggah',
                                ],
                                [
                                    'ok' => !$isCampusEmail,
                                    'label_ok' => 'Email non-kampus (butuh verifikasi KTM)',
                                    'label_fail' => 'Email kampus (.ac.id) seharusnya lolos otomatis',
                                ],
                            ];
                            $allOk = collect($checks)->every(fn ($c) => $c['ok']);
                        @endphp

                        <section class="rounded-2xl border bg-white p-5">
                            {{-- Header pendaftar --}}
                            <div class="flex flex-wrap items-center justify-between gap-3">
                                <div class="flex items-center gap-3">
                                    <div class="flex h-12 w-12 items-center justify-center rounded-full bg-blue-100 text-base font-bold text-blue-700">
                                        {{ strtoupper(mb_substr($selected->name, 0, 2)) }}
                                    </div>
                                    <div>
                                        <div class="text-lg font-bold">{{ $selected->name }}</div>
                                        <div class="text-xs text-slate-500">
                                            Mendaftar {{ $selected->created_at?->diffForHumans() }}
                                            · {{ $selected->created_at?->format('d M Y, H:i') }} WIB
                                        </div>
                                    </div>
                                </div>
                                <span class="rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-700">
                                    ⏳ Menunggu Verifikasi
                                </span>
                            </div>

                            <div class="mt-5 grid gap-5 md:grid-cols-2">
                                {{-- Foto KTM --}}
                                <div>
                                    <div class="mb-2 flex items-center justify-between">
                                        <span class="text-[10px] font-bold tracking-widest text-slate-500">PRATINJAU FOTO KTM</span>
                                        @if ($selected->ktm_image_path)
                                            <button type="button" @click="imageOpen = true"
                                                    class="text-xs font-semibold text-blue-600 hover:underline">
                                                🔍 Perbesar
                                            </button>
                                        @endif
                                    </div>
                                    <div class="flex min-h-56 items-center justify-center overflow-hidden rounded-xl bg-slate-100 p-2">
                                        @if ($selected->ktm_image_path)
                                            <img src="{{ asset('storage/' . $selected->ktm_image_path) }}"
                                                 alt="KTM {{ $selected->name }}"
                                                 @click="imageOpen = true"
                                                 class="max-h-72 w-full cursor-zoom-in rounded-lg object-contain">
                                        @else
                                            <span class="text-sm text-slate-400">Foto KTM belum diunggah</span>
                                        @endif
                                    </div>
                                </div>

                                {{-- Identitas + pengecekan --}}
                                <div class="space-y-4">
                                    <div>
                                        <div class="mb-2 text-[10px] font-bold tracking-widest text-slate-500">IDENTITAS PENDAFTAR</div>
                                        <dl class="space-y-2 rounded-xl bg-slate-50 p-4 text-sm">
                                            <div class="flex justify-between gap-3"><dt class="text-slate-500">Nama</dt><dd class="font-semibold text-right">{{ $selected->name }}</dd></div>
                                            <div class="flex justify-between gap-3"><dt class="text-slate-500">NIM</dt><dd class="font-semibold text-right">{{ $selected->nim ?? '-' }}</dd></div>
                                            <div class="flex justify-between gap-3"><dt class="text-slate-500">Email</dt><dd class="font-semibold text-right break-all">{{ $selected->email }}</dd></div>
                                            <div class="flex justify-between gap-3"><dt class="text-slate-500">Tipe email</dt><dd class="font-semibold text-right capitalize">{{ $selected->email_type }}</dd></div>
                                            <div class="flex justify-between gap-3"><dt class="text-slate-500">Kampus</dt><dd class="font-semibold text-right">{{ $selected->campus ?? '-' }}</dd></div>
                                            <div class="flex justify-between gap-3"><dt class="text-slate-500">No. HP</dt><dd class="font-semibold text-right">{{ $selected->phone ?? '-' }}</dd></div>
                                        </dl>
                                    </div>

                                    <div>
                                        <div class="mb-2 text-[10px] font-bold tracking-widest text-slate-500">PENGECEKAN OTOMATIS</div>
                                        <div class="rounded-xl p-4 {{ $allOk ? 'bg-emerald-50' : 'bg-amber-50' }}">
                                            <ul class="space-y-1.5 text-sm">
                                                @foreach ($checks as $c)
                                                    <li class="flex items-center gap-2 {{ $c['ok'] ? 'text-emerald-700' : 'text-amber-700' }}">
                                                        <span class="font-bold">{{ $c['ok'] ? '✓' : '!' }}</span>
                                                        {{ $c['ok'] ? $c['label_ok'] : $c['label_fail'] }}
                                                    </li>
                                                @endforeach
                                            </ul>
                                            <p class="mt-3 text-[11px] text-slate-500">
                                                Cocokkan manual nama &amp; NIM di KTM dengan data di atas sebelum menyetujui.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Aksi --}}
                            <div class="mt-5 flex flex-wrap items-center justify-between gap-3 border-t pt-4">
                                <p class="text-xs text-slate-500">
                                    ⓘ Setelah disetujui, akun langsung aktif dan bisa bertransaksi.
                                </p>
                                <div class="flex gap-2">
                                    <button type="button" @click="rejectOpen = true"
                                            class="rounded-xl bg-red-50 px-5 py-2.5 text-sm font-semibold text-red-600 hover:bg-red-100">
                                        ✕ Tolak
                                    </button>
                                    <form method="POST" action="{{ route('admin.verifications.approve', $selected) }}">
                                        @csrf
                                        <button type="submit"
                                                class="rounded-xl bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-blue-700">
                                            ✓ Setujui &amp; Aktifkan Akun
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </section>
                    @else
                        <section class="rounded-2xl border bg-white p-12 text-center">
                            <div class="text-4xl">🎉</div>
                            <div class="mt-2 text-lg font-bold">Antrean kosong</div>
                            <p class="text-sm text-slate-500">Tidak ada pendaftar yang menunggu verifikasi KTM.</p>
                        </section>
                    @endif

                    {{-- Antrean lainnya --}}
                    @if ($queue->isNotEmpty())
                        <section>
                            <div class="mb-3 flex items-center justify-between">
                                <span class="text-[10px] font-bold tracking-widest text-slate-500">
                                    ANTREAN LAINNYA ({{ $queue->count() }} TERSISA)
                                </span>
                                <span class="text-xs font-semibold text-blue-600">Diurutkan berdasarkan waktu masuk</span>
                            </div>
                            <div class="grid gap-3 sm:grid-cols-2">
                                @foreach ($queue as $u)
                                    <a href="{{ route('admin.verifications.index', ['user' => $u->id]) }}"
                                       class="rounded-2xl border bg-white p-4 transition hover:border-blue-400 hover:shadow">
                                        <div class="font-semibold">{{ $u->name }}</div>
                                        <div class="text-xs text-slate-500">NIM {{ $u->nim ?? '-' }} · {{ $u->campus ?? '-' }}</div>
                                        <div class="mt-2 flex items-center justify-between text-xs">
                                            <span class="truncate text-slate-400">{{ $u->email }}</span>
                                            <span class="ml-2 shrink-0 text-slate-500">{{ $u->created_at?->diffForHumans(null, true) }}</span>
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        </section>
                    @endif
                </div>

                {{-- ===== KOLOM KANAN: RIWAYAT ===== --}}
                <aside class="rounded-2xl border bg-white p-5">
                    <div class="mb-4 text-[10px] font-bold tracking-widest text-slate-500">RIWAYAT VERIFIKASI TERBARU</div>
                    <ul class="space-y-3">
                        @forelse ($recent as $r)
                            <li class="flex items-start justify-between gap-3 rounded-xl bg-slate-50 p-3">
                                <div class="min-w-0">
                                    <div class="truncate text-sm font-semibold">{{ $r->name }}</div>
                                    <div class="text-[11px] text-slate-500">{{ $r->verified_at?->diffForHumans() }}</div>
                                    @if ($r->verification_status === 'rejected' && $r->rejection_reason)
                                        <div class="mt-1 text-[11px] text-red-600">{{ $r->rejection_reason }}</div>
                                    @endif
                                </div>
                                @if ($r->verification_status === 'active')
                                    <span class="shrink-0 rounded-full bg-emerald-100 px-2 py-0.5 text-[11px] font-semibold text-emerald-700">Disetujui</span>
                                @else
                                    <span class="shrink-0 rounded-full bg-red-100 px-2 py-0.5 text-[11px] font-semibold text-red-700">Ditolak</span>
                                @endif
                            </li>
                        @empty
                            <li class="text-sm text-slate-400">Belum ada riwayat verifikasi.</li>
                        @endforelse
                    </ul>
                </aside>
            </div>
        </main>
    </div>

    {{-- ===== MODAL: PERBESAR KTM ===== --}}
    @if ($selected && $selected->ktm_image_path)
        <div x-show="imageOpen" x-cloak
             class="fixed inset-0 z-50 flex items-center justify-center bg-black/80 p-4"
             @click.self="imageOpen = false" @keydown.escape.window="imageOpen = false">
            <div class="relative w-full max-w-4xl rounded-2xl bg-white p-4">
                <button type="button" @click="imageOpen = false"
                        class="absolute right-3 top-2 text-3xl leading-none text-slate-400 hover:text-black">&times;</button>
                <img src="{{ asset('storage/' . $selected->ktm_image_path) }}" alt="KTM"
                     class="mx-auto max-h-[80vh] rounded-lg">
            </div>
        </div>
    @endif

    {{-- ===== MODAL: ALASAN PENOLAKAN ===== --}}
    @if ($selected)
        <div x-show="rejectOpen" x-cloak
             class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 p-4"
             @click.self="rejectOpen = false" @keydown.escape.window="rejectOpen = false">
            <form method="POST" action="{{ route('admin.verifications.reject', $selected) }}"
                  class="w-full max-w-md rounded-2xl bg-white p-6">
                @csrf
                <h2 class="text-lg font-bold">Tolak pendaftaran</h2>
                <p class="mb-4 text-sm text-slate-500">Akun: {{ $selected->name }}</p>

                <div class="mb-3 flex flex-wrap gap-2">
                    @foreach (['Foto KTM buram / tidak terbaca', 'NIM tidak sesuai dengan KTM', 'Nama tidak sesuai dengan KTM', 'Bukan KTM yang valid'] as $chip)
                        <button type="button" @click="reason = @js($chip)"
                                class="rounded-full bg-slate-100 px-3 py-1 text-xs hover:bg-slate-200">
                            {{ $chip }}
                        </button>
                    @endforeach
                </div>

                <label for="rejection_reason" class="mb-1 block text-sm font-medium">Alasan penolakan</label>
                <textarea id="rejection_reason" name="rejection_reason" rows="3" required maxlength="255"
                          x-model="reason"
                          class="mb-4 w-full rounded-lg border border-slate-300 p-2 text-sm"
                          placeholder="Tulis alasan atau pilih salah satu di atas"></textarea>

                <div class="flex justify-end gap-2">
                    <button type="button" @click="rejectOpen = false"
                            class="rounded-lg px-4 py-2 text-sm text-slate-600 hover:bg-slate-100">Batal</button>
                    <button type="submit"
                            class="rounded-lg bg-red-600 px-4 py-2 text-sm font-semibold text-white hover:bg-red-700">
                        Tolak Pendaftaran
                    </button>
                </div>
            </form>
        </div>
    @endif
</div>
</body>
</html>