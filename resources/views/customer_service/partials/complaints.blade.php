<div x-show="currentTab === 'cs_complaints'" class="space-y-6 fade-in" x-cloak>
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                        <div>
                            <h1 class="text-2xl font-outfit font-extrabold text-slate-900">Manajemen Keluhan & Hubungan Tamu</h1>
                            <p class="text-xs text-slate-500">Monitor laporan kerusakan, keluhan fasilitas, dan update status penanganan secara real-time.</p>
                        </div>
                        <button @click="openNewComplaintModal()" class="px-5 py-2.5 bg-wisma-navy hover:bg-slate-800 text-white font-bold text-xs rounded-xl shadow-lg transition-all flex items-center gap-1.5">
                            <i data-lucide="plus" class="w-4 h-4"></i> Input Keluhan Baru
                        </button>
                    </div>

                    <!-- Search and filters -->
                    <div class="bg-white p-4 rounded-2xl border border-slate-100 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
                        <div class="relative w-80">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                                <i data-lucide="search" class="w-4 h-4"></i>
                            </span>
                            <input type="text" 
                                   x-model.debounce.500ms="complaintSearch" 
                                   @input="setTimeout(() => { if (window.lucide) window.lucide.createIcons(); }, 600)"
                                   placeholder="Cari keluhan atau lokasi..." 
                                   class="w-full pl-9 pr-4 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-1 focus:ring-wisma-gold focus:outline-none transition-all">
                        </div>
                        <div class="flex gap-2 flex-wrap">
                            <button @click="complaintFilterTab = 'semua'; setTimeout(() => { if (window.lucide) window.lucide.createIcons(); }, 50);" :class="complaintFilterTab === 'semua' ? 'bg-wisma-navy text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'" class="px-4 py-2 rounded-xl text-xs font-bold transition-all">Semua</button>
                            <button @click="complaintFilterTab = 'Pending'; setTimeout(() => { if (window.lucide) window.lucide.createIcons(); }, 50);" :class="complaintFilterTab === 'Pending' ? 'bg-wisma-navy text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'" class="px-4 py-2 rounded-xl text-xs font-bold transition-all">Menunggu (Pending)</button>
                            <button @click="complaintFilterTab = 'Processed'; setTimeout(() => { if (window.lucide) window.lucide.createIcons(); }, 50);" :class="complaintFilterTab === 'Processed' ? 'bg-wisma-navy text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'" class="px-4 py-2 rounded-xl text-xs font-bold transition-all">Diproses</button>
                            <button @click="complaintFilterTab = 'NeedConfirmation'; setTimeout(() => { if (window.lucide) window.lucide.createIcons(); }, 50);" :class="complaintFilterTab === 'NeedConfirmation' ? 'bg-wisma-navy text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'" class="px-4 py-2 rounded-xl text-xs font-bold transition-all">Menunggu Konfirmasi</button>
                            <button @click="complaintFilterTab = 'Resolved'; setTimeout(() => { if (window.lucide) window.lucide.createIcons(); }, 50);" :class="complaintFilterTab === 'Resolved' ? 'bg-wisma-navy text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'" class="px-4 py-2 rounded-xl text-xs font-bold transition-all">Selesai</button>
                        </div>
                    </div>

                    <!-- Complaints Cards Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <template x-for="c in filteredComplaints()" :key="c.id">
                            <div class="bg-white rounded-3xl border border-slate-100 p-6 shadow-sm hover:shadow-md transition-shadow flex flex-col justify-between space-y-4">
                                <div class="space-y-3">
                                    <div class="flex justify-between items-start">
                                        <span class="px-2 py-0.5 bg-red-50 text-red-600 rounded text-[9px] uppercase font-bold tracking-wide" x-text="c.id"></span>
                                        <span class="px-2 py-0.5 rounded text-[9px] uppercase font-bold tracking-wide"
                                              :class="{
                                                  'bg-red-100 text-red-700': c.status === 'Pending',
                                                  'bg-blue-100 text-blue-700': c.status === 'Processed',
                                                  'bg-amber-100 text-amber-700': c.status === 'NeedConfirmation',
                                                              'bg-emerald-100 text-emerald-700': c.status === 'Resolved'
                                              }"
                                              x-text="c.status === 'Pending' ? 'Menunggu' : (c.status === 'Processed' ? 'Diproses' : (c.status === 'NeedConfirmation' ? 'Menunggu Konf.' : 'Selesai'))"></span>
                                    </div>
                                    <div>
                                        <h4 class="text-sm font-bold text-slate-900 font-outfit" x-text="c.title"></h4>
                                        <p class="text-xs text-slate-500 font-light mt-1.5 leading-relaxed" x-text="'Kategori: ' + c.category"></p>
                                        <p class="text-xs text-slate-500 font-medium mt-1 leading-relaxed" x-text="'Lokasi: ' + c.location"></p>
                                    </div>
                                </div>
                                <div class="pt-4 border-t border-slate-50 flex items-center justify-between">
                                    <span class="text-[9px] text-slate-400" x-text="c.date"></span>
                                    <div>
                                        <template x-if="c.status === 'Pending'">
                                            <button @click="processComplaint(c.id)" class="px-3.5 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-xl shadow transition-colors flex items-center gap-1">
                                                <i data-lucide="wrench" class="w-3.5 h-3.5"></i> Tugaskan Tim
                                            </button>
                                        </template>
                                        <template x-if="c.status === 'Processed'">
                                            <button @click="resolveComplaint(c.id)" class="px-3.5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl shadow transition-colors flex items-center gap-1">
                                                <i data-lucide="check" class="w-3.5 h-3.5"></i> Selesaikan
                                            </button>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                    <div x-show="filteredComplaints().length === 0" class="text-center py-12 text-slate-400">
                        <i data-lucide="alert-circle" class="w-12 h-12 mx-auto mb-2 text-slate-200"></i>
                        <p class="text-xs">Tidak ada keluhan dengan kriteria ini.</p>
                    </div>
                </div>

                <!-- 3. REPORTS & REVIEWS VIEW -->