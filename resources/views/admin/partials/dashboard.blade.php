<!-- 1. DASHBOARD VIEW -->
                <div x-show="currentTab === 'admin_dashboard'" class="space-y-8 fade-in">
                    <!-- Welcome Banner -->
                    <div class="relative overflow-hidden bg-gradient-to-r from-wisma-navy to-slate-900 text-white rounded-3xl p-8 shadow-xl">
                        <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_top_right,_var(--tw-gradient-stops))] from-amber-500/15 via-transparent to-transparent"></div>
                        <div class="relative z-10 max-w-xl">
                            <span class="px-3 py-1 bg-amber-500/20 text-wisma-gold text-[10px] uppercase font-bold tracking-widest rounded-full border border-wisma-gold/30">Backend Control Panel</span>
                            <h1 class="text-3xl font-outfit font-extrabold mt-4 mb-2 tracking-tight" x-text="'Selamat Datang, ' + profile.nama"></h1>
                            <p class="text-xs text-slate-300 leading-relaxed font-light">
                                Sistem Manajemen Terpadu Wisma DPR RI. Tambah, edit, dan awasi ketersediaan unit kamar, tinjau log data reservasi masuk, serta pantau laporan okupansi operasional wisma.
                            </p>
                            <div class="mt-6 flex gap-3">
                                <button @click="switchTab('admin_management')" class="px-5 py-2.5 bg-wisma-gold hover:bg-wisma-goldHover text-wisma-dark font-semibold text-xs rounded-xl shadow-lg shadow-wisma-gold/20 transition-all flex items-center gap-1">
                                    <i data-lucide="layout" class="w-4 h-4"></i> Manajemen Unit
                                </button>
                                <button @click="switchTab('admin_guests')" class="px-5 py-2.5 bg-white/10 hover:bg-white/20 text-white border border-white/10 font-semibold text-xs rounded-xl transition-all flex items-center gap-1">
                                    <i data-lucide="users" class="w-4 h-4"></i> Database Tamu
                                </button>
                            </div>
                        </div>
                        <div class="absolute right-10 bottom-0 top-0 hidden lg:flex items-center text-white/5 pointer-events-none select-none">
                            <i data-lucide="shield" class="w-64 h-64"></i>
                        </div>
                    </div>

                    <!-- Dashboard Filter -->
                    <div class="bg-white p-5 rounded-3xl border border-slate-100 shadow-sm space-y-4 mb-8">
                        <div class="flex items-center justify-between flex-wrap gap-2">
                            <div class="flex items-center gap-2 text-xs font-bold text-slate-800 uppercase tracking-wide">
                                <i data-lucide="calendar" class="w-4 h-4 text-wisma-gold"></i>
                                <span>Filter Dashboard</span>
                            </div>
                            <div class="flex items-center gap-1.5">
                                <button @click="setQuickPeriodDashboard('all')" :class="activeQuickPeriodDashboard === 'all' ? 'bg-wisma-navy text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'" class="px-3 py-1.5 rounded-xl text-[10px] font-bold transition-all">Semua Waktu</button>
                                <button @click="setQuickPeriodDashboard('this_month')" :class="activeQuickPeriodDashboard === 'this_month' ? 'bg-wisma-navy text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'" class="px-3 py-1.5 rounded-xl text-[10px] font-bold transition-all">Bulan Ini</button>
                                <button @click="setQuickPeriodDashboard('last_month')" :class="activeQuickPeriodDashboard === 'last_month' ? 'bg-wisma-navy text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'" class="px-3 py-1.5 rounded-xl text-[10px] font-bold transition-all">Bulan Lalu</button>
                                <button @click="setQuickPeriodDashboard('this_year')" :class="activeQuickPeriodDashboard === 'this_year' ? 'bg-wisma-navy text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'" class="px-3 py-1.5 rounded-xl text-[10px] font-bold transition-all">Tahun Ini</button>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="space-y-1">
                                <label class="block text-[10px] text-slate-400 font-bold uppercase">Tanggal Mulai</label>
                                <input type="date" x-model="dashboardStartDate" @change="activeQuickPeriodDashboard = 'custom'" class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl p-2.5 focus:bg-white focus:ring-1 focus:ring-wisma-gold focus:outline-none transition-all">
                            </div>
                            <div class="space-y-1">
                                <label class="block text-[10px] text-slate-400 font-bold uppercase">Tanggal Selesai</label>
                                <input type="date" x-model="dashboardEndDate" @change="activeQuickPeriodDashboard = 'custom'" class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl p-2.5 focus:bg-white focus:ring-1 focus:ring-wisma-gold focus:outline-none transition-all">
                            </div>
                        </div>
                    </div>

                    <!-- Stats Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex items-center justify-between group hover:shadow-md transition-shadow">
                            <div>
                                <span class="text-xs text-slate-500 font-medium">Fasilitas Unit Terdaftar</span>
                                <h3 class="text-2xl font-bold font-outfit mt-1 text-slate-900" x-text="facilities.length + ' Unit'"></h3>
                            </div>
                            <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-500 flex items-center justify-center transition-transform group-hover:scale-110">
                                <i data-lucide="home" class="w-6 h-6"></i>
                            </div>
                        </div>
                        <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex flex-col justify-between hover:shadow-md transition-shadow">
                            <div class="flex justify-between items-start w-full">
                                <div>
                                    <span class="text-xs text-slate-500 font-medium">Tamu Terdaftar</span>
                                    <h3 class="text-2xl font-bold font-outfit mt-1 text-slate-900" x-text="getFilteredGuestsCount() + ' Tamu'"></h3>
                                </div>
                                <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-500 flex items-center justify-center">
                                    <i data-lucide="user-check" class="w-5 h-5"></i>
                                </div>
                            </div>
                            <div class="mt-4 pt-3 border-t border-slate-50 flex justify-between items-center">
                                <span class="text-[10px] text-slate-400 font-semibold uppercase">Berdasarkan Periode</span>
                            </div>
                        </div>
                        <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex flex-col justify-between hover:shadow-md transition-shadow">
                            <div class="flex justify-between items-start w-full">
                                <div>
                                    <span class="text-xs text-slate-500 font-medium">Pendapatan Diterima (Estimasi)</span>
                                    <h3 class="text-xl font-bold font-outfit mt-1.5 text-slate-900" x-text="formatRupiah(getFilteredIncome())"></h3>
                                </div>
                                <div class="w-10 h-10 rounded-xl bg-amber-50 text-wisma-gold flex items-center justify-center">
                                    <i data-lucide="wallet" class="w-5 h-5"></i>
                                </div>
                            </div>
                            <div class="mt-4 pt-3 border-t border-slate-50 flex justify-between items-center">
                                <span class="text-[10px] text-slate-400 font-semibold uppercase">Berdasarkan Periode</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Statistics Charts Relocated from Reports -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="bg-white border border-slate-100 rounded-3xl p-6 shadow-sm space-y-4">
                            <h3 class="text-sm font-bold text-slate-900">Performa Hunian Kamar & Ruang</h3>
                            <div class="h-48 flex items-end justify-between gap-4 pt-6 border-b border-slate-100 pb-4">
                                <template x-for="(m, idx) in getMonthlyChart()" :key="idx">
                                    <div class="w-full bg-slate-100 rounded-t-lg h-32 relative group flex flex-col justify-end">
                                        <div class="absolute -top-16 left-1/2 -translate-x-1/2 bg-slate-800 text-white text-[9px] p-2 rounded-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all whitespace-nowrap z-10 shadow-lg pointer-events-none flex flex-col gap-1 items-center">
                                            <span class="font-bold text-wisma-gold" x-text="m.count + ' Transaksi'"></span>
                                            <div class="flex gap-2 text-slate-300">
                                                <span x-text="'Kamar: ' + m.kamar"></span>
                                                <span x-text="'Rapat: ' + m.rapat"></span>
                                            </div>
                                            <div class="absolute -bottom-1 left-1/2 -translate-x-1/2 border-4 border-transparent border-t-slate-800"></div>
                                        </div>
                                        <div class="w-full bg-[#0B1A30] rounded-t-lg transition-all" :style="'height: ' + m.percent + '%'"></div>
                                        <span class="text-[8px] text-slate-400 absolute -bottom-5 w-full text-center block" x-text="m.label"></span>
                                    </div>
                                </template>
                            </div>
                            <p class="text-[10px] text-slate-500 mt-2">Grafik dinamis ini menghitung tren kepadatan pemesanan (okupansi) selama 3 bulan terakhir berdasarkan data di sistem.</p>
                        </div>

                        <div class="bg-white border border-slate-100 rounded-3xl p-6 shadow-sm space-y-4">
                            <h3 class="text-sm font-bold text-slate-900">Persentase Hunian Berdasarkan Tipe</h3>
                            <div class="space-y-3 pt-4 text-xs">
                                <template x-for="t in getTypeOccupancy()" :key="t.label">
                                    <div class="space-y-1">
                                        <div class="flex justify-between font-bold text-slate-800"><span x-text="t.label"></span><span x-text="t.percent + '%' "></span></div>
                                        <div class="w-full h-2 bg-slate-100 rounded-full"><div class="bg-wisma-navy h-full rounded-full transition-all" :style="'width: ' + t.percent + '%'"></div></div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 2. FACILITIES CRUD INVENTORY VIEW -->