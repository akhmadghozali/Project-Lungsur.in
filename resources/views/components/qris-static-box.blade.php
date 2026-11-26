@props(['amount' => 5000, 'accent' => 'amber'])

@php
    $centerFill = $accent === 'emerald' ? '#047857' : '#d97706';
    $qrisUid = 'qris-svg-' . uniqid();
@endphp

<div class="bg-gradient-to-b from-slate-50 to-white border border-slate-200 rounded-3xl p-5 sm:p-6 text-center shadow-xs"
     x-data="{
         downloading: false,
         downloadQrisPng() {
             const svgEl = this.$refs.qrisSvg;
             if (!svgEl) return;
             this.downloading = true;

             const serializer = new XMLSerializer();
             let svgString = serializer.serializeToString(svgEl);
             if (!svgString.match(/^<svg[^>]+xmlns=/)) {
                 svgString = svgString.replace(/^<svg/, '<svg xmlns=\"http://www.w3.org/2000/svg\"');
             }

             const canvas = document.createElement('canvas');
             canvas.width = 600;
             canvas.height = 720;
             const ctx = canvas.getContext('2d');

             ctx.fillStyle = '#ffffff';
             ctx.fillRect(0, 0, canvas.width, canvas.height);

             ctx.fillStyle = '#0f172a';
             ctx.font = 'bold 28px sans-serif';
             ctx.textAlign = 'center';
             ctx.fillText('QRIS STATIS LUNGSUR.IN', 300, 56);

             ctx.fillStyle = '#475569';
             ctx.font = 'bold 18px sans-serif';
             ctx.fillText('NMID: ID1024098271', 300, 88);

             const img = new Image();
             const svgBlob = new Blob([svgString], { type: 'image/svg+xml;charset=utf-8' });
             const url = URL.createObjectURL(svgBlob);

             img.onload = () => {
                 ctx.drawImage(img, 90, 115, 420, 420);
                 URL.revokeObjectURL(url);

                 ctx.fillStyle = '#0f172a';
                 ctx.font = 'bold 26px monospace';
                 ctx.fillText('Nominal: Rp {{ number_format($amount, 0, ',', '.') }}', 300, 595);

                 ctx.fillStyle = '#475569';
                 ctx.font = '16px sans-serif';
                 ctx.fillText('Scan dari Galeri aplikasi M-Banking / e-Wallet', 300, 635);

                 const pngUrl = canvas.toDataURL('image/png');
                 const link = document.createElement('a');
                 link.download = 'QRIS-Lungsurin-Rp{{ $amount }}.png';
                 link.href = pngUrl;
                 document.body.appendChild(link);
                 link.click();
                 document.body.removeChild(link);
                 this.downloading = false;
             };
             img.onerror = () => {
                 URL.revokeObjectURL(url);
                 this.downloading = false;
             };
             img.src = url;
         }
     }">
    <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-slate-900 text-white text-[10px] font-black tracking-widest uppercase mb-4 shadow-xs">
        <span>QRIS STATIS RESMI</span>
    </div>

    <div class="w-56 mx-auto bg-white p-4 rounded-2xl border-2 border-slate-200 shadow-sm flex flex-col items-center">
        <div class="w-full flex items-center justify-between border-b border-slate-100 pb-2 mb-3">
            <span class="text-xs font-black tracking-tight text-slate-900">QRIS</span>
            <span class="text-[10px] font-bold text-slate-600 uppercase tracking-wider">NMID: ID1024098271</span>
        </div>

        <div class="p-2 bg-white rounded-xl">
            <svg x-ref="qrisSvg" id="{{ $qrisUid }}" class="w-36 h-36 text-slate-900" viewBox="0 0 100 100" fill="currentColor">
                <rect x="0" y="0" width="100" height="100" fill="#ffffff"/>
                <rect x="5" y="5" width="30" height="30" rx="3" fill="#0f172a"/>
                <rect x="10" y="10" width="20" height="20" rx="1" fill="#ffffff"/>
                <rect x="15" y="15" width="10" height="10" rx="1" fill="#0f172a"/>

                <rect x="65" y="5" width="30" height="30" rx="3" fill="#0f172a"/>
                <rect x="70" y="10" width="20" height="20" rx="1" fill="#ffffff"/>
                <rect x="75" y="15" width="10" height="10" rx="1" fill="#0f172a"/>

                <rect x="5" y="65" width="30" height="30" rx="3" fill="#0f172a"/>
                <rect x="10" y="70" width="20" height="20" rx="1" fill="#ffffff"/>
                <rect x="15" y="75" width="10" height="10" rx="1" fill="#0f172a"/>

                <rect x="42" y="8" width="5" height="5" fill="#0f172a"/>
                <rect x="52" y="8" width="5" height="5" fill="#0f172a"/>
                <rect x="42" y="18" width="5" height="5" fill="#0f172a"/>
                <rect x="47" y="25" width="5" height="5" fill="#0f172a"/>
                <rect x="10" y="45" width="5" height="5" fill="#0f172a"/>
                <rect x="22" y="45" width="5" height="5" fill="#0f172a"/>
                <rect x="45" y="45" width="10" height="10" rx="2" fill="{{ $centerFill }}"/>
                <rect x="60" y="45" width="5" height="5" fill="#0f172a"/>
                <rect x="75" y="45" width="8" height="8" fill="#0f172a"/>
                <rect x="88" y="45" width="5" height="5" fill="#0f172a"/>
                <rect x="42" y="65" width="5" height="5" fill="#0f172a"/>
                <rect x="52" y="65" width="5" height="5" fill="#0f172a"/>
                <rect x="65" y="65" width="5" height="5" fill="#0f172a"/>
                <rect x="78" y="65" width="5" height="5" fill="#0f172a"/>
                <rect x="42" y="78" width="8" height="8" fill="#0f172a"/>
                <rect x="58" y="78" width="5" height="5" fill="#0f172a"/>
                <rect x="70" y="78" width="10" height="10" fill="#0f172a"/>
                <rect x="88" y="78" width="5" height="5" fill="#0f172a"/>
            </svg>
        </div>

        <div class="mt-2 text-center">
            <span class="text-[11px] text-slate-600 font-semibold block">Nominal Transfer Tepat:</span>
            <span class="text-sm font-black text-slate-900 tracking-tight font-mono">Rp {{ number_format($amount, 0, ',', '.') }}</span>
        </div>
    </div>

    <div class="mt-4 flex flex-col items-center gap-2">
        <button type="button"
                @click="downloadQrisPng()"
                class="min-h-[44px] inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl bg-white hover:bg-slate-100 text-slate-900 border border-slate-300 text-xs font-bold shadow-2xs transition cursor-pointer focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600">
            <svg class="w-4 h-4 text-blue-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
            </svg>
            <span x-text="downloading ? 'Menyiapkan Gambar QRIS...' : 'Simpan Gambar QRIS ke Galeri HP'"></span>
        </button>
        <p class="text-[11px] text-slate-600 max-w-xs">
            Buka dari HP? Simpan gambar QRIS di atas lalu pilih gambar dari galeri saat scan di M-Banking atau e-Wallet.
        </p>
    </div>

    <div class="mt-4 flex flex-wrap items-center justify-center gap-2 text-[11px] text-slate-700 font-semibold">
        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-slate-100 text-slate-800">✓ GoPay</span>
        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-slate-100 text-slate-800">✓ OVO</span>
        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-slate-100 text-slate-800">✓ DANA</span>
        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-slate-100 text-slate-800">✓ ShopeePay</span>
        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-slate-100 text-slate-800">✓ Mobile Banking (BCA / Mandiri / BRI dll)</span>
    </div>
</div>
