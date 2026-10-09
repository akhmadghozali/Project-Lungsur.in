<footer class="bg-slate-900 text-slate-300 text-sm mt-auto border-t border-slate-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
            <!-- Brand & Info -->
            <div class="space-y-3 md:col-span-1">
                <div class="flex items-center gap-2">
                    <span class="w-7 h-7 rounded-lg bg-emerald-600 text-white font-black flex items-center justify-center text-xs">L</span>
                    <span class="font-bold text-lg text-white tracking-tight">Lungsur<span class="text-emerald-400">.in</span></span>
                </div>
                <p class="text-xs text-slate-400 leading-relaxed">
                    Platform katalog perabot kos dan lelang terbuka khusus komunitas mahasiswa di Kota Malang. Menghubungkan sesama civitas akademika secara aman dan terpercaya.
                </p>
            </div>

            <!-- Navigasi Cepat -->
            <div>
                <h4 class="text-xs font-bold text-white uppercase tracking-wider mb-3">Layanan</h4>
                <ul class="space-y-2 text-xs">
                    <li><a href="{{ Route::has('listings.index') ? route('listings.index') : url('/katalog') }}" class="text-slate-400 hover:text-white transition">Katalog Barang Bekas</a></li>
                    <li><a href="{{ Route::has('auctions.index') ? route('auctions.index') : url('/lelang') }}" class="text-slate-400 hover:text-white transition">Papan Lelang Terbuka</a></li>
                    <li><a href="{{ Route::has('wanted-ads.index') ? route('wanted-ads.index') : url('/wanted-ads') }}" class="text-slate-400 hover:text-white transition">Pencarian Kebutuhan (Wanted Ads)</a></li>
                </ul>
            </div>

            <!-- Komunitas Kampus Malang -->
            <div>
                <h4 class="text-xs font-bold text-white uppercase tracking-wider mb-3">Wilayah Jangkauan</h4>
                <p class="text-xs text-slate-400 leading-relaxed">
                    Khusus area kos mahasiswa Kota Malang: Polinema, Universitas Brawijaya (UB), Universitas Negeri Malang (UM), UMM, UIN Maliki, dan sekitarnya.
                </p>
            </div>

            <!-- Panduan Transaksi & COD -->
            <div>
                <h4 class="text-xs font-bold text-white uppercase tracking-wider mb-3">Keamanan Transaksi</h4>
                <p class="text-xs text-slate-400 leading-relaxed">
                    Sistem memfasilitasi temu janji & obrolan. Transaksi pembayaran barang lelang dan jual-beli murni diselesaikan mandiri di tempat saat serah terima fisik (<em class="text-slate-300 not-italic font-semibold">Cash on Delivery / COD</em>).
                </p>
            </div>
        </div>

        <div class="border-t border-slate-800 mt-10 pt-6 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-slate-400">
            <p>&copy; {{ date('Y') }} Lungsur.in &bull; Project Based Learning (PBL) Kelompok 5 - SIB Polinema.</p>
            <p class="text-slate-400">D-IV Sistem Informasi Bisnis, Jurusan Teknologi Informasi</p>
        </div>
    </div>
</footer>
