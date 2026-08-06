<div x-show="currentTab === 'receptionist_dashboard'" class="space-y-8 fade-in">
                    <!-- Welcome Banner -->
                    <div class="relative overflow-hidden bg-gradient-to-r from-wisma-navy to-slate-900 text-white rounded-3xl p-8 shadow-xl">
                        <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_top_right,_var(--tw-gradient-stops))] from-emerald-500/10 via-transparent to-transparent"></div>
                        <div class="relative z-10 max-w-xl">
                            <span class="px-3 py-1 bg-emerald-500/20 text-emerald-400 text-[10px] uppercase font-bold tracking-widest rounded-full border border-emerald-400/30">Dashboard Pelayanan Tamu</span>
                            <h1 class="text-3xl font-outfit font-extrabold mt-4 mb-2 tracking-tight" x-text="'Selamat Bertugas, ' + profile.nama"></h1>
                            <p class="text-xs text-slate-300 leading-relaxed font-light">
                                Sistem Monitoring Front Desk. Pantau antrean check-in hari ini, proses pemesanan yang masuk, serta kelola hunian bungalow untuk menjamin kepuasan pelayanan.
                            </p>
                            <div class="mt-6 flex gap-3">
                                <button @click="switchTab('receptionist_check')" class="px-5 py-2.5 bg-wisma-gold hover:bg-wisma-goldHover text-wisma-dark font-semibold text-xs rounded-xl shadow-lg shadow-wisma-gold/20 transition-all flex items-center gap-1">
                                    <i data-lucide="log-in" class="w-4 h-4"></i> Layani Check-In / Out
                                </button>
                            </div>
                        </div>
                        <div class="absolute right-10 bottom-0 top-0 hidden lg:flex items-center text-white/5 pointer-events-none select-none">
                            <i data-lucide="concierge-bell" class="w-64 h-64"></i>
                        </div>
                    </div>

                    <!-- Stats Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex items-center justify-between group hover:shadow-md transition-shadow">
                            <div>
                                <span class="text-xs text-slate-500 font-medium">Antrean Check-in (Lunas)</span>
                                <h3 class="text-2xl font-bold font-outfit mt-1 text-slate-900" x-text="bookings.filter(b => b.status === 'lunas').length"></h3>
                            </div>
                            <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-500 flex items-center justify-center transition-transform group-hover:scale-110">
                                <i data-lucide="bell" class="w-6 h-6"></i>
                            </div>
                        </div>
                        <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex items-center justify-between group hover:shadow-md transition-shadow">
                            <div>
                                <span class="text-xs text-slate-500 font-medium">Tamu Sedang Menginap</span>
                                <h3 class="text-2xl font-bold font-outfit mt-1 text-slate-900" x-text="bookings.filter(b => b.status === 'check_in').length"></h3>
                            </div>
                            <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-500 flex items-center justify-center transition-transform group-hover:scale-110">
                                <i data-lucide="key" class="w-6 h-6"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 2. CHECK-IN / CHECK-OUT MANAGEMENT -->