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

    <div class="space-y-8 sm:space-y-10" 
         x-data="{ 
             createModalOpen: {{ $errors->any() && $isUserBuyer ? 'true' : 'false' }},
             offerModalOpen: false,
             selectedAd: {
                 id: null,
                 title: '',
                 category: '',
                 budget_max: 0,
                 formatted_budget: '',
                 buyer_name: '',
                 buyer_campus: '',
                 buyer_phone: '',
                 addresses: '',
                 description: ''
             },
             selectedListingId: null,
             myListings: {{ \Illuminate\Support\Js::from($myListingsPayload) }},
             customOfferNote: '',
             copiedToast: false,

             openOffer(ad) {
                 this.selectedAd = ad;
                 this.selectedListingId = this.myListings.length > 0 ? this.myListings[0].id : null;
                 this.customOfferNote = '';
                 this.copiedToast = false;
                 this.offerModalOpen = true;
             },

             getSelectedListing() {
                 return this.myListings.find(l => l.id === this.selectedListingId) || null;
             },

             getOfferMessage() {
                 const listing = this.getSelectedListing();
                 let buyerName = (this.selectedAd.buyer_name || 'Kak').trim();
                 let msg = 'Halo kak ' + buyerName + '! 👋\n\n' +
                           'Saya melihat postingan kakak di papan Wanted Ads Lungsur.in sedang mencari *' + (this.selectedAd.title || '') + '* dengan pagu anggaran Rp ' + (this.selectedAd.formatted_budget || '') + '.\n\n';
                 
                 if (listing) {
                     msg += 'Kebetulan saya ada barang yang cocok dengan kebutuhan kakak:\n' +
                            '📦 Barang: *' + listing.title + '*\n' +
                            '💰 Harga: Rp ' + listing.formatted_price + '\n' +
                            '🔗 Link Iklan: ' + listing.url + '\n\n';
                 } else {
                     msg += 'Saya memiliki barang perlengkapan kos yang cocok dengan kriteria yang kakak cari.\n\n';
                 }

                 if (this.customOfferNote && this.customOfferNote.trim() !== '') {
                     msg += 'Catatan Tambahan: ' + this.customOfferNote.trim() + '\n\n';
                 }

                 msg += 'Apakah kakak berminat atau ingin cek kondisi fisiknya terlebih dahulu? Terima kasih! 🙏';
                 return msg;
             },

             getWhatsAppUrl() {
                 let phone = (this.selectedAd.buyer_phone || '').trim().replace(/[^0-9]/g, '');
                 if (phone.startsWith('0')) {
                     phone = '62' + phone.substring(1);
                 }
                 const text = encodeURIComponent(this.getOfferMessage());
                 return phone ? ('https://wa.me/' + phone + '?text=' + text) : '#';
             },

             copyMessage() {
                 if (navigator.clipboard && window.isSecureContext) {
                     navigator.clipboard.writeText(this.getOfferMessage());
                 } else {
                     const textarea = document.createElement('textarea');
                     textarea.value = this.getOfferMessage();
                     textarea.style.position = 'fixed';
                     textarea.style.opacity = '0';
                     document.body.appendChild(textarea);
                     textarea.select();
                     document.execCommand('copy');
                     document.body.removeChild(textarea);
                 }
                 this.copiedToast = true;
                 setTimeout(() => { this.copiedToast = false; }, 2500);
             }
         }">

        <!-- ========================================== -->
        <!-- 1. HERO BANNER PAPAN BARANG DICARI         -->
        <!-- ========================================== -->
        <section class="bg-gradient-to-br from-indigo-50/90 via-sky-50/50 to-blue-50/70 rounded-3xl border border-indigo-100/60 p-6 sm:p-10 relative overflow-hidden shadow-xs">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center relative z-10">
                <div class="lg:col-span-8 space-y-4">
                    
                    @if ($isUserSeller)
                        <!-- Seller Mode Active Pill -->
                        <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full text-xs font-semibold bg-amber-100/90 text-amber-900 border border-amber-300/80 shadow-2xs">
                            <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                            <span>🏷️ Mode Penjual Aktif</span>
                            <span class="text-amber-500">|</span>
                            <span>Papan Kebutuhan Pembeli</span>
                        </div>

                        <h1 class="text-2xl sm:text-4xl font-black text-slate-900 tracking-tight leading-snug">
                            Temukan Pembeli yang <br class="hidden sm:inline">
                            <span class="text-emerald-600">Mencari Perlengkapan</span> Dagangan Anda!
                        </h1>

                        <p class="text-xs sm:text-sm text-slate-600 leading-relaxed max-w-2xl">
                            Papan ini adalah wadah mahasiswa pembeli yang sedang mencari barang kos impian mereka. Sebagai penjual, Anda dapat langsung <strong>menawarkan barang jualan</strong> Anda ke pembeli yang sesuai dengan pagu anggarannya.
                        </p>

                        <div class="pt-2 flex flex-wrap items-center gap-3">
                            <a href="{{ route('katalog.pasang') }}" 
                               class="inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs sm:text-sm font-bold px-6 py-3 rounded-2xl shadow-md shadow-emerald-500/25 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                                </svg>
                                <span>+ Pasang Iklan Barang di Katalog</span>
                            </a>
                            
                            <form action="{{ route('user.switch_mode') }}" method="POST" class="inline">
                                @csrf
                                <input type="hidden" name="mode" value="buyer">
                                <button type="submit" 
                                        class="inline-flex items-center gap-2 bg-white hover:bg-slate-50 text-blue-700 text-xs sm:text-sm font-bold px-5 py-3 rounded-2xl border border-blue-200 shadow-2xs transition cursor-pointer"
                                        title="Beralih ke Mode Pembeli jika Anda ingin memasang pengumuman barang yang Anda cari">
                                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                                    </svg>
                                    <span>Beralih ke Mode Pembeli (Cari Barang)</span>
                                </button>
                            </form>
                        </div>
                    @else
                        <!-- Buyer Mode / Guest Pill -->
                        <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full text-xs font-semibold bg-indigo-100/80 text-indigo-700 border border-indigo-200/60">
                            <svg class="w-3.5 h-3.5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span>Papan Permintaan Kebutuhan Mahasiswa (Wanted Board)</span>
                        </div>

                        <h1 class="text-2xl sm:text-4xl font-black text-slate-900 tracking-tight leading-snug">
                            Lagi Cari Perlengkapan Kos? <br class="hidden sm:inline">
                            <span class="text-blue-600">Umumkan di Sini</span> dan Temukan Penjualnya!
                        </h1>

                        <p class="text-xs sm:text-sm text-slate-600 leading-relaxed max-w-2xl">
                            Tidak menemukan perabot atau perlengkapan kos yang cocok di katalog? Pasang pengumuman barang yang kamu cari secara <strong>gratis</strong>. Sesama mahasiswa yang punya barang tersebut akan langsung menawarkan barangnya kepadamu.
                        </p>

                        <div class="pt-2 flex flex-wrap items-center gap-3">
                            @auth
                                <button type="button" 
                                        @click="createModalOpen = true" 
                                        class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-xs sm:text-sm font-bold px-6 py-3 rounded-2xl shadow-md shadow-blue-500/25 transition cursor-pointer">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                                    </svg>
                                    <span>+ Pasang Kebutuhan Baru</span>
                                </button>
                            @else
                                <a href="{{ route('login') }}" 
                                   class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-xs sm:text-sm font-bold px-6 py-3 rounded-2xl shadow-md shadow-blue-500/25 transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                                    </svg>
                                    <span>Masuk untuk Pasang Kebutuhan</span>
                                </a>
                            @endauth
                            <a href="{{ url('/') }}" 
                               class="inline-flex items-center gap-2 bg-white hover:bg-slate-50 text-slate-700 text-xs sm:text-sm font-bold px-5 py-3 rounded-2xl border border-slate-200 shadow-2xs transition">
                                <span>Jelajahi Katalog Barang Dijual</span>
                                <span>&rarr;</span>
                            </a>
                        </div>
                    @endif
                </div>

                <div class="lg:col-span-4 hidden lg:flex justify-end">
                    <div class="bg-white/90 backdrop-blur-xs p-5 rounded-3xl border border-indigo-100 shadow-sm space-y-3.5 max-w-xs">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-2xl bg-indigo-100 text-indigo-600 flex items-center justify-center font-bold text-lg shrink-0">
                                📢
                            </div>
                            <div class="text-xs">
                                <div class="font-bold text-slate-900">Publikasi Terbuka</div>
                                <div class="text-slate-500">Khusus pembeli yang mencari barang kos</div>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-2xl bg-emerald-100 text-emerald-600 flex items-center justify-center font-bold text-lg shrink-0">
                                🤝
                            </div>
                            <div class="text-xs">
                                <div class="font-bold text-slate-900">Penjual Langsung Menawar</div>
                                <div class="text-slate-500">Kirim tawaran produk kos yang cocok</div>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-2xl bg-blue-100 text-blue-600 flex items-center justify-center font-bold text-lg shrink-0">
                                💰
                            </div>
                            <div class="text-xs">
                                <div class="font-bold text-slate-900">Pagu Anggaran Jelas</div>
                                <div class="text-slate-500">Transaksi transparan sesuai budget mahasiswa</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ========================================== -->
        <!-- 2. PENCARIAN & FILTER KATEGORI KEBUTUHAN   -->
        <!-- ========================================== -->
        <section class="space-y-4">
            <div class="bg-white rounded-2xl border border-slate-200/80 p-4 sm:p-5 shadow-xs">
                <form action="{{ route('wanted-ads.index') }}" method="GET" class="flex flex-col md:flex-row items-center gap-3">
                    <!-- Search Input -->
                    <div class="relative flex-1 w-full">
                        <svg class="w-4 h-4 text-slate-400 absolute left-3.5 top-3 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <input type="text" 
                               name="q" 
                               value="{{ $search ?? '' }}" 
                               placeholder="Cari barang yang sedang dicari mahasiswa (contoh: meja lipat, kasur busa)..." 
                               class="w-full pl-10 pr-4 py-2.5 text-xs sm:text-sm rounded-xl border border-slate-200 bg-slate-50/70 text-slate-800 placeholder-slate-400 focus:bg-white focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-100">
                    </div>

                    <!-- Category Select -->
                    <div class="w-full md:w-56 shrink-0">
                        <select name="category" class="w-full py-2.5 px-3 text-xs sm:text-sm rounded-xl border border-slate-200 bg-slate-50/70 text-slate-700 focus:bg-white focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-100">
                            <option value="">Semua Kategori</option>
                            @foreach ($categories as $cat)
                                <option value="{{ $cat->slug }}" {{ ($categoryParam ?? '') === $cat->slug ? 'selected' : '' }}>
                                    {{ $cat->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex items-center gap-2 w-full md:w-auto shrink-0">
                        <button type="submit" 
                                class="flex-1 md:flex-initial inline-flex items-center justify-center gap-2 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs sm:text-sm px-6 py-2.5 rounded-xl shadow-xs transition cursor-pointer">
                            <span>Cari</span>
                        </button>
                        @if (!empty($search) || !empty($categoryParam))
                            <a href="{{ route('wanted-ads.index') }}" 
                               class="inline-flex items-center justify-center text-xs font-semibold text-rose-600 hover:text-rose-700 hover:bg-rose-50 px-3.5 py-2.5 rounded-xl border border-rose-200 transition">
                                Reset
                            </a>
                        @endif
                    </div>
                </form>
            </div>
        </section>

        <!-- ========================================== -->
        <!-- 3. DAFTAR KARTU BARANG DICARI              -->
        <!-- ========================================== -->
        <section class="space-y-6">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div>
                    <h2 class="text-xl font-black text-slate-900 tracking-tight flex items-center gap-2">
                        <span>Daftar Kebutuhan Terkini</span>
                        <span class="text-xs font-semibold px-2.5 py-0.5 rounded-full bg-blue-100 text-blue-700">
                            {{ $wantedAds->total() }} Permintaan
                        </span>
                    </h2>
                    <p class="text-xs text-slate-500 mt-0.5">
                        @if ($isUserSeller)
                            Punya barang yang dicari di bawah? Klik tombol <strong>Tawari Barang Dagangan</strong> untuk menghubungkan produk jualanmu langsung ke pembeli!
                        @else
                            Forum bagi mahasiswa yang sedang membutuhkan perlengkapan kos. Penjual akan langsung menawarkan barang yang cocok.
                        @endif
                    </p>
                </div>

                @if ($isUserBuyer)
                    <button type="button" 
                            @click="createModalOpen = true" 
                            class="inline-flex items-center gap-1.5 text-xs font-bold text-blue-600 hover:text-blue-700 bg-blue-50 hover:bg-blue-100/80 px-3.5 py-2 rounded-xl transition self-start sm:self-auto cursor-pointer">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                        </svg>
                        <span>+ Pasang Kebutuhan Baru</span>
                    </button>
                @elseif ($isUserSeller)
                    <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl bg-emerald-50 text-emerald-800 text-xs font-semibold border border-emerald-200/80">
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                        <span>Anda dapat menawari pembeli di bawah ini</span>
                    </div>
                @endif
            </div>

            <!-- Grid Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                @forelse ($wantedAds as $ad)
                    <article class="bg-white rounded-2xl border border-slate-200/80 shadow-2xs hover:shadow-md transition p-5 flex flex-col justify-between space-y-4 group">
                        <!-- Top Metadata -->
                        <div class="space-y-2.5">
                            <div class="flex items-center justify-between gap-2 text-xs">
                                <span class="inline-flex items-center gap-1 font-semibold text-blue-700 bg-blue-50 px-2.5 py-0.5 rounded-lg border border-blue-200/50 text-[11px]">
                                    {{ $ad->category->name ?? 'Lainnya' }}
                                </span>
                                <span class="text-slate-400 text-[11px]">
                                    {{ $ad->created_at->diffForHumans() }}
                                </span>
                            </div>

                            <!-- Judul Barang yang Dicari -->
                            <h3 class="text-base font-bold text-slate-900 group-hover:text-blue-600 transition line-clamp-2">
                                {{ $ad->title }}
                            </h3>

                            <!-- Deskripsi Kriteria -->
                            <p class="text-xs text-slate-600 leading-relaxed line-clamp-3">
                                {{ $ad->description }}
                            </p>
                        </div>

                        <!-- Budget Box & Pencari Info -->
                        <div class="space-y-3 pt-3 border-t border-slate-100">
                            <!-- Budget Max Box -->
                            <div class="bg-emerald-50/70 border border-emerald-200/60 rounded-xl px-3.5 py-2 flex items-center justify-between">
                                <span class="text-[11px] font-semibold text-emerald-800">Pagu Anggaran Maks:</span>
                                <span class="text-sm font-black text-emerald-700">
                                    Rp {{ number_format($ad->budget_max, 0, ',', '.') }}
                                </span>
                            </div>

                            <!-- User Info & Area Kos -->
                            <div class="flex items-center justify-between text-xs text-slate-500 gap-2">
                                <div class="flex items-center gap-2 min-w-0">
                                    <div class="w-7 h-7 rounded-full bg-slate-900 text-white font-bold flex items-center justify-center text-[11px] shrink-0">
                                        {{ strtoupper(substr($ad->user->name ?? 'U', 0, 1)) }}
                                    </div>
                                    <div class="min-w-0">
                                        <div class="font-bold text-slate-800 truncate text-[11px]">{{ $ad->user->name ?? 'Mahasiswa' }}</div>
                                        <div class="text-[10px] text-blue-600 truncate">{{ $ad->user->campus ?? 'Mahasiswa Malang' }}</div>
                                    </div>
                                </div>

                                <div class="text-right text-[10px] text-slate-400 shrink-0 max-w-[120px]" title="{{ $ad->addresses }}">
                                    📍 <span class="truncate block">{{ $ad->addresses }}</span>
                                </div>
                            </div>

                            <!-- Action Buttons Berdasarkan Role / Mode -->
                            <div class="pt-1 flex items-center gap-2">
                                @if (auth()->id() === (int) $ad->user_id || (auth()->check() && auth()->user()->role === 'admin'))
                                    <!-- Pemilik iklan atau Admin: Hapus Postingan -->
                                    <form action="{{ route('wanted-ads.destroy', $ad->id) }}" method="POST" class="w-full" onsubmit="return confirm('Apakah Anda yakin ingin menghapus postingan kebutuhan ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="w-full py-2 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-600 font-bold text-xs transition cursor-pointer">
                                            Hapus Postingan Ini
                                        </button>
                                    </form>
                                @elseif ($isUserSeller)
                                    <!-- Penjual: Tawari Barang Dagangan ke Pembeli -->
                                    <button type="button" 
                                            @click="openOffer({
                                                id: {{ $ad->id }},
                                                title: {{ \Illuminate\Support\Js::from($ad->title) }},
                                                category: {{ \Illuminate\Support\Js::from($ad->category->name ?? 'Lainnya') }},
                                                budget_max: {{ (float) $ad->budget_max }},
                                                formatted_budget: {{ \Illuminate\Support\Js::from(number_format($ad->budget_max, 0, ',', '.')) }},
                                                buyer_name: {{ \Illuminate\Support\Js::from($ad->user->name ?? 'Mahasiswa') }},
                                                buyer_campus: {{ \Illuminate\Support\Js::from($ad->user->campus ?? 'Mahasiswa Malang') }},
                                                buyer_phone: {{ \Illuminate\Support\Js::from($ad->user->phone ?? '') }},
                                                addresses: {{ \Illuminate\Support\Js::from($ad->addresses) }},
                                                description: {{ \Illuminate\Support\Js::from($ad->description) }}
                                            })"
                                            class="w-full text-center py-2.5 px-3 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs transition flex items-center justify-center gap-2 shadow-sm shadow-emerald-600/20 cursor-pointer">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                                        </svg>
                                        <span>Tawari Barang Dagangan</span>
                                    </button>
                                @else
                                    <!-- Pembeli Umum / Pengunjung: Cek Barang Serupa di Katalog -->
                                    <a href="{{ route('home', ['q' => $ad->title]) }}" 
                                       class="w-full text-center py-2 rounded-xl bg-blue-50 hover:bg-blue-600 text-blue-600 hover:text-white font-bold text-xs transition flex items-center justify-center gap-1.5 shadow-2xs">
                                        <span>Cek Barang Serupa di Katalog</span>
                                        <span>&rarr;</span>
                                    </a>
                                @endif
                            </div>
                        </div>
                    </article>
                @empty
                    <div class="col-span-full rounded-2xl border border-dashed border-slate-300 bg-white p-12 text-center space-y-3">
                        <div class="w-12 h-12 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center mx-auto text-xl font-bold">
                            📦
                        </div>
                        <h3 class="text-base font-bold text-slate-800">Belum Ada Permintaan Barang</h3>
                        <p class="text-xs text-slate-500 max-w-md mx-auto">
                            Saat ini belum ada pengumuman barang yang dicari mahasiswa.
                        </p>
                        @if ($isUserBuyer)
                            <button type="button" 
                                    @click="createModalOpen = true" 
                                    class="inline-flex items-center gap-1.5 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs px-5 py-2.5 rounded-xl shadow-xs transition cursor-pointer">
                                <span>+ Pasang Kebutuhan Sekarang</span>
                            </button>
                        @endif
                    </div>
                @endforelse
            </div>

            <!-- Pagination -->
            <div class="pt-4">
                {{ $wantedAds->links() }}
            </div>
        </section>

        <!-- ========================================== -->
        <!-- 4. MODAL TAWARI BARANG DAGANGAN (PENJUAL)  -->
        <!-- ========================================== -->
        <div x-show="offerModalOpen" 
             x-cloak 
             class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-xs p-4 overflow-y-auto"
             @keydown.escape.window="offerModalOpen = false">
            <div class="bg-white rounded-3xl max-w-2xl w-full p-6 sm:p-8 shadow-2xl relative my-8 border border-slate-100" 
                 @click.outside="offerModalOpen = false">
                
                <!-- Close Button -->
                <button type="button" 
                        @click="offerModalOpen = false" 
                        class="absolute right-5 top-5 text-slate-400 hover:text-slate-600 p-1.5 rounded-full hover:bg-slate-100 transition cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>

                <!-- Modal Header -->
                <div class="mb-5">
                    <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-700 mb-2 border border-emerald-200/60">
                        <span>🤝 Tawari Barang Dagangan</span>
                    </div>
                    <h3 class="text-xl font-black text-slate-900 tracking-tight">Tawarkan Produk ke Pembeli</h3>
                    <p class="text-xs text-slate-500 mt-1">
                        Pilih barang dagangan Anda yang sesuai dengan kriteria pembeli dan hubungi langsung melalui WhatsApp.
                    </p>
                </div>

                <div class="space-y-5">
                    <!-- Ringkasan Kebutuhan Pembeli -->
                    <div class="bg-slate-50 border border-slate-200/80 rounded-2xl p-4 space-y-3">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <span class="inline-block text-[10px] font-bold px-2 py-0.5 rounded-md bg-blue-100 text-blue-800 mb-1" x-text="selectedAd.category"></span>
                                <h4 class="text-sm font-black text-slate-900" x-text="selectedAd.title"></h4>
                            </div>
                            <div class="text-right shrink-0">
                                <div class="text-[10px] text-slate-500">Pagu Anggaran Maks:</div>
                                <div class="text-xs sm:text-sm font-black text-emerald-700">
                                    Rp <span x-text="selectedAd.formatted_budget"></span>
                                </div>
                            </div>
                        </div>

                        <div class="text-xs text-slate-600 bg-white p-3 rounded-xl border border-slate-200/60 space-y-1.5">
                            <div class="font-semibold text-slate-700">Kriteria / Spesifikasi Dicari:</div>
                            <p class="italic text-slate-600 leading-relaxed" x-text="selectedAd.description"></p>
                        </div>

                        <div class="flex flex-wrap items-center justify-between text-[11px] text-slate-500 gap-2 pt-1">
                            <div>
                                👤 Pembeli: <span class="font-bold text-slate-800" x-text="selectedAd.buyer_name"></span> (<span x-text="selectedAd.buyer_campus"></span>)
                            </div>
                            <div>
                                📍 Lokasi: <span class="font-medium text-slate-700" x-text="selectedAd.addresses"></span>
                            </div>
                        </div>
                    </div>

                    <!-- Pilihan Barang Dagangan Milik Penjual -->
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <label class="block text-xs font-bold text-slate-800">
                                Pilih Barang Dagangan Anda yang Ditawarkan:
                            </label>
                            <span class="text-[11px] text-slate-400" x-text="myListings.length + ' Barang Tersedia'"></span>
                        </div>

                        <template x-if="myListings.length === 0">
                            <div class="rounded-2xl border border-dashed border-amber-300 bg-amber-50/60 p-4 text-center space-y-2">
                                <p class="text-xs text-amber-900 font-medium">
                                    Anda belum memiliki iklan barang yang berstatus <strong>Tersedia (Available)</strong> di katalog.
                                </p>
                                <a href="{{ route('katalog.pasang') }}" 
                                   class="inline-flex items-center gap-1.5 text-xs font-bold bg-amber-600 hover:bg-amber-700 text-white px-4 py-2 rounded-xl transition">
                                    <span>+ Pasang Iklan Barang di Katalog Sekarang</span>
                                </a>
                            </div>
                        </template>

                        <template x-if="myListings.length > 0">
                            <div class="max-h-52 overflow-y-auto space-y-2 pr-1">
                                <template x-for="item in myListings" :key="item.id">
                                    <div @click="selectedListingId = item.id"
                                         :class="selectedListingId === item.id 
                                            ? 'border-emerald-500 bg-emerald-50/50 ring-2 ring-emerald-500/20' 
                                            : 'border-slate-200 hover:border-slate-300 bg-white'"
                                         class="p-3 rounded-2xl border transition cursor-pointer flex items-center justify-between gap-3">
                                        
                                        <div class="flex items-center gap-3 min-w-0">
                                            <input type="radio" 
                                                   :value="item.id" 
                                                   :checked="selectedListingId === item.id"
                                                   class="w-4 h-4 text-emerald-600 border-slate-300 focus:ring-emerald-500 shrink-0">
                                            
                                            <template x-if="item.photo_url">
                                                <img :src="item.photo_url" :alt="item.title" class="w-12 h-12 rounded-xl object-cover border border-slate-200 shrink-0">
                                            </template>
                                            <template x-if="!item.photo_url">
                                                <div class="w-12 h-12 rounded-xl bg-slate-100 text-slate-400 flex items-center justify-center font-bold text-xs shrink-0">
                                                    📦
                                                </div>
                                            </template>

                                            <div class="min-w-0">
                                                <div class="text-xs font-bold text-slate-900 truncate" x-text="item.title"></div>
                                                <div class="text-[11px] text-slate-500" x-text="item.category_name"></div>
                                                <div class="text-xs font-black text-emerald-700">
                                                    Rp <span x-text="item.formatted_price"></span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="text-right shrink-0">
                                            <template x-if="item.price <= selectedAd.budget_max">
                                                <span class="inline-block text-[10px] font-bold px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800">
                                                    ✅ Sesuai Budget
                                                </span>
                                            </template>
                                            <template x-if="item.price > selectedAd.budget_max">
                                                <span class="inline-block text-[10px] font-bold px-2 py-0.5 rounded-full bg-amber-100 text-amber-800">
                                                    ⚠️ Di Atas Pagu
                                                </span>
                                            </template>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </template>
                    </div>

                    <!-- Catatan Tambahan (Opsional) -->
                    <div>
                        <label for="offer_note" class="block text-xs font-bold text-slate-700 mb-1">
                            Catatan Tambahan untuk Pembeli (Opsional)
                        </label>
                        <input type="text" 
                               id="offer_note" 
                               x-model="customOfferNote" 
                               placeholder="Contoh: Kondisi 95% mulus kak, bisa COD langsung di gerbang kampus sore ini..." 
                               class="w-full text-xs sm:text-sm px-3.5 py-2.5 rounded-xl border border-slate-200 bg-slate-50/60 focus:bg-white focus:outline-none focus:border-emerald-600 focus:ring-1 focus:ring-emerald-100">
                    </div>

                    <!-- Preview Pesan Penawaran Otomatis -->
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <label class="block text-xs font-bold text-slate-700">
                                Preview Template Pesan WhatsApp:
                            </label>
                            <button type="button" 
                                    @click="copyMessage()" 
                                    class="text-[11px] font-bold text-blue-600 hover:text-blue-700 flex items-center gap-1 cursor-pointer">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                </svg>
                                <span x-text="copiedToast ? 'Tersalin ke Clipboard! ✅' : 'Salin Pesan'"></span>
                            </button>
                        </div>
                        <div class="bg-slate-900 text-slate-100 p-3.5 rounded-2xl text-xs font-mono leading-relaxed whitespace-pre-wrap max-h-36 overflow-y-auto border border-slate-800"
                             x-text="getOfferMessage()"></div>
                    </div>

                    <!-- Footer / Action Buttons -->
                    <div class="pt-3 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-3">
                        <button type="button" 
                                @click="offerModalOpen = false" 
                                class="w-full sm:w-auto px-4 py-2.5 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-100 transition cursor-pointer text-center">
                            Tutup
                        </button>

                        <div class="w-full sm:w-auto flex flex-wrap items-center gap-2">
                            <button type="button" 
                                    @click="copyMessage()" 
                                    class="flex-1 sm:flex-initial inline-flex items-center justify-center gap-1.5 px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition cursor-pointer">
                                <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                </svg>
                                <span x-text="copiedToast ? 'Tersalin! ✅' : 'Salin Teks'"></span>
                            </button>

                            <template x-if="selectedAd.buyer_phone && selectedAd.buyer_phone.trim() !== ''">
                                <a :href="getWhatsAppUrl()" 
                                   target="_blank" 
                                   rel="noopener noreferrer" 
                                   class="flex-1 sm:flex-initial inline-flex items-center justify-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs sm:text-sm px-6 py-2.5 rounded-xl shadow-md shadow-emerald-600/25 transition">
                                    <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24">
                                        <path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.77-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.299.045-.677.063-1.092-.069-.252-.08-.575-.187-.988-.365-1.739-.751-2.874-2.502-2.961-2.617-.087-.116-.708-.94-.708-1.793s.448-1.273.607-1.446c.159-.173.346-.217.462-.217l.332.006c.106.005.249-.04.39.298.144.347.491 1.2.534 1.287.043.087.072.188.014.304-.058.116-.087.188-.173.289l-.26.304c-.087.086-.177.18-.076.354.101.174.449.741.964 1.201.662.591 1.221.774 1.394.86.173.086.275.072.376-.044.101-.116.433-.506.549-.68.116-.173.231-.145.39-.086s1.011.477 1.184.564.289.13.332.202c.045.072.045.419-.1.824zm-3.423-14.416c-6.627 0-12 5.373-12 12 0 2.155.57 4.178 1.564 5.927l-1.564 5.829 6.012-1.577c1.706.945 3.678 1.488 5.788 1.488 6.627 0 12-5.373 12-12s-5.373-12-12-12z"/>
                                    </svg>
                                    <span>Kirim Tawaran via WhatsApp</span>
                                </a>
                            </template>

                            <template x-if="!selectedAd.buyer_phone || selectedAd.buyer_phone.trim() === ''">
                                <span class="text-[11px] text-amber-700 bg-amber-50 border border-amber-200 px-3 py-2 rounded-xl">
                                    No. WA pembeli belum ada di profil
                                </span>
                            </template>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ========================================== -->
        <!-- 5. MODAL FORM PASANG KEBUTUHAN (PEMBELI)   -->
        <!-- ========================================== -->
        @if ($isUserBuyer)
            <div x-show="createModalOpen" 
                 x-cloak 
                 class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 p-4 overflow-y-auto"
                 @keydown.escape.window="createModalOpen = false">
                <div class="bg-white rounded-3xl max-w-xl w-full p-6 sm:p-8 shadow-2xl relative my-8" 
                     @click.outside="createModalOpen = false">
                    
                    <!-- Close Button -->
                    <button type="button" 
                            @click="createModalOpen = false" 
                            class="absolute right-5 top-5 text-slate-400 hover:text-slate-600 p-1.5 rounded-full hover:bg-slate-100 transition cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>

                    <div class="mb-5">
                        <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-bold bg-blue-50 text-blue-700 mb-2">
                            <span>📢 Wanted Ads Mahasiswa</span>
                        </div>
                        <h3 class="text-xl font-black text-slate-900 tracking-tight">Pasang Iklan Kebutuhan Barang</h3>
                        <p class="text-xs text-slate-500 mt-1">
                            Tuliskan perlengkapan kos yang sedang kamu cari beserta batas pagu anggaran maksimalmu.
                        </p>
                    </div>

                    <form action="{{ route('wanted-ads.store') }}" method="POST" class="space-y-4">
                        @csrf

                        <!-- Judul Barang -->
                        <div>
                            <label for="title" class="block text-xs font-bold text-slate-700 mb-1">
                                Nama Barang yang Dicari <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" 
                                   id="title" 
                                   name="title" 
                                   value="{{ old('title') }}" 
                                   required 
                                   maxlength="200" 
                                   placeholder="Contoh: Meja Belajar Lipat Kayu Minimalis" 
                                   class="w-full text-xs sm:text-sm px-3.5 py-2.5 rounded-xl border {{ $errors->has('title') ? 'border-rose-400 bg-rose-50/40' : 'border-slate-200 bg-slate-50/60' }} focus:bg-white focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-100">
                            @error('title')
                                <p class="text-rose-500 text-[11px] mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Kategori & Budget Max -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                            <!-- Kategori -->
                            <div>
                                <label for="category_id" class="block text-xs font-bold text-slate-700 mb-1">
                                    Kategori <span class="text-rose-500">*</span>
                                </label>
                                <select id="category_id" 
                                        name="category_id" 
                                        required 
                                        class="w-full text-xs sm:text-sm px-3.5 py-2.5 rounded-xl border {{ $errors->has('category_id') ? 'border-rose-400 bg-rose-50/40' : 'border-slate-200 bg-slate-50/60' }} focus:bg-white focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-100">
                                    <option value="">-- Pilih Kategori --</option>
                                    @foreach ($categories as $cat)
                                        <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>
                                            {{ $cat->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('category_id')
                                    <p class="text-rose-500 text-[11px] mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Pagu Anggaran Maksimal -->
                            <div>
                                <label for="budget_max" class="block text-xs font-bold text-slate-700 mb-1">
                                    Pagu Anggaran Maks (Rp) <span class="text-rose-500">*</span>
                                </label>
                                <input type="number" 
                                       id="budget_max" 
                                       name="budget_max" 
                                       value="{{ old('budget_max') }}" 
                                       required 
                                       min="1000" 
                                       step="1000" 
                                       placeholder="Contoh: 150000" 
                                       class="w-full text-xs sm:text-sm px-3.5 py-2.5 rounded-xl border {{ $errors->has('budget_max') ? 'border-rose-400 bg-rose-50/40' : 'border-slate-200 bg-slate-50/60' }} focus:bg-white focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-100">
                                <span class="text-[10px] text-slate-400 mt-1 block">Wajib kelipatan bulat Rp1.000</span>
                                @error('budget_max')
                                    <p class="text-rose-500 text-[11px] mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Area / Lokasi Kos -->
                        <div>
                            <label for="addresses" class="block text-xs font-bold text-slate-700 mb-1">
                                Area / Lokasi Kos Serah Terima <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" 
                                   id="addresses" 
                                   name="addresses" 
                                   value="{{ old('addresses') }}" 
                                   required 
                                   maxlength="255" 
                                   placeholder="Contoh: Kawasan Sigura-gura, dekat Gerbang Belakang UB / Polinema" 
                                   class="w-full text-xs sm:text-sm px-3.5 py-2.5 rounded-xl border {{ $errors->has('addresses') ? 'border-rose-400 bg-rose-50/40' : 'border-slate-200 bg-slate-50/60' }} focus:bg-white focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-100">
                            @error('addresses')
                                <p class="text-rose-500 text-[11px] mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Deskripsi Spesifikasi / Kriteria -->
                        <div>
                            <label for="description" class="block text-xs font-bold text-slate-700 mb-1">
                                Deskripsi Spesifikasi yang Diinginkan <span class="text-rose-500">*</span>
                            </label>
                            <textarea id="description" 
                                      name="description" 
                                      rows="3" 
                                      required 
                                      placeholder="Jelaskan kriteria barang, seperti warna, ukuran, kondisi minimal yang bisa diterima, dsb..." 
                                      class="w-full text-xs sm:text-sm px-3.5 py-2.5 rounded-xl border {{ $errors->has('description') ? 'border-rose-400 bg-rose-50/40' : 'border-slate-200 bg-slate-50/60' }} focus:bg-white focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-100">{{ old('description') }}</textarea>
                            @error('description')
                                <p class="text-rose-500 text-[11px] mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-100">
                            <button type="button" 
                                    @click="createModalOpen = false" 
                                    class="px-4 py-2.5 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-100 transition cursor-pointer">
                                Batal
                            </button>
                            <button type="submit" 
                                    class="px-6 py-2.5 rounded-xl text-xs font-bold text-white bg-blue-600 hover:bg-blue-700 shadow-md shadow-blue-500/25 transition cursor-pointer">
                                Publikasikan Kebutuhan (Gratis)
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        @endif

    </div>
</x-layouts.app>
