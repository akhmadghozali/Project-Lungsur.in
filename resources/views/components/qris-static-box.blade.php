@props(['amount' => 5000, 'accent' => 'amber'])

@php
    $centerFill = $accent === 'emerald' ? '#10b981' : '#f59e0b';
@endphp

<div class="bg-gradient-to-b from-slate-50 to-white border border-slate-200 rounded-3xl p-6 text-center shadow-xs">
    <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-slate-900 text-white text-[10px] font-black tracking-widest uppercase mb-4 shadow-xs">
        <span>QRIS STATIS RESMI</span>
    </div>

    {{-- QR Code Card --}}
    <div class="w-56 mx-auto bg-white p-4 rounded-2xl border-2 border-slate-200 shadow-sm flex flex-col items-center">
        {{-- Header QRIS Style --}}
        <div class="w-full flex items-center justify-between border-b border-slate-100 pb-2 mb-3">
            <span class="text-xs font-black tracking-tight text-slate-800">QRIS</span>
            <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider">NMID: ID1024098271</span>
        </div>

        {{-- SVG QR Code Visual --}}
        <div class="p-2 bg-white rounded-xl">
            <svg class="w-36 h-36 text-slate-900" viewBox="0 0 100 100" fill="currentColor">
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

        {{-- Nominal di bawah QR --}}
        <div class="mt-2 text-center">
            <span class="text-[11px] text-slate-400 font-semibold block">Nominal Transfer Tepat:</span>
            <span class="text-sm font-black text-slate-900 tracking-tight font-mono">Rp {{ number_format($amount, 0, ',', '.') }}</span>
        </div>
    </div>

    {{-- Badges Supported Apps --}}
    <div class="mt-4 flex flex-wrap items-center justify-center gap-2 text-[11px] text-slate-500 font-medium">
        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-slate-100 text-slate-700">✓ GoPay</span>
        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-slate-100 text-slate-700">✓ OVO</span>
        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-slate-100 text-slate-700">✓ DANA</span>
        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-slate-100 text-slate-700">✓ ShopeePay</span>
        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-slate-100 text-slate-700">✓ Mobile Banking (BCA / Mandiri / BRI dll)</span>
    </div>
</div>

