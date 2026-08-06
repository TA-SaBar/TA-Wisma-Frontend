<!-- RATING & FEEDBACK MODAL -->
    <div x-show="ratingModalOpen" class="fixed inset-0 z-[100] flex items-center justify-center overflow-hidden" x-cloak>
        <div @click="ratingModalOpen = false" class="absolute inset-0 bg-slate-950/60 backdrop-blur-sm transition-opacity"></div>
        <div class="bg-white rounded-3xl shadow-2xl p-8 max-w-md w-full relative z-10 space-y-6 transform scale-100 transition-all fade-in">
            <div class="text-center space-y-2">
                <div class="w-12 h-12 rounded-2xl bg-amber-50 text-wisma-gold flex items-center justify-center mx-auto mb-2 shadow-inner">
                    <i data-lucide="star" class="w-6 h-6 fill-current"></i>
                </div>
                <h3 class="text-base font-extrabold text-slate-900 font-outfit">Beri Rating & Feedback</h3>
                <p class="text-[11px] text-slate-500" x-text="'Bagikan ulasan Anda untuk unit ' + feedbackBooking.unit_name"></p>
            </div>

            <!-- Star ratings categories -->
            <div class="space-y-4">
                <div class="flex items-center justify-between text-xs">
                    <span class="font-medium text-slate-700">Kebersihan Kamar</span>
                    <div class="flex items-center gap-1">
                        <template x-for="star in 5">
                            <button @click="feedbackRating.cleanliness = star" class="text-slate-300 hover:text-amber-400 transition-colors">
                                <svg class="w-5 h-5 fill-current" :class="star <= feedbackRating.cleanliness ? 'text-wisma-gold' : 'text-slate-200'" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                            </button>
                        </template>
                    </div>
                </div>

                <div class="flex items-center justify-between text-xs">
                    <span class="font-medium text-slate-700">Kelengkapan Fasilitas</span>
                    <div class="flex items-center gap-1">
                        <template x-for="star in 5">
                            <button @click="feedbackRating.facilities = star" class="text-slate-300 hover:text-amber-400 transition-colors">
                                <svg class="w-5 h-5 fill-current" :class="star <= feedbackRating.facilities ? 'text-wisma-gold' : 'text-slate-200'" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                            </button>
                        </template>
                    </div>
                </div>

                <div class="flex items-center justify-between text-xs">
                    <span class="font-medium text-slate-700">Kualitas Layanan</span>
                    <div class="flex items-center gap-1">
                        <template x-for="star in 5">
                            <button @click="feedbackRating.service = star" class="text-slate-300 hover:text-amber-400 transition-colors">
                                <svg class="w-5 h-5 fill-current" :class="star <= feedbackRating.service ? 'text-wisma-gold' : 'text-slate-200'" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                            </button>
                        </template>
                    </div>
                </div>
            </div>

            <!-- Written Comment -->
            <div class="space-y-1">
                <label class="text-[9px] text-slate-500 font-bold uppercase tracking-wide">Ulasan Anda</label>
                <textarea x-model="feedbackComment" 
                          placeholder="Bagikan pengalaman Anda selama menginap secara detail untuk membantu kami meningkatkan layanan..." 
                          rows="3" 
                          class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl p-3 focus:ring-1 focus:ring-wisma-gold focus:bg-white focus:outline-none transition-all resize-none"></textarea>
            </div>

            <div class="flex gap-3 pt-2">
                <button @click="ratingModalOpen = false" class="flex-1 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-xl transition-colors">
                    Batalkan
                </button>
                <button @click="submitRating()" class="flex-1 py-2.5 bg-wisma-navy hover:bg-slate-800 text-white font-bold text-xs rounded-xl shadow-lg transition-colors">
                    Kirim Ulasan
                </button>
            </div>
        </div>
    </div>


<div x-show="drawerOpen" class="fixed inset-0 z-50 overflow-hidden" x-cloak>
        <div @click="closeDrawer()" class="absolute inset-0 bg-slate-950/60 backdrop-blur-sm transition-opacity"></div>
        <div class="absolute inset-y-0 right-0 max-w-md w-full bg-white shadow-2xl flex flex-col justify-between h-full slide-in-right">
            <div class="overflow-y-auto flex-1">
                <div class="relative h-64 bg-slate-200">
                    <img :src="drawerFacility.photo" class="w-full h-full object-cover">
                    <button @click="closeDrawer()" class="absolute top-4 right-4 w-9 h-9 bg-white/80 hover:bg-white text-slate-800 rounded-full flex items-center justify-center backdrop-blur-md shadow-md">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>
                <div class="p-6 space-y-6">
                    <div>
                        <span class="px-2 py-0.5 bg-slate-100 text-slate-700 rounded text-[9px] uppercase font-bold tracking-wide" x-text="drawerFacility.type"></span>
                        <h2 class="text-xl font-bold font-outfit text-slate-950 mt-2" x-text="drawerFacility.name"></h2>
                        <p class="text-xs text-slate-500 font-medium mt-1 flex items-center gap-1"><i data-lucide="map-pin" class="w-3.5 h-3.5"></i> <span x-text="drawerFacility.area"></span></p>
                    </div>

                    <div class="grid grid-cols-1 gap-4">
                        <div class="bg-slate-50 p-3.5 rounded-2xl border border-slate-100 flex items-center gap-3">
                            <i data-lucide="maximize" class="w-5 h-5 text-slate-500"></i>
                            <div>
                                <span class="text-[9px] text-slate-400 block font-bold uppercase tracking-wide" x-text="drawerFacility.unit === 'day' ? 'Tipe Meja' : 'Tipe Bed'"></span>
                                <p class="text-xs font-bold text-slate-800" x-text="drawerFacility.bed || 'Standar'"></p>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <h3 class="text-xs font-bold text-slate-900 uppercase tracking-wider">Deskripsi Fasilitas</h3>
                        <p class="text-xs text-slate-600 leading-relaxed font-light" x-text="drawerFacility.description"></p>
                    </div>
                </div>
            </div>

            <!-- CTA Bottom -->
            <div class="p-6 border-t border-slate-100 bg-slate-50/50 flex items-center justify-between gap-4">
                <div>
                    <span class="text-[9px] text-slate-400 block font-bold uppercase tracking-wide">Tarif Unit</span>
                    <p class="text-base font-extrabold text-slate-900" x-text="formatRupiah(drawerFacility.price)"></p>
                </div>
                <template x-if="drawerFacility.status !== 'MAINTENANCE'">
                    <button @click="startBookingFlow(drawerFacility)" class="px-6 py-3 bg-wisma-navy hover:bg-slate-800 text-white font-bold text-xs rounded-xl shadow-lg transition-colors flex items-center gap-1.5">
                        <i data-lucide="calendar" class="w-4 h-4"></i> Booking Sekarang
                    </button>
                </template>
                <template x-if="drawerFacility.status === 'MAINTENANCE'">
                    <button disabled class="px-6 py-3 bg-slate-200 text-slate-400 font-bold text-xs rounded-xl cursor-not-allowed flex items-center gap-1.5">
                        <i data-lucide="lock" class="w-4 h-4"></i> Tidak Dapat Dipesan
                    </button>
                </template>
            </div>
        </div>
    </div>