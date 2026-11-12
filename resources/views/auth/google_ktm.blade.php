<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-100/70">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Lengkapi Berkas KTM - Lungsur.in</title>

    <!-- Vite Assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-100/70 flex flex-col justify-center items-center py-6 sm:py-10 px-4 sm:px-6 lg:px-8 font-sans antialiased text-slate-900 selection:bg-blue-600 selection:text-white">

    <!-- Card Container -->
    <div class="max-w-2xl w-full bg-white rounded-[2rem] sm:rounded-[2.25rem] shadow-2xl shadow-slate-300/50 border border-slate-200/80 overflow-hidden p-6 sm:p-10 transition-all duration-300"
         x-data="googleKtmForm()">

        <!-- Header Info -->
        <div class="text-center mb-8">
            <a href="{{ route('home') }}" class="inline-flex items-center gap-2 mb-4 group">
                <span class="w-10 h-10 rounded-2xl bg-blue-600 text-white font-black text-xl flex items-center justify-center shadow-md shadow-blue-500/30 group-hover:scale-105 transition">
                    L
                </span>
                <span class="text-xl font-black tracking-tight text-slate-900">
                    Lungsur<span class="text-blue-600">.in</span>
                </span>
            </a>

            <div class="w-12 h-12 rounded-2xl bg-emerald-50 border border-emerald-100 text-emerald-600 mx-auto flex items-center justify-center mb-3">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                </svg>
            </div>

            <h1 class="text-2xl sm:text-3xl font-black tracking-tight text-slate-900">
                Verifikasi Identitas Mahasiswa (KTM)
            </h1>
            <p class="mt-2 text-sm text-slate-500 max-w-md mx-auto leading-relaxed">
                Akun Google Anda menggunakan email umum (<strong>{{ $user->email }}</strong>). Lengkapi data dan unggah foto KTM aktif Anda agar dapat disetujui administrator.
            </p>
        </div>

        <!-- Google User Banner -->
        <div class="mb-6 p-4 rounded-2xl bg-slate-50 border border-slate-200 flex items-center gap-3">
            @if ($user->avatar)
                <img src="{{ $user->avatar }}" alt="{{ $user->name }}" class="w-11 h-11 rounded-full object-cover border border-slate-200 shrink-0">
            @else
                <div class="w-11 h-11 rounded-full bg-blue-600 text-white font-bold flex items-center justify-center shrink-0">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
            @endif
            <div class="min-w-0 flex-1">
                <div class="text-sm font-bold text-slate-900 truncate">{{ $user->name }}</div>
                <div class="text-xs text-slate-500 truncate">{{ $user->email }}</div>
            </div>
            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200 shrink-0">
                Menunggu Berkas
            </span>
        </div>

        <!-- Error Alert -->
        @if ($errors->any())
            <div class="mb-6 p-4 rounded-2xl bg-red-50 border border-red-200 text-red-800 text-xs font-medium space-y-1">
                @foreach ($errors->all() as $error)
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-red-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>{{ $error }}</span>
                    </div>
                @endforeach
            </div>
        @endif

        <!-- Formulir Unggah KTM -->
        <form action="{{ route('auth.google.ktm.submit') }}" 
              method="POST" 
              enctype="multipart/form-data" 
              class="space-y-4">
            @csrf

            <!-- Pilihan Kampus -->
            <div>
                <label for="campus" class="block text-xs font-bold text-slate-700 mb-1">
                    Asal Kampus / Perguruan Tinggi <span class="text-red-500">*</span>
                </label>
                <select name="campus" 
                        id="campus" 
                        required 
                        class="w-full px-4 py-2.5 rounded-2xl border border-slate-200 bg-white text-slate-900 font-medium text-sm focus:outline-none focus:border-blue-600 focus:ring-4 focus:ring-blue-100 transition shadow-2xs">
                    <option value="" disabled selected>Pilih Kampus</option>
                    @foreach ($campusList as $c)
                        <option value="{{ $c->campus_name }}" {{ old('campus', $user->campus) === $c->campus_name ? 'selected' : '' }}>
                            {{ $c->campus_name }}
                        </option>
                    @endforeach
                    <option value="Lainnya" {{ old('campus', $user->campus) === 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                </select>
            </div>

            <!-- NIM -->
            <div>
                <label for="nim" class="block text-xs font-bold text-slate-700 mb-1">
                    Nomor Induk Mahasiswa (NIM) <span class="text-red-500">*</span>
                </label>
                <input type="text" 
                       name="nim" 
                       id="nim" 
                       value="{{ old('nim', $user->nim) }}" 
                       placeholder="Contoh: 244107060059" 
                       required 
                       class="w-full px-4 py-2.5 rounded-2xl border border-slate-200 bg-white text-slate-900 font-medium text-sm focus:outline-none focus:border-blue-600 focus:ring-4 focus:ring-blue-100 transition shadow-2xs">
            </div>

            <!-- Upload Foto KTM -->
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">
                    Foto Kartu Tanda Mahasiswa (KTM) <span class="text-red-500">*</span>
                </label>

                <div class="border-2 border-dashed border-slate-300 hover:border-blue-500 rounded-2xl p-5 text-center transition cursor-pointer bg-slate-50/50 hover:bg-blue-50/30"
                     @click="$refs.ktmInput.click()"
                     @dragover.prevent="dragOver = true"
                     @dragleave.prevent="dragOver = false"
                     @drop.prevent="handleDrop($event)"
                     :class="{ 'border-blue-500 bg-blue-50/40': dragOver }">

                    <input type="file" 
                           name="ktm_file" 
                           x-ref="ktmInput" 
                           @change="handleFileSelect($event)" 
                           accept="image/jpeg,image/png,image/jpg" 
                           class="hidden" 
                           required>

                    <!-- Belum pilih file -->
                    <template x-if="!ktmPreview">
                        <div class="space-y-2">
                            <div class="w-12 h-12 rounded-2xl bg-white shadow-2xs border border-slate-200 text-blue-600 mx-auto flex items-center justify-center">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <div class="text-xs font-bold text-slate-700">
                                Klik untuk unggah atau seret foto KTM ke sini
                            </div>
                            <div class="text-[11px] text-slate-400">
                                Format JPG atau PNG, maksimal 2 MB
                            </div>
                        </div>
                    </template>

                    <!-- Preview Foto Terpilih -->
                    <template x-if="ktmPreview">
                        <div class="space-y-3">
                            <div class="relative inline-block mx-auto">
                                <img :src="ktmPreview" alt="Pratinjau KTM" class="max-h-48 rounded-xl object-contain border border-slate-200 shadow-sm mx-auto">
                                <button type="button" 
                                        @click.stop="removeKtmFile()" 
                                        class="absolute -top-2 -right-2 w-7 h-7 rounded-full bg-red-600 text-white font-bold flex items-center justify-center shadow hover:bg-red-700 transition">
                                    &times;
                                </button>
                            </div>
                            <div class="text-xs font-semibold text-slate-600">
                                <span x-text="ktmFileName"></span> (<span x-text="ktmFileSize"></span>)
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Tombol Aksi -->
            <div class="pt-4 space-y-2.5">
                <button type="submit" 
                        class="w-full py-3.5 px-4 rounded-2xl bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-white font-bold text-sm tracking-wide shadow-md shadow-blue-500/20 focus:outline-none focus:ring-4 focus:ring-blue-100 transition cursor-pointer flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <span>Ajukan Verifikasi Berkas KTM</span>
                </button>

                <a href="{{ route('login') }}" 
                   class="block text-center text-xs font-semibold text-slate-500 hover:text-slate-700 py-2 transition">
                    &larr; Batalkan &amp; Kembali ke Halaman Login
                </a>
            </div>
        </form>

    </div>

    <!-- Script Alpine.js Form -->
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('googleKtmForm', () => ({
                dragOver: false,
                ktmPreview: null,
                ktmFileName: '',
                ktmFileSize: '',

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
                    if (this.$refs.ktmInput) {
                        this.$refs.ktmInput.value = '';
                    }
                }
            }));
        });
    </script>

</body>
</html>
