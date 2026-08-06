<div x-show="currentTab === 'profile'" class="space-y-6 fade-in" x-cloak>
                    <div>
                        <h1 class="text-2xl font-outfit font-extrabold text-slate-900">Profil Saya</h1>
                        <p class="text-xs text-slate-500">Kelola dan amankan informasi pribadi Anda.</p>
                    </div>

                    <div class="max-w-2xl bg-white border border-slate-100 rounded-3xl p-8 shadow-sm space-y-6">
                        <div class="flex items-center gap-5 pb-6 border-b border-slate-100">
                            <img class="w-20 h-20 rounded-2xl border-2 border-wisma-gold object-cover shadow-md" 
                                 src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&w=150&h=150&q=80">
                            <div>
                                <h3 class="text-lg font-bold text-slate-900 font-outfit" x-text="profile.nama"></h3>
                                <p class="text-xs text-slate-400" x-text="profile.role_label + ' • ' + profile.instansi"></p>
                            </div>
                        </div>

                        <form @submit.prevent="saveProfile()" class="space-y-4">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="space-y-1">
                                    <label class="text-[10px] text-slate-500 font-bold uppercase tracking-wide block">Nama Lengkap</label>
                                    <input type="text" x-model="profile.nama" class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl p-3 focus:ring-1 focus:ring-wisma-gold focus:outline-none focus:bg-white transition-all">
                                </div>
                                <div class="space-y-1">
                                    <label class="text-[10px] text-slate-500 font-bold uppercase tracking-wide block">NIP Kedinasan</label>
                                    <input type="text" x-model="profile.nip" disabled class="w-full text-xs bg-slate-100 border border-slate-200 rounded-xl p-3 text-slate-500 cursor-not-allowed">
                                </div>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="space-y-1">
                                    <label class="text-[10px] text-slate-500 font-bold uppercase tracking-wide block">No. WhatsApp</label>
                                    <input type="text" x-model="profile.whatsapp" class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl p-3 focus:ring-1 focus:ring-wisma-gold focus:outline-none focus:bg-white transition-all">
                                </div>
                                <div class="space-y-1">
                                    <label class="text-[10px] text-slate-500 font-bold uppercase tracking-wide block">Email Resmi</label>
                                    <input type="email" x-model="profile.email" class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl p-3 focus:ring-1 focus:ring-wisma-gold focus:outline-none focus:bg-white transition-all">
                                </div>
                            </div>
                            <button type="submit" class="py-3 px-6 bg-slate-950 hover:bg-slate-900 text-white font-bold text-xs rounded-xl shadow-lg transition-colors">
                                Simpan Perubahan Profil
                            </button>
                        </form>
                    </div>

                    <div class="max-w-2xl bg-white border border-slate-100 rounded-3xl p-8 shadow-sm space-y-6 mt-6">
                        <div>
                            <h3 class="text-sm font-bold text-slate-900 font-outfit">Keamanan Akun</h3>
                            <p class="text-xs text-slate-500">Ubah kata sandi Anda secara berkala untuk menjaga keamanan akun.</p>
                        </div>
                        <form @submit.prevent="savePassword()" class="space-y-4">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="space-y-1">
                                    <label class="text-[10px] text-slate-500 font-bold uppercase tracking-wide block">Kata Sandi Saat Ini</label>
                                    <input type="password" x-model="passwordForm.current_password" class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl p-3 focus:ring-1 focus:ring-wisma-gold focus:outline-none focus:bg-white transition-all" placeholder="••••••••" required>
                                </div>
                                <div class="space-y-1 md:col-span-2">
                                    <hr class="border-slate-100 my-2">
                                </div>
                                <div class="space-y-1">
                                    <label class="text-[10px] text-slate-500 font-bold uppercase tracking-wide block">Kata Sandi Baru</label>
                                    <input type="password" x-model="passwordForm.password" class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl p-3 focus:ring-1 focus:ring-wisma-gold focus:outline-none focus:bg-white transition-all" placeholder="••••••••" required>
                                </div>
                                <div class="space-y-1">
                                    <label class="text-[10px] text-slate-500 font-bold uppercase tracking-wide block">Konfirmasi Kata Sandi Baru</label>
                                    <input type="password" x-model="passwordForm.password_confirmation" class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl p-3 focus:ring-1 focus:ring-wisma-gold focus:outline-none focus:bg-white transition-all" placeholder="••••••••" required>
                                </div>
                            </div>
                            <button type="submit" class="py-3 px-6 bg-slate-950 hover:bg-slate-900 text-white font-bold text-xs rounded-xl shadow-lg transition-colors mt-2">
                                Perbarui Kata Sandi
                            </button>
                        </form>
                    </div>
                </div>

                <!-- 6. COMPLAINTS & HELP VIEW -->