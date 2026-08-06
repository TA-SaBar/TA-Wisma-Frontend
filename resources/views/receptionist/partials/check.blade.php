<div x-show="currentTab === 'receptionist_check'" class="space-y-6 fade-in" x-cloak>
                    <div>
                        <h1 class="text-2xl font-outfit font-extrabold text-slate-900">Manajemen Check-In / Check-Out</h1>
                        <p class="text-xs text-slate-500">Konfirmasi kedatangan tamu kedinasan (Check-In) atau kepulangan tamu (Check-Out) secara instan.</p>
                    </div>

                    <!-- Search and filters -->
                    <div class="bg-white p-4 rounded-2xl border border-slate-100 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
                        <div class="relative w-80">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                                <i data-lucide="search" class="w-4 h-4"></i>
                            </span>
                            <input type="text" 
                                   x-model="receptionistSearch" 
                                   placeholder="Cari nama tamu, NIP, atau no booking..." 
                                   class="w-full pl-9 pr-4 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-1 focus:ring-wisma-gold focus:outline-none transition-all">
                        </div>
                        <div class="flex gap-2 flex-wrap">
                            <button @click="receptionistFilter = 'semua'; setTimeout(() => { if (window.lucide) window.lucide.createIcons(); }, 50);" :class="receptionistFilter === 'semua' ? 'bg-wisma-navy text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'" class="px-4 py-2 rounded-xl text-xs font-bold transition-all">Semua</button>
                            <button @click="receptionistFilter = 'lunas'; setTimeout(() => { if (window.lucide) window.lucide.createIcons(); }, 50);" :class="receptionistFilter === 'lunas' ? 'bg-wisma-navy text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'" class="px-4 py-2 rounded-xl text-xs font-bold transition-all">Antrean Check-In</button>
                            <button @click="receptionistFilter = 'check_in'; setTimeout(() => { if (window.lucide) window.lucide.createIcons(); }, 50);" :class="receptionistFilter === 'check_in' ? 'bg-wisma-navy text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'" class="px-4 py-2 rounded-xl text-xs font-bold transition-all">Sedang Menginap</button>
                            <button @click="receptionistFilter = 'selesai'; setTimeout(() => { if (window.lucide) window.lucide.createIcons(); }, 50);" :class="receptionistFilter === 'selesai' ? 'bg-wisma-navy text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'" class="px-4 py-2 rounded-xl text-xs font-bold transition-all">Check-Out Selesai</button>
                        </div>
                    </div>

                    <!-- Bookings List Table -->
                    <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-slate-50 border-b border-slate-100 text-[10px] text-slate-400 font-bold uppercase tracking-wider">
                                    <th class="py-4 px-6">No. Booking / Tamu</th>
                                    <th class="py-4 px-6">Detail Unit</th>
                                    <th class="py-4 px-6">Masa Inap (Durasi)</th>
                                    <th class="py-4 px-6">Status Booking</th>
                                    <th class="py-4 px-6 text-right">Aksi Pelayanan</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-xs">
                                <template x-for="b in filteredBookings()" :key="b.id">
                                    <tr class="hover:bg-slate-50/50 transition-all">
                                        <td class="py-4 px-6">
                                            <p class="font-bold text-slate-900" x-text="b.booking_code"></p>
                                            <p class="text-[11px] text-slate-500 font-medium mt-0.5" x-text="b.guest_name"></p>
                                            <p class="text-[9px] text-slate-400 mt-0.5" x-text="'NIP: ' + (b.guest_nip || '-')"></p>
                                        </td>
                                        <td class="py-4 px-6">
                                            <p class="font-bold text-slate-900" x-text="b.facility ? b.facility.name : '-'"></p>
                                            <p class="text-[9px] text-slate-400 mt-0.5" x-text="b.facility ? b.facility.area : ''"></p>
                                        </td>
                                        <td class="py-4 px-6">
                                            <p class="font-semibold text-slate-800" x-text="formatIndoDate(b.check_in) + ' s/d'"></p>
                                            <p class="font-semibold text-slate-800" x-text="formatIndoDate(b.check_out)"></p>
                                            <span class="text-[10px] text-slate-400 block mt-1" x-text="b.nights + ' Malam/Hari'"></span>
                                        </td>
                                        <td class="py-4 px-6">
                                            <span class="px-2 py-0.5 rounded text-[9px] uppercase font-bold tracking-wide"
                                                  :class="{
                                                      'bg-yellow-100 text-yellow-700': b.status === 'pending',
                                                      'bg-emerald-100 text-emerald-700': b.status === 'lunas',
                                                      'bg-blue-100 text-blue-700': b.status === 'check_in',
                                                      'bg-slate-100 text-slate-600': b.status === 'selesai',
                                                      'bg-red-100 text-red-600': b.status === 'cancelled'
                                                  }"
                                                  x-text="statusLabel(b.status)"></span>
                                        </td>
                                        <td class="py-4 px-6 text-right">
                                            <template x-if="b.status === 'lunas'">
                                                <button @click="showConfirm('checkin', b)" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl shadow-md transition-all flex items-center gap-1.5 ml-auto">
                                                    <i data-lucide="log-in" class="w-3.5 h-3.5"></i> Proses Check In
                                                </button>
                                            </template>
                                            <template x-if="b.status === 'check_in'">
                                                <button @click="showConfirm('checkout', b)" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-bold text-xs rounded-xl shadow-md transition-all flex items-center gap-1.5 ml-auto">
                                                    <i data-lucide="log-out" class="w-3.5 h-3.5"></i> Proses Check Out
                                                </button>
                                            </template>
                                            <template x-if="b.status === 'selesai'">
                                                <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block">Selesai/Arsip</span>
                                            </template>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                        <div x-show="filteredBookings().length === 0" class="text-center py-12 text-slate-400">
                            <i data-lucide="inbox" class="w-12 h-12 mx-auto mb-2 text-slate-200"></i>
                            <p class="text-xs">Tidak ada reservasi yang sesuai filter pencarian.</p>
                        </div>
                    </div>
                </div>