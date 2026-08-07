<div x-show="currentTab === 'history'" class="space-y-6 fade-in" x-cloak>
                    <div>
                        <h1 class="text-2xl font-outfit font-extrabold text-slate-900">Riwayat & Log Pemesanan Anda</h1>
                        <p class="text-xs text-slate-500">Log transaksi booking kamar dan ruang rapat kenegaraan.</p>
                    </div>

                    <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
                        <div class="p-6 border-b border-slate-100">
                            <h3 class="text-sm font-bold text-slate-900">Seluruh Transaksi Reservasi</h3>
                        </div>
                        <div class="divide-y divide-slate-100">
                            <template x-for="b in bookings" :key="b.id">
                                <div class="p-6 flex flex-col md:flex-row md:items-center justify-between gap-4 hover:bg-slate-50/50 transition-colors">
                                    <div class="flex items-center gap-4">
                                        <img :src="b.unit_photo" class="w-16 h-16 rounded-xl object-cover border border-slate-200 shrink-0" alt="Foto Fasilitas Terpilih">
                                        <div>
                                            <div class="flex items-center gap-2">
                                                <h4 class="text-sm font-bold text-slate-900" x-text="b.unit_name"></h4>
                                                <span class="px-2 py-0.5 text-[9px] uppercase font-bold rounded"
                                                      :class="{
                                                          'bg-amber-100 text-amber-700': b.status === 'Pending',
                                                          'bg-emerald-100 text-emerald-700': b.status === 'Lunas',
                                                          'bg-blue-100 text-blue-700': b.status === 'Check In',
                                                          'bg-red-100 text-red-700': b.status === 'Dibatalkan' || b.status === 'Cancelled',
                                                          'bg-slate-100 text-slate-600': b.status === 'Selesai'
                                                      }"
                                                      x-text="b.status === 'Check In' ? 'Aktif Menginap' : b.status"></span>
                                            </div>
                                            <p class="text-xs text-slate-500 mt-1 flex items-center gap-1.5 flex-wrap">
                                                <span x-text="b.booking_code"></span>
                                                <span class="text-slate-300">•</span>
                                                <i data-lucide="calendar" class="w-3.5 h-3.5"></i>
                                                <span x-text="formatIndoDate(b.check_in) + ' - ' + formatIndoDate(b.check_out)"></span>
                                                <span class="text-slate-300">•</span>
                                                <span x-text="b.nights + ((b.unit_name || '').includes('Rapat') ? ' Hari' : ' Malam')"></span>
                                            </p>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-4">
                                        <div class="text-right">
                                            <span class="text-[9px] text-slate-400 block font-bold uppercase tracking-wide">Total Biaya</span>
                                            <p class="text-sm font-bold text-slate-900" x-text="formatRupiah(b.total_price)"></p>
                                        </div>
                                        
                                        <!-- Actions -->
                                        <div class="flex items-center gap-2">
                                            <template x-if="b.status !== 'Pending' && b.status !== 'Cancelled' && b.status !== 'Dibatalkan'">
                                                <button @click="viewTicket(b)" class="px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-xl transition-all flex items-center gap-1">
                                                    <i data-lucide="ticket" class="w-3.5 h-3.5"></i> Tiket
                                                </button>
                                            </template>
                                            <template x-if="b.status === 'Pending'">
                                                <div class="flex flex-col items-end gap-1.5">
                                                    <div class="text-[10px] text-red-500 font-bold flex items-center gap-1 bg-red-50 px-2 py-1 rounded-md border border-red-100">
                                                        <i data-lucide="clock" class="w-3 h-3"></i> 
                                                        <span x-text="'Batas Waktu: ' + formatExpiryTime(b.created_at)"></span>
                                                    </div>
                                                    <div class="flex items-center gap-1.5 mt-1">
                                                        <button @click="resumePayment(b)" class="px-3 py-2 bg-[#0091FF] hover:bg-[#007CE6] text-white font-bold text-xs rounded-xl shadow-md transition-all flex items-center gap-1">
                                                            <i data-lucide="credit-card" class="w-3.5 h-3.5"></i> Bayar
                                                        </button>
                                                        <button @click="checkPaymentStatus(b)" class="px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-xl shadow-sm transition-all" title="Cek Status Manual">
                                                            <i data-lucide="refresh-cw" class="w-3.5 h-3.5"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </template>
                                            <template x-if="b.status === 'Selesai' && !b.hasFeedback">
                                                <button @click="openRatingModal(b)" class="px-3 py-2 bg-wisma-gold hover:bg-wisma-goldHover text-wisma-dark font-bold text-xs rounded-xl shadow-sm transition-all flex items-center gap-1">
                                                    <i data-lucide="star" class="w-3.5 h-3.5"></i> Ulas
                                                </button>
                                            </template>
                                            <template x-if="b.hasFeedback">
                                                <div class="flex items-center gap-1 text-wisma-gold px-2 py-1 bg-amber-50 rounded-lg text-xs font-extrabold border border-amber-100">
                                                    <i data-lucide="star" class="w-3.5 h-3.5 fill-current"></i>
                                                    <span x-text="b.rating.toFixed(1)"></span>
                                                </div>
                                            </template>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>

                <!-- 5. GUEST PROFILE VIEW -->