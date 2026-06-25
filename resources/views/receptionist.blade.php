<!DOCTYPE html>
<html lang="id" x-data="wismaApp()" x-init="initApp()">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Portal Resepsionis Wisma DPR RI - Pelayanan Tamu</title>

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

    <!-- 1. LOGIN SCREEN -->
    <div x-show="!isLoggedIn" class="w-full h-screen flex relative z-30 fade-in">
        <!-- Cover Section Left -->
        <div class="w-[55%] h-full bg-slate-900 relative overflow-hidden hidden md:block">
            <img class="absolute inset-0 w-full h-full object-cover opacity-60" 
                 src="https://images.unsplash.com/photo-1566073771259-6a8506099945?auto=format&fit=crop&w=1200&q=80">
            <div class="absolute inset-0 bg-gradient-to-t from-wisma-dark via-wisma-dark/45 to-transparent"></div>
            
            <div class="absolute inset-x-12 bottom-16 space-y-8 z-10">
                <div class="space-y-4">
                    <span class="px-3 py-1 bg-emerald-500/20 text-emerald-400 text-[10px] uppercase font-bold tracking-widest rounded-full border border-emerald-400/30 flex items-center gap-1.5 w-max">
                        <i data-lucide="users" class="w-3 h-3"></i> Front Office Portal
                    </span>
                    <h1 class="text-4xl font-outfit font-extrabold text-white tracking-tight leading-tight max-w-lg">
                        Pelayanan Keramahan Front Desk & Pelayanan Tamu
                    </h1>
                    <p class="text-xs text-slate-300 leading-relaxed max-w-md font-light">
                        Portal khusus petugas Resepsionis Wisma DPR RI. Kelola kedatangan tamu (check-in) dan keberangkatan tamu (check-out) secara cepat, ramah, dan profesional.
                    </p>
                </div>

                <div class="grid grid-cols-3 gap-6 pt-4 border-t border-white/10 max-w-lg text-white">
                    <div>
                        <h4 class="text-2xl font-bold font-outfit text-wisma-gold">24/7</h4>
                        <span class="text-[10px] text-slate-400 block mt-0.5">Layanan Siaga</span>
                    </div>
                    <div>
                        <h4 class="text-2xl font-bold font-outfit text-wisma-gold">Check-in</h4>
                        <span class="text-[10px] text-slate-400 block mt-0.5">Sistem Instan</span>
                    </div>
                    <div>
                        <h4 class="text-2xl font-bold font-outfit text-wisma-gold">Layanan</h4>
                        <span class="text-[10px] text-slate-400 block mt-0.5">Ramah & Responsif</span>
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
                    <h2 class="text-2xl font-extrabold text-slate-900 font-outfit tracking-tight">Portal Resepsionis</h2>
                    <p class="text-xs text-slate-500">Silakan masukkan kredensial petugas Resepsionis.</p>
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
                                   placeholder="Contoh: receptionist" 
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
                        Masuk Portal Resepsionis <i data-lucide="arrow-right" class="w-4 h-4"></i>
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
                    <i data-lucide="concierge-bell" class="w-6 h-6 text-wisma-dark"></i>
                </div>
                <div>
                    <h2 class="font-outfit font-bold text-base tracking-wider leading-none">Wisma DPR RI</h2>
                    <span class="text-[10px] text-wisma-textMuted font-medium uppercase tracking-widest font-outfit">Portal Resepsionis</span>
                </div>
            </div>

            <!-- Sidebar Navigation Menu -->
            <nav class="flex-1 px-4 py-6 space-y-1.5 overflow-y-auto scrollbar-hide">
                <p class="text-[10px] text-slate-500 font-semibold px-3 mb-2 uppercase tracking-widest">Layanan Utama</p>
                
                <button @click="switchTab('receptionist_dashboard')"
                        class="w-full flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 group text-left"
                        :class="currentTab === 'receptionist_dashboard' ? 'bg-gradient-to-r from-wisma-gold to-amber-500 text-wisma-dark font-semibold shadow-lg shadow-wisma-gold/15' : 'text-wisma-textMuted hover:bg-slate-800/50 hover:text-white'">
                    <i data-lucide="layout-dashboard" class="w-5 h-5 transition-transform group-hover:scale-110"></i>
                    <span class="text-sm">Dashboard Resepsionis</span>
                </button>

                <button @click="switchTab('receptionist_check')"
                        class="w-full flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 group text-left"
                        :class="currentTab === 'receptionist_check' ? 'bg-gradient-to-r from-wisma-gold to-amber-500 text-wisma-dark font-semibold shadow-lg shadow-wisma-gold/15' : 'text-wisma-textMuted hover:bg-slate-800/50 hover:text-white'">
                    <i data-lucide="log-in" class="w-5 h-5 transition-transform group-hover:scale-110"></i>
                    <span class="text-sm">Check-In / Check-Out</span>
                    <span class="ml-auto px-2 py-0.5 bg-wisma-dark/25 rounded-md text-[10px]" x-text="bookings.filter(b => b.status === 'Lunas').length + ' Antre'"></span>
                </button>
            </nav>

            <!-- Sidebar Footer/Petugas Profile Summary -->
            <div class="p-4 border-t border-slate-800 bg-wisma-dark/40 flex items-center gap-3">
                <img class="w-10 h-10 rounded-full border border-wisma-gold/30 object-cover" 
                     src="https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?auto=format&fit=crop&w=100&h=100&q=80" 
                     alt="Resepsionis Avatar">
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
                    <i data-lucide="shield-check" class="w-5 h-5 text-emerald-500"></i>
                    <span class="text-xs font-bold font-outfit uppercase tracking-wider">Portal Resepsionis Aktif</span>
                </div>

                <div class="flex items-center gap-4">
                    <div class="flex items-center gap-3">
                        <div class="text-right">
                            <p class="text-xs font-semibold text-slate-800" x-text="profile.nama"></p>
                            <p class="text-[10px] text-slate-500" x-text="profile.instansi"></p>
                        </div>
                        <div class="w-10 h-10 rounded-xl bg-wisma-navy text-wisma-gold flex items-center justify-center font-bold text-sm border border-wisma-gold/20 shadow-sm">R</div>
                    </div>
                </div>
            </header>

            <!-- SCROLLABLE PAGE CONTENT -->
            <main class="flex-1 overflow-y-auto p-8 relative">

                <!-- 1. DASHBOARD VIEW -->
                <div x-show="currentTab === 'receptionist_dashboard'" class="space-y-8 fade-in">
                    <!-- Welcome Banner -->
                    <div class="relative overflow-hidden bg-gradient-to-r from-wisma-navy to-slate-900 text-white rounded-3xl p-8 shadow-xl">
                        <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_top_right,_var(--tw-gradient-stops))] from-emerald-500/10 via-transparent to-transparent"></div>
                        <div class="relative z-10 max-w-xl">
                            <span class="px-3 py-1 bg-emerald-500/20 text-emerald-400 text-[10px] uppercase font-bold tracking-widest rounded-full border border-emerald-400/30">Dashboard Pelayanan Tamu</span>
                            <h1 class="text-3xl font-outfit font-extrabold mt-4 mb-2 tracking-tight" x-text="'Selamat Bertugas, ' + profile.nama"></h1>
                            <p class="text-xs text-slate-300 leading-relaxed font-light">
                                Sistem Monitoring Front Desk. Pantau antrean check-in hari ini, proses pemesanan yang masuk, serta kelola hunian kamar untuk menjamin kepuasan pelayanan.
                            </p>
                            <div class="mt-6 flex gap-3">
                                <button @click="switchTab('receptionist_check')" class="px-5 py-2.5 bg-wisma-gold hover:bg-wisma-goldHover text-wisma-dark font-semibold text-xs rounded-xl shadow-lg shadow-wisma-gold/20 transition-all flex items-center gap-1">
                                    <i data-lucide="log-in" class="w-4 h-4"></i> Layani Check-In / Out
                                </button>
                            </div>
                        </div>
                        <div class="absolute right-10 bottom-0 top-0 hidden lg:flex items-center text-white/5 pointer-events-none select-none">
                            <i data-lucide="concierge-bell" class="w-64 h-64"></i>
                        </div>
                    </div>

                    <!-- Stats Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex items-center justify-between group hover:shadow-md transition-shadow">
                            <div>
                                <span class="text-xs text-slate-500 font-medium">Antrean Check-in (Lunas)</span>
                                <h3 class="text-2xl font-bold font-outfit mt-1 text-slate-900" x-text="bookings.filter(b => b.status === 'Lunas').length"></h3>
                            </div>
                            <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-500 flex items-center justify-center transition-transform group-hover:scale-110">
                                <i data-lucide="bell" class="w-6 h-6"></i>
                            </div>
                        </div>
                        <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex items-center justify-between group hover:shadow-md transition-shadow">
                            <div>
                                <span class="text-xs text-slate-500 font-medium">Tamu Sedang Menginap</span>
                                <h3 class="text-2xl font-bold font-outfit mt-1 text-slate-900" x-text="bookings.filter(b => b.status === 'Check In').length"></h3>
                            </div>
                            <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-500 flex items-center justify-center transition-transform group-hover:scale-110">
                                <i data-lucide="key" class="w-6 h-6"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 2. CHECK-IN / CHECK-OUT MANAGEMENT -->
                <div x-show="currentTab === 'receptionist_check'" class="space-y-6 fade-in" x-cloak>
                    <div>
                        <h1 class="text-2xl font-outfit font-extrabold text-slate-900">Manajemen Check-In / Check-Out</h1>
                        <p class="text-xs text-slate-500">Konfirmasi kedatangan tamu kedinasan (Check-In) atau kepulangan tamu (Check-Out) secara instan.</p>
                    </div>

                    <!-- Search and filters -->
                    <div class="bg-white p-4 rounded-2xl border border-slate-100 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
                        <div class="relative w-80">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                                <i data-lucide="search" class="w-4 h-4"></i>
                            </span>
                            <input type="text" 
                                   x-model="receptionistSearch" 
                                   placeholder="Cari nama tamu, NIP, atau no booking..." 
                                   class="w-full pl-9 pr-4 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-1 focus:ring-wisma-gold focus:outline-none transition-all">
                        </div>
                        <div class="flex gap-2">
                            <button @click="receptionistFilter = 'semua'" :class="receptionistFilter === 'semua' ? 'bg-wisma-navy text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'" class="px-4 py-2 rounded-xl text-xs font-bold transition-all">Semua</button>
                            <button @click="receptionistFilter = 'Lunas'" :class="receptionistFilter === 'Lunas' ? 'bg-wisma-navy text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'" class="px-4 py-2 rounded-xl text-xs font-bold transition-all">Antrean Check-In</button>
                            <button @click="receptionistFilter = 'Check In'" :class="receptionistFilter === 'Check In' ? 'bg-wisma-navy text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'" class="px-4 py-2 rounded-xl text-xs font-bold transition-all">Sedang Menginap</button>
                            <button @click="receptionistFilter = 'Selesai'" :class="receptionistFilter === 'Selesai' ? 'bg-wisma-navy text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'" class="px-4 py-2 rounded-xl text-xs font-bold transition-all">Check-Out Selesai</button>
                        </div>
                    </div>

                    <!-- Bookings List Table -->
                    <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-slate-50 border-b border-slate-100 text-[10px] text-slate-400 font-bold uppercase tracking-wider">
                                    <th class="py-4 px-6">No. Booking / Tamu</th>
                                    <th class="py-4 px-6">Detail Unit</th>
                                    <th class="py-4 px-6">Masa Inap (Durasi)</th>
                                    <th class="py-4 px-6">Status Booking</th>
                                    <th class="py-4 px-6 text-right">Aksi Pelayanan</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-xs">
                                <template x-for="b in filteredBookings()" :key="b.id">
                                    <tr class="hover:bg-slate-50/50 transition-all">
                                        <td class="py-4 px-6">
                                            <p class="font-bold text-slate-900" x-text="b.id"></p>
                                            <p class="text-[11px] text-slate-500 font-medium mt-0.5" x-text="b.nama"></p>
                                            <p class="text-[9px] text-slate-400 mt-0.5" x-text="'NIP: ' + b.nip"></p>
                                        </td>
                                        <td class="py-4 px-6">
                                            <p class="font-bold text-slate-900" x-text="b.unit_name"></p>
                                            <p class="text-[9px] text-slate-400 mt-0.5" x-text="b.unit_location"></p>
                                        </td>
                                        <td class="py-4 px-6">
                                            <p class="font-semibold text-slate-800" x-text="formatIndoDate(b.check_in) + ' s/d'"></p>
                                            <p class="font-semibold text-slate-800" x-text="formatIndoDate(b.check_out)"></p>
                                            <span class="text-[10px] text-slate-400 block mt-1" x-text="b.nights + ' Malam'"></span>
                                        </td>
                                        <td class="py-4 px-6">
                                            <span class="px-2 py-0.5 rounded text-[9px] uppercase font-bold tracking-wide"
                                                  :class="{
                                                      'bg-emerald-100 text-emerald-700': b.status === 'Lunas',
                                                      'bg-blue-100 text-blue-700': b.status === 'Check In',
                                                      'bg-slate-100 text-slate-600': b.status === 'Selesai'
                                                  }"
                                                  x-text="b.status === 'Check In' ? 'Aktif Menginap' : b.status"></span>
                                        </td>
                                        <td class="py-4 px-6 text-right">
                                            <template x-if="b.status === 'Lunas'">
                                                <button @click="doCheckIn(b)" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl shadow-md transition-all flex items-center gap-1.5 ml-auto">
                                                    <i data-lucide="log-in" class="w-3.5 h-3.5"></i> Proses Check In
                                                </button>
                                            </template>
                                            <template x-if="b.status === 'Check In'">
                                                <button @click="doCheckOut(b)" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-bold text-xs rounded-xl shadow-md transition-all flex items-center gap-1.5 ml-auto">
                                                    <i data-lucide="log-out" class="w-3.5 h-3.5"></i> Proses Check Out
                                                </button>
                                            </template>
                                            <template x-if="b.status === 'Selesai'">
                                                <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block">Selesai/Arsip</span>
                                            </template>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                        <div x-show="filteredBookings().length === 0" class="text-center py-12 text-slate-400">
                            <i data-lucide="inbox" class="w-12 h-12 mx-auto mb-2 text-slate-200"></i>
                            <p class="text-xs">Tidak ada reservasi yang sesuai filter pencarian.</p>
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
                    username: 'receptionist',
                    password: 'receptionist'
                },

                currentTab: 'receptionist_dashboard',
                
                receptionistSearch: '',
                receptionistFilter: 'semua',
                
                toasts: [],
                toastCount: 0,

                profile: {
                    role: 'receptionist',
                    nama: 'Amira Resepsionis',
                    role_label: 'Front Office & Resepsionis',
                    instansi: 'Front Desk Wisma'
                },

                // Shared LocalStorage data
                facilities: [],
                bookings: [],
                guests: [],

                initApp() {
                    this.loadState();
                    setTimeout(() => {
                        if (window.lucide) {
                            window.lucide.createIcons();
                        }
                    }, 100);
                },

                login() {
                    if (this.loginForm.username !== 'receptionist' || this.loginForm.password !== 'receptionist') {
                        this.addToast('Login Gagal', 'Username atau Password Resepsionis salah.', 'error');
                        return;
                    }
                    
                    this.isLoggedIn = true;
                    this.profile.role = 'receptionist';
                    this.profile.nama = 'Amira Resepsionis';
                    this.profile.role_label = 'Front Office & Resepsionis';
                    this.currentTab = 'receptionist_dashboard';
                    
                    this.addToast('Login Berhasil', `Selamat datang kembali, ${this.profile.nama}.`, 'success');
                    
                    setTimeout(() => {
                        if (window.lucide) {
                            window.lucide.createIcons();
                        }
                    }, 50);
                },

                logout() {
                    this.isLoggedIn = false;
                    this.loginForm.username = 'receptionist';
                    this.loginForm.password = 'receptionist';
                    this.addToast('Sesi Berakhir', 'Anda telah logout dari portal resepsionis.', 'info');
                    
                    setTimeout(() => {
                        if (window.lucide) {
                            window.lucide.createIcons();
                        }
                    }, 50);
                },

                persistState() {
                    localStorage.setItem('wisma_facilities', JSON.stringify(this.facilities));
                    localStorage.setItem('wisma_bookings', JSON.stringify(this.bookings));
                    localStorage.setItem('wisma_guests', JSON.stringify(this.guests));
                },

                loadState() {
                    const savedFacilities = localStorage.getItem('wisma_facilities');
                    const savedBookings = localStorage.getItem('wisma_bookings');
                    const savedGuests = localStorage.getItem('wisma_guests');
                    
                    if (savedFacilities) {
                        this.facilities = JSON.parse(savedFacilities);
                    }
                    if (savedBookings) {
                        this.bookings = JSON.parse(savedBookings);
                        // Patch older bookings data for schema compatibility
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
                                unit_name: 'VIP Suite Nusantara',
                                unit_photo: 'https://images.unsplash.com/photo-1590490360182-c33d57733427?auto=format&fit=crop&w=100&h=100&q=80',
                                unit_location: 'Wing A • Lantai 12',
                                check_in: '2026-05-10',
                                check_out: '2026-05-12',
                                nights: 2,
                                total_price: 5550000,
                                status: 'Selesai',
                                nama: 'Budi Santoso',
                                nip: '198904122015031002',
                                hasFeedback: true,
                                rating: 4.7,
                                rating_cleanliness: 5,
                                rating_facilities: 4,
                                rating_service: 5,
                                comment: 'Pelayanan wisma sangat memuaskan, kamar bersih dan fasilitas suite bintang lima.'
                            },
                            {
                                id: 'WDPR-2026-0083',
                                unit_name: 'Ruang Rapat Nusantara III',
                                unit_photo: 'https://images.unsplash.com/photo-1517502884422-41eaaced0168?auto=format&fit=crop&w=100&h=100&q=80',
                                unit_location: 'Gedung Utama • Lantai 2',
                                check_in: '2026-06-15',
                                check_out: '2026-06-16',
                                nights: 1,
                                total_price: 1200000,
                                status: 'Selesai',
                                nama: 'Dr. H. Heru Pramono',
                                nip: '197805162005011003',
                                hasFeedback: true,
                                rating: 4.3,
                                rating_cleanliness: 4,
                                rating_facilities: 4,
                                rating_service: 5,
                                comment: 'Sangat cocok untuk rapat koordinasi, fasilitas projector dan sound system sangat baik.'
                            },
                            {
                                id: 'WDPR-2026-0084',
                                unit_name: 'Executive Suite - Wing A',
                                unit_photo: 'https://images.unsplash.com/photo-1618773928121-c32242e63f39?auto=format&fit=crop&w=100&h=100&q=80',
                                unit_location: 'Wing A • Lantai 5',
                                check_in: '2026-06-20',
                                check_out: '2026-06-25',
                                nights: 5,
                                total_price: 6250000,
                                status: 'Check In',
                                nama: 'Ahmad Fauzi',
                                nip: '199112022018031001',
                                hasFeedback: false
                            }
                        ];
                        localStorage.setItem('wisma_bookings', JSON.stringify(this.bookings));
                    }
                    if (savedGuests) {
                        this.guests = JSON.parse(savedGuests);
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

                filteredBookings() {
                    return this.bookings.filter(b => {
                        const matchesSearch = b.nama.toLowerCase().includes(this.receptionistSearch.toLowerCase()) || 
                                              b.id.toLowerCase().includes(this.receptionistSearch.toLowerCase()) ||
                                              b.nip.includes(this.receptionistSearch);
                        const matchesFilter = this.receptionistFilter === 'semua' || b.status === this.receptionistFilter;
                        return matchesSearch && matchesFilter;
                    });
                },

                doCheckIn(booking) {
                    // Update Booking Status
                    const idx = this.bookings.findIndex(b => b.id === booking.id);
                    if (idx !== -1) {
                        this.bookings[idx].status = 'Check In';
                    }

                    // Update Facility Status to OCCUPIED
                    const fIdx = this.facilities.findIndex(f => f.name === booking.unit_name || f.id === booking.unit_id);
                    if (fIdx !== -1) {
                        this.facilities[fIdx].status = 'OCCUPIED';
                    }

                    this.persistState();
                    this.addToast('Check In Sukses', `Tamu ${booking.nama} resmi check-in ke kamar.`, 'success');
                    
                    setTimeout(() => {
                        if (window.lucide) window.lucide.createIcons();
                    }, 50);
                },

                doCheckOut(booking) {
                    // Update Booking Status
                    const idx = this.bookings.findIndex(b => b.id === booking.id);
                    if (idx !== -1) {
                        this.bookings[idx].status = 'Selesai';
                    }

                    // Update Facility Status to READY
                    const fIdx = this.facilities.findIndex(f => f.name === booking.unit_name || f.id === booking.unit_id);
                    if (fIdx !== -1) {
                        this.facilities[fIdx].status = 'READY';
                    }

                    this.persistState();
                    this.addToast('Check Out Sukses', `Masa inap tamu ${booking.nama} selesai. Unit siap dibersihkan.`, 'success');
                    
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
                }
            };
        }
    </script>
</body>
</html>
