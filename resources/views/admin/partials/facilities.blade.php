<div x-show="currentTab === 'admin_management'" class="space-y-6" x-cloak>
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                        <div>
                            <h1 class="text-2xl font-outfit font-extrabold text-slate-900">Manajemen Bungalow Wisma (CRUD)</h1>
                            <p class="text-xs text-slate-500">Tambahkan unit baru, ubah tarif bungalow, kelola status kebersihan, atau hapus fasilitas dari database.</p>
                        </div>
                        <div class="flex gap-3">
                            <button @click="openAddModal('Buah')" class="px-4 py-2.5 bg-[#0B1A30] hover:bg-slate-800 text-white font-bold text-xs rounded-xl shadow-md transition-colors flex items-center gap-1.5">
                                <i data-lucide="plus" class="w-4 h-4"></i> Tambah Bungalow Buah
                            </button>
                            <button @click="openAddModal('Bunga')" class="px-4 py-2.5 bg-[#0B1A30] hover:bg-slate-800 text-white font-bold text-xs rounded-xl shadow-md transition-colors flex items-center gap-1.5">
                                <i data-lucide="plus" class="w-4 h-4"></i> Tambah Bungalow Bunga
                            </button>
                            <button @click="openAddModal('Rapat')" class="px-4 py-2.5 bg-[#0B1A30] hover:bg-slate-800 text-white font-bold text-xs rounded-xl shadow-md transition-colors flex items-center gap-1.5">
                                <i data-lucide="plus" class="w-4 h-4"></i> Tambah Ruang Rapat
                            </button>
                        </div>
                    </div>

                    <!-- Search and filters -->
                    <div class="bg-white p-4 rounded-2xl border border-slate-100 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
                        <div class="relative w-80">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                                <i data-lucide="search" class="w-4 h-4"></i>
                            </span>
                            <input type="text" 
                                   x-model.debounce.500ms="adminManagementSearch" 
                                   placeholder="Cari nama unit..." 
                                   class="w-full pl-9 pr-4 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-1 focus:ring-wisma-gold focus:outline-none transition-all">
                        </div>
                        <div class="flex bg-slate-100 p-1 rounded-xl select-none">
                            <button type="button" 
                                    @click="adminManagementSubTab = 'Buah'; setTimeout(() => { if (window.lucide) window.lucide.createIcons(); }, 50);"
                                    class="px-4 py-1.5 text-xs font-bold rounded-lg transition-all"
                                    :class="adminManagementSubTab === 'Buah' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-500 hover:text-slate-800'">
                                Bungalow Buah
                            </button>
                            <button type="button" 
                                    @click="adminManagementSubTab = 'Bunga'; setTimeout(() => { if (window.lucide) window.lucide.createIcons(); }, 50);"
                                    class="px-4 py-1.5 text-xs font-bold rounded-lg transition-all"
                                    :class="adminManagementSubTab === 'Bunga' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-500 hover:text-slate-800'">
                                Bungalow Bunga
                            </button>
                            <button type="button" 
                                    @click="adminManagementSubTab = 'Rapat'; setTimeout(() => { if (window.lucide) window.lucide.createIcons(); }, 50);"
                                    class="px-4 py-1.5 text-xs font-bold rounded-lg transition-all"
                                    :class="adminManagementSubTab === 'Rapat' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-500 hover:text-slate-800'">
                                Ruang Rapat
                            </button>
                        </div>
                    </div>

                    <!-- Inventory Table -->
                    <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-slate-50 border-b border-slate-100 text-[10px] text-slate-400 font-bold uppercase tracking-wider">
                                    <th class="py-4 px-6">Foto / Nama Unit</th>
                                    <th class="py-4 px-6">Tipe / Area</th>
                                    <th class="py-4 px-6">Tarif Unit</th>
                                    <th class="py-4 px-6">Status</th>
                                    <th class="py-4 px-6 text-right">Aksi Manajemen</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-xs">
                                <template x-for="f in filteredAdminFacilities()" :key="f.id">
                                    <tr class="hover:bg-slate-50/50 transition-all">
                                        <td class="py-4 px-6 flex items-center gap-3">
                                            <img :src="f.photo" class="w-12 h-12 rounded-lg object-cover border border-slate-200">
                                            <div>
                                                <p class="font-bold text-slate-900" x-text="f.name"></p>
                                                <template x-if="f.bed">
                                                    <p class="text-[10px] text-slate-400 mt-0.5" x-text="f.bed || 'Tipe Standar'"></p>
                                                </template>
                                            </div>
                                        </td>
                                        <td class="py-4 px-6">
                                            <p class="font-semibold text-slate-800" x-text="f.type"></p>
                                            <p class="text-[10px] text-slate-400 mt-0.5" x-text="f.area"></p>
                                        </td>
                                        <td class="py-4 px-6">
                                            <p class="font-bold text-slate-900" x-text="formatRupiah(f.price)"></p>
                                            <p class="text-[9px] text-slate-400 mt-0.5 uppercase tracking-wide" x-text="'per ' + (f.unit === 'night' ? 'Malam' : 'Hari')"></p>
                                        </td>
                                        <td class="py-4 px-6">
                                            <span class="px-2 py-0.5 rounded text-[9px] uppercase font-bold tracking-wide"
                                                  :class="{ 'bg-emerald-100 text-emerald-700': f.status === 'READY', 'bg-amber-100 text-amber-700': f.status === 'CLEANING', 'bg-red-100 text-red-700': f.status === 'MAINTENANCE' || f.status === 'OCCUPIED' }"
                                                  x-text="f.status"></span>
                                        </td>
                                        <td class="py-4 px-6 text-right">
                                            <div class="flex items-center justify-end gap-2">
                                                <button @click="openEditModal(f)" class="p-1.5 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-lg transition-colors" title="Edit">
                                                    <i data-lucide="edit-3" class="w-4 h-4"></i>
                                                </button>
                                                <button @click="confirmDelete(f.id)" class="p-1.5 bg-red-50 hover:bg-red-100 text-red-600 rounded-lg transition-colors" title="Hapus">
                                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                        <div x-show="filteredAdminFacilities().length === 0" class="text-center py-12 text-slate-400">
                            <i data-lucide="inbox" class="w-12 h-12 mx-auto mb-2 text-slate-200"></i>
                            <p class="text-xs">Tidak ada data unit dalam daftar ini.</p>
                        </div>
                    </div>
                </div>

                <!-- 3. DATABASE TAMU & RESERVASI LOG -->