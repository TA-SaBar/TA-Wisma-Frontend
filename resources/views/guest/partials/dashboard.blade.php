<div x-show="currentTab === 'dashboard'" class="space-y-8 fade-in">
                    <!-- Welcome Banner -->
                    <div class="relative overflow-hidden bg-gradient-to-r from-wisma-navy to-slate-900 text-white rounded-3xl p-8 shadow-xl">
                        <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_top_right,_var(--tw-gradient-stops))] from-amber-500/20 via-transparent to-transparent"></div>
                        <div class="relative z-10 max-w-xl">
                            <span class="px-3 py-1 bg-amber-500/20 text-wisma-gold text-[10px] uppercase font-bold tracking-widest rounded-full border border-wisma-gold/30">Portal Reservasi Tamu</span>
                            <h1 class="text-3xl font-outfit font-extrabold mt-4 mb-2 tracking-tight" x-text="'Selamat Datang, ' + profile.nama"></h1>
                            <p class="text-xs text-slate-300 leading-relaxed font-light">
                                Portal Layanan Wisma DPR RI. Lakukan pemesanan unit kamar penginapan atau ruang rapat secara dinamis. Pantau status check-in Anda hari ini.
                            </p>
                            <div class="mt-6 flex gap-3">
                                <button @click="switchTab('facilities')" class="px-5 py-2.5 bg-wisma-gold hover:bg-wisma-goldHover text-wisma-dark font-semibold text-xs rounded-xl shadow-lg shadow-wisma-gold/20 transition-all flex items-center gap-2">
                                    <i data-lucide="search" class="w-4 h-4"></i>
                                    Cari Fasilitas & Booking
                                </button>
                                <button @click="switchTab('history')" class="px-5 py-2.5 bg-white/10 hover:bg-white/20 text-white border border-white/10 font-semibold text-xs rounded-xl transition-all flex items-center gap-2">
                                    <i data-lucide="ticket" class="w-4 h-4"></i>
                                    Lihat Tiket Reservasi
                                </button>
                            </div>
                        </div>
                        <div class="absolute right-10 bottom-0 top-0 hidden lg:flex items-center text-white/5 pointer-events-none select-none">
                            <i data-lucide="hotel" class="w-64 h-64"></i>
                        </div>
                    </div>

                    <!-- Stats Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex items-center justify-between group hover:shadow-md transition-shadow">
                            <div>
                                <span class="text-xs text-slate-500 font-medium">Reservasi Aktif</span>
                                <h3 class="text-2xl font-bold font-outfit mt-1 text-slate-900" x-text="bookings.filter(b => b.status === 'Lunas' || b.status === 'Check In').length"></h3>
                            </div>
                            <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-500 flex items-center justify-center transition-transform group-hover:scale-110">
                                <i data-lucide="calendar-check" class="w-6 h-6"></i>
                            </div>
                        </div>
                        <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex items-center justify-between group hover:shadow-md transition-shadow">
                            <div>
                                <span class="text-xs text-slate-500 font-medium">Reservasi Selesai</span>
                                <h3 class="text-2xl font-bold font-outfit mt-1 text-slate-900" x-text="bookings.filter(b => b.status === 'Selesai').length"></h3>
                            </div>
                            <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-500 flex items-center justify-center transition-transform group-hover:scale-110">
                                <i data-lucide="check-circle" class="w-6 h-6"></i>
                            </div>
                        </div>
                        <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex items-center justify-between group hover:shadow-md transition-shadow">
                            <div>
                                <span class="text-xs text-slate-500 font-medium">Keluhan Anda Diajukan</span>
                                <h3 class="text-2xl font-bold font-outfit mt-1 text-slate-900" x-text="complaints.length"></h3>
                            </div>
                            <div class="w-12 h-12 rounded-xl bg-amber-50 text-wisma-gold flex items-center justify-center transition-transform group-hover:scale-110">
                                <i data-lucide="alert-triangle" class="w-6 h-6"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Active Bookings Summary -->
                    <div class="bg-white rounded-3xl border border-slate-100 shadow-sm p-6">
                        <div class="flex items-center justify-between mb-6">
                            <div>
                                <h2 class="text-base font-bold text-slate-900">Reservasi Aktif Anda</h2>
                                <p class="text-xs text-slate-500">Boarding pass digital Anda. Tunjukkan tiket ini kepada resepsionis saat check-in.</p>
                            </div>
                            <button @click="switchTab('history')" class="text-xs text-wisma-gold font-semibold hover:underline flex items-center gap-1">
                                Lihat Semua Riwayat <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
                            </button>
                        </div>

                        <div class="space-y-4">
                            <template x-for="b in bookings.filter(x => x.status === 'Lunas' || x.status === 'Check In')" :key="b.id">
                                <div class="flex flex-col md:flex-row items-start md:items-center justify-between p-4 bg-slate-50 hover:bg-slate-100/70 border border-slate-100 rounded-2xl transition-colors">
                                    <div class="flex items-center gap-4">
                                        <img :src="b.unit_photo" class="w-16 h-16 rounded-xl object-cover border border-slate-200">
                                        <div>
                                            <div class="flex items-center gap-2">
                                                <h4 class="text-sm font-bold text-slate-900" x-text="b.unit_name"></h4>
                                                <span class="px-2 py-0.5 text-[9px] uppercase font-bold rounded"
                                                      :class="b.status === 'Check In' ? 'bg-blue-100 text-blue-700' : 'bg-emerald-100 text-emerald-700'"
                                                      x-text="b.status === 'Check In' ? 'Aktif Menginap' : b.status"></span>
                                            </div>
                                            <p class="text-xs text-slate-500 mt-1 flex items-center gap-2">
                                                <i data-lucide="calendar" class="w-3.5 h-3.5"></i>
                                                <span x-text="formatIndoDate(b.check_in) + ' - ' + formatIndoDate(b.check_out)"></span>
                                                <span class="text-slate-300">|</span>
                                                <span x-text="b.nights + ((b.unit_name || '').includes('Rapat') ? ' Hari' : ' Malam')"></span>
                                            </p>
                                        </div>
                                    </div>
                                    <div class="mt-4 md:mt-0 flex items-center gap-3">
                                        <div class="text-right">
                                            <span class="text-[10px] text-slate-500">Total Pembayaran</span>
                                            <p class="text-sm font-bold text-slate-900" x-text="formatRupiah(b.total_price)"></p>
                                        </div>
                                        <button @click="viewTicket(b)" class="px-4 py-2 bg-wisma-navy hover:bg-slate-800 text-white font-semibold text-xs rounded-xl shadow-md transition-colors flex items-center gap-1.5">
                                            <i data-lucide="ticket" class="w-3.5 h-3.5"></i> Lihat Tiket
                                        </button>
                                    </div>
                                </div>
                            </template>
                            <div x-show="bookings.filter(x => x.status === 'Lunas' || x.status === 'Check In').length === 0" class="text-center py-8 text-slate-400">
                                <i data-lucide="calendar-x" class="w-12 h-12 mx-auto mb-2 text-slate-300"></i>
                                <p class="text-xs">Tidak ada reservasi aktif saat ini.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 2. FACILITIES CATALOG VIEW -->