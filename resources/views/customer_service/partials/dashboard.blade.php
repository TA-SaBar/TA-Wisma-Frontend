<div x-show="currentTab === 'cs_dashboard'" class="space-y-8 fade-in">
                    <!-- Welcome Banner -->
                    <div class="relative overflow-hidden bg-gradient-to-r from-wisma-navy to-slate-900 text-white rounded-3xl p-8 shadow-xl">
                        <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_top_right,_var(--tw-gradient-stops))] from-amber-500/20 via-transparent to-transparent"></div>
                        <div class="relative z-10 max-w-xl">
                            <span class="px-3 py-1 bg-amber-500/20 text-wisma-gold text-[10px] uppercase font-bold tracking-widest rounded-full border border-wisma-gold/30">Dashboard Penanganan Keluhan</span>
                            <h1 class="text-3xl font-outfit font-extrabold mt-4 mb-2 tracking-tight" x-text="'Selamat Bertugas, ' + profile.nama"></h1>
                            <p class="text-xs text-slate-300 leading-relaxed font-light">
                                Sistem Monitoring & Resolusi Keluhan Tamu. Catat keluhan baru, tugaskan tim teknis dengan sigap, dan selesaikan masalah untuk kenyamanan tamu wisma.
                            </p>
                            <div class="mt-6 flex gap-3">
                                <button @click="switchTab('cs_complaints')" class="px-5 py-2.5 bg-wisma-gold hover:bg-wisma-goldHover text-wisma-dark font-semibold text-xs rounded-xl shadow-lg transition-all flex items-center gap-1">
                                    <i data-lucide="alert-triangle" class="w-4 h-4"></i> Layani & Proses Keluhan
                                </button>
                                <button @click="openNewComplaintModal()" class="px-5 py-2.5 bg-white/10 hover:bg-white/20 text-white border border-white/10 font-semibold text-xs rounded-xl transition-all flex items-center gap-1">
                                    <i data-lucide="plus" class="w-4 h-4"></i> Input Keluhan Baru
                                </button>
                            </div>
                        </div>
                        <div class="absolute right-10 bottom-0 top-0 hidden lg:flex items-center text-white/5 pointer-events-none select-none">
                            <i data-lucide="alert-triangle" class="w-64 h-64"></i>
                        </div>
                    </div>

                    <!-- Stats Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                        <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex items-center justify-between group hover:shadow-md transition-shadow">
                            <div>
                                <span class="text-xs text-slate-500 font-medium">Keluhan Menunggu (Pending)</span>
                                <h3 class="text-2xl font-bold font-outfit mt-1 text-red-500" x-text="complaints.filter(c => c.status === 'Pending').length"></h3>
                            </div>
                            <div class="w-12 h-12 rounded-xl bg-red-50 text-red-500 flex items-center justify-center transition-transform group-hover:scale-110">
                                <i data-lucide="bell" class="w-6 h-6"></i>
                            </div>
                        </div>
                        <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex items-center justify-between group hover:shadow-md transition-shadow">
                            <div>
                                <span class="text-xs text-slate-500 font-medium">Keluhan Sedang Diproses</span>
                                <h3 class="text-2xl font-bold font-outfit mt-1 text-blue-500" x-text="complaints.filter(c => c.status === 'Processed').length"></h3>
                            </div>
                            <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-500 flex items-center justify-center transition-transform group-hover:scale-110">
                                <i data-lucide="wrench" class="w-6 h-6"></i>
                            </div>
                        </div>
                                                <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex items-center justify-between group hover:shadow-md transition-shadow">
                            <div>
                                <span class="text-xs text-slate-500 font-medium">Menunggu Konf. Tamu</span>
                                <h3 class="text-2xl font-bold font-outfit mt-1 text-amber-500" x-text="complaints.filter(c => c.status === 'NeedConfirmation').length"></h3>
                            </div>
                            <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-500 flex items-center justify-center transition-transform group-hover:scale-110">
                                <i data-lucide="user-check" class="w-6 h-6"></i>
                            </div>
                        </div>
                        <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex items-center justify-between group hover:shadow-md transition-shadow">
                            <div>
                                <span class="text-xs text-slate-500 font-medium">Keluhan Selesai (Resolved)</span>
                                <h3 class="text-2xl font-bold font-outfit mt-1 text-emerald-500" x-text="complaints.filter(c => c.status === 'Resolved').length"></h3>
                            </div>
                            <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-500 flex items-center justify-center transition-transform group-hover:scale-110">
                                <i data-lucide="check-circle" class="w-6 h-6"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Complaints Dashboard List -->
                    <div class="bg-white rounded-3xl border border-slate-100 shadow-sm p-6">
                        <div class="flex items-center justify-between mb-6">
                            <div>
                                <h2 class="text-base font-bold text-slate-900">Keluhan Tamu Terbaru</h2>
                                <p class="text-xs text-slate-500">Daftar keluhan masuk yang memerlukan respon penanganan segera.</p>
                            </div>
                            <button @click="switchTab('cs_complaints')" class="text-xs text-amber-600 font-semibold hover:underline flex items-center gap-1">
                                Kelola Seluruh Keluhan <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
                            </button>
                        </div>

                        <div class="divide-y divide-slate-100">
                            <template x-for="c in complaints.filter(comp => comp.status !== 'Resolved' && comp.status !== 'NeedConfirmation').slice(0, 5)" :key="c.id">
                                <div class="py-4 flex items-center justify-between">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-xl bg-red-50 text-red-500 flex items-center justify-center shrink-0">
                                            <i data-lucide="alert-circle" class="w-5 h-5"></i>
                                        </div>
                                        <div>
                                            <div class="flex items-center gap-2">
                                                <h4 class="text-xs font-bold text-slate-900" x-text="c.title"></h4>
                                                <span class="px-2 py-0.5 rounded text-[8px] uppercase font-bold tracking-wide"
                                                      :class="{
                                                          'bg-red-100 text-red-700': c.status === 'Pending',
                                                          'bg-blue-100 text-blue-700': c.status === 'Processed',
                                                          'bg-amber-100 text-amber-700': c.status === 'NeedConfirmation',
                                                              'bg-emerald-100 text-emerald-700': c.status === 'Resolved'
                                                      }"
                                                      x-text="c.status === 'Pending' ? 'Menunggu' : (c.status === 'Processed' ? 'Proses' : 'Selesai')"></span>
                                            </div>
                                            <p class="text-[10px] text-slate-400 mt-0.5" x-text="c.category + ' • Lokasi: ' + c.location + ' • ' + c.date"></p>
                                        </div>
                                    </div>
                                    <div>
                                        <template x-if="c.status === 'Pending'">
                                            <button @click="processComplaint(c.id)" class="px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white font-bold text-[10px] rounded-lg shadow transition-colors flex items-center gap-1">
                                                <i data-lucide="wrench" class="w-3.5 h-3.5"></i> Tugaskan Tim
                                            </button>
                                        </template>
                                        <template x-if="c.status === 'Processed'">
                                            <button @click="resolveComplaint(c.id)" class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-[10px] rounded-lg shadow transition-colors flex items-center gap-1">
                                                <i data-lucide="check" class="w-3.5 h-3.5"></i> Selesaikan
                                            </button>
                                        </template>
                                    </div>
                                </div>
                            </template>
                            <div x-show="complaints.length === 0" class="text-center py-6 text-slate-400">
                                <i data-lucide="smile" class="w-10 h-10 mx-auto mb-2 text-slate-200"></i>
                                <p class="text-xs">Hebat! Tidak ada keluhan aktif dari tamu saat ini.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 2. COMPLAINTS MANAGEMENT -->