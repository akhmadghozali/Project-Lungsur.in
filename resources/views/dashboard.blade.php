<x-layouts.app>
    <x-slot:title>
        Dasbor Mahasiswa - Lungsur.in
    </x-slot:title>

    <div class="space-y-6 sm:space-y-8 py-2 sm:py-6 max-w-7xl mx-auto">

        @if (session('mode_restriction'))
            @php $res = session('mode_restriction'); @endphp
            <div class="rounded-2xl border border-amber-300 bg-amber-50 p-4 sm:p-5 shadow-sm text-slate-800">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div class="flex items-start gap-3">
                        <div class="w-10 h-10 rounded-xl bg-amber-100 text-amber-800 flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-bold text-sm text-amber-950">Aksi Ini Khusus {{ $res['required_label'] }}</h3>
                            <p class="text-xs text-amber-900 mt-0.5">{{ $res['message'] }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 shrink-0">
                        <a href="{{ $res['target_url'] }}" 
                           class="min-h-[44px] px-4 py-2.5 rounded-xl bg-amber-700 hover:bg-amber-800 text-white font-bold text-xs shadow-xs transition inline-flex items-center gap-1.5">
                            <span>Beralih ke {{ $res['required_label'] }} &amp; Lanjutkan</span>
                            <span aria-hidden="true">&rarr;</span>
                        </a>
                    </div>
                </div>
            </div>
        @endif

        <div class="bg-gradient-to-r {{ $mode === 'seller' ? 'from-emerald-800 via-teal-900 to-slate-900' : 'from-blue-700 via-indigo-900 to-slate-900' }} rounded-3xl p-5 sm:p-8 text-white shadow-xl shadow-blue-500/10 relative overflow-hidden transition-colors duration-300">
            <div class="absolute -right-10 -bottom-10 w-64 h-64 rounded-full border border-white/10 pointer-events-none"></div>
            <div class="absolute -right-4 -bottom-4 w-48 h-48 rounded-full border border-white/15 pointer-events-none"></div>

            <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-5 sm:gap-6">
                <div class="space-y-2">
                    <div class="inline-flex items-center gap-2 bg-white/15 backdrop-blur-xs px-3.5 py-1 rounded-full text-xs font-semibold text-white">
                        <span class="w-2 h-2 rounded-full {{ $mode === 'seller' ? 'bg-emerald-300' : 'bg-blue-300' }}"></span>
                        <span>{{ $mode === 'seller' ? 'Mode Penjual Aktif' : 'Mode Pembeli Aktif' }}</span>
                        <span class="text-white/60">&bull;</span>
                        <span>{{ $user->campus ?? 'Mahasiswa Malang' }}</span>
                    </div>

                    <h1 class="text-2xl sm:text-3xl font-black tracking-tight">
                        Halo, {{ $user->name }}!
                    </h1>
                    <p class="text-xs sm:text-sm text-slate-100 max-w-xl leading-relaxed">
                        @if ($mode === 'seller')
                            Kelola dagangan perlengkapan kos, pantau tawaran masuk, dan pasang iklan barang baru untuk sesama mahasiswa Malang.
                        @else
                            Jelajahi perabot kos hemat, ikuti lelang, pantau wanted ads, dan kelola tawaran belanja COD kamu.
                        @endif
                    </p>
                </div>

                <div class="flex flex-wrap items-center gap-2.5 sm:gap-3 shrink-0">
                    <form method="POST" action="{{ route('user.switch_mode') }}" class="flex-1 sm:flex-initial">
                        @csrf
                        <input type="hidden" name="mode" value="{{ $mode === 'seller' ? 'buyer' : 'seller' }}">
                        <button type="submit"
                                class="w-full sm:w-auto min-h-[44px] px-4 sm:px-5 py-2.5 sm:py-3 rounded-2xl bg-white/15 hover:bg-white/25 text-white font-bold text-xs sm:text-sm transition border border-white/25 flex items-center justify-center gap-2 cursor-pointer shadow-xs focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-white">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                            </svg>
                            <span>Ganti ke {{ $mode === 'seller' ? 'Mode Pembeli' : 'Mode Penjual' }}</span>
                        </button>
                    </form>

                    @if ($mode === 'seller')
                        <a href="{{ route('katalog.pasang') }}"
                           class="flex-1 sm:flex-initial min-h-[44px] px-4 sm:px-5 py-2.5 sm:py-3 rounded-2xl bg-white text-emerald-800 hover:bg-emerald-50 font-black text-xs sm:text-sm shadow-md transition flex items-center justify-center gap-1.5 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-white">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                            <span>Pasang Iklan Baru</span>
                        </a>
                    @else
                        <a href="{{ route('wanted-ads.index') }}"
                           class="flex-1 sm:flex-initial min-h-[44px] px-4 sm:px-5 py-2.5 sm:py-3 rounded-2xl bg-white text-blue-700 hover:bg-blue-50 font-bold text-xs sm:text-sm shadow-md transition flex items-center justify-center gap-1.5 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-white">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            <span>Pasang Wanted Ad</span>
                        </a>
                    @endif
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-6">

            <div class="lg:col-span-1 bg-white border border-slate-200/80 rounded-3xl p-5 sm:p-6 shadow-xs space-y-4 sm:space-y-5">
                <div class="flex items-center gap-3.5 sm:gap-4">
                    <div class="w-12 h-12 sm:w-14 sm:h-14 rounded-2xl {{ $mode === 'seller' ? 'bg-emerald-700' : 'bg-blue-600' }} text-white font-black text-lg sm:text-xl flex items-center justify-center shadow-md shrink-0">
                        {{ strtoupper(substr($user->name, 0, 2)) }}
                    </div>
                    <div class="overflow-hidden">
                        <h3 class="font-bold text-slate-900 text-sm sm:text-base truncate">{{ $user->name }}</h3>
                        <p class="text-xs text-slate-600 font-medium truncate">{{ $user->email }}</p>
                        <div class="flex items-center gap-2 mt-1">
                            <span class="text-[10px] font-extrabold uppercase px-2 py-0.5 rounded-md bg-emerald-100 text-emerald-800">
                                {{ $user->verification_status === 'active' ? 'Akun Aktif' : $user->verification_status }}
                            </span>
                            <span class="text-[10px] font-extrabold uppercase px-2 py-0.5 rounded-md {{ $mode === 'seller' ? 'bg-emerald-50 text-emerald-800 border border-emerald-200' : 'bg-blue-50 text-blue-700 border border-blue-200' }}">
                                {{ $mode === 'seller' ? 'Penjual' : 'Pembeli' }}
                            </span>
                        </div>
                    </div>
                </div>

                <div class="border-t border-slate-100 pt-3.5 sm:pt-4 space-y-2.5 sm:space-y-3 text-xs">
                    <div class="flex justify-between py-1 border-b border-slate-50 gap-2">
                        <span class="text-slate-600 font-medium shrink-0">Asal Kampus</span>
                        <span class="font-bold text-slate-900 text-right truncate">{{ $user->campus ?? '-' }}</span>
                    </div>
                    <div class="flex justify-between py-1 border-b border-slate-50">
                        <span class="text-slate-600 font-medium">NIM Mahasiswa</span>
                        <span class="font-bold text-slate-900">{{ $user->nim ?? 'Belum diatur' }}</span>
                    </div>
                    <div class="flex justify-between py-1 border-b border-slate-50">
                        <span class="text-slate-600 font-medium">Jalur Validasi</span>
                        <span class="font-bold text-slate-900">
                            {{ $user->email_type === 'campus' ? 'Email Kampus Otomatis' : 'Verifikasi KTM Manual' }}
                        </span>
                    </div>
                    <div class="flex justify-between py-1">
                        <span class="text-slate-600 font-medium">Bergabung Sejak</span>
                        <span class="font-bold text-slate-900">{{ $user->created_at?->translatedFormat('d F Y') ?? '-' }}</span>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-2 grid grid-cols-3 gap-2.5 sm:gap-4">
                @if ($mode === 'seller')
                    <div class="bg-white border border-slate-200/80 rounded-2xl sm:rounded-3xl p-3.5 sm:p-6 shadow-xs flex flex-col justify-between">
                        <div>
                            <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-xl sm:rounded-2xl bg-emerald-50 text-emerald-700 flex items-center justify-center mb-2 sm:mb-3">
                                <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                </svg>
                            </div>
                            <div class="text-[11px] sm:text-xs font-bold text-slate-600 leading-tight">Total Iklan</div>
                            <div class="text-xl sm:text-2xl font-black text-slate-900 mt-1">{{ $myListingsCount }}</div>
                        </div>
                        <span class="hidden sm:block text-[11px] {{ $myListingsCount > 0 ? 'text-emerald-700' : 'text-slate-600' }} font-semibold mt-4">
                            {{ $myListingsCount > 0 ? $myListingsCount . ' iklan terdaftar' : 'Belum ada iklan' }}
                        </span>
                    </div>

                    <div class="bg-white border border-slate-200/80 rounded-2xl sm:rounded-3xl p-3.5 sm:p-6 shadow-xs flex flex-col justify-between">
                        <div>
                            <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-xl sm:rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center mb-2 sm:mb-3">
                                <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div class="text-[11px] sm:text-xs font-bold text-slate-600 leading-tight">Iklan Aktif</div>
                            <div class="text-xl sm:text-2xl font-black text-slate-900 mt-1">{{ $activeListingsCount }}</div>
                        </div>
                        <span class="hidden sm:block text-[11px] text-blue-700 font-semibold mt-4">Tersedia di katalog</span>
                    </div>

                    <div class="bg-white border border-slate-200/80 rounded-2xl sm:rounded-3xl p-3.5 sm:p-6 shadow-xs flex flex-col justify-between">
                        <div>
                            <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-xl sm:rounded-2xl bg-amber-50 text-amber-700 flex items-center justify-center mb-2 sm:mb-3">
                                <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                </svg>
                            </div>
                            <div class="text-[11px] sm:text-xs font-bold text-slate-600 leading-tight">Sesi Lelang</div>
                            <div class="text-xl sm:text-2xl font-black text-slate-900 mt-1">{{ $myAuctionsCount }}</div>
                        </div>
                        <span class="hidden sm:block text-[11px] text-amber-700 font-semibold mt-4">
                            {{ $myAuctionsCount > 0 ? $myAuctionsCount . ' sesi lelang' : 'Belum buka lelang' }}
                        </span>
                    </div>
                @else
                    <div class="bg-white border border-slate-200/80 rounded-2xl sm:rounded-3xl p-3.5 sm:p-6 shadow-xs flex flex-col justify-between">
                        <div>
                            <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-xl sm:rounded-2xl bg-amber-50 text-amber-700 flex items-center justify-center mb-2 sm:mb-3">
                                <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                </svg>
                            </div>
                            <div class="text-[11px] sm:text-xs font-bold text-slate-600 leading-tight">Lelang Diikuti</div>
                            <div class="text-xl sm:text-2xl font-black text-slate-900 mt-1">{{ $myBidsCount }}</div>
                        </div>
                        <span class="hidden sm:block text-[11px] {{ $myBidsCount > 0 ? 'text-amber-700' : 'text-slate-600' }} font-semibold mt-4">
                            {{ $myBidsCount > 0 ? $myBidsCount . ' sesi lelang' : 'Belum ikut lelang' }}
                        </span>
                    </div>

                    <div class="bg-white border border-slate-200/80 rounded-2xl sm:rounded-3xl p-3.5 sm:p-6 shadow-xs flex flex-col justify-between">
                        <div>
                            <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-xl sm:rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center mb-2 sm:mb-3">
                                <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                            </div>
                            <div class="text-[11px] sm:text-xs font-bold text-slate-600 leading-tight">Wanted Ads</div>
                            <div class="text-xl sm:text-2xl font-black text-slate-900 mt-1">{{ $myWantedAdsCount }}</div>
                        </div>
                        <span class="hidden sm:block text-[11px] text-blue-700 font-semibold mt-4">Barang yang kamu cari</span>
                    </div>

                    <div class="bg-white border border-slate-200/80 rounded-2xl sm:rounded-3xl p-3.5 sm:p-6 shadow-xs flex flex-col justify-between">
                        <div>
                            <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-xl sm:rounded-2xl bg-emerald-50 text-emerald-700 flex items-center justify-center mb-2 sm:mb-3">
                                <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div class="text-[11px] sm:text-xs font-bold text-slate-600 leading-tight">Deal Selesai</div>
                            <div class="text-xl sm:text-2xl font-black text-slate-900 mt-1">{{ $purchasedListings->count() + $wonAuctions->count() }}</div>
                        </div>
                        <span class="hidden sm:block text-[11px] text-emerald-700 font-semibold mt-4">
                            {{ ($purchasedListings->count() + $wonAuctions->count()) > 0 ? ($purchasedListings->count() + $wonAuctions->count()) . ' transaksi sukses' : 'Belum ada transaksi selesai' }}
                        </span>
                    </div>
                @endif
            </div>

        </div>

        <div x-data="{ 
            activeTab: '{{ $activeTab }}',
            showDeleteModal: false,
            deleteTitle: '',
            deleteAction: '',
            showOfferModal: false,
            offerWantedAdId: null,
            offerWantedTitle: '',
            offerBuyerName: '',
            openOfferModal(adId, adTitle, buyerName) {
                this.offerWantedAdId = adId;
                this.offerWantedTitle = adTitle;
                this.offerBuyerName = buyerName;
                this.showOfferModal = true;
                document.body.style.overflow = 'hidden';
            },
            closeOfferModal() {
                this.showOfferModal = false;
                document.body.style.overflow = '';
            },
            openDeleteModal(title, action) {
                this.deleteTitle = title;
                this.deleteAction = action;
                this.showDeleteModal = true;
                document.body.style.overflow = 'hidden';
            },
            closeDeleteModal() {
                this.showDeleteModal = false;
                document.body.style.overflow = '';
            },
            confirmDelete() {
                document.getElementById('hidden-delete-form').action = this.deleteAction;
                document.getElementById('hidden-delete-form').submit();
            }
        }" class="space-y-5 sm:space-y-6">

            <div class="flex items-center gap-2 border-b border-slate-200 overflow-x-auto no-scrollbar pb-1">
                @if ($mode === 'seller')
                    <button type="button" 
                            @click="activeTab = 'dagangan'"
                            :class="activeTab === 'dagangan' ? 'border-emerald-700 text-emerald-800 font-black bg-emerald-50/70' : 'border-transparent text-slate-600 hover:text-slate-900 hover:border-slate-300 font-bold'"
                            class="min-h-[44px] px-4 sm:px-5 py-2.5 sm:py-3 border-b-2 text-xs sm:text-sm rounded-t-xl transition flex items-center gap-2 cursor-pointer shrink-0 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-emerald-700">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                        <span>Barang Dagangan</span>
                        <span class="text-xs px-2 py-0.5 rounded-full bg-slate-200/80 text-slate-800">{{ $myListingsCount }}</span>
                    </button>

                    <button type="button" 
                            @click="activeTab = 'lelang'"
                            :class="activeTab === 'lelang' ? 'border-emerald-700 text-emerald-800 font-black bg-emerald-50/70' : 'border-transparent text-slate-600 hover:text-slate-900 hover:border-slate-300 font-bold'"
                            class="min-h-[44px] px-4 sm:px-5 py-2.5 sm:py-3 border-b-2 text-xs sm:text-sm rounded-t-xl transition flex items-center gap-2 cursor-pointer shrink-0 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-emerald-700">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        <span>Lelang Saya</span>
                        <span class="text-xs px-2 py-0.5 rounded-full bg-slate-200/80 text-slate-800">{{ $myAuctionsCount }}</span>
                    </button>

                    <button type="button" 
                            @click="activeTab = 'peluang'"
                            :class="activeTab === 'peluang' ? 'border-emerald-700 text-emerald-800 font-black bg-emerald-50/70' : 'border-transparent text-slate-600 hover:text-slate-900 hover:border-slate-300 font-bold'"
                            class="min-h-[44px] px-4 sm:px-5 py-2.5 sm:py-3 border-b-2 text-xs sm:text-sm rounded-t-xl transition flex items-center gap-2 cursor-pointer shrink-0 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-emerald-700">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        <span>Peluang Wanted Ads</span>
                        <span class="text-xs px-2 py-0.5 rounded-full bg-slate-200/80 text-slate-800">{{ $wantedOpportunities->count() }}</span>
                    </button>
                @else
                    <button type="button" 
                            @click="activeTab = 'lelang'"
                            :class="activeTab === 'lelang' ? 'border-blue-600 text-blue-700 font-black bg-blue-50/70' : 'border-transparent text-slate-600 hover:text-slate-900 hover:border-slate-300 font-bold'"
                            class="min-h-[44px] px-4 sm:px-5 py-2.5 sm:py-3 border-b-2 text-xs sm:text-sm rounded-t-xl transition flex items-center gap-2 cursor-pointer shrink-0 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        <span>Bidding Lelang Saya</span>
                        <span class="text-xs px-2 py-0.5 rounded-full bg-slate-200/80 text-slate-800">{{ $myBidsCount }}</span>
                    </button>

                    <button type="button" 
                            @click="activeTab = 'wanted'"
                            :class="activeTab === 'wanted' ? 'border-blue-600 text-blue-700 font-black bg-blue-50/70' : 'border-transparent text-slate-600 hover:text-slate-900 hover:border-slate-300 font-bold'"
                            class="min-h-[44px] px-4 sm:px-5 py-2.5 sm:py-3 border-b-2 text-xs sm:text-sm rounded-t-xl transition flex items-center gap-2 cursor-pointer shrink-0 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>Wanted Ads Saya</span>
                        <span class="text-xs px-2 py-0.5 rounded-full bg-slate-200/80 text-slate-800">{{ $myWantedAdsCount }}</span>
                    </button>

                    <button type="button" 
                            @click="activeTab = 'riwayat'"
                            :class="activeTab === 'riwayat' ? 'border-blue-600 text-blue-700 font-black bg-blue-50/70' : 'border-transparent text-slate-600 hover:text-slate-900 hover:border-slate-300 font-bold'"
                            class="min-h-[44px] px-4 sm:px-5 py-2.5 sm:py-3 border-b-2 text-xs sm:text-sm rounded-t-xl transition flex items-center gap-2 cursor-pointer shrink-0 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>Riwayat Pembelian</span>
                        <span class="text-xs px-2 py-0.5 rounded-full bg-slate-200/80 text-slate-800">{{ $purchasedListings->count() + $wonAuctions->count() }}</span>
                    </button>
                @endif
            </div>

            @if ($mode === 'seller')

                <div x-show="activeTab === 'dagangan'" class="bg-white border border-slate-200/80 rounded-3xl p-4 sm:p-6 shadow-xs space-y-5 sm:space-y-6">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3.5 border-b border-slate-100 pb-4">
                        <div>
                            <h2 class="text-base sm:text-lg font-black text-slate-900 tracking-tight flex items-center gap-2">
                                <span>Kelola Barang Dagangan Saya</span>
                                <span class="text-xs font-bold text-slate-700 bg-slate-100 px-2.5 py-0.5 rounded-full">{{ $myListingsCount }}</span>
                            </h2>
                            <p class="text-xs text-slate-600 mt-0.5">Daftar barang yang kamu iklankan di marketplace Lungsur.in</p>
                        </div>

                        <a href="{{ route('katalog.pasang') }}"
                           class="min-h-[44px] inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-md shadow-blue-500/20 transition self-start sm:self-auto focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                            </svg>
                            <span>Pasang Iklan Baru</span>
                        </a>
                    </div>

                    @if ($myListings->count() > 0)
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                            @foreach ($myListings as $listing)
                                <div id="listing-{{ $listing->id }}" class="border border-slate-200 rounded-2xl overflow-hidden flex flex-col justify-between bg-white hover:border-slate-300 transition group scroll-mt-24">
                                    <div>
                                        <div class="aspect-[4/3] bg-slate-100 relative overflow-hidden group/img">
                                            @if ($listing->images->count() > 0)
                                                @php
                                                    $firstImg = $listing->images->first();
                                                    $imgSrc = Str::startsWith($firstImg->image_url, ['http://', 'https://']) ? $firstImg->image_url : asset('storage/' . ltrim($firstImg->image_url, '/'));
                                                @endphp
                                                <img src="{{ $imgSrc }}" alt="{{ $listing->title }}" class="w-full h-full object-cover">
                                            @else
                                                <div class="w-full h-full flex items-center justify-center text-xs font-medium text-slate-500">
                                                    Belum ada foto
                                                </div>
                                            @endif

                                            <div class="absolute top-2.5 left-2.5">
                                                <span class="text-[10px] font-black uppercase px-2 py-0.5 rounded-md shadow-xs {{ $listing->status === 'available' ? 'bg-emerald-700 text-white' : ($listing->status === 'lelang' ? 'bg-amber-500 text-slate-950' : 'bg-slate-800 text-white') }}">
                                                    {{ $listing->status === 'available' ? 'Tersedia' : ($listing->status === 'lelang' ? 'Lelang' : 'Terjual') }}
                                                </span>
                                            </div>

                                            @if ($listing->isCurrentlyBoosted())
                                                <div class="absolute top-2.5 right-2.5 z-10">
                                                    <span class="bg-amber-500 text-slate-950 text-[10px] font-black uppercase px-2 py-0.5 rounded-md shadow-xs">
                                                        Boost Aktif
                                                    </span>
                                                </div>
                                            @endif
                                        </div>

                                        <div class="p-3.5 space-y-1.5">
                                            <div class="flex items-center justify-between text-[11px] font-medium text-slate-600">
                                                <span>{{ $listing->category->name ?? 'Lainnya' }}</span>
                                                <span>{{ $listing->created_at->diffForHumans() }}</span>
                                            </div>
                                            <h3 class="font-bold text-sm text-slate-900 line-clamp-1 group-hover:text-blue-600 transition">
                                                <a href="{{ route('listings.show', $listing->id) }}">{{ $listing->title }}</a>
                                            </h3>
                                            <p class="text-base font-black text-blue-700">
                                                Rp {{ number_format($listing->price, 0, ',', '.') }}
                                            </p>

                                            <div class="pt-2 space-y-2">
                                                @if ($listing->isCurrentlyBoosted())
                                                    <div class="text-[11px] font-bold text-amber-800 bg-amber-50 px-3 py-2 rounded-xl border border-amber-200/80 flex items-center justify-between">
                                                        <span>Boost Aktif s.d.</span>
                                                        <span class="font-black text-amber-900">{{ $listing->boosted_until ? $listing->boosted_until->translatedFormat('d M Y') : '30 Hari' }}</span>
                                                    </div>
                                                @elseif ($listing->status === 'available')
                                                    <a href="{{ route('boost.create', $listing->id) }}" 
                                                       class="w-full min-h-[44px] py-2 px-3 rounded-xl text-xs font-extrabold bg-amber-500 hover:bg-amber-400 text-slate-950 shadow-xs transition flex items-center justify-center gap-1.5 cursor-pointer focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-amber-600">
                                                        <span>Boost Iklan (Rp 5.000 / 30 Hari)</span>
                                                    </a>
                                                @endif

                                                @if ($listing->auction && $listing->auction->status === 'active')
                                                    <a href="{{ route('auctions.show', $listing->auction->id) }}"
                                                       class="w-full min-h-[44px] py-2 px-3 rounded-xl text-xs font-black bg-emerald-50 hover:bg-emerald-100 text-emerald-800 border border-emerald-200 transition flex items-center justify-between">
                                                        <span>Sesi Lelang Aktif</span>
                                                        <span>Lihat &rarr;</span>
                                                    </a>
                                                @elseif ($listing->status === 'available')
                                                    <a href="{{ route('auctions.payment.create', $listing->id) }}"
                                                       class="w-full min-h-[44px] py-2 px-3 rounded-xl text-xs font-bold bg-emerald-700 hover:bg-emerald-800 text-white shadow-xs transition flex items-center justify-center gap-1.5 cursor-pointer focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-emerald-700">
                                                        <span>Buka Lelang (Rp 5.000)</span>
                                                    </a>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <div class="p-3 border-t border-slate-100 bg-slate-50/60 space-y-2">
                                        @if ($listing->status === 'lelang' && $listing->auction)
                                            <div class="flex items-center gap-2">
                                                <a href="{{ route('auctions.show', $listing->auction->id) }}"
                                                   class="flex-1 min-h-[44px] py-2 px-3 rounded-xl text-xs font-bold transition flex items-center justify-center bg-teal-50 hover:bg-teal-100 text-teal-800 border border-teal-200">
                                                    Pantau Lelang &rarr;
                                                </a>
                                                <a href="{{ route('iklan.edit', $listing->id) }}" aria-label="Edit Iklan" class="w-11 h-11 rounded-xl text-blue-700 bg-white hover:bg-blue-50 border border-blue-200 transition flex items-center justify-center shrink-0" title="Edit Iklan">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                                </a>
                                                <button type="button" aria-label="Hapus Iklan" @click="openDeleteModal('{{ addslashes($listing->title) }}', '{{ route('dashboard.listings.destroy', $listing->id) }}')" class="w-11 h-11 rounded-xl text-rose-700 bg-white hover:bg-rose-50 border border-rose-200 transition flex items-center justify-center shrink-0 cursor-pointer" title="Hapus Iklan">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                </button>
                                            </div>
                                        @else
                                            <form method="POST" action="{{ route('dashboard.listings.status', $listing->id) }}#listing-{{ $listing->id }}" class="space-y-2">
                                                @csrf
                                                @method('PATCH')
                                                @if ($listing->status === 'available')
                                                    <input type="hidden" name="status" value="sold">
                                                    <select name="buyer_id" required aria-label="Pilih pembeli barang" class="w-full min-h-[44px] py-2 px-3 rounded-xl text-base sm:text-xs bg-white text-slate-800 border border-slate-300 focus:outline-none focus:border-blue-600">
                                                        <option value="">Pilih pembeli deal COD</option>
                                                        @foreach ($buyers as $b)
                                                            <option value="{{ $b->id }}">{{ $b->name }}</option>
                                                        @endforeach
                                                    </select>
                                                    <div class="flex items-center gap-2">
                                                        <button type="submit" class="flex-1 min-h-[44px] py-2 px-3 rounded-xl text-xs font-bold bg-amber-50 hover:bg-amber-100 text-amber-900 border border-amber-300 transition cursor-pointer">
                                                            Tandai Terjual
                                                        </button>
                                                        <a href="{{ route('iklan.edit', $listing->id) }}" aria-label="Edit Iklan" class="w-11 h-11 rounded-xl text-blue-700 bg-white hover:bg-blue-50 border border-blue-200 transition flex items-center justify-center shrink-0" title="Edit Iklan">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                                        </a>
                                                        <button type="button" aria-label="Hapus Iklan" @click="openDeleteModal('{{ addslashes($listing->title) }}', '{{ route('dashboard.listings.destroy', $listing->id) }}')" class="w-11 h-11 rounded-xl text-rose-700 bg-white hover:bg-rose-50 border border-rose-200 transition flex items-center justify-center shrink-0 cursor-pointer" title="Hapus Iklan">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                        </button>
                                                    </div>
                                                @elseif ($listing->status === 'sold')
                                                    <input type="hidden" name="status" value="available">
                                                    <div class="flex items-center gap-2">
                                                        <button type="submit" class="flex-1 min-h-[44px] py-2 px-3 rounded-xl text-xs font-bold bg-emerald-50 hover:bg-emerald-100 text-emerald-800 border border-emerald-200 transition cursor-pointer">
                                                            Jadikan Tersedia
                                                        </button>
                                                        <a href="{{ route('iklan.edit', $listing->id) }}" aria-label="Edit Iklan" class="w-11 h-11 rounded-xl text-blue-700 bg-white hover:bg-blue-50 border border-blue-200 transition flex items-center justify-center shrink-0" title="Edit Iklan">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                                        </a>
                                                        <button type="button" aria-label="Hapus Iklan" @click="openDeleteModal('{{ addslashes($listing->title) }}', '{{ route('dashboard.listings.destroy', $listing->id) }}')" class="w-11 h-11 rounded-xl text-rose-700 bg-white hover:bg-rose-50 border border-rose-200 transition flex items-center justify-center shrink-0 cursor-pointer" title="Hapus Iklan">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                        </button>
                                                    </div>
                                                @endif
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="pt-4 border-t border-slate-100">
                            {{ $myListings->links() }}
                        </div>
                    @else
                        <div class="rounded-2xl border border-dashed border-slate-200 py-12 text-center space-y-3">
                            <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center mx-auto">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            </div>
                            <div class="space-y-1">
                                <p class="text-sm font-bold text-slate-900">Belum ada barang yang kamu iklankan</p>
                                <p class="text-xs text-slate-600">Mulai jual perabot kos, buku, atau barang yang sudah tidak terpakai ke sesama mahasiswa.</p>
                            </div>
                            <a href="{{ route('katalog.pasang') }}" class="min-h-[44px] inline-flex items-center gap-1.5 px-6 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-md transition">
                                <span>Pasang Iklan Pertamamu</span>
                                <span aria-hidden="true">&rarr;</span>
                            </a>
                        </div>
                    @endif
                </div>

                <div x-show="activeTab === 'lelang'" class="bg-white border border-slate-200/80 rounded-3xl p-4 sm:p-6 shadow-xs space-y-5 sm:space-y-6">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                        <div>
                            <h2 class="text-base sm:text-lg font-black text-slate-900 tracking-tight">Sesi Lelang yang Saya Buka</h2>
                            <p class="text-xs text-slate-600 mt-0.5">Pantau sesi bidding terbuka dan penawar tertinggi barang kos kamu.</p>
                        </div>
                    </div>

                    @if ($myAuctions->count() > 0)
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach ($myAuctions as $auctionListing)
                                @php $auc = $auctionListing->auction; @endphp
                                <div class="border border-slate-200 rounded-2xl p-4 bg-white flex flex-col justify-between hover:border-slate-300 transition space-y-3">
                                    <div class="space-y-2">
                                        <div class="flex items-center justify-between text-xs">
                                            <span class="text-slate-600 font-semibold">{{ $auctionListing->category->name ?? 'Lelang' }}</span>
                                            <span class="px-2 py-0.5 rounded-md text-[10px] font-black uppercase {{ $auc->status === 'active' ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-700' }}">
                                                {{ $auc->status === 'active' ? 'Sedang Berjalan' : ($auc->status === 'pending_approval' ? 'Menunggu Admin' : 'Selesai') }}
                                            </span>
                                        </div>
                                        <h3 class="font-bold text-sm text-slate-900 line-clamp-1">{{ $auctionListing->title }}</h3>
                                        <div class="p-3 rounded-xl bg-slate-50 border border-slate-100 space-y-1.5 text-xs">
                                            <div class="flex justify-between">
                                                <span class="text-slate-600">Harga Awal:</span>
                                                <span class="font-semibold text-slate-800">Rp {{ number_format($auc->starting_price ?? $auctionListing->price, 0, ',', '.') }}</span>
                                            </div>
                                            <div class="flex justify-between">
                                                <span class="text-slate-600">Tawaran Tertinggi:</span>
                                                <span class="font-black text-emerald-700">
                                                    Rp {{ number_format($auc->highestBid?->bid_amount ?? $auc->starting_price ?? 0, 0, ',', '.') }}
                                                </span>
                                            </div>
                                            <div class="flex justify-between">
                                                <span class="text-slate-600">Penawar Terdepan:</span>
                                                <span class="font-bold text-slate-900">{{ $auc->highestBid?->user?->name ?? 'Belum ada tawaran' }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <a href="{{ route('auctions.show', $auc->id) }}" class="w-full min-h-[44px] py-2.5 px-3 rounded-xl bg-blue-50 hover:bg-blue-100 text-blue-700 font-bold text-xs flex items-center justify-center transition">
                                        Pantau Ruang Lelang &rarr;
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="rounded-2xl border border-dashed border-slate-200 py-12 text-center space-y-3">
                            <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-700 flex items-center justify-center mx-auto">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                            </div>
                            <div class="space-y-1">
                                <p class="text-sm font-bold text-slate-900">Kamu belum pernah membuka sesi lelang</p>
                                <p class="text-xs text-slate-600">Pilih salah satu barang daganganmu yang aktif untuk dibuka sesi lelang cepat.</p>
                            </div>
                        </div>
                    @endif
                </div>

                <div x-show="activeTab === 'peluang'" class="bg-white border border-slate-200/80 rounded-3xl p-4 sm:p-6 shadow-xs space-y-5 sm:space-y-6">
                    <div class="flex flex-wrap items-center justify-between gap-2 border-b border-slate-100 pb-4">
                        <div>
                            <h2 class="text-base sm:text-lg font-black text-slate-900 tracking-tight">Peluang Jual dari Papan Wanted Ads</h2>
                            <p class="text-xs text-slate-600 mt-0.5">Mahasiswa lain di Malang sedang membutuhkan barang-barang ini. Tawarkan barangmu langsung!</p>
                        </div>
                        <span class="text-xs font-bold px-2.5 py-0.5 rounded-full bg-emerald-100 text-emerald-800">
                            {{ $wantedOpportunities->count() }} Permintaan Aktif
                        </span>
                    </div>

                    @if ($wantedOpportunities->count() > 0)
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach ($wantedOpportunities as $wad)
                                <div class="border border-slate-200 rounded-2xl p-4 bg-white flex flex-col justify-between hover:border-slate-300 transition space-y-3">
                                    <div class="space-y-2">
                                        <div class="flex items-center justify-between text-xs text-slate-600 font-medium">
                                            <span>{{ $wad->category->name ?? 'Kebutuhan' }}</span>
                                            <span>{{ $wad->created_at->diffForHumans() }}</span>
                                        </div>
                                        <h3 class="font-bold text-sm text-slate-900">{{ $wad->title }}</h3>
                                        <p class="text-xs text-slate-600 line-clamp-2">{{ $wad->description }}</p>
                                        <div class="p-2.5 rounded-xl bg-amber-50/70 border border-amber-200/60 flex items-center justify-between text-xs">
                                            <span class="text-amber-900 font-semibold">Budget Maksimal:</span>
                                            <span class="font-black text-amber-950">Rp {{ number_format($wad->max_budget, 0, ',', '.') }}</span>
                                        </div>
                                    </div>
                                    <button type="button" 
                                            @click="openOfferModal({{ $wad->id }}, '{{ addslashes($wad->title) }}', '{{ addslashes($wad->user->name ?? 'Mahasiswa') }}')"
                                            class="w-full min-h-[44px] py-2.5 px-3 rounded-xl bg-emerald-700 hover:bg-emerald-800 text-white font-bold text-xs text-center transition shadow-xs cursor-pointer flex items-center justify-center gap-1.5">
                                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                                        <span>Tawarkan Barang Saya</span>
                                    </button>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="rounded-2xl border border-dashed border-slate-200 py-12 text-center space-y-2">
                            <p class="text-sm font-bold text-slate-900">Belum ada wanted ads baru dari mahasiswa lain</p>
                            <p class="text-xs text-slate-600">Cek kembali secara berkala saat mahasiswa memasang kebutuhan perabot kos mereka.</p>
                        </div>
                    @endif
                </div>

            @else

                <div x-show="activeTab === 'lelang'" class="bg-white border border-slate-200/80 rounded-3xl p-4 sm:p-6 shadow-xs space-y-5 sm:space-y-6">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3.5 border-b border-slate-100 pb-4">
                        <div>
                            <h2 class="text-base sm:text-lg font-black text-slate-900 tracking-tight">Lelang yang Pernah Kamu Ikuti</h2>
                            <p class="text-xs text-slate-600 mt-0.5">Pantau status penawaran lelang barang kos mahasiswa</p>
                        </div>
                        <a href="{{ route('auctions.index') }}" class="min-h-[44px] inline-flex items-center justify-center gap-1.5 px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 font-bold text-xs transition self-start sm:self-auto">
                            <span>Jelajahi Lelang Aktif &rarr;</span>
                        </a>
                    </div>

                    @if ($myBids->count() > 0)
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach ($myBids as $bid)
                                @php
                                    $auction = $bid->auction;
                                    $isHighest = $auction && $auction->highestBid && (int) $auction->highestBid->user_id === (int) $user->id;
                                    $isClosed = $auction && $auction->status === 'closed';
                                    $isWinner = $isClosed && $isHighest;
                                @endphp
                                <div class="border border-slate-200 rounded-2xl p-4 flex flex-col justify-between bg-white hover:border-slate-300 transition space-y-3">
                                    <div class="space-y-2">
                                        <div class="flex items-center justify-between text-xs">
                                            @if ($isWinner)
                                                <span class="px-2 py-0.5 rounded-md text-[10px] font-black bg-blue-100 text-blue-800">Menang Lelang</span>
                                            @elseif ($isHighest && !$isClosed)
                                                <span class="px-2 py-0.5 rounded-md text-[10px] font-black bg-emerald-100 text-emerald-800">Tawaran Tertinggi</span>
                                            @elseif (!$isHighest && !$isClosed)
                                                <span class="px-2 py-0.5 rounded-md text-[10px] font-black bg-amber-100 text-amber-800">Tersalip</span>
                                            @else
                                                <span class="px-2 py-0.5 rounded-md text-[10px] font-black bg-slate-100 text-slate-700">Selesai</span>
                                            @endif
                                            <span class="text-slate-600 text-[11px] font-medium">{{ $bid->created_at->diffForHumans() }}</span>
                                        </div>
                                        <h3 class="font-bold text-sm text-slate-900 line-clamp-1">
                                            {{ $auction->listing->title ?? 'Sesi Lelang' }}
                                        </h3>
                                        <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-100 space-y-1 text-xs">
                                            <div class="flex justify-between">
                                                <span class="text-slate-600">Tawaranmu:</span>
                                                <strong class="text-blue-700 font-black">Rp {{ number_format($bid->bid_amount, 0, ',', '.') }}</strong>
                                            </div>
                                            <div class="flex justify-between">
                                                <span class="text-slate-600">Tertinggi Saat Ini:</span>
                                                <strong class="text-slate-900 font-bold">Rp {{ number_format($auction->highestBid?->bid_amount ?? $bid->bid_amount, 0, ',', '.') }}</strong>
                                            </div>
                                        </div>
                                    </div>
                                    @if ($auction)
                                        <a href="{{ route('auctions.show', $auction->id) }}" class="w-full min-h-[44px] py-2.5 px-3 rounded-xl bg-blue-50 hover:bg-blue-100 text-blue-700 font-bold text-xs flex items-center justify-center transition">
                                            Lihat Sesi Lelang &rarr;
                                        </a>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="rounded-2xl border border-dashed border-slate-200 py-12 text-center space-y-3">
                            <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-700 flex items-center justify-center mx-auto">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                            </div>
                            <div class="space-y-1">
                                <p class="text-sm font-bold text-slate-900">Kamu belum pernah mengajukan tawaran lelang</p>
                                <p class="text-xs text-slate-600">Ikuti lelang barang kos mahasiswa untuk mendapatkan harga hemat dengan sistem bidding terbuka.</p>
                            </div>
                            <a href="{{ route('auctions.index') }}" class="min-h-[44px] inline-flex items-center gap-1.5 px-6 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-md transition">
                                <span>Cek Lelang Terbuka</span>
                                <span aria-hidden="true">&rarr;</span>
                            </a>
                        </div>
                    @endif
                </div>

                <div x-show="activeTab === 'wanted'" class="bg-white border border-slate-200/80 rounded-3xl p-4 sm:p-6 shadow-xs space-y-5 sm:space-y-6">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3.5 border-b border-slate-100 pb-4">
                        <div>
                            <h2 class="text-base sm:text-lg font-black text-slate-900 tracking-tight flex items-center gap-2">
                                <span>Wanted Ads (Barang yang Sedang Kamu Cari)</span>
                                <span class="text-xs font-bold text-slate-700 bg-slate-100 px-2.5 py-0.5 rounded-full">{{ $myWantedAdsCount }}</span>
                            </h2>
                            <p class="text-xs text-slate-600 mt-0.5">Iklan kebutuhan yang kamu pasang di papan Wanted Ads kampus.</p>
                        </div>
                        <a href="{{ route('wanted-ads.index') }}" class="min-h-[44px] inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-md transition self-start sm:self-auto">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                            <span>+ Pasang Wanted Ad Baru</span>
                        </a>
                    </div>

                    @if ($myWantedAds->count() > 0)
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach ($myWantedAds as $ad)
                                <div class="border border-slate-200 rounded-2xl p-4 bg-white flex flex-col justify-between hover:border-slate-300 transition space-y-3">
                                    <div class="space-y-2">
                                        <div class="flex items-center justify-between text-xs">
                                            <span class="text-slate-600 font-medium">{{ $ad->category->name ?? 'Kategori' }}</span>
                                            <span class="px-2 py-0.5 rounded-md text-[10px] font-black uppercase bg-blue-100 text-blue-800">
                                                Masih Dicari
                                            </span>
                                        </div>
                                        <h3 class="font-bold text-sm text-slate-900">{{ $ad->title }}</h3>
                                        <p class="text-xs text-slate-600 line-clamp-2">{{ $ad->description }}</p>
                                        <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-100 flex items-center justify-between text-xs">
                                            <span class="text-slate-600">Budget Maks:</span>
                                            <strong class="text-slate-900 font-black">Rp {{ number_format($ad->max_budget, 0, ',', '.') }}</strong>
                                        </div>
                                    </div>
                                    <div class="pt-2 border-t border-slate-100 flex items-center justify-between">
                                        <span class="text-[11px] font-medium text-slate-600">{{ $ad->created_at->diffForHumans() }}</span>
                                        <form method="POST" action="{{ route('wanted-ads.destroy', $ad->id) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus postingan kebutuhan ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="min-h-[40px] px-2.5 rounded-lg text-xs text-rose-700 font-bold hover:bg-rose-50 transition cursor-pointer">
                                                Hapus Iklan
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="rounded-2xl border border-dashed border-slate-200 py-12 text-center space-y-3">
                            <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center mx-auto">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <div class="space-y-1">
                                <p class="text-sm font-bold text-slate-900">Kamu belum pernah memasang Wanted Ads</p>
                                <p class="text-xs text-slate-600">Lagi cari rice cooker, meja lipat, atau buku kuliah? Pasang iklan dicari agar penjual sekitar menghubungimu.</p>
                            </div>
                            <a href="{{ route('wanted-ads.index') }}" class="min-h-[44px] inline-flex items-center gap-1.5 px-6 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-md transition">
                                <span>Pasang Wanted Ad Sekarang</span>
                                <span aria-hidden="true">&rarr;</span>
                            </a>
                        </div>
                    @endif
                </div>

                <div x-show="activeTab === 'riwayat'" class="bg-white border border-slate-200/80 rounded-3xl p-4 sm:p-6 shadow-xs space-y-5 sm:space-y-6">
                    <div class="flex flex-wrap items-center justify-between gap-2 border-b border-slate-100 pb-4">
                        <div>
                            <h2 class="text-base sm:text-lg font-black text-slate-900 tracking-tight">Riwayat Pembelian &amp; Deal COD</h2>
                            <p class="text-xs text-slate-600 mt-0.5">Daftar barang kos dan sesi lelang yang telah berhasil kamu dapatkan.</p>
                        </div>
                        <a href="{{ route('dashboard.history') }}" class="min-h-[44px] inline-flex items-center px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 text-xs font-bold transition">
                            Lihat Riwayat Lengkap &rarr;
                        </a>
                    </div>

                    @if ($purchasedListings->count() > 0 || $wonAuctions->count() > 0)
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach ($purchasedListings as $pList)
                                <div class="border border-slate-200 rounded-2xl p-4 bg-white space-y-2">
                                    <span class="px-2 py-0.5 rounded-md text-[10px] font-black uppercase bg-emerald-100 text-emerald-800">Deal COD Selesai</span>
                                    <h3 class="font-bold text-sm text-slate-900">{{ $pList->title }}</h3>
                                    <p class="text-xs text-slate-600">Harga: <strong class="text-slate-900">Rp {{ number_format($pList->price, 0, ',', '.') }}</strong></p>
                                    <p class="text-xs text-slate-600">Penjual: {{ $pList->user->name ?? 'Mahasiswa' }}</p>
                                </div>
                            @endforeach
                            @foreach ($wonAuctions as $wAuc)
                                <div class="border border-slate-200 rounded-2xl p-4 bg-white space-y-2">
                                    <span class="px-2 py-0.5 rounded-md text-[10px] font-black uppercase bg-blue-100 text-blue-800">Lelang Dimenangkan</span>
                                    <h3 class="font-bold text-sm text-slate-900">{{ $wAuc->listing->title ?? 'Sesi Lelang' }}</h3>
                                    <p class="text-xs text-slate-600">Tawaran Menang: <strong class="text-blue-700">Rp {{ number_format($wAuc->highestBid?->bid_amount ?? 0, 0, ',', '.') }}</strong></p>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="rounded-2xl border border-dashed border-slate-200 py-12 text-center space-y-2">
                            <p class="text-sm font-bold text-slate-900">Belum ada riwayat pembelian</p>
                            <p class="text-xs text-slate-600">Setelah transaksi COD dengan penjual ditandai selesai, barang akan muncul di sini.</p>
                        </div>
                    @endif
                </div>

            @endif

            <form id="hidden-delete-form" method="POST" action="" class="hidden">
                @csrf
                @method('DELETE')
            </form>

            <div x-show="showDeleteModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4" @keydown.escape.window="closeDeleteModal()">
                <div class="absolute inset-0 bg-slate-900/50 backdrop-blur-xs" @click="closeDeleteModal()"></div>
                <div class="relative bg-white rounded-2xl shadow-2xl border border-slate-100 w-full max-w-sm p-6 text-center space-y-4">
                    <div class="mx-auto w-14 h-14 rounded-2xl bg-rose-50 border border-rose-100 flex items-center justify-center">
                        <svg class="w-7 h-7 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                    </div>
                    <div class="space-y-1.5">
                        <h3 class="text-lg font-black text-slate-900">Hapus Iklan Ini?</h3>
                        <p class="text-sm text-slate-600 leading-relaxed">
                            Kamu akan menghapus iklan <span class="font-bold text-slate-900" x-text="'\"' + deleteTitle + '\"'"></span>. Tindakan ini tidak bisa dibatalkan.
                        </p>
                    </div>
                    <div class="flex items-center gap-3 pt-2">
                        <button type="button" @click="closeDeleteModal()" class="flex-1 min-h-[44px] py-2.5 px-4 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 font-bold text-sm transition cursor-pointer">
                            Batal
                        </button>
                        <button type="button" @click="confirmDelete()" class="flex-1 min-h-[44px] py-2.5 px-4 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-bold text-sm transition shadow-md shadow-rose-500/20 cursor-pointer">
                            Ya, Hapus
                        </button>
                    </div>
                </div>
            </div>

            @if ($mode === 'seller')
                <div x-show="showOfferModal" x-cloak class="fixed inset-0 z-50 flex items-end sm:items-center justify-center p-0 sm:p-4" @keydown.escape.window="closeOfferModal()">
                    <div class="absolute inset-0 bg-slate-900/50 backdrop-blur-xs" @click="closeOfferModal()"></div>
                    <div class="relative bg-white rounded-t-3xl sm:rounded-3xl shadow-2xl border border-slate-100 w-full max-w-lg p-5 sm:p-7 space-y-5 max-h-[88dvh] overflow-y-auto">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <span class="text-xs font-bold uppercase tracking-wider text-emerald-700">Peluang Wanted Ads</span>
                                <h3 class="text-lg font-black text-slate-900 mt-1">Tawarkan Barang Dagangan</h3>
                                <p class="text-xs text-slate-600 mt-0.5">
                                    Kirim penawaran barang Anda ke pembeli <strong class="text-slate-900" x-text="offerBuyerName"></strong>
                                </p>
                            </div>
                            <button type="button" @click="closeOfferModal()" aria-label="Tutup modal" class="w-11 h-11 rounded-xl text-slate-500 hover:text-slate-800 hover:bg-slate-100 flex items-center justify-center transition cursor-pointer shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>

                        <div class="p-3.5 bg-slate-50 rounded-2xl border border-slate-100 text-xs space-y-1">
                            <span class="text-slate-600 block">Kebutuhan yang dicari pembeli:</span>
                            <strong class="text-slate-900 block font-bold text-sm" x-text="offerWantedTitle"></strong>
                        </div>

                        @if ($myActiveListings->count() > 0)
                            <form :action="'/cari-barang/' + offerWantedAdId + '/tawari'" method="POST" class="space-y-4">
                                @csrf
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-1.5">
                                        Pilih Barang Dagangan Anda yang Sesuai:
                                    </label>
                                    <select name="listing_id" required class="w-full min-h-[44px] py-2.5 px-3 text-base sm:text-sm rounded-xl border border-slate-200 bg-white text-slate-900 focus:outline-none focus:border-emerald-600 focus:ring-2 focus:ring-emerald-100">
                                        <option value="">Pilih Barang Dagangan Aktif</option>
                                        @foreach ($myActiveListings as $actListing)
                                            <option value="{{ $actListing->id }}">
                                                {{ $actListing->title }} (Rp {{ number_format($actListing->price, 0, ',', '.') }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-1.5">
                                        Catatan Penawaran Tambahan (Opsional):
                                    </label>
                                    <textarea name="note" rows="3" placeholder="Contoh: Barang masih mulus, bisa nego tipis dan COD di area kampus UB/Polinema..." class="w-full p-3 text-base sm:text-sm rounded-xl border border-slate-200 bg-white text-slate-900 placeholder-slate-500 focus:outline-none focus:border-emerald-600 focus:ring-2 focus:ring-emerald-100"></textarea>
                                </div>

                                <div class="flex items-center gap-3 pt-2">
                                    <button type="button" @click="closeOfferModal()" class="flex-1 min-h-[44px] py-2.5 px-4 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 font-bold text-xs sm:text-sm transition cursor-pointer">
                                        Batal
                                    </button>
                                    <button type="submit" class="flex-1 min-h-[44px] py-2.5 px-4 rounded-xl bg-emerald-700 hover:bg-emerald-800 text-white font-bold text-xs sm:text-sm transition shadow-md shadow-emerald-700/20 cursor-pointer">
                                        Kirim Penawaran ke Chat
                                    </button>
                                </div>
                            </form>
                        @else
                            <div class="rounded-2xl border border-dashed border-amber-300 bg-amber-50/70 p-5 text-center space-y-3">
                                <p class="text-xs font-bold text-amber-900">Anda belum memiliki barang dagangan aktif yang tersedia.</p>
                                <p class="text-xs text-amber-800">Pasang iklan barang Anda di katalog terlebih dahulu agar dapat ditawarkan ke pembeli.</p>
                                <a href="{{ route('katalog.pasang') }}" class="min-h-[44px] inline-flex items-center justify-center py-2.5 px-4 rounded-xl bg-emerald-700 hover:bg-emerald-800 text-white font-bold text-xs shadow-xs transition">
                                    + Pasang Iklan Sekarang
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

        </div>

    </div>
</x-layouts.app>
