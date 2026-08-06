<!-- INPUT COMPLAINT MODAL -->
    <div x-show="inputComplaintModalOpen" class="fixed inset-0 z-[100] flex items-center justify-center overflow-hidden" x-cloak>
        <div @click="inputComplaintModalOpen = false" class="absolute inset-0 bg-slate-950/60 backdrop-blur-sm transition-opacity"></div>
        <div class="bg-white rounded-3xl shadow-2xl p-8 max-w-md w-full relative z-10 space-y-6 transform scale-100 transition-all fade-in">
            <div class="text-center space-y-2">
                <div class="w-12 h-12 rounded-2xl bg-rose-50 text-rose-500 flex items-center justify-center mx-auto mb-2 shadow-inner">
                    <i data-lucide="alert-triangle" class="w-6 h-6"></i>
                </div>
                <h3 class="text-base font-extrabold text-slate-900 font-outfit">Input Keluhan Masuk</h3>
                <p class="text-[11px] text-slate-500">Catat keluhan yang dilaporkan tamu secara lisan atau telepon.</p>
            </div>

            <form @submit.prevent="saveNewComplaint()" class="space-y-4">
                <div class="space-y-1">
                    <label class="text-[10px] text-slate-500 font-bold uppercase tracking-wide block">Kategori Keluhan</label>
                    <select x-model="newComplaintForm.category" class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl p-3 focus:ring-1 focus:ring-wisma-gold focus:outline-none focus:bg-white transition-all">
                        <option value="facility">Fasilitas (Bungalow, Gedung)</option>
                        <option value="laundry">Layanan Laundry</option>
                        <option value="internet">Internet / Wifi</option>
                        <option value="food">Layanan Makanan</option>
                    </select>
                </div>
                <div class="space-y-1">
                    <label class="text-[10px] text-slate-500 font-bold uppercase tracking-wide block">Lokasi (No. Bungalow / Area)</label>
                    <input type="text" x-model="newComplaintForm.location" placeholder="Contoh: Bungalow Kedondong atau Lobby Wisma" required class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl p-3 focus:ring-1 focus:ring-wisma-gold focus:outline-none focus:bg-white transition-all">
                </div>
                <div class="space-y-1">
                    <label class="text-[10px] text-slate-500 font-bold uppercase tracking-wide block">Nama Pelapor (Tamu)</label>
                    <div x-data="{ open: false, search: '' }" class="relative">
                        <div @click="open = !open" class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl p-3 flex justify-between items-center cursor-pointer hover:border-wisma-gold transition-all" :class="{'ring-1 ring-wisma-gold bg-white': open}">
                            <span x-text="newComplaintForm.userId ? (guests.find(g => g.id == newComplaintForm.userId)?.name || 'Pilih Tamu...') : 'Pilih Tamu...'" :class="{'text-slate-400': !newComplaintForm.userId}"></span>
                            <svg class="w-4 h-4 text-slate-400 transition-transform duration-200" :class="{'rotate-180': open}" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                        </div>
                        
                        <div x-show="open" @click.away="open = false" x-transition.opacity.duration.200ms class="absolute z-50 w-full bg-white border border-slate-200 rounded-xl mt-1 shadow-xl max-h-60 overflow-y-auto overflow-x-hidden flex flex-col" style="display: none;">
                            <div class="p-2 sticky top-0 bg-white border-b border-slate-100 z-10 shadow-sm">
                                <div class="relative">
                                    <svg class="w-3.5 h-3.5 absolute left-2.5 top-1/2 -translate-y-1/2 text-slate-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                                    <input type="text" x-model="search" placeholder="Cari nama atau NIP..." class="w-full text-xs pl-8 pr-3 py-2 bg-slate-50 border border-slate-200 rounded-lg focus:outline-none focus:border-wisma-gold focus:ring-1 focus:ring-wisma-gold" @click.stop>
                                </div>
                            </div>
                            
                            <template x-for="guest in guests.filter(g => g.name.toLowerCase().includes(search.toLowerCase()) || (g.nip && g.nip.toLowerCase().includes(search.toLowerCase())))" :key="guest.id">
                                <div @click="newComplaintForm.userId = guest.id; open = false; search = ''" 
                                     class="px-3 py-2.5 text-xs hover:bg-slate-50 cursor-pointer border-b border-slate-50 last:border-0 transition-colors"
                                     :class="{'bg-wisma-gold/5 text-wisma-gold': newComplaintForm.userId == guest.id}">
                                    <div class="font-bold flex items-center justify-between">
                                        <span x-text="guest.name"></span>
                                        <svg x-show="newComplaintForm.userId == guest.id" class="w-3.5 h-3.5 text-wisma-gold" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                    </div>
                                    <div class="text-[10px] text-slate-500 mt-0.5 flex gap-2">
                                        <span x-text="guest.nip ? 'NIP: ' + guest.nip : 'Tidak ada NIP'"></span>
                                        <span>&bull;</span>
                                        <span x-text="guest.email || 'Tanpa Email'"></span>
                                    </div>
                                </div>
                            </template>
                            
                            <div x-show="guests.length > 0 && guests.filter(g => g.name.toLowerCase().includes(search.toLowerCase()) || (g.nip && g.nip.toLowerCase().includes(search.toLowerCase()))).length === 0" class="p-4 text-center text-xs text-slate-500 flex flex-col items-center justify-center gap-2">
                                <span>Tidak ada tamu yang cocok dengan pencarian.</span>
                            </div>
                            <div x-show="guests.length === 0" class="p-4 text-center text-xs text-slate-500 flex flex-col items-center justify-center gap-2">
                                <span>Memuat atau tidak ada data tamu...</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="space-y-1">
                    <label class="text-[10px] text-slate-500 font-bold uppercase tracking-wide block">Deskripsi Keluhan</label>
                    <textarea x-model="newComplaintForm.description" rows="3" placeholder="Tuliskan kendala secara jelas..." required class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl p-3 focus:ring-1 focus:ring-wisma-gold focus:outline-none focus:bg-white transition-all resize-none"></textarea>
                </div>

                <div class="flex gap-3 pt-2">
                    <button type="button" @click="inputComplaintModalOpen = false" class="flex-1 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-xl transition-colors">
                        Batalkan
                    </button>
                    <button type="submit" class="flex-1 py-2.5 bg-[#0B1A30] hover:bg-slate-800 text-white font-bold text-xs rounded-xl shadow-lg transition-colors">
                        Simpan Keluhan
                    </button>
                </div>
            </form>
        </div>
    </div>
