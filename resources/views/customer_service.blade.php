<!DOCTYPE html>
<html lang="id" x-data="wismaApp()" x-init="initApp()">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Portal Customer Service Wisma DPR RI</title>

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
            aside, header, nav, .no-print, button, .modal, .toast, [title="Keluar"] {
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
                        <option value="lainnya">Lainnya (Other)</option>
                    </select>
                </div>
                <div x-show="newComplaintForm.category === 'lainnya'" class="space-y-1 fade-in">
                    <label class="text-[10px] text-slate-500 font-bold uppercase tracking-wide block">Masukkan Kategori Keluhan Kustom</label>
                    <input type="text" x-model="newComplaintForm.customCategory" placeholder="Contoh: Masalah Listrik, Air Bersih, dll." :required="newComplaintForm.category === 'lainnya'" class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl p-3 focus:ring-1 focus:ring-wisma-gold focus:outline-none focus:bg-white transition-all">
                </div>
                <div class="space-y-1">
                    <label class="text-[10px] text-slate-500 font-bold uppercase tracking-wide block">Lokasi (No. Bungalow / Area)</label>
                    <input type="text" x-model="newComplaintForm.location" placeholder="Contoh: Bungalow Kedondong atau Lobby Wisma" required class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl p-3 focus:ring-1 focus:ring-wisma-gold focus:outline-none focus:bg-white transition-all">
                </div>
                <div class="space-y-1">
                    <label class="text-[10px] text-slate-500 font-bold uppercase tracking-wide block">Nama Pelapor (Tamu)</label>
                    <input type="text" x-model="newComplaintForm.guestName" placeholder="Contoh: Bpk. Kurniawan" required class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl p-3 focus:ring-1 focus:ring-wisma-gold focus:outline-none focus:bg-white transition-all">
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
                        <i data-lucide="life-buoy" class="w-3 h-3"></i> Customer Service Portal
                    </span>
                    <h1 class="text-4xl font-outfit font-extrabold text-white tracking-tight leading-tight max-w-lg">
                        Pelayanan Keluhan Tamu & Maintenance Bungalow
                    </h1>
                    <p class="text-xs text-slate-300 leading-relaxed max-w-md font-light">
                        Portal khusus petugas Customer Service Wisma DPR RI. Catat keluhan, delegasikan tugas ke tim teknis, serta monitor resolusi secara real-time.
                    </p>
                </div>

                <div class="grid grid-cols-3 gap-6 pt-4 border-t border-white/10 max-w-lg text-white">
                    <div>
                        <h4 class="text-2xl font-bold font-outfit text-wisma-gold">Responsif</h4>
                        <span class="text-[10px] text-slate-400 block mt-0.5">Penanganan Keluhan</span>
                    </div>
                    <div>
                        <h4 class="text-2xl font-bold font-outfit text-wisma-gold">Real-Time</h4>
                        <span class="text-[10px] text-slate-400 block mt-0.5">Status Pelacakan</span>
                    </div>
                    <div>
                        <h4 class="text-2xl font-bold font-outfit text-wisma-gold">Efisien</h4>
                        <span class="text-[10px] text-slate-400 block mt-0.5">Delegasi Tim</span>
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
                        <i data-lucide="life-buoy" class="w-5 h-5"></i>
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
                    <h2 class="text-2xl font-extrabold text-slate-900 font-outfit tracking-tight">Portal Customer Service</h2>
                    <p class="text-xs text-slate-500">Silakan masukkan kredensial petugas Customer Service.</p>
                </div>

                <form @submit.prevent="login()" class="space-y-5">
                    <!-- Username -->
                    <div class="space-y-1">
                        <label class="text-[10px] text-slate-500 font-bold uppercase tracking-wide block">Username Petugas</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                <i data-lucide="user" class="w-4.5 h-4.5"></i>
                            </span>
                            <input type="text" 
                                   x-model="loginForm.username"
                                   placeholder="Contoh: cs" 
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

                    <!-- Button submit -->
                    <button type="submit" class="w-full py-3 bg-slate-950 hover:bg-slate-900 text-white font-bold text-xs rounded-xl shadow-lg transition-colors flex items-center justify-center gap-2">
                        Masuk Portal CS <i data-lucide="arrow-right" class="w-4 h-4"></i>
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
                    <i data-lucide="life-buoy" class="w-6 h-6 text-wisma-dark"></i>
                </div>
                <div>
                    <h2 class="font-outfit font-bold text-base tracking-wider leading-none">Wisma DPR RI</h2>
                    <span class="text-[10px] text-wisma-textMuted font-medium uppercase tracking-widest font-outfit">Portal CS</span>
                </div>
            </div>

            <!-- Sidebar Navigation Menu -->
            <nav class="flex-1 px-4 py-6 space-y-1.5 overflow-y-auto scrollbar-hide">
                <p class="text-[10px] text-slate-500 font-semibold px-3 mb-2 uppercase tracking-widest">Layanan Utama</p>
                
                <button @click="switchTab('cs_dashboard')"
                        class="w-full flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 group text-left"
                        :class="currentTab === 'cs_dashboard' ? 'bg-gradient-to-r from-wisma-gold to-amber-500 text-wisma-dark font-semibold shadow-lg shadow-wisma-gold/15' : 'text-wisma-textMuted hover:bg-slate-800/50 hover:text-white'">
                    <i data-lucide="layout-dashboard" class="w-5 h-5 transition-transform group-hover:scale-110"></i>
                    <span class="text-sm">Dashboard Keluhan</span>
                </button>

                <button @click="switchTab('cs_complaints')"
                        class="w-full flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 group text-left"
                        :class="currentTab === 'cs_complaints' ? 'bg-gradient-to-r from-wisma-gold to-amber-500 text-wisma-dark font-semibold shadow-lg shadow-wisma-gold/15' : 'text-wisma-textMuted hover:bg-slate-800/50 hover:text-white'">
                    <i data-lucide="alert-circle" class="w-5 h-5 transition-transform group-hover:scale-110"></i>
                    <span class="text-sm">Manajemen Keluhan</span>
                    <span class="ml-auto px-2 py-0.5 bg-red-950/45 text-red-400 rounded-md text-[10px] font-bold" x-text="complaints.filter(c => c.status !== 'Resolved').length + ' Aktif'"></span>
                </button>

                <button @click="switchTab('cs_reports')"
                        class="w-full flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 group text-left"
                        :class="currentTab === 'cs_reports' ? 'bg-gradient-to-r from-wisma-gold to-amber-500 text-wisma-dark font-semibold shadow-lg shadow-wisma-gold/15' : 'text-wisma-textMuted hover:bg-slate-800/50 hover:text-white'">
                    <i data-lucide="bar-chart-2" class="w-5 h-5 transition-transform group-hover:scale-110"></i>
                    <span class="text-sm">Laporan & Ulasan</span>
                </button>

                <div class="border-t border-slate-800 my-2 mx-3"></div>
                <p class="text-[10px] text-slate-500 font-semibold px-3 mb-2 uppercase tracking-widest">Akun</p>

                <button @click="switchTab('cs_settings')"
                        class="w-full flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 group text-left"
                        :class="currentTab === 'cs_settings' ? 'bg-gradient-to-r from-wisma-gold to-amber-500 text-wisma-dark font-semibold shadow-lg shadow-wisma-gold/15' : 'text-wisma-textMuted hover:bg-slate-800/50 hover:text-white'">
                    <i data-lucide="settings" class="w-5 h-5 transition-transform group-hover:scale-110"></i>
                    <span class="text-sm">Pengaturan</span>
                </button>
            </nav>

            <!-- Sidebar Footer/Petugas Profile Summary -->
            <div class="p-4 border-t border-slate-800 bg-wisma-dark/40 flex items-center gap-3">
                <img class="w-10 h-10 rounded-full border border-wisma-gold/30 object-cover" 
                     src="https://images.unsplash.com/photo-1573497019940-1c28c88b4f3e?auto=format&fit=crop&w=100&h=100&q=80" 
                     alt="Avatar">
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
                    <i data-lucide="shield-alert" class="w-5 h-5 text-amber-500"></i>
                    <span class="text-xs font-bold font-outfit uppercase tracking-wider">Portal Customer Service Aktif</span>
                </div>

                <div class="flex items-center gap-4">
                    <div class="flex items-center gap-3">
                        <div class="text-right">
                            <p class="text-xs font-semibold text-slate-800" x-text="profile.nama"></p>
                            <p class="text-[10px] text-slate-500" x-text="profile.instansi"></p>
                        </div>
                        <div class="w-10 h-10 rounded-xl bg-wisma-navy text-wisma-gold flex items-center justify-center font-bold text-sm border border-wisma-gold/20 shadow-sm">CS</div>
                    </div>
                </div>
            </header>

            <!-- SCROLLABLE PAGE CONTENT -->
            <main class="flex-1 overflow-y-auto p-8 relative">

                <!-- 1. DASHBOARD VIEW -->
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
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
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
                            <template x-for="c in complaints.slice(0, 5)" :key="c.id">
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
                                                          'bg-emerald-100 text-emerald-700': c.status === 'Resolved'
                                                      }"
                                                      x-text="c.status === 'Pending' ? 'Menunggu' : (c.status === 'Processed' ? 'Proses' : 'Selesai')"></span>
                                            </div>
                                            <p class="text-[10px] text-slate-400 mt-0.5" x-text="c.category + ' • Lokasi: ' + c.location + ' • ' + c.date"></p>
                                        </div>
                                    </div>
                                    <div>
                                        <template x-if="c.status === 'Pending'">
                                            <button @click="processComplaint(c.id)" class="px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white font-bold text-[10px] rounded-lg shadow transition-colors">
                                                Tugaskan Tim
                                            </button>
                                        </template>
                                        <template x-if="c.status === 'Processed'">
                                            <button @click="resolveComplaint(c.id)" class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-[10px] rounded-lg shadow transition-colors">
                                                Selesaikan
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
                                   x-model="complaintSearch" 
                                   placeholder="Cari keluhan atau lokasi..." 
                                   class="w-full pl-9 pr-4 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-1 focus:ring-wisma-gold focus:outline-none transition-all">
                        </div>
                        <div class="flex gap-2">
                            <button @click="complaintFilterTab = 'semua'" :class="complaintFilterTab === 'semua' ? 'bg-wisma-navy text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'" class="px-4 py-2 rounded-xl text-xs font-bold transition-all">Semua</button>
                            <button @click="complaintFilterTab = 'Pending'" :class="complaintFilterTab === 'Pending' ? 'bg-wisma-navy text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'" class="px-4 py-2 rounded-xl text-xs font-bold transition-all">Menunggu (Pending)</button>
                            <button @click="complaintFilterTab = 'Processed'" :class="complaintFilterTab === 'Processed' ? 'bg-wisma-navy text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'" class="px-4 py-2 rounded-xl text-xs font-bold transition-all">Diproses</button>
                            <button @click="complaintFilterTab = 'Resolved'" :class="complaintFilterTab === 'Resolved' ? 'bg-wisma-navy text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'" class="px-4 py-2 rounded-xl text-xs font-bold transition-all">Selesai</button>
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
                                                  'bg-emerald-100 text-emerald-700': c.status === 'Resolved'
                                              }"
                                              x-text="c.status === 'Pending' ? 'Pending' : (c.status === 'Processed' ? 'Diproses' : 'Selesai')"></span>
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
                <div x-show="currentTab === 'cs_reports'" class="space-y-6 fade-in" x-cloak>
                    <!-- Header -->
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 no-print">
                        <div>
                            <h1 class="text-2xl font-outfit font-extrabold text-slate-900">Laporan Keluhan & Ulasan Tamu</h1>
                            <p class="text-xs text-slate-500">Analisis tingkat kepuasan tamu dan ringkasan keluhan masuk.</p>
                        </div>
                        <div class="flex gap-2">
                            <button @click="printReport()" class="px-5 py-2.5 bg-[#0B1A30] hover:bg-slate-800 text-white font-bold text-xs rounded-xl shadow-lg transition-all flex items-center gap-1.5">
                                <i data-lucide="printer" class="w-4 h-4"></i> Cetak Laporan
                            </button>
                        </div>
                    </div>

                    <!-- Sub-tabs selection -->
                    <div class="border-b border-slate-200 flex gap-6 no-print">
                        <button @click="csReportSubTab = 'keluhan'"
                                :class="csReportSubTab === 'keluhan' ? 'border-wisma-gold text-wisma-gold font-bold' : 'border-transparent text-slate-500 hover:text-slate-900'"
                                class="pb-3 border-b-2 text-xs font-semibold tracking-wide transition-all uppercase">
                            Rekap Keluhan Masuk
                        </button>
                        <button @click="csReportSubTab = 'ulasan'"
                                :class="csReportSubTab === 'ulasan' ? 'border-wisma-gold text-wisma-gold font-bold' : 'border-transparent text-slate-500 hover:text-slate-900'"
                                class="pb-3 border-b-2 text-xs font-semibold tracking-wide transition-all uppercase">
                            Ulasan & Rating Tamu
                        </button>
                    </div>

                    <!-- SUBTAB 1: REKAP KELUHAN MASUK -->
                    <div x-show="csReportSubTab === 'keluhan'" class="space-y-6">
                        <!-- Stats Grid -->
                        <div class="grid grid-cols-2 md:grid-cols-5 gap-4 no-print">
                            <div class="bg-white p-4 rounded-2xl border border-slate-100 shadow-sm">
                                <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block">Total Keluhan</span>
                                <h3 class="text-xl font-bold font-outfit mt-1 text-slate-800" x-text="complaints.length"></h3>
                            </div>
                            <div class="bg-white p-4 rounded-2xl border border-slate-100 shadow-sm">
                                <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block">Menunggu (Pending)</span>
                                <h3 class="text-xl font-bold font-outfit mt-1 text-red-500" x-text="complaints.filter(c => c.status === 'Pending').length"></h3>
                            </div>
                            <div class="bg-white p-4 rounded-2xl border border-slate-100 shadow-sm">
                                <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block">Sedang Diproses</span>
                                <h3 class="text-xl font-bold font-outfit mt-1 text-blue-500" x-text="complaints.filter(c => c.status === 'Processed').length"></h3>
                            </div>
                            <div class="bg-white p-4 rounded-2xl border border-slate-100 shadow-sm">
                                <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block">Selesai (Resolved)</span>
                                <h3 class="text-xl font-bold font-outfit mt-1 text-emerald-500" x-text="complaints.filter(c => c.status === 'Resolved').length"></h3>
                            </div>
                            <div class="bg-white p-4 rounded-2xl border border-slate-100 shadow-sm col-span-2 md:col-span-1">
                                <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block">Tingkat Resolusi</span>
                                <h3 class="text-xl font-bold font-outfit mt-1 text-amber-600" 
                                    x-text="complaints.length ? Math.round((complaints.filter(c => c.status === 'Resolved').length / complaints.length) * 100) + '%' : '0%'"></h3>
                            </div>
                        </div>

                        <!-- Print-only Title Header -->
                        <div class="hidden print:block text-center border-b border-slate-800 pb-4 mb-6">
                            <h2 class="text-xl font-bold font-outfit uppercase tracking-wider">LAPORAN REKAPITULASI KELUHAN TAMU</h2>
                            <p class="text-xs text-slate-600">Sistem Pelayanan Wisma DPR RI Kopo</p>
                            <p class="text-[10px] text-slate-500 mt-1" x-text="'Dicetak pada: ' + new Date().toLocaleDateString('id-ID', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' })"></p>
                        </div>

                        <!-- Filters for Printing & View -->
                        <div class="bg-white p-4 rounded-2xl border border-slate-100 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4 no-print">
                            <div class="flex flex-wrap gap-2">
                                <div class="text-xs text-slate-500 flex items-center pr-2 font-bold uppercase tracking-wide">Filter:</div>
                                <select x-model="reportComplaintFilterCategory" class="text-xs bg-slate-50 border border-slate-200 rounded-lg p-2 focus:ring-1 focus:ring-wisma-gold focus:outline-none">
                                    <option value="semua">Semua Kategori</option>
                                    <option value="Fasilitas (Bungalow, Gedung)">Fasilitas (Bungalow, Gedung)</option>
                                    <option value="Layanan Laundry">Layanan Laundry</option>
                                    <option value="Internet / Wifi">Internet / Wifi</option>
                                    <option value="Layanan Makanan">Layanan Makanan</option>
                                </select>
                                <select x-model="reportComplaintFilterStatus" class="text-xs bg-slate-50 border border-slate-200 rounded-lg p-2 focus:ring-1 focus:ring-wisma-gold focus:outline-none">
                                    <option value="semua">Semua Status</option>
                                    <option value="Pending">Menunggu (Pending)</option>
                                    <option value="Processed">Diproses</option>
                                    <option value="Resolved">Selesai</option>
                                </select>
                            </div>
                        </div>

                        <!-- Table -->
                        <div class="bg-white border border-slate-100 rounded-3xl p-6 shadow-sm printable-report">
                            <div class="overflow-x-auto">
                                <table class="w-full text-left border-collapse text-xs">
                                    <thead>
                                        <tr class="border-b border-slate-200 text-[10px] text-slate-400 font-bold uppercase tracking-wider">
                                            <th class="py-3 px-4">No. Tiket</th>
                                            <th class="py-3 px-4">Kategori</th>
                                            <th class="py-3 px-4">Lokasi & Pelapor</th>
                                            <th class="py-3 px-4">Deskripsi Keluhan</th>
                                            <th class="py-3 px-4">Tanggal Masuk</th>
                                            <th class="py-3 px-4 text-right">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <template x-for="c in complaints.filter(c => {
                                            const categoryMatch = reportComplaintFilterCategory === 'semua' || c.category === reportComplaintFilterCategory;
                                            const statusMatch = reportComplaintFilterStatus === 'semua' || c.status === reportComplaintFilterStatus;
                                            return categoryMatch && statusMatch;
                                        })" :key="c.id">
                                            <tr class="border-b border-slate-100 hover:bg-slate-50/50 transition-colors">
                                                <td class="py-3 px-4 font-bold text-slate-900" x-text="c.id"></td>
                                                <td class="py-3 px-4" x-text="c.category"></td>
                                                <td class="py-3 px-4" x-text="c.location"></td>
                                                <td class="py-3 px-4 text-slate-600" x-text="c.title"></td>
                                                <td class="py-3 px-4 text-slate-500" x-text="c.date"></td>
                                                <td class="py-3 px-4 text-right">
                                                    <span class="px-2 py-0.5 rounded text-[9px] uppercase font-bold"
                                                          :class="{
                                                              'bg-red-100 text-red-700': c.status === 'Pending',
                                                              'bg-blue-100 text-blue-700': c.status === 'Processed',
                                                              'bg-emerald-100 text-emerald-700': c.status === 'Resolved'
                                                          }"
                                                          x-text="c.status === 'Pending' ? 'Menunggu' : (c.status === 'Processed' ? 'Diproses' : 'Selesai')"></span>
                                                </td>
                                            </tr>
                                        </template>
                                        <tr x-show="complaints.filter(c => {
                                            const categoryMatch = reportComplaintFilterCategory === 'semua' || c.category === reportComplaintFilterCategory;
                                            const statusMatch = reportComplaintFilterStatus === 'semua' || c.status === reportComplaintFilterStatus;
                                            return categoryMatch && statusMatch;
                                        }).length === 0">
                                            <td colspan="6" class="text-center py-8 text-slate-400">Tidak ada rekapitulasi keluhan.</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Signature Area for Printing -->
                            <div class="hidden print:grid grid-cols-2 gap-8 pt-12 text-xs">
                                <div></div>
                                <div class="text-center space-y-12">
                                    <div>
                                        <p>Mengetahui,</p>
                                        <p class="font-bold">Customer Service Wisma DPR RI</p>
                                    </div>
                                    <div>
                                        <p class="font-bold underline" x-text="profile.nama"></p>
                                        <p class="text-[10px] text-slate-500">NIP. 199308122018022003</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- SUBTAB 2: ULASAN & RATING TAMU -->
                    <div x-show="csReportSubTab === 'ulasan'" class="space-y-6">
                        <!-- Star Breakdown & Average Card -->
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 no-print">
                            <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm flex flex-col justify-center items-center text-center">
                                <span class="text-xs text-slate-400 font-semibold uppercase tracking-wider block">Rata-rata Rating</span>
                                <h1 class="text-5xl font-extrabold font-outfit text-slate-900 mt-2" 
                                    x-text="bookings.filter(b => b.hasFeedback).length ? (bookings.filter(b => b.hasFeedback).reduce((acc, b) => acc + b.rating, 0) / bookings.filter(b => b.hasFeedback).length).toFixed(1) : '0.0'"></h1>
                                <div class="flex items-center gap-1 mt-2 text-wisma-gold">
                                    <template x-for="star in [1, 2, 3, 4, 5]">
                                        <svg class="w-4 h-4 fill-current" :class="star <= Math.round(bookings.filter(b => b.hasFeedback).reduce((acc, b) => acc + b.rating, 0) / bookings.filter(b => b.hasFeedback).length) ? 'text-wisma-gold' : 'text-slate-200'" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                        </svg>
                                    </template>
                                </div>
                                <span class="text-[10px] text-slate-400 mt-2" x-text="'Dari ' + bookings.filter(b => b.hasFeedback).length + ' ulasan tamu'"></span>
                            </div>

                            <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm md:col-span-3 space-y-4">
                                <h4 class="text-xs font-bold text-slate-800 uppercase tracking-wide">Rincian Kepuasan Tamu (Berdasarkan Kategori)</h4>
                                <div class="space-y-3.5 pt-2 text-xs">
                                    <!-- Cleanliness -->
                                    <div class="space-y-1">
                                        <div class="flex justify-between font-semibold text-slate-700">
                                            <span>Kebersihan Bungalow & Gedung</span>
                                            <span class="font-bold text-slate-900" x-text="bookings.filter(b => b.hasFeedback).length ? (bookings.filter(b => b.hasFeedback).reduce((acc, b) => acc + (b.rating_cleanliness || 0), 0) / bookings.filter(b => b.hasFeedback).length).toFixed(1) + ' / 5.0' : '0.0 / 5.0'"></span>
                                        </div>
                                        <div class="w-full h-2 bg-slate-100 rounded-full overflow-hidden">
                                            <div class="bg-amber-500 h-full rounded-full" 
                                                 :style="'width: ' + (bookings.filter(b => b.hasFeedback).length ? (bookings.filter(b => b.hasFeedback).reduce((acc, b) => acc + (b.rating_cleanliness || 0), 0) / bookings.filter(b => b.hasFeedback).length) * 20 : 0) + '%'"></div>
                                        </div>
                                    </div>
                                    <!-- Facilities -->
                                    <div class="space-y-1">
                                        <div class="flex justify-between font-semibold text-slate-700">
                                            <span>Kualitas Fasilitas & Peralatan</span>
                                            <span class="font-bold text-slate-900" x-text="bookings.filter(b => b.hasFeedback).length ? (bookings.filter(b => b.hasFeedback).reduce((acc, b) => acc + (b.rating_facilities || 0), 0) / bookings.filter(b => b.hasFeedback).length).toFixed(1) + ' / 5.0' : '0.0 / 5.0'"></span>
                                        </div>
                                        <div class="w-full h-2 bg-slate-100 rounded-full overflow-hidden">
                                            <div class="bg-amber-500 h-full rounded-full" 
                                                 :style="'width: ' + (bookings.filter(b => b.hasFeedback).length ? (bookings.filter(b => b.hasFeedback).reduce((acc, b) => acc + (b.rating_facilities || 0), 0) / bookings.filter(b => b.hasFeedback).length) * 20 : 0) + '%'"></div>
                                        </div>
                                    </div>
                                    <!-- Service -->
                                    <div class="space-y-1">
                                        <div class="flex justify-between font-semibold text-slate-700">
                                            <span>Keramahan & Kecepatan Pelayanan</span>
                                            <span class="font-bold text-slate-900" x-text="bookings.filter(b => b.hasFeedback).length ? (bookings.filter(b => b.hasFeedback).reduce((acc, b) => acc + (b.rating_service || 0), 0) / bookings.filter(b => b.hasFeedback).length).toFixed(1) + ' / 5.0' : '0.0 / 5.0'"></span>
                                        </div>
                                        <div class="w-full h-2 bg-slate-100 rounded-full overflow-hidden">
                                            <div class="bg-amber-500 h-full rounded-full" 
                                                 :style="'width: ' + (bookings.filter(b => b.hasFeedback).length ? (bookings.filter(b => b.hasFeedback).reduce((acc, b) => acc + (b.rating_service || 0), 0) / bookings.filter(b => b.hasFeedback).length) * 20 : 0) + '%'"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Print-only Title Header -->
                        <div class="hidden print:block text-center border-b border-slate-800 pb-4 mb-6">
                            <h2 class="text-xl font-bold font-outfit uppercase tracking-wider">LAPORAN ULASAN & PENILAIAN TAMU</h2>
                            <p class="text-xs text-slate-600">Sistem Pelayanan Wisma DPR RI Kopo</p>
                            <p class="text-[10px] text-slate-500 mt-1" x-text="'Dicetak pada: ' + new Date().toLocaleDateString('id-ID', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' })"></p>
                        </div>

                        <!-- Review Filter -->
                        <div class="bg-white p-4 rounded-2xl border border-slate-100 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4 no-print">
                            <div class="flex gap-2">
                                <button @click="reportRatingFilter = 'semua'" :class="reportRatingFilter === 'semua' ? 'bg-wisma-navy text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'" class="px-4 py-2 rounded-xl text-xs font-bold transition-all">Semua Ulasan</button>
                                <button @click="reportRatingFilter = '5'" :class="reportRatingFilter === '5' ? 'bg-wisma-navy text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'" class="px-4 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-1">5 ★</button>
                                <button @click="reportRatingFilter = '4'" :class="reportRatingFilter === '4' ? 'bg-wisma-navy text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'" class="px-4 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-1">4 ★</button>
                                <button @click="reportRatingFilter = '3'" :class="reportRatingFilter === '3' ? 'bg-wisma-navy text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'" class="px-4 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-1">3 Ke Bawah</button>
                            </div>
                        </div>

                        <!-- Feedback List -->
                        <div class="space-y-4 printable-report">
                            <template x-for="b in bookings.filter(b => {
                                if (!b.hasFeedback) return false;
                                if (reportRatingFilter === '5') return Math.floor(b.rating) === 5;
                                if (reportRatingFilter === '4') return Math.floor(b.rating) === 4;
                                if (reportRatingFilter === '3') return b.rating < 4;
                                return true;
                            })" :key="b.id">
                                <div class="bg-white border border-slate-100 rounded-3xl p-6 shadow-sm space-y-4 flex flex-col md:flex-row gap-6 justify-between items-start page-break-inside-avoid">
                                    <div class="flex-1 space-y-3">
                                        <!-- Guest & Unit details -->
                                        <div class="flex justify-between items-start flex-wrap gap-2 border-b border-slate-50 pb-2.5">
                                            <div>
                                                <h4 class="text-sm font-bold text-slate-900" x-text="b.nama"></h4>
                                                <p class="text-[10px] text-slate-400 mt-0.5" x-text="'NIP: ' + b.nip + ' • Menginap di: ' + b.unit_name"></p>
                                            </div>
                                            <div class="flex items-center gap-2">
                                                <span class="text-xs font-bold text-amber-500 font-outfit" x-text="'Score: ' + b.rating + ' / 5.0'"></span>
                                                <div class="flex text-wisma-gold">
                                                    <template x-for="star in [1, 2, 3, 4, 5]">
                                                        <svg class="w-3.5 h-3.5 fill-current" :class="star <= Math.round(b.rating) ? 'text-wisma-gold' : 'text-slate-200'" viewBox="0 0 20 20">
                                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                                        </svg>
                                                    </template>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- Ratings breakdown detailed -->
                                        <div class="grid grid-cols-3 gap-4 text-[10px] bg-slate-50 p-2.5 rounded-xl text-slate-500">
                                            <div>
                                                <span class="block text-slate-400 font-bold uppercase tracking-wider">Kebersihan</span>
                                                <span class="font-extrabold text-slate-800 text-xs" x-text="(b.rating_cleanliness || 0) + ' ★'"></span>
                                            </div>
                                            <div>
                                                <span class="block text-slate-400 font-bold uppercase tracking-wider">Fasilitas</span>
                                                <span class="font-extrabold text-slate-800 text-xs" x-text="(b.rating_facilities || 0) + ' ★'"></span>
                                            </div>
                                            <div>
                                                <span class="block text-slate-400 font-bold uppercase tracking-wider">Pelayanan</span>
                                                <span class="font-extrabold text-slate-800 text-xs" x-text="(b.rating_service || 0) + ' ★'"></span>
                                            </div>
                                        </div>
                                        <!-- Guest comment -->
                                        <div class="pt-1.5">
                                            <p class="text-xs text-slate-600 leading-relaxed italic" x-text="'“' + b.comment + '”'"></p>
                                        </div>
                                    </div>
                                    <div class="text-[10px] text-slate-400 text-right w-full md:w-auto mt-2 md:mt-0 font-medium whitespace-nowrap">
                                        <span x-text="'Selesai pada: ' + formatIndoDate(b.check_out)"></span>
                                    </div>
                                </div>
                            </template>

                            <div x-show="bookings.filter(b => {
                                if (!b.hasFeedback) return false;
                                if (reportRatingFilter === '5') return Math.floor(b.rating) === 5;
                                if (reportRatingFilter === '4') return Math.floor(b.rating) === 4;
                                if (reportRatingFilter === '3') return b.rating < 4;
                                return true;
                            }).length === 0" class="bg-white border border-slate-100 rounded-3xl p-8 text-center text-slate-400">
                                <i data-lucide="message-square" class="w-12 h-12 mx-auto mb-2 text-slate-200"></i>
                                <p class="text-xs">Tidak ada ulasan rating dengan kriteria filter ini.</p>
                            </div>

                            <!-- Signature Area for Printing -->
                            <div class="hidden print:grid grid-cols-2 gap-8 pt-12 text-xs">
                                <div></div>
                                <div class="text-center space-y-12">
                                    <div>
                                        <p>Mengetahui,</p>
                                        <p class="font-bold">Customer Service Wisma DPR RI</p>
                                    </div>
                                    <div>
                                        <p class="font-bold underline" x-text="profile.nama"></p>
                                        <p class="text-[10px] text-slate-500">NIP. 199308122018022003</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- SETTINGS VIEW -->
                <div x-show="currentTab === 'cs_settings'" class="space-y-6 fade-in" x-cloak>
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
                                    <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-amber-500 to-amber-700 text-white flex items-center justify-center font-bold text-xl font-outfit shadow-lg">
                                        CS
                                    </div>
                                    <div>
                                        <h2 class="text-base font-bold text-slate-900 font-outfit" x-text="settingsProfile.nama"></h2>
                                        <p class="text-xs text-slate-500 mt-0.5" x-text="settingsProfile.jabatan"></p>
                                        <span class="inline-block mt-1.5 px-2 py-0.5 bg-amber-50 text-amber-700 text-[10px] font-bold rounded-md uppercase tracking-wide">Customer Service</span>
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
        function wismaApp() {
            return {
                isLoggedIn: false,
                passwordVisible: false,
                loginForm: {
                    username: 'cs',
                    password: 'cs'
                },

                currentTab: 'cs_dashboard',
                
                complaintSearch: '',
                complaintFilterTab: 'semua',

                csReportSubTab: 'keluhan',
                reportComplaintFilterCategory: 'semua',
                reportComplaintFilterStatus: 'semua',
                reportRatingFilter: 'semua',
                
                inputComplaintModalOpen: false,
                newComplaintForm: {
                    category: 'facility',
                    location: '',
                    guestName: '',
                    description: '',
                    customCategory: ''
                },

                toasts: [],
                toastCount: 0,

                // Settings state
                settingsTab: 'profil',
                settingsProfile: {
                    nama: 'Amira CS',
                    jabatan: 'Customer Service & Pelayanan Keluhan',
                    nip: '199308122018022003',
                    instansi: 'Layanan Customer Service Wisma'
                },
                settingsContact: {
                    email: 'amira.cs@dpr.go.id',
                    telepon: '+62 814-3456-7891'
                },
                settingsPassword: {
                    current: '',
                    new: '',
                    confirm: ''
                },
                settingsPasswordVisible: { current: false, new: false, confirm: false },

                profile: {
                    role: 'customer_service',
                    nama: 'Amira CS',
                    role_label: 'Customer Service & Pelayanan Keluhan',
                    instansi: 'Layanan Customer Service Wisma'
                },

                // Shared LocalStorage data
                complaints: [],
                bookings: [],

                initApp() {
                    this.loadState();
                    setTimeout(() => {
                        if (window.lucide) {
                            window.lucide.createIcons();
                        }
                    }, 100);
                },

                login() {
                    if (this.loginForm.username !== 'cs' || this.loginForm.password !== 'cs') {
                        this.addToast('Login Gagal', 'Username atau Password Customer Service salah.', 'error');
                        return;
                    }
                    
                    this.isLoggedIn = true;
                    this.profile.role = 'customer_service';
                    this.profile.nama = 'Amira CS';
                    this.profile.role_label = 'Customer Service & Pelayanan Keluhan';
                    this.currentTab = 'cs_dashboard';
                    
                    this.addToast('Login Berhasil', `Selamat datang kembali, ${this.profile.nama}.`, 'success');
                    
                    setTimeout(() => {
                        if (window.lucide) {
                            window.lucide.createIcons();
                        }
                    }, 50);
                },

                logout() {
                    this.isLoggedIn = false;
                    this.loginForm.username = 'cs';
                    this.loginForm.password = 'cs';
                    this.addToast('Sesi Berakhir', 'Anda telah logout dari portal customer service.', 'info');
                    
                    setTimeout(() => {
                        if (window.lucide) {
                            window.lucide.createIcons();
                        }
                    }, 50);
                },

                persistState() {
                    localStorage.setItem('wisma_complaints', JSON.stringify(this.complaints));
                    localStorage.setItem('wisma_bookings', JSON.stringify(this.bookings));
                },

                loadState() {
                    const savedComplaints = localStorage.getItem('wisma_complaints');
                    const savedBookings = localStorage.getItem('wisma_bookings');
                    
                    // Force refresh schema if old bookings structure exists
                    let needForceRefresh = false;
                    if (savedBookings) {
                        try {
                            const bookingsList = JSON.parse(savedBookings);
                            if (bookingsList.length === 0 || bookingsList.some(b => b.unit_name.includes('Kamar') || b.unit_location.includes('Lantai') || b.total_price === 1500000 || b.unit_photo.includes('unsplash.com') || (b.unit_photo.includes('bungalow.jpg') && !b.unit_photo.includes('_buah') && !b.unit_photo.includes('_bunga')))) {
                                needForceRefresh = true;
                            }
                        } catch (e) {
                            needForceRefresh = true;
                        }
                    } else {
                        needForceRefresh = true;
                    }

                    if (needForceRefresh) {
                        localStorage.removeItem('wisma_complaints');
                        localStorage.removeItem('wisma_bookings');
                    }

                    const freshComplaints = localStorage.getItem('wisma_complaints');
                    const freshBookings = localStorage.getItem('wisma_bookings');

                    if (freshComplaints) {
                        this.complaints = JSON.parse(freshComplaints);
                    } else {
                        this.complaints = [
                            {
                                id: 'COMP-101',
                                title: 'AC Bungalow Kedondong Kurang Dingin',
                                category: 'Fasilitas (Bungalow, Gedung)',
                                category_slug: 'facility',
                                location: 'Bungalow Kedondong (Dilaporkan oleh: Bpk. Kurniawan)',
                                date: '25 Jun 2026, 10:15',
                                status: 'Pending'
                            },
                            {
                                id: 'COMP-102',
                                title: 'Koneksi Wifi Terputus-putus',
                                category: 'Internet / Wifi',
                                category_slug: 'internet',
                                location: 'Lobby Wisma (Dilaporkan oleh: Ibu Sri Mulyani)',
                                date: '24 Jun 2026, 14:30',
                                status: 'Processed'
                            },
                            {
                                id: 'COMP-103',
                                title: 'Sarapan Pagi Belum Diantar',
                                category: 'Layanan Makanan',
                                category_slug: 'food',
                                location: 'Bungalow Gladiol (Dilaporkan oleh: Bpk. Budi)',
                                date: '24 Jun 2026, 08:00',
                                status: 'Resolved'
                            }
                        ];
                        localStorage.setItem('wisma_complaints', JSON.stringify(this.complaints));
                    }

                    if (freshBookings) {
                        this.bookings = JSON.parse(freshBookings);
                        this.bookings.forEach(b => {
                            if (b.hasFeedback) {
                                if (b.rating === undefined || b.rating === null) b.rating = 5.0;
                                if (b.rating_cleanliness === undefined || b.rating_cleanliness === null) b.rating_cleanliness = Math.round(b.rating) || 5;
                                if (b.rating_facilities === undefined || b.rating_facilities === null) b.rating_facilities = Math.round(b.rating) || 5;
                                if (b.rating_service === undefined || b.rating_service === null) b.rating_service = Math.round(b.rating) || 5;
                                  if (b.comment === undefined || b.comment === null) b.comment = 'Layanan sangat memuaskan, tempat bersih, aman dan nyaman.';
                            }
                        });
                    } else {
                        this.bookings = [
                            {
                                id: 'WDPR-2026-0082',
                                unit_name: 'Bungalow Kedondong',
                                unit_photo: '/images/bungalow_buah.jpg',
                                unit_location: 'Wisma • Area Bawah',
                                check_in: '2026-05-10',
                                check_out: '2026-05-12',
                                nights: 2,
                                total_price: 774000,
                                status: 'Selesai',
                                nama: 'Budi Santoso',
                                nip: '198904122015031002',
                                hasFeedback: true,
                                rating: 4.7,
                                rating_cleanliness: 5,
                                rating_facilities: 4,
                                rating_service: 5,
                                comment: 'Pelayanan wisma sangat memuaskan, bungalow bersih dan nyaman.'
                            },
                            {
                                id: 'WDPR-2026-0083',
                                unit_name: 'Bungalow Widelia',
                                unit_photo: '/images/bungalow_bunga.jpg',
                                unit_location: 'Wisma • Area Atas',
                                check_in: '2026-06-20',
                                check_out: '2026-06-25',
                                nights: 5,
                                total_price: 2745000,
                                status: 'Check In',
                                nama: 'Ahmad Fauzi',
                                nip: '199112022018031001',
                                hasFeedback: false
                            }
                        ];
                        localStorage.setItem('wisma_bookings', JSON.stringify(this.bookings));
                    }
                },

                switchTab(tab) {
                    this.currentTab = tab;
                    setTimeout(() => {
                        if (window.lucide) {
                            window.lucide.createIcons();
                        }
                    }, 50);
                },

                printReport() {
                    window.print();
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
                },

                filteredComplaints() {
                    return this.complaints.filter(c => {
                        const matchesSearch = c.title.toLowerCase().includes(this.complaintSearch.toLowerCase()) || 
                                              c.location.toLowerCase().includes(this.complaintSearch.toLowerCase());
                        const matchesFilter = this.complaintFilterTab === 'semua' || c.status === this.complaintFilterTab;
                        return matchesSearch && matchesFilter;
                    });
                },

                processComplaint(id) {
                    const idx = this.complaints.findIndex(c => c.id === id);
                    if (idx !== -1) {
                        this.complaints[idx].status = 'Processed';
                    }
                    this.persistState();
                    this.addToast('Keluhan Diproses', 'Tim teknis/layanan telah ditugaskan ke lokasi.', 'success');
                    
                    setTimeout(() => {
                        if (window.lucide) window.lucide.createIcons();
                    }, 50);
                },

                resolveComplaint(id) {
                    const idx = this.complaints.findIndex(c => c.id === id);
                    if (idx !== -1) {
                        this.complaints[idx].status = 'Resolved';
                    }
                    this.persistState();
                    this.addToast('Keluhan Selesai', 'Masalah telah diselesaikan dan ditutup.', 'success');
                    
                    setTimeout(() => {
                        if (window.lucide) window.lucide.createIcons();
                    }, 50);
                },

                openNewComplaintModal() {
                    this.newComplaintForm = {
                        category: 'facility',
                        location: '',
                        guestName: '',
                        description: '',
                        customCategory: ''
                    };
                    this.inputComplaintModalOpen = true;
                },

                saveNewComplaint() {
                    const categoryNames = {
                        facility: 'Fasilitas (Bungalow, Gedung)',
                        laundry: 'Layanan Laundry',
                        internet: 'Internet / Wifi',
                        food: 'Layanan Makanan'
                    };

                    let categoryLabel = '';
                    if (this.newComplaintForm.category === 'lainnya') {
                        categoryLabel = this.newComplaintForm.customCategory || 'Lainnya';
                    } else {
                        categoryLabel = categoryNames[this.newComplaintForm.category] || this.newComplaintForm.category;
                    }

                    const now = new Date();
                    const hours = String(now.getHours()).padStart(2, '0');
                    const mins = String(now.getMinutes()).padStart(2, '0');
                    const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
                    const dateStr = `${now.getDate()} ${months[now.getMonth()]} ${now.getFullYear()}, ${hours}:${mins}`;

                    const newComplaint = {
                        id: 'COMP-' + String(Math.floor(104 + Math.random() * 800)),
                        title: this.newComplaintForm.description.length > 30 ? this.newComplaintForm.description.substring(0, 30) + '...' : this.newComplaintForm.description,
                        category: categoryLabel,
                        category_slug: this.newComplaintForm.category,
                        location: `${this.newComplaintForm.location} (Dilaporkan oleh: ${this.newComplaintForm.guestName})`,
                        date: dateStr,
                        status: 'Pending'
                    };

                    this.complaints.unshift(newComplaint);
                    this.persistState();

                    this.inputComplaintModalOpen = false;
                    this.addToast('Keluhan Berhasil Dicatat', 'Laporan keluhan lisan tamu telah dimasukkan ke sistem.', 'success');
                    
                    setTimeout(() => {
                        if (window.lucide) window.lucide.createIcons();
                    }, 50);
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
                }
            };
        }
    </script>
</body>
</html>
