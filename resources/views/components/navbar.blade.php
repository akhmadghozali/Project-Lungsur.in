<nav x-data="{ mobileMenuOpen: false, userDropdownOpen: false, notifOpen: false }"
     @keydown.escape.window="mobileMenuOpen = false; userDropdownOpen = false; notifOpen = false"
     class="bg-white border-b border-slate-200/80 sticky top-0 z-40 shadow-2xs">
    <div class="w-full max-w-7xl 2xl:max-w-screen-2xl mx-auto px-3 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16 sm:h-20 items-center gap-2 lg:gap-4">

            <div class="flex items-center gap-3 lg:gap-5 xl:gap-6 min-w-0">
                <a href="{{ url('/') }}" class="min-h-[44px] flex items-center gap-2 sm:gap-2.5 group shrink-0 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600 rounded-xl">
                    <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-2xl bg-blue-600 flex items-center justify-center shadow-sm shadow-blue-600/20 relative overflow-hidden group-hover:bg-blue-700 transition shrink-0">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M21 12a9 9 0 0 0-9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/>
                            <path d="M3 3v5h5"/>
                            <path d="M3 12a9 9 0 0 0 9 9 9.75 9.75 0 0 0 6.74-2.74L21 16"/>
                            <path d="M21 21v-5h-5"/>
                            <path d="M12 9l5 2.5-5 2.5-5-2.5 5-2.5z" fill="#FBBF24" stroke="#FBBF24" stroke-width="1.5"/>
                            <path d="M9.5 13v2.2c0 .8 1.1 1.5 2.5 1.5s2.5-.7 2.5-1.5V13" stroke="#FBBF24" stroke-width="1.5"/>
                        </svg>
                    </div>
                    <span class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">
                        Lungsur<span class="text-blue-600">.in</span>
                    </span>
                </a>

                <div class="hidden xl:flex items-center gap-0.5 2xl:gap-1 font-medium text-xs 2xl:text-sm text-slate-700 whitespace-nowrap">
                    <a href="{{ url('/') }}"
                       class="flex items-center gap-1.5 px-2.5 py-1.5 2xl:px-3 2xl:py-2 rounded-xl {{ request()->routeIs('home') ? 'text-blue-700 font-bold bg-blue-50' : 'text-slate-700 hover:text-blue-700 hover:bg-slate-50' }} transition focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600">
                        <svg class="w-4 h-4 shrink-0 {{ request()->routeIs('home') ? 'text-blue-700' : 'text-slate-500' }}" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                            <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"/>
                        </svg>
                        <span>Beranda</span>
                    </a>

                    <a href="{{ Route::has('listings.index') ? route('listings.index') : url('/katalog') }}"
                       class="flex items-center gap-1.5 px-2.5 py-1.5 2xl:px-3 2xl:py-2 rounded-xl {{ request()->is('katalog*') && !request()->is('katalog/pasang') ? 'text-blue-700 font-bold bg-blue-50' : 'text-slate-700 hover:text-blue-700 hover:bg-slate-50' }} transition focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600">
                        <svg class="w-4 h-4 shrink-0 {{ request()->is('katalog*') && !request()->is('katalog/pasang') ? 'text-blue-700' : 'text-slate-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                        </svg>
                        <span>Katalog</span>
                    </a>

                    @auth
                        @if(auth()->user()->isSeller())
                            <a href="{{ route('dashboard', ['tab' => 'dagangan']) }}"
                               class="flex items-center gap-1.5 px-2.5 py-1.5 2xl:px-3 2xl:py-2 rounded-xl {{ request()->routeIs('dashboard*') && request('tab', 'dagangan') === 'dagangan' ? 'text-emerald-800 font-bold bg-emerald-50' : 'text-slate-700 hover:text-emerald-800 hover:bg-slate-50' }} transition focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600">
                                <svg class="w-4 h-4 shrink-0 text-emerald-700" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                                </svg>
                                <span>Dagangan<span class="hidden 2xl:inline"> Saya</span></span>
                            </a>

                            <a href="{{ route('dashboard', ['tab' => 'lelang']) }}"
                               class="flex items-center gap-1.5 px-2.5 py-1.5 2xl:px-3 2xl:py-2 rounded-xl {{ request()->routeIs('dashboard*') && request('tab') === 'lelang' ? 'text-emerald-800 font-bold bg-emerald-50' : 'text-slate-700 hover:text-emerald-800 hover:bg-slate-50' }} transition focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600">
                                <svg class="w-4 h-4 shrink-0 text-emerald-700" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                </svg>
                                <span>Lelang<span class="hidden 2xl:inline"> Saya</span></span>
                            </a>

                            <a href="{{ route('dashboard', ['tab' => 'peluang']) }}"
                               class="flex items-center gap-1.5 px-2.5 py-1.5 2xl:px-3 2xl:py-2 rounded-xl {{ request()->routeIs('dashboard*') && request('tab') === 'peluang' ? 'text-emerald-800 font-bold bg-emerald-50' : 'text-slate-700 hover:text-emerald-800 hover:bg-slate-50' }} transition focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600">
                                <svg class="w-4 h-4 shrink-0 text-emerald-700" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                                <span><span class="hidden 2xl:inline">Peluang </span>Wanted Ads</span>
                            </a>
                        @else
                            <a href="{{ Route::has('auctions.index') ? route('auctions.index') : url('/lelang') }}"
                               class="flex items-center gap-1.5 px-2.5 py-1.5 2xl:px-3 2xl:py-2 rounded-xl {{ request()->routeIs('auctions.*') || request()->is('lelang*') ? 'text-blue-700 font-bold bg-blue-50' : 'text-slate-700 hover:text-blue-700 hover:bg-slate-50' }} transition focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600">
                                <svg class="w-4 h-4 shrink-0 {{ request()->routeIs('auctions.*') || request()->is('lelang*') ? 'text-blue-700' : 'text-slate-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                </svg>
                                <span>Lelang</span>
                            </a>

                            <a href="{{ route('wanted-ads.index') }}"
                               class="flex items-center gap-1.5 px-2.5 py-1.5 2xl:px-3 2xl:py-2 rounded-xl {{ request()->routeIs('wanted-ads.*') || request()->is('cari-barang*') ? 'text-blue-700 font-bold bg-blue-50' : 'text-slate-700 hover:text-blue-700 hover:bg-slate-50' }} transition focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600">
                                <svg class="w-4 h-4 shrink-0 {{ request()->routeIs('wanted-ads.*') || request()->is('cari-barang*') ? 'text-blue-700' : 'text-slate-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <span>Wanted Ads</span>
                            </a>
                        @endif
                    @else
                        <a href="{{ Route::has('auctions.index') ? route('auctions.index') : url('/lelang') }}"
                           class="flex items-center gap-1.5 px-2.5 py-1.5 2xl:px-3 2xl:py-2 rounded-xl {{ request()->routeIs('auctions.*') || request()->is('lelang*') ? 'text-blue-700 font-bold bg-blue-50' : 'text-slate-700 hover:text-blue-700 hover:bg-slate-50' }} transition focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600">
                            <svg class="w-4 h-4 shrink-0 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                            </svg>
                            <span>Lelang</span>
                        </a>
                        <a href="{{ route('wanted-ads.index') }}"
                           class="flex items-center gap-1.5 px-2.5 py-1.5 2xl:px-3 2xl:py-2 rounded-xl {{ request()->routeIs('wanted-ads.*') || request()->is('cari-barang*') ? 'text-blue-700 font-bold bg-blue-50' : 'text-slate-700 hover:text-blue-700 hover:bg-slate-50' }} transition focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600">
                            <svg class="w-4 h-4 shrink-0 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span>Kebutuhan</span>
                        </a>
                    @endauth
                </div>
            </div>

            <div class="flex items-center gap-1.5 sm:gap-2.5 lg:gap-3 shrink-0">
                <form action="{{ Route::has('listings.index') ? route('listings.index') : url('/katalog') }}" method="GET"
                      class="hidden md:flex relative w-28 sm:w-36 lg:w-44 xl:w-48 2xl:w-60 focus-within:w-44 sm:focus-within:w-52 lg:focus-within:w-60 xl:focus-within:w-64 2xl:focus-within:w-72 transition-all duration-300">
                    <input type="text"
                           name="q"
                           value="{{ request('q') }}"
                           placeholder="Cari barang..."
                           class="w-full pl-8 pr-3 py-1.5 text-xs rounded-full border border-slate-300 bg-slate-50 text-slate-800 placeholder-slate-500 focus:bg-white focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-100 transition shadow-2xs">
                    <button type="submit" class="absolute left-2.5 top-2 text-slate-500 hover:text-blue-700 cursor-pointer" aria-label="Cari">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </button>
                </form>

                @auth
                    @php
                        $unreadChatCount = auth()->user()->unreadMessagesCount();
                    @endphp
                    <form method="POST" action="{{ route('user.switch_mode') }}" class="hidden sm:inline-flex items-center shrink-0">
                        @csrf
                        @if(auth()->user()->isSeller())
                            <input type="hidden" name="mode" value="buyer">
                            <button type="submit"
                                    class="min-h-[38px] inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-extrabold bg-emerald-50 text-emerald-800 hover:bg-emerald-100 border border-emerald-300 transition shadow-2xs cursor-pointer group whitespace-nowrap focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-emerald-700"
                                    title="Klik untuk beralih ke Mode Pembeli">
                                <span class="w-2 h-2 rounded-full bg-emerald-600 shrink-0"></span>
                                <span>Penjual</span>
                                <svg class="w-3.5 h-3.5 text-emerald-700 group-hover:rotate-180 transition-transform shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                                </svg>
                            </button>
                        @else
                            <input type="hidden" name="mode" value="seller">
                            <button type="submit"
                                    class="min-h-[38px] inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-extrabold bg-blue-50 text-blue-700 hover:bg-blue-100 border border-blue-200 transition shadow-2xs cursor-pointer group whitespace-nowrap focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600"
                                    title="Klik untuk beralih ke Mode Penjual">
                                <span class="w-2 h-2 rounded-full bg-blue-600 shrink-0"></span>
                                <span>Pembeli</span>
                                <svg class="w-3.5 h-3.5 text-blue-700 group-hover:rotate-180 transition-transform shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                                </svg>
                            </button>
                        @endif
                    </form>

                    <a href="{{ Route::has('chat.index') ? route('chat.index') : url('/chat') }}"
                       class="relative min-w-[44px] min-h-[44px] sm:min-w-0 sm:min-h-0 p-2.5 sm:p-2 rounded-full flex items-center justify-center transition shrink-0 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600 {{ request()->routeIs('chat.*') || request()->is('chat*') ? 'text-blue-700 bg-blue-50' : 'text-slate-700 hover:text-blue-700 hover:bg-slate-100' }}"
                       title="Obrolan Pesan"
                       aria-label="Obrolan Pesan">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                        </svg>
                        <span id="navbar-unread-badge"
                              class="absolute top-0.5 right-0.5 sm:-top-0.5 sm:-right-0.5 min-w-[18px] h-[18px] px-1 bg-rose-600 text-white text-[10px] font-bold rounded-full ring-2 ring-white flex items-center justify-center {{ $unreadChatCount > 0 ? '' : 'hidden' }}">
                              {{ $unreadChatCount > 99 ? '99+' : $unreadChatCount }}
                        </span>
                    </a>

                    @if(auth()->user()->isSeller())
                        <a href="{{ route('katalog.pasang') }}"
                           class="hidden lg:inline-flex items-center gap-1 px-3.5 py-2 rounded-full text-xs font-extrabold bg-blue-600 hover:bg-blue-700 text-white shadow-xs transition whitespace-nowrap shrink-0 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600">
                            <span>+ Iklan<span class="hidden 2xl:inline"> Baru</span></span>
                        </a>
                    @endif

                    <div class="relative shrink-0" @click.outside="notifOpen = false">
                        <button @click="notifOpen = !notifOpen; userDropdownOpen = false"
                                type="button"
                                class="relative min-w-[44px] min-h-[44px] sm:min-w-0 sm:min-h-0 p-2.5 sm:p-2 text-slate-700 hover:text-blue-700 hover:bg-slate-100 rounded-full flex items-center justify-center transition shrink-0 cursor-pointer focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600"
                                :aria-expanded="notifOpen.toString()"
                                aria-label="Notifikasi">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                            </svg>
                            <span id="navbar-notif-dot"
                                  class="absolute top-1.5 right-1.5 w-2 h-2 rounded-full bg-rose-600 ring-2 ring-white {{ $unreadChatCount > 0 ? '' : 'hidden' }}"></span>
                        </button>

                        <div x-show="notifOpen"
                             x-cloak
                             x-transition:enter="transition ease-out duration-150"
                             x-transition:enter-start="opacity-0 scale-95"
                             x-transition:enter-end="opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-100"
                             x-transition:leave-start="opacity-100 scale-100"
                             x-transition:leave-end="opacity-0 scale-95"
                             class="absolute right-0 mt-2 w-72 sm:w-80 bg-white border border-slate-200 rounded-2xl shadow-xl py-2.5 text-xs z-50">
                            <div class="px-4 pb-2 border-b border-slate-100 flex items-center justify-between">
                                <span class="font-bold text-slate-900 text-sm">Notifikasi Aktivitas</span>
                                <span class="px-2 py-0.5 rounded-md font-bold {{ auth()->user()->isSeller() ? 'bg-emerald-50 text-emerald-800' : 'bg-blue-50 text-blue-700' }}">
                                    {{ auth()->user()->isSeller() ? 'Penjual' : 'Pembeli' }}
                                </span>
                            </div>

                            <div class="divide-y divide-slate-100 max-h-64 overflow-y-auto">
                                @if($unreadChatCount > 0)
                                    <a href="{{ route('chat.index') }}" class="flex items-start gap-3 px-4 py-3 hover:bg-blue-50/60 transition">
                                        <span class="w-8 h-8 rounded-xl bg-blue-600 text-white font-bold flex items-center justify-center shrink-0">
                                            {{ $unreadChatCount > 99 ? '99+' : $unreadChatCount }}
                                        </span>
                                        <div class="min-w-0 flex-1">
                                            <p class="font-bold text-slate-900">Pesan Obrolan Belum Dibaca</p>
                                            <p class="text-slate-600 mt-0.5 leading-snug">Anda memiliki {{ $unreadChatCount }} pesan baru di ruang negosiasi.</p>
                                        </div>
                                    </a>
                                @else
                                    <div class="px-4 py-5 text-center space-y-1">
                                        <p class="font-bold text-slate-800">Belum Ada Pesan Baru</p>
                                        <p class="text-slate-600 leading-relaxed">Seluruh percakapan dan aktivitas lelang Anda dapat dipantau melalui dasbor.</p>
                                    </div>
                                @endif
                            </div>

                            <div class="px-3 pt-2 border-t border-slate-100 flex items-center justify-between gap-2">
                                <a href="{{ route('chat.index') }}" class="min-h-[38px] flex-1 px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 font-bold text-center flex items-center justify-center transition">
                                    Buka Ruang Chat
                                </a>
                                <a href="{{ route('dashboard.history') }}" class="min-h-[38px] flex-1 px-3 py-1.5 rounded-xl bg-blue-50 hover:bg-blue-100 text-blue-700 font-bold text-center flex items-center justify-center transition">
                                    Riwayat Transaksi
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="relative shrink-0" @click.outside="userDropdownOpen = false">
                        <button @click="userDropdownOpen = !userDropdownOpen; notifOpen = false"
                                type="button"
                                class="min-h-[44px] sm:min-h-0 flex items-center gap-1 p-1.5 sm:p-1 rounded-full hover:bg-slate-100 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600 transition cursor-pointer"
                                :aria-expanded="userDropdownOpen.toString()"
                                aria-label="Menu profil pengguna">
                            <div class="w-8 h-8 sm:w-9 sm:h-9 rounded-full bg-slate-900 text-white font-bold flex items-center justify-center text-xs shadow-2xs shrink-0">
                                {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 2)) }}
                            </div>
                            <svg class="w-3.5 h-3.5 text-slate-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>

                        <div x-show="userDropdownOpen"
                             x-cloak
                             x-transition:enter="transition ease-out duration-150"
                             x-transition:enter-start="opacity-0 scale-95"
                             x-transition:enter-end="opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-100"
                             x-transition:leave-start="opacity-100 scale-100"
                             x-transition:leave-end="opacity-0 scale-95"
                             class="absolute right-0 mt-2 w-60 bg-white border border-slate-200 rounded-2xl shadow-xl py-2 text-sm z-50">
                            <div class="px-4 py-2.5 border-b border-slate-100">
                                <p class="font-bold text-slate-900 truncate">{{ auth()->user()->name }}</p>
                                <p class="text-xs text-slate-600 truncate">{{ auth()->user()->email }}</p>
                                <div class="mt-1.5">
                                    <span class="inline-block text-[10px] font-extrabold uppercase px-2 py-0.5 rounded-md {{ auth()->user()->isSeller() ? 'bg-emerald-50 text-emerald-800 border border-emerald-200' : 'bg-blue-50 text-blue-700 border border-blue-200' }}">
                                        {{ auth()->user()->isSeller() ? 'Mode Penjual' : 'Mode Pembeli' }}
                                    </span>
                                </div>
                            </div>

                            <div class="px-3 py-2 border-b border-slate-100">
                                <form method="POST" action="{{ route('user.switch_mode') }}">
                                    @csrf
                                    <input type="hidden" name="mode" value="{{ auth()->user()->isSeller() ? 'buyer' : 'seller' }}">
                                    <button type="submit" class="min-h-[40px] w-full px-2.5 py-1.5 rounded-xl bg-slate-50 hover:bg-blue-50 flex items-center justify-between text-xs font-bold text-slate-800 hover:text-blue-700 transition cursor-pointer">
                                        <span>Beralih ke {{ auth()->user()->isSeller() ? 'Mode Pembeli' : 'Mode Penjual' }}</span>
                                        <svg class="w-3.5 h-3.5 text-blue-700 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                                        </svg>
                                    </button>
                                </form>
                            </div>

                            <a href="{{ Route::has('dashboard') ? route('dashboard') : url('/dashboard') }}" class="min-h-[40px] flex items-center px-4 py-2 text-slate-700 hover:bg-slate-50 font-medium">Dashboard Saya</a>
                            <a href="{{ route('dashboard.history') }}" class="min-h-[40px] flex items-center px-4 py-2 text-slate-700 hover:bg-slate-50 font-medium">
                                Riwayat Transaksi
                            </a>
                            @if(auth()->user()->isSeller())
                                <a href="{{ route('katalog.pasang') }}" class="min-h-[40px] flex items-center px-4 py-2 text-blue-700 hover:bg-blue-50 font-bold">+ Pasang Iklan Baru</a>
                            @endif
                            @if((auth()->user()->role ?? '') === 'admin')
                                <a href="{{ route('admin.verifications.index') }}" class="min-h-[40px] flex items-center px-4 py-2 text-blue-700 font-bold hover:bg-blue-50">Panel Admin</a>
                            @endif
                            <form method="POST" action="{{ Route::has('logout') ? route('logout') : url('/logout') }}" class="border-t border-slate-100 mt-1 pt-1">
                                @csrf
                                <button type="submit" class="min-h-[40px] w-full text-left px-4 py-2 text-rose-700 hover:bg-rose-50 font-semibold cursor-pointer">Keluar</button>
                            </form>
                        </div>
                    </div>
                @else
                    <div class="flex items-center gap-1.5 sm:gap-2 shrink-0">
                        <a href="{{ Route::has('login') ? route('login') : url('/login') }}"
                           class="min-h-[44px] sm:min-h-0 inline-flex items-center text-xs sm:text-sm font-bold text-slate-700 hover:text-blue-700 px-3 py-2 sm:py-1.5 rounded-xl transition focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600">
                            Masuk
                        </a>
                        <a href="{{ Route::has('register') ? route('register') : url('/register') }}"
                           class="min-h-[44px] sm:min-h-0 inline-flex items-center bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold px-3.5 py-2 rounded-full shadow-xs transition whitespace-nowrap focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600">
                            Daftar Akun
                        </a>
                    </div>
                @endauth

                <button @click="mobileMenuOpen = !mobileMenuOpen"
                        type="button"
                        class="xl:hidden min-w-[44px] min-h-[44px] px-2.5 py-2 rounded-xl border border-slate-200 bg-slate-50/80 text-slate-800 hover:bg-slate-100 flex items-center justify-center gap-1.5 shrink-0 cursor-pointer transition focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600"
                        :aria-expanded="mobileMenuOpen.toString()"
                        aria-label="Menu navigasi">
                    <svg x-show="!mobileMenuOpen" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                    <svg x-show="mobileMenuOpen" x-cloak class="w-5 h-5 text-blue-700" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                    <span class="text-xs font-bold hidden sm:inline xl:hidden">Menu</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Bar Akses Cepat Khusus Mobile & Tablet Kecil (md:hidden) -->
    <div class="md:hidden border-t border-slate-100 bg-slate-50/90 px-3 py-1.5 flex items-center justify-between gap-2 overflow-x-auto no-scrollbar">
        <div class="flex items-center gap-1 shrink-0">
            <a href="{{ url('/') }}"
               class="min-h-[38px] px-3 py-1.5 rounded-xl text-xs font-bold inline-flex items-center transition {{ request()->routeIs('home') ? 'bg-blue-600 text-white shadow-2xs' : 'text-slate-700 hover:bg-white' }}">
                Beranda
            </a>
            <a href="{{ Route::has('listings.index') ? route('listings.index') : url('/katalog') }}"
               class="min-h-[38px] px-3 py-1.5 rounded-xl text-xs font-bold inline-flex items-center transition {{ request()->is('katalog*') && !request()->is('katalog/pasang') ? 'bg-blue-600 text-white shadow-2xs' : 'text-slate-700 hover:bg-white' }}">
                Katalog
            </a>
            <a href="{{ Route::has('auctions.index') ? route('auctions.index') : url('/lelang') }}"
               class="min-h-[38px] px-3 py-1.5 rounded-xl text-xs font-bold inline-flex items-center transition {{ request()->routeIs('auctions.*') || request()->is('lelang*') ? 'bg-blue-600 text-white shadow-2xs' : 'text-slate-700 hover:bg-white' }}">
                Lelang
            </a>
            @auth
                @if(auth()->user()->isSeller())
                    <a href="{{ route('dashboard', ['tab' => 'dagangan']) }}"
                       class="min-h-[38px] px-3 py-1.5 rounded-xl text-xs font-bold inline-flex items-center transition {{ request()->routeIs('dashboard*') ? 'bg-emerald-700 text-white shadow-2xs' : 'text-emerald-800 bg-emerald-50 border border-emerald-200' }}">
                        Dagangan
                    </a>
                @else
                    <a href="{{ route('wanted-ads.index') }}"
                       class="min-h-[38px] px-3 py-1.5 rounded-xl text-xs font-bold inline-flex items-center transition {{ request()->routeIs('wanted-ads.*') || request()->is('cari-barang*') ? 'bg-blue-600 text-white shadow-2xs' : 'text-slate-700 hover:bg-white' }}">
                        Wanted Ads
                    </a>
                @endif
            @else
                <a href="{{ route('wanted-ads.index') }}"
                   class="min-h-[38px] px-3 py-1.5 rounded-xl text-xs font-bold inline-flex items-center transition {{ request()->routeIs('wanted-ads.*') || request()->is('cari-barang*') ? 'bg-blue-600 text-white shadow-2xs' : 'text-slate-700 hover:bg-white' }}">
                    Wanted Ads
                </a>
            @endauth
        </div>

        @auth
            <form method="POST" action="{{ route('user.switch_mode') }}" class="shrink-0 sm:hidden">
                @csrf
                <input type="hidden" name="mode" value="{{ auth()->user()->isSeller() ? 'buyer' : 'seller' }}">
                <button type="submit"
                        class="min-h-[38px] px-2.5 py-1.5 rounded-xl text-[11px] font-extrabold inline-flex items-center gap-1 border transition cursor-pointer {{ auth()->user()->isSeller() ? 'bg-emerald-50 text-emerald-800 border-emerald-300' : 'bg-blue-50 text-blue-700 border-blue-200' }}">
                    <span class="w-1.5 h-1.5 rounded-full {{ auth()->user()->isSeller() ? 'bg-emerald-600' : 'bg-blue-600' }}"></span>
                    <span>{{ auth()->user()->isSeller() ? 'Penjual' : 'Pembeli' }}</span>
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                    </svg>
                </button>
            </form>
        @endauth
    </div>

    <!-- Mobile & Tablet Navigation Drawer -->
    <div x-show="mobileMenuOpen"
         x-cloak
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 -translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 -translate-y-2"
         @click.outside="mobileMenuOpen = false"
         class="xl:hidden border-t border-slate-200 bg-white px-4 pt-4 pb-6 space-y-4 shadow-xl max-h-[calc(100dvh-4.5rem)] overflow-y-auto">

        <form action="{{ Route::has('listings.index') ? route('listings.index') : url('/katalog') }}" method="GET" class="relative">
            <input type="text"
                   name="q"
                   value="{{ request('q') }}"
                   placeholder="Cari barang kos, buku, atau elektronik..."
                   class="w-full min-h-[44px] pl-10 pr-20 py-2.5 text-base sm:text-sm rounded-2xl border border-slate-300 bg-slate-50 text-slate-900 placeholder-slate-500 focus:bg-white focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-100 transition">
            <svg class="w-4 h-4 text-slate-500 absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <button type="submit" class="min-h-[36px] px-3.5 py-1.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold absolute right-1.5 top-1/2 -translate-y-1/2 transition cursor-pointer">
                Cari
            </button>
        </form>

        @auth
            <div class="p-3.5 rounded-2xl border flex items-center justify-between gap-3 {{ auth()->user()->isSeller() ? 'bg-emerald-50/70 border-emerald-200' : 'bg-blue-50/70 border-blue-200' }}">
                <div class="min-w-0">
                    <span class="text-[11px] font-semibold text-slate-600 block">Peran Aktif Saat Ini</span>
                    <strong class="text-sm font-black flex items-center gap-1.5 {{ auth()->user()->isSeller() ? 'text-emerald-800' : 'text-blue-700' }}">
                        <span class="w-2 h-2 rounded-full {{ auth()->user()->isSeller() ? 'bg-emerald-600' : 'bg-blue-600' }}"></span>
                        <span>{{ auth()->user()->isSeller() ? 'Mode Penjual' : 'Mode Pembeli' }}</span>
                    </strong>
                </div>
                <form method="POST" action="{{ route('user.switch_mode') }}" class="shrink-0">
                    @csrf
                    <input type="hidden" name="mode" value="{{ auth()->user()->isSeller() ? 'buyer' : 'seller' }}">
                    <button type="submit" class="min-h-[44px] px-3.5 py-2 rounded-xl bg-white border border-slate-300 text-slate-800 hover:border-blue-600 hover:text-blue-700 text-xs font-bold shadow-2xs inline-flex items-center gap-1.5 transition cursor-pointer">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                        </svg>
                        <span>Ke {{ auth()->user()->isSeller() ? 'Pembeli' : 'Penjual' }}</span>
                    </button>
                </form>
            </div>
        @endauth

        <div class="space-y-1">
            <p class="px-2 text-[11px] font-bold uppercase tracking-wider text-slate-500">Menu Utama</p>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-1.5">
                <a href="{{ url('/') }}"
                   class="min-h-[44px] px-3.5 py-2.5 rounded-xl flex items-center gap-2.5 text-sm font-bold transition {{ request()->routeIs('home') ? 'bg-blue-50 text-blue-700 border border-blue-200/80' : 'text-slate-800 hover:bg-slate-50' }}">
                    <svg class="w-4 h-4 shrink-0 {{ request()->routeIs('home') ? 'text-blue-700' : 'text-slate-500' }}" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                        <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"/>
                    </svg>
                    <span>Beranda</span>
                </a>

                <a href="{{ Route::has('listings.index') ? route('listings.index') : url('/katalog') }}"
                   class="min-h-[44px] px-3.5 py-2.5 rounded-xl flex items-center gap-2.5 text-sm font-bold transition {{ request()->is('katalog*') && !request()->is('katalog/pasang') ? 'bg-blue-50 text-blue-700 border border-blue-200/80' : 'text-slate-800 hover:bg-slate-50' }}">
                    <svg class="w-4 h-4 shrink-0 {{ request()->is('katalog*') && !request()->is('katalog/pasang') ? 'text-blue-700' : 'text-slate-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                    </svg>
                    <span>Katalog Barang</span>
                </a>

                <a href="{{ Route::has('auctions.index') ? route('auctions.index') : url('/lelang') }}"
                   class="min-h-[44px] px-3.5 py-2.5 rounded-xl flex items-center gap-2.5 text-sm font-bold transition {{ request()->routeIs('auctions.*') || request()->is('lelang*') ? 'bg-blue-50 text-blue-700 border border-blue-200/80' : 'text-slate-800 hover:bg-slate-50' }}">
                    <svg class="w-4 h-4 shrink-0 {{ request()->routeIs('auctions.*') || request()->is('lelang*') ? 'text-blue-700' : 'text-slate-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                    <span>Lelang Terbuka</span>
                </a>

                <a href="{{ route('wanted-ads.index') }}"
                   class="min-h-[44px] px-3.5 py-2.5 rounded-xl flex items-center gap-2.5 text-sm font-bold transition {{ request()->routeIs('wanted-ads.*') || request()->is('cari-barang*') ? 'bg-blue-50 text-blue-700 border border-blue-200/80' : 'text-slate-800 hover:bg-slate-50' }}">
                    <svg class="w-4 h-4 shrink-0 {{ request()->routeIs('wanted-ads.*') || request()->is('cari-barang*') ? 'text-blue-700' : 'text-slate-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>Papan Wanted Ads</span>
                </a>

                @auth
                    <a href="{{ Route::has('chat.index') ? route('chat.index') : url('/chat') }}"
                       class="min-h-[44px] px-3.5 py-2.5 rounded-xl flex items-center justify-between text-sm font-bold transition sm:col-span-2 {{ request()->routeIs('chat.*') || request()->is('chat*') ? 'bg-blue-50 text-blue-700 border border-blue-200/80' : 'text-slate-800 hover:bg-slate-50' }}">
                        <span class="flex items-center gap-2.5">
                            <svg class="w-4 h-4 shrink-0 {{ request()->routeIs('chat.*') || request()->is('chat*') ? 'text-blue-700' : 'text-slate-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                            </svg>
                            <span>Pesan &amp; Obrolan</span>
                        </span>
                        <span class="navbar-unread-badge-mobile min-w-[20px] h-[20px] px-1.5 bg-rose-600 text-white text-[11px] font-bold rounded-full flex items-center justify-center {{ $unreadChatCount > 0 ? '' : 'hidden' }}">
                            {{ $unreadChatCount > 99 ? '99+' : $unreadChatCount }}
                        </span>
                    </a>
                @endauth
            </div>
        </div>

        @auth
            <div class="space-y-1 pt-2 border-t border-slate-100">
                <p class="px-2 text-[11px] font-bold uppercase tracking-wider text-slate-500">
                    {{ auth()->user()->isSeller() ? 'Menu Kelola Penjual' : 'Menu Aktivitas Pembeli' }}
                </p>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-1.5">
                    @if(auth()->user()->isSeller())
                        <a href="{{ route('dashboard', ['tab' => 'dagangan']) }}"
                           class="min-h-[44px] px-3.5 py-2.5 rounded-xl flex items-center justify-between text-sm font-semibold text-emerald-800 bg-emerald-50/50 hover:bg-emerald-50 border border-emerald-200/60 transition">
                            <span>Dagangan Saya</span>
                            <span class="text-xs text-emerald-700">&rarr;</span>
                        </a>
                        <a href="{{ route('dashboard', ['tab' => 'lelang']) }}"
                           class="min-h-[44px] px-3.5 py-2.5 rounded-xl flex items-center justify-between text-sm font-semibold text-emerald-800 bg-emerald-50/50 hover:bg-emerald-50 border border-emerald-200/60 transition">
                            <span>Lelang Saya</span>
                            <span class="text-xs text-emerald-700">&rarr;</span>
                        </a>
                        <a href="{{ route('dashboard', ['tab' => 'peluang']) }}"
                           class="min-h-[44px] px-3.5 py-2.5 rounded-xl flex items-center justify-between text-sm font-semibold text-emerald-800 bg-emerald-50/50 hover:bg-emerald-50 border border-emerald-200/60 transition">
                            <span>Peluang Wanted Ads</span>
                            <span class="text-xs text-emerald-700">&rarr;</span>
                        </a>
                    @else
                        <a href="{{ route('dashboard', ['tab' => 'lelang']) }}"
                           class="min-h-[44px] px-3.5 py-2.5 rounded-xl flex items-center justify-between text-sm font-semibold text-blue-700 bg-blue-50/50 hover:bg-blue-50 border border-blue-200/60 transition">
                            <span>Bidding Lelang Saya</span>
                            <span class="text-xs text-blue-700">&rarr;</span>
                        </a>
                        <a href="{{ route('dashboard', ['tab' => 'wanted']) }}"
                           class="min-h-[44px] px-3.5 py-2.5 rounded-xl flex items-center justify-between text-sm font-semibold text-blue-700 bg-blue-50/50 hover:bg-blue-50 border border-blue-200/60 transition">
                            <span>Wanted Ads Saya</span>
                            <span class="text-xs text-blue-700">&rarr;</span>
                        </a>
                        <a href="{{ route('dashboard.history') }}"
                           class="min-h-[44px] px-3.5 py-2.5 rounded-xl flex items-center justify-between text-sm font-semibold text-blue-700 bg-blue-50/50 hover:bg-blue-50 border border-blue-200/60 transition">
                            <span>Riwayat Pembelian</span>
                            <span class="text-xs text-blue-700">&rarr;</span>
                        </a>
                    @endif
                </div>
            </div>
        @endauth

        <div class="pt-3 border-t border-slate-200">
            @if (auth()->check() && auth()->user()->isSeller())
                <a href="{{ route('katalog.pasang') }}" class="min-h-[48px] flex items-center justify-center px-4 py-3 text-sm font-bold bg-emerald-700 hover:bg-emerald-800 text-white rounded-xl shadow-xs transition">
                    + Pasang Iklan Barang Baru
                </a>
            @elseif (auth()->check() && auth()->user()->isBuyer())
                <a href="{{ route('wanted-ads.index') }}" class="min-h-[48px] flex items-center justify-center px-4 py-3 text-sm font-bold bg-blue-600 hover:bg-blue-700 text-white rounded-xl shadow-xs transition">
                    + Pasang Wanted Ad (Cari Barang)
                </a>
            @else
                <div class="grid grid-cols-2 gap-2.5">
                    <a href="{{ route('login') }}" class="min-h-[46px] flex items-center justify-center px-4 py-2.5 text-sm font-bold border border-slate-300 text-slate-800 hover:bg-slate-50 rounded-xl transition">
                        Masuk
                    </a>
                    <a href="{{ route('register') }}" class="min-h-[46px] flex items-center justify-center px-4 py-2.5 text-sm font-bold bg-blue-600 hover:bg-blue-700 text-white rounded-xl shadow-xs transition">
                        Daftar Akun
                    </a>
                </div>
            @endif
        </div>
    </div>

    @auth
        <script>
            (function() {
                const updateNavbarBadges = (count) => {
                    const badges = document.querySelectorAll('#navbar-unread-badge, .navbar-unread-badge-mobile');
                    const notifDot = document.getElementById('navbar-notif-dot');
                    badges.forEach(b => {
                        if (count > 0) {
                            b.textContent = count > 99 ? '99+' : count;
                            b.classList.remove('hidden');
                        } else {
                            b.classList.add('hidden');
                        }
                    });
                    if (notifDot) {
                        notifDot.classList.toggle('hidden', !(count > 0));
                    }
                };

                // Poll unread chat count every 8 seconds when outside /chat
                setInterval(async () => {
                    if (document.hidden) return;
                    if (window.location.pathname.startsWith('/chat')) return;

                    try {
                        const res = await fetch('{{ route('chat.web.unread_count') }}', {
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });
                        if (res.ok) {
                            const data = await res.json();
                            if (data && data.status === 'success') {
                                updateNavbarBadges(data.unread_count);
                            }
                        }
                    } catch (e) {
                        // Ignore transient network errors
                    }
                }, 8000);
            })();
        </script>
    @endauth
</nav>
