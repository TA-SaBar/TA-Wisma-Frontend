<!-- HEADER -->
            <header class="h-20 bg-white border-b border-slate-100 flex items-center justify-between px-8 relative z-10 shrink-0">
                <div class="flex items-center gap-2 text-slate-700">
                    <i data-lucide="shield-check" class="w-5 h-5 text-emerald-500"></i>
                    <span class="text-xs font-bold font-outfit uppercase tracking-wider">Portal Resepsionis Aktif</span>
                </div>

                <div class="flex items-center gap-4">
                                        <!-- Notifications -->
                    <div class="relative" @click.outside="notificationsOpen = false">
                        <button @click="notificationsOpen = !notificationsOpen" 
                                class="w-10 h-10 rounded-xl bg-slate-50 border border-slate-100 flex items-center justify-center text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition-colors relative shadow-sm hover:shadow">
                            <i data-lucide="bell" class="w-5 h-5"></i>
                            <template x-if="unreadNotificationCount > 0">
                                <span class="absolute top-2 right-2 w-2.5 h-2.5 bg-red-500 rounded-full border-2 border-white animate-pulse"></span>
                            </template>
                        </button>

                        <!-- Dropdown Panel -->
                        <div x-show="notificationsOpen" 
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 translate-y-1 scale-95"
                             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                             x-transition:leave="transition ease-in duration-150"
                             x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                             x-transition:leave-end="opacity-0 translate-y-1 scale-95"
                             class="absolute right-0 mt-3 w-[360px] bg-white rounded-2xl shadow-xl border border-slate-100 z-50 flex flex-col max-h-[calc(100dvh-6rem)] sm:max-h-[480px] overflow-hidden" 
                             x-cloak>
                            
                            <!-- Header -->
                            <div class="px-5 py-4 bg-slate-50/80 backdrop-blur-md border-b border-slate-100 flex items-center justify-between shrink-0">
                                <div class="flex items-center gap-2">
                                    <h3 class="text-sm font-bold text-slate-900 font-outfit">Notifikasi</h3>
                                    <span class="px-2 py-0.5 bg-red-100 text-red-600 rounded-full text-[9px] font-extrabold" x-show="unreadNotificationCount > 0" x-text="unreadNotificationCount + ' Baru'"></span>
                                </div>
                                <button @click="markNotificationsRead()" x-show="unreadNotificationCount > 0" class="text-[10px] text-indigo-600 hover:text-indigo-800 font-bold hover:underline">
                                    Tandai semua dibaca
                                </button>
                            </div>

                            <!-- List -->
                            <div class="flex-1 overflow-y-auto divide-y divide-slate-50 scrollbar-hide">
                                <template x-for="n in notifications" :key="n.id">
                                    <div @click="handleNotificationClick(n)" class="cursor-pointer px-5 py-4 hover:bg-slate-50/50 transition-colors flex gap-3 relative group"
                                         :class="!(n.read || n.is_read) ? 'bg-indigo-50/30' : ''">
                                        
                                        <!-- Unread indicator dot -->
                                        <div x-show="!(n.read || n.is_read)" class="absolute left-2.5 top-1/2 -translate-y-1/2 w-1.5 h-1.5 bg-indigo-600 rounded-full"></div>
                                        
                                        <!-- Icon -->
                                        <div class="shrink-0 w-8 h-8 rounded-lg flex items-center justify-center mt-0.5"
                                             :class="{
                                                 'bg-emerald-50 text-emerald-600': ['booking', 'checkin', 'checkout'].includes(n.type),
                                                 'bg-amber-50 text-amber-500': n.type === 'complaint',
                                                 'bg-indigo-50 text-indigo-600': n.type === 'payment',
                                                 'bg-blue-50 text-blue-600': !['booking', 'checkin', 'checkout', 'complaint', 'payment'].includes(n.type)
                                             }">
                                            <template x-if="['booking', 'checkin', 'checkout'].includes(n.type)">
                                                <i data-lucide="calendar-check" class="w-4.5 h-4.5"></i>
                                            </template>
                                            <template x-if="n.type === 'payment'">
                                                <i data-lucide="credit-card" class="w-4.5 h-4.5"></i>
                                            </template>
                                            <template x-if="n.type === 'complaint'">
                                                <i data-lucide="alert-triangle" class="w-4.5 h-4.5"></i>
                                            </template>
                                            <template x-if="!['booking', 'checkin', 'checkout', 'complaint', 'payment'].includes(n.type)">
                                                <i data-lucide="bell" class="w-4.5 h-4.5"></i>
                                            </template>
                                        </div>

                                        <!-- Message content -->
                                        <div class="flex-1 min-w-0 pr-10">
                                            <div>
                                                <h4 class="text-xs font-bold text-slate-800 truncate" x-text="n.title"></h4>
                                                <p class="text-[11px] text-slate-500 leading-normal mt-0.5 font-light" x-text="n.message"></p>
                                                <span class="text-[9px] text-slate-400 font-medium block mt-1" x-text="formatTime(n.created_at)"></span>
                                            </div>
                                            <button @click.stop="deleteNotification(n.id)" class="absolute right-4 top-1/2 -translate-y-1/2 p-1.5 text-slate-300 hover:text-red-500 hover:bg-red-50 rounded-lg transition-all z-10 opacity-0 group-hover:opacity-100" title="Hapus Notifikasi">
                                                <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                                            </button>
                                        </div>
                                    </div>
                                </template>

                                <div x-show="notifications.length === 0" class="text-center py-12 text-slate-400">
                                    <i data-lucide="bell-off" class="w-10 h-10 mx-auto mb-2 text-slate-200"></i>
                                    <p class="text-xs font-medium text-slate-500">Belum ada notifikasi.</p>
                                </div>
                            </div>
                            
                            <!-- Footer -->
                            <div class="p-3 bg-slate-50 border-t border-slate-100 flex justify-center shrink-0">
                                <button @click="notificationsOpen = false" class="text-xs text-slate-500 hover:text-slate-700 font-bold">
                                    Tutup
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="w-px h-6 bg-slate-200 mx-2 hidden sm:block"></div>
                    <div class="flex items-center gap-3">
                        <div class="text-right hidden sm:block">
                            <p class="text-xs font-semibold text-slate-800" x-text="profile.nama"></p>
                            <p class="text-[10px] text-slate-500">Resepsionis</p>
                        </div>
                        <div class="w-10 h-10 rounded-xl bg-wisma-navy text-wisma-gold flex items-center justify-center font-bold text-sm border border-wisma-gold/20 shadow-sm" x-text="getInitials(profile.nama)"></div>
                    </div>
                </div>
            </header>