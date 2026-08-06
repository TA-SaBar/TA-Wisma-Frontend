<div x-show="currentTab === 'admin_guests'" class="space-y-6" x-cloak>
                    <div class="flex justify-between items-center">
                        <div>
                            <h1 class="text-2xl font-outfit font-extrabold text-slate-900">Database Tamu & Log Reservasi</h1>
                            <p class="text-xs text-slate-500">Monitor profil tamu resmi serta seluruh riwayat pemesanan e-Budgeting.</p>
                        </div>
                        
                        <div class="flex bg-slate-100 p-1 rounded-xl select-none">
                            <button type="button" 
                                    @click="adminGuestViewTab = 'tamu'"
                                    class="px-4 py-1.5 text-xs font-bold rounded-lg transition-all"
                                    :class="adminGuestViewTab === 'tamu' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-500 hover:text-slate-800'">
                                Database Tamu
                            </button>
                            <button type="button" 
                                    @click="adminGuestViewTab = 'reservasi'"
                                    class="px-4 py-1.5 text-xs font-bold rounded-lg transition-all"
                                    :class="adminGuestViewTab === 'reservasi' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-500 hover:text-slate-800'">
                                Log Pemesanan
                            </button>
                        </div>
                    </div>

                    <!-- VIEW 1: GUEST DATABASE -->
                    <div x-show="adminGuestViewTab === 'tamu'" class="space-y-4">
                        <div class="bg-white p-4 rounded-2xl border border-slate-100 shadow-sm flex items-center justify-between gap-4">
                            <div class="relative w-80">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                                    <i data-lucide="search" class="w-4 h-4"></i>
                                </span>
                                <input type="text" 
                                       x-model.debounce.500ms="adminGuestSearch" 
                                       placeholder="Cari nama tamu, email, telepon..." 
                                       class="w-full pl-9 pr-4 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-1 focus:ring-wisma-gold focus:outline-none transition-all">
                            </div>
                            <div class="flex gap-2">
                                <button @click="adminGuestFilter = 'semua'" :class="adminGuestFilter === 'semua' ? 'bg-wisma-navy text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'" class="px-4 py-2 rounded-xl text-xs font-bold transition-all">Semua</button>
                                <button @click="adminGuestFilter = 'member'" :class="adminGuestFilter === 'member' ? 'bg-wisma-navy text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'" class="px-4 py-2 rounded-xl text-xs font-bold transition-all">Hanya Member</button>
                                <button @click="adminGuestFilter = 'menginap'" :class="adminGuestFilter === 'menginap' ? 'bg-wisma-navy text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'" class="px-4 py-2 rounded-xl text-xs font-bold transition-all">Sedang Menginap</button>
                            </div>
                        </div>

                        <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
                            <div class="mb-4 bg-blue-50/50 p-3 rounded-xl border border-blue-100/50">
                                <p class="text-xs text-slate-600 leading-relaxed">
                                    <i data-lucide="info" class="w-3.5 h-3.5 inline-block mr-1 -mt-0.5 text-blue-500"></i>
                                    <strong>Status Registrasi:</strong> Label <span class="text-indigo-600 font-bold">Member</span> menandakan bahwa akun tersebut sudah memiliki riwayat reservasi (pernah menginap). Sedangkan label <span class="text-slate-500 font-bold">Reguler</span> berarti pengguna belum memiliki transaksi.
                                </p>
                            </div>

                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr class="bg-slate-50 border-b border-slate-100 text-[10px] text-slate-400 font-bold uppercase tracking-wider">
                                        <th class="py-4 px-6">ID / Nama Tamu</th>
                                        <th class="py-4 px-6">DPR ID / NIP</th>
                                        <th class="py-4 px-6">Kontak & Email</th>
                                        <th class="py-4 px-6">Kunjungan</th>
                                        <th class="py-4 px-6">Status Registrasi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 text-xs">
                                    <template x-for="g in filteredGuests()" :key="g.id">
                                        <tr class="hover:bg-slate-50/50 transition-all">
                                            <td class="py-4 px-6">
                                                <p class="font-bold text-slate-900" x-text="g.nama"></p>
                                                <p class="text-[10px] text-slate-400 mt-0.5" x-text="g.id"></p>
                                            </td>
                                            <td class="py-4 px-6 font-semibold text-slate-800" x-text="g.nip"></td>
                                            <td class="py-4 px-6">
                                                <p class="font-medium text-slate-800" x-text="g.phone"></p>
                                                <p class="text-[10px] text-slate-400 mt-0.5" x-text="g.email"></p>
                                            </td>
                                            <td class="py-4 px-6 font-bold text-slate-700" x-text="g.kunjungan + ' kali'"></td>
                                            <td class="py-4 px-6">
                                                <span class="px-2 py-0.5 rounded text-[9px] uppercase font-bold tracking-wide"
                                                      :class="g.status === 'Member' ? 'bg-indigo-100 text-indigo-700' : (g.status === 'Reguler' ? 'bg-slate-100 text-slate-600' : 'bg-red-100 text-red-700')"
                                                      x-text="g.status"></span>
                                            </td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- VIEW 2: RESERVATION LOGS -->
                    <div x-show="adminGuestViewTab === 'reservasi'" class="space-y-4">
                        <div class="bg-white p-4 rounded-2xl border border-slate-100 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
                            <div class="relative w-full md:w-96">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                                    <i data-lucide="search" class="w-4 h-4"></i>
                                </span>
                                <input type="text" 
                                       x-model.debounce.500ms="adminLogSearch" 
                                       placeholder="Cari kode booking, nama tamu, atau NIP..." 
                                       class="w-full pl-9 pr-4 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-1 focus:ring-wisma-gold focus:outline-none transition-all">
                            </div>
                            <div class="flex flex-wrap gap-2">
                                <button @click="adminLogFilter = 'semua'" :class="adminLogFilter === 'semua' ? 'bg-wisma-navy text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'" class="px-4 py-2 rounded-xl text-[10px] uppercase tracking-wide font-bold transition-all">Semua</button>
                                <button @click="adminLogFilter = 'pending'" :class="adminLogFilter === 'pending' ? 'bg-wisma-navy text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'" class="px-4 py-2 rounded-xl text-[10px] uppercase tracking-wide font-bold transition-all">Pending</button>
                                <button @click="adminLogFilter = 'lunas'" :class="adminLogFilter === 'lunas' ? 'bg-wisma-navy text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'" class="px-4 py-2 rounded-xl text-[10px] uppercase tracking-wide font-bold transition-all">Lunas</button>
                                <button @click="adminLogFilter = 'check in'" :class="adminLogFilter === 'check in' ? 'bg-wisma-navy text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'" class="px-4 py-2 rounded-xl text-[10px] uppercase tracking-wide font-bold transition-all">Check In</button>
                                <button @click="adminLogFilter = 'selesai'" :class="adminLogFilter === 'selesai' ? 'bg-wisma-navy text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'" class="px-4 py-2 rounded-xl text-[10px] uppercase tracking-wide font-bold transition-all">Selesai</button>
                                <button @click="adminLogFilter = 'dibatalkan'" :class="adminLogFilter === 'dibatalkan' ? 'bg-wisma-navy text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'" class="px-4 py-2 rounded-xl text-[10px] uppercase tracking-wide font-bold transition-all">Dibatalkan</button>
                            </div>
                        </div>

                        <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr class="bg-slate-50 border-b border-slate-100 text-[10px] text-slate-400 font-bold uppercase tracking-wider">
                                        <th class="py-4 px-6">No. | Kode</th>
                                        <th class="py-4 px-6">Nama Tamu</th>
                                        <th class="py-4 px-6">Fasilitas / Unit</th>
                                        <th class="py-4 px-6">Masa Inap</th>
                                        <th class="py-4 px-6">Total Tagihan</th>
                                        <th class="py-4 px-6">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 text-xs text-slate-800">
                                    <template x-for="(b, idx) in bookings.filter(x => {
                                        const matchesSearch = (x.nama || '').toLowerCase().includes(adminLogSearch.toLowerCase()) || (x.nip || '').toLowerCase().includes(adminLogSearch.toLowerCase()) || (x.booking_code || '').toLowerCase().includes(adminLogSearch.toLowerCase());
                                        const matchesStatus = adminLogFilter === 'semua' || x.status.toLowerCase() === adminLogFilter.toLowerCase();
                                        return matchesSearch && matchesStatus;
                                    })" :key="b.id">
                                        <tr class="hover:bg-slate-50/50 transition-all">
                                        <td class="py-4 px-6">
                                            <p class="font-bold text-slate-900" x-text="idx + 1"></p>
                                            <p class="text-[9px] text-slate-400 mt-0.5" x-text="b.booking_code"></p>
                                        </td>
                                        <td class="py-4 px-6">
                                            <p class="font-semibold" x-text="b.nama"></p>
                                            <p class="text-[9px] text-slate-400 mt-0.5" x-text="'NIP: ' + b.nip"></p>
                                        </td>
                                        <td class="py-4 px-6" x-text="b.unit_name"></td>
                                        <td class="py-4 px-6">
                                            <p class="font-medium" x-text="formatIndoDate(b.check_in) + ' -'"></p>
                                            <p class="font-medium" x-text="formatIndoDate(b.check_out)"></p>
                                        </td>
                                        <td class="py-4 px-6 font-bold" x-text="formatRupiah(b.total_price)"></td>
                                        <td class="py-4 px-6">
                                                  <span class="px-2 py-0.5 rounded text-[9px] uppercase font-bold tracking-wide"
                                                        :class="{
                                                            'bg-emerald-100 text-emerald-700': b.status === 'Lunas',
                                                            'bg-blue-100 text-blue-700': b.status === 'Check In',
                                                            'bg-slate-100 text-slate-600': b.status === 'Selesai',
                                                            'bg-amber-100 text-amber-700': b.status === 'Pending',
                                                            'bg-red-100 text-red-700': b.status === 'Dibatalkan' || b.status === 'Cancelled'
                                                        }"
                                                        x-text="b.status === 'Check In' ? 'Aktif Menginap' : b.status"></span>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- 4. REPORTS VIEW -->