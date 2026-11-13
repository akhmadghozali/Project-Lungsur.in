@extends('layouts.app')

@section('content')
@php
    $currentUser = auth()->user();
    $userData = $currentUser ? [
        'id' => $currentUser->id,
        'name' => $currentUser->name,
        'campus' => $currentUser->campus,
        'is_verified' => $currentUser->verification_status === 'active'
    ] : null;
@endphp
<div class="flex-1 w-full flex flex-col min-w-0" 
      x-data="chatApp({{ \Illuminate\Support\Js::from($selectedListing) }}, {{ \Illuminate\Support\Js::from($userData) }})" 
      x-init="initChat()">
    
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs flex-1 flex overflow-hidden min-h-[720px] max-h-[860px]">
        
        <!-- ======================================================== -->
        <!-- 1. CONVERSATION SIDEBAR (KOLOM KIRI: DAFTAR KONTAK CHAT) -->
        <!-- ======================================================== -->
        <aside class="w-full md:w-[350px] lg:w-[390px] border-r border-slate-200/80 flex flex-col bg-white flex-shrink-0"
               :class="activeRoom ? 'hidden md:flex' : 'flex'">
            
            <!-- Sidebar Header & Search -->
            <div class="p-4 border-b border-slate-100 space-y-3">
                <div class="flex items-center justify-between">
                    <h1 class="text-xl font-bold text-slate-900 tracking-tight">Pesan</h1>
                    <span class="text-xs bg-slate-100 text-slate-600 px-2.5 py-0.5 rounded-full font-medium" 
                          x-text="chatRooms.length + ' Aktif'"></span>
                </div>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-slate-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path>
                        </svg>
                    </span>
                    <input type="text" 
                           x-model="searchQuery" 
                           class="w-full pl-9 pr-4 py-2 text-xs bg-slate-50 border border-slate-200/90 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition placeholder:text-slate-400" 
                           placeholder="Cari obrolan atau iklan..."/>
                </div>
            </div>

            <!-- Loading Skeleton -->
            <div x-show="isLoadingRooms" class="p-4 space-y-3 flex-1 overflow-y-auto">
                <template x-for="i in 4" :key="i">
                    <div class="flex items-center gap-3 p-2 animate-pulse">
                        <div class="w-12 h-12 bg-slate-200 rounded-full flex-shrink-0"></div>
                        <div class="flex-1 space-y-2">
                            <div class="h-3.5 bg-slate-200 rounded-md w-3/4"></div>
                            <div class="h-2.5 bg-slate-100 rounded-md w-1/2"></div>
                        </div>
                    </div>
                </template>
            </div>

            <!-- Empty State: Belum Ada Percakapan -->
            <div x-show="!isLoadingRooms && chatRooms.length === 0" class="flex-1 flex flex-col items-center justify-center p-6 text-center space-y-3">
                <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                    </svg>
                </div>
                <div class="space-y-1">
                    <p class="text-xs font-bold text-slate-800">Belum Ada Percakapan</p>
                    <p class="text-[11px] text-slate-400 leading-relaxed max-w-[240px] mx-auto">
                        Cari barang kos atau kuliah yang kamu butuhkan, lalu klik "Hubungi Penjual" untuk mulai berdiskusi.
                    </p>
                </div>
                <a href="{{ route('listings.index') }}" 
                   class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl bg-blue-600 text-white text-xs font-bold hover:bg-blue-700 transition shadow-xs">
                    <span>Jelajahi Katalog</span>
                </a>
            </div>

            <!-- Empty Search Results -->
            <div x-show="!isLoadingRooms && chatRooms.length > 0 && filteredRooms.length === 0" class="flex-1 flex flex-col items-center justify-center p-6 text-center text-slate-400 space-y-1 text-xs">
                <p class="font-semibold text-slate-600">Tidak ada percakapan ditemukan</p>
                <p class="text-[11px]">Coba kata kunci nama lawan bicara atau judul barang lain.</p>
            </div>

            <!-- Conversation Item List -->
            <div x-show="!isLoadingRooms && filteredRooms.length > 0" class="flex-1 overflow-y-auto divide-y divide-slate-100">
                <template x-for="room in filteredRooms" :key="room.id">
                    <div @click="selectRoom(room)" 
                         class="flex items-center gap-3 p-3.5 cursor-pointer transition select-none"
                         :class="activeRoom && activeRoom.id === room.id ? 'bg-blue-50/70 border-l-4 border-blue-600' : 'hover:bg-slate-50'">
                        
                        <!-- Avatar & Status Dot -->
                        <div class="relative flex-shrink-0">
                            <div class="w-11 h-11 rounded-full bg-slate-900 text-white font-bold flex items-center justify-center text-xs shadow-xs" 
                                 x-text="room.initials"></div>
                            <span class="absolute bottom-0 right-0 w-3 h-3 border-2 border-white rounded-full" 
                                  :class="room.is_online ? 'bg-emerald-500' : 'bg-slate-300'"></span>
                        </div>

                        <!-- Info Room -->
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between mb-0.5">
                                <h2 class="text-xs sm:text-sm font-bold truncate text-slate-900" x-text="room.name"></h2>
                                <span class="text-[10px] font-semibold flex-shrink-0 ml-1 text-slate-400" x-text="room.time"></span>
                            </div>
                            
                            <!-- Judul Listing Terkait (jika ada) -->
                            <template x-if="room.listing && room.listing.title">
                                <p class="text-[10px] font-semibold text-blue-600 truncate mb-0.5 flex items-center gap-1">
                                    <svg class="w-2.5 h-2.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                                    <span x-text="room.listing.title"></span>
                                </p>
                            </template>

                            <p class="text-xs truncate" 
                               :class="room.unread_count > 0 ? 'text-slate-900 font-bold' : 'text-slate-500'" 
                               x-text="room.last_message"></p>
                        </div>

                        <!-- Unread Counter Badge -->
                        <span x-show="room.unread_count > 0" 
                              class="w-5 h-5 rounded-full bg-blue-600 text-white text-[10px] font-bold flex items-center justify-center flex-shrink-0 shadow-xs" 
                              x-text="room.unread_count"></span>
                    </div>
                </template>
            </div>
        </aside>

        <!-- ======================================================== -->
        <!-- 2. ACTIVE CHAT PANEL (KOLOM KANAN: RUANG OBROLAN AKTIF)  -->
        <!-- ======================================================== -->
        <section class="flex-1 flex flex-col bg-white overflow-hidden relative"
                 :class="!activeRoom ? 'hidden md:flex' : 'flex'">
            
            <!-- KONDISI A: JIKA BELUM MEMILIH ROOM CHAT -->
            <div x-show="!activeRoom" class="flex-1 w-full h-full flex items-center justify-center p-6 bg-gradient-to-br from-slate-50 via-blue-50/20 to-slate-50">
                <div class="max-w-md w-full p-8 bg-white/95 backdrop-blur-md rounded-3xl border border-slate-200/70 shadow-lg shadow-blue-500/5 text-center space-y-4">
                    
                    <!-- Icon Wadah Chat Lungsur.in -->
                    <div class="w-16 h-16 rounded-2xl bg-blue-600 flex items-center justify-center shadow-lg shadow-blue-500/25 text-white mx-auto">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                        </svg>
                    </div>

                    <!-- Teks Sapaan Ramah -->
                    <div class="space-y-1.5">
                        <h2 class="text-lg sm:text-xl font-extrabold text-slate-900 tracking-tight">
                            Ruang Obrolan &amp; Negosiasi
                        </h2>
                        <p class="text-xs sm:text-sm text-slate-500 leading-relaxed">
                            Pilih salah satu percakapan di kolom kiri untuk menawar harga, menanyakan kondisi barang, dan mengatur janji temu COD langsung sesama mahasiswa Malang.
                        </p>
                    </div>

                    <!-- Tips Transaksi COD Bersih -->
                    <div class="pt-2">
                        <span class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full bg-blue-50 text-blue-700 text-xs font-semibold border border-blue-100">
                            <svg class="w-3.5 h-3.5 text-blue-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                            <span>Tips: Cek fisik barang secara teliti sebelum menyelesaikan deal COD.</span>
                        </span>
                    </div>

                </div>
            </div>

            <!-- KONDISI B: JIKA ROOM CHAT AKTIF DIPILIH -->
            <div x-show="activeRoom" class="flex-1 flex flex-col h-full overflow-hidden" style="display: none;">
                
                <!-- Active Chat Header -->
                <div class="px-4 sm:px-6 py-3 border-b border-slate-200/80 flex items-center justify-between bg-white z-10 flex-shrink-0">
                    <div class="flex items-center gap-3">
                        
                        <!-- Tombol Kembali ke Sidebar di Layar Ponsel -->
                        <button type="button" 
                                @click="activeRoom = null" 
                                class="md:hidden p-1.5 text-slate-500 hover:text-slate-800 rounded-lg hover:bg-slate-100 transition mr-0.5">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/>
                            </svg>
                        </button>

                        <div class="relative flex-shrink-0">
                            <div class="w-10 h-10 rounded-full bg-slate-900 text-white font-bold flex items-center justify-center text-xs shadow-xs" 
                                 x-text="activeRoom?.initials"></div>
                            <span class="absolute bottom-0 right-0 w-2.5 h-2.5 bg-emerald-500 border-2 border-white rounded-full"></span>
                        </div>
                        <div>
                            <h2 class="text-xs sm:text-sm font-bold text-slate-900 leading-tight" x-text="activeRoom?.name"></h2>
                            <p class="text-[11px] text-emerald-600 font-medium flex items-center gap-1.5 mt-0.5">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                <span x-text="activeRoom?.statusText"></span>
                            </p>
                        </div>
                    </div>

                    <!-- Indikator Komunitas Kampus -->
                    <div class="hidden sm:inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-slate-100 text-slate-600 text-[11px] font-semibold border border-slate-200">
                        <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/>
                        </svg>
                        <span>Mahasiswa Malang</span>
                    </div>
                </div>

                <!-- Alert Pesan Error jika Ada Masalah -->
                <template x-if="errorMessage">
                    <div class="mx-4 sm:mx-6 mt-3 px-4 py-2.5 rounded-xl bg-amber-50 border border-amber-200 text-amber-800 text-xs flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-amber-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                            <span x-text="errorMessage"></span>
                        </div>
                        <button type="button" @click="errorMessage = null" class="text-amber-600 hover:text-amber-800 text-xs font-bold ml-2">Tutup</button>
                    </div>
                </template>

                <!-- Pinned Related Product Card (Barang Iklan yang Sedang Dibahas) -->
                <div class="mx-4 sm:mx-6 mt-3 px-4 py-2.5 bg-blue-50/70 border border-blue-100 rounded-xl flex items-center justify-between flex-shrink-0"
                     x-show="currentListing">
                    <div class="flex items-center gap-3 min-w-0">
                        <div class="w-9 h-9 rounded-lg bg-blue-100 text-blue-600 flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                            </svg>
                        </div>
                        <div class="min-w-0">
                            <h3 class="text-xs font-bold text-slate-800 truncate" x-text="currentListing?.title"></h3>
                            <p class="text-xs font-bold text-blue-600" 
                               x-text="'Rp ' + Number(currentListing?.price || 0).toLocaleString('id-ID')"></p>
                        </div>
                    </div>
                    <a :href="'/katalog/' + currentListing?.id" 
                       class="text-xs font-bold text-blue-600 hover:text-blue-700 hover:underline flex-shrink-0 ml-3">
                        Lihat Iklan
                    </a>
                </div>

                <!-- Chat History Scroll Area -->
                <div class="flex-1 overflow-y-auto p-4 sm:p-6 space-y-4" x-ref="messageContainer">
                    
                    <!-- Loading Indicator Saat Memuat Pesan -->
                    <div x-show="isLoadingMessages" class="flex items-center justify-center py-6">
                        <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-slate-100 text-slate-500 text-xs font-medium">
                            <svg class="w-4 h-4 animate-spin text-blue-600" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                            </svg>
                            <span>Memuat riwayat obrolan...</span>
                        </div>
                    </div>

                    <!-- Divider Riwayat Pesan -->
                    <div x-show="!isLoadingMessages" class="flex items-center justify-center my-2">
                        <span class="px-3 py-1 bg-slate-100 text-slate-500 rounded-full text-[10px] font-semibold tracking-wide">
                            Pesan Terenkripsi Komunitas Kampus
                        </span>
                    </div>

                    <!-- Pesan Kosong / Baru Memulai -->
                    <div x-show="!isLoadingMessages && messages.length === 0" class="text-center py-10 space-y-2">
                        <div class="w-10 h-10 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mx-auto">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                            </svg>
                        </div>
                        <p class="text-xs font-bold text-slate-700">Belum ada pesan</p>
                        <p class="text-[11px] text-slate-400 max-w-xs mx-auto">Kirim pesan pertama atau gunakan opsi pesan instan di bawah untuk memulai percakapan.</p>
                    </div>

                    <!-- Loop Pesan -->
                    <template x-for="msg in messages" :key="msg.id">
                        <div class="flex flex-col" :class="msg.sender === 'me' ? 'items-end ml-auto max-w-md' : 'items-start max-w-md'">
                            
                            <div class="px-4 py-2.5 shadow-xs rounded-2xl break-words" 
                                 :class="msg.sender === 'me' ? 'bg-blue-600 text-white rounded-tr-xs' : 'bg-slate-100 text-slate-800 rounded-tl-xs'">
                                <p class="text-xs sm:text-sm leading-relaxed whitespace-pre-wrap" x-text="msg.text"></p>
                                
                                <div class="flex items-center justify-end gap-1 mt-1 text-[10px]" 
                                     :class="msg.sender === 'me' ? 'text-blue-100' : 'text-slate-400'">
                                    <span x-text="msg.time"></span>
                                    
                                    <template x-if="msg.sender === 'me'">
                                        <span class="inline-flex items-center">
                                            <!-- Jam (Pending) -->
                                            <template x-if="msg.is_pending">
                                                <svg class="w-3 h-3 text-blue-200 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>
                                            </template>
                                            <!-- Centang Ganda (Read) -->
                                            <template x-if="!msg.is_pending && msg.is_read">
                                                <svg class="w-3.5 h-3.5 text-blue-100" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7m-14 4l4 4L19 7"/>
                                                </svg>
                                            </template>
                                            <!-- Centang Tunggal (Sent) -->
                                            <template x-if="!msg.is_pending && !msg.is_read">
                                                <svg class="w-3 h-3 text-blue-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                                </svg>
                                            </template>
                                        </span>
                                    </template>
                                </div>
                            </div>

                            <!-- Indikator Gagal Kirim -->
                            <template x-if="msg.is_failed">
                                <span class="text-[10px] text-red-500 font-semibold mt-0.5">Gagal terkirim. Periksa koneksi internet.</span>
                            </template>
                        </div>
                    </template>
                </div>

                <!-- Quick Reply Bar (Tanpa Emoji / Gaya Mahasiswa Kasual) -->
                <div class="px-4 sm:px-6 pb-2 pt-1 flex items-center gap-2 overflow-x-auto no-scrollbar border-t border-slate-100">
                    <span class="text-[11px] font-semibold text-slate-400 shrink-0">Pesan Cepat:</span>
                    <button type="button" 
                            @click="sendQuickReply('Halo kak, apakah barang ini masih ada?')" 
                            class="px-3 py-1 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-medium rounded-full transition flex-shrink-0 cursor-pointer">
                        Barang masih ada?
                    </button>
                    <button type="button" 
                            @click="sendQuickReply('Bisa COD di kampus mana ya kak?')" 
                            class="px-3 py-1 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-medium rounded-full transition flex-shrink-0 cursor-pointer">
                        Bisa COD di mana?
                    </button>
                    <button type="button" 
                            @click="sendQuickReply('Halo, boleh nego tipis nggak kak?')" 
                            class="px-3 py-1 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-medium rounded-full transition flex-shrink-0 cursor-pointer">
                        Boleh nego tipis?
                    </button>
                    <button type="button" 
                            @click="sendQuickReply('Boleh minta foto kondisi aslinya kak?')" 
                            class="px-3 py-1 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-medium rounded-full transition flex-shrink-0 cursor-pointer">
                        Minta foto asli
                    </button>
                </div>

                <!-- Chat Input Form -->
                <div class="p-3 sm:p-4 bg-white border-t border-slate-100 flex-shrink-0">
                    <form @submit.prevent="sendMessage()" class="flex items-center gap-2">
                        <div class="flex-1">
                            <input type="text" 
                                   x-model="newMessage" 
                                   :disabled="isSending"
                                   class="w-full px-4 py-2.5 text-xs sm:text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/25 transition placeholder:text-slate-400" 
                                   placeholder="Tulis pesan untuk berdiskusi atau COD..."/>
                        </div>
                        <button type="submit" 
                                :disabled="!newMessage.trim() || isSending"
                                :class="(!newMessage.trim() || isSending) ? 'opacity-50 cursor-not-allowed bg-slate-300' : 'bg-blue-600 hover:bg-blue-700 shadow-md shadow-blue-500/20 cursor-pointer'"
                                class="w-10 h-10 text-white rounded-xl flex items-center justify-center transition flex-shrink-0" 
                                title="Kirim Pesan">
                            <svg class="w-4 h-4 translate-x-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path d="M5 12h14M12 5l7 7-7 7" stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"></path>
                            </svg>
                        </button>
                    </form>
                </div>

            </div>

        </section>

    </div>
</div>

<!-- Alpine.js Interactivity Script -->
<script>
function chatApp(initialListing = null, currentUser = null) {
    return {
        currentUser: currentUser || { id: null, name: '', campus: '', is_verified: true },
        currentListing: initialListing || null,
        activeRoom: null,
        chatRooms: [],
        messages: [],
        searchQuery: '',
        newMessage: '',
        isLoadingRooms: true,
        isLoadingMessages: false,
        isSending: false,
        errorMessage: null,
        pollTimer: null,

        get csrfToken() {
            return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
        },

        get filteredRooms() {
            if (!this.searchQuery.trim()) return this.chatRooms;
            const q = this.searchQuery.toLowerCase();
            return this.chatRooms.filter(room => 
                (room.name && room.name.toLowerCase().includes(q)) ||
                (room.listing && room.listing.title && room.listing.title.toLowerCase().includes(q)) ||
                (room.last_message && room.last_message.toLowerCase().includes(q))
            );
        },

        getInitials(name) {
            if (!name) return 'U';
            const parts = name.trim().split(/\s+/);
            if (parts.length === 1) return parts[0].substring(0, 2).toUpperCase();
            return (parts[0][0] + parts[1][0]).toUpperCase();
        },

        formatTime(dateString) {
            if (!dateString) return '';
            try {
                const date = new Date(dateString);
                if (isNaN(date.getTime())) return dateString;
                
                const now = new Date();
                const diffMs = now - date;
                const diffSec = Math.floor(diffMs / 1000);
                const diffMin = Math.floor(diffSec / 60);
                const diffHour = Math.floor(diffMin / 60);
                const diffDay = Math.floor(diffHour / 24);

                if (diffSec < 60) return 'Baru saja';
                if (diffMin < 60) return `${diffMin} mnt lalu`;
                if (diffHour < 24 && date.getDate() === now.getDate()) {
                    return date.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }).replace('.', ':');
                }
                if (diffDay === 1 || (diffDay < 2 && date.getDate() === now.getDate() - 1)) {
                    return 'Kemarin';
                }
                if (diffDay < 7) {
                    const days = ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'];
                    return days[date.getDay()];
                }
                return date.toLocaleDateString('id-ID', { day: 'numeric', month: 'short' });
            } catch (e) {
                return '';
            }
        },

        formatClock(dateString) {
            if (!dateString) return '';
            try {
                const date = new Date(dateString);
                return date.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }).replace('.', ':');
            } catch (e) {
                return '';
            }
        },

        async initChat() {
            await this.loadConversations();

            // Jika user diarahkan dari detail produk (?listing_id=...)
            if (this.currentListing) {
                await this.handleListingInitiation();
            }

            // Polling pembaruan berkala setiap 4 detik
            this.startPolling();
        },

        async handleListingInitiation() {
            if (!this.currentListing) return;

            // Mencegah chat ke barang jualan sendiri
            if (this.currentUser && this.currentUser.id && this.currentListing.user_id === this.currentUser.id) {
                this.errorMessage = 'Anda tidak dapat mengirim pesan pada barang jualan Anda sendiri.';
                return;
            }

            // Cari apakah percakapan untuk listing ini sudah ada di daftar
            const existingRoom = this.chatRooms.find(r => r.listing && r.listing.id === this.currentListing.id);
            if (existingRoom) {
                await this.selectRoom(existingRoom);
                return;
            }

            // Jika belum ada di list lokal, inisiasi percakapan baru via API
            try {
                const res = await fetch('/chat/api/conversations', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': this.csrfToken
                    },
                    body: JSON.stringify({
                        listing_id: this.currentListing.id
                    })
                });

                const json = await res.json();
                if (res.ok && json.status === 'success') {
                    await this.loadConversations(true);
                    const newRoom = this.chatRooms.find(r => r.id === json.data.conversation_id);
                    if (newRoom) {
                        await this.selectRoom(newRoom);
                    }
                } else if (json.message) {
                    this.errorMessage = json.message;
                }
            } catch (err) {
                console.error('Gagal memulai percakapan:', err);
            }
        },

        async loadConversations(isSilent = false) {
            if (!isSilent) this.isLoadingRooms = true;
            try {
                const res = await fetch('/chat/api/conversations', {
                    headers: {
                        'Accept': 'application/json'
                    }
                });

                if (res.status === 401) {
                    window.location.href = '/login';
                    return;
                }

                const json = await res.json();
                if (json.status === 'success' && Array.isArray(json.data)) {
                    this.chatRooms = json.data.map(item => {
                        const interlocutorName = item.interlocutor?.name || 'Mahasiswa Lungsur';
                        return {
                            id: item.id,
                            name: interlocutorName,
                            initials: this.getInitials(interlocutorName),
                            campus: item.interlocutor?.campus || '',
                            statusText: item.interlocutor?.campus ? `Mahasiswa · ${item.interlocutor.campus}` : 'Mahasiswa Malang',
                            time: this.formatTime(item.last_message_at),
                            last_message: item.latest_message ? item.latest_message.message_text : 'Percakapan baru dibuat',
                            unread_count: item.unread_count || 0,
                            is_online: true,
                            listing: item.listing || null,
                            raw: item
                        };
                    });

                    this.updateNavbarBadge();
                }
            } catch (err) {
                console.error('Gagal memuat percakapan:', err);
            } finally {
                if (!isSilent) this.isLoadingRooms = false;
            }
        },

        async selectRoom(room) {
            this.activeRoom = room;
            this.currentListing = room.listing || this.currentListing;
            this.errorMessage = null;
            this.isLoadingMessages = true;

            // Reset unread count lokal
            room.unread_count = 0;
            this.updateNavbarBadge();

            try {
                const res = await fetch(`/chat/api/conversations/${room.id}`, {
                    headers: {
                        'Accept': 'application/json'
                    }
                });

                const json = await res.json();
                if (json.status === 'success' && json.data) {
                    if (json.data.listing) {
                        this.currentListing = json.data.listing;
                    }
                    if (Array.isArray(json.data.messages)) {
                        this.messages = json.data.messages.map(m => ({
                            id: m.id,
                            sender: (m.sender && m.sender.id === this.currentUser.id) ? 'me' : 'other',
                            sender_name: m.sender?.name || '',
                            type: 'text',
                            text: m.message_text,
                            time: this.formatClock(m.created_at),
                            is_read: m.is_read
                        }));
                    }
                }
            } catch (err) {
                console.error('Gagal memuat pesan:', err);
            } finally {
                this.isLoadingMessages = false;
                this.scrollToBottom();
            }
        },

        async sendMessage() {
            if (!this.newMessage.trim() || !this.activeRoom || this.isSending) return;

            const text = this.newMessage.trim();
            this.newMessage = '';
            this.isSending = true;
            this.errorMessage = null;

            // Optimistic UI push
            const tempId = 'temp-' + Date.now();
            const tempMsg = {
                id: tempId,
                sender: 'me',
                sender_name: this.currentUser.name,
                type: 'text',
                text: text,
                time: this.formatClock(new Date().toISOString()),
                is_pending: true,
                is_read: false
            };
            this.messages.push(tempMsg);
            this.activeRoom.last_message = text;
            this.activeRoom.time = 'Baru saja';
            this.scrollToBottom();

            // Pindahkan activeRoom ke paling atas daftar
            const roomIndex = this.chatRooms.findIndex(r => r.id === this.activeRoom.id);
            if (roomIndex > 0) {
                const [movedRoom] = this.chatRooms.splice(roomIndex, 1);
                this.chatRooms.unshift(movedRoom);
            }

            try {
                const res = await fetch(`/chat/api/conversations/${this.activeRoom.id}/messages`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': this.csrfToken
                    },
                    body: JSON.stringify({
                        message_text: text
                    })
                });

                const json = await res.json();
                if (res.ok && json.status === 'success' && json.data) {
                    const idx = this.messages.findIndex(m => m.id === tempId);
                    if (idx !== -1) {
                        this.messages[idx].id = json.data.id;
                        this.messages[idx].is_pending = false;
                        this.messages[idx].time = this.formatClock(json.data.created_at);
                    }
                } else {
                    const idx = this.messages.findIndex(m => m.id === tempId);
                    if (idx !== -1) {
                        this.messages[idx].is_failed = true;
                        this.messages[idx].is_pending = false;
                    }
                    this.errorMessage = json.message || 'Gagal mengirim pesan.';
                }
            } catch (err) {
                console.error('Error saat kirim pesan:', err);
                const idx = this.messages.findIndex(m => m.id === tempId);
                if (idx !== -1) {
                    this.messages[idx].is_failed = true;
                    this.messages[idx].is_pending = false;
                }
                this.errorMessage = 'Terjadi kendala koneksi saat mengirim pesan.';
            } finally {
                this.isSending = false;
                this.scrollToBottom();
            }
        },

        sendQuickReply(text) {
            this.newMessage = text;
            this.sendMessage();
        },

        startPolling() {
            if (this.pollTimer) clearInterval(this.pollTimer);
            this.pollTimer = setInterval(async () => {
                if (document.hidden) return;

                if (this.activeRoom && !this.isLoadingMessages && !this.isSending) {
                    await this.pollMessages();
                }

                await this.loadConversations(true);
            }, 4000);
        },

        async pollMessages() {
            if (!this.activeRoom) return;
            try {
                const res = await fetch(`/chat/api/conversations/${this.activeRoom.id}`, {
                    headers: { 'Accept': 'application/json' }
                });
                const json = await res.json();
                if (json.status === 'success' && json.data && Array.isArray(json.data.messages)) {
                    const serverMessages = json.data.messages;
                    if (serverMessages.length !== this.messages.length) {
                        const wasAtBottom = this.isScrolledToBottom();
                        this.messages = serverMessages.map(m => ({
                            id: m.id,
                            sender: (m.sender && m.sender.id === this.currentUser.id) ? 'me' : 'other',
                            sender_name: m.sender?.name || '',
                            type: 'text',
                            text: m.message_text,
                            time: this.formatClock(m.created_at),
                            is_read: m.is_read
                        }));

                        if (wasAtBottom) {
                            this.scrollToBottom();
                        }
                    }
                }
            } catch (e) {
                // Polling senyap
            }
        },

        isScrolledToBottom() {
            const container = this.$refs.messageContainer;
            if (!container) return true;
            return container.scrollHeight - container.scrollTop - container.clientHeight < 120;
        },

        scrollToBottom() {
            this.$nextTick(() => {
                const container = this.$refs.messageContainer;
                if (container) {
                    container.scrollTop = container.scrollHeight;
                }
            });
        },

        updateNavbarBadge() {
            const totalUnread = this.chatRooms.reduce((sum, room) => sum + (room.unread_count || 0), 0);
            const badges = document.querySelectorAll('#navbar-unread-badge, .navbar-unread-badge-mobile');
            badges.forEach(badgeEl => {
                if (totalUnread > 0) {
                    badgeEl.innerText = totalUnread > 99 ? '99+' : totalUnread;
                    badgeEl.classList.remove('hidden', 'd-none');
                } else {
                    badgeEl.classList.add('hidden');
                }
            });
        }
    };
}
</script>
@endsection
