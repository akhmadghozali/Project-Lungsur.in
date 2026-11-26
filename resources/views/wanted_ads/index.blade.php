<x-layouts.app>
    <x-slot:title>
        Papan Barang Dicari (Wanted Ads) - Lungsur.in
    </x-slot:title>

    @php
        $isUserSeller = auth()->check() && auth()->user()->isSeller();
        $isUserBuyer = auth()->check() && !auth()->user()->isSeller();
        $myListingsPayload = $myActiveListings->map(function ($item) {
            return [
                'id' => $item->id,
                'title' => $item->title,
                'price' => (float) $item->price,
                'formatted_price' => number_format($item->price, 0, ',', '.'),
                'category_name' => $item->category->name ?? 'Lainnya',
                'photo_url' => $item->photo_url,
                'url' => route('listings.show', $item->id),
            ];
        })->values();
    @endphp

    <div class="space-y-6 sm:space-y-10" 
         x-data="{ 
             createModalOpen: {{ $errors->any() && $isUserBuyer ? 'true' : 'false' }},
             offerModalOpen: false,
             chatUnavailable: false,
             selectedAd: {
                 id: null,
                 title: '',
                 category: '',
                 budget_max: 0,
                 formatted_budget: '',
                 buyer_name: '',
                 buyer_campus: '',
                 addresses: '',
                 description: ''
             },
             selectedListingId: null,
             myListings: {{ \Illuminate\Support\Js::from($myListingsPayload) }},

             openOffer(ad) {
                 this.selectedAd = ad;
                 this.selectedListingId = this.myListings.length > 0 ? this.myListings[0].id : null;
                 this.chatUnavailable = false;
                 this.offerModalOpen = true;
             },

             getSelectedListing() {
                 return this.myListings.find(l => l.id === this.selectedListingId) || null;
             },

             submitOffer() {
                 if (!this.selectedListingId) {
                     alert('Silakan pilih salah satu postingan barang dagangan Anda terlebih dahulu.');
                     return;
                 }
                 window.location.href = `/chat?listing_id=${this.selectedListingId}`;
             }
         }">

        <section class="bg-gradient-to-br from-indigo-50/90 via-sky-50/50 to-blue-50/70 rounded-3xl border border-indigo-100/60 p-5 sm:p-10 relative overflow-hidden shadow-xs">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 sm:gap-8 items-center relative z-10">
                <div class="lg:col-span-8 space-y-3.5 sm:space-y-4">
                    
                    @if ($isUserSeller)
                        <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full text-xs font-bold bg-amber-100/90 text-amber-900 border border-amber-300/80 shadow-2xs">
                            <span class="w-2 h-2 rounded-full bg-amber-600"></span>
                            <span>Mode Penjual Aktif</span>
                            <span class="text-amber-600">|</span>
                            <span>Papan Kebutuhan Pembeli</span>
                        </div>

                        <h1 class="text-2xl sm:text-4xl font-black text-slate-900 tracking-tight leading-snug">
                            Temukan Pembeli yang <br class="hidden sm:inline">
                            <span class="text-emerald-700">Mencari Perlengkapan</span> Dagangan Anda!
                        </h1>

                        <p class="text-xs sm:text-sm text-slate-700 leading-relaxed max-w-2xl">
                            Papan ini adalah wadah mahasiswa pembeli yang sedang mencari barang kos. Sebagai penjual, Anda dapat langsung <strong>menawarkan postingan barang jualan</strong> Anda ke pembeli yang sesuai dengan Budget Maksimalnya.
                        </p>

                        <div class="pt-1 flex flex-wrap items-center gap-2.5 sm:gap-3">
                            <a href="{{ route('katalog.pasang') }}" 
                               class="w-full sm:w-auto min-h-[44px] inline-flex items-center justify-center gap-2 bg-emerald-700 hover:bg-emerald-800 text-white text-xs sm:text-sm font-bold px-6 py-3 rounded-2xl shadow-md shadow-emerald-700/20 transition focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-emerald-700">
                                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                                </svg>
                                <span>+ Pasang Iklan Barang di Katalog</span>
                            </a>
                            
                            <form action="{{ route('user.switch_mode') }}" method="POST" class="w-full sm:w-auto inline">
                                @csrf
                                <input type="hidden" name="mode" value="buyer">
                                <button type="submit" 
                                        class="w-full sm:w-auto min-h-[44px] inline-flex items-center justify-center gap-2 bg-white hover:bg-slate-50 text-blue-700 text-xs sm:text-sm font-bold px-5 py-3 rounded-2xl border border-blue-200 shadow-2xs transition cursor-pointer focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600"
                                        title="Beralih ke Mode Pembeli jika Anda ingin memasang pengumuman barang yang Anda cari">
                                    <svg class="w-4 h-4 text-blue-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                                    </svg>
                                    <span>Beralih ke Mode Pembeli (Cari Barang)</span>
                                </button>
                            </form>
                        </div>
                    @else
                        <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full text-xs font-bold bg-indigo-100/80 text-indigo-800 border border-indigo-200/80">
                            <svg class="w-3.5 h-3.5 text-indigo-700 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span>Papan Permintaan Kebutuhan Mahasiswa</span>
                        </div>

                        <h1 class="text-2xl sm:text-4xl font-black text-slate-900 tracking-tight leading-snug">
                            Lagi Cari Perlengkapan Kos? <br class="hidden sm:inline">
                            <span class="text-blue-600">Umumkan di Sini</span> dan Temukan Penjualnya!
                        </h1>

                        <p class="text-xs sm:text-sm text-slate-700 leading-relaxed max-w-2xl">
                            Belum menemukan perabot atau perlengkapan kos yang cocok di katalog? Pasang pengumuman barang yang kamu cari secara <strong>gratis</strong>. Sesama mahasiswa yang punya barang tersebut akan langsung menawarkan barang dagangannya kepadamu melalui fitur obrolan.
                        </p>

                        <div class="pt-1 flex flex-wrap items-center gap-2.5 sm:gap-3">
                            @if ($isUserBuyer)
                                <button type="button" 
                                        @click="createModalOpen = true" 
                                        class="w-full sm:w-auto min-h-[44px] inline-flex items-center justify-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-xs sm:text-sm font-bold px-6 py-3 rounded-2xl shadow-md shadow-blue-500/25 transition cursor-pointer focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600">
                                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                                    </svg>
                                    <span>Pasang Kebutuhan Baru</span>
                                </button>
                            @elseif ($isUserSeller)
                                <div class="inline-flex items-center gap-2 px-4 py-3 rounded-2xl bg-emerald-50 text-emerald-800 text-xs sm:text-sm font-semibold border border-emerald-200/80">
                                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-600"></span>
                                    <span>Mode Penjual: Temukan pembeli dan tawarkan barangmu di bawah</span>
                                </div>
                            @else
                                <a href="{{ route('login') }}" 
                                   class="w-full sm:w-auto min-h-[44px] inline-flex items-center justify-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-xs sm:text-sm font-bold px-6 py-3 rounded-2xl shadow-md shadow-blue-500/25 transition focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600">
                                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                                    </svg>
                                    <span>Masuk untuk Pasang Kebutuhan</span>
                                </a>
                            @endif
                            <a href="{{ url('/') }}" 
                               class="w-full sm:w-auto min-h-[44px] inline-flex items-center justify-center gap-2 bg-white hover:bg-slate-50 text-slate-800 text-xs sm:text-sm font-bold px-5 py-3 rounded-2xl border border-slate-200 shadow-2xs transition focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600">
                                <span>Jelajahi Katalog Barang Dijual</span>
                                <span aria-hidden="true">&rarr;</span>
                            </a>
                        </div>
                    @endif
                </div>

                <div class="lg:col-span-4 hidden lg:flex justify-end">
                    <div class="bg-white/90 backdrop-blur-xs p-5 rounded-3xl border border-indigo-100 shadow-sm space-y-3.5 max-w-xs">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-2xl bg-indigo-100 text-indigo-700 flex items-center justify-center shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/></svg>
                            </div>
                            <div class="text-xs">
                                <div class="font-bold text-slate-900">Publikasi Terbuka</div>
                                <div class="text-slate-600">Khusus pembeli yang mencari barang kos</div>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-2xl bg-emerald-100 text-emerald-700 flex items-center justify-center shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                            </div>
                            <div class="text-xs">
                                <div class="font-bold text-slate-900">Terhubung ke Chat</div>
                                <div class="text-slate-600">Tawaran penjual langsung masuk ke obrolan</div>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-2xl bg-blue-100 text-blue-700 flex items-center justify-center shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <div class="text-xs">
                                <div class="font-bold text-slate-900">Budget Maksimal Jelas</div>
                                <div class="text-slate-600">Transaksi transparan sesuai budget mahasiswa</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="space-y-4">
            <div class="bg-white rounded-2xl border border-slate-200/80 p-4 sm:p-5 shadow-xs">
                <form action="{{ route('wanted-ads.index') }}" method="GET" class="flex flex-col md:flex-row items-center gap-3">
                    <div class="relative flex-1 w-full">
                        <svg class="w-4 h-4 text-slate-500 absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <input type="text" 
                               name="q" 
                               value="{{ $search ?? '' }}" 
                               aria-label="Cari barang yang sedang dicari mahasiswa"
                               placeholder="Cari barang yang dicari (contoh: meja lipat, kasur busa)..." 
                               class="w-full min-h-[44px] pl-10 pr-4 py-2.5 text-base sm:text-sm rounded-xl border border-slate-200 bg-slate-50/70 text-slate-900 placeholder-slate-500 focus:bg-white focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-100">
                    </div>

                    <div class="w-full md:w-56 shrink-0">
                        <select name="category" aria-label="Filter kategori kebutuhan" class="w-full min-h-[44px] py-2.5 px-3 text-base sm:text-sm rounded-xl border border-slate-200 bg-slate-50/70 text-slate-800 focus:bg-white focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-100">
                            <option value="">Semua Kategori</option>
                            @foreach ($categories as $cat)
                                <option value="{{ $cat->slug }}" {{ ($categoryParam ?? '') === $cat->slug ? 'selected' : '' }}>
                                    {{ $cat->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex items-center gap-2 w-full md:w-auto shrink-0">
                        <button type="submit" 
                                class="flex-1 md:flex-initial min-h-[44px] inline-flex items-center justify-center gap-2 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs sm:text-sm px-6 py-2.5 rounded-xl shadow-xs transition cursor-pointer focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600">
                            <span>Cari</span>
                        </button>
                        @if (!empty($search) || !empty($categoryParam))
                            <a href="{{ route('wanted-ads.index') }}" 
                               class="min-h-[44px] inline-flex items-center justify-center text-xs font-bold text-rose-700 hover:text-rose-800 bg-rose-50 hover:bg-rose-100 px-4 py-2.5 rounded-xl border border-rose-200 transition focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-rose-600">
                                Reset
                            </a>
                        @endif
                    </div>
                </form>
            </div>
        </section>

        <section class="space-y-5 sm:space-y-6">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div>
                    <h2 class="text-lg sm:text-xl font-black text-slate-900 tracking-tight flex items-center gap-2">
                        <span>Daftar Kebutuhan Terkini</span>
                        <span class="text-xs font-bold px-2.5 py-0.5 rounded-full bg-blue-100 text-blue-800">
                            {{ $wantedAds->total() }} Permintaan
                        </span>
                    </h2>
                    <p class="text-xs text-slate-600 mt-0.5">
                        @if ($isUserSeller)
                            Punya barang yang dicari di bawah? Ketuk tombol <strong>Tawari Barang Dagangan</strong> untuk menghubungkan postingan jualanmu langsung ke chat pembeli.
                        @else
                            Forum bagi mahasiswa yang sedang membutuhkan perlengkapan kos. Penjual akan langsung menawarkan barang yang cocok.
                        @endif
                    </p>
                </div>

                @if ($isUserBuyer)
                    <button type="button" 
                            @click="createModalOpen = true" 
                            class="min-h-[44px] inline-flex items-center gap-1.5 text-xs font-bold text-blue-700 hover:text-blue-800 bg-blue-50 hover:bg-blue-100 border border-blue-200/80 px-4 py-2.5 rounded-xl transition self-start sm:self-auto cursor-pointer focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600">
                        <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                        </svg>
                        <span>Pasang Kebutuhan Baru</span>
                    </button>
                @elseif ($isUserSeller)
                    <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl bg-emerald-50 text-emerald-800 text-xs font-bold border border-emerald-200/80">
                        <span class="w-2 h-2 rounded-full bg-emerald-600"></span>
                        <span>Anda dapat menawari pembeli di bawah ini</span>
                    </div>
                @endif
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-5">
                @forelse ($wantedAds as $ad)
                    <article class="bg-white rounded-2xl border border-slate-200/80 shadow-2xs hover:shadow-md transition p-5 flex flex-col justify-between space-y-4 group">
                        <div class="space-y-2.5">
                            <div class="flex items-center justify-between gap-2 text-xs">
                                <span class="inline-flex items-center gap-1 font-bold text-blue-700 bg-blue-50 px-2.5 py-0.5 rounded-lg border border-blue-200/60 text-[11px]">
                                    {{ $ad->category->name ?? 'Lainnya' }}
                                </span>
                                <span class="text-slate-600 font-medium text-[11px]">
                                    {{ $ad->created_at->diffForHumans() }}
                                </span>
                            </div>

                            <h3 class="text-base font-bold text-slate-900 group-hover:text-blue-600 transition line-clamp-2">
                                {{ $ad->title }}
                            </h3>

                            <p class="text-xs text-slate-700 leading-relaxed line-clamp-3">
                                {{ $ad->description }}
                            </p>
                        </div>

                        <div class="space-y-3 pt-3 border-t border-slate-100">
                            <div class="bg-emerald-50/80 border border-emerald-200/80 rounded-xl px-3.5 py-2.5 flex items-center justify-between">
                                <span class="text-[11px] font-bold text-emerald-900">Budget Maksimal:</span>
                                <span class="text-sm font-black text-emerald-800">
                                    Rp {{ number_format($ad->budget_max, 0, ',', '.') }}
                                </span>
                            </div>

                            <div class="flex items-center justify-between text-xs text-slate-600 gap-2">
                                <div class="flex items-center gap-2 min-w-0">
                                    <div class="w-8 h-8 rounded-full bg-slate-900 text-white font-bold flex items-center justify-center text-xs shrink-0">
                                        {{ strtoupper(substr($ad->user->name ?? 'U', 0, 1)) }}
                                    </div>
                                    <div class="min-w-0">
                                        <div class="font-bold text-slate-900 truncate text-xs">{{ $ad->user->name ?? 'Mahasiswa' }}</div>
                                        <div class="text-[11px] font-semibold text-blue-700 truncate">{{ $ad->user->campus ?? 'Mahasiswa Malang' }}</div>
                                    </div>
                                </div>

                                <div class="text-right text-[11px] text-slate-600 font-medium shrink-0 max-w-[130px]" title="{{ $ad->addresses }}">
                                    <span class="inline-flex items-center gap-1 truncate">
                                        <svg class="w-3.5 h-3.5 text-slate-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                        <span class="truncate block">{{ $ad->addresses }}</span>
                                    </span>
                                </div>
                            </div>

                            <div class="pt-1 flex items-center gap-2">
                                @if (auth()->id() === (int) $ad->user_id || (auth()->check() && auth()->user()->role === 'admin'))
                                    <form action="{{ route('wanted-ads.destroy', $ad->id) }}" method="POST" class="w-full" onsubmit="return confirm('Apakah Anda yakin ingin menghapus postingan kebutuhan ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="w-full min-h-[44px] py-2.5 px-3 rounded-xl bg-rose-50 hover:bg-rose-100 border border-rose-200 text-rose-700 font-bold text-xs transition cursor-pointer focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-rose-600">
                                            Hapus Postingan Ini
                                        </button>
                                    </form>
                                @elseif ($isUserSeller)
                                    <button type="button" 
                                            @click="openOffer({
                                                id: {{ $ad->id }},
                                                title: {{ \Illuminate\Support\Js::from($ad->title) }},
                                                category: {{ \Illuminate\Support\Js::from($ad->category->name ?? 'Lainnya') }},
                                                budget_max: {{ (float) $ad->budget_max }},
                                                formatted_budget: {{ \Illuminate\Support\Js::from(number_format($ad->budget_max, 0, ',', '.')) }},
                                                buyer_name: {{ \Illuminate\Support\Js::from($ad->user->name ?? 'Mahasiswa') }},
                                                buyer_campus: {{ \Illuminate\Support\Js::from($ad->user->campus ?? 'Mahasiswa Malang') }},
                                                addresses: {{ \Illuminate\Support\Js::from($ad->addresses) }},
                                                description: {{ \Illuminate\Support\Js::from($ad->description) }}
                                            })"
                                            class="w-full min-h-[44px] text-center py-2.5 px-3 rounded-xl bg-emerald-700 hover:bg-emerald-800 text-white font-bold text-xs transition flex items-center justify-center gap-2 shadow-sm shadow-emerald-700/20 cursor-pointer focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-emerald-700">
                                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                                        </svg>
                                        <span>Tawari Barang Dagangan</span>
                                    </button>
                                @else
                                    <a href="{{ route('home', ['q' => $ad->title]) }}" 
                                       class="w-full min-h-[44px] text-center py-2.5 px-3 rounded-xl bg-blue-50 hover:bg-blue-600 text-blue-700 hover:text-white border border-blue-200/80 font-bold text-xs transition flex items-center justify-center gap-1.5 shadow-2xs focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600">
                                        <span>Cek Barang Serupa di Katalog</span>
                                        <span aria-hidden="true">&rarr;</span>
                                    </a>
                                @endif
                            </div>
                        </div>
                    </article>
                @empty
                    <div class="col-span-full rounded-2xl border border-dashed border-slate-300 bg-white p-8 sm:p-12 text-center space-y-3">
                        <div class="w-12 h-12 rounded-2xl bg-indigo-50 text-indigo-700 flex items-center justify-center mx-auto">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                        </div>
                        <h3 class="text-base font-bold text-slate-900">Belum Ada Permintaan Barang</h3>
                        <p class="text-xs text-slate-600 max-w-md mx-auto">
                            Saat ini belum ada pengumuman barang yang dicari mahasiswa.
                        </p>
                        @if ($isUserBuyer)
                            <button type="button" 
                                    @click="createModalOpen = true" 
                                    class="min-h-[44px] inline-flex items-center gap-1.5 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs px-5 py-2.5 rounded-xl shadow-xs transition cursor-pointer focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600">
                                <span>+ Pasang Kebutuhan Sekarang</span>
                            </button>
                        @endif
                    </div>
                @endforelse
            </div>

            <div class="pt-4">
                {{ $wantedAds->links() }}
            </div>
        </section>

        <div x-show="offerModalOpen" 
             x-cloak 
             role="dialog"
             aria-modal="true"
             aria-labelledby="offerModalTitle"
             class="fixed inset-0 z-50 flex items-end sm:items-center justify-center bg-slate-900/60 backdrop-blur-xs p-0 sm:p-4"
             @keydown.escape.window="offerModalOpen = false">
            <div class="bg-white rounded-t-3xl sm:rounded-3xl max-w-2xl w-full p-5 sm:p-8 shadow-2xl relative max-h-[88dvh] overflow-y-auto border border-slate-100" 
                 @click.outside="offerModalOpen = false">
                
                <div class="w-10 h-1.5 bg-slate-200 rounded-full mx-auto mb-3 sm:hidden"></div>

                <button type="button" 
                        @click="offerModalOpen = false" 
                        aria-label="Tutup modal penawaran"
                        class="absolute right-4 top-4 w-11 h-11 flex items-center justify-center text-slate-500 hover:text-slate-800 rounded-full hover:bg-slate-100 transition cursor-pointer focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>

                <div class="mb-4 sm:mb-5 pr-10">
                    <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-800 mb-2 border border-emerald-200/80">
                        <svg class="w-3.5 h-3.5 text-emerald-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                        </svg>
                        <span>Obrolan Internal (Chat)</span>
                    </div>
                    <h3 id="offerModalTitle" class="text-lg sm:text-xl font-black text-slate-900 tracking-tight">Tawarkan Produk Dagangan ke Pembeli</h3>
                    <p class="text-xs text-slate-600 mt-1">
                        Pilih salah satu postingan jualan Anda untuk langsung disambungkan ke ruang obrolan pembeli.
                    </p>
                </div>

                <div class="space-y-4 sm:space-y-5">
                    <div class="bg-slate-50 border border-slate-200/80 rounded-2xl p-4 space-y-3">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <span class="inline-block text-[10px] font-bold px-2 py-0.5 rounded-md bg-blue-100 text-blue-800 mb-1" x-text="selectedAd.category"></span>
                                <h4 class="text-sm font-black text-slate-900" x-text="selectedAd.title"></h4>
                            </div>
                            <div class="text-right shrink-0">
                                <div class="text-[11px] text-slate-600 font-medium">Budget Maksimal:</div>
                                <div class="text-xs sm:text-sm font-black text-emerald-700">
                                    Rp <span x-text="selectedAd.formatted_budget"></span>
                                </div>
                            </div>
                        </div>

                        <div class="text-xs text-slate-700 bg-white p-3 rounded-xl border border-slate-200/60 space-y-1.5">
                            <div class="font-bold text-slate-800">Kriteria / Spesifikasi Dicari:</div>
                            <p class="italic text-slate-600 leading-relaxed" x-text="selectedAd.description"></p>
                        </div>

                        <div class="flex flex-wrap items-center justify-between text-[11px] text-slate-600 gap-2 pt-1">
                            <div>
                                Calon Pembeli: <span class="font-bold text-slate-900" x-text="selectedAd.buyer_name"></span> (<span x-text="selectedAd.buyer_campus"></span>)
                            </div>
                            <div>
                                Lokasi Serah Terima: <span class="font-semibold text-slate-800" x-text="selectedAd.addresses"></span>
                            </div>
                        </div>
                    </div>

                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <label class="block text-xs font-bold text-slate-800">
                                Pilih Postingan Dagangan Anda: <span class="text-rose-600">*</span>
                            </label>
                            <span class="text-[11px] font-bold text-emerald-700" x-text="myListings.length + ' Postingan Aktif'"></span>
                        </div>

                        <template x-if="myListings.length === 0">
                            <div class="rounded-2xl border border-dashed border-amber-300 bg-amber-50/70 p-5 text-center space-y-2.5">
                                <p class="text-xs text-amber-900 font-medium">
                                    Anda belum memiliki iklan barang yang berstatus <strong>Tersedia</strong> di katalog Anda.
                                </p>
                                <a href="{{ route('katalog.pasang') }}" 
                                   class="min-h-[44px] inline-flex items-center justify-center gap-1.5 text-xs font-bold bg-amber-700 hover:bg-amber-800 text-white px-4 py-2.5 rounded-xl transition">
                                    <span>+ Pasang Iklan Barang di Katalog Sekarang</span>
                                </a>
                            </div>
                        </template>

                        <template x-if="myListings.length > 0">
                            <div class="max-h-56 overflow-y-auto space-y-2 pr-1">
                                <template x-for="item in myListings" :key="item.id">
                                    <div @click="selectedListingId = item.id; chatUnavailable = false;"
                                         :class="selectedListingId === item.id 
                                            ? 'border-emerald-600 bg-emerald-50/50 ring-2 ring-emerald-500/20' 
                                            : 'border-slate-200 hover:border-slate-300 bg-white'"
                                         class="p-3 rounded-2xl border transition cursor-pointer flex items-center justify-between gap-3">
                                        
                                        <div class="flex items-center gap-3 min-w-0">
                                            <input type="radio" 
                                                   name="radio_listing_selector"
                                                   :value="item.id" 
                                                   :checked="selectedListingId === item.id"
                                                   class="w-4 h-4 text-emerald-700 border-slate-300 focus:ring-emerald-600 shrink-0">
                                            
                                            <template x-if="item.photo_url">
                                                <img :src="item.photo_url" :alt="item.title" class="w-12 h-12 rounded-xl object-cover border border-slate-200 shrink-0">
                                            </template>
                                            <template x-if="!item.photo_url">
                                                <div class="w-12 h-12 rounded-xl bg-slate-100 text-slate-500 flex items-center justify-center shrink-0">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                                                </div>
                                            </template>

                                            <div class="min-w-0">
                                                <div class="text-xs font-bold text-slate-900 truncate" x-text="item.title"></div>
                                                <div class="text-[11px] text-slate-600" x-text="item.category_name"></div>
                                                <div class="text-xs font-black text-emerald-700">
                                                    Rp <span x-text="item.formatted_price"></span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="text-right shrink-0">
                                            <template x-if="item.price <= selectedAd.budget_max">
                                                <span class="inline-block text-[10px] font-bold px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800">
                                                    Sesuai Budget
                                                </span>
                                            </template>
                                            <template x-if="item.price > selectedAd.budget_max">
                                                <span class="inline-block text-[10px] font-bold px-2 py-0.5 rounded-full bg-amber-100 text-amber-900">
                                                    Di Atas Budget
                                                </span>
                                            </template>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </template>
                    </div>

                    <div class="pt-3 border-t border-slate-100 flex flex-col-reverse sm:flex-row items-stretch sm:items-center justify-end gap-2.5 sm:gap-3">
                        <button type="button" 
                                @click="offerModalOpen = false" 
                                class="min-h-[44px] px-4 py-2.5 rounded-xl text-xs sm:text-sm font-bold text-slate-700 border border-slate-200 sm:border-transparent hover:bg-slate-100 transition cursor-pointer focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-slate-600">
                            Batal
                        </button>

                        <button type="button" 
                                @click="submitOffer()"
                                :disabled="!selectedListingId || myListings.length === 0"
                                :class="(!selectedListingId || myListings.length === 0) ? 'opacity-50 cursor-not-allowed bg-slate-400' : 'bg-emerald-700 hover:bg-emerald-800 shadow-md shadow-emerald-700/25 cursor-pointer'"
                                class="min-h-[44px] inline-flex items-center justify-center gap-2 text-white font-bold text-xs sm:text-sm px-6 py-2.5 rounded-xl transition focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-emerald-700">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                            </svg>
                            <span>Kirim Tawaran ke Chat Pembeli</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        @if ($isUserBuyer)
            <div x-show="createModalOpen" 
                 x-cloak 
                 role="dialog"
                 aria-modal="true"
                 aria-labelledby="createWantedModalTitle"
                 class="fixed inset-0 z-50 flex items-end sm:items-center justify-center bg-slate-900/60 backdrop-blur-xs p-0 sm:p-4"
                 @keydown.escape.window="createModalOpen = false">
                <div class="bg-white rounded-t-3xl sm:rounded-3xl max-w-xl w-full p-5 sm:p-8 shadow-2xl relative max-h-[88dvh] overflow-y-auto" 
                     @click.outside="createModalOpen = false">
                    
                    <div class="w-10 h-1.5 bg-slate-200 rounded-full mx-auto mb-3 sm:hidden"></div>

                    <button type="button" 
                            @click="createModalOpen = false" 
                            aria-label="Tutup modal pasang kebutuhan"
                            class="absolute right-4 top-4 w-11 h-11 flex items-center justify-center text-slate-500 hover:text-slate-800 rounded-full hover:bg-slate-100 transition cursor-pointer focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>

                    <div class="mb-4 sm:mb-5 pr-10">
                        <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-bold bg-blue-50 text-blue-700 mb-2 border border-blue-200/80">
                            <span>Wanted Ads Mahasiswa</span>
                        </div>
                        <h3 id="createWantedModalTitle" class="text-lg sm:text-xl font-black text-slate-900 tracking-tight">Pasang Iklan Kebutuhan Barang</h3>
                        <p class="text-xs text-slate-600 mt-1">
                            Tuliskan perlengkapan kos yang sedang kamu cari beserta batas budget maksimalmu.
                        </p>
                    </div>

                    <form action="{{ route('wanted-ads.store') }}" method="POST" class="space-y-4">
                        @csrf

                        <div>
                            <label for="title" class="block text-xs font-bold text-slate-700 mb-1.5">
                                Nama Barang yang Dicari <span class="text-rose-600">*</span>
                            </label>
                            <input type="text" 
                                   id="title" 
                                   name="title" 
                                   value="{{ old('title') }}" 
                                   required 
                                   maxlength="200" 
                                   placeholder="Contoh: Meja Belajar Lipat Kayu Minimalis" 
                                   class="w-full min-h-[44px] text-base sm:text-sm px-3.5 py-2.5 rounded-xl border {{ $errors->has('title') ? 'border-rose-400 bg-rose-50/40' : 'border-slate-200 bg-slate-50/60' }} text-slate-900 placeholder-slate-500 focus:bg-white focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-100">
                            @error('title')
                                <p class="text-rose-600 font-semibold text-[11px] mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                            <div>
                                <label for="category_id" class="block text-xs font-bold text-slate-700 mb-1.5">
                                    Kategori <span class="text-rose-600">*</span>
                                </label>
                                <select id="category_id" 
                                        name="category_id" 
                                        required 
                                        class="w-full min-h-[44px] text-base sm:text-sm px-3.5 py-2.5 rounded-xl border {{ $errors->has('category_id') ? 'border-rose-400 bg-rose-50/40' : 'border-slate-200 bg-slate-50/60' }} text-slate-900 focus:bg-white focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-100">
                                    <option value="">Pilih Kategori</option>
                                    @foreach ($categories as $cat)
                                        <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>
                                            {{ $cat->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('category_id')
                                    <p class="text-rose-600 font-semibold text-[11px] mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="budget_max" class="block text-xs font-bold text-slate-700 mb-1.5">
                                    Budget Maksimal (Rp) <span class="text-rose-600">*</span>
                                </label>
                                <input type="number" 
                                       id="budget_max" 
                                       name="budget_max" 
                                       inputmode="numeric"
                                       value="{{ old('budget_max') }}" 
                                       required 
                                       min="1000" 
                                       step="1000" 
                                       placeholder="Contoh: 150000" 
                                       class="w-full min-h-[44px] text-base sm:text-sm px-3.5 py-2.5 rounded-xl border {{ $errors->has('budget_max') ? 'border-rose-400 bg-rose-50/40' : 'border-slate-200 bg-slate-50/60' }} text-slate-900 placeholder-slate-500 focus:bg-white focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-100">
                                <span class="text-[11px] text-slate-600 mt-1 block">Wajib kelipatan bulat Rp 1.000</span>
                                @error('budget_max')
                                    <p class="text-rose-600 font-semibold text-[11px] mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div>
                            <label for="addresses" class="block text-xs font-bold text-slate-700 mb-1.5">
                                Area / Lokasi Kos Serah Terima <span class="text-rose-600">*</span>
                            </label>
                            <input type="text" 
                                   id="addresses" 
                                   name="addresses" 
                                   value="{{ old('addresses') }}" 
                                   required 
                                   maxlength="255" 
                                   placeholder="Contoh: Kawasan Sigura-gura, dekat Gerbang Belakang UB / Polinema" 
                                   class="w-full min-h-[44px] text-base sm:text-sm px-3.5 py-2.5 rounded-xl border {{ $errors->has('addresses') ? 'border-rose-400 bg-rose-50/40' : 'border-slate-200 bg-slate-50/60' }} text-slate-900 placeholder-slate-500 focus:bg-white focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-100">
                            @error('addresses')
                                <p class="text-rose-600 font-semibold text-[11px] mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="description" class="block text-xs font-bold text-slate-700 mb-1.5">
                                Deskripsi Spesifikasi yang Diinginkan <span class="text-rose-600">*</span>
                            </label>
                            <textarea id="description" 
                                      name="description" 
                                      rows="3" 
                                      required 
                                      placeholder="Jelaskan kriteria barang, seperti warna, ukuran, kondisi minimal yang bisa diterima..." 
                                      class="w-full text-base sm:text-sm px-3.5 py-2.5 rounded-xl border {{ $errors->has('description') ? 'border-rose-400 bg-rose-50/40' : 'border-slate-200 bg-slate-50/60' }} text-slate-900 placeholder-slate-500 focus:bg-white focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-100">{{ old('description') }}</textarea>
                            @error('description')
                                <p class="text-rose-600 font-semibold text-[11px] mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex flex-col-reverse sm:flex-row items-stretch sm:items-center justify-end gap-2.5 sm:gap-3 pt-3 border-t border-slate-100">
                            <button type="button" 
                                    @click="createModalOpen = false" 
                                    class="min-h-[44px] px-4 py-2.5 rounded-xl text-xs sm:text-sm font-bold text-slate-700 border border-slate-200 sm:border-transparent hover:bg-slate-100 transition cursor-pointer focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-slate-600">
                                Batal
                            </button>
                            <button type="submit" 
                                    class="min-h-[44px] px-6 py-2.5 rounded-xl text-xs sm:text-sm font-bold text-white bg-blue-600 hover:bg-blue-700 shadow-md shadow-blue-500/25 transition cursor-pointer focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600">
                                Publikasikan Kebutuhan (Gratis)
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        @endif

    </div>
</x-layouts.app>
