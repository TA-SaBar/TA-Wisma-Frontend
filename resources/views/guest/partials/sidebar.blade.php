<div x-show="sidebarOpen" @click="sidebarOpen = false" class="fixed inset-0 bg-slate-900/50 z-40 md:hidden" x-transition.opacity x-cloak></div>
<!-- SIDEBAR -->
        <!-- Mobile Sidebar Backdrop -->
        <div x-show="sidebarOpen" @click="sidebarOpen = false" class="fixed inset-0 bg-slate-900/50 z-40 md:hidden" x-transition.opacity x-cloak></div>

        <!-- SIDEBAR -->
        <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" class="w-72 bg-wisma-navy text-white flex flex-col shrink-0 h-[100dvh] shadow-2xl fixed inset-y-0 left-0 z-50 md:relative md:translate-x-0 transform transition-transform duration-300">
            <!-- Logo Area -->
            <div class="p-6 border-b border-slate-800 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <img src="/images/logo.png" class="h-10 w-auto object-contain rounded-xl" alt="Logo Wisma DPR RI">
                    <div>
                        <h2 class="font-outfit font-bold text-base tracking-wider leading-none">Wisma DPR RI</h2>
                        <span class="text-[10px] text-wisma-textMuted font-medium uppercase tracking-widest">Portal Tamu</span>
                    </div>
                </div>
                <button @click="sidebarOpen = false" class="md:hidden text-slate-400 hover:text-white p-1">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <!-- Sidebar Navigation Menu -->
            <nav class="flex-1 px-4 py-6 space-y-1.5 overflow-y-auto scrollbar-hide">
                <p class="text-[10px] text-slate-500 font-semibold px-3 mb-2 uppercase tracking-widest">Utama</p>
                
                <button @click="switchTab('dashboard')"
                        class="w-full flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 group text-left"
                        :class="currentTab === 'dashboard' ? 'bg-gradient-to-r from-wisma-gold to-amber-500 text-wisma-dark font-semibold shadow-lg shadow-wisma-gold/15' : 'text-wisma-textMuted hover:bg-slate-800/50 hover:text-white'">
                    <i data-lucide="layout-dashboard" class="w-5 h-5 transition-transform group-hover:scale-110"></i>
                    <span class="text-sm">Dashboard</span>
                </button>

                <button @click="switchTab('facilities')"
                        class="w-full flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 group text-left"
                        :class="currentTab === 'facilities' || currentTab === 'booking_wizard' ? 'bg-gradient-to-r from-wisma-gold to-amber-500 text-wisma-dark font-semibold shadow-lg shadow-wisma-gold/15' : 'text-wisma-textMuted hover:bg-slate-800/50 hover:text-white'">
                    <i data-lucide="search" class="w-5 h-5 transition-transform group-hover:scale-110"></i>
                    <span class="text-sm">Cari & Booking</span>
                    <span class="ml-auto px-2 py-0.5 bg-wisma-dark/25 rounded-md text-[10px]" x-text="facilities.filter(f => f.status === 'READY').length + ' Tersedia'"></span>
                </button>

                <button @click="switchTab('history')"
                        class="w-full flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 group text-left"
                        :class="currentTab === 'history' ? 'bg-gradient-to-r from-wisma-gold to-amber-500 text-wisma-dark font-semibold shadow-lg shadow-wisma-gold/15' : 'text-wisma-textMuted hover:bg-slate-800/50 hover:text-white'">
                    <i data-lucide="history" class="w-5 h-5 transition-transform group-hover:scale-110"></i>
                    <span class="text-sm">Riwayat Booking</span>
                    <span class="ml-auto w-2 h-2 bg-emerald-500" x-show="bookings.some(b => b.status === 'Lunas' || b.status === 'Check In')"></span>
                </button>

                <p class="text-[10px] text-slate-500 font-semibold px-3 pt-6 mb-2 uppercase tracking-widest">Layanan Mandiri</p>

                <button @click="switchTab('profile')"
                        class="w-full flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 group text-left"
                        :class="currentTab === 'profile' ? 'bg-gradient-to-r from-wisma-gold to-amber-500 text-wisma-dark font-semibold shadow-lg shadow-wisma-gold/15' : 'text-wisma-textMuted hover:bg-slate-800/50 hover:text-white'">
                    <i data-lucide="user" class="w-5 h-5 transition-transform group-hover:scale-110"></i>
                    <span class="text-sm">Profil Saya</span>
                </button>

                <button @click="switchTab('help')"
                        class="w-full flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 group text-left"
                        :class="currentTab === 'help' ? 'bg-gradient-to-r from-wisma-gold to-amber-500 text-wisma-dark font-semibold shadow-lg shadow-wisma-gold/15' : 'text-wisma-textMuted hover:bg-slate-800/50 hover:text-white'">
                    <i data-lucide="help-circle" class="w-5 h-5 transition-transform group-hover:scale-110"></i>
                    <span class="text-sm">Keluhan & Bantuan</span>
                </button>
            </nav>

            <!-- Sidebar Footer/User Profile Summary -->
            <div class="p-4 border-t border-slate-800 bg-wisma-dark/40 flex items-center gap-3">
                <img class="w-10 h-10 rounded-full border border-wisma-gold/30 object-cover" 
                     src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&w=100&h=100&q=80" 
                     alt="User Avatar">
                <div class="overflow-hidden">
                    <p class="text-xs font-semibold text-white truncate" x-text="profile.nama"></p>
                    <p class="text-[10px] text-wisma-textMuted truncate" x-text="profile.role_label"></p>
                </div>
                <button @click="logout()" class="ml-auto text-slate-400 hover:text-red-400 p-1.5 rounded-lg hover:bg-slate-800 transition-colors" title="Keluar">
                    <i data-lucide="log-out" class="w-4 h-4"></i>
                </button>
            </div>
        </aside>