<div x-show="currentTab === 'help'" class="space-y-6 fade-in" x-cloak>
                    <div>
                        <h1 class="text-2xl font-outfit font-extrabold text-slate-900">Keluhan & Bantuan Layanan</h1>
                        <p class="text-xs text-slate-500">Laporkan kendala fasilitas selama Anda menginap untuk penanganan cepat.</p>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                        <!-- Submit Complaint Form -->
                        <div class="bg-white border border-slate-100 rounded-3xl p-6 shadow-sm space-y-6">
                            <h3 class="text-sm font-bold text-slate-900">Buat Tiket Keluhan Baru</h3>
                            <form @submit.prevent="submitComplaint()" class="space-y-4">
                                <div class="space-y-1">
                                    <label class="text-[10px] text-slate-500 font-bold uppercase tracking-wide block">Kategori Keluhan</label>
                                    <select x-model="complaintForm.category" class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl p-3 focus:ring-1 focus:ring-wisma-gold focus:outline-none focus:bg-white transition-all">
                                        <option value="facility">Fasilitas (Kamar, Gedung)</option>
                                        <option value="laundry">Layanan Laundry</option>
                                        <option value="internet">Internet / Wifi</option>
                                        <option value="food">Layanan Makanan</option>
                                    </select>
                                </div>
                                <div class="space-y-1">
                                    <label class="text-[10px] text-slate-500 font-bold uppercase tracking-wide block">Lokasi (No. Kamar / Area)</label>
                                    <input type="text" x-model="complaintForm.location" placeholder="Contoh: Bungalow Semangka atau Ruang Panja" required class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl p-3 focus:ring-1 focus:ring-wisma-gold focus:outline-none focus:bg-white transition-all">
                                </div>
                                <div class="space-y-1">
                                    <label class="text-[10px] text-slate-500 font-bold uppercase tracking-wide block">Penjelasan Kendala</label>
                                    <textarea x-model="complaintForm.description" rows="4" placeholder="Jelaskan secara detail keluhan Anda (misal: AC bocor air, Wifi terputus)..." required class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl p-3 focus:ring-1 focus:ring-wisma-gold focus:outline-none focus:bg-white transition-all resize-none"></textarea>
                                </div>
                                <button type="submit" class="w-full py-3 bg-red-600 hover:bg-red-700 text-white font-bold text-xs rounded-xl shadow-lg transition-colors flex items-center justify-center gap-1.5">
                                    <i data-lucide="alert-triangle" class="w-4 h-4"></i> Kirim Laporan Keluhan
                                </button>
                            </form>
                        </div>

                        <!-- Active Complaints Ticket List -->
                        <div class="bg-white border border-slate-100 rounded-3xl p-6 shadow-sm lg:col-span-2 space-y-4">
                            <h3 class="text-sm font-bold text-slate-900">Status Tiket Keluhan Anda</h3>
                            
                            <div class="divide-y divide-slate-100">
                                <template x-for="c in complaints" :key="c.id">
                                    <div class="py-4 flex items-center justify-between hover:bg-slate-50/50 rounded-xl px-2 transition-colors">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-xl bg-red-50 text-red-500 flex items-center justify-center">
                                                <i data-lucide="alert-circle" class="w-5 h-5"></i>
                                            </div>
                                            <div>
                                                <div class="flex items-center gap-2">
                                                    <span class="px-2 py-0.5 bg-slate-100 text-slate-600 rounded text-[9px] uppercase font-bold tracking-wide" x-text="c.id"></span>
                                                    <h4 class="text-xs font-bold text-slate-900" x-text="c.title"></h4>
                                                </div>
                                                <p class="text-[10px] text-slate-400 mt-1" x-text="c.category + ' • ' + c.location"></p>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <span class="px-2 py-0.5 rounded text-[9px] uppercase font-bold tracking-wide"
                                                  :class="{
                                                      'bg-red-100 text-red-700': c.status === 'Pending',
                                                      'bg-blue-100 text-blue-700': c.status === 'Processed',
                                                      'bg-amber-100 text-amber-700': c.status === 'NeedConfirmation',
                                                      'bg-emerald-100 text-emerald-700': c.status === 'Resolved'
                                                  }"
                                                  x-text="c.status === 'Pending' ? 'Menunggu' : (c.status === 'Processed' ? 'Diproses' : (c.status === 'NeedConfirmation' ? 'Konfirmasi' : 'Selesai'))"></span>
                                            <span class="text-[9px] text-slate-400 block mt-1" x-text="c.date"></span>
                                            <template x-if="c.status === 'NeedConfirmation'">
                                                <button @click="confirmComplaint(c.db_id)" class="mt-1.5 block w-full text-[9px] font-bold bg-wisma-gold hover:bg-amber-600 text-white px-2 py-1 rounded transition-colors shadow">
                                                    Konfirmasi Selesai
                                                </button>
                                            </template>

                                        </div>
                                    </div>
                                </template>
                                <div x-show="complaints.length === 0" class="text-center py-12 text-slate-400">
                                    <i data-lucide="check-circle-2" class="w-12 h-12 mx-auto mb-2 text-slate-300"></i>
                                    <p class="text-xs">Tidak ada keluhan aktif dari Anda saat ini.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>