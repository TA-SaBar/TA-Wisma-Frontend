<!-- CRUD CREATION & MODIFICATION MODAL -->
    <div x-show="crudModalOpen" class="fixed inset-0 z-[100] flex items-center justify-center overflow-hidden" x-cloak>
        <div @click="crudModalOpen = false" class="absolute inset-0 bg-slate-950/60 backdrop-blur-sm transition-opacity"></div>
        <div class="bg-white rounded-3xl shadow-2xl p-8 max-w-2xl w-full relative z-10 space-y-6 transform scale-100 transition-all fade-in">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center">
                        <i data-lucide="layout" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-extrabold text-slate-900 font-outfit" x-text="crudAction === 'create' ? 'Tambah Inventaris Baru' : 'Edit Inventaris Unit'"></h3 >
                        <p class="text-[10px] text-slate-500 font-medium" x-text="'Formulir isian data ' + crudType"></p>
                    </div>
                </div>
                <button @click="crudModalOpen = false" class="text-slate-400 hover:text-slate-600">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <form @submit.prevent="saveCrudItem()" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Name -->
                    <div class="space-y-1 md:col-span-2">
                        <label class="text-[10px] text-slate-500 font-bold uppercase tracking-wide block">Nama Unit Fasilitas</label>
                        <input type="text" x-model="crudForm.name" required placeholder="Contoh: Bungalow Semangka / Ruang Panja" class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl p-3 focus:ring-1 focus:ring-wisma-gold focus:bg-white focus:outline-none transition-all">
                    </div>

                    <!-- Area -->
                    <div class="space-y-1">
                        <label class="text-[10px] text-slate-500 font-bold uppercase tracking-wide block">Area</label>
                        <select x-model="crudForm.area" class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl p-3 focus:ring-1 focus:ring-wisma-gold focus:bg-white focus:outline-none transition-all">
                            <option value="Area Bawah">Area Bawah</option>
                            <option value="Area Atas">Area Atas</option>
                        </select>
                    </div>

                    <!-- Harga -->
                    <div class="space-y-1">
                        <label class="text-[10px] text-slate-500 font-bold uppercase tracking-wide block">Harga / Tarif (Rp)</label>
                        <input type="number" x-model="crudForm.price" required class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl p-3 focus:ring-1 focus:ring-wisma-gold focus:bg-white focus:outline-none transition-all">
                    </div>


                    <!-- Bed Configuration -->
                    <div class="space-y-1">
                        <label class="text-[10px] text-slate-500 font-bold uppercase tracking-wide block" x-text="(crudType === 'Buah' || crudType === 'Bunga') ? 'Tipe Tempat Tidur' : 'Konfigurasi Rapat'"></label>
                        <input type="text" x-model="crudForm.bed" placeholder="Contoh: Queen Size atau Twin Bed" class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl p-3 focus:ring-1 focus:ring-wisma-gold focus:bg-white focus:outline-none transition-all">
                    </div>

                    <!-- Status -->
                    <div class="space-y-1">
                        <label class="text-[10px] text-slate-500 font-bold uppercase tracking-wide block">Status Awal</label>
                        <select x-model="crudForm.status" class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl p-3 focus:ring-1 focus:ring-wisma-gold focus:bg-white focus:outline-none transition-all">
                            <option value="READY">Tersedia (READY)</option>
                            <option value="CLEANING">Pembersihan (CLEANING)</option>
                            <option value="MAINTENANCE">Perbaikan (MAINTENANCE)</option>
                        </select>
                    </div>

                    <!-- Photo Upload -->
                    <div class="space-y-1 md:col-span-2">
                        <label class="text-[10px] text-slate-500 font-bold uppercase tracking-wide block">Upload Foto Bungalow</label>
                        <div class="flex items-center gap-4 bg-slate-50 border border-slate-200 rounded-xl p-3">
                            <input type="file" accept="image/*" @change="handlePhotoUpload($event)" class="text-xs text-slate-500 file:mr-4 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-[#0B1A30] file:text-white hover:file:bg-slate-800 cursor-pointer flex-1">
                            <template x-if="crudForm.photo">
                                <img :src="crudForm.photo" class="w-12 h-12 rounded-lg object-cover border border-slate-200 flex-shrink-0" alt="Foto Fasilitas">
                            </template>
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="space-y-1 md:col-span-2">
                        <label class="text-[10px] text-slate-500 font-bold uppercase tracking-wide block">Deskripsi Lengkap Fasilitas</label>
                        <textarea x-model="crudForm.description" rows="3" placeholder="Deskripsikan kelengkapan fasilitas unit..." class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl p-3 focus:ring-1 focus:ring-wisma-gold focus:bg-white focus:outline-none transition-all resize-none"></textarea>
                    </div>
                </div>

                <div class="flex gap-3 pt-4 border-t border-slate-100">
                    <button type="button" @click="crudModalOpen = false" class="flex-1 py-3 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-xl transition-colors">
                        Batalkan
                    </button>
                    <button type="submit" class="flex-1 py-3 bg-[#0B1A30] hover:bg-slate-800 text-white font-bold text-xs rounded-xl shadow-lg transition-colors">
                        Simpan Data Inventaris
                    </button>
                </div>
            </form>
        </div>
    </div>

<!-- Delete Confirmation Modal -->
    <div x-show="deleteModalOpen" class="fixed inset-0 z-[100] flex items-center justify-center overflow-hidden" x-cloak>
        <div @click="deleteModalOpen = false" class="absolute inset-0 bg-slate-950/60 backdrop-blur-sm transition-opacity"></div>
        <div x-show="deleteModalOpen" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-90" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-90" class="bg-white rounded-3xl p-8 max-w-sm w-full mx-4 shadow-2xl relative z-10">
            <div class="w-16 h-16 rounded-2xl bg-red-50 text-red-500 flex items-center justify-center mx-auto mb-6">
                <i data-lucide="trash-2" class="w-8 h-8"></i>
            </div>
            <h3 class="text-xl font-bold font-outfit text-slate-900 text-center mb-2">Hapus Unit?</h3>
            <p class="text-sm text-slate-500 text-center mb-8" x-text="`Anda yakin ingin menghapus unit ${itemToDelete?.name}? Tindakan ini tidak dapat dibatalkan.`"></p>
            <div class="flex flex-col gap-3">
                <button @click="executeDelete()" class="w-full py-3.5 bg-red-600 hover:bg-red-700 text-white rounded-xl font-bold transition-colors">
                    Ya, Hapus Unit
                </button>
                <button @click="deleteModalOpen = false" class="w-full py-3.5 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl font-bold transition-colors">
                    Batal
                </button>
            </div>
        </div>
    </div>