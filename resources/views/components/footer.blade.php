<footer class="bg-slate-950 text-slate-300 text-sm mt-auto border-t border-slate-800/80 pt-14 pb-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-12 gap-8 lg:gap-10">
            <!-- Kolom 1: Brand & Info (col-span-4) -->
            <div class="space-y-4 md:col-span-4">
                <a href="{{ url('/') }}" class="inline-flex items-center gap-2.5 group">
                    <!-- Icon Logo Biru Panah Sirkular + Toga Kuning -->
                    <div class="w-9 h-9 rounded-2xl bg-blue-600 flex items-center justify-center shadow-md shadow-blue-500/20 relative overflow-hidden group-hover:bg-blue-700 transition shrink-0">
                        <svg class="w-5 h-5 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 12a9 9 0 0 0-9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/>
                            <path d="M3 3v5h5"/>
                            <path d="M3 12a9 9 0 0 0 9 9 9.75 9.75 0 0 0 6.74-2.74L21 16"/>
                            <path d="M21 21v-5h-5"/>
                            <path d="M12 9l5 2.5-5 2.5-5-2.5 5-2.5z" fill="#FBBF24" stroke="#FBBF24" stroke-width="1.5"/>
                            <path d="M9.5 13v2.2c0 .8 1.1 1.5 2.5 1.5s2.5-.7 2.5-1.5V13" stroke="#FBBF24" stroke-width="1.5"/>
                        </svg>
                    </div>
                    <span class="text-xl font-black text-white tracking-tight">
                        Lungsur<span class="text-blue-500">.in</span>
                    </span>
                </a>
                <p class="text-xs text-slate-400 leading-relaxed max-w-sm">
                    Platform pasang iklan, wanted ads, dan lelang barang khusus mahasiswa di Malang. Tawarkan barangmu atau cari yang kamu butuhkan, lalu deal langsung.
                </p>
            </div>

            <!-- Kolom 2: Navigasi (col-span-2) -->
            <div class="md:col-span-2">
                <h4 class="text-xs font-bold text-white uppercase tracking-wider mb-4">Jelajahi</h4>
                <ul class="space-y-2.5 text-xs">
                    <li>
                        <a href="{{ Route::has('listings.index') ? route('listings.index') : url('/katalog') }}" class="text-slate-400 hover:text-white transition inline-block">
                            Katalog Iklan
                        </a>
                    </li>
                    <li>
                        <a href="{{ Route::has('auctions.index') ? route('auctions.index') : url('/lelang') }}" class="text-slate-400 hover:text-white transition inline-block">
                            Lelang Barang
                        </a>
                    </li>
                    <li>
                        <a href="{{ Route::has('wanted-ads.index') ? route('wanted-ads.index') : url('/wanted-ads') }}" class="text-slate-400 hover:text-white transition inline-block">
                            Wanted Ads (Dicari)
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Kolom 3: Komunitas Kampus Malang (col-span-3) -->
            <div class="md:col-span-3">
                <h4 class="text-xs font-bold text-white uppercase tracking-wider mb-4">Komunitas Kampus Malang</h4>
                <div class="flex flex-wrap gap-2">
                    <span class="inline-flex items-center px-3 py-1 rounded-full bg-white/5 border border-white/10 text-slate-300 font-medium text-xs hover:bg-white/10 transition">Polinema</span>
                    <span class="inline-flex items-center px-3 py-1 rounded-full bg-white/5 border border-white/10 text-slate-300 font-medium text-xs hover:bg-white/10 transition">UB</span>
                    <span class="inline-flex items-center px-3 py-1 rounded-full bg-white/5 border border-white/10 text-slate-300 font-medium text-xs hover:bg-white/10 transition">UM</span>
                    <span class="inline-flex items-center px-3 py-1 rounded-full bg-white/5 border border-white/10 text-slate-300 font-medium text-xs hover:bg-white/10 transition">UMM</span>
                    <span class="inline-flex items-center px-3 py-1 rounded-full bg-white/5 border border-white/10 text-slate-300 font-medium text-xs hover:bg-white/10 transition">UIN Maliki</span>
                    <span class="inline-flex items-center px-3 py-1 rounded-full bg-white/5 border border-white/10 text-slate-300 font-medium text-xs hover:bg-white/10 transition">&amp; Kampus Malang Lainnya</span>
                </div>
            </div>

            <!-- Kolom 4: Disclaimer Transaksi (col-span-3) -->
            <div class="md:col-span-3">
                <div class="bg-white/[0.03] border border-white/10 rounded-2xl p-4 sm:p-5 space-y-2">
                    <h4 class="text-xs font-bold text-white uppercase tracking-wider flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5 text-blue-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>Sistem Deal &amp; COD</span>
                    </h4>
                    <p class="text-xs text-slate-400 leading-relaxed">
                        Lungsur.in hanya wadah pasang iklan &amp; lelang (tanpa pembayaran di dalam aplikasi). Negosiasi dan COD dilakukan langsung antar mahasiswa.
                    </p>
                </div>
            </div>
        </div>

        <!-- Bottom Bar (Copyright) -->
        <div class="border-t border-white/10 mt-12 pt-6 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-slate-400">
            <p>&copy; 2026 Lungsur.in &bull; Project Based Learning (PBL) Kelompok 5 - SIB Polinema.</p>
            <p class="text-slate-400">D-IV Sistem Informasi Bisnis, Jurusan Teknologi Informasi</p>
        </div>
    </div>
</footer>
