@props(['active' => ''])

@php
    $admin = auth()->user();
    $adminRoleLabel = match ($admin?->role) {
        'admin' => 'Administrator',
        default => ucfirst($admin?->role ?? 'Staff'),
    };
    $adminInitial = strtoupper(mb_substr($admin?->name ?? 'A', 0, 1));

    $pendingKtmCount = \App\Models\User::where('verification_status', 'pending')->count();
    $pendingBoostCount = \App\Models\BoostPayment::where('status', 'pending')->count();
    $pendingAuctionCount = \App\Models\AuctionPayment::where('status', 'pending')->count();
    $pendingReportsCount = \App\Models\Report::where('status', 'pending')->count();
@endphp

<aside class="hidden lg:flex w-60 shrink-0 flex-col bg-[#1c2541] text-slate-300 border-r border-slate-800/80 sticky top-0 h-screen no-print print:hidden">
    <!-- Brand -->
    <div class="px-6 py-6 border-b border-white/5">
        <a href="{{ route('admin.dashboard') }}" class="block">
            <div class="text-xl font-extrabold text-white tracking-tight">Lungsur.in</div>
            <div class="text-[10px] font-semibold tracking-widest text-slate-400 uppercase">STAFF OPERATIONS</div>
        </a>
    </div>

    <!-- Navigation -->
    <div class="flex-1 overflow-y-auto px-3 py-4 space-y-4">
        <!-- Section: Portal Moderasi -->
        <div>
            <div class="px-3 pb-2 text-[10px] font-bold tracking-widest text-slate-400 uppercase">PORTAL MODERASI</div>
            <nav class="space-y-1 text-sm font-medium">
                <!-- Dashboard -->
                <a href="{{ route('admin.dashboard') }}"
                   class="flex items-center gap-3 rounded-xl px-3 py-2.5 transition {{ $active === 'dashboard' ? 'bg-blue-600 font-semibold text-white shadow-sm' : 'text-slate-300 hover:bg-white/5 hover:text-white' }}">
                    <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                    </svg>
                    <span>Dashboard</span>
                </a>

                <!-- User Verification -->
                <a href="{{ route('admin.verifications.index') }}"
                   class="flex items-center justify-between rounded-xl px-3 py-2.5 transition {{ $active === 'verifications' ? 'bg-blue-600 font-semibold text-white shadow-sm' : 'text-slate-300 hover:bg-white/5 hover:text-white' }}">
                    <span class="flex items-center gap-3">
                        <span class="text-base leading-none">🛡</span>
                        <span>User Verification</span>
                    </span>
                    @if ($pendingKtmCount > 0)
                        <span class="rounded-full bg-red-500 px-2 py-0.5 text-[11px] font-bold text-white shadow-xs">{{ $pendingKtmCount }}</span>
                    @endif
                </a>
            </nav>
        </div>

        <!-- Section: Monetisasi -->
        <div>
            <div class="px-3 pb-2 text-[10px] font-bold tracking-widest text-slate-400 uppercase">MONETISASI</div>
            <nav class="space-y-1 text-sm font-medium">
                <!-- Boost Iklan -->
                <a href="{{ route('admin.payments.boost') }}"
                   class="flex items-center justify-between rounded-xl px-3 py-2.5 transition {{ $active === 'payments.boost' ? 'bg-blue-600 font-semibold text-white shadow-sm' : 'text-slate-300 hover:bg-white/5 hover:text-white' }}">
                    <span class="flex items-center gap-3">
                        <span class="text-amber-400 leading-none">⚡</span>
                        <span>Verifikasi Boost Iklan</span>
                    </span>
                    @if ($pendingBoostCount > 0)
                        <span class="rounded-full bg-amber-500 text-slate-950 font-bold px-2 py-0.5 text-[11px]">{{ $pendingBoostCount }}</span>
                    @endif
                </a>

                <!-- Biaya Lelang -->
                <a href="{{ route('admin.payments.auction') }}"
                   class="flex items-center justify-between rounded-xl px-3 py-2.5 transition {{ $active === 'payments.auction' ? 'bg-blue-600 font-semibold text-white shadow-sm' : 'text-slate-300 hover:bg-white/5 hover:text-white' }}">
                    <span class="flex items-center gap-3">
                        <span class="text-sky-300 leading-none">🔨</span>
                        <span>Verifikasi Biaya Lelang</span>
                    </span>
                    @if ($pendingAuctionCount > 0)
                        <span class="rounded-full bg-emerald-500 text-white font-bold px-2 py-0.5 text-[11px]">{{ $pendingAuctionCount }}</span>
                    @endif
                </a>
            </nav>
        </div>

        <!-- Section: Sistem & Aduan -->
        <div>
            <div class="px-3 pb-2 text-[10px] font-bold tracking-widest text-slate-400 uppercase">SISTEM & ADUAN</div>
            <nav class="space-y-1 text-sm font-medium">
                <!-- Reported Items -->
                <a href="{{ route('admin.reports.index') }}"
                   class="flex items-center justify-between rounded-xl px-3 py-2.5 transition {{ $active === 'reports' ? 'bg-blue-600 font-semibold text-white shadow-sm' : 'text-slate-300 hover:bg-white/5 hover:text-white' }}">
                    <span class="flex items-center gap-3">
                        <span class="text-rose-400 leading-none">🚩</span>
                        <span>Reported Items</span>
                    </span>
                    @if ($pendingReportsCount > 0)
                        <span class="rounded-full bg-rose-500 px-2 py-0.5 text-[11px] font-bold text-white shadow-xs">{{ $pendingReportsCount }}</span>
                    @endif
                </a>
            </nav>
        </div>
    </div>

    <!-- Bottom User Profile Card -->
    <div class="mt-auto p-4 border-t border-white/5">
        <div class="rounded-2xl bg-white/5 p-3 text-xs">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-blue-600 font-bold text-white shadow-sm">
                    {{ $adminInitial }}
                </div>
                <div class="min-w-0 flex-1">
                    <div class="truncate font-bold text-white">{{ $admin?->name ?? 'Admin Lungsur.in' }}</div>
                    <div class="text-[11px] text-slate-400">{{ $adminRoleLabel }}</div>
                </div>
            </div>
            <div class="mt-2 truncate text-[11px] text-slate-400">{{ $admin?->email ?? 'admin@lungsurin.test' }}</div>

            <form method="POST" action="{{ route('logout') }}" class="mt-3">
                @csrf
                <button type="submit"
                        class="w-full flex items-center justify-center gap-2 rounded-xl bg-white/10 py-2 font-semibold text-slate-300 hover:bg-white/20 hover:text-white transition cursor-pointer">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                    Keluar
                </button>
            </form>
        </div>
        <div class="mt-3 text-center text-[10px] text-slate-500">© 2026 Lungsur Campus</div>
    </div>
</aside>
