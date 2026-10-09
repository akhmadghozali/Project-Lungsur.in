@if (session()->has('success') || session()->has('error') || session()->has('warning') || session()->has('info'))
    <div x-data="{ show: true }" 
         x-show="show" 
         x-init="setTimeout(() => show = false, 6000)"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 -translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 -translate-y-2"
         class="fixed top-5 right-5 z-50 max-w-md w-full shadow-lg rounded-xl overflow-hidden border bg-white"
         role="alert"
         aria-live="assertive">

        @if (session()->has('success'))
            <div class="bg-emerald-50 border-l-4 border-emerald-600 text-emerald-950 p-4 flex items-start gap-3">
                <svg class="w-5 h-5 text-emerald-700 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <div class="flex-1 text-sm font-medium leading-relaxed">
                    {{ session('success') }}
                </div>
                <button type="button" @click="show = false" class="text-emerald-800 hover:text-emerald-950 p-1 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500" aria-label="Tutup notifikasi">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        @endif

        @if (session()->has('error'))
            <div class="bg-rose-50 border-l-4 border-rose-600 text-rose-950 p-4 flex items-start gap-3">
                <svg class="w-5 h-5 text-rose-700 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <div class="flex-1 text-sm font-medium leading-relaxed">
                    {{ session('error') }}
                </div>
                <button type="button" @click="show = false" class="text-rose-800 hover:text-rose-950 p-1 rounded-lg focus:outline-none focus:ring-2 focus:ring-rose-500" aria-label="Tutup notifikasi">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        @endif

        @if (session()->has('warning'))
            <div class="bg-amber-50 border-l-4 border-amber-500 text-amber-950 p-4 flex items-start gap-3">
                <svg class="w-5 h-5 text-amber-700 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                <div class="flex-1 text-sm font-medium leading-relaxed">
                    {{ session('warning') }}
                </div>
                <button type="button" @click="show = false" class="text-amber-800 hover:text-amber-950 p-1 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-500" aria-label="Tutup notifikasi">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        @endif

        @if (session()->has('info'))
            <div class="bg-sky-50 border-l-4 border-sky-600 text-sky-950 p-4 flex items-start gap-3">
                <svg class="w-5 h-5 text-sky-700 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <div class="flex-1 text-sm font-medium leading-relaxed">
                    {{ session('info') }}
                </div>
                <button type="button" @click="show = false" class="text-sky-800 hover:text-sky-950 p-1 rounded-lg focus:outline-none focus:ring-2 focus:ring-sky-500" aria-label="Tutup notifikasi">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        @endif
    </div>
@endif
