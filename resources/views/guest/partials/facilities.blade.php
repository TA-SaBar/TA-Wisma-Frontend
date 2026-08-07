<div x-show="currentTab === 'facilities'" class="space-y-6 fade-in" x-cloak>
                    <div>
                        <h1 class="text-2xl font-outfit font-extrabold text-slate-900">Katalog & Booking Bungalow Wisma</h1>
                        <p class="text-xs text-slate-500">Jelajahi dan pesan bungalow wisma yang tersedia.</p>
                    </div>

                    <!-- Filters Bar -->
                    <div class="bg-white p-4 rounded-2xl border border-slate-100 shadow-sm grid grid-cols-1 md:grid-cols-4 gap-4 items-center">
                        <div>
                            <label class="text-[10px] text-slate-500 font-bold block mb-1 uppercase tracking-wide">Area</label>
                            <select x-model="filterLantai" class="w-full text-xs bg-slate-50 border border-slate-200 rounded-lg p-2 focus:ring-1 focus:ring-wisma-gold focus:outline-none">
                                <option value="">Semua Area</option>
                                <option value="Area Bawah">Bawah</option>
                                <option value="Area Atas">Atas</option>
                            </select>
                        </div>
                        <div>
                            <label class="text-[10px] text-slate-500 font-bold block mb-1 uppercase tracking-wide">Tipe Unit</label>
                            <select x-model="filterTipe" class="w-full text-xs bg-slate-50 border border-slate-200 rounded-lg p-2 focus:ring-1 focus:ring-wisma-gold focus:outline-none">
                                <option value="">Semua Tipe</option>
                                <option value="Buah">Bungalow Buah</option>
                                <option value="Bunga">Bungalow Bunga</option>
                                <option value="Rapat">Ruang Rapat</option>
                            </select>
                        </div>
                        <div>
                            <label class="text-[10px] text-slate-500 font-bold block mb-1 uppercase tracking-wide">Status</label>
                            <select x-model="filterStatus" class="w-full text-xs bg-slate-50 border border-slate-200 rounded-lg p-2 focus:ring-1 focus:ring-wisma-gold focus:outline-none">
                                <option value="semua">Semua Unit</option>
                                <option value="ready">Hanya Tersedia (READY)</option>
                            </select>
                        </div>
                        <div class="flex gap-2 pt-4">
                            <button @click="resetFilters()" class="flex-1 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-lg transition-colors">
                                Reset
                            </button>
                        </div>
                    </div>

                    <!-- Facilities Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <template x-for="f in filteredFacilities()" :key="f.id">
                            <div class="bg-white rounded-3xl border border-slate-100 overflow-hidden shadow-sm hover:shadow-xl transition-all duration-300 flex flex-col group">
                                <div class="relative overflow-hidden h-48 bg-slate-200 shrink-0">
                                    <img :src="f.photo" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110" alt="Foto Fasilitas">
                                    <div class="absolute top-4 left-4 flex gap-1.5 flex-wrap">
                                        <span class="px-2 py-0.5 bg-slate-900/70 text-white backdrop-blur-md rounded text-[9px] uppercase font-bold tracking-wide" x-text="f.type"></span>
                                        <span class="px-2 py-0.5 rounded text-[9px] uppercase font-bold tracking-wide text-white"
                                              :class="{ 'bg-emerald-500/80': f.status === 'READY', 'bg-amber-500/80': f.status === 'CLEANING', 'bg-red-500/80': f.status === 'MAINTENANCE' || f.status === 'OCCUPIED' }"
                                              x-text="f.status"></span>
                                    </div>
                                </div>
                                <div class="p-6 flex-1 flex flex-col justify-between space-y-4">
                                    <div>
                                        <span class="text-[9px] text-slate-400 font-bold uppercase tracking-wider block" x-text="f.area"></span>
                                        <h3 class="text-sm font-bold text-slate-900 mt-1 font-outfit truncate" x-text="f.name"></h3>
                                        <p class="text-xs text-slate-500 font-light mt-1.5 line-clamp-2" x-text="f.description"></p>
                                    </div>
                                    <div class="pt-4 border-t border-slate-50 flex items-center justify-between">
                                        <div>
                                            <span class="text-[9px] text-slate-400 block font-medium uppercase tracking-wider">Tarif Layanan</span>
                                            <p class="text-sm font-extrabold text-slate-900" x-text="formatRupiah(f.price)"></p>
                                            <span class="text-[9px] text-slate-400 font-bold uppercase tracking-wide block mt-0.5" x-text="'per ' + (f.unit === 'night' ? 'Malam' : (f.unit === 'day' ? 'Hari' : f.unit))"></span>
                                        </div>
                                        <button @click="openDrawer(f)" class="px-4 py-2 bg-slate-950 hover:bg-slate-900 text-white font-bold text-xs rounded-xl shadow-md transition-colors flex items-center gap-1">
                                            Detail <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- 3. BOOKING WIZARD VIEW -->