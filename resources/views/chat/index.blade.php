@extends('layouts.app')

@section('content')
<main class="flex-1 max-w-[1600px] w-full mx-auto p-3 sm:p-5 lg:p-6 flex flex-col" 
      x-data="chatApp(@json($selectedListing))" 
      x-init="initChat()">
    
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs flex-1 flex overflow-hidden min-h-[760px] max-h-[860px]">
        
        <!-- BEGIN: ConversationSidebar (Left Column) -->
        <aside class="w-full md:w-[350px] lg:w-[390px] border-r border-slate-200/80 flex flex-col bg-white flex-shrink-0">
            <!-- Sidebar Header & Search -->
            <div class="p-4 border-b border-slate-100 space-y-3">
                <div class="flex items-center justify-between">
                    <h1 class="text-xl font-bold text-slate-900 tracking-tight">Pesan</h1>
                    <span class="text-xs bg-slate-100 text-slate-600 px-2.5 py-0.5 rounded-full font-medium" x-text="chatRooms.length + ' Aktif'"></span>
                </div>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-slate-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path>
                        </svg>
                    </span>
                    <input type="text" x-model="searchQuery" class="w-full pl-9 pr-4 py-2 text-xs bg-slate-50 border border-slate-200/90 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition placeholder:text-slate-400" placeholder="Cari percakapan..."/>
                </div>
            </div>

            <!-- Conversation Item List -->
            <div class="flex-1 overflow-y-auto divide-y divide-slate-100">
                <template x-for="room in filteredRooms" :key="room.id">
                    <div @click="selectRoom(room)" 
                         class="flex items-center gap-3 p-3.5 cursor-pointer transition"
                         :class="activeRoom && activeRoom.id === room.id ? 'bg-blue-50/60 border-l-4 border-brand-600' : 'hover:bg-slate-50'">
                        <div class="relative flex-shrink-0">
                            <div class="w-12 h-12 rounded-full bg-slate-900 text-white font-bold flex items-center justify-center text-sm shadow-xs" x-text="room.initials"></div>
                            <span class="absolute bottom-0 right-0 w-3 h-3 border-2 border-white rounded-full" :class="room.is_online ? 'bg-emerald-500' : 'bg-slate-300'"></span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between mb-1">
                                <h2 class="text-sm font-bold truncate" :class="activeRoom && activeRoom.id === room.id ? 'text-slate-900' : 'text-slate-800'" x-text="room.name"></h2>
                                <span class="text-[11px] font-semibold" :class="activeRoom && activeRoom.id === room.id ? 'text-brand-600' : 'text-slate-400'" x-text="room.time"></span>
                            </div>
                            <p class="text-xs truncate" :class="room.unread_count > 0 ? 'text-slate-800 font-semibold' : 'text-slate-500'" x-text="room.last_message"></p>
                        </div>
                        <!-- Unread Counter Badge -->
                        <span x-show="room.unread_count > 0" class="w-5 h-5 rounded-full bg-brand-600 text-white text-[11px] font-bold flex items-center justify-center flex-shrink-0 shadow-xs" x-text="room.unread_count"></span>
                    </div>
                </template>
            </div>
        </aside>
        <!-- END: ConversationSidebar -->

        <!-- BEGIN: ActiveChatPanel (Right Column) -->
        <section class="flex-1 flex flex-col bg-white overflow-hidden relative">
            
            <!-- KONDISI 1: JIKA BELUM MEMILIH ROOM CHAT (Tampilan Sambutan Lungsur.in) -->
            <div x-show="!activeRoom" class="flex-1 w-full h-full flex items-center justify-center p-6 bg-gradient-to-br from-slate-50 via-blue-50/20 to-slate-50">
                <div class="max-w-md w-full p-8 bg-white/90 backdrop-blur-md rounded-3xl border border-slate-200/70 shadow-lg shadow-blue-500/5 text-center space-y-4">
                    
                    <!-- Logo Lungsur.in -->
                    <div class="w-20 h-20 rounded-3xl bg-blue-600 flex items-center justify-center shadow-lg shadow-blue-500/30 text-white mx-auto">
                        <svg class="w-10 h-10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 12a9 9 0 0 0-9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/>
                            <path d="M3 3v5h5"/>
                            <path d="M3 12a9 9 0 0 0 9 9 9.75 9.75 0 0 0 6.74-2.74L21 16"/>
                            <path d="M21 21v-5h-5"/>
                            <path d="M12 9l5 2.5-5 2.5-5-2.5 5-2.5z" fill="#FBBF24" stroke="#FBBF24" stroke-width="1.5"/>
                            <path d="M9.5 13v2.2c0 .8 1.1 1.5 2.5 1.5s2.5-.7 2.5-1.5V13" stroke="#FBBF24" stroke-width="1.5"/>
                        </svg>
                    </div>

                    <!-- Teks Sapaan Ramah -->
                    <div class="space-y-1.5">
                        <h2 class="text-xl font-extrabold text-slate-900 tracking-tight">
                            Halo, Teman Mahasiswa! 👋
                        </h2>
                        <p class="text-xs sm:text-sm text-slate-500 leading-relaxed">
                            Belum ada obrolan yang dipilih nih. Yuk, pilih salah satu percakapan di sebelah kiri untuk mulai negosiasi atau koordinasi barang kampusmu dengan asik!
                        </p>
                    </div>

                    <!-- Tips Kecil -->
                    <div class="pt-2">
                        <span class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full bg-blue-50 text-blue-700 text-xs font-semibold border border-blue-100">
                            💡 Tips: Pastikan cek detail barang sebelum COD ya!
                        </span>
                    </div>

                </div>
            </div>

            <!-- KONDISI 2: JIKA ROOM CHAT DIPILIH -->
            <div x-show="activeRoom" class="flex-1 flex flex-col h-full overflow-hidden" style="display: none;">
                
                <!-- Active Chat Header -->
                <div class="px-6 py-3.5 border-b border-slate-200/80 flex items-center justify-between bg-white z-10 flex-shrink-0">
                    <div class="flex items-center gap-3.5">
                        <div class="relative">
                            <div class="w-11 h-11 rounded-full bg-slate-900 text-white font-bold flex items-center justify-center text-sm" x-text="activeRoom?.initials"></div>
                            <span class="absolute bottom-0 right-0 w-3 h-3 bg-emerald-500 border-2 border-white rounded-full"></span>
                        </div>
                        <div>
                            <h2 class="text-sm font-bold text-slate-900 leading-tight" x-text="activeRoom?.name"></h2>
                            <p class="text-xs text-emerald-600 font-medium flex items-center gap-1.5 mt-0.5">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                <span x-text="activeRoom?.statusText"></span>
                            </p>
                        </div>
                    </div>
                    <button class="p-2 text-slate-400 hover:text-slate-700 rounded-full hover:bg-slate-100 transition">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z"></path>
                        </svg>
                    </button>
                </div>

                <!-- Pinned Related Product Card (Hanya muncul jika di-klik dari detail produk) -->
                <div class="mx-6 mt-3.5 px-4 py-2.5 bg-blue-50/70 border border-blue-100 rounded-xl flex items-center justify-between flex-shrink-0"
                     x-show="currentListing">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-lg bg-blue-100 text-brand-600 flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-xs font-bold text-slate-800" x-text="currentListing?.title"></h3>
                            <p class="text-xs font-bold text-brand-600" x-text="'Rp ' + Number(currentListing?.price || 0).toLocaleString('id-ID')"></p>
                        </div>
                    </div>
                    <a :href="'/katalog/' + currentListing?.id" class="text-xs font-bold text-brand-600 hover:text-brand-700 hover:underline">Lihat Iklan</a>
                </div>

                <!-- Chat History Scroll Area -->
                <div class="flex-1 overflow-y-auto p-6 space-y-4" x-ref="messageContainer">
                    <div class="flex items-center justify-center my-2">
                        <span class="px-3.5 py-1 bg-slate-100 text-slate-500 rounded-full text-[11px] font-semibold">Hari ini</span>
                    </div>

                    <template x-for="msg in messages" :key="msg.id">
                        <div class="flex flex-col" :class="msg.sender === 'me' ? 'items-end ml-auto max-w-md' : 'items-start max-w-md'">
                            
                            <!-- Text Message -->
                            <template x-if="msg.type === 'text'">
                                <div class="px-4 py-3 shadow-xs rounded-2xl" :class="msg.sender === 'me' ? 'bg-brand-600 text-white rounded-tr-sm' : 'bg-white border border-slate-200/90 text-slate-800 rounded-tl-sm'">
                                    <p class="text-xs sm:text-sm leading-relaxed" x-text="msg.text"></p>
                                    <div class="flex items-center justify-end gap-1 mt-1">
                                        <span class="text-[10px]" :class="msg.sender === 'me' ? 'text-blue-100' : 'text-slate-400'" x-text="msg.time"></span>
                                        <template x-if="msg.sender === 'me'">
                                            <svg class="w-3.5 h-3.5 text-blue-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path d="M5 13l4 4L19 7m-14 4l4 4L19 7" stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"></path>
                                            </svg>
                                        </template>
                                    </div>
                                </div>
                            </template>

                            <!-- Image Message -->
                            <template x-if="msg.type === 'image'">
                                <div class="bg-brand-600 p-2 rounded-2xl rounded-tr-sm shadow-sm space-y-2">
                                    <div class="w-full h-44 bg-blue-100/90 rounded-xl flex items-center justify-center border border-white/20 relative overflow-hidden group">
                                        <svg class="w-12 h-12 text-brand-600/70" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"></path>
                                        </svg>
                                        <div class="absolute bottom-2 left-2 bg-white/95 backdrop-blur-xs text-emerald-700 font-bold text-[11px] px-2.5 py-1 rounded-full flex items-center gap-1.5 shadow-xs">
                                            <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"></path>
                                            </svg>
                                            <span x-text="msg.compressionInfo"></span>
                                        </div>
                                    </div>
                                    <div class="flex items-center justify-end gap-1 px-1">
                                        <span class="text-[10px] text-blue-100 font-medium" x-text="msg.time"></span>
                                        <svg class="w-3.5 h-3.5 text-blue-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path d="M5 13l4 4L19 7m-14 4l4 4L19 7" stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"></path>
                                        </svg>
                                    </div>
                                </div>
                            </template>

                        </div>
                    </template>
                </div>

                <!-- Floating Attachment Menu & Quick Emoji Bar -->
                <div class="px-6 pb-2 relative flex-shrink-0">
                    <div x-show="showAttachmentMenu" @click.away="showAttachmentMenu = false" x-transition class="absolute bottom-full left-6 mb-3 bg-white rounded-2xl shadow-xl border border-slate-200 p-2.5 w-64 z-20 space-y-1">
                        <button @click="showAttachmentMenu = false" class="w-full flex items-center gap-3 p-2 rounded-xl hover:bg-slate-50 transition text-left">
                            <div class="w-9 h-9 rounded-lg bg-blue-50 text-brand-600 flex items-center justify-center flex-shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs font-bold text-slate-800">Foto & Gambar</p>
                                <p class="text-[10px] text-emerald-600 font-medium">Otomatis dikompresi</p>
                            </div>
                        </button>
                    </div>

                    <div class="flex items-center gap-2 overflow-x-auto pb-2 scrollbar-none">
                        <div class="flex items-center gap-1.5 pr-2 border-r border-slate-200">
                            <button @click="insertQuickText('😎')" class="p-1 hover:bg-slate-100 rounded-lg text-base transition">😎</button>
                            <button @click="insertQuickText('🙏')" class="p-1 hover:bg-slate-100 rounded-lg text-base transition">🙏</button>
                            <button @click="insertQuickText('👍')" class="p-1 hover:bg-slate-100 rounded-lg text-base transition">👍</button>
                            <button @click="insertQuickText('🍚')" class="p-1 hover:bg-slate-100 rounded-lg text-base transition">🍚</button>
                        </div>
                        <div class="flex items-center gap-2 flex-nowrap">
                            <button @click="sendQuickReply('Siap!')" class="px-3 py-1 bg-amber-100/70 hover:bg-amber-200/70 text-slate-800 text-xs font-semibold rounded-xl border border-amber-200/80 transition flex-shrink-0">😊 Siap!</button>
                            <button @click="sendQuickReply('Oke fix')" class="px-3 py-1 bg-emerald-100/70 hover:bg-emerald-200/70 text-slate-800 text-xs font-semibold rounded-xl border border-emerald-200/80 transition flex-shrink-0">😃 Oke fix</button>
                            <button @click="sendQuickReply('COD yuk')" class="px-3 py-1 bg-indigo-100/70 hover:bg-indigo-200/70 text-slate-800 text-xs font-semibold rounded-xl border border-indigo-200/80 transition flex-shrink-0">🤝 COD yuk</button>
                        </div>
                    </div>
                </div>

                <!-- ChatInputForm -->
                <div class="p-4 pt-1 bg-white border-t border-slate-100 flex-shrink-0">
                    <form @submit.prevent="sendMessage" class="flex items-center gap-2">
                        <button type="button" @click="showAttachmentMenu = !showAttachmentMenu" class="p-2.5 text-slate-500 hover:text-brand-600 hover:bg-slate-100 rounded-xl transition" title="Lampirkan File">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path>
                            </svg>
                        </button>
                        <div class="flex-1">
                            <input type="text" x-model="newMessage" class="w-full px-4 py-2.5 text-xs sm:text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-500/25 transition placeholder:text-slate-400" placeholder="Tulis pesan..."/>
                        </div>
                        <button type="submit" class="w-10 h-10 bg-brand-600 hover:bg-brand-700 text-white rounded-xl flex items-center justify-center shadow-md shadow-brand-500/20 transition flex-shrink-0" title="Kirim">
                            <svg class="w-4 h-4 translate-x-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path d="M5 12h14M12 5l7 7-7 7" stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"></path>
                            </svg>
                        </button>
                    </form>
                </div>

            </div>

        </section>
        <!-- END: ActiveChatPanel -->

    </div>
</main>

<!-- Alpine.js Interactivity Script -->
<script>
function chatApp(initialListing = null) {
    return {
        searchQuery: '',
        newMessage: '',
        showAttachmentMenu: false,
        activeRoom: null,
        currentListing: initialListing, 
        
        chatRooms: [
            { id: 1, name: 'Rina Aulia', initials: 'RA', time: '09:20', last_message: 'Oke fix, COD di kantin FILKOM ...', unread_count: 2, is_online: true, statusText: 'Online · Universitas Brawijaya' },
            { id: 2, name: 'Dimas Pratama', initials: 'DP', time: '08:41', last_message: 'Meja belajarnya masih ada kak?', unread_count: 1, is_online: true, statusText: 'Online · Polinema' },
            { id: 3, name: 'Salsa N.', initials: 'SN', time: 'Kemarin', last_message: 'Terima kasih, barang sudah sampai', unread_count: 0, is_online: false, statusText: 'Offline' },
            { id: 4, name: 'Fajar Hidayat', initials: 'FH', time: 'Kemarin', last_message: 'Boleh nego jadi 120rb?', unread_count: 0, is_online: false, statusText: 'Offline' },
            { id: 5, name: 'Maya K.', initials: 'MK', time: 'Sen', last_message: 'Kirim foto bagian belakang ya', unread_count: 0, is_online: false, statusText: 'Offline' }
        ],
        messages: [
            { id: 1, sender: 'other', type: 'text', text: 'Halo kak, rice cooker-nya masih ada?', time: '09:12' },
            { id: 2, sender: 'me', type: 'text', text: 'Masih ada kak! Ini foto kondisi aslinya ya', time: '09:13' },
            { id: 3, sender: 'me', type: 'image', compressionInfo: 'Dikompresi 3,8 MB → 420 KB', time: '09:14' }
        ],

        get filteredRooms() {
            if (!this.searchQuery) return this.chatRooms;
            return this.chatRooms.filter(room => room.name.toLowerCase().includes(this.searchQuery.toLowerCase()));
        },

        initChat() {
            this.updateNavbarBadge();
            if (this.currentListing) {
                console.log('Chat dibuka dari detail produk:', this.currentListing.title);
            }
        },

        selectRoom(room) {
            this.activeRoom = room;
            room.unread_count = 0;
            this.updateNavbarBadge();
            this.scrollToBottom();
        },

        sendMessage() {
            if (!this.newMessage.trim()) return;

            this.messages.push({
                id: Date.now(),
                sender: 'me',
                type: 'text',
                text: this.newMessage,
                time: new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })
            });

            if (this.activeRoom) {
                this.activeRoom.last_message = this.newMessage;
                this.activeRoom.time = 'Baru saja';
            }

            this.newMessage = '';
            this.scrollToBottom();
        },

        sendQuickReply(text) {
            this.newMessage = text;
            this.sendMessage();
        },

        insertQuickText(emoji) {
            this.newMessage += emoji;
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
            const totalUnread = this.chatRooms.reduce((sum, room) => sum + room.unread_count, 0);
            const badgeEl = document.getElementById('navbar-unread-badge');
            
            if (badgeEl) {
                if (totalUnread > 0) {
                    badgeEl.innerText = totalUnread;
                    badgeEl.classList.remove('hidden', 'd-none');
                } else {
                    badgeEl.classList.add('hidden');
                }
            }
        }
    }
}
</script>
@endsection