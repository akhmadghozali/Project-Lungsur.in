<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-100/70">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title x-text="mode === 'register' ? 'Buat Akun - Lungsur.in' : (mode === 'forgot_password' ? 'Lupa Password - Lungsur.in' : (mode === 'reset_password' ? 'Reset Password - Lungsur.in' : 'Login - Lungsur.in'))">
        {{ ($initialMode ?? (request()->is('register') ? 'register' : (request()->is('forgot-password') ? 'forgot_password' : (request()->is('reset-password') ? 'reset_password' : 'login')))) === 'register' ? 'Buat Akun - Lungsur.in' : 'Login - Lungsur.in' }}
    </title>

    <!-- Vite Assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-100/70 flex flex-col justify-center items-center py-6 sm:py-10 px-4 sm:px-6 lg:px-8 font-sans antialiased text-slate-900 selection:bg-blue-600 selection:text-white"
      x-data="authSpa()">

    <!-- Card Container Mengambang Terpusat (SPA Container) -->
    <div class="max-w-5xl w-full bg-white rounded-[2rem] sm:rounded-[2.25rem] shadow-2xl shadow-slate-300/50 border border-slate-200/80 overflow-hidden grid grid-cols-1 lg:grid-cols-12 min-h-[620px] transition-all duration-300">

        <!-- ============================================================== -->
        <!-- SISI KIRI (BRANDING PANEL BERGANTI HALUS TANPA RELOAD)         -->
        <!-- ============================================================== -->
        <div class="lg:col-span-5 relative overflow-hidden transition-colors duration-500 min-h-[520px] bg-slate-950">
            
            <div class="grid grid-cols-1 items-stretch h-full">
                <!-- PANEL KIRI VERSI LOGIN (GAMBAR KOS MALAM AESTHETIC) -->
                <div x-show="mode === 'login'" 
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-x-4"
                     x-transition:enter-end="opacity-100 translate-x-0"
                     x-transition:leave="transition ease-in duration-200"
                     x-transition:leave-start="opacity-100 translate-x-0"
                     x-transition:leave-end="opacity-0 -translate-x-4"
                     class="col-start-1 row-start-1 h-full flex flex-col justify-between p-8 sm:p-10 lg:p-12 text-white relative overflow-hidden">
                
                    <!-- Foto Ilustrasi Kamar Kos Malam (Dibuat Agak Transparan) -->
                    <img src="{{ asset('images/auth/auth-login.jpg') }}" 
                         alt="Kamar Kos Mahasiswa Malam" 
                         class="absolute inset-0 w-full h-full object-cover object-center pointer-events-none opacity-75">

                    <!-- Overlay Bottom Gradient: Hitam Pekat di Bawah, Memudar Transparan ke Tengah & Atas -->
                    <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-950/70 via-35% to-transparent to-70% pointer-events-none"></div>

                    <!-- Top: Logo Lungsur.in Terkunci di Sudut Kiri Atas -->
                    <div class="relative z-10">
                        <a href="{{ url('/') }}" class="inline-flex items-center gap-2 bg-white/95 backdrop-blur-md px-3.5 py-2 rounded-2xl shadow-lg shadow-black/20 group transition hover:scale-[1.02] border border-white/20">
                            <div class="w-8 h-8 rounded-xl bg-blue-600 flex items-center justify-center shrink-0">
                                <svg class="w-4 h-4 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M21 12a9 9 0 0 0-9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/>
                                    <path d="M3 3v5h5"/>
                                    <path d="M3 12a9 9 0 0 0 9 9 9.75 9.75 0 0 0 6.74-2.74L21 16"/>
                                    <path d="M21 21v-5h-5"/>
                                    <path d="M12 9l5 2.5-5 2.5-5-2.5 5-2.5z" fill="#FBBF24" stroke="#FBBF24" stroke-width="1.5"/>
                                    <path d="M9.5 13v2.2c0 .8 1.1 1.5 2.5 1.5s2.5-.7 2.5-1.5V13" stroke="#FBBF24" stroke-width="1.5"/>
                                </svg>
                            </div>
                            <span class="text-base font-black text-slate-900 tracking-tight">
                                Lungsur<span class="text-blue-600">.in</span>
                            </span>
                        </a>
                    </div>

                    <!-- Bottom: Headline & Deskripsi (Rapat di Bagian Bawah) -->
                    <div class="relative z-10 space-y-2">
                        <h2 class="text-3xl sm:text-4xl font-black tracking-tight leading-tight drop-shadow-md text-white">
                            Masuk ke Lungsur.in.
                        </h2>
                        <p class="text-xs sm:text-sm text-slate-200 font-medium leading-relaxed drop-shadow-sm max-w-sm">
                            Cek iklan barang kos terbaru, pasang wanted ads saat lagi butuh barang, atau pantau lelang yang kamu ikuti sesama mahasiswa Malang.
                        </p>
                    </div>
                </div>

                <!-- PANEL KIRI VERSI REGISTER (GAMBAR KOS HUJAN & KUCING AESTHETIC) -->
                <div x-show="mode === 'register'" 
                     x-cloak
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 -translate-x-4"
                     x-transition:enter-end="opacity-100 translate-x-0"
                     x-transition:leave="transition ease-in duration-200"
                     x-transition:leave-start="opacity-100 translate-x-0"
                     x-transition:leave-end="opacity-0 translate-x-4"
                     class="col-start-1 row-start-1 h-full flex flex-col justify-between p-8 sm:p-10 lg:p-12 text-white relative overflow-hidden">
                
                    <!-- Foto Ilustrasi Kamar Kos Hujan (Dibuat Agak Transparan) -->
                    <img src="{{ asset('images/auth/auth-register.jpg') }}" 
                         alt="Kamar Kos Mahasiswa Suasana Hujan" 
                         class="absolute inset-0 w-full h-full object-cover object-center pointer-events-none opacity-75">

                    <!-- Overlay Bottom Gradient: Hitam Pekat di Bawah, Memudar Transparan ke Tengah & Atas -->
                    <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-950/70 via-35% to-transparent to-70% pointer-events-none"></div>

                    <!-- Top: Logo Lungsur.in Terkunci di Sudut Kiri Atas -->
                    <div class="relative z-10">
                        <a href="{{ url('/') }}" class="inline-flex items-center gap-2 bg-white/95 backdrop-blur-md px-3.5 py-2 rounded-2xl shadow-lg shadow-black/20 group transition hover:scale-[1.02] border border-white/20">
                            <div class="w-8 h-8 rounded-xl bg-blue-600 flex items-center justify-center shrink-0">
                                <svg class="w-4 h-4 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M21 12a9 9 0 0 0-9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/>
                                    <path d="M3 3v5h5"/>
                                    <path d="M3 12a9 9 0 0 0 9 9 9.75 9.75 0 0 0 6.74-2.74L21 16"/>
                                    <path d="M21 21v-5h-5"/>
                                    <path d="M12 9l5 2.5-5 2.5-5-2.5 5-2.5z" fill="#FBBF24" stroke="#FBBF24" stroke-width="1.5"/>
                                    <path d="M9.5 13v2.2c0 .8 1.1 1.5 2.5 1.5s2.5-.7 2.5-1.5V13" stroke="#FBBF24" stroke-width="1.5"/>
                                </svg>
                            </div>
                            <span class="text-base font-black text-slate-900 tracking-tight">
                                Lungsur<span class="text-blue-600">.in</span>
                            </span>
                        </a>
                    </div>

                    <!-- Bottom: Headline, Subheadline & 2 Poin Ringkas Tanpa Kotak Background -->
                    <div class="relative z-10 space-y-2.5">
                        <div>
                            <h2 class="text-3xl sm:text-4xl font-black tracking-tight leading-tight drop-shadow-md text-white">
                                Gabung sekarang.
                            </h2>
                            <p class="mt-1.5 text-xs sm:text-sm text-slate-200 font-medium leading-relaxed drop-shadow-sm max-w-sm">
                                Pasang iklan, wanted ads, dan lelang barang kos khusus mahasiswa Malang.
                            </p>
                        </div>

                        <div class="space-y-1.5 pt-0.5 text-xs text-slate-200">
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-emerald-400 shrink-0 drop-shadow-sm" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                </svg>
                                <span class="font-medium drop-shadow-sm">
                                    Khusus mahasiswa Malang (verifikasi email kampus / KTM)
                                </span>
                            </div>

                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-emerald-400 shrink-0 drop-shadow-sm" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                </svg>
                                <span class="font-medium drop-shadow-sm">
                                    Pasang iklan, wanted ads &amp; lelang barang-barang milikmu
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- PANEL KIRI VERSI LUPA / RESET PASSWORD -->
                <div x-show="mode === 'forgot_password' || mode === 'reset_password'" 
                     x-cloak
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-x-4"
                     x-transition:enter-end="opacity-100 translate-x-0"
                     x-transition:leave="transition ease-in duration-200"
                     x-transition:leave-start="opacity-100 translate-x-0"
                     x-transition:leave-end="opacity-0 -translate-x-4"
                     class="col-start-1 row-start-1 h-full flex flex-col justify-between p-8 sm:p-10 lg:p-12 text-white relative overflow-hidden">
                
                    <!-- Foto Ilustrasi Kamar Kos Malam (Background Soft) -->
                    <img src="{{ asset('images/auth/auth-login.jpg') }}" 
                         alt="Kamar Kos Mahasiswa" 
                         class="absolute inset-0 w-full h-full object-cover object-center pointer-events-none opacity-60">

                    <!-- Overlay Bottom Gradient: Hitam Pekat di Bawah, Memudar Transparan ke Tengah & Atas -->
                    <div class="absolute inset-0 bg-gradient-to-t from-indigo-950 via-indigo-950/70 via-35% to-transparent to-70% pointer-events-none"></div>

                    <!-- Top: Logo Lungsur.in Terkunci di Sudut Kiri Atas -->
                    <div class="relative z-10">
                        <a href="{{ url('/') }}" class="inline-flex items-center gap-2 bg-white/95 backdrop-blur-md px-3.5 py-2 rounded-2xl shadow-lg shadow-black/20 group transition hover:scale-[1.02] border border-white/20">
                            <div class="w-8 h-8 rounded-xl bg-blue-600 flex items-center justify-center shrink-0">
                                <svg class="w-4 h-4 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M21 12a9 9 0 0 0-9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/>
                                    <path d="M3 3v5h5"/>
                                    <path d="M3 12a9 9 0 0 0 9 9 9.75 9.75 0 0 0 6.74-2.74L21 16"/>
                                    <path d="M21 21v-5h-5"/>
                                    <path d="M12 9l5 2.5-5 2.5-5-2.5 5-2.5z" fill="#FBBF24" stroke="#FBBF24" stroke-width="1.5"/>
                                    <path d="M9.5 13v2.2c0 .8 1.1 1.5 2.5 1.5s2.5-.7 2.5-1.5V13" stroke="#FBBF24" stroke-width="1.5"/>
                                </svg>
                            </div>
                            <span class="text-base font-black text-slate-900 tracking-tight">
                                Lungsur<span class="text-blue-600">.in</span>
                            </span>
                        </a>
                    </div>

                    <!-- Bottom: Headline & Points -->
                    <div class="relative z-10 space-y-3">
                        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-500/20 border border-blue-400/30 text-blue-200 text-xs font-bold mb-2">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                            Pemulihan Akun
                        </div>
                        <h2 class="text-3xl sm:text-4xl font-black tracking-tight leading-tight text-white">
                            Akses Akun Terlindungi.
                        </h2>
                        <p class="text-xs sm:text-sm text-indigo-100 font-medium leading-relaxed max-w-sm">
                            Lupa kata sandi? Kami akan mengirimkan kode verifikasi 6 digit ke alamat email kampus atau umum terdaftar Anda untuk mengatur ulang kata sandi dengan aman.
                        </p>

                        <div class="space-y-1.5 pt-1 text-xs text-slate-200">
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-blue-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                </svg>
                                <span>Kode OTP berlaku 15 menit</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-blue-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                </svg>
                                <span>Enkripsi sandi standar industri</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ============================================================== -->
        <!-- SISI KANAN (FORMULIR SPA: BERGANTI INSTAN TANPA LOAD HALAMAN) -->
        <!-- ============================================================== -->
        <div class="lg:col-span-7 p-6 sm:p-8 lg:p-10 flex flex-col justify-center bg-white relative">
            
            <div class="max-w-md w-full mx-auto">
                
                <!-- Notifikasi / Flash Messages Global -->
                @if (session('success'))
                    <div class="mb-4 p-3 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-medium flex items-start gap-2">
                        <svg class="w-4 h-4 text-emerald-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <div>{{ session('success') }}</div>
                    </div>
                @endif

                @if (session('warning'))
                    <div class="mb-4 p-3 rounded-2xl bg-amber-50 border border-amber-200 text-amber-800 text-xs font-medium flex items-start gap-2">
                        <svg class="w-4 h-4 text-amber-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        <div>{{ session('warning') }}</div>
                    </div>
                @endif

                @if (session('error'))
                    <div class="mb-4 p-3 rounded-2xl bg-red-50 border border-red-200 text-red-800 text-xs font-medium flex items-start gap-2">
                        <svg class="w-4 h-4 text-red-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <div>{{ session('error') }}</div>
                    </div>
                @endif

                @if (session('info'))
                    <div class="mb-4 p-3 rounded-2xl bg-blue-50 border border-blue-200 text-blue-800 text-xs font-medium flex items-start gap-2">
                        <svg class="w-4 h-4 text-blue-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <div>{{ session('info') }}</div>
                    </div>
                @endif

                <!-- FORMS GRID STACK: Menjaga posisi form tetap halus dan tidak melompat saat transisi -->
                <div class="grid grid-cols-1 items-start w-full">

                    <!-- ============================================================== -->
                    <!-- FORMULIR 1: LOGIN (AKTIF JIKA mode === 'login')               -->
                    <!-- ============================================================== -->
                    <div x-show="mode === 'login'" 
                         class="col-start-1 row-start-1 w-full"
                         x-transition:enter="transition ease-out duration-300"
                         x-transition:enter-start="opacity-0 translate-y-3"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100 translate-y-0"
                         x-transition:leave-end="opacity-0 -translate-y-3">
                    
                    <div class="mb-6">
                        <h1 class="text-2xl sm:text-3xl font-black tracking-tight text-slate-900">
                            Login
                        </h1>
                        <p class="mt-1 text-sm text-slate-500 font-medium">
                            Belum punya akun? 
                            <button type="button" 
                                    @click="switchMode('register')" 
                                    class="font-bold text-blue-600 hover:text-blue-700 transition hover:underline cursor-pointer">
                                Daftar
                            </button>
                        </p>
                    </div>

                    <!-- Notifikasi Error Khusus Login -->
                    @if (old('mode', 'login') === 'login' && isset($errors) && $errors->any())
                        <div class="mb-4 p-3 rounded-2xl bg-red-50 border border-red-200 text-red-800 text-xs font-medium space-y-1">
                            <div class="font-bold flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Mohon periksa kesalahan berikut:
                            </div>
                            <ul class="list-disc list-inside pl-1 space-y-0.5">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('login') }}" method="POST" class="space-y-4">
                        @csrf
                        <input type="hidden" name="mode" value="login">

                        <!-- Email (Tanpa Placeholder) -->
                        <div>
                            <label for="login_email" class="block text-xs font-bold text-slate-700 mb-1.5">
                                Email
                            </label>
                            <input type="email" 
                                   name="email" 
                                   id="login_email" 
                                   value="{{ old('mode') === 'login' ? old('email') : '' }}" 
                                   required 
                                   autocomplete="email"
                                   class="w-full px-4 py-3 rounded-2xl border border-slate-200 bg-white text-slate-900 font-medium text-sm focus:outline-none focus:border-blue-600 focus:ring-4 focus:ring-blue-100 transition shadow-2xs">
                        </div>

                        <!-- Password dengan Toggle Alpine.js -->
                        <div x-data="{ show: false }">
                            <label for="login_password" class="block text-xs font-bold text-slate-700 mb-1.5">
                                Password
                            </label>
                            <div class="relative">
                                <input :type="show ? 'text' : 'password'" 
                                       name="password" 
                                       id="login_password" 
                                       required 
                                       class="w-full px-4 py-3 pr-11 rounded-2xl border border-slate-200 bg-white text-slate-900 font-medium text-sm focus:outline-none focus:border-blue-600 focus:ring-4 focus:ring-blue-100 transition shadow-2xs">
                                
                                <button type="button" 
                                        @click="show = !show" 
                                        class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600"
                                        tabindex="-1">
                                    <svg x-show="!show" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    <svg x-show="show" x-cloak class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <!-- Ingat Saya & Lupa Password -->
                        <div class="flex items-center justify-between pt-1">
                            <label class="flex items-center cursor-pointer">
                                <input id="remember" 
                                       name="remember" 
                                       type="checkbox" 
                                       value="1"
                                       {{ old('remember') ? 'checked' : '' }}
                                       class="w-4 h-4 rounded text-blue-600 focus:ring-blue-500 border-slate-300">
                                <span class="ml-2 text-xs font-semibold text-slate-600">
                                    Ingat saya
                                </span>
                            </label>
                            <a href="{{ route('password.request') }}" 
                               @click.prevent="switchMode('forgot_password')"
                               class="text-xs font-semibold text-blue-600 hover:text-blue-700 transition hover:underline">
                                Lupa password?
                            </a>
                        </div>

                        <!-- Tombol Masuk -->
                        <div class="pt-2">
                            <button type="submit" 
                                    class="w-full py-3.5 px-4 rounded-2xl bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-white font-bold text-sm tracking-wide shadow-md shadow-blue-500/20 focus:outline-none focus:ring-4 focus:ring-blue-100 transition cursor-pointer">
                                Masuk
                            </button>
                        </div>

                        <!-- Divider "atau" -->
                        <div class="relative my-3 flex items-center justify-center">
                            <div class="absolute inset-0 flex items-center">
                                <div class="w-full border-t border-slate-200"></div>
                            </div>
                            <span class="relative bg-white px-3 text-xs font-medium text-slate-400">
                                atau
                            </span>
                        </div>

                        <!-- Tombol Masuk dengan Google -->
                        <a href="{{ route('auth.google') }}" 
                           class="w-full py-3 px-4 rounded-2xl bg-white hover:bg-slate-50 border border-slate-200 text-slate-700 font-bold text-sm flex items-center justify-center gap-3 transition shadow-2xs cursor-pointer">
                            <svg class="w-4 h-4" viewBox="0 0 24 24">
                                <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                                <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                                <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z"/>
                                <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z"/>
                            </svg>
                            <span>Masuk dengan Google</span>
                        </a>
                    </form>
                </div>

                    <!-- ============================================================== -->
                    <!-- FORMULIR 2: REGISTER (AKTIF JIKA mode === 'register')         -->
                    <!-- ============================================================== -->
                    <div x-show="mode === 'register'" 
                         x-cloak
                         class="col-start-1 row-start-1 w-full"
                         x-transition:enter="transition ease-out duration-300"
                         x-transition:enter-start="opacity-0 translate-y-3"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100 translate-y-0"
                         x-transition:leave-end="opacity-0 -translate-y-3">
                    
                    <div class="mb-5">
                        <h1 class="text-2xl sm:text-3xl font-black tracking-tight text-slate-900">
                            Buat akun
                        </h1>
                        <p class="mt-1 text-sm text-slate-500 font-medium">
                            Sudah punya akun? 
                            <button type="button" 
                                    @click="switchMode('login')" 
                                    class="font-bold text-blue-600 hover:text-blue-700 transition hover:underline cursor-pointer">
                                Login
                            </button>
                        </p>
                    </div>

                    <!-- Alert Error Registrasi (Server-Side) -->
                    @if (old('mode') === 'register' && isset($errors) && $errors->any())
                        <div class="mb-4 p-3 rounded-2xl bg-red-50 border border-red-200 text-red-800 text-xs font-medium space-y-1">
                            <div class="font-bold flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Mohon periksa kesalahan berikut:
                            </div>
                            <ul class="list-disc list-inside pl-1 space-y-0.5">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <!-- Alert Error Registrasi (AJAX / Real-Time) -->
                    <div x-show="registerError" x-cloak class="mb-4 p-3 rounded-2xl bg-red-50 border border-red-200 text-red-800 text-xs font-medium flex items-start gap-2 shadow-2xs">
                        <svg class="w-4 h-4 text-red-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <div x-text="registerError"></div>
                    </div>

                    <form action="{{ route('register') }}" 
                          method="POST" 
                          enctype="multipart/form-data" 
                          class="space-y-3.5"
                          @submit.prevent="submitRegister($event)">
                        @csrf
                        <input type="hidden" name="mode" value="register">

                        <!-- Nama Lengkap (Tanpa Placeholder) -->
                        <div>
                            <label for="reg_name" class="block text-xs font-bold text-slate-700 mb-1">
                                Nama lengkap
                            </label>
                            <input type="text" 
                                   name="name" 
                                   id="reg_name" 
                                   value="{{ old('mode') === 'register' ? old('name') : '' }}" 
                                   required 
                                   class="w-full px-4 py-2.5 rounded-2xl border border-slate-200 bg-white text-slate-900 font-medium text-sm focus:outline-none focus:border-blue-600 focus:ring-4 focus:ring-blue-100 transition shadow-2xs">
                        </div>

                        <!-- Kampus -->
                        <div>
                            <label for="reg_campus" class="block text-xs font-bold text-slate-700 mb-1">
                                Kampus
                            </label>
                            <select name="campus" 
                                    id="reg_campus" 
                                    required 
                                    x-model="selectedCampus"
                                    @change="detectEmail()"
                                    class="w-full px-4 py-2.5 rounded-2xl border bg-white text-slate-900 font-medium text-sm focus:outline-none transition shadow-2xs"
                                    :class="{
                                        'border-red-500 focus:border-red-500 focus:ring-4 focus:ring-red-100 bg-red-50/20': isCampusMismatch,
                                        'border-slate-200 focus:border-blue-600 focus:ring-4 focus:ring-blue-100': !isCampusMismatch
                                    }">
                                <option value="" disabled selected>Pilih Perguruan Tinggi</option>
                                <option value="Politeknik Negeri Malang">Politeknik Negeri Malang (Polinema)</option>
                                <option value="Universitas Brawijaya">Universitas Brawijaya (UB)</option>
                                <option value="Universitas Negeri Malang">Universitas Negeri Malang (UM)</option>
                                <option value="UIN Maulana Malik Ibrahim Malang">UIN Maulana Malik Ibrahim</option>
                                <option value="Universitas Muhammadiyah Malang">Universitas Muhammadiyah Malang (UMM)</option>
                                <option value="Institut Teknologi Nasional Malang">ITN Malang</option>
                                <option value="Lainnya">Kampus Lainnya di Malang</option>
                            </select>
                        </div>

                        <!-- Email (Alpine.js Deteksi Otomatis, Tanpa Placeholder) -->
                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <label for="reg_email" class="block text-xs font-bold text-slate-700">
                                    Email
                                </label>
                                <span class="text-[11px] font-medium text-slate-400">
                                    Gunakan email kampus untuk verifikasi instan
                                </span>
                            </div>

                            <div class="relative">
                                <input type="email" 
                                       name="email" 
                                       id="reg_email" 
                                       x-model="email" 
                                       @input.debounce.250ms="detectEmail()" 
                                       value="{{ old('mode') === 'register' ? old('email') : '' }}" 
                                       required 
                                       class="w-full px-4 py-2.5 pr-11 rounded-2xl border bg-white text-slate-900 font-medium text-sm focus:outline-none transition shadow-2xs"
                                       :class="{
                                           'border-red-500 focus:border-red-500 focus:ring-4 focus:ring-red-100 bg-red-50/20': isCampusMismatch,
                                           'border-emerald-500 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100': isCampus && !isCampusMismatch,
                                           'border-amber-400 focus:border-amber-500 focus:ring-4 focus:ring-amber-100': isPublic && !isCampusMismatch,
                                           'border-slate-200 focus:border-blue-600 focus:ring-4 focus:ring-blue-100': !isCampus && !isPublic && !isCampusMismatch
                                       }">

                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                    <template x-if="isCampusMismatch">
                                        <div class="w-5 h-5 rounded-full bg-red-100 text-red-600 flex items-center justify-center">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </div>
                                    </template>
                                    <template x-if="isCampus && !isCampusMismatch">
                                        <div class="w-5 h-5 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                            </svg>
                                        </div>
                                    </template>
                                    <template x-if="isPublic && !isCampusMismatch">
                                        <div class="w-5 h-5 rounded-full bg-amber-100 text-amber-600 flex items-center justify-center">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01" />
                                            </svg>
                                        </div>
                                    </template>
                                </div>
                            </div>

                            <!-- Feedback Error: Ketidakcocokan Kampus dan Domain Email -->
                            <div x-show="isCampusMismatch" x-cloak x-transition class="mt-2 p-3 rounded-2xl bg-red-50 border border-red-200 text-xs text-red-800 space-y-1.5 shadow-2xs">
                                <div class="font-bold flex items-center gap-1.5 text-red-700">
                                    <svg class="w-4 h-4 shrink-0 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                    </svg>
                                    <span>Pilihan Kampus &amp; Domain Email Tidak Sesuai!</span>
                                </div>
                                <p class="leading-relaxed pl-5 text-slate-700 font-medium" x-text="campusMismatchMessage"></p>
                                <div class="pl-5 pt-0.5" x-show="matchedCampusForEmail">
                                    <button type="button" 
                                            @click="autoFixCampus()" 
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-red-600 hover:bg-red-700 text-white font-bold text-[11px] transition shadow-xs cursor-pointer">
                                        <span>Ubah pilihan kampus ke <span class="underline" x-text="matchedCampusForEmail"></span></span>
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            <!-- Feedback Whitelist Kampus -->
                            <div x-show="isCampus && !isCampusMismatch" x-cloak x-transition class="mt-1 flex items-center gap-1.5 text-xs font-semibold text-emerald-600">
                                <svg class="w-3.5 h-3.5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                                <span>Domain <span class="font-bold underline" x-text="'@' + detectedDomain"></span> terdaftar whitelist kampus. Akun aktif instan!</span>
                            </div>

                            <!-- Feedback Email Umum -->
                            <div x-show="isPublic && !isCampusMismatch" x-cloak x-transition class="mt-1 p-2.5 rounded-xl bg-amber-50 border border-amber-200 text-xs text-amber-800">
                                <strong>Email publik terdeteksi (@<span x-text="detectedDomain"></span>):</strong> Wajib melampirkan foto KTM aktif untuk divalidasi admin.
                            </div>
                        </div>

                        <!-- Dropzone Upload Foto KTM (Jika Email Umum) -->
                        <div x-show="isPublic" 
                             x-cloak 
                             x-transition
                             class="p-3.5 rounded-2xl bg-slate-50 border-2 border-dashed border-blue-300">
                            
                            <div class="flex items-center justify-between mb-2">
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-800">
                                    Unggah Foto Kartu Tanda Mahasiswa (KTM) <span class="text-red-500">*</span>
                                </label>
                                <span class="text-[10px] font-semibold text-blue-600 bg-blue-50 px-2 py-0.5 rounded-md">
                                    Maks. 2 MB
                                </span>
                            </div>

                            <div class="relative bg-white rounded-xl border border-slate-200 p-3 text-center hover:border-blue-500 transition group cursor-pointer"
                                 @click="$refs.ktmInput.click()"
                                 @dragover.prevent="dragOver = true"
                                 @dragleave.prevent="dragOver = false"
                                 @drop.prevent="handleDrop($event)"
                                 :class="{ 'border-blue-500 bg-blue-50/50': dragOver }">
                                
                                <input type="file" 
                                       name="ktm_file" 
                                       x-ref="ktmInput" 
                                       @change="handleFileSelect($event)" 
                                       accept="image/png, image/jpeg, image/jpg" 
                                       class="hidden">

                                <template x-if="!ktmPreview">
                                    <div class="py-1">
                                        <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 mx-auto flex items-center justify-center mb-1.5 group-hover:scale-105 transition">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <rect x="3" y="4" width="18" height="16" rx="3" stroke-width="1.8"/>
                                                <circle cx="9" cy="10" r="2" stroke-width="1.8"/>
                                                <line x1="15" y1="9" x2="17" y2="9" stroke-width="1.8"/>
                                                <line x1="15" y1="12" x2="17" y2="12" stroke-width="1.8"/>
                                                <line x1="7" y1="16" x2="17" y2="16" stroke-width="1.8"/>
                                            </svg>
                                        </div>
                                        <div class="text-xs font-bold text-slate-800">
                                            Klik untuk memilih foto KTM atau seret file ke sini
                                        </div>
                                        <div class="text-[11px] text-slate-500">
                                            Pastikan Nama, NIM, & Logo Kampus terlihat jelas
                                        </div>
                                    </div>
                                </template>

                                <template x-if="ktmPreview">
                                    <div class="flex items-center justify-between gap-3 text-left">
                                        <div class="flex items-center gap-2.5">
                                            <img :src="ktmPreview" alt="Pratinjau KTM" class="w-14 h-10 object-cover rounded-lg border border-slate-200">
                                            <div>
                                                <div class="text-xs font-bold text-slate-800 truncate max-w-[180px]" x-text="ktmFileName"></div>
                                                <div class="text-[11px] text-emerald-600 font-semibold" x-text="ktmFileSize + ' • Siap diunggah'"></div>
                                            </div>
                                        </div>
                                        <button type="button" 
                                                @click.stop="removeKtmFile()" 
                                                class="text-xs font-bold text-red-500 hover:text-red-700 px-2.5 py-1 rounded-lg bg-red-50 hover:bg-red-100 transition">
                                            Ganti
                                        </button>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <!-- Password & Ulangi Password -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label for="reg_password" class="block text-xs font-bold text-slate-700 mb-1">
                                    Password
                                </label>
                                <div class="relative">
                                    <input :type="showPassword ? 'text' : 'password'" 
                                           name="password" 
                                           id="reg_password" 
                                           x-model="password" 
                                           @input="checkPasswordStrength()" 
                                           required 
                                           class="w-full px-4 py-2.5 pr-10 rounded-2xl border border-slate-200 bg-white text-slate-900 font-medium text-sm focus:outline-none focus:border-blue-600 focus:ring-4 focus:ring-blue-100 transition shadow-2xs">
                                    
                                    <button type="button" 
                                            @click="showPassword = !showPassword" 
                                            class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600"
                                            tabindex="-1">
                                        <svg x-show="!showPassword" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                        <svg x-show="showPassword" x-cloak class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
                                        </svg>
                                    </button>
                                </div>

                                <div class="mt-1.5 flex items-center gap-1.5" x-show="password.length > 0" x-cloak>
                                    <div class="grid grid-cols-4 gap-1 flex-1">
                                        <div class="h-1 rounded-full" :class="strengthScore >= 1 ? 'bg-red-500' : 'bg-slate-200'"></div>
                                        <div class="h-1 rounded-full" :class="strengthScore >= 2 ? 'bg-amber-500' : 'bg-slate-200'"></div>
                                        <div class="h-1 rounded-full" :class="strengthScore >= 3 ? 'bg-emerald-400' : 'bg-slate-200'"></div>
                                        <div class="h-1 rounded-full" :class="strengthScore >= 4 ? 'bg-emerald-600' : 'bg-slate-200'"></div>
                                    </div>
                                    <span class="text-[10px] font-bold shrink-0 ml-1" 
                                          :class="{
                                              'text-red-500': strengthScore <= 1,
                                              'text-amber-500': strengthScore === 2,
                                              'text-emerald-600': strengthScore >= 3
                                          }"
                                          x-text="strengthLabel"></span>
                                </div>
                            </div>

                            <div>
                                <label for="reg_password_confirmation" class="block text-xs font-bold text-slate-700 mb-1">
                                    Ulangi password
                                </label>
                                <div class="relative">
                                    <input :type="showConfirmPassword ? 'text' : 'password'" 
                                           name="password_confirmation" 
                                           id="reg_password_confirmation" 
                                           x-model="passwordConfirmation" 
                                           required 
                                           class="w-full px-4 py-2.5 pr-10 rounded-2xl border border-slate-200 bg-white text-slate-900 font-medium text-sm focus:outline-none focus:border-blue-600 focus:ring-4 focus:ring-blue-100 transition shadow-2xs">
                                    
                                    <button type="button" 
                                            @click="showConfirmPassword = !showConfirmPassword" 
                                            class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600"
                                            tabindex="-1">
                                        <svg x-show="!showConfirmPassword" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                        <svg x-show="showConfirmPassword" x-cloak class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Checkbox Syarat & Ketentuan -->
                        <div class="flex items-start pt-1">
                            <div class="flex items-center h-5">
                                <input id="reg_terms" 
                                       name="terms" 
                                       type="checkbox" 
                                       required 
                                       class="w-4 h-4 rounded text-blue-600 focus:ring-blue-500 border-slate-300">
                            </div>
                            <div class="ml-2.5 text-xs text-slate-600 leading-snug">
                                <label for="reg_terms" class="cursor-pointer">
                                    Saya setuju dengan <a href="#" class="font-bold text-slate-800 hover:text-blue-600">Ketentuan Layanan</a> &amp; <a href="#" class="font-bold text-slate-800 hover:text-blue-600">Kebijakan Privasi</a> Lungsur.in
                                </label>
                            </div>
                        </div>

                        <!-- Tombol Aksi Utama -->
                        <div class="pt-2 space-y-3">
                            <button type="submit" 
                                    :disabled="isCampusMismatch || registerLoading"
                                    :class="{ 'opacity-50 cursor-not-allowed hover:bg-blue-600 active:bg-blue-600': isCampusMismatch || registerLoading }"
                                    class="w-full py-3.5 px-4 rounded-2xl bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-white font-bold text-sm tracking-wide shadow-md shadow-blue-500/20 focus:outline-none focus:ring-4 focus:ring-blue-100 transition cursor-pointer flex items-center justify-center gap-2">
                                <template x-if="registerLoading">
                                    <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                </template>
                                <span x-text="registerLoading ? 'Mengirim Kode OTP...' : (isCampusMismatch ? 'Perbaiki Kampus / Email Terlebih Dahulu' : (isCampus ? 'Daftar Akun Mahasiswa' : (isPublic ? 'Daftar & Ajukan Verifikasi KTM' : 'Buat akun')))"></span>
                            </button>

                            <div class="relative flex items-center justify-center">
                                <div class="absolute inset-0 flex items-center">
                                    <div class="w-full border-t border-slate-200"></div>
                                </div>
                                <span class="relative bg-white px-3 text-xs font-medium text-slate-400">
                                    atau
                                </span>
                            </div>

                            <a href="{{ route('auth.google') }}" 
                               class="w-full py-3 px-4 rounded-2xl bg-white hover:bg-slate-50 border border-slate-200 text-slate-700 font-bold text-sm flex items-center justify-center gap-3 transition shadow-2xs cursor-pointer">
                                <svg class="w-4 h-4" viewBox="0 0 24 24">
                                    <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                                    <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                                    <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z"/>
                                    <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z"/>
                                </svg>
                                <span>Masuk dengan Google</span>
                            </a>
                        </div>
                    </form>
                </div>

                    <!-- ============================================================== -->
                    <!-- FORMULIR 3: LUPA PASSWORD (KIRIM KODE OTP)                     -->
                    <!-- ============================================================== -->
                    <div x-show="mode === 'forgot_password'" 
                         x-cloak
                         class="col-start-1 row-start-1 w-full"
                         x-transition:enter="transition ease-out duration-300"
                         x-transition:enter-start="opacity-0 translate-y-3"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100 translate-y-0"
                         x-transition:leave-end="opacity-0 -translate-y-3">
                    
                    <div class="mb-6">
                        <button type="button" 
                                @click="switchMode('login')"
                                class="inline-flex items-center gap-1.5 text-xs font-bold text-slate-500 hover:text-blue-600 transition mb-3 cursor-pointer">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                            </svg>
                            Kembali ke Login
                        </button>
                        <h1 class="text-2xl sm:text-3xl font-black tracking-tight text-slate-900">
                            Lupa Kata Sandi?
                        </h1>
                        <p class="text-xs sm:text-sm font-medium text-slate-500 mt-1">
                            Masukkan email akun Anda. Kami akan mengirimkan kode OTP 6-digit untuk verifikasi akun Anda.
                        </p>
                    </div>

                    @if (old('mode') === 'forgot_password' && isset($errors) && $errors->any())
                        <div class="mb-4 p-3 rounded-2xl bg-red-50 border border-red-200 text-red-800 text-xs font-medium space-y-1">
                            <ul class="list-disc list-inside pl-1 space-y-0.5">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('password.email') }}" method="POST" class="space-y-4">
                        @csrf
                        
                        <div>
                            <label for="forgot_email" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                                Alamat Email Terdaftar
                            </label>
                            <input id="forgot_email" 
                                   name="email" 
                                   type="email" 
                                   value="{{ old('email', $presetEmail ?? '') }}"
                                   placeholder="nama@student.ub.ac.id atau nama@gmail.com" 
                                   required 
                                   class="w-full px-4 py-3 rounded-2xl border border-slate-200 bg-white text-slate-900 font-medium text-sm focus:outline-none focus:border-blue-600 focus:ring-4 focus:ring-blue-100 transition shadow-2xs">
                        </div>

                        <div class="pt-2">
                            <button type="submit" 
                                    class="w-full py-3.5 px-4 rounded-2xl bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-white font-bold text-sm tracking-wide shadow-md shadow-blue-500/20 focus:outline-none focus:ring-4 focus:ring-blue-100 transition cursor-pointer">
                                Kirim Kode OTP Reset
                            </button>
                        </div>
                    </form>
                </div>

                    <!-- ============================================================== -->
                    <!-- FORMULIR 4: RESET PASSWORD (VERIFIKASI OTP & KATA SANDI BARU) -->
                    <!-- ============================================================== -->
                    <div x-show="mode === 'reset_password'" 
                         x-cloak
                         class="col-start-1 row-start-1 w-full"
                         x-transition:enter="transition ease-out duration-300"
                         x-transition:enter-start="opacity-0 translate-y-3"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100 translate-y-0"
                         x-transition:leave-end="opacity-0 -translate-y-3">
                        
                        <div class="mb-6">
                            <button type="button" 
                                    @click="switchMode('forgot_password')"
                                    class="inline-flex items-center gap-1.5 text-xs font-bold text-slate-500 hover:text-blue-600 transition mb-3 cursor-pointer">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                                </svg>
                                Ganti Email / Kirim Ulang OTP
                            </button>
                            <h1 class="text-2xl sm:text-3xl font-black tracking-tight text-slate-900">
                                Atur Kata Sandi Baru
                            </h1>
                            <p class="text-xs sm:text-sm font-medium text-slate-500 mt-1">
                                Masukkan kode OTP yang dikirimkan ke email Anda dan buat kata sandi baru.
                            </p>
                        </div>

                        @if (old('mode') === 'reset_password' && isset($errors) && $errors->any())
                            <div class="mb-4 p-3 rounded-2xl bg-red-50 border border-red-200 text-red-800 text-xs font-medium space-y-1">
                                <ul class="list-disc list-inside pl-1 space-y-0.5">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form action="{{ route('password.update') }}" method="POST" class="space-y-4">
                            @csrf
                            
                            <div>
                                <label for="reset_email" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                                    Alamat Email
                                </label>
                                <input id="reset_email" 
                                       name="email" 
                                       type="email" 
                                       value="{{ old('email', $presetEmail ?? '') }}"
                                       placeholder="nama@email.com" 
                                       required 
                                       class="w-full px-4 py-3 rounded-2xl border border-slate-200 bg-white text-slate-900 font-medium text-sm focus:outline-none focus:border-blue-600 focus:ring-4 focus:ring-blue-100 transition shadow-2xs">
                            </div>

                            <div>
                                <label for="reset_otp" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                                    Kode OTP (6 Digit)
                                </label>
                                <input id="reset_otp" 
                                       name="otp" 
                                       type="text" 
                                       inputmode="numeric"
                                       pattern="[0-9]*"
                                       autocomplete="one-time-code"
                                       maxlength="6"
                                       placeholder="123456" 
                                       required 
                                       class="w-full px-4 py-3 rounded-2xl border border-slate-200 bg-white text-slate-900 font-mono text-center tracking-[0.4em] text-lg font-bold focus:outline-none focus:border-blue-600 focus:ring-4 focus:ring-blue-100 transition shadow-2xs">
                            </div>

                            <div x-data="{ show: false }">
                                <label for="reset_password_field" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                                    Kata Sandi Baru
                                </label>
                                <div class="relative">
                                    <input id="reset_password_field" 
                                           name="password" 
                                           :type="show ? 'text' : 'password'" 
                                           placeholder="Minimal 8 karakter" 
                                           required 
                                           class="w-full px-4 py-3 pr-11 rounded-2xl border border-slate-200 bg-white text-slate-900 font-medium text-sm focus:outline-none focus:border-blue-600 focus:ring-4 focus:ring-blue-100 transition shadow-2xs">
                                    <button type="button" 
                                            @click="show = !show" 
                                            class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600"
                                            tabindex="-1">
                                        <svg x-show="!show" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                        <svg x-show="show" x-cloak class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            <div>
                                <label for="reset_password_confirmation" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                                    Konfirmasi Kata Sandi Baru
                                </label>
                                <input id="reset_password_confirmation" 
                                       name="password_confirmation" 
                                       type="password" 
                                       placeholder="Ulangi kata sandi baru" 
                                       required 
                                       class="w-full px-4 py-3 rounded-2xl border border-slate-200 bg-white text-slate-900 font-medium text-sm focus:outline-none focus:border-blue-600 focus:ring-4 focus:ring-blue-100 transition shadow-2xs">
                            </div>

                            <div class="pt-2">
                                <button type="submit" 
                                        class="w-full py-3.5 px-4 rounded-2xl bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white font-bold text-sm tracking-wide shadow-md shadow-emerald-500/20 focus:outline-none focus:ring-4 focus:ring-emerald-100 transition cursor-pointer">
                                    Simpan Kata Sandi Baru
                                </button>
                            </div>
                        </form>
                    </div>

                </div>

            </div>

        </div>

    </div>

    <!-- ======================================================== -->
    <!-- MODAL POPUP VERIFIKASI OTP EMAIL 6-DIGIT                 -->
    <!-- ======================================================== -->
    <div x-show="showOtpModal" 
         x-cloak 
         @keydown.escape.window="if (showOtpModal) closeOtpModal()"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/70 backdrop-blur-xs"
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">

        <div class="max-w-md w-full bg-white rounded-3xl p-6 sm:p-8 shadow-2xl border border-slate-200 text-center relative overflow-hidden"
             @click.away="closeOtpModal()"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4 scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 scale-100"
             x-transition:leave-end="opacity-0 translate-y-4 scale-95">

            <!-- Tombol Tutup (X) -->
            <button type="button" 
                    @click="closeOtpModal()" 
                    class="absolute top-4 right-4 w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-400 hover:text-slate-700 flex items-center justify-center transition cursor-pointer"
                    aria-label="Tutup modal verifikasi">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>

            <!-- Soft ambient accent in modal -->
            <div class="absolute -right-12 -top-12 w-32 h-32 rounded-full bg-blue-500/10 blur-2xl pointer-events-none"></div>

            <!-- Header Icon -->
            <div class="w-16 h-16 rounded-2xl bg-blue-50 border border-blue-100 text-blue-600 mx-auto flex items-center justify-center mb-4 shadow-xs">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
            </div>

            <!-- Title & Info -->
            <h3 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">
                Verifikasi Email Mahasiswa
            </h3>
            <p class="mt-2 text-xs sm:text-sm text-slate-600 leading-relaxed">
                Kami telah mengirimkan 6 digit kode OTP ke:
            </p>
            <div class="mt-1 font-bold text-sm text-blue-600 bg-blue-50 py-1 px-3 rounded-lg inline-block border border-blue-100">
                <span x-text="otpEmail"></span>
            </div>

            <!-- Alert Error OTP -->
            <div x-show="otpError" x-cloak class="mt-4 p-3 rounded-xl bg-red-50 border border-red-200 text-xs text-red-700 font-semibold text-left flex items-start gap-2">
                <svg class="w-4 h-4 text-red-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span x-text="otpError"></span>
            </div>

            <!-- Alert Sukses Kirim Ulang OTP -->
            <div x-show="otpSuccessMessage" x-cloak class="mt-4 p-3 rounded-xl bg-emerald-50 border border-emerald-200 text-xs text-emerald-800 font-semibold text-left flex items-start gap-2">
                <svg class="w-4 h-4 text-emerald-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <span x-text="otpSuccessMessage"></span>
            </div>

            <!-- 6 Input OTP Boxes: Static x-ref untuk auto-focus dan auto-advance lancar -->
            <div class="my-6">
                <div class="flex justify-center items-center gap-2 sm:gap-2.5" 
                     x-ref="otpContainer" 
                     @paste="handleOtpPaste($event)"
                     @keydown.enter.prevent="if (otpDigits.every(d => d !== '')) submitVerifyOtp()">
                    
                    <input type="text"
                           inputmode="numeric"
                           pattern="[0-9]*"
                           autocomplete="one-time-code"
                           maxlength="1"
                           x-ref="otpInput0"
                           x-model="otpDigits[0]"
                           @input="handleOtpInput($event, 0)"
                           @keydown="handleOtpKeydown($event, 0)"
                           @focus="$event.target.select()"
                           aria-label="Digit OTP ke-1"
                           :class="otpDigits[0] ? 'border-blue-600 bg-blue-50/25 text-blue-700 ring-2 ring-blue-100 font-black' : 'border-slate-300 bg-slate-50/60 text-slate-900'"
                           class="w-10 h-13 sm:w-12 sm:h-14 text-center font-mono text-2xl font-black rounded-xl border focus:bg-white focus:border-blue-600 focus:ring-4 focus:ring-blue-100 focus:outline-none transition-all duration-150 shadow-2xs select-all">

                    <input type="text"
                           inputmode="numeric"
                           pattern="[0-9]*"
                           autocomplete="one-time-code"
                           maxlength="1"
                           x-ref="otpInput1"
                           x-model="otpDigits[1]"
                           @input="handleOtpInput($event, 1)"
                           @keydown="handleOtpKeydown($event, 1)"
                           @focus="$event.target.select()"
                           aria-label="Digit OTP ke-2"
                           :class="otpDigits[1] ? 'border-blue-600 bg-blue-50/25 text-blue-700 ring-2 ring-blue-100 font-black' : 'border-slate-300 bg-slate-50/60 text-slate-900'"
                           class="w-10 h-13 sm:w-12 sm:h-14 text-center font-mono text-2xl font-black rounded-xl border focus:bg-white focus:border-blue-600 focus:ring-4 focus:ring-blue-100 focus:outline-none transition-all duration-150 shadow-2xs select-all">

                    <input type="text"
                           inputmode="numeric"
                           pattern="[0-9]*"
                           autocomplete="one-time-code"
                           maxlength="1"
                           x-ref="otpInput2"
                           x-model="otpDigits[2]"
                           @input="handleOtpInput($event, 2)"
                           @keydown="handleOtpKeydown($event, 2)"
                           @focus="$event.target.select()"
                           aria-label="Digit OTP ke-3"
                           :class="otpDigits[2] ? 'border-blue-600 bg-blue-50/25 text-blue-700 ring-2 ring-blue-100 font-black' : 'border-slate-300 bg-slate-50/60 text-slate-900'"
                           class="w-10 h-13 sm:w-12 sm:h-14 text-center font-mono text-2xl font-black rounded-xl border focus:bg-white focus:border-blue-600 focus:ring-4 focus:ring-blue-100 focus:outline-none transition-all duration-150 shadow-2xs select-all">

                    <input type="text"
                           inputmode="numeric"
                           pattern="[0-9]*"
                           autocomplete="one-time-code"
                           maxlength="1"
                           x-ref="otpInput3"
                           x-model="otpDigits[3]"
                           @input="handleOtpInput($event, 3)"
                           @keydown="handleOtpKeydown($event, 3)"
                           @focus="$event.target.select()"
                           aria-label="Digit OTP ke-4"
                           :class="otpDigits[3] ? 'border-blue-600 bg-blue-50/25 text-blue-700 ring-2 ring-blue-100 font-black' : 'border-slate-300 bg-slate-50/60 text-slate-900'"
                           class="w-10 h-13 sm:w-12 sm:h-14 text-center font-mono text-2xl font-black rounded-xl border focus:bg-white focus:border-blue-600 focus:ring-4 focus:ring-blue-100 focus:outline-none transition-all duration-150 shadow-2xs select-all">

                    <input type="text"
                           inputmode="numeric"
                           pattern="[0-9]*"
                           autocomplete="one-time-code"
                           maxlength="1"
                           x-ref="otpInput4"
                           x-model="otpDigits[4]"
                           @input="handleOtpInput($event, 4)"
                           @keydown="handleOtpKeydown($event, 4)"
                           @focus="$event.target.select()"
                           aria-label="Digit OTP ke-5"
                           :class="otpDigits[4] ? 'border-blue-600 bg-blue-50/25 text-blue-700 ring-2 ring-blue-100 font-black' : 'border-slate-300 bg-slate-50/60 text-slate-900'"
                           class="w-10 h-13 sm:w-12 sm:h-14 text-center font-mono text-2xl font-black rounded-xl border focus:bg-white focus:border-blue-600 focus:ring-4 focus:ring-blue-100 focus:outline-none transition-all duration-150 shadow-2xs select-all">

                    <input type="text"
                           inputmode="numeric"
                           pattern="[0-9]*"
                           autocomplete="one-time-code"
                           maxlength="1"
                           x-ref="otpInput5"
                           x-model="otpDigits[5]"
                           @input="handleOtpInput($event, 5)"
                           @keydown="handleOtpKeydown($event, 5)"
                           @focus="$event.target.select()"
                           aria-label="Digit OTP ke-6"
                           :class="otpDigits[5] ? 'border-blue-600 bg-blue-50/25 text-blue-700 ring-2 ring-blue-100 font-black' : 'border-slate-300 bg-slate-50/60 text-slate-900'"
                           class="w-10 h-13 sm:w-12 sm:h-14 text-center font-mono text-2xl font-black rounded-xl border focus:bg-white focus:border-blue-600 focus:ring-4 focus:ring-blue-100 focus:outline-none transition-all duration-150 shadow-2xs select-all">
                </div>
                <p class="text-[11px] text-slate-400 mt-2.5 font-medium">
                    Ketik 6 digit angka dari email atau tempel (paste) langsung ke kotak
                </p>
            </div>

            <!-- Action Button -->
            <div class="space-y-3">
                <button type="button"
                        @click="submitVerifyOtp()"
                        :disabled="otpLoading || otpDigits.some(d => d === '')"
                        :class="{ 'opacity-50 cursor-not-allowed': otpLoading || otpDigits.some(d => d === '') }"
                        class="w-full py-3.5 px-4 rounded-2xl bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-white font-bold text-sm shadow-lg shadow-blue-500/25 transition flex items-center justify-center gap-2 cursor-pointer">
                    <template x-if="otpLoading">
                        <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </template>
                    <span x-text="otpLoading ? 'Memverifikasi...' : 'Verifikasi & Selesaikan Pendaftaran'"></span>
                </button>

                <!-- Resend Timer -->
                <div class="text-xs text-slate-500 pt-1">
                    <template x-if="otpCountdown > 0">
                        <span>Kirim ulang kode dalam <strong class="text-blue-600 font-bold" x-text="otpCountdown + ' detik'"></strong></span>
                    </template>
                    <template x-if="otpCountdown <= 0">
                        <button type="button"
                                @click="resendOtp()"
                                :disabled="otpLoading"
                                class="font-bold text-blue-600 hover:text-blue-800 underline transition cursor-pointer">
                            Kirim Ulang Kode OTP
                        </button>
                    </template>
                </div>

                <div class="pt-2 border-t border-slate-100">
                    <button type="button"
                            @click="closeOtpModal()"
                            class="text-xs text-slate-400 hover:text-slate-600 font-semibold transition cursor-pointer">
                        &larr; Salah ketik email? Ubah data pendaftaran
                    </button>
                </div>
            </div>

        </div>
    </div>

    <!-- ======================================================== -->
    <!-- ALPINE.JS SPA ORCHESTRATOR & DUAL FLOW LOGIC             -->
    <!-- ======================================================== -->
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('authSpa', () => ({
                // Mode awal berdasarkan server-side atau URL
                mode: '{{ old('mode', $initialMode ?? (request()->is('register') ? 'register' : 'login')) }}',

                // State form register
                email: '{{ old('mode') === 'register' ? old('email', '') : '' }}',
                selectedCampus: '{{ old('mode') === 'register' ? old('campus', '') : '' }}',
                detectedDomain: '',
                isCampus: false,
                isPublic: false,
                isCampusMismatch: false,
                campusMismatchMessage: '',
                matchedCampusForEmail: '',

                // State OTP Verification Modal
                showOtpModal: {{ session('otp_required') ? 'true' : 'false' }},
                otpEmail: '{{ session('otp_email', '') }}',
                otpDigits: ['', '', '', '', '', ''],
                otpLoading: false,
                otpError: '',
                otpSuccessMessage: '',
                otpCountdown: 0,
                otpTimer: null,

                // State submit loading & error
                registerLoading: false,
                registerError: '',

                // Password strength
                password: '',
                passwordConfirmation: '',
                showPassword: false,
                showConfirmPassword: false,
                strengthScore: 0,
                strengthLabel: 'Lemah',

                // Upload KTM
                dragOver: false,
                ktmPreview: null,
                ktmFileName: '',
                ktmFileSize: '',
                ktmFileSelected: false,

                campusList: [
                    { name: 'Politeknik Negeri Malang', domains: ['polinema.ac.id', 'student.polinema.ac.id'] },
                    { name: 'Universitas Brawijaya', domains: ['ub.ac.id', 'student.ub.ac.id'] },
                    { name: 'Universitas Negeri Malang', domains: ['um.ac.id', 'student.um.ac.id'] },
                    { name: 'UIN Maulana Malik Ibrahim Malang', domains: ['uin-malang.ac.id', 'student.uin-malang.ac.id'] },
                    { name: 'Universitas Muhammadiyah Malang', domains: ['umm.ac.id', 'webmail.umm.ac.id'] },
                    { name: 'Institut Teknologi Nasional Malang', domains: ['itn.ac.id'] }
                ],

                publicDomains: [
                    'gmail.com',
                    'yahoo.com',
                    'ymail.com',
                    'rocketmail.com',
                    'hotmail.com',
                    'outlook.com',
                    'live.com',
                    'icloud.com'
                ],

                init() {
                    // Sinkronisasi dengan browser URL popstate (Back/Forward)
                    window.addEventListener('popstate', (e) => {
                        if (e.state && e.state.mode) {
                            this.mode = e.state.mode;
                        } else {
                            if (window.location.pathname.includes('register')) {
                                this.mode = 'register';
                            } else if (window.location.pathname.includes('forgot-password')) {
                                this.mode = 'forgot_password';
                            } else if (window.location.pathname.includes('reset-password')) {
                                this.mode = 'reset_password';
                            } else {
                                this.mode = 'login';
                            }
                        }
                        this.updatePageTitle();
                    });

                    if (this.email) {
                        this.detectEmail();
                    }

                    if (this.showOtpModal) {
                        this.startOtpCountdown(60);
                        this.focusOtpInput(0);
                    }
                },

                // Ganti mode Login <-> Register <-> Lupa Password tanpa reload halaman (SPA)
                switchMode(target) {
                    if (this.mode === target) return;
                    this.mode = target;
                    this.registerError = '';

                    const urlMap = {
                        'register': '{{ route('register') }}',
                        'login': '{{ route('login') }}',
                        'forgot_password': '{{ route('password.request') }}',
                        'reset_password': '{{ route('password.reset') }}'
                    };
                    const newUrl = urlMap[target] || '{{ route('login') }}';
                    window.history.pushState({ mode: target }, '', newUrl);
                    this.updatePageTitle();
                },

                updatePageTitle() {
                    if (this.mode === 'register') {
                        document.title = 'Buat Akun - Lungsur.in';
                    } else if (this.mode === 'forgot_password') {
                        document.title = 'Lupa Password - Lungsur.in';
                    } else if (this.mode === 'reset_password') {
                        document.title = 'Reset Password - Lungsur.in';
                    } else {
                        document.title = 'Login - Lungsur.in';
                    }
                },

                detectEmail() {
                    this.isCampusMismatch = false;
                    this.campusMismatchMessage = '';
                    this.matchedCampusForEmail = '';

                    const clean = this.email.trim().toLowerCase();
                    if (!clean.includes('@')) {
                        this.isCampus = false;
                        this.isPublic = false;
                        this.detectedDomain = '';
                        return;
                    }

                    const parts = clean.split('@');
                    this.detectedDomain = parts[1] || '';

                    if (!this.detectedDomain || !this.detectedDomain.includes('.')) {
                        this.isCampus = false;
                        this.isPublic = false;
                        return;
                    }

                    // Cari apakah domain cocok dengan salah satu kampus terdaftar
                    let foundCampus = null;
                    for (const item of this.campusList) {
                        const matches = item.domains.some(d => this.detectedDomain === d || this.detectedDomain.endsWith('.' + d));
                        if (matches) {
                            foundCampus = item.name;
                            break;
                        }
                    }

                    const isGenericAcId = this.detectedDomain.endsWith('.ac.id');

                    // 1. Jika domain cocok dengan salah satu kampus terdaftar
                    if (foundCampus) {
                        this.matchedCampusForEmail = foundCampus;

                        // Jika user belum memilih kampus, pilihkan otomatis
                        if (!this.selectedCampus) {
                            this.selectedCampus = foundCampus;
                        }

                        // JIKA USER MEMILIH KAMPUS BERBEDA: TOLAK LANGSUNG DI TAMPILAN!
                        if (this.selectedCampus && this.selectedCampus !== foundCampus) {
                            this.isCampusMismatch = true;
                            this.campusMismatchMessage = 'Domain email @' + this.detectedDomain + ' adalah milik ' + foundCampus + ', tidak sesuai dengan kampus ' + this.selectedCampus + ' yang Anda pilih.';
                            this.isCampus = false;
                            this.isPublic = false;
                            return;
                        }

                        this.isCampus = true;
                        this.isPublic = false;
                        return;
                    }

                    // 2. Jika user memilih kampus terdaftar, tapi email .ac.id kampus lain
                    if (this.selectedCampus && this.selectedCampus !== 'Lainnya' && isGenericAcId) {
                        const selectedConfig = this.campusList.find(c => c.name === this.selectedCampus);
                        if (selectedConfig) {
                            this.isCampusMismatch = true;
                            this.campusMismatchMessage = 'Kampus ' + this.selectedCampus + ' menggunakan domain resmi kampus terdaftar. Domain @' + this.detectedDomain + ' tidak sesuai.';
                            this.isCampus = false;
                            this.isPublic = false;
                            return;
                        }
                    }

                    // 3. Jika generic .ac.id dan memilih 'Lainnya'
                    if (isGenericAcId) {
                        this.isCampus = true;
                        this.isPublic = false;
                        return;
                    }

                    // 4. Email umum (Gmail, Yahoo, dll.)
                    const isMatchedPublic = this.publicDomains.some(domain => 
                        this.detectedDomain === domain || this.detectedDomain.endsWith('.' + domain)
                    );

                    if (isMatchedPublic || (!foundCampus && !isGenericAcId)) {
                        this.isCampus = false;
                        this.isPublic = true;
                    }
                },

                autoFixCampus() {
                    if (this.matchedCampusForEmail) {
                        this.selectedCampus = this.matchedCampusForEmail;
                        this.detectEmail();
                    }
                },

                checkPasswordStrength() {
                    let score = 0;
                    if (this.password.length >= 8) score++;
                    if (/[0-9]/.test(this.password)) score++;
                    if (/[A-Z]/.test(this.password)) score++;
                    if (/[^A-Za-z0-9]/.test(this.password)) score++;

                    this.strengthScore = score;
                    if (score <= 1) {
                        this.strengthLabel = 'Lemah';
                    } else if (score === 2) {
                        this.strengthLabel = 'Cukup';
                    } else if (score === 3) {
                        this.strengthLabel = 'Kuat';
                    } else {
                        this.strengthLabel = 'Sangat Kuat';
                    }
                },

                handleFileSelect(event) {
                    const file = event.target.files[0];
                    this.processFile(file);
                },

                handleDrop(event) {
                    this.dragOver = false;
                    const file = event.dataTransfer.files[0];
                    if (file) {
                        this.$refs.ktmInput.files = event.dataTransfer.files;
                        this.processFile(file);
                    }
                },

                processFile(file) {
                    if (!file) return;

                    if (!['image/jpeg', 'image/png', 'image/jpg'].includes(file.type)) {
                        alert('Format berkas harus berupa gambar JPG atau PNG.');
                        return;
                    }

                    if (file.size > 2 * 1024 * 1024) {
                        alert('Ukuran berkas melebihi batas maksimal 2 MB.');
                        return;
                    }

                    this.ktmFileName = file.name;
                    this.ktmFileSize = (file.size / 1024).toFixed(0) + ' KB';
                    this.ktmFileSelected = true;

                    const reader = new FileReader();
                    reader.onload = (e) => {
                        this.ktmPreview = e.target.result;
                    };
                    reader.readAsDataURL(file);
                },

                removeKtmFile() {
                    this.ktmPreview = null;
                    this.ktmFileName = '';
                    this.ktmFileSize = '';
                    this.ktmFileSelected = false;
                    this.$refs.ktmInput.value = '';
                },

                validateBeforeSubmit(event) {
                    if (this.isCampusMismatch) {
                        alert(this.campusMismatchMessage || 'Pilihan kampus dan domain email tidak cocok!');
                        event.preventDefault();
                        return false;
                    }

                    if (this.isPublic && !this.ktmFileSelected) {
                        alert('Karena Anda menggunakan email umum, Anda wajib mengunggah foto KTM untuk verifikasi!');
                        event.preventDefault();
                        return false;
                    }
                    return true;
                },

                startOtpCountdown(seconds = 60) {
                    this.otpCountdown = seconds;
                    if (this.otpTimer) clearInterval(this.otpTimer);
                    this.otpTimer = setInterval(() => {
                        if (this.otpCountdown > 0) {
                            this.otpCountdown--;
                        } else {
                            clearInterval(this.otpTimer);
                        }
                    }, 1000);
                },

                focusOtpInput(index) {
                    this.$nextTick(() => {
                        const target = this.$refs['otpInput' + index];
                        if (target) {
                            target.focus();
                            target.select();
                        }
                    });
                },

                handleOtpInput(event, index) {
                    const rawVal = event.target.value;
                    const digits = rawVal.replace(/[^0-9]/g, '');

                    // Jika user paste atau ketik lebih dari 1 digit sekaligus di kotak ini
                    if (digits.length > 1) {
                        const splitDigits = digits.slice(0, 6).split('');
                        for (let i = 0; i < 6; i++) {
                            this.otpDigits[i] = splitDigits[i] || '';
                        }
                        const nextIndex = Math.min(splitDigits.length - 1, 5);
                        this.focusOtpInput(nextIndex);
                        if (this.otpDigits.every(d => d !== '')) {
                            this.$nextTick(() => {
                                this.submitVerifyOtp();
                            });
                        }
                        return;
                    }

                    this.otpDigits[index] = digits;

                    // Otomatis geser kursor ke kotak berikutnya setelah angka diketik
                    if (digits && index < 5) {
                        this.focusOtpInput(index + 1);
                    }

                    // Otomatis verifikasi jika 6 digit sudah lengkap
                    if (this.otpDigits.every(d => d !== '')) {
                        this.$nextTick(() => {
                            this.submitVerifyOtp();
                        });
                    }
                },

                handleOtpKeydown(event, index) {
                    if (event.key === 'Enter') {
                        event.preventDefault();
                        if (this.otpDigits.every(d => d !== '')) {
                            this.submitVerifyOtp();
                        }
                        return;
                    }

                    if (event.key === 'Backspace') {
                        // Jika kotak saat ini kosong dan bukan kotak pertama, mundur dan hapus kotak sebelumnya
                        if (!this.otpDigits[index] && index > 0) {
                            event.preventDefault();
                            this.otpDigits[index - 1] = '';
                            this.focusOtpInput(index - 1);
                        } else {
                            this.otpDigits[index] = '';
                        }
                    } else if (event.key === 'ArrowLeft' && index > 0) {
                        event.preventDefault();
                        this.focusOtpInput(index - 1);
                    } else if (event.key === 'ArrowRight' && index < 5) {
                        event.preventDefault();
                        this.focusOtpInput(index + 1);
                    } else if (event.key === 'Delete') {
                        this.otpDigits[index] = '';
                    }
                },

                handleOtpPaste(event) {
                    event.preventDefault();
                    event.stopPropagation();
                    const rawText = (event.clipboardData || window.clipboardData).getData('text') || '';
                    const pasted = rawText.trim().replace(/[^0-9]/g, '');

                    if (pasted.length >= 6) {
                        const digits = pasted.slice(0, 6).split('');
                        for (let i = 0; i < 6; i++) {
                            this.otpDigits[i] = digits[i];
                        }
                        this.focusOtpInput(5);
                        this.$nextTick(() => {
                            this.submitVerifyOtp();
                        });
                    } else if (pasted.length > 0) {
                        const digits = pasted.split('');
                        for (let i = 0; i < 6; i++) {
                            this.otpDigits[i] = digits[i] || '';
                        }
                        this.focusOtpInput(Math.min(digits.length, 5));
                    }
                },

                async submitRegister(event) {
                    if (!this.validateBeforeSubmit(event)) return;

                    this.registerLoading = true;
                    this.registerError = '';

                    const form = event.target;
                    const formData = new FormData(form);

                    try {
                        const response = await fetch('{{ route('register') }}', {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json'
                            }
                        });

                        const data = await response.json();

                        if (response.ok && data.status === 'otp_required') {
                            this.otpEmail = data.email;
                            this.otpDigits = ['', '', '', '', '', ''];
                            this.otpError = '';
                            this.otpSuccessMessage = '';
                            this.showOtpModal = true;
                            this.startOtpCountdown(data.resend_cooldown || 60);
                            this.focusOtpInput(0);
                        } else if (response.ok && data.status === 'pending_verification') {
                            alert(data.message || 'Pendaftaran berhasil diajukan! Berkas KTM Anda telah kami terima dan sedang dalam antrean verifikasi administrator.');
                            window.location.href = data.redirect || '{{ route('login') }}';
                        } else if (!response.ok) {
                            if (data.errors) {
                                const firstErrKey = Object.keys(data.errors)[0];
                                this.registerError = data.errors[firstErrKey][0];
                            } else {
                                this.registerError = data.message || 'Terjadi kesalahan saat pendaftaran. Silakan periksa kembali formulir.';
                            }
                        }
                    } catch (err) {
                        this.registerError = 'Gagal menghubungi server. Periksa koneksi internet Anda.';
                    } finally {
                        this.registerLoading = false;
                    }
                },

                async submitVerifyOtp() {
                    if (this.otpLoading) return;

                    const otpCode = this.otpDigits.join('').trim();
                    if (otpCode.length < 6) {
                        this.otpError = 'Harap lengkapi 6 digit kode OTP.';
                        return;
                    }

                    this.otpLoading = true;
                    this.otpError = '';

                    try {
                        const response = await fetch('{{ route('register.verify_otp') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                email: this.otpEmail,
                                otp: otpCode
                            })
                        });

                        const data = await response.json();

                        if (response.ok && data.redirect) {
                            if (data.status === 'pending_verification') {
                                alert(data.message || 'Verifikasi email berhasil! Berkas KTM Anda telah kami terima dan sedang dalam antrean verifikasi administrator.');
                            }
                            window.location.href = data.redirect;
                        } else {
                            this.otpError = data.message || 'Kode OTP yang Anda masukkan tidak sesuai.';
                        }
                    } catch (err) {
                        this.otpError = 'Gagal memverifikasi OTP. Coba beberapa saat lagi.';
                    } finally {
                        this.otpLoading = false;
                    }
                },

                async resendOtp() {
                    if (this.otpCountdown > 0) return;

                    this.otpLoading = true;
                    this.otpError = '';

                    try {
                        const response = await fetch('{{ route('register.resend_otp') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                email: this.otpEmail
                            })
                        });

                        const data = await response.json();

                        if (response.ok) {
                            this.otpSuccessMessage = data.message || 'Kode OTP baru telah berhasil dikirimkan ke email Anda.';
                            this.otpDigits = ['', '', '', '', '', ''];
                            this.startOtpCountdown(data.resend_cooldown || 60);
                            this.focusOtpInput(0);
                        } else {
                            this.otpError = data.message || 'Gagal mengirim ulang kode OTP.';
                        }
                    } catch (err) {
                        this.otpError = 'Terjadi kesalahan jaringan saat meminta kode baru.';
                    } finally {
                        this.otpLoading = false;
                    }
                },

                closeOtpModal() {
                    this.showOtpModal = false;
                    this.otpError = '';
                    this.otpSuccessMessage = '';
                    if (this.otpTimer) clearInterval(this.otpTimer);
                }
            }));
        });
    </script>

</body>
</html>
