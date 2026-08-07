<div x-show="currentTab === 'cs_reports'" class="space-y-6 fade-in" x-cloak>
                    <!-- Header -->
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 no-print">
                        <div>
                            <h1 class="text-2xl font-outfit font-extrabold text-slate-900">Laporan Keluhan & Ulasan Tamu</h1>
                            <p class="text-xs text-slate-500">Analisis tingkat kepuasan tamu dan ringkasan keluhan masuk.</p>
                        </div>
                        <div class="flex gap-2">
                            <button @click="printReport()" class="px-5 py-2.5 bg-[#0B1A30] hover:bg-slate-800 text-white font-bold text-xs rounded-xl shadow-lg transition-all flex items-center gap-1.5">
                                <i data-lucide="printer" class="w-4 h-4"></i> Cetak Laporan
                            </button>
                        </div>
                    </div>

                    <!-- Sub-tabs selection -->
                    <div class="border-b border-slate-200 flex gap-6 no-print">
                        <button @click="csReportSubTab = 'keluhan'"
                                :class="csReportSubTab === 'keluhan' ? 'border-wisma-gold text-wisma-gold font-bold' : 'border-transparent text-slate-500 hover:text-slate-900'"
                                class="pb-3 border-b-2 text-xs font-semibold tracking-wide transition-all uppercase">
                            Rekap Keluhan Masuk
                        </button>
                        <button @click="csReportSubTab = 'ulasan'"
                                :class="csReportSubTab === 'ulasan' ? 'border-wisma-gold text-wisma-gold font-bold' : 'border-transparent text-slate-500 hover:text-slate-900'"
                                class="pb-3 border-b-2 text-xs font-semibold tracking-wide transition-all uppercase">
                            Ulasan & Rating Tamu
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
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="space-y-1">
                                <label class="block text-[10px] text-slate-400 font-bold uppercase">Tanggal Mulai</label>
                                <input type="date" x-model="reportStartDate" @change="activeQuickPeriod = 'custom'" class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl p-2.5 focus:bg-white focus:ring-1 focus:ring-wisma-gold focus:outline-none transition-all">
                            </div>
                            <div class="space-y-1">
                                <label class="block text-[10px] text-slate-400 font-bold uppercase">Tanggal Selesai</label>
                                <input type="date" x-model="reportEndDate" @change="activeQuickPeriod = 'custom'" class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl p-2.5 focus:bg-white focus:ring-1 focus:ring-wisma-gold focus:outline-none transition-all">
                            </div>
                        </div>
                    </div>

                    <!-- SUBTAB 1: REKAP KELUHAN MASUK -->
                    <div x-show="csReportSubTab === 'keluhan'" class="space-y-6">
                        <!-- Stats Grid -->
                        <div class="grid grid-cols-2 md:grid-cols-6 gap-4 no-print">
                            <div class="bg-white p-4 rounded-2xl border border-slate-100 shadow-sm">
                                <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block">Total Keluhan</span>
                                <h3 class="text-xl font-bold font-outfit mt-1 text-slate-800" x-text="complaints.filter(c => isDateInPeriod(c.date, reportStartDate, reportEndDate)).length"></h3>
                            </div>
                            <div class="bg-white p-4 rounded-2xl border border-slate-100 shadow-sm">
                                <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block">Menunggu (Pending)</span>
                                <h3 class="text-xl font-bold font-outfit mt-1 text-red-500" x-text="complaints.filter(c => isDateInPeriod(c.date, reportStartDate, reportEndDate) && c.status === 'Pending').length"></h3>
                            </div>
                            <div class="bg-white p-4 rounded-2xl border border-slate-100 shadow-sm">
                                <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block">Sedang Diproses</span>
                                <h3 class="text-xl font-bold font-outfit mt-1 text-blue-500" x-text="complaints.filter(c => isDateInPeriod(c.date, reportStartDate, reportEndDate) && c.status === 'Processed').length"></h3>
                            </div>
                                                        <div class="bg-white p-4 rounded-2xl border border-slate-100 shadow-sm">
                                <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block">Menunggu Konf.</span>
                                <h3 class="text-xl font-bold font-outfit mt-1 text-amber-500" x-text="complaints.filter(c => isDateInPeriod(c.date, reportStartDate, reportEndDate) && c.status === 'NeedConfirmation').length"></h3>
                            </div>
                            <div class="bg-white p-4 rounded-2xl border border-slate-100 shadow-sm">
                                <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block">Selesai (Resolved)</span>
                                <h3 class="text-xl font-bold font-outfit mt-1 text-emerald-500" x-text="complaints.filter(c => isDateInPeriod(c.date, reportStartDate, reportEndDate) && c.status === 'Resolved').length"></h3>
                            </div>
                            <div class="bg-white p-4 rounded-2xl border border-slate-100 shadow-sm col-span-2 md:col-span-1">
                                <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block">Tingkat Resolusi</span>
                                <h3 class="text-xl font-bold font-outfit mt-1 text-amber-600" 
                                    x-text="complaints.length ? Math.round((complaints.filter(c => isDateInPeriod(c.date, reportStartDate, reportEndDate) && c.status === 'Resolved').length / complaints.length) * 100) + '%' : '0%'"></h3>
                            </div>
                        </div>

                        <!-- Print-only Title Header -->
                        <div class="hidden print:block text-center border-b border-slate-800 pb-4 mb-6">
                            <h2 class="text-xl font-bold font-outfit uppercase tracking-wider">LAPORAN REKAPITULASI KELUHAN TAMU</h2>
                            <p class="text-xs text-slate-600">Sistem Pelayanan Wisma DPR RI Kopo</p>
                            <p class="text-[11px] text-slate-800 font-bold mb-1" x-html="'Periode: ' + (reportStartDate ? formatIndoDate(reportStartDate) : 'Awal') + ' s/d ' + (reportEndDate ? formatIndoDate(reportEndDate) : 'Sekarang')"></p>
                            <p class="text-[10px] text-slate-500 mt-1" x-text="'Dicetak pada: ' + new Date().toLocaleDateString('id-ID', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' })"></p>
                        </div>

                        <!-- Filters for Printing & View -->
                        <div class="bg-white p-4 rounded-2xl border border-slate-100 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4 no-print">
                            <div class="flex flex-wrap gap-2">
                                <div class="text-xs text-slate-500 flex items-center pr-2 font-bold uppercase tracking-wide">Filter:</div>
                                <select x-model="reportComplaintFilterCategory" class="text-xs bg-slate-50 border border-slate-200 rounded-lg p-2 focus:ring-1 focus:ring-wisma-gold focus:outline-none">
                                    <option value="semua">Semua Kategori</option>
                                    <option value="facility">Fasilitas (Bungalow, Gedung)</option>
                                    <option value="laundry">Layanan Laundry</option>
                                    <option value="internet">Internet / Wifi</option>
                                    <option value="food">Layanan Makanan</option>
                                </select>
                                <select x-model="reportComplaintFilterStatus" class="text-xs bg-slate-50 border border-slate-200 rounded-lg p-2 focus:ring-1 focus:ring-wisma-gold focus:outline-none">
                                    <option value="semua">Semua Status</option>
                                    <option value="Pending">Menunggu (Pending)</option>
                                    <option value="Processed">Diproses</option>
                                    <option value="NeedConfirmation">Menunggu Konfirmasi</option>
                                    <option value="Resolved">Selesai</option>
                                </select>
                            </div>
                        </div>

                        <!-- Table -->
                        <div class="bg-white border border-slate-100 rounded-3xl p-6 shadow-sm printable-report">
                            <div class="overflow-x-auto">
                                <table class="w-full text-left border-collapse text-xs">
                                    <thead>
                                        <tr class="border-b border-slate-200 text-[10px] text-slate-400 font-bold uppercase tracking-wider">
                                            <th class="py-3 px-4">No. Tiket</th>
                                            <th class="py-3 px-4">Kategori</th>
                                            <th class="py-3 px-4">Lokasi & Pelapor</th>
                                            <th class="py-3 px-4">Deskripsi Keluhan</th>
                                            <th class="py-3 px-4">Tanggal Masuk</th>
                                            <th class="py-3 px-4 text-right">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <template x-for="c in complaints.filter(c => {
                                            const categoryMatch = reportComplaintFilterCategory === 'semua' || c.category === reportComplaintFilterCategory;
                                            const statusMatch = reportComplaintFilterStatus === 'semua' || c.status === reportComplaintFilterStatus;
                                            const dateMatch = isDateInPeriod(c.date, reportStartDate, reportEndDate);
                                            return categoryMatch && statusMatch && dateMatch;
                                        })" :key="c.id">
                                            <tr class="border-b border-slate-100 hover:bg-slate-50/50 transition-colors">
                                                <td class="py-3 px-4 font-bold text-slate-900" x-text="c.id"></td>
                                                <td class="py-3 px-4" x-text="c.category_label"></td>
                                                <td class="py-3 px-4" x-text="c.location"></td>
                                                <td class="py-3 px-4 text-slate-600" x-text="c.title"></td>
                                                <td class="py-3 px-4 text-slate-500" x-text="c.date"></td>
                                                <td class="py-3 px-4 text-right">
                                                    <span class="px-2 py-0.5 rounded text-[9px] uppercase font-bold"
                                                          :class="{
                                                              'bg-red-100 text-red-700': c.status === 'Pending',
                                                              'bg-blue-100 text-blue-700': c.status === 'Processed',
                                                              'bg-amber-100 text-amber-700': c.status === 'NeedConfirmation',
                                                              'bg-emerald-100 text-emerald-700': c.status === 'Resolved'
                                                          }"
                                                          x-text="c.status === 'Pending' ? 'Menunggu' : (c.status === 'Processed' ? 'Diproses' : (c.status === 'NeedConfirmation' ? 'Menunggu Konf.' : 'Selesai'))"></span>
                                                </td>
                                            </tr>
                                        </template>
                                        <tr x-show="complaints.filter(c => {
                                            const categoryMatch = reportComplaintFilterCategory === 'semua' || c.category === reportComplaintFilterCategory;
                                            const statusMatch = reportComplaintFilterStatus === 'semua' || c.status === reportComplaintFilterStatus;
                                            const dateMatch = isDateInPeriod(c.date, reportStartDate, reportEndDate);
                                            return categoryMatch && statusMatch && dateMatch;
                                        }).length === 0">
                                            <td colspan="6" class="text-center py-8 text-slate-400">Tidak ada rekapitulasi keluhan.</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Signature Area for Printing -->
                            <div class="hidden print:grid grid-cols-2 gap-8 pt-12 text-xs">
                                <div></div>
                                <div class="text-center space-y-12">
                                    <div>
                                        <p>Mengetahui,</p>
                                        <p class="font-bold">Customer Service Wisma DPR RI</p>
                                    </div>
                                    <div>
                                        <p class="font-bold underline" x-text="profile.nama"></p>
                                        <p class="text-[10px] text-slate-500">NIP. 199308122018022003</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- SUBTAB 2: ULASAN & RATING TAMU -->
                    <div x-show="csReportSubTab === 'ulasan'" class="space-y-6">
                        <!-- Star Breakdown & Average Card -->
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 no-print">
                            <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm flex flex-col justify-center items-center text-center">
                                <span class="text-xs text-slate-400 font-semibold uppercase tracking-wider block">Rata-rata Rating</span>
                                <h1 class="text-5xl font-extrabold font-outfit text-slate-900 mt-2" 
                                    x-text="filteredFeedbacksAgg.avg_overall ? Number(filteredFeedbacksAgg.avg_overall).toFixed(1) : '0.0'"></h1>
                                <div class="flex justify-center items-center gap-1 mt-3">
                                    <template x-for="star in [1, 2, 3, 4, 5]">
                                        <div class="relative w-6 h-6">
                                            <!-- Background star (Empty) -->
                                            <svg class="absolute inset-0 w-6 h-6 text-slate-100 fill-current drop-shadow-sm" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                            </svg>
                                            <!-- Foreground star (Filled partially) -->
                                            <div class="absolute inset-0 overflow-hidden" 
                                                 :style="'width: ' + (star <= Math.floor(filteredFeedbacksAgg.avg_overall) ? '100%' : (star === Math.floor(filteredFeedbacksAgg.avg_overall) + 1 ? ((filteredFeedbacksAgg.avg_overall % 1) * 100) + '%' : '0%'))">
                                                <svg class="w-6 h-6 text-amber-500 fill-current drop-shadow-sm max-w-none" viewBox="0 0 20 20">
                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                                </svg>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                                <span class="text-[10px] text-slate-400 mt-2" x-text="'Dari ' + filteredFeedbacksAgg.total + ' ulasan tamu'"></span>
                            </div>

                            <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm md:col-span-3 space-y-4">
                                <h4 class="text-xs font-bold text-slate-800 uppercase tracking-wide">Rincian Kepuasan Tamu (Berdasarkan Kategori)</h4>
                                <div class="space-y-3.5 pt-2 text-xs">
                                    <!-- Cleanliness -->
                                    <div class="space-y-1">
                                        <div class="flex justify-between font-semibold text-slate-700">
                                            <span>Kebersihan Bungalow & Gedung</span>
                                            <span class="font-bold text-slate-900" x-text="filteredFeedbacksAgg.avg_cleanliness ? Number(filteredFeedbacksAgg.avg_cleanliness).toFixed(1) + ' / 5.0' : '0.0 / 5.0'"></span>
                                        </div>
                                        <div class="w-full h-2 bg-slate-100 rounded-full overflow-hidden">
                                            <div class="bg-amber-500 h-full rounded-full" 
                                                 :style="'width: ' + (filteredFeedbacksAgg.avg_cleanliness * 20) + '%'"></div>
                                        </div>
                                    </div>
                                    <!-- Facilities -->
                                    <div class="space-y-1">
                                        <div class="flex justify-between font-semibold text-slate-700">
                                            <span>Kualitas Fasilitas & Peralatan</span>
                                            <span class="font-bold text-slate-900" x-text="filteredFeedbacksAgg.avg_facilities ? Number(filteredFeedbacksAgg.avg_facilities).toFixed(1) + ' / 5.0' : '0.0 / 5.0'"></span>
                                        </div>
                                        <div class="w-full h-2 bg-slate-100 rounded-full overflow-hidden">
                                            <div class="bg-amber-500 h-full rounded-full" 
                                                 :style="'width: ' + (filteredFeedbacksAgg.avg_facilities * 20) + '%'"></div>
                                        </div>
                                    </div>
                                    <!-- Service -->
                                    <div class="space-y-1">
                                        <div class="flex justify-between font-semibold text-slate-700">
                                            <span>Keramahan & Kecepatan Pelayanan</span>
                                            <span class="font-bold text-slate-900" x-text="filteredFeedbacksAgg.avg_service ? Number(filteredFeedbacksAgg.avg_service).toFixed(1) + ' / 5.0' : '0.0 / 5.0'"></span>
                                        </div>
                                        <div class="w-full h-2 bg-slate-100 rounded-full overflow-hidden">
                                            <div class="bg-amber-500 h-full rounded-full" 
                                                 :style="'width: ' + (filteredFeedbacksAgg.avg_service * 20) + '%'"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Print-only Title Header -->
                        <div class="hidden print:block text-center border-b border-slate-800 pb-4 mb-6">
                            <h2 class="text-xl font-bold font-outfit uppercase tracking-wider">LAPORAN ULASAN & PENILAIAN TAMU</h2>
                            <p class="text-xs text-slate-600">Sistem Pelayanan Wisma DPR RI Kopo</p>
                            <p class="text-[11px] text-slate-800 font-bold mb-1" x-html="'Periode: ' + (reportStartDate ? formatIndoDate(reportStartDate) : 'Awal') + ' s/d ' + (reportEndDate ? formatIndoDate(reportEndDate) : 'Sekarang')"></p>
                            <p class="text-[10px] text-slate-500 mt-1" x-text="'Dicetak pada: ' + new Date().toLocaleDateString('id-ID', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' })"></p>
                        </div>

                        <!-- Review Filter -->
                        <div class="bg-white p-4 rounded-2xl border border-slate-100 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4 no-print">
                            <div class="flex gap-2">
                                <button @click="reportRatingFilter = 'semua'" :class="reportRatingFilter === 'semua' ? 'bg-wisma-navy text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'" class="px-4 py-2 rounded-xl text-xs font-bold transition-all">Semua Ulasan</button>
                                <button @click="reportRatingFilter = '5'" :class="reportRatingFilter === '5' ? 'bg-wisma-navy text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'" class="px-4 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-1">5 ★</button>
                                <button @click="reportRatingFilter = '4'" :class="reportRatingFilter === '4' ? 'bg-wisma-navy text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'" class="px-4 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-1">4 ★</button>
                                <button @click="reportRatingFilter = '3'" :class="reportRatingFilter === '3' ? 'bg-wisma-navy text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'" class="px-4 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-1">3 Ke Bawah</button>
                            </div>
                        </div>

                        <!-- Feedback List -->
                        <div class="space-y-4 printable-report">
                            <template x-for="f in filteredFeedbacksList" :key="f.id">
                                <div class="group bg-white border border-slate-100/60 rounded-3xl p-6 shadow-sm hover:shadow-xl hover:shadow-wisma-navy/5 transition-all duration-300 flex flex-col gap-5 page-break-inside-avoid">
                                    <!-- Header: Avatar, Info, and Overall Score -->
                                    <div class="flex flex-col md:flex-row justify-between items-start gap-4 border-b border-slate-100 pb-5">
                                        <div class="flex items-start gap-4">
                                            <!-- Initial Avatar -->
                                            <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-slate-100 to-slate-200 flex items-center justify-center flex-shrink-0 text-slate-600 font-bold font-outfit shadow-inner group-hover:scale-105 transition-transform duration-300">
                                                <span class="text-lg" x-text="f.user?.name ? f.user.name.charAt(0).toUpperCase() : '?'"></span>
                                            </div>
                                            <!-- Text Info -->
                                            <div class="space-y-1">
                                                <h4 class="text-base font-bold text-slate-900 font-outfit" x-text="f.user?.name || 'Tamu Wisma'"></h4>
                                                <div class="flex flex-wrap items-center gap-2 text-xs text-slate-500">
                                                    <span class="font-medium bg-slate-100 px-2 py-0.5 rounded-md" x-text="'NIP: ' + (f.user?.nip || '-')"></span>
                                                    <span>&bull;</span>
                                                    <span class="font-medium" x-text="'Menginap di: ' + (f.booking?.facility?.name || '-')"></span>
                                                </div>
                                                <div class="flex items-center gap-2 text-xs text-slate-400 mt-1">
                                                    <i data-lucide="calendar" class="w-3.5 h-3.5"></i>
                                                    <span x-text="'Check-in: ' + (f.booking ? formatIndoDate(f.booking.check_in) : '-')"></span>
                                                    <span>&bull;</span>
                                                    <span x-text="'Ulasan pada: ' + formatIndoDate(f.created_at)"></span>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- Overall Rating Badge -->
                                        <div class="flex flex-col items-end gap-1.5">
                                            <div class="bg-gradient-to-r from-amber-50 to-orange-50 border border-amber-100 px-4 py-2 rounded-2xl flex items-center gap-3 shadow-sm">
                                                <div class="flex flex-col items-end">
                                                    <span class="text-[10px] font-bold text-amber-600 uppercase tracking-wider">Score</span>
                                                    <span class="text-sm font-extrabold text-amber-700 font-outfit" x-text="f.average_rating + ' / 5.0'"></span>
                                                </div>
                                                <div class="h-8 w-px bg-amber-200"></div>
                                                <div class="flex gap-0.5">
                                                    <template x-for="star in [1, 2, 3, 4, 5]">
                                                        <div class="relative w-4 h-4">
                                                            <!-- Background star (Empty) -->
                                                            <svg class="absolute inset-0 w-4 h-4 text-amber-100 fill-current drop-shadow-sm" viewBox="0 0 20 20">
                                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                                            </svg>
                                                            <!-- Foreground star (Filled partially) -->
                                                            <div class="absolute inset-0 overflow-hidden" 
                                                                 :style="'width: ' + (star <= Math.floor(f.average_rating) ? '100%' : (star === Math.floor(f.average_rating) + 1 ? ((f.average_rating % 1) * 100) + '%' : '0%'))">
                                                                <svg class="w-4 h-4 text-amber-500 fill-current drop-shadow-sm max-w-none" viewBox="0 0 20 20">
                                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                                                </svg>
                                                            </div>
                                                        </div>
                                                    </template>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Body: Detail Ratings & Comment -->
                                    <div class="flex flex-col md:flex-row gap-6">
                                        <!-- Detail Ratings -->
                                        <div class="grid grid-cols-1 gap-3 md:w-1/3 border-r border-transparent md:border-slate-100 pr-0 md:pr-4">
                                            <div class="flex items-center justify-between bg-slate-50/80 px-3 py-2 rounded-xl group-hover:bg-blue-50/50 transition-colors">
                                                <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Kebersihan</span>
                                                <div class="flex items-center gap-1.5">
                                                    <span class="font-extrabold text-slate-800 text-xs" x-text="f.rating_cleanliness + '.0'"></span>
                                                    <i data-lucide="star" class="w-3.5 h-3.5 text-amber-500 fill-amber-500"></i>
                                                </div>
                                            </div>
                                            <div class="flex items-center justify-between bg-slate-50/80 px-3 py-2 rounded-xl group-hover:bg-blue-50/50 transition-colors">
                                                <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Fasilitas</span>
                                                <div class="flex items-center gap-1.5">
                                                    <span class="font-extrabold text-slate-800 text-xs" x-text="f.rating_facilities + '.0'"></span>
                                                    <i data-lucide="star" class="w-3.5 h-3.5 text-amber-500 fill-amber-500"></i>
                                                </div>
                                            </div>
                                            <div class="flex items-center justify-between bg-slate-50/80 px-3 py-2 rounded-xl group-hover:bg-blue-50/50 transition-colors">
                                                <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Pelayanan</span>
                                                <div class="flex items-center gap-1.5">
                                                    <span class="font-extrabold text-slate-800 text-xs" x-text="f.rating_service + '.0'"></span>
                                                    <i data-lucide="star" class="w-3.5 h-3.5 text-amber-500 fill-amber-500"></i>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- Comment -->
                                        <div class="flex-1 pl-0 md:pl-2">
                                            <div class="bg-blue-50/40 border border-blue-100/50 rounded-2xl p-4 h-full relative overflow-hidden group-hover:bg-blue-50/80 transition-colors">
                                                <i data-lucide="quote" class="w-12 h-12 absolute -top-2 -left-2 text-blue-500/10"></i>
                                                <p class="text-sm text-slate-600 leading-relaxed relative z-10 font-medium" x-text="f.comment ? '“' + f.comment + '”' : 'Tidak ada ulasan tertulis.'"></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </template>

                            <div x-show="filteredFeedbacksList.length === 0" class="bg-white border border-slate-100 rounded-3xl p-8 text-center text-slate-400">
                                <i data-lucide="message-square" class="w-12 h-12 mx-auto mb-2 text-slate-200"></i>
                                <p class="text-xs">Tidak ada ulasan rating dengan kriteria filter ini.</p>
                            </div>

                            <!-- Signature Area for Printing -->
                            <div class="hidden print:grid grid-cols-2 gap-8 pt-12 text-xs">
                                <div></div>
                                <div class="text-center space-y-12">
                                    <div>
                                        <p>Mengetahui,</p>
                                        <p class="font-bold">Customer Service Wisma DPR RI</p>
                                    </div>
                                    <div>
                                        <p class="font-bold underline" x-text="profile.nama"></p>
                                        <p class="text-[10px] text-slate-500">NIP. 199308122018022003</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>