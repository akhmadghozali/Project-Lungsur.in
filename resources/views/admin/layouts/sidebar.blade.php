@php
    $admin = $admin ?? auth()->user();

    $adminRoleLabel = $adminRoleLabel ?? match ($admin->role) {
        'admin' => 'Administrator',
        default => ucfirst($admin->role),
    };

    $adminInitial = $adminInitial ?? strtoupper(mb_substr($admin->name, 0, 1));

    // Jumlah antrean verifikasi mahasiswa
    $pendingVerificationCount = \App\Models\User::where('verification_status', 'pending')->count();

    // Jumlah pembayaran pending
    $pendingBoostCount = \App\Models\BoostPayment::where('status', 'pending')->count();
    $pendingAuctionCount = \App\Models\AuctionPayment::where('status', 'pending')->count();

    // Jumlah laporan yang belum ditangani
    $pendingListingReports = \App\Models\Report::where('report_type', 'listing')
        ->where('status', 'pending')
        ->count();

    $pendingAccountReports = \App\Models\Report::where('report_type', 'account')
        ->where('status', 'pending')
        ->count();

    // Status menu aktif
    $isVerificationActive = request()->routeIs('admin.verifications.*');
    $isBoostActive = request()->routeIs('admin.payments.boost', 'admin.payments.boost.*');
    $isAuctionActive = request()->routeIs('admin.payments.auction', 'admin.payments.auction.*');
    $isListingReportActive = request()->routeIs('admin.reports.*');
    $isAccountReportActive = request()->routeIs('admin.account_reports.*');
@endphp

<style>
    /* SIDEBAR UTAMA */
    .admin-sidebar {
        position: sticky;
        top: 0;
        height: 100vh;
        height: 100dvh;
        overflow: hidden;
    }

    /* Tidak ada scroll pada navigasi */
    .admin-sidebar-nav {
        min-height: 0;
        overflow: hidden !important;
    }

    .admin-sidebar-brand,
    .admin-sidebar-profile {
        flex-shrink: 0;
    }

    .admin-section-title {
        margin-bottom: 12px;
    }

    .admin-primary-link {
        padding-top: 12px;
        padding-bottom: 12px;
    }

    /* Layar dengan tinggi terbatas */
    @media screen and (max-height: 760px) {
        .admin-sidebar-nav {
            padding-top: 14px !important;
            padding-bottom: 14px !important;
        }

        .admin-sidebar-nav > :not([hidden]) ~ :not([hidden]) {
            margin-top: 13px !important;
        }

        .admin-section-title {
            margin-bottom: 6px !important;
        }

        .admin-primary-link {
            padding-top: 7px !important;
            padding-bottom: 7px !important;
        }

        .admin-sidebar-profile {
            padding: 10px !important;
        }

        .admin-sidebar-profile > div:first-child {
            margin-bottom: 7px !important;
        }

        .admin-sidebar-profile form button {
            padding-top: 7px !important;
            padding-bottom: 7px !important;
        }

        .admin-sidebar-profile > p {
            margin-top: 7px !important;
        }
    }

    /* Layar sangat pendek */
    @media screen and (max-height: 640px) {
        .admin-sidebar-nav {
            padding-top: 8px !important;
            padding-bottom: 8px !important;
        }

        .admin-sidebar-nav > :not([hidden]) ~ :not([hidden]) {
            margin-top: 8px !important;
        }

        .admin-section-title {
            margin-bottom: 4px !important;
        }

        .admin-primary-link {
            padding-top: 5px !important;
            padding-bottom: 5px !important;
        }

        .admin-sidebar-profile {
            padding: 7px !important;
        }

        .admin-sidebar-profile > div:first-child {
            margin-bottom: 4px !important;
        }

        .admin-sidebar-profile > p {
            margin-top: 4px !important;
        }
    }
</style>

{{-- SIDEBAR UTAMA --}}
<aside class="admin-sidebar sticky top-0 hidden h-screen w-60 shrink-0 self-start flex-col overflow-hidden bg-[#1c2541] text-slate-300 lg:flex">

    {{-- LOGO --}}
    <div class="admin-sidebar-brand flex h-20 items-center border-b border-white/10 px-6">
        <div>
            <h1 class="text-xl font-extrabold tracking-tight text-white">
                Lungsur<span class="text-blue-400">.in</span>
            </h1>

            <p class="mt-1 text-[9px] font-bold uppercase tracking-[0.22em] text-slate-400">
                Staff Operations
            </p>
        </div>
    </div>

    {{-- NAVIGATION --}}
    <nav class="admin-sidebar-nav flex-1 space-y-7 px-4 py-6">

        {{-- PORTAL MODERASI --}}
        <div>
            <p class="admin-section-title px-3 text-[10px] font-bold uppercase tracking-[0.18em] text-slate-500">
                Portal Moderasi
            </p>

            <a href="{{ route('admin.verifications.index') }}"
               class="admin-primary-link group flex items-center justify-between rounded-xl px-3 text-sm font-medium transition
               {{ $isVerificationActive
                    ? 'bg-blue-600 text-white shadow-lg shadow-blue-900/20'
                    : 'text-slate-400 hover:bg-white/5 hover:text-white' }}">

                <span class="flex items-center gap-3">
                    <svg class="h-5 w-5 shrink-0 {{ $isVerificationActive ? 'text-white' : 'text-slate-400 group-hover:text-white' }}"
                         fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M9 12.75 11.25 15 15 9.75M12 3l8.25 4.5v5.25c0 4.5-3.75 7.5-8.25 9-4.5-1.5-8.25-4.5-8.25-9V7.5L12 3Z"/>
                    </svg>

                    <span>User Verification</span>
                </span>

                @if ($pendingVerificationCount > 0)
                    <span class="rounded-full bg-amber-400 px-2 py-0.5 text-[10px] font-bold text-slate-900">
                        {{ $pendingVerificationCount }}
                    </span>
                @endif
            </a>
        </div>

        {{-- MONETISASI --}}
        <div>
            <p class="admin-section-title px-3 text-[10px] font-bold uppercase tracking-[0.18em] text-slate-500">
                Monetisasi
            </p>

            {{-- BOOST PAYMENT --}}
            <a href="{{ route('admin.payments.boost') }}"
               class="admin-primary-link group mb-1 flex items-center justify-between rounded-xl px-3 text-sm font-medium transition
               {{ $isBoostActive
                    ? 'bg-blue-600 text-white shadow-lg shadow-blue-900/20'
                    : 'text-slate-400 hover:bg-white/5 hover:text-white' }}">

                <span class="flex items-center gap-3">
                    <svg class="h-5 w-5 shrink-0 {{ $isBoostActive ? 'text-white' : 'text-slate-400 group-hover:text-white' }}"
                         fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="m13 2-9 12h7l-1 8 10-13h-7l1-7Z"/>
                    </svg>

                    <span>Verifikasi Boost Iklan</span>
                </span>

                @if ($pendingBoostCount > 0)
                    <span class="rounded-full bg-amber-400 px-2 py-0.5 text-[10px] font-bold text-slate-900">
                        {{ $pendingBoostCount }}
                    </span>
                @endif
            </a>

            {{-- AUCTION PAYMENT --}}
            <a href="{{ route('admin.payments.auction') }}"
               class="admin-primary-link group flex items-center justify-between rounded-xl px-3 text-sm font-medium transition
               {{ $isAuctionActive
                    ? 'bg-blue-600 text-white shadow-lg shadow-blue-900/20'
                    : 'text-slate-400 hover:bg-white/5 hover:text-white' }}">

                <span class="flex items-center gap-3">
                    <svg class="h-5 w-5 shrink-0 {{ $isAuctionActive ? 'text-white' : 'text-slate-400 group-hover:text-white' }}"
                         fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <rect x="3" y="6" width="18" height="12" rx="2"/>
                        <circle cx="12" cy="12" r="2.5"/>
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M7 9h.01M17 15h.01"/>
                    </svg>

                    <span>Verifikasi Biaya Lelang</span>
                </span>

                @if ($pendingAuctionCount > 0)
                    <span class="rounded-full bg-emerald-400 px-2 py-0.5 text-[10px] font-bold text-slate-900">
                        {{ $pendingAuctionCount }}
                    </span>
                @endif
            </a>
        </div>

        {{-- SISTEM & ADUAN --}}
        <div>
            <p class="admin-section-title px-3 text-[10px] font-bold uppercase tracking-[0.18em] text-slate-500">
                Sistem & Aduan
            </p>

            {{-- LAPORAN IKLAN: MENU UTAMA --}}
            <a href="{{ route('admin.reports.index') }}"
               class="admin-primary-link group mb-1 flex items-center justify-between rounded-xl px-3 text-sm font-medium transition
               {{ $isListingReportActive
                    ? 'bg-blue-600 text-white shadow-lg shadow-blue-900/20'
                    : 'text-slate-400 hover:bg-white/5 hover:text-white' }}">

                <span class="flex items-center gap-3">
                    <svg class="h-5 w-5 shrink-0 {{ $isListingReportActive ? 'text-white' : 'text-slate-400 group-hover:text-white' }}"
                         fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M7.5 3.75h6l4.5 4.5v12H7.5a2.25 2.25 0 0 1-2.25-2.25V6A2.25 2.25 0 0 1 7.5 3.75Z"/>
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M13.5 3.75v4.5H18M9 12h6M9 15.75h6"/>
                    </svg>

                    <span>Laporan Iklan</span>
                </span>

                @if ($pendingListingReports > 0)
                    <span class="rounded-full bg-amber-400 px-2 py-0.5 text-[10px] font-bold text-slate-900">
                        {{ $pendingListingReports }}
                    </span>
                @endif
            </a>

            {{-- LAPORAN AKUN: MENU UTAMA --}}
            <a href="{{ route('admin.account_reports.index') }}"
               class="admin-primary-link group flex items-center justify-between rounded-xl px-3 text-sm font-medium transition
               {{ $isAccountReportActive
                    ? 'bg-blue-600 text-white shadow-lg shadow-blue-900/20'
                    : 'text-slate-400 hover:bg-white/5 hover:text-white' }}">

                <span class="flex items-center gap-3">
                    <svg class="h-5 w-5 shrink-0 {{ $isAccountReportActive ? 'text-white' : 'text-slate-400 group-hover:text-white' }}"
                         fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M15 19.5a6 6 0 0 0-12 0m12 0h6m-6 0a6 6 0 0 0-1.2-3.6M9 13.5a4.5 4.5 0 1 0 0-9 4.5 4.5 0 0 0 0 9Z"/>
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M18 8.25v4.5m-2.25-2.25h4.5"/>
                    </svg>

                    <span>Laporan Akun</span>
                </span>

                @if ($pendingAccountReports > 0)
                    <span class="rounded-full bg-rose-400 px-2 py-0.5 text-[10px] font-bold text-slate-900">
                        {{ $pendingAccountReports }}
                    </span>
                @endif
            </a>

            {{-- SYSTEM LOGS --}}
            <a href="#"
               class="group mt-1 flex items-center gap-3 rounded-xl px-3 py-3 text-sm font-medium text-slate-400 transition hover:bg-white/5 hover:text-white">

                <svg class="h-5 w-5 shrink-0 group-hover:text-white"
                     fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M4.5 4.5h15v15h-15v-15Z"/>
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M8 8h8M8 12h8M8 16h5"/>
                </svg>

                <span>System Logs</span>
            </a>

            {{-- ADMIN SETTINGS --}}
            <a href="#"
               class="group flex items-center gap-3 rounded-xl px-3 py-3 text-sm font-medium text-slate-400 transition hover:bg-white/5 hover:text-white">

                <svg class="h-5 w-5 shrink-0 group-hover:text-white"
                     fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M12 8.25a3.75 3.75 0 1 0 0 7.5 3.75 3.75 0 0 0 0-7.5Z"/>
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="m19.4 15 .1.1a1.8 1.8 0 0 1-2.55 2.55l-.1-.1a1.8 1.8 0 0 0-3.07 1.27v.18a1.8 1.8 0 0 1-3.6 0v-.15A1.8 1.8 0 0 0 7.1 17.6l-.1.1a1.8 1.8 0 0 1-2.55-2.55l.1-.1A1.8 1.8 0 0 0 3.28 12H3.1a1.8 1.8 0 0 1 0-3.6h.15A1.8 1.8 0 0 0 4.52 5.3l-.1-.1a1.8 1.8 0 0 1 2.55-2.55l.1.1a1.8 1.8 0 0 0 3.08-1.27V1.3a1.8 1.8 0 0 1 3.6 0v.15a1.8 1.8 0 0 0 3.08 1.27l.1-.1a1.8 1.8 0 0 1 2.55 2.55l.1.1A1.8 1.8 0 0 0 20.65 8h.15a1.8 1.8 0 0 1 0 3.6h-.15A1.8 1.8 0 0 0 19.4 15Z"/>
                </svg>

                <span>Admin Settings</span>
            </a>
        </div>
    </nav>

    {{-- ADMIN PROFILE --}}
    <div class="admin-sidebar-profile shrink-0 border-t border-white/10 p-4">

        <div class="mb-4 flex items-center gap-3 px-1">
            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-blue-500/20 text-sm font-bold text-blue-300 ring-1 ring-blue-400/30">
                {{ $adminInitial }}
            </div>

            <div class="min-w-0 flex-1">
                <p class="truncate text-sm font-semibold text-white">
                    {{ $admin->name }}
                </p>

                <p class="mt-0.5 text-[11px] text-slate-400">
                    {{ $adminRoleLabel }}
                </p>
            </div>
        </div>

        {{-- LOGOUT --}}
        <form action="{{ route('logout') }}" method="POST">
            @csrf

            <button type="submit"
                    class="group flex w-full items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium text-rose-300 transition hover:bg-rose-500/10 hover:text-rose-200">

                <svg class="h-5 w-5 shrink-0"
                     fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M15.75 8.25V5.5A1.75 1.75 0 0 0 14 3.75H6A1.75 1.75 0 0 0 4.25 5.5v13A1.75 1.75 0 0 0 6 20.25h8a1.75 1.75 0 0 0 1.75-1.75v-2.75"/>
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M10.5 12h9m0 0-3.75-3.75M19.5 12l-3.75 3.75"/>
                </svg>

                <span>Keluar</span>
            </button>
        </form>

        <p class="mt-4 text-center text-[9px] tracking-wide text-slate-600">
            &copy; {{ date('Y') }} Lungsur.in
        </p>
    </div>
</aside>
