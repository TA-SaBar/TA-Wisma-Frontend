<div x-show="currentTab === 'admin_reports'" class="space-y-6" x-cloak>
                    <!-- Header -->
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 no-print">
                        <div>
                            <h1 class="text-2xl font-outfit font-extrabold text-slate-900">Laporan Keuangan & Okupansi Wisma</h1>
                            <p class="text-xs text-slate-500">Statistik performa tingkat hunian dan audit penerimaan dana.</p>
                        </div>
                        <button @click="printReport()" class="px-5 py-2.5 bg-[#0B1A30] hover:bg-slate-800 text-white font-bold text-xs rounded-xl shadow-lg transition-all flex items-center gap-1.5">
                            <i data-lucide="printer" class="w-4 h-4"></i> Cetak Laporan
                        </button>
                    </div>

                                        <!-- Filter Periode Laporan (no-print) -->
                    <div class="bg-white p-5 rounded-3xl border border-slate-100 shadow-sm space-y-4 no-print">
                        <div class="flex items-center justify-between flex-wrap gap-2">
                            <div class="flex items-center gap-2 text-xs font-bold text-slate-800 uppercase tracking-wide">
                                <i data-lucide="calendar" class="w-4 h-4 text-wisma-gold"></i>
                                <span>Filter Periode Laporan</span>
                            </div>
                            <div class="flex items-center gap-1.5">
                                <button @click="setQuickPeriod('all')" :class="activeQuickPeriod === 'all' ? 'bg-wisma-navy text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'" class="px-3 py-1.5 rounded-xl text-[10px] font-bold transition-all">Semua Waktu</button>
                                <button @click="setQuickPeriod('this_month')" :class="activeQuickPeriod === 'this_month' ? 'bg-wisma-navy text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'" class="px-3 py-1.5 rounded-xl text-[10px] font-bold transition-all">Bulan Ini</button>
                                <button @click="setQuickPeriod('last_month')" :class="activeQuickPeriod === 'last_month' ? 'bg-wisma-navy text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'" class="px-3 py-1.5 rounded-xl text-[10px] font-bold transition-all">Bulan Lalu</button>
                                <button @click="setQuickPeriod('this_year')" :class="activeQuickPeriod === 'this_year' ? 'bg-wisma-navy text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'" class="px-3 py-1.5 rounded-xl text-[10px] font-bold transition-all">Tahun Ini</button>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <div class="space-y-1">
                                <label class="block text-[10px] text-slate-400 font-bold uppercase">Tanggal Mulai</label>
                                <input type="date" x-model="reportStartDate" @change="activeQuickPeriod = 'custom'" class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl p-2.5 focus:bg-white focus:ring-1 focus:ring-wisma-gold focus:outline-none transition-all">
                            </div>
                            <div class="space-y-1">
                                <label class="block text-[10px] text-slate-400 font-bold uppercase">Tanggal Selesai</label>
                                <input type="date" x-model="reportEndDate" @change="activeQuickPeriod = 'custom'" class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl p-2.5 focus:bg-white focus:ring-1 focus:ring-wisma-gold focus:outline-none transition-all">
                            </div>
                            <div class="space-y-1">
                                <label class="block text-[10px] text-slate-400 font-bold uppercase">Pencarian Tamu</label>
                                <div class="relative w-full">
                                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                                        <i data-lucide="search" class="w-3.5 h-3.5"></i>
                                    </span>
                                    <input type="text" x-model.debounce.500ms="reportGuestSearch" placeholder="Nama atau NIP..." class="w-full pl-9 pr-4 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-1 focus:ring-wisma-gold focus:outline-none transition-all">
                                </div>
                            </div>
                            <div class="space-y-1">
                                <label class="block text-[10px] text-slate-400 font-bold uppercase">Status Unit</label>
                                <select x-model="reportGuestStatus" class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl p-2.5 focus:bg-white focus:ring-1 focus:ring-wisma-gold focus:outline-none transition-all">
                                    <option value="semua">Semua Status</option>
                                    <option value="Lunas">Lunas</option>
                                    <option value="Check In">Menginap (Aktif)</option>
                                    <option value="Selesai">Selesai</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Print-only Title Header -->
                    <div class="hidden print:block text-center border-b border-slate-800 pb-4 mb-6">
                        <h2 class="text-xl font-bold font-outfit uppercase tracking-wider">LAPORAN OKUPANSI & REKAPITULASI PENGGUNAAN WISMA</h2>
                        <p class="text-xs text-slate-600">Sistem Informasi & Manajemen Wisma DPR RI Kopo</p>
                        <p class="text-xs text-slate-800 mt-1 font-semibold">
                            Periode: <span x-text="reportStartDate ? formatIndoDate(reportStartDate) : 'Awal'"></span> s/d <span x-text="reportEndDate ? formatIndoDate(reportEndDate) : 'Akhir'"></span>
                        </p>
                        <p class="text-[10px] text-slate-500 mt-1" x-text="'Dicetak pada: ' + new Date().toLocaleDateString('id-ID', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' })"></p>
                    </div>



                    <!-- Rekapitulasi Pernah Menginap (Kamar) -->
                    <div class="bg-white border border-slate-100 rounded-3xl p-6 shadow-sm space-y-4 printable-report">
                        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                            <div>
                                <h3 class="text-sm font-bold text-slate-900">Rekapitulasi Tamu Pernah Menginap (Kamar)</h3>
                                <p class="text-[11px] text-slate-500">Daftar riwayat tamu yang sudah pernah menginap atau sedang aktif menginap.</p>
                            </div>
                            <span class="px-2.5 py-1 bg-[#0B1A30] text-wisma-gold text-[10px] font-bold rounded-xl shadow-sm no-print" x-text="(reportFinancialData?.transaksi || []).filter(b => !(b.unit_name.toLowerCase().includes('rapat') || b.unit_name.toLowerCase().includes('hall') || b.unit_name.toLowerCase().includes('auditorium'))).length + ' Tamu'"></span>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse text-xs">
                                <thead>
                                    <tr class="border-b border-slate-100 text-[10px] text-slate-400 font-bold uppercase tracking-wider">
                                        <th class="py-3 px-4">No. | Kode</th>
                                        <th class="py-3 px-4">Nama Tamu & NIP</th>
                                        <th class="py-3 px-4">Unit Kamar</th>
                                        <th class="py-3 px-4">Tanggal Menginap</th>
                                        <th class="py-3 px-4">Durasi</th>
                                        <th class="py-3 px-4">Total Tagihan</th>
                                        <th class="py-3 px-4 text-right">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <template x-for="(b, idx) in (reportFinancialData?.transaksi || []).filter(b => !(b.unit_name.toLowerCase().includes('rapat') || b.unit_name.toLowerCase().includes('hall') || b.unit_name.toLowerCase().includes('auditorium')))" :key="b.id">
                                        <tr class="border-b border-slate-50 hover:bg-slate-50/50 transition-colors">
                                            <td class="py-3 px-4">
                                                <p class="font-bold text-slate-900" x-text="idx + 1"></p>
                                                <p class="text-[9px] text-slate-400 mt-0.5" x-text="b.booking_code"></p>
                                            </td>
                                            <td class="py-3 px-4">
                                                <p class="font-bold text-slate-800" x-text="b.nama"></p>
                                                <p class="text-[10px] text-slate-400 mt-0.5" x-text="'NIP: ' + b.nip"></p>
                                            </td>
                                            <td class="py-3 px-4">
                                                <p class="font-bold text-slate-800" x-text="b.unit_name"></p>
                                                <p class="text-[9px] text-slate-400 mt-0.5" x-text="b.unit_location"></p>
                                            </td>
                                            <td class="py-3 px-4" x-text="formatIndoDate(b.check_in) + ' s/d ' + formatIndoDate(b.check_out)"></td>
                                            <td class="py-3 px-4" x-text="b.nights + ' Malam'"></td>
                                            <td class="py-3 px-4 font-semibold text-wisma-gold" x-text="formatRupiah(b.total_price)"></td>
                                            <td class="py-3 px-4 text-right">
                                                <span class="px-2 py-0.5 rounded text-[9px] uppercase font-bold"
                                                      :class="b.status === 'Check In' ? 'bg-blue-100 text-blue-700' : (b.status === 'Lunas' ? 'bg-wisma-gold/20 text-wisma-navy' : 'bg-emerald-100 text-emerald-700')" x-text="b.status === 'Check In' ? 'Aktif Menginap' : (b.status === 'Lunas' ? 'Lunas' : 'Selesai')"></span>
                                            </td>
                                        </tr>
                                    </template>
                                    <tr x-show="(reportFinancialData?.transaksi || []).filter(b => !(b.unit_name.toLowerCase().includes('rapat') || b.unit_name.toLowerCase().includes('hall') || b.unit_name.toLowerCase().includes('auditorium'))).length === 0">
                                        <td colspan="7" class="text-center py-6 text-slate-400">Tidak ada riwayat menginap kamar.</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Rekapitulasi Riwayat Ruang Rapat -->
                    <div class="bg-white border border-slate-100 rounded-3xl p-6 shadow-sm space-y-4 printable-report page-break-inside-avoid">
                        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                            <div>
                                <h3 class="text-sm font-bold text-slate-900">Rekapitulasi Pemesanan Ruang Rapat</h3>
                                <p class="text-[11px] text-slate-500">Daftar riwayat pemesanan unit ruang rapat atau aula pertemuan.</p>
                            </div>
                            <span class="px-2.5 py-1 bg-[#0B1A30] text-wisma-gold text-[10px] font-bold rounded-xl shadow-sm no-print" x-text="bookings.filter(b => {
                                const isRapat = b.unit_name.toLowerCase().includes('rapat') || b.unit_name.toLowerCase().includes('hall') || b.unit_name.toLowerCase().includes('auditorium');
                                const matchesActive = b.status === 'Selesai' || b.status === 'Check In' || b.status === 'Lunas';
                                const matchesSearch = (b.nama || '').toLowerCase().includes(reportGuestSearch.toLowerCase()) || (b.nip || '').toLowerCase().includes(reportGuestSearch.toLowerCase());
                                const matchesStatus = reportGuestStatus === 'semua' || b.status === reportGuestStatus;
                                                                                                return isRapat && matchesActive && matchesSearch && matchesStatus;
                            }).length + ' Ruangan'"></span>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse text-xs">
                                <thead>
                                    <tr class="border-b border-slate-100 text-[10px] text-slate-400 font-bold uppercase tracking-wider">
                                        <th class="py-3 px-4">No. | Kode</th>
                                        <th class="py-3 px-4">Nama Pemesan & NIP</th>
                                        <th class="py-3 px-4">Ruang Rapat</th>
                                        <th class="py-3 px-4">Tanggal Penggunaan</th>
                                        <th class="py-3 px-4">Durasi</th>
                                        <th class="py-3 px-4">Total Tagihan</th>
                                        <th class="py-3 px-4 text-right">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <template x-for="(b, idx) in (reportFinancialData?.transaksi || []).filter(b => b.unit_name.toLowerCase().includes('rapat') || b.unit_name.toLowerCase().includes('hall') || b.unit_name.toLowerCase().includes('auditorium'))" :key="b.id">
                                        <tr class="border-b border-slate-50 hover:bg-slate-50/50 transition-colors">
                                            <td class="py-3 px-4">
                                                <p class="font-bold text-slate-900" x-text="idx + 1"></p>
                                                <p class="text-[9px] text-slate-400 mt-0.5" x-text="b.booking_code"></p>
                                            </td>
                                            <td class="py-3 px-4">
                                                <p class="font-bold text-slate-800" x-text="b.nama"></p>
                                                <p class="text-[10px] text-slate-400 mt-0.5" x-text="'NIP: ' + b.nip"></p>
                                            </td>
                                            <td class="py-3 px-4">
                                                <p class="font-bold text-slate-800" x-text="b.unit_name"></p>
                                                <p class="text-[9px] text-slate-400 mt-0.5" x-text="b.unit_location"></p>
                                            </td>
                                            <td class="py-3 px-4" x-text="formatIndoDate(b.check_in) + ' s/d ' + formatIndoDate(b.check_out)"></td>
                                            <td class="py-3 px-4" x-text="b.nights + ' Hari'"></td>
                                            <td class="py-3 px-4 font-semibold text-wisma-gold" x-text="formatRupiah(b.total_price)"></td>
                                            <td class="py-3 px-4 text-right">
                                                <span class="px-2 py-0.5 rounded text-[9px] uppercase font-bold"
                                                      :class="b.status === 'Check In' ? 'bg-blue-100 text-blue-700' : (b.status === 'Lunas' ? 'bg-wisma-gold/20 text-wisma-navy' : 'bg-emerald-100 text-emerald-700')"
                                                      x-text="b.status === 'Check In' ? 'Aktif Menginap' : (b.status === 'Lunas' ? 'Lunas' : 'Selesai')"></span>
                                            </td>
                                        </tr>
                                    </template>
                                    <tr x-show="(reportFinancialData?.transaksi || []).filter(b => b.unit_name.toLowerCase().includes('rapat') || b.unit_name.toLowerCase().includes('hall') || b.unit_name.toLowerCase().includes('auditorium')).length === 0">
                                        <td colspan="7" class="text-center py-6 text-slate-400">Tidak ada riwayat pemesanan ruang rapat.</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Signature Area for Printing -->
                        <div class="hidden print:grid grid-cols-2 gap-8 pt-12 text-xs text-left">
                            <div></div>
                            <div class="text-center space-y-12">
                                <div>
                                    <p>Mengetahui,</p>
                                    <p class="font-bold">Administrator Wisma DPR RI</p>
                                </div>
                                <div>
                                    <p class="font-bold underline" x-text="profile.nama"></p>
                                    <p class="text-[10px] text-slate-500">NIP. 198510122010031004</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- SETTINGS VIEW -->