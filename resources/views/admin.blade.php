<!DOCTYPE html>
<html lang="id" x-data="wismaApp()" x-init="initApp()">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Portal Koordinator Wisma DPR RI - Manajemen Sistem</title>

    <!-- Google Fonts: Plus Jakarta Sans & Outfit -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Tailwind CSS v4 CDN Fallback & Styling -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Plus Jakarta Sans', 'ui-sans-serif', 'system-ui', 'sans-serif'],
                        outfit: ['Outfit', 'sans-serif'],
                    },
                    colors: {
                        wisma: {
                            navy: '#0B1A30',
                            dark: '#081324',
                            gold: '#E5A93C',
                            goldHover: '#C9922E',
                            accent: '#F4F7FC',
                            textMuted: '#A1B0CB',
                        }
                    }
                }
            }
        }
    </script>

    <!-- Lucide Icons CDN -->
    <script src="https://unpkg.com/lucide@latest"></script>

    <!-- Alpine.js CDN -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        [x-cloak] { display: none !important; }
        .scrollbar-hide::-webkit-scrollbar { display: none; }
        .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
        
        .fade-in { animation: fadeIn 0.3s ease-out forwards; }
        .slide-in-right { animation: slideInRight 0.35s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(4px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @media print {
            body {
                background: white !important;
                color: black !important;
                overflow: visible !important;
                height: auto !important;
            }
            aside, header, nav, .no-print, button, .modal, .toast, [title="Keluar"], .print-hide {
                display: none !important;
            }
            main {
                padding: 0 !important;
                margin: 0 !important;
                overflow: visible !important;
                height: auto !important;
                width: 100% !important;
                display: block !important;
                background: white !important;
            }
            .printable-report {
                display: block !important;
                background: white !important;
                padding: 20px !important;
                box-shadow: none !important;
                border: none !important;
            }
            tr {
                page-break-inside: avoid;
            }
            .page-break-inside-avoid {
                page-break-inside: avoid !important;
            }
        }
    </style>
</head>
<body class="bg-[#F8FAFC] text-slate-800 font-sans min-h-screen flex overflow-hidden">

    <!-- Global Toast Notification -->
    <div class="fixed top-5 right-5 z-[100] space-y-2 pointer-events-none">
        <template x-for="toast in toasts" :key="toast.id">
            <div class="flex items-center gap-3 bg-white text-slate-800 border-l-4 border-wisma-gold px-4 py-3 rounded-lg shadow-xl pointer-events-auto transform translate-y-0 transition-all duration-300 max-w-sm fade-in"
                 :class="{ 'border-emerald-500': toast.type === 'success', 'border-red-500': toast.type === 'error', 'border-blue-500': toast.type === 'info' }">
                <div class="flex-shrink-0">
                    <template x-if="toast.type === 'success'">
                        <div class="p-1 bg-emerald-100 text-emerald-600 rounded-full">
                            <i data-lucide="check" class="w-4 h-4"></i>
                        </div>
                    </template>
                    <template x-if="toast.type === 'error'">
                        <div class="p-1 bg-red-100 text-red-600 rounded-full">
                            <i data-lucide="alert-circle" class="w-4 h-4"></i>
                        </div>
                    </template>
                    <template x-if="toast.type === 'info'">
                        <div class="p-1 bg-blue-100 text-blue-600 rounded-full">
                            <i data-lucide="info" class="w-4 h-4"></i>
                        </div>
                    </template>
                </div>
                <div class="flex-1">
                    <p class="text-xs font-semibold text-slate-900" x-text="toast.title"></p>
                    <p class="text-[11px] text-slate-500" x-text="toast.message"></p>
                </div>
                <button @click="removeToast(toast.id)" class="text-slate-400 hover:text-slate-600">
                    <i data-lucide="x" class="w-3.5 h-3.5"></i>
                </button>
            </div>
        </template>
    </div>

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
                    <div class="space-y-1">
                        <label class="text-[10px] text-slate-500 font-bold uppercase tracking-wide block">Nama Unit Fasilitas</label>
                        <input type="text" x-model="crudForm.name" required placeholder="Contoh: Deluxe Room 204" class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl p-3 focus:ring-1 focus:ring-wisma-gold focus:bg-white focus:outline-none transition-all">
                    </div>

                    <!-- Gedung -->
                    <div class="space-y-1">
                        <label class="text-[10px] text-slate-500 font-bold uppercase tracking-wide block">Gedung</label>
                        <select x-model="crudForm.gedung" class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl p-3 focus:ring-1 focus:ring-wisma-gold focus:bg-white focus:outline-none transition-all">
                            <option value="Wisma">Wisma</option>
                        </select>
                    </div>

                    <!-- Area -->
                    <div class="space-y-1">
                        <label class="text-[10px] text-slate-500 font-bold uppercase tracking-wide block">Area</label>
                        <select x-model="crudForm.lantai" class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl p-3 focus:ring-1 focus:ring-wisma-gold focus:bg-white focus:outline-none transition-all">
                            <option value="Area Bawah">Area Bawah</option>
                            <option value="Area Atas">Area Atas</option>
                        </select>
                    </div>

                    <!-- Harga -->
                    <div class="space-y-1">
                        <label class="text-[10px] text-slate-500 font-bold uppercase tracking-wide block">Harga / Tarif (Rp)</label>
                        <input type="number" x-model="crudForm.price" required class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl p-3 focus:ring-1 focus:ring-wisma-gold focus:bg-white focus:outline-none transition-all">
                    </div>

                    <!-- Unit -->
                    <div class="space-y-1">
                        <label class="text-[10px] text-slate-500 font-bold uppercase tracking-wide block">Satuan Tarif</label>
                        <select x-model="crudForm.unit" class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl p-3 focus:ring-1 focus:ring-wisma-gold focus:bg-white focus:outline-none transition-all">
                            <option value="night">Per Malam (Bungalow)</option>
                            <option value="day">Per Hari (Ruang Rapat)</option>
                        </select>
                    </div>

                    <!-- Luas -->
                    <div class="space-y-1">
                        <label class="text-[10px] text-slate-500 font-bold uppercase tracking-wide block">Luas Area</label>
                        <div class="relative">
                            <input type="number" x-model="crudForm.luas" placeholder="Contoh: 32" class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl p-3 pr-10 focus:ring-1 focus:ring-wisma-gold focus:bg-white focus:outline-none transition-all">
                            <span class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none text-slate-400 text-xs font-semibold">m²</span>
                        </div>
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
                                <img :src="crudForm.photo" class="w-12 h-12 rounded-lg object-cover border border-slate-200 flex-shrink-0">
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

    <!-- 1. LOGIN SCREEN -->
    <div x-show="!isLoggedIn" class="w-full h-screen flex relative z-30 fade-in">
        <!-- Cover Section Left -->
        <div class="w-[55%] h-full bg-slate-900 relative overflow-hidden hidden md:block">
            <img class="absolute inset-0 w-full h-full object-cover opacity-60" 
                 src="/images/wisma_dpr.jpg">
            <div class="absolute inset-0 bg-gradient-to-t from-wisma-dark via-wisma-dark/45 to-transparent"></div>
            
            <div class="absolute inset-x-12 bottom-16 space-y-8 z-10">
                <div class="space-y-4">
                    <span class="px-3 py-1 bg-amber-500/20 text-wisma-gold text-[10px] uppercase font-bold tracking-widest rounded-full border border-wisma-gold/30 flex items-center gap-1.5 w-max">
                        <i data-lucide="shield" class="w-3 h-3"></i> Koordinator Wisma Portal
                    </span>
                    <h1 class="text-4xl font-outfit font-extrabold text-white tracking-tight leading-tight max-w-lg">
                        Sistem Inventarisasi, Pemantauan, & Laporan Keuangan
                    </h1>
                    <p class="text-xs text-slate-300 leading-relaxed max-w-md font-light">
                        Portal backend khusus Koordinator Wisma DPR RI. Akses modul manajemen inventaris (CRUD), tinjau database tamu resmi, audit riwayat reservasi, serta pelajari laporan keuangan wisma.
                    </p>
                </div>

                <div class="grid grid-cols-3 gap-6 pt-4 border-t border-white/10 max-w-lg text-white">
                    <div>
                        <h4 class="text-2xl font-bold font-outfit text-wisma-gold">Inventaris</h4>
                        <span class="text-[10px] text-slate-400 block mt-0.5">Kontrol Penuh</span>
                    </div>
                    <div>
                        <h4 class="text-2xl font-bold font-outfit text-wisma-gold">Log Audit</h4>
                        <span class="text-[10px] text-slate-400 block mt-0.5">Reservasi Tamu</span>
                    </div>
                    <div>
                        <h4 class="text-2xl font-bold font-outfit text-wisma-gold">Laporan</h4>
                        <span class="text-[10px] text-slate-400 block mt-0.5">Keuangan & Pajak</span>
                    </div>
                </div>

                <div class="flex items-center gap-2.5 text-white/50 text-xs">
                    <i data-lucide="hotel" class="w-4 h-4"></i>
                    <span class="uppercase tracking-widest font-semibold text-[10px]">Wisma DPR RI</span>
                </div>
            </div>
        </div>

        <!-- Login Form Right -->
        <div class="flex-1 h-full bg-white flex flex-col justify-between p-12">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center border border-indigo-100">
                        <i data-lucide="landmark" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <h2 class="font-outfit font-bold text-sm text-slate-900 tracking-wider leading-none">Wisma DPR RI</h2>
                        <span class="text-[9px] text-slate-400 font-medium uppercase tracking-widest">Government Hospitality</span>
                    </div>
                </div>
                <a href="/" class="flex items-center gap-1.5 text-slate-500 hover:text-slate-900 text-xs font-bold transition-colors">
                    <i data-lucide="arrow-left" class="w-4 h-4"></i> Kembali
                </a>
            </div>

            <div class="max-w-md w-full mx-auto space-y-8">
                <div class="space-y-2 text-center md:text-left">
                    <h2 class="text-2xl font-extrabold text-slate-900 font-outfit tracking-tight">Portal Koordinator Wisma</h2>
                    <p class="text-xs text-slate-500">Silakan masukkan kredensial Koordinator Wisma Anda.</p>
                </div>

                <form @submit.prevent="login()" class="space-y-5">
                    <!-- Email -->
                    <div class="space-y-1">
                        <label class="text-[10px] text-slate-500 font-bold uppercase tracking-wide block">Email</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                <i data-lucide="mail" class="w-4.5 h-4.5"></i>
                            </span>
                            <input type="email" 
                                   x-model="loginForm.email"
                                   placeholder="Contoh: koordinator@wisma.dpr.go.id" 
                                   class="w-full pl-10 pr-4 py-3 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-1 focus:ring-wisma-gold focus:outline-none transition-all">
                        </div>
                    </div>

                    <!-- Password -->
                    <div class="space-y-1">
                        <label class="text-[10px] text-slate-500 font-bold uppercase tracking-wide block">Password</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                <i data-lucide="lock" class="w-4.5 h-4.5"></i>
                            </span>
                            <input :type="passwordVisible ? 'text' : 'password'" 
                                   x-model="loginForm.password"
                                   placeholder="••••••••" 
                                   class="w-full pl-10 pr-12 py-3 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-1 focus:ring-wisma-gold focus:outline-none transition-all">
                            <button type="button" @click="passwordVisible = !passwordVisible" class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600">
                                <i :data-lucide="passwordVisible ? 'eye-off' : 'eye'" class="w-4.5 h-4.5"></i>
                            </button>
                        </div>
                    </div>

                    <button type="submit" class="w-full py-3 bg-slate-950 hover:bg-slate-900 text-white font-bold text-xs rounded-xl shadow-lg transition-colors flex items-center justify-center gap-2">
                        Masuk Portal Koordinator Wisma <i data-lucide="arrow-right" class="w-4 h-4"></i>
                    </button>
                </form>
            </div>

            <!-- Footer copyright -->
            <div class="flex items-center justify-between text-[10px] text-slate-400 pt-8 border-t border-slate-100 w-full">
                <span>© 2026 Sekretariat Jenderal DPR RI. Semua Hak Dilindungi.</span>
            </div>
        </div>
    </div>

    <!-- MAIN PORTAL DASHBOARD (Visible if isLoggedIn) -->
    <div x-show="isLoggedIn" class="flex-1 flex h-screen overflow-hidden" x-cloak>
        
        <!-- SIDEBAR -->
        <aside class="w-72 bg-wisma-navy text-white flex flex-col shrink-0 h-screen shadow-2xl relative z-20">
            <!-- Logo Area -->
            <div class="p-6 border-b border-slate-800 flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-wisma-gold to-amber-300 flex items-center justify-center shadow-lg shadow-amber-500/20">
                    <i data-lucide="shield" class="w-6 h-6 text-wisma-dark"></i>
                </div>
                <div>
                    <h2 class="font-outfit font-bold text-base tracking-wider leading-none">Wisma DPR RI</h2>
                    <span class="text-[10px] text-wisma-textMuted font-medium uppercase tracking-widest font-outfit">Koordinator Wisma</span>
                </div>
            </div>

            <!-- Sidebar Navigation Menu -->
            <nav class="flex-1 px-4 py-6 space-y-1.5 overflow-y-auto scrollbar-hide">
                <p class="text-[10px] text-slate-500 font-semibold px-3 mb-2 uppercase tracking-widest">Manajemen</p>
                
                <button @click="switchTab('admin_dashboard')"
                        class="w-full flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 group text-left"
                        :class="currentTab === 'admin_dashboard' ? 'bg-gradient-to-r from-wisma-gold to-amber-500 text-wisma-dark font-semibold shadow-lg shadow-wisma-gold/15' : 'text-wisma-textMuted hover:bg-slate-800/50 hover:text-white'">
                    <i data-lucide="layout-dashboard" class="w-5 h-5 transition-transform group-hover:scale-110"></i>
                    <span class="text-sm">Dashboard</span>
                </button>

                <button @click="switchTab('admin_management')"
                        class="w-full flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 group text-left"
                        :class="currentTab === 'admin_management' ? 'bg-gradient-to-r from-wisma-gold to-amber-500 text-wisma-dark font-semibold shadow-lg shadow-wisma-gold/15' : 'text-wisma-textMuted hover:bg-slate-800/50 hover:text-white'">
                    <i data-lucide="layout" class="w-5 h-5 transition-transform group-hover:scale-110"></i>
                    <span class="text-sm">Manajemen Unit</span>
                    <span class="ml-auto px-2 py-0.5 bg-wisma-dark/25 rounded-md text-[10px]" x-text="facilities.length"></span>
                </button>

                <button @click="switchTab('admin_guests')"
                        class="w-full flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 group text-left"
                        :class="currentTab === 'admin_guests' ? 'bg-gradient-to-r from-wisma-gold to-amber-500 text-wisma-dark font-semibold shadow-lg shadow-wisma-gold/15' : 'text-wisma-textMuted hover:bg-slate-800/50 hover:text-white'">
                    <i data-lucide="users" class="w-5 h-5 transition-transform group-hover:scale-110"></i>
                    <span class="text-sm">Database Tamu</span>
                    <span class="ml-auto px-2 py-0.5 bg-wisma-dark/25 rounded-md text-[10px]" x-text="guests.length"></span>
                </button>

                <button @click="switchTab('admin_reports')"
                        class="w-full flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 group text-left"
                        :class="currentTab === 'admin_reports' ? 'bg-gradient-to-r from-wisma-gold to-amber-500 text-wisma-dark font-semibold shadow-lg shadow-wisma-gold/15' : 'text-wisma-textMuted hover:bg-slate-800/50 hover:text-white'">
                    <i data-lucide="bar-chart-3" class="w-5 h-5 transition-transform group-hover:scale-110"></i>
                    <span class="text-sm">Laporan Keuangan</span>
                </button>

                <div class="border-t border-slate-800 my-2 mx-3"></div>
                <p class="text-[10px] text-slate-500 font-semibold px-3 mb-2 uppercase tracking-widest">Akun</p>

                <button @click="switchTab('admin_settings')"
                        class="w-full flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 group text-left"
                        :class="currentTab === 'admin_settings' ? 'bg-gradient-to-r from-wisma-gold to-amber-500 text-wisma-dark font-semibold shadow-lg shadow-wisma-gold/15' : 'text-wisma-textMuted hover:bg-slate-800/50 hover:text-white'">
                    <i data-lucide="settings" class="w-5 h-5 transition-transform group-hover:scale-110"></i>
                    <span class="text-sm">Pengaturan</span>
                </button>
            </nav>

            <!-- Sidebar Footer/User Profile Summary -->
            <div class="p-4 border-t border-slate-800 bg-wisma-dark/40 flex items-center gap-3">
                <img class="w-10 h-10 rounded-full border border-wisma-gold/30 object-cover" 
                     src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?auto=format&fit=crop&w=100&h=100&q=80" 
                     alt="Admin Avatar">
                <div class="overflow-hidden">
                    <p class="text-xs font-semibold text-white truncate" x-text="profile.nama"></p>
                    <p class="text-[10px] text-wisma-textMuted truncate" x-text="profile.role_label"></p>
                </div>
                <button @click="logout()" class="ml-auto text-slate-400 hover:text-red-400 p-1.5 rounded-lg hover:bg-slate-800 transition-colors" title="Keluar">
                    <i data-lucide="log-out" class="w-4 h-4"></i>
                </button>
            </div>
        </aside>

        <!-- CONTENT WRAPPER -->
        <div class="flex-1 flex flex-col h-screen overflow-hidden bg-slate-50">
            
            <!-- HEADER -->
            <header class="h-20 bg-white border-b border-slate-100 flex items-center justify-between px-8 relative z-10 shrink-0">
                <div class="flex items-center gap-2 text-slate-700">
                    <i data-lucide="shield-check" class="w-5 h-5 text-indigo-600"></i>
                    <span class="text-xs font-bold font-outfit uppercase tracking-wider">Mode Koordinator Wisma Aktif</span>
                </div>

                <div class="flex items-center gap-4">
                    <div class="flex items-center gap-3">
                        <div class="text-right">
                            <p class="text-xs font-semibold text-slate-800" x-text="profile.nama"></p>
                            <p class="text-[10px] text-slate-500" x-text="profile.instansi"></p>
                        </div>
                        <div class="w-10 h-10 rounded-xl bg-wisma-navy text-wisma-gold flex items-center justify-center font-bold text-sm border border-wisma-gold/20 shadow-sm">AD</div>
                    </div>
                </div>
            </header>

            <!-- SCROLLABLE PAGE CONTENT -->
            <main class="flex-1 overflow-y-auto p-8 relative">

                <!-- 1. DASHBOARD VIEW -->
                <div x-show="currentTab === 'admin_dashboard'" class="space-y-8 fade-in">
                    <!-- Welcome Banner -->
                    <div class="relative overflow-hidden bg-gradient-to-r from-wisma-navy to-slate-900 text-white rounded-3xl p-8 shadow-xl">
                        <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_top_right,_var(--tw-gradient-stops))] from-amber-500/15 via-transparent to-transparent"></div>
                        <div class="relative z-10 max-w-xl">
                            <span class="px-3 py-1 bg-amber-500/20 text-wisma-gold text-[10px] uppercase font-bold tracking-widest rounded-full border border-wisma-gold/30">Backend Control Panel</span>
                            <h1 class="text-3xl font-outfit font-extrabold mt-4 mb-2 tracking-tight" x-text="'Selamat Datang, ' + profile.nama"></h1>
                            <p class="text-xs text-slate-300 leading-relaxed font-light">
                                Sistem Manajemen Terpadu Wisma DPR RI. Tambah, edit, dan awasi ketersediaan unit kamar, tinjau log data reservasi masuk, serta pantau laporan okupansi operasional wisma.
                            </p>
                            <div class="mt-6 flex gap-3">
                                <button @click="switchTab('admin_management')" class="px-5 py-2.5 bg-wisma-gold hover:bg-wisma-goldHover text-wisma-dark font-semibold text-xs rounded-xl shadow-lg shadow-wisma-gold/20 transition-all flex items-center gap-1">
                                    <i data-lucide="layout" class="w-4 h-4"></i> Manajemen Unit
                                </button>
                                <button @click="switchTab('admin_guests')" class="px-5 py-2.5 bg-white/10 hover:bg-white/20 text-white border border-white/10 font-semibold text-xs rounded-xl transition-all flex items-center gap-1">
                                    <i data-lucide="users" class="w-4 h-4"></i> Database Tamu
                                </button>
                            </div>
                        </div>
                        <div class="absolute right-10 bottom-0 top-0 hidden lg:flex items-center text-white/5 pointer-events-none select-none">
                            <i data-lucide="shield" class="w-64 h-64"></i>
                        </div>
                    </div>

                    <!-- Stats Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex items-center justify-between group hover:shadow-md transition-shadow">
                            <div>
                                <span class="text-xs text-slate-500 font-medium">Fasilitas Unit Terdaftar</span>
                                <h3 class="text-2xl font-bold font-outfit mt-1 text-slate-900" x-text="facilities.length + ' Unit'"></h3>
                            </div>
                            <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-500 flex items-center justify-center transition-transform group-hover:scale-110">
                                <i data-lucide="home" class="w-6 h-6"></i>
                            </div>
                        </div>
                        <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex items-center justify-between group hover:shadow-md transition-shadow">
                            <div>
                                <span class="text-xs text-slate-500 font-medium">Tamu Terdaftar (Log DIPA)</span>
                                <h3 class="text-2xl font-bold font-outfit mt-1 text-slate-900" x-text="guests.length + ' Tamu'"></h3>
                            </div>
                            <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-500 flex items-center justify-center transition-transform group-hover:scale-110">
                                <i data-lucide="user-check" class="w-6 h-6"></i>
                            </div>
                        </div>
                        <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex items-center justify-between group hover:shadow-md transition-shadow">
                            <div>
                                <span class="text-xs text-slate-500 font-medium">Pendapatan Diterima (Estimasi)</span>
                                <h3 class="text-xl font-bold font-outfit mt-1.5 text-slate-900" x-text="formatRupiah(bookings.reduce((sum, b) => b.status === 'Lunas' || b.status === 'Selesai' || b.status === 'Check In' ? sum + Number(b.total_price) : sum, 0))"></h3>
                            </div>
                            <div class="w-12 h-12 rounded-xl bg-amber-50 text-wisma-gold flex items-center justify-center transition-transform group-hover:scale-110">
                                <i data-lucide="wallet" class="w-6 h-6"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 2. FACILITIES CRUD INVENTORY VIEW -->
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
                                   x-model="adminManagementSearch" 
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
                                    <th class="py-4 px-6">Gedung / Area</th>
                                    <th class="py-4 px-6">Luas Area</th>
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
                                                <p class="text-[10px] text-slate-400 mt-0.5" x-text="f.bed || 'Tipe Standar'"></p>
                                            </div>
                                        </td>
                                        <td class="py-4 px-6">
                                            <p class="font-semibold text-slate-800" x-text="f.gedung"></p>
                                            <p class="text-[10px] text-slate-400 mt-0.5" x-text="f.lantai"></p>
                                        </td>
                                        <td class="py-4 px-6">
                                            <p class="font-medium text-slate-800" x-text="f.luas || '24 m²'"></p>
                                        </td>
                                        <td class="py-4 px-6">
                                            <p class="font-bold text-slate-900" x-text="formatRupiah(f.price)"></p>
                                            <p class="text-[9px] text-slate-400 mt-0.5 uppercase tracking-wide" x-text="'per ' + (f.unit === 'night' ? 'Malam' : f.unit)"></p>
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
                                                <button @click="deleteCrudItem(f.id)" class="p-1.5 bg-red-50 hover:bg-red-100 text-red-600 rounded-lg transition-colors" title="Hapus">
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
                <div x-show="currentTab === 'admin_guests'" class="space-y-6" x-cloak>
                    <div class="flex justify-between items-center">
                        <div>
                            <h1 class="text-2xl font-outfit font-extrabold text-slate-900">Database Tamu & Log Reservasi</h1>
                            <p class="text-xs text-slate-500">Monitor profil tamu resmi serta seluruh riwayat pemesanan e-Budgeting.</p>
                        </div>
                        
                        <div class="flex bg-slate-100 p-1 rounded-xl select-none">
                            <button type="button" 
                                    @click="adminGuestViewTab = 'tamu'"
                                    class="px-4 py-1.5 text-xs font-bold rounded-lg transition-all"
                                    :class="adminGuestViewTab === 'tamu' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-500 hover:text-slate-800'">
                                Database Tamu
                            </button>
                            <button type="button" 
                                    @click="adminGuestViewTab = 'reservasi'"
                                    class="px-4 py-1.5 text-xs font-bold rounded-lg transition-all"
                                    :class="adminGuestViewTab === 'reservasi' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-500 hover:text-slate-800'">
                                Log Pemesanan
                            </button>
                        </div>
                    </div>

                    <!-- VIEW 1: GUEST DATABASE -->
                    <div x-show="adminGuestViewTab === 'tamu'" class="space-y-4">
                        <div class="bg-white p-4 rounded-2xl border border-slate-100 shadow-sm flex items-center justify-between gap-4">
                            <div class="relative w-80">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                                    <i data-lucide="search" class="w-4 h-4"></i>
                                </span>
                                <input type="text" 
                                       x-model="adminGuestSearch" 
                                       placeholder="Cari nama tamu, email, telepon..." 
                                       class="w-full pl-9 pr-4 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-1 focus:ring-wisma-gold focus:outline-none transition-all">
                            </div>
                            <div class="flex gap-2">
                                <button @click="adminGuestFilter = 'semua'" :class="adminGuestFilter === 'semua' ? 'bg-wisma-navy text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'" class="px-4 py-2 rounded-xl text-xs font-bold transition-all">Semua</button>
                                <button @click="adminGuestFilter = 'member'" :class="adminGuestFilter === 'member' ? 'bg-wisma-navy text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'" class="px-4 py-2 rounded-xl text-xs font-bold transition-all">Hanya Member</button>
                                <button @click="adminGuestFilter = 'menginap'" :class="adminGuestFilter === 'menginap' ? 'bg-wisma-navy text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'" class="px-4 py-2 rounded-xl text-xs font-bold transition-all">Sedang Menginap</button>
                            </div>
                        </div>

                        <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
                            <div class="mb-4 bg-blue-50/50 p-3 rounded-xl border border-blue-100/50">
                                <p class="text-xs text-slate-600 leading-relaxed">
                                    <i data-lucide="info" class="w-3.5 h-3.5 inline-block mr-1 -mt-0.5 text-blue-500"></i>
                                    <strong>Status Registrasi:</strong> Label <span class="text-indigo-600 font-bold">Member</span> menandakan bahwa akun tersebut sudah memiliki riwayat reservasi (pernah menginap). Sedangkan label <span class="text-slate-500 font-bold">Reguler</span> berarti pengguna baru mendaftar atau belum memiliki transaksi.
                                </p>
                            </div>

                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr class="bg-slate-50 border-b border-slate-100 text-[10px] text-slate-400 font-bold uppercase tracking-wider">
                                        <th class="py-4 px-6">ID / Nama Tamu</th>
                                        <th class="py-4 px-6">DPR ID / NIP</th>
                                        <th class="py-4 px-6">Kontak & Email</th>
                                        <th class="py-4 px-6">Kunjungan</th>
                                        <th class="py-4 px-6">Status Registrasi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 text-xs">
                                    <template x-for="g in filteredGuests()" :key="g.id">
                                        <tr class="hover:bg-slate-50/50 transition-all">
                                            <td class="py-4 px-6">
                                                <p class="font-bold text-slate-900" x-text="g.nama"></p>
                                                <p class="text-[10px] text-slate-400 mt-0.5" x-text="g.id"></p>
                                            </td>
                                            <td class="py-4 px-6 font-semibold text-slate-800" x-text="g.nip"></td>
                                            <td class="py-4 px-6">
                                                <p class="font-medium text-slate-800" x-text="g.phone"></p>
                                                <p class="text-[10px] text-slate-400 mt-0.5" x-text="g.email"></p>
                                            </td>
                                            <td class="py-4 px-6 font-bold text-slate-700" x-text="g.kunjungan + ' kali'"></td>
                                            <td class="py-4 px-6">
                                                <span class="px-2 py-0.5 rounded text-[9px] uppercase font-bold tracking-wide"
                                                      :class="g.status === 'Member' ? 'bg-indigo-100 text-indigo-700' : (g.status === 'Reguler' ? 'bg-slate-100 text-slate-600' : 'bg-red-100 text-red-700')"
                                                      x-text="g.status"></span>
                                            </td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- VIEW 2: RESERVATION LOGS -->
                    <div x-show="adminGuestViewTab === 'reservasi'" class="space-y-4">
                        <div class="bg-white p-4 rounded-2xl border border-slate-100 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
                            <div class="relative w-full md:w-96">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                                    <i data-lucide="search" class="w-4 h-4"></i>
                                </span>
                                <input type="text" 
                                       x-model="adminLogSearch" 
                                       placeholder="Cari kode booking, nama tamu, atau NIP..." 
                                       class="w-full pl-9 pr-4 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-1 focus:ring-wisma-gold focus:outline-none transition-all">
                            </div>
                            <div class="flex flex-wrap gap-2">
                                <button @click="adminLogFilter = 'semua'" :class="adminLogFilter === 'semua' ? 'bg-wisma-navy text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'" class="px-4 py-2 rounded-xl text-[10px] uppercase tracking-wide font-bold transition-all">Semua</button>
                                <button @click="adminLogFilter = 'pending'" :class="adminLogFilter === 'pending' ? 'bg-wisma-navy text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'" class="px-4 py-2 rounded-xl text-[10px] uppercase tracking-wide font-bold transition-all">Pending</button>
                                <button @click="adminLogFilter = 'lunas'" :class="adminLogFilter === 'lunas' ? 'bg-wisma-navy text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'" class="px-4 py-2 rounded-xl text-[10px] uppercase tracking-wide font-bold transition-all">Lunas</button>
                                <button @click="adminLogFilter = 'check in'" :class="adminLogFilter === 'check in' ? 'bg-wisma-navy text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'" class="px-4 py-2 rounded-xl text-[10px] uppercase tracking-wide font-bold transition-all">Check In</button>
                                <button @click="adminLogFilter = 'selesai'" :class="adminLogFilter === 'selesai' ? 'bg-wisma-navy text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'" class="px-4 py-2 rounded-xl text-[10px] uppercase tracking-wide font-bold transition-all">Selesai</button>
                                <button @click="adminLogFilter = 'dibatalkan'" :class="adminLogFilter === 'dibatalkan' ? 'bg-wisma-navy text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'" class="px-4 py-2 rounded-xl text-[10px] uppercase tracking-wide font-bold transition-all">Dibatalkan</button>
                            </div>
                        </div>

                        <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr class="bg-slate-50 border-b border-slate-100 text-[10px] text-slate-400 font-bold uppercase tracking-wider">
                                        <th class="py-4 px-6">No. | Kode</th>
                                        <th class="py-4 px-6">Nama Tamu</th>
                                        <th class="py-4 px-6">Fasilitas / Unit</th>
                                        <th class="py-4 px-6">Masa Inap</th>
                                        <th class="py-4 px-6">Total Tagihan</th>
                                        <th class="py-4 px-6">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 text-xs text-slate-800">
                                    <template x-for="b in bookings.filter(x => {
                                        const matchesSearch = x.nama.toLowerCase().includes(adminLogSearch.toLowerCase()) || x.nip.toLowerCase().includes(adminLogSearch.toLowerCase()) || x.booking_code.toLowerCase().includes(adminLogSearch.toLowerCase());
                                        const matchesStatus = adminLogFilter === 'semua' || x.status.toLowerCase() === adminLogFilter.toLowerCase();
                                        return matchesSearch && matchesStatus;
                                    })" :key="b.id">
                                        <tr class="hover:bg-slate-50/50 transition-all">
                                        <td class="py-4 px-6">
                                            <p class="font-bold text-slate-900" x-text="'#' + b.id"></p>
                                            <p class="text-[9px] text-slate-400 mt-0.5" x-text="b.booking_code"></p>
                                        </td>
                                        <td class="py-4 px-6">
                                            <p class="font-semibold" x-text="b.nama"></p>
                                            <p class="text-[9px] text-slate-400 mt-0.5" x-text="'NIP: ' + b.nip"></p>
                                        </td>
                                        <td class="py-4 px-6" x-text="b.unit_name"></td>
                                        <td class="py-4 px-6">
                                            <p class="font-medium" x-text="formatIndoDate(b.check_in) + ' -'"></p>
                                            <p class="font-medium" x-text="formatIndoDate(b.check_out)"></p>
                                        </td>
                                        <td class="py-4 px-6 font-bold" x-text="formatRupiah(b.total_price)"></td>
                                        <td class="py-4 px-6">
                                                  <span class="px-2 py-0.5 rounded text-[9px] uppercase font-bold tracking-wide"
                                                        :class="{
                                                            'bg-emerald-100 text-emerald-700': b.status === 'Lunas',
                                                            'bg-blue-100 text-blue-700': b.status === 'Check In',
                                                            'bg-slate-100 text-slate-600': b.status === 'Selesai',
                                                            'bg-amber-100 text-amber-700': b.status === 'Pending',
                                                            'bg-red-100 text-red-700': b.status === 'Dibatalkan' || b.status === 'Cancelled'
                                                        }"
                                                        x-text="b.status === 'Check In' ? 'Aktif Menginap' : b.status"></span>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- 4. REPORTS VIEW -->
                <div x-show="currentTab === 'admin_reports'" class="space-y-6" x-cloak>
                    <!-- Header -->
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 no-print">
                        <div>
                            <h1 class="text-2xl font-outfit font-extrabold text-slate-900">Laporan Keuangan & Okupansi Wisma</h1>
                            <p class="text-xs text-slate-500">Statistik performa tingkat hunian dan audit penerimaan dana DIPA.</p>
                        </div>
                        <button @click="printReport()" class="px-5 py-2.5 bg-[#0B1A30] hover:bg-slate-800 text-white font-bold text-xs rounded-xl shadow-lg transition-all flex items-center gap-1.5">
                            <i data-lucide="printer" class="w-4 h-4"></i> Cetak Laporan
                        </button>
                    </div>

                    <!-- Print-only Title Header -->
                    <div class="hidden print:block text-center border-b border-slate-800 pb-4 mb-6">
                        <h2 class="text-xl font-bold font-outfit uppercase tracking-wider">LAPORAN OKUPANSI & REKAPITULASI PENGGUNAAN WISMA</h2>
                        <p class="text-xs text-slate-600">Sistem Informasi & Manajemen Wisma DPR RI Kopo</p>
                        <p class="text-[10px] text-slate-500 mt-1" x-text="'Dicetak pada: ' + new Date().toLocaleDateString('id-ID', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' })"></p>
                    </div>

                    <!-- Laporan Cards -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="bg-white border border-slate-100 rounded-3xl p-6 shadow-sm space-y-4">
                            <h3 class="text-sm font-bold text-slate-900">Performa Hunian Kamar & Ruang</h3>
                            <div class="h-48 flex items-end justify-between gap-4 pt-6 border-b border-slate-100 pb-4">
                                <template x-for="(m, idx) in getMonthlyChart()" :key="idx">
                                    <div class="w-full bg-slate-100 rounded-t-lg h-32 relative group flex flex-col justify-end">
                                        <div class="w-full bg-[#0B1A30] rounded-t-lg transition-all" :style="'height: ' + m.percent + '%'"></div>
                                        <span class="text-[8px] text-slate-400 absolute -bottom-5 w-full text-center block" x-text="m.label"></span>
                                    </div>
                                </template>
                            </div>
                            <p class="text-[10px] text-slate-500 mt-2">Grafik dinamis ini menghitung tren kepadatan pemesanan (okupansi) selama 3 bulan terakhir berdasarkan data di sistem.</p>
                        </div>

                        <div class="bg-white border border-slate-100 rounded-3xl p-6 shadow-sm space-y-4">
                            <h3 class="text-sm font-bold text-slate-900">Persentase Hunian Berdasarkan Tipe</h3>
                            <div class="space-y-3 pt-4 text-xs">
                                <template x-for="t in getTypeOccupancy()" :key="t.label">
                                    <div class="space-y-1">
                                        <div class="flex justify-between font-bold text-slate-800"><span x-text="t.label"></span><span x-text="t.percent + '%'"></span></div>
                                        <div class="w-full h-2 bg-slate-100 rounded-full"><div class="bg-wisma-navy h-full rounded-full transition-all" :style="'width: ' + t.percent + '%'"></div></div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>

                    <!-- Rekapitulasi Pernah Menginap (Kamar) -->
                    <div class="bg-white border border-slate-100 rounded-3xl p-6 shadow-sm space-y-4 printable-report">
                        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                            <div>
                                <h3 class="text-sm font-bold text-slate-900">Rekapitulasi Tamu Pernah Menginap (Kamar)</h3>
                                <p class="text-[11px] text-slate-500">Daftar riwayat tamu yang sudah pernah menginap atau sedang aktif menginap.</p>
                            </div>
                            <span class="px-2.5 py-1 bg-[#0B1A30] text-wisma-gold text-[10px] font-bold rounded-xl shadow-sm no-print" x-text="bookings.filter(b => {
                                const isKamar = !(b.unit_name.toLowerCase().includes('rapat') || b.unit_name.toLowerCase().includes('hall') || b.unit_name.toLowerCase().includes('auditorium'));
                                const matchesActive = b.status === 'Selesai' || b.status === 'Check In';
                                const matchesSearch = b.nama.toLowerCase().includes(reportGuestSearchKamar.toLowerCase()) || b.nip.toLowerCase().includes(reportGuestSearchKamar.toLowerCase());
                                const matchesStatus = reportGuestStatusKamar === 'semua' || b.status === reportGuestStatusKamar;
                                const matchesStartDate = reportStartDateKamar === '' || new Date(b.check_in) >= new Date(reportStartDateKamar);
                                const matchesEndDate = reportEndDateKamar === '' || new Date(b.check_in) <= new Date(reportEndDateKamar);
                                return isKamar && matchesActive && matchesSearch && matchesStatus && matchesStartDate && matchesEndDate;
                            }).length + ' Tamu'"></span>
                        </div>

                        <!-- Filters for Kamar -->
                        <div class="flex flex-col md:flex-row gap-4 items-center justify-between no-print pt-2 pb-2">
                            <div class="relative w-full md:w-80">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                                    <i data-lucide="search" class="w-4 h-4"></i>
                                </span>
                                <input type="text" 
                                       x-model="reportGuestSearchKamar" 
                                       placeholder="Cari nama tamu atau NIP..." 
                                       class="w-full pl-9 pr-4 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-1 focus:ring-wisma-gold focus:outline-none transition-all">
                            </div>
                            <div class="flex gap-2 w-full md:w-auto justify-end">
                                <input type="date" x-model="reportStartDateKamar" class="text-xs bg-slate-50 border border-slate-200 rounded-xl p-2 focus:ring-1 focus:ring-wisma-gold focus:outline-none w-full md:w-auto text-slate-500">
                                <span class="text-[10px] text-slate-400 self-center font-bold">S/D</span>
                                <input type="date" x-model="reportEndDateKamar" class="text-xs bg-slate-50 border border-slate-200 rounded-xl p-2 focus:ring-1 focus:ring-wisma-gold focus:outline-none w-full md:w-auto text-slate-500">
                                <select x-model="reportGuestStatusKamar" class="text-xs bg-slate-50 border border-slate-200 rounded-xl p-2 focus:ring-1 focus:ring-wisma-gold focus:outline-none w-full md:w-auto">
                                    <option value="semua">Semua Status</option>
                                    <option value="Check In">Menginap (Check In)</option>
                                    <option value="Selesai">Selesai</option>
                                </select>
                            </div>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse text-xs">
                                <thead>
                                    <tr class="border-b border-slate-100 text-[10px] text-slate-400 font-bold uppercase tracking-wider">
                                        <th class="py-3 px-4">No. | Kode</th>
                                        <th class="py-3 px-4">Nama Tamu & NIP</th>
                                        <th class="py-3 px-4">Unit Kamar</th>
                                        <th class="py-3 px-4">Tanggal Menginap</th>
                                        <th class="py-3 px-4">Durasi</th>
                                        <th class="py-3 px-4">Total Tagihan</th>
                                        <th class="py-3 px-4 text-right">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <template x-for="b in bookings.filter(b => {
                                        const isKamar = !(b.unit_name.toLowerCase().includes('rapat') || b.unit_name.toLowerCase().includes('hall') || b.unit_name.toLowerCase().includes('auditorium'));
                                        const matchesActive = b.status === 'Selesai' || b.status === 'Check In';
                                        const matchesSearch = b.nama.toLowerCase().includes(reportGuestSearchKamar.toLowerCase()) || b.nip.toLowerCase().includes(reportGuestSearchKamar.toLowerCase());
                                        const matchesStatus = reportGuestStatusKamar === 'semua' || b.status === reportGuestStatusKamar;
                                        const matchesStartDate = reportStartDateKamar === '' || new Date(b.check_in) >= new Date(reportStartDateKamar);
                                        const matchesEndDate = reportEndDateKamar === '' || new Date(b.check_in) <= new Date(reportEndDateKamar);
                                        return isKamar && matchesActive && matchesSearch && matchesStatus && matchesStartDate && matchesEndDate;
                                    })" :key="b.id">
                                        <tr class="border-b border-slate-50 hover:bg-slate-50/50 transition-colors">
                                            <td class="py-3 px-4">
                                                <p class="font-bold text-slate-900" x-text="'#' + b.id"></p>
                                                <p class="text-[9px] text-slate-400 mt-0.5" x-text="b.booking_code"></p>
                                            </td>
                                            <td class="py-3 px-4">
                                                <p class="font-bold text-slate-800" x-text="b.nama"></p>
                                                <p class="text-[10px] text-slate-400 mt-0.5" x-text="'NIP: ' + b.nip"></p>
                                            </td>
                                            <td class="py-3 px-4">
                                                <p class="font-bold text-slate-800" x-text="b.unit_name"></p>
                                                <p class="text-[9px] text-slate-400 mt-0.5" x-text="b.unit_location"></p>
                                            </td>
                                            <td class="py-3 px-4" x-text="formatIndoDate(b.check_in) + ' s/d ' + formatIndoDate(b.check_out)"></td>
                                            <td class="py-3 px-4" x-text="b.nights + ' Malam'"></td>
                                            <td class="py-3 px-4 font-semibold text-wisma-gold" x-text="formatRupiah(b.total_price)"></td>
                                            <td class="py-3 px-4 text-right">
                                                <span class="px-2 py-0.5 rounded text-[9px] uppercase font-bold"
                                                      :class="b.status === 'Check In' ? 'bg-blue-100 text-blue-700' : 'bg-emerald-100 text-emerald-700'"
                                                      x-text="b.status === 'Check In' ? 'Menginap' : 'Selesai'"></span>
                                            </td>
                                        </tr>
                                    </template>
                                    <tr x-show="bookings.filter(b => {
                                        const isKamar = !(b.unit_name.toLowerCase().includes('rapat') || b.unit_name.toLowerCase().includes('hall') || b.unit_name.toLowerCase().includes('auditorium'));
                                        const matchesActive = b.status === 'Selesai' || b.status === 'Check In';
                                        const matchesSearch = b.nama.toLowerCase().includes(reportGuestSearchKamar.toLowerCase()) || b.nip.toLowerCase().includes(reportGuestSearchKamar.toLowerCase());
                                        const matchesStatus = reportGuestStatusKamar === 'semua' || b.status === reportGuestStatusKamar;
                                        const matchesStartDate = reportStartDateKamar === '' || new Date(b.check_in) >= new Date(reportStartDateKamar);
                                        const matchesEndDate = reportEndDateKamar === '' || new Date(b.check_in) <= new Date(reportEndDateKamar);
                                        return isKamar && matchesActive && matchesSearch && matchesStatus && matchesStartDate && matchesEndDate;
                                    }).length === 0">
                                        <td colspan="7" class="text-center py-6 text-slate-400">Tidak ada riwayat menginap kamar.</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Rekapitulasi Riwayat Ruang Rapat -->
                    <div class="bg-white border border-slate-100 rounded-3xl p-6 shadow-sm space-y-4 printable-report page-break-inside-avoid">
                        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                            <div>
                                <h3 class="text-sm font-bold text-slate-900">Rekapitulasi Pemesanan Ruang Rapat</h3>
                                <p class="text-[11px] text-slate-500">Daftar riwayat pemesanan unit ruang rapat atau aula pertemuan.</p>
                            </div>
                            <span class="px-2.5 py-1 bg-[#0B1A30] text-wisma-gold text-[10px] font-bold rounded-xl shadow-sm no-print" x-text="bookings.filter(b => {
                                const isRapat = b.unit_name.toLowerCase().includes('rapat') || b.unit_name.toLowerCase().includes('hall') || b.unit_name.toLowerCase().includes('auditorium');
                                const matchesActive = b.status === 'Selesai' || b.status === 'Check In';
                                const matchesSearch = b.nama.toLowerCase().includes(reportGuestSearchRapat.toLowerCase()) || b.nip.toLowerCase().includes(reportGuestSearchRapat.toLowerCase());
                                const matchesStatus = reportGuestStatusRapat === 'semua' || b.status === reportGuestStatusRapat;
                                const matchesStartDate = reportStartDateRapat === '' || new Date(b.check_in) >= new Date(reportStartDateRapat);
                                const matchesEndDate = reportEndDateRapat === '' || new Date(b.check_in) <= new Date(reportEndDateRapat);
                                return isRapat && matchesActive && matchesSearch && matchesStatus && matchesStartDate && matchesEndDate;
                            }).length + ' Ruangan'"></span>
                        </div>

                        <!-- Filters for Rapat -->
                        <div class="flex flex-col md:flex-row gap-4 items-center justify-between no-print pt-2 pb-2">
                            <div class="relative w-full md:w-80">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                                    <i data-lucide="search" class="w-4 h-4"></i>
                                </span>
                                <input type="text" 
                                       x-model="reportGuestSearchRapat" 
                                       placeholder="Cari nama pemesan atau NIP..." 
                                       class="w-full pl-9 pr-4 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-1 focus:ring-wisma-gold focus:outline-none transition-all">
                            </div>
                            <div class="flex gap-2 w-full md:w-auto justify-end">
                                <input type="date" x-model="reportStartDateRapat" class="text-xs bg-slate-50 border border-slate-200 rounded-xl p-2 focus:ring-1 focus:ring-wisma-gold focus:outline-none w-full md:w-auto text-slate-500">
                                <span class="text-[10px] text-slate-400 self-center font-bold">S/D</span>
                                <input type="date" x-model="reportEndDateRapat" class="text-xs bg-slate-50 border border-slate-200 rounded-xl p-2 focus:ring-1 focus:ring-wisma-gold focus:outline-none w-full md:w-auto text-slate-500">
                                <select x-model="reportGuestStatusRapat" class="text-xs bg-slate-50 border border-slate-200 rounded-xl p-2 focus:ring-1 focus:ring-wisma-gold focus:outline-none w-full md:w-auto">
                                    <option value="semua">Semua Status</option>
                                    <option value="Check In">Aktif (Check In)</option>
                                    <option value="Selesai">Selesai</option>
                                </select>
                            </div>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse text-xs">
                                <thead>
                                    <tr class="border-b border-slate-100 text-[10px] text-slate-400 font-bold uppercase tracking-wider">
                                        <th class="py-3 px-4">No. | Kode</th>
                                        <th class="py-3 px-4">Nama Pemesan & NIP</th>
                                        <th class="py-3 px-4">Ruang Rapat</th>
                                        <th class="py-3 px-4">Tanggal Penggunaan</th>
                                        <th class="py-3 px-4">Durasi</th>
                                        <th class="py-3 px-4">Total Tagihan</th>
                                        <th class="py-3 px-4 text-right">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <template x-for="b in bookings.filter(b => {
                                        const isRapat = b.unit_name.toLowerCase().includes('rapat') || b.unit_name.toLowerCase().includes('hall') || b.unit_name.toLowerCase().includes('auditorium');
                                        const matchesActive = b.status === 'Selesai' || b.status === 'Check In';
                                        const matchesSearch = b.nama.toLowerCase().includes(reportGuestSearchRapat.toLowerCase()) || b.nip.toLowerCase().includes(reportGuestSearchRapat.toLowerCase());
                                        const matchesStatus = reportGuestStatusRapat === 'semua' || b.status === reportGuestStatusRapat;
                                        const matchesStartDate = reportStartDateRapat === '' || new Date(b.check_in) >= new Date(reportStartDateRapat);
                                        const matchesEndDate = reportEndDateRapat === '' || new Date(b.check_in) <= new Date(reportEndDateRapat);
                                        return isRapat && matchesActive && matchesSearch && matchesStatus && matchesStartDate && matchesEndDate;
                                    })" :key="b.id">
                                        <tr class="border-b border-slate-50 hover:bg-slate-50/50 transition-colors">
                                            <td class="py-3 px-4">
                                                <p class="font-bold text-slate-900" x-text="'#' + b.id"></p>
                                                <p class="text-[9px] text-slate-400 mt-0.5" x-text="b.booking_code"></p>
                                            </td>
                                            <td class="py-3 px-4">
                                                <p class="font-bold text-slate-800" x-text="b.nama"></p>
                                                <p class="text-[10px] text-slate-400 mt-0.5" x-text="'NIP: ' + b.nip"></p>
                                            </td>
                                            <td class="py-3 px-4">
                                                <p class="font-bold text-slate-800" x-text="b.unit_name"></p>
                                                <p class="text-[9px] text-slate-400 mt-0.5" x-text="b.unit_location"></p>
                                            </td>
                                            <td class="py-3 px-4" x-text="formatIndoDate(b.check_in) + ' s/d ' + formatIndoDate(b.check_out)"></td>
                                            <td class="py-3 px-4" x-text="b.nights + ' Hari'"></td>
                                            <td class="py-3 px-4 font-semibold text-wisma-gold" x-text="formatRupiah(b.total_price)"></td>
                                            <td class="py-3 px-4 text-right">
                                                <span class="px-2 py-0.5 rounded text-[9px] uppercase font-bold"
                                                      :class="b.status === 'Check In' ? 'bg-blue-100 text-blue-700' : 'bg-emerald-100 text-emerald-700'"
                                                      x-text="b.status === 'Check In' ? 'Aktif' : 'Selesai'"></span>
                                            </td>
                                        </tr>
                                    </template>
                                    <tr x-show="bookings.filter(b => {
                                        const isRapat = b.unit_name.toLowerCase().includes('rapat') || b.unit_name.toLowerCase().includes('hall') || b.unit_name.toLowerCase().includes('auditorium');
                                        const matchesActive = b.status === 'Selesai' || b.status === 'Check In';
                                        const matchesSearch = b.nama.toLowerCase().includes(reportGuestSearchRapat.toLowerCase()) || b.nip.toLowerCase().includes(reportGuestSearchRapat.toLowerCase());
                                        const matchesStatus = reportGuestStatusRapat === 'semua' || b.status === reportGuestStatusRapat;
                                        const matchesStartDate = reportStartDateRapat === '' || new Date(b.check_in) >= new Date(reportStartDateRapat);
                                        const matchesEndDate = reportEndDateRapat === '' || new Date(b.check_in) <= new Date(reportEndDateRapat);
                                        return isRapat && matchesActive && matchesSearch && matchesStatus && matchesStartDate && matchesEndDate;
                                    }).length === 0">
                                        <td colspan="7" class="text-center py-6 text-slate-400">Tidak ada riwayat pemesanan ruang rapat.</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Signature Area for Printing -->
                        <div class="hidden print:grid grid-cols-2 gap-8 pt-12 text-xs text-left">
                            <div></div>
                            <div class="text-center space-y-12">
                                <div>
                                    <p>Mengetahui,</p>
                                    <p class="font-bold">Administrator Wisma DPR RI</p>
                                </div>
                                <div>
                                    <p class="font-bold underline" x-text="profile.nama"></p>
                                    <p class="text-[10px] text-slate-500">NIP. 198510122010031004</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- SETTINGS VIEW -->
                <div x-show="currentTab === 'admin_settings'" class="space-y-6 fade-in" x-cloak>
                    <div>
                        <h1 class="text-2xl font-outfit font-extrabold text-slate-900">Pengaturan</h1>
                        <p class="text-xs text-slate-500">Kelola preferensi akun dan informasi pribadi Anda.</p>
                    </div>

                    <div class="flex gap-6">
                        <!-- Settings Sidebar -->
                        <div class="w-56 shrink-0 space-y-1">
                            <button @click="settingsTab = 'profil'"
                                    class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-left text-xs font-semibold transition-all"
                                    :class="settingsTab === 'profil' ? 'bg-wisma-navy text-white shadow-md' : 'text-slate-600 hover:bg-slate-100'">
                                <i data-lucide="user-circle" class="w-4 h-4"></i> Profil Saya
                            </button>
                            <button @click="settingsTab = 'kontak'"
                                    class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-left text-xs font-semibold transition-all"
                                    :class="settingsTab === 'kontak' ? 'bg-wisma-navy text-white shadow-md' : 'text-slate-600 hover:bg-slate-100'">
                                <i data-lucide="mail" class="w-4 h-4"></i> Email &amp; Telepon
                            </button>
                            <button @click="settingsTab = 'password'"
                                    class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-left text-xs font-semibold transition-all"
                                    :class="settingsTab === 'password' ? 'bg-wisma-navy text-white shadow-md' : 'text-slate-600 hover:bg-slate-100'">
                                <i data-lucide="lock" class="w-4 h-4"></i> Ganti Kata Sandi
                            </button>
                        </div>

                        <!-- Settings Content -->
                        <div class="flex-1">

                            <!-- PROFIL SAYA -->
                            <div x-show="settingsTab === 'profil'" class="bg-white rounded-3xl border border-slate-100 shadow-sm p-8 space-y-6">
                                <div class="flex items-center gap-4 pb-6 border-b border-slate-100">
                                    <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-wisma-navy to-slate-700 text-wisma-gold flex items-center justify-center font-bold text-xl font-outfit shadow-lg">
                                        KW
                                    </div>
                                    <div>
                                        <h2 class="text-base font-bold text-slate-900 font-outfit" x-text="settingsProfile.nama"></h2>
                                        <p class="text-xs text-slate-500 mt-0.5" x-text="settingsProfile.jabatan"></p>
                                        <span class="inline-block mt-1.5 px-2 py-0.5 bg-indigo-50 text-indigo-700 text-[10px] font-bold rounded-md uppercase tracking-wide">Koordinator Wisma</span>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                    <div class="space-y-1.5">
                                        <label class="text-[10px] text-slate-500 font-bold uppercase tracking-wide block">Nama Lengkap</label>
                                        <input type="text" x-model="settingsProfile.nama" class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl p-3 focus:ring-1 focus:ring-wisma-gold focus:bg-white focus:outline-none transition-all">
                                    </div>
                                    <div class="space-y-1.5">
                                        <label class="text-[10px] text-slate-500 font-bold uppercase tracking-wide block">Jabatan / Role</label>
                                        <input type="text" x-model="settingsProfile.jabatan" class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl p-3 focus:ring-1 focus:ring-wisma-gold focus:bg-white focus:outline-none transition-all">
                                    </div>
                                    <div class="space-y-1.5">
                                        <label class="text-[10px] text-slate-500 font-bold uppercase tracking-wide block">NIP</label>
                                        <input type="text" x-model="settingsProfile.nip" class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl p-3 focus:ring-1 focus:ring-wisma-gold focus:bg-white focus:outline-none transition-all">
                                    </div>
                                    <div class="space-y-1.5">
                                        <label class="text-[10px] text-slate-500 font-bold uppercase tracking-wide block">Unit / Instansi</label>
                                        <input type="text" x-model="settingsProfile.instansi" class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl p-3 focus:ring-1 focus:ring-wisma-gold focus:bg-white focus:outline-none transition-all">
                                    </div>
                                </div>

                                <div class="flex justify-end pt-2">
                                    <button @click="saveSettingsProfile()" class="px-6 py-2.5 bg-wisma-navy hover:bg-slate-800 text-white font-bold text-xs rounded-xl shadow-md transition-colors flex items-center gap-1.5">
                                        <i data-lucide="save" class="w-3.5 h-3.5"></i> Simpan Perubahan
                                    </button>
                                </div>
                            </div>

                            <!-- EMAIL & TELEPON -->
                            <div x-show="settingsTab === 'kontak'" class="bg-white rounded-3xl border border-slate-100 shadow-sm p-8 space-y-6" x-cloak>
                                <div class="pb-4 border-b border-slate-100">
                                    <h2 class="text-sm font-bold text-slate-900">Email &amp; Nomor Telepon</h2>
                                    <p class="text-xs text-slate-400 mt-1">Informasi kontak digunakan untuk notifikasi sistem resmi.</p>
                                </div>

                                <div class="space-y-5">
                                    <div class="space-y-1.5">
                                        <label class="text-[10px] text-slate-500 font-bold uppercase tracking-wide block">Alamat Email</label>
                                        <div class="relative">
                                            <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                                <i data-lucide="mail" class="w-4 h-4"></i>
                                            </span>
                                            <input type="email" x-model="settingsContact.email" class="w-full pl-10 pr-4 text-xs bg-slate-50 border border-slate-200 rounded-xl p-3 focus:ring-1 focus:ring-wisma-gold focus:bg-white focus:outline-none transition-all">
                                        </div>
                                    </div>
                                    <div class="space-y-1.5">
                                        <label class="text-[10px] text-slate-500 font-bold uppercase tracking-wide block">Nomor Telepon</label>
                                        <div class="relative">
                                            <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                                <i data-lucide="phone" class="w-4 h-4"></i>
                                            </span>
                                            <input type="tel" x-model="settingsContact.telepon" class="w-full pl-10 pr-4 text-xs bg-slate-50 border border-slate-200 rounded-xl p-3 focus:ring-1 focus:ring-wisma-gold focus:bg-white focus:outline-none transition-all">
                                        </div>
                                    </div>
                                </div>

                                <div class="flex justify-end pt-2">
                                    <button @click="saveSettingsContact()" class="px-6 py-2.5 bg-wisma-navy hover:bg-slate-800 text-white font-bold text-xs rounded-xl shadow-md transition-colors flex items-center gap-1.5">
                                        <i data-lucide="save" class="w-3.5 h-3.5"></i> Simpan Perubahan
                                    </button>
                                </div>
                            </div>

                            <!-- GANTI KATA SANDI -->
                            <div x-show="settingsTab === 'password'" class="bg-white rounded-3xl border border-slate-100 shadow-sm p-8 space-y-6" x-cloak>
                                <div class="pb-4 border-b border-slate-100">
                                    <h2 class="text-sm font-bold text-slate-900">Ganti Kata Sandi</h2>
                                    <p class="text-xs text-slate-400 mt-1">Pastikan kata sandi baru kuat dan tidak mudah ditebak.</p>
                                </div>

                                <div class="space-y-5 max-w-md">
                                    <div class="space-y-1.5">
                                        <label class="text-[10px] text-slate-500 font-bold uppercase tracking-wide block">Kata Sandi Saat Ini</label>
                                        <div class="relative">
                                            <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                                <i data-lucide="lock" class="w-4 h-4"></i>
                                            </span>
                                            <input :type="settingsPasswordVisible.current ? 'text' : 'password'" x-model="settingsPassword.current" placeholder="••••••••" class="w-full pl-10 pr-10 text-xs bg-slate-50 border border-slate-200 rounded-xl p-3 focus:ring-1 focus:ring-wisma-gold focus:bg-white focus:outline-none transition-all">
                                            <button type="button" @click="settingsPasswordVisible.current = !settingsPasswordVisible.current" class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600">
                                                <i :data-lucide="settingsPasswordVisible.current ? 'eye-off' : 'eye'" class="w-4 h-4"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="space-y-1.5">
                                        <label class="text-[10px] text-slate-500 font-bold uppercase tracking-wide block">Kata Sandi Baru</label>
                                        <div class="relative">
                                            <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                                <i data-lucide="key" class="w-4 h-4"></i>
                                            </span>
                                            <input :type="settingsPasswordVisible.new ? 'text' : 'password'" x-model="settingsPassword.new" placeholder="Min. 6 karakter" class="w-full pl-10 pr-10 text-xs bg-slate-50 border border-slate-200 rounded-xl p-3 focus:ring-1 focus:ring-wisma-gold focus:bg-white focus:outline-none transition-all">
                                            <button type="button" @click="settingsPasswordVisible.new = !settingsPasswordVisible.new" class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600">
                                                <i :data-lucide="settingsPasswordVisible.new ? 'eye-off' : 'eye'" class="w-4 h-4"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="space-y-1.5">
                                        <label class="text-[10px] text-slate-500 font-bold uppercase tracking-wide block">Konfirmasi Kata Sandi Baru</label>
                                        <div class="relative">
                                            <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                                <i data-lucide="key" class="w-4 h-4"></i>
                                            </span>
                                            <input :type="settingsPasswordVisible.confirm ? 'text' : 'password'" x-model="settingsPassword.confirm" placeholder="Ulangi kata sandi baru" class="w-full pl-10 pr-10 text-xs bg-slate-50 border border-slate-200 rounded-xl p-3 focus:ring-1 focus:ring-wisma-gold focus:bg-white focus:outline-none transition-all">
                                            <button type="button" @click="settingsPasswordVisible.confirm = !settingsPasswordVisible.confirm" class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600">
                                                <i :data-lucide="settingsPasswordVisible.confirm ? 'eye-off' : 'eye'" class="w-4 h-4"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <div class="pt-2 max-w-md">
                                    <div x-show="settingsPassword.new && settingsPassword.confirm && settingsPassword.new !== settingsPassword.confirm" class="flex items-center gap-2 text-xs text-red-600 bg-red-50 rounded-xl p-3 mb-4">
                                        <i data-lucide="alert-circle" class="w-4 h-4 shrink-0"></i> Kata sandi baru dan konfirmasi tidak cocok.
                                    </div>
                                    <button @click="saveSettingsPassword()" class="px-6 py-2.5 bg-wisma-navy hover:bg-slate-800 text-white font-bold text-xs rounded-xl shadow-md transition-colors flex items-center gap-1.5">
                                        <i data-lucide="shield-check" class="w-3.5 h-3.5"></i> Perbarui Kata Sandi
                                    </button>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

            </main>
        </div>
    </div>

    <!-- APP SCRIPT STATE MANAGEMENT -->
    <script>
        const API_URL = '{{ env('BACKEND_API_URL', 'http://localhost:8000/api') }}';

        function wismaApp() {
            return {
                isLoggedIn: false,
                isLoading: false,
                passwordVisible: false,
                loginForm: {
                    role: 'admin',
                    email: 'koordinator@wisma.dpr.go.id',
                    password: 'password'
                },

                currentTab: 'admin_dashboard',
                
                adminManagementSearch: '',
                adminManagementSubTab: 'Buah',
                
                adminGuestSearch: '',
                adminLogSearch: '',
                adminLogFilter: 'semua',
                adminGuestFilter: 'semua',
                adminGuestViewTab: 'tamu',

                reportGuestSearchKamar: '',
                reportGuestStatusKamar: 'semua',
                reportStartDateKamar: '',
                reportEndDateKamar: '',
                reportGuestSearchRapat: '',
                reportGuestStatusRapat: 'semua',
                reportStartDateRapat: '',
                reportEndDateRapat: '',
                
                crudModalOpen: false,
                crudAction: 'create',
                crudType: 'Buah',
                crudForm: {
                    id: null,
                    name: '',
                    type: 'Buah',
                    gedung: 'Wisma',
                    lantai: 'Area Bawah',
                    capacity: 2,
                    price: 387000,
                    unit: 'night',
                    luas: '24',
                    bed: 'Queen Size',
                    status: 'READY',
                    photo: '',
                    description: ''
                },
                crudPhotoFile: null,

                toasts: [],
                toastCount: 0,

                profile: {
                    id: null,
                    role: 'koordinator_wisma',
                    nama: '',
                    role_label: 'Koordinator Wisma',
                    instansi: 'Wisma DPR RI'
                },

                // Settings state
                settingsTab: 'profil',
                settingsProfile: {
                    nama: '',
                    jabatan: 'Koordinator Wisma',
                    nip: '',
                    instansi: 'Wisma DPR RI'
                },
                settingsContact: {
                    email: '',
                    telepon: ''
                },
                settingsPassword: {
                    current: '',
                    new: '',
                    confirm: ''
                },
                settingsPasswordVisible: { current: false, new: false, confirm: false },

                // Shared data
                facilities: [],
                bookings: [],
                guests: [],
                complaints: [],

                // ==========================================
                // HELPER: API CALL
                // ==========================================
                async apiCall(method, path, body = null, isFormData = false) {
                    const token = localStorage.getItem('wisma_token');
                    const headers = {
                        'Accept': 'application/json',
                        ...(token ? { 'Authorization': 'Bearer ' + token } : {}),
                        ...(!isFormData ? { 'Content-Type': 'application/json' } : {})
                    };
                    const opts = {
                        method,
                        headers,
                        ...(body ? { body: isFormData ? body : JSON.stringify(body) } : {})
                    };
                    const res = await fetch(API_URL + path, opts);
                    return res.json();
                },

                // ==========================================
                // INIT
                // ==========================================
                async initApp() {
                    const token = localStorage.getItem('wisma_token');
                    if (token) {
                        const me = await this.apiCall('GET', '/me');
                        if (me.success) {
                            this.fillProfile(me.data);
                            this.isLoggedIn = true;
                            await this.loadFacilitiesFromApi();
                            await this.loadBookingsFromApi();
                            await this.loadGuestsFromApi();
                        } else {
                            localStorage.removeItem('wisma_token');
                        }
                    }
                    
                    // Re-render icons when filters change
                    this.$watch('adminManagementSearch', () => setTimeout(() => { if (window.lucide) window.lucide.createIcons(); }, 50));
                    this.$watch('adminManagementFilter', () => setTimeout(() => { if (window.lucide) window.lucide.createIcons(); }, 50));
                    this.$watch('adminGuestSearch', () => setTimeout(() => { if (window.lucide) window.lucide.createIcons(); }, 50));
                    this.$watch('adminLogSearch', () => setTimeout(() => { if (window.lucide) window.lucide.createIcons(); }, 50));
                    this.$watch('reportGuestSearchKamar', () => setTimeout(() => { if (window.lucide) window.lucide.createIcons(); }, 50));
                    this.$watch('reportGuestSearchRapat', () => setTimeout(() => { if (window.lucide) window.lucide.createIcons(); }, 50));

                    setTimeout(() => {
                        if (window.lucide) window.lucide.createIcons();
                    }, 100);
                },

                fillProfile(user) {
                    this.profile.id       = user.id;
                    this.profile.role     = user.role;
                    this.profile.nama     = user.name;
                    this.profile.instansi = user.instansi || 'Wisma DPR RI';
                    this.settingsProfile.nama     = user.name;
                    this.settingsProfile.nip      = user.nip || '';
                    this.settingsProfile.instansi = user.instansi || 'Wisma DPR RI';
                    this.settingsContact.email    = user.email;
                    this.settingsContact.telepon  = user.phone || '';
                },

                async loadFacilitiesFromApi() {
                    try {
                        const res = await this.apiCall('GET', '/facilities');
                        if (res.success) {
                            this.facilities = res.data.map(f => ({
                                ...f,
                                photo: f.photo ? (f.photo.startsWith('http') ? f.photo : 'http://localhost:8000' + f.photo) : '/images/bungalow_buah.jpg'
                            }));
                        }
                    } catch (e) {
                        console.error('Gagal memuat fasilitas dari API:', e);
                    }
                },

                async loadBookingsFromApi() {
                    try {
                        const res = await this.apiCall('GET', '/bookings');
                        if (res.success) {
                            this.bookings = res.data.map(b => ({
                                id: b.id,
                                booking_code: b.booking_code,
                                unit_name: b.facility.name,
                                check_in: b.check_in.substring(0,10),
                                check_out: b.check_out.substring(0,10),
                                nights: b.nights,
                                total_price: b.total_price,
                                status: (b.status === 'pending' ? 'Pending' : 
                                        (b.status === 'lunas' ? 'Lunas' : 
                                        (b.status === 'check_in' ? 'Check In' : 
                                        (b.status === 'cancelled' ? 'Dibatalkan' : 'Selesai')))),
                                nama: b.guest_name,
                                nip: b.guest_nip,
                            }));
                        }
                    } catch (e) {
                        console.error('Gagal memuat bookings:', e);
                    }
                },

                async loadGuestsFromApi() {
                    try {
                        const res = await this.apiCall('GET', '/reports/master-guests');
                        if (res.success) {
                            this.guests = res.data.map(g => ({
                                id: g.id,
                                nama: g.name,
                                nip: g.nip || '-',
                                instansi: g.instansi || '-',
                                email: g.email,
                                phone: g.phone || '-',
                                kunjungan: g.total_booking,
                                status: g.total_booking > 0 ? 'Member' : 'Reguler',
                                joinDate: g.last_visit_at ? g.last_visit_at.substring(0,10) : '2026-07-09'
                            }));
                        }
                    } catch (e) {
                        console.error('Gagal memuat guests:', e);
                    }
                },

                // ==========================================
                // LOGIN
                // ==========================================
                async login() {
                    if (this.isLoading) return;
                    if (!this.loginForm.email || !this.loginForm.password) {
                        this.addToast('Data Tidak Lengkap', 'Email dan Password tidak boleh kosong.', 'error');
                        return;
                    }
                    this.isLoading = true;
                    try {
                        const res = await this.apiCall('POST', '/login', {
                            email:    this.loginForm.email,
                            password: this.loginForm.password
                        });
                        if (res.success) {
                            if (res.data.user.role !== 'koordinator_wisma') {
                                this.addToast('Akses Ditolak', 'Portal ini hanya untuk Koordinator Wisma.', 'error');
                                return;
                            }
                            localStorage.setItem('wisma_token', res.data.token);
                            this.fillProfile(res.data.user);
                            this.isLoggedIn = true;
                            this.currentTab = 'admin_dashboard';
                            this.addToast('Login Berhasil', `Selamat datang kembali, ${this.profile.nama}.`, 'success');
                            await this.loadFacilitiesFromApi();
                            await this.loadBookingsFromApi();
                            await this.loadGuestsFromApi();
                        } else {
                            this.addToast('Login Gagal', res.message || 'Email atau password salah.', 'error');
                        }
                    } catch (e) {
                        this.addToast('Koneksi Gagal', 'Tidak dapat terhubung ke server backend.', 'error');
                    } finally {
                        this.isLoading = false;
                        setTimeout(() => { if (window.lucide) window.lucide.createIcons(); }, 50);
                    }
                },

                // ==========================================
                // LOGOUT
                // ==========================================
                async logout() {
                    try { await this.apiCall('POST', '/logout'); } catch (e) { /* ignore */ }
                    localStorage.removeItem('wisma_token');
                    this.isLoggedIn = false;
                    this.facilities = [];
                    this.addToast('Logout Sukses', 'Anda telah keluar dari sesi admin.', 'info');
                    setTimeout(() => { if (window.lucide) window.lucide.createIcons(); }, 50);
                },

                switchTab(tab) {
                    this.currentTab = tab;
                    setTimeout(() => {
                        if (window.lucide) {
                            window.lucide.createIcons();
                        }
                    }, 50);
                },

                async printReport() {
                    try {
                        this.addToast('Memproses', 'Sedang menyiapkan Laporan PDF...', 'info');
                        const token = localStorage.getItem('wisma_token');
                        
                        const params = new URLSearchParams();
                        if (this.reportStartDateKamar) params.append('start_date', this.reportStartDateKamar);
                        if (this.reportEndDateKamar) params.append('end_date', this.reportEndDateKamar);
                        if (this.reportGuestStatusKamar) params.append('status', this.reportGuestStatusKamar);
                        if (this.reportGuestSearchKamar) params.append('search', this.reportGuestSearchKamar);

                        const response = await fetch(`${API_URL}/reports/financial/export-pdf?${params.toString()}`, {
                            method: 'GET',
                            headers: {
                                'Authorization': `Bearer ${token}`,
                                'Accept': 'application/pdf'
                            }
                        });

                        if (!response.ok) {
                            this.addToast('Gagal', 'Gagal mengunduh laporan PDF.', 'error');
                            return;
                        }

                        const blob = await response.blob();
                        const url = window.URL.createObjectURL(blob);
                        
                        const a = document.createElement('a');
                        a.href = url;
                        a.download = `Laporan-Keuangan-Wisma-${new Date().toISOString().slice(0, 10)}.pdf`;
                        document.body.appendChild(a);
                        a.click();
                        document.body.removeChild(a);
                        window.URL.revokeObjectURL(url);
                        
                        this.addToast('Berhasil', 'Laporan berhasil diunduh.', 'success');
                    } catch (error) {
                        this.addToast('Gagal', 'Terjadi kesalahan saat mengunduh laporan.', 'error');
                    }
                },

                openAddModal(type) {
                    this.crudAction = 'create';
                    this.crudType = type;
                    this.crudForm = {
                        id: null,
                        name: '',
                        type: type,
                        gedung: 'Wisma',
                        lantai: type === 'Buah' ? 'Area Bawah' : (type === 'Bunga' ? 'Area Atas' : 'Area Bawah'),
                        capacity: type === 'Rapat' ? 30 : 2,
                        price: type === 'Buah' ? 387000 : (type === 'Bunga' ? 549000 : 250000),
                        unit: type === 'Rapat' ? 'day' : 'night',
                        luas: type === 'Buah' ? '24' : (type === 'Bunga' ? '28' : '60'),
                        bed: type === 'Buah' ? 'Queen Size' : (type === 'Bunga' ? 'Twin Bed' : 'Meja Rapat Oval'),
                        status: 'READY',
                        photo: type === 'Buah' ? '/images/bungalow_buah.jpg' : (type === 'Bunga' ? '/images/bungalow_bunga.jpg' : '/images/ruang_rapat.jpeg'),
                        description: ''
                    };
                    this.crudModalOpen = true;
                    setTimeout(() => {
                        if (window.lucide) window.lucide.createIcons();
                    }, 50);
                },

                openEditModal(item) {
                    this.crudAction = 'edit';
                    this.crudType = item.type;
                    this.crudForm = { ...item };
                    if (this.crudForm.luas && typeof this.crudForm.luas === 'string') {
                        this.crudForm.luas = this.crudForm.luas.replace(/m²|m2|\s/gi, '');
                    }
                    this.crudModalOpen = true;
                    setTimeout(() => {
                         if (window.lucide) window.lucide.createIcons();
                    }, 50);
                },

                handlePhotoUpload(event) {
                    const file = event.target.files[0];
                    if (!file) return;

                    this.crudPhotoFile = file;

                    const reader = new FileReader();
                    reader.onload = (e) => {
                        const img = new Image();
                        img.onload = () => {
                            const canvas = document.createElement('canvas');
                            const ctx = canvas.getContext('2d');
                            
                            const maxW = 400;
                            const maxH = 300;
                            let w = img.width;
                            let h = img.height;
                            
                            if (w > h) {
                                if (w > maxW) {
                                    h *= maxW / w;
                                    w = maxW;
                                }
                            } else {
                                if (h > maxH) {
                                    w *= maxH / h;
                                    h = maxH;
                                }
                            }
                            
                            canvas.width = w;
                            canvas.height = h;
                            ctx.drawImage(img, 0, 0, w, h);
                            this.crudForm.photo = canvas.toDataURL('image/jpeg', 0.7);
                        };
                        img.src = e.target.result;
                    };
                    reader.readAsDataURL(file);
                },

                async saveCrudItem() {
                    if (!this.crudForm.name || !this.crudForm.price) {
                        this.addToast('Data Tidak Lengkap', 'Nama dan harga harus diisi.', 'error');
                        return;
                    }

                    const formData = new FormData();
                    formData.append('name', this.crudForm.name);
                    formData.append('type', this.crudForm.type);
                    formData.append('gedung', this.crudForm.gedung);
                    formData.append('lantai', this.crudForm.lantai);
                    formData.append('capacity', this.crudForm.capacity || 2);
                    formData.append('price', parseInt(this.crudForm.price));
                    formData.append('unit', this.crudForm.unit);
                    
                    let finalLuas = this.crudForm.luas ? this.crudForm.luas.toString().trim() : '';
                    if (finalLuas && !finalLuas.includes('m²') && !finalLuas.includes('m2')) {
                        finalLuas += ' m²';
                    }
                    formData.append('luas', finalLuas);
                    
                    formData.append('bed', this.crudForm.bed || '');
                    formData.append('status', this.crudForm.status);
                    formData.append('description', this.crudForm.description || '');
                    
                    if (this.crudPhotoFile) {
                        formData.append('photo', this.crudPhotoFile);
                    }
                    
                    if (this.crudAction === 'edit') {
                        formData.append('_method', 'PUT'); // Laravel requirement for FormData PUT
                    }

                    try {
                        let res;
                        if (this.crudAction === 'create') {
                            res = await this.apiCall('POST', '/facilities', formData, true);
                        } else {
                            res = await this.apiCall('POST', `/facilities/${this.crudForm.id}`, formData, true);
                        }

                        if (res.success) {
                            this.addToast('Berhasil', res.message, 'success');
                            this.crudModalOpen = false;
                            this.crudPhotoFile = null;
                            await this.loadFacilitiesFromApi();
                        } else {
                            const errors = res.errors ? Object.values(res.errors).flat().join(' ') : res.message;
                            this.addToast('Gagal', errors, 'error');
                        }
                    } catch (e) {
                        this.addToast('Koneksi Gagal', 'Gagal menyimpan data ke server.', 'error');
                    }

                    setTimeout(() => {
                        if (window.lucide) window.lucide.createIcons();
                    }, 50);
                },

                async deleteCrudItem(id) {
                    const item = this.facilities.find(f => f.id === id);
                    if (!item) return;

                    if (confirm(`Apakah Anda yakin ingin menghapus ${item.type} '${item.name}'?`)) {
                        try {
                            const res = await this.apiCall('DELETE', `/facilities/${id}`);
                            if (res.success) {
                                this.addToast('Berhasil Dihapus', res.message, 'success');
                                await this.loadFacilitiesFromApi();
                            } else {
                                this.addToast('Gagal Menghapus', res.message, 'error');
                            }
                        } catch (e) {
                            this.addToast('Koneksi Gagal', 'Gagal menghapus data dari server.', 'error');
                        }
                        
                        setTimeout(() => {
                            if (window.lucide) window.lucide.createIcons();
                        }, 50);
                    }
                },
                
                async saveSettingsProfile() {
                    try {
                        const res = await this.apiCall('PUT', '/profile', {
                            name:     this.settingsProfile.nama,
                            phone:    this.settingsContact.telepon,
                            email:    this.settingsContact.email,
                            instansi: this.settingsProfile.instansi,
                        });
                        if (res.success) {
                            this.fillProfile(res.data);
                            this.addToast('Profil Diperbarui', 'Data diri Anda berhasil diperbarui.', 'success');
                        } else {
                            const errors = res.errors ? Object.values(res.errors).flat().join(' ') : (res.message || 'Gagal memperbarui profil.');
                            this.addToast('Gagal', errors, 'error');
                        }
                    } catch (e) {
                        this.addToast('Koneksi Gagal', 'Tidak dapat terhubung ke server.', 'error');
                    }
                },

                async saveSettingsContact() {
                    // Contact settings use the same endpoint as profile settings
                    await this.saveSettingsProfile();
                },

                async saveSettingsPassword() {
                    if (!this.settingsPassword.current) {
                        this.addToast('Gagal', 'Masukkan kata sandi saat ini.', 'error'); return;
                    }
                    if (this.settingsPassword.new.length < 6) {
                        this.addToast('Gagal', 'Kata sandi baru minimal 6 karakter.', 'error'); return;
                    }
                    if (this.settingsPassword.new !== this.settingsPassword.confirm) {
                        this.addToast('Gagal', 'Konfirmasi kata sandi tidak cocok.', 'error'); return;
                    }
                    try {
                        const res = await this.apiCall('PUT', '/profile/password', {
                            current_password:      this.settingsPassword.current,
                            password:              this.settingsPassword.new,
                            password_confirmation: this.settingsPassword.confirm,
                        });
                        if (res.success) {
                            this.settingsPassword = { current: '', new: '', confirm: '' };
                            this.addToast('Kata Sandi Diperbarui', 'Kata sandi Anda berhasil diubah.', 'success');
                        } else {
                            const errors = res.errors ? Object.values(res.errors).flat().join(' ') : (res.message || 'Gagal mengubah kata sandi.');
                            this.addToast('Gagal', errors, 'error');
                        }
                    } catch (e) {
                        this.addToast('Koneksi Gagal', 'Tidak dapat terhubung ke server.', 'error');
                    }
                },

                filteredAdminFacilities() {
                    return this.facilities.filter(f => {
                        const matchesTab = f.type === this.adminManagementSubTab;
                        const matchesSearch = f.name.toLowerCase().includes(this.adminManagementSearch.toLowerCase()) || 
                                              f.gedung.toLowerCase().includes(this.adminManagementSearch.toLowerCase());
                        return matchesTab && matchesSearch;
                    });
                },

                filteredGuests() {
                    return this.guests.filter(g => {
                        const matchesSearch = g.nama.toLowerCase().includes(this.adminGuestSearch.toLowerCase()) || 
                                              g.email.toLowerCase().includes(this.adminGuestSearch.toLowerCase()) ||
                                              g.phone.includes(this.adminGuestSearch);
                        if (this.adminGuestFilter === 'member') {
                            return matchesSearch && g.status === 'Member';
                        }
                        if (this.adminGuestFilter === 'menginap') {
                            return matchesSearch && g.status === 'Reguler';
                        }
                        return matchesSearch;
                    });
                },

                getMonthlyChart() {
                    const months = [];
                    const now = new Date();
                    for (let i = 2; i >= 0; i--) {
                        const d = new Date(now.getFullYear(), now.getMonth() - i, 1);
                        const label = d.toLocaleString('id-ID', { month: 'short' });
                        const count = this.bookings.filter(b => {
                            if (!b.check_in) return false;
                            const bDate = new Date(b.check_in);
                            return bDate.getMonth() === d.getMonth() && bDate.getFullYear() === d.getFullYear();
                        }).length;
                        months.push({ label, count });
                    }
                    const maxCount = Math.max(...months.map(m => m.count), 1);
                    return months.map(m => ({
                        label: m.label,
                        percent: Math.floor((m.count / maxCount) * 100)
                    }));
                },

                getTypeOccupancy() {
                    const total = this.bookings.length || 1;
                    let kamar = 0, rapat = 0;
                    this.bookings.forEach(b => {
                        const name = (b.unit_name || '').toLowerCase();
                        if (name.includes('rapat')) rapat++;
                        else kamar++;
                    });
                    return [
                        { label: 'Kamar / Bungalow', percent: Math.round((kamar / total) * 100) },
                        { label: 'Ruang Rapat', percent: Math.round((rapat / total) * 100) }
                    ];
                },

                addToast(title, message, type = 'success') {
                    const id = this.toastCount++;
                    this.toasts.push({ id, title, message, type });
                    setTimeout(() => {
                        if (window.lucide) window.lucide.createIcons();
                    }, 20);
                    setTimeout(() => {
                        this.removeToast(id);
                    }, 4000);
                },

                removeToast(id) {
                    this.toasts = this.toasts.filter(t => t.id !== id);
                },

                saveSettingsProfile() {
                    this.profile.nama = this.settingsProfile.nama;
                    this.profile.instansi = this.settingsProfile.instansi;
                    this.addToast('Profil Diperbarui', 'Data profil berhasil disimpan.', 'success');
                },

                saveSettingsContact() {
                    this.addToast('Kontak Diperbarui', 'Email dan nomor telepon berhasil disimpan.', 'success');
                },

                saveSettingsPassword() {
                    if (!this.settingsPassword.current) {
                        this.addToast('Gagal', 'Masukkan kata sandi saat ini.', 'error'); return;
                    }
                    if (this.settingsPassword.new.length < 6) {
                        this.addToast('Gagal', 'Kata sandi baru minimal 6 karakter.', 'error'); return;
                    }
                    if (this.settingsPassword.new !== this.settingsPassword.confirm) {
                        this.addToast('Gagal', 'Konfirmasi kata sandi tidak cocok.', 'error'); return;
                    }
                    this.settingsPassword = { current: '', new: '', confirm: '' };
                    this.addToast('Kata Sandi Diperbarui', 'Kata sandi berhasil diubah.', 'success');
                },

                formatRupiah(amount) {
                    if (amount === undefined || amount === null) return 'Rp 0';
                    let num = parseFloat(amount);
                    if (isNaN(num)) return 'Rp 0';
                    return 'Rp ' + num.toLocaleString('id-ID');
                },

                formatIndoDate(dateStr) {
                    if (!dateStr) return '';
                    const parts = dateStr.split('-');
                    if (parts.length !== 3) return dateStr;
                    
                    const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
                    const day = parseInt(parts[2]);
                    const month = months[parseInt(parts[1]) - 1];
                    const year = parts[0];
                    
                    return `${day} ${month} ${year}`;
                }
            };
        }
    </script>
</body>
</html>



