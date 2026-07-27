<!DOCTYPE html>
<html lang="id" x-data="wismaApp()" x-init="initApp()">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Portal Resepsionis Wisma DPR RI - Pelayanan Tamu</title>
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('images/favicon/apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/favicon/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('images/favicon/favicon-16x16.png') }}">
    <link rel="manifest" href="{{ asset('images/favicon/site.webmanifest') }}">

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

    <!-- CONFIRMATION MODAL -->
    <div x-show="confirmModal.isOpen" class="fixed inset-0 z-[100] flex items-center justify-center overflow-hidden" x-cloak>
        <div @click="confirmModal.isOpen = false" class="absolute inset-0 bg-slate-950/60 backdrop-blur-sm transition-opacity"></div>
        <div class="bg-white rounded-3xl shadow-2xl p-8 max-w-md w-full relative z-10 space-y-6 transform scale-100 transition-all fade-in">
            <div class="text-center space-y-3">
                <div :class="confirmModal.iconBg" class="w-14 h-14 rounded-2xl flex items-center justify-center mx-auto mb-2 shadow-inner">
                    <i data-lucide="log-in" x-show="confirmModal.icon === 'log-in'" class="w-6 h-6"></i>
                    <i data-lucide="log-out" x-show="confirmModal.icon === 'log-out'" class="w-6 h-6"></i>
                </div>
                <h3 class="text-base font-extrabold text-slate-900 font-outfit" x-text="confirmModal.title"></h3>
                <p class="text-xs text-slate-600 leading-relaxed" x-html="confirmModal.message"></p>
            </div>
            
            <div class="flex items-center gap-3">
                <button @click="confirmModal.isOpen = false" class="flex-1 py-3 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-xl transition-colors">
                    Batal
                </button>
                <button @click="executeConfirm()" :class="confirmModal.btnClass" class="flex-1 py-3 font-bold text-xs rounded-xl shadow-lg transition-all" x-text="confirmModal.btnText">
                </button>
            </div>
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
                    <img src="/images/logo.png" class="w-4 h-4 object-contain rounded" alt="Logo">
                    <span class="uppercase tracking-widest font-semibold text-[10px]">Wisma DPR RI</span>
                </div>
            </div>
        </div>

        <!-- Login Form Right -->
        <div class="flex-1 h-full bg-white flex flex-col justify-between p-12">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                    <img src="/images/logo.png" class="h-9 w-auto object-contain rounded-lg" alt="Logo Wisma DPR RI">
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
                    <p class="text-xs text-slate-500">Silakan masukkan email dan password petugas Resepsionis.</p>
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
                                   placeholder="Contoh: receptionist@wisma.dpr.go.id" 
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
                    <button type="submit" :disabled="isLoading" class="w-full py-3 bg-slate-950 hover:bg-slate-900 text-white font-bold text-xs rounded-xl shadow-lg transition-colors flex items-center justify-center gap-2 disabled:opacity-75 disabled:cursor-not-allowed">
                        <span x-show="!isLoading" class="flex items-center justify-center gap-2">Masuk Portal Resepsionis <i data-lucide="arrow-right" class="w-4 h-4"></i></span>
                        <span x-show="isLoading" class="flex items-center justify-center gap-2"><i data-lucide="loader-2" class="w-4 h-4 animate-spin"></i> Memproses...</span>
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
                <img src="/images/logo.png" class="h-10 w-auto object-contain rounded-xl" alt="Logo Wisma DPR RI">
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
                    <span class="ml-auto px-2 py-0.5 bg-wisma-dark/25 rounded-md text-[10px]" x-text="bookings.filter(b => b.status === 'lunas').length + ' Antre'"></span>
                </button>

                <div class="border-t border-slate-800 my-2 mx-3"></div>
                <p class="text-[10px] text-slate-500 font-semibold px-3 mb-2 uppercase tracking-widest">Akun</p>

                <button @click="switchTab('receptionist_settings')"
                        class="w-full flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 group text-left"
                        :class="currentTab === 'receptionist_settings' ? 'bg-gradient-to-r from-wisma-gold to-amber-500 text-wisma-dark font-semibold shadow-lg shadow-wisma-gold/15' : 'text-wisma-textMuted hover:bg-slate-800/50 hover:text-white'">
                    <i data-lucide="settings" class="w-5 h-5 transition-transform group-hover:scale-110"></i>
                    <span class="text-sm">Pengaturan</span>
                </button>
            </nav>

            <!-- Sidebar Footer/Petugas Profile Summary -->
            <div class="p-4 border-t border-slate-800 bg-wisma-dark/40 flex items-center gap-3">
                <div class="w-10 h-10 rounded-full border border-wisma-gold/30 bg-emerald-700 flex items-center justify-center text-white font-bold text-sm">
                    <span x-text="profile.nama ? profile.nama.charAt(0).toUpperCase() : 'R'"></span>
                </div>
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
                                        <!-- Notifications -->
                    <div class="relative" @click.outside="notificationsOpen = false">
                        <button @click="notificationsOpen = !notificationsOpen" 
                                class="w-10 h-10 rounded-xl bg-slate-50 border border-slate-100 flex items-center justify-center text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition-colors relative shadow-sm hover:shadow">
                            <i data-lucide="bell" class="w-5 h-5"></i>
                            <template x-if="unreadNotificationCount > 0">
                                <span class="absolute top-2 right-2 w-2.5 h-2.5 bg-red-500 rounded-full border-2 border-white animate-pulse"></span>
                            </template>
                        </button>

                        <!-- Dropdown Panel -->
                        <div x-show="notificationsOpen" 
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 translate-y-1 scale-95"
                             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                             x-transition:leave="transition ease-in duration-150"
                             x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                             x-transition:leave-end="opacity-0 translate-y-1 scale-95"
                             class="absolute right-0 mt-3 w-[360px] bg-white rounded-2xl shadow-xl border border-slate-100 z-50 flex flex-col max-h-[calc(100dvh-6rem)] sm:max-h-[480px] overflow-hidden" 
                             x-cloak>
                            
                            <!-- Header -->
                            <div class="px-5 py-4 bg-slate-50/80 backdrop-blur-md border-b border-slate-100 flex items-center justify-between shrink-0">
                                <div class="flex items-center gap-2">
                                    <h3 class="text-sm font-bold text-slate-900 font-outfit">Notifikasi</h3>
                                    <span class="px-2 py-0.5 bg-red-100 text-red-600 rounded-full text-[9px] font-extrabold" x-show="unreadNotificationCount > 0" x-text="unreadNotificationCount + ' Baru'"></span>
                                </div>
                                <button @click="markNotificationsRead()" x-show="unreadNotificationCount > 0" class="text-[10px] text-indigo-600 hover:text-indigo-800 font-bold hover:underline">
                                    Tandai semua dibaca
                                </button>
                            </div>

                            <!-- List -->
                            <div class="flex-1 overflow-y-auto divide-y divide-slate-50 scrollbar-hide">
                                <template x-for="n in notifications" :key="n.id">
                                    <div @click="handleNotificationClick(n)" class="cursor-pointer px-5 py-4 hover:bg-slate-50/50 transition-colors flex gap-3 relative group"
                                         :class="!(n.read || n.is_read) ? 'bg-indigo-50/30' : ''">
                                        
                                        <!-- Unread indicator dot -->
                                        <div x-show="!(n.read || n.is_read)" class="absolute left-2.5 top-1/2 -translate-y-1/2 w-1.5 h-1.5 bg-indigo-600 rounded-full"></div>
                                        
                                        <!-- Icon -->
                                        <div class="shrink-0 w-8 h-8 rounded-lg flex items-center justify-center mt-0.5"
                                             :class="{
                                                 'bg-emerald-50 text-emerald-600': ['booking', 'checkin', 'checkout'].includes(n.type),
                                                 'bg-amber-50 text-amber-500': n.type === 'complaint',
                                                 'bg-indigo-50 text-indigo-600': n.type === 'payment',
                                                 'bg-blue-50 text-blue-600': !['booking', 'checkin', 'checkout', 'complaint', 'payment'].includes(n.type)
                                             }">
                                            <template x-if="['booking', 'checkin', 'checkout'].includes(n.type)">
                                                <i data-lucide="calendar-check" class="w-4.5 h-4.5"></i>
                                            </template>
                                            <template x-if="n.type === 'payment'">
                                                <i data-lucide="credit-card" class="w-4.5 h-4.5"></i>
                                            </template>
                                            <template x-if="n.type === 'complaint'">
                                                <i data-lucide="alert-triangle" class="w-4.5 h-4.5"></i>
                                            </template>
                                            <template x-if="!['booking', 'checkin', 'checkout', 'complaint', 'payment'].includes(n.type)">
                                                <i data-lucide="bell" class="w-4.5 h-4.5"></i>
                                            </template>
                                        </div>

                                        <!-- Message content -->
                                        <div class="flex-1 min-w-0 pr-10">
                                            <div>
                                                <h4 class="text-xs font-bold text-slate-800 truncate" x-text="n.title"></h4>
                                                <p class="text-[11px] text-slate-500 leading-normal mt-0.5 font-light" x-text="n.message"></p>
                                                <span class="text-[9px] text-slate-400 font-medium block mt-1" x-text="formatTime(n.created_at)"></span>
                                            </div>
                                            <button @click.stop="deleteNotification(n.id)" class="absolute right-4 top-1/2 -translate-y-1/2 p-1.5 text-slate-300 hover:text-red-500 hover:bg-red-50 rounded-lg transition-all z-10 opacity-0 group-hover:opacity-100" title="Hapus Notifikasi">
                                                <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                                            </button>
                                        </div>
                                    </div>
                                </template>

                                <div x-show="notifications.length === 0" class="text-center py-12 text-slate-400">
                                    <i data-lucide="bell-off" class="w-10 h-10 mx-auto mb-2 text-slate-200"></i>
                                    <p class="text-xs font-medium text-slate-500">Belum ada notifikasi.</p>
                                </div>
                            </div>
                            
                            <!-- Footer -->
                            <div class="p-3 bg-slate-50 border-t border-slate-100 flex justify-center shrink-0">
                                <button @click="notificationsOpen = false" class="text-xs text-slate-500 hover:text-slate-700 font-bold">
                                    Tutup
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="w-px h-6 bg-slate-200 mx-2 hidden sm:block"></div>
                    <div class="flex items-center gap-3">
                        <div class="text-right hidden sm:block">
                            <p class="text-xs font-semibold text-slate-800" x-text="profile.nama"></p>
                            <p class="text-[10px] text-slate-500">Resepsionis</p>
                        </div>
                        <div class="w-10 h-10 rounded-xl bg-wisma-navy text-wisma-gold flex items-center justify-center font-bold text-sm border border-wisma-gold/20 shadow-sm" x-text="getInitials(profile.nama)"></div>
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
                                Sistem Monitoring Front Desk. Pantau antrean check-in hari ini, proses pemesanan yang masuk, serta kelola hunian bungalow untuk menjamin kepuasan pelayanan.
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
                                <h3 class="text-2xl font-bold font-outfit mt-1 text-slate-900" x-text="bookings.filter(b => b.status === 'lunas').length"></h3>
                            </div>
                            <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-500 flex items-center justify-center transition-transform group-hover:scale-110">
                                <i data-lucide="bell" class="w-6 h-6"></i>
                            </div>
                        </div>
                        <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex items-center justify-between group hover:shadow-md transition-shadow">
                            <div>
                                <span class="text-xs text-slate-500 font-medium">Tamu Sedang Menginap</span>
                                <h3 class="text-2xl font-bold font-outfit mt-1 text-slate-900" x-text="bookings.filter(b => b.status === 'check_in').length"></h3>
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
                        <div class="flex gap-2 flex-wrap">
                            <button @click="receptionistFilter = 'semua'; setTimeout(() => { if (window.lucide) window.lucide.createIcons(); }, 50);" :class="receptionistFilter === 'semua' ? 'bg-wisma-navy text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'" class="px-4 py-2 rounded-xl text-xs font-bold transition-all">Semua</button>
                            <button @click="receptionistFilter = 'lunas'; setTimeout(() => { if (window.lucide) window.lucide.createIcons(); }, 50);" :class="receptionistFilter === 'lunas' ? 'bg-wisma-navy text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'" class="px-4 py-2 rounded-xl text-xs font-bold transition-all">Antrean Check-In</button>
                            <button @click="receptionistFilter = 'check_in'; setTimeout(() => { if (window.lucide) window.lucide.createIcons(); }, 50);" :class="receptionistFilter === 'check_in' ? 'bg-wisma-navy text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'" class="px-4 py-2 rounded-xl text-xs font-bold transition-all">Sedang Menginap</button>
                            <button @click="receptionistFilter = 'selesai'; setTimeout(() => { if (window.lucide) window.lucide.createIcons(); }, 50);" :class="receptionistFilter === 'selesai' ? 'bg-wisma-navy text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'" class="px-4 py-2 rounded-xl text-xs font-bold transition-all">Check-Out Selesai</button>
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
                                            <p class="font-bold text-slate-900" x-text="b.booking_code"></p>
                                            <p class="text-[11px] text-slate-500 font-medium mt-0.5" x-text="b.guest_name"></p>
                                            <p class="text-[9px] text-slate-400 mt-0.5" x-text="'NIP: ' + (b.guest_nip || '-')"></p>
                                        </td>
                                        <td class="py-4 px-6">
                                            <p class="font-bold text-slate-900" x-text="b.facility ? b.facility.name : '-'"></p>
                                            <p class="text-[9px] text-slate-400 mt-0.5" x-text="b.facility ? b.facility.area : ''"></p>
                                        </td>
                                        <td class="py-4 px-6">
                                            <p class="font-semibold text-slate-800" x-text="formatIndoDate(b.check_in) + ' s/d'"></p>
                                            <p class="font-semibold text-slate-800" x-text="formatIndoDate(b.check_out)"></p>
                                            <span class="text-[10px] text-slate-400 block mt-1" x-text="b.nights + ' Malam/Hari'"></span>
                                        </td>
                                        <td class="py-4 px-6">
                                            <span class="px-2 py-0.5 rounded text-[9px] uppercase font-bold tracking-wide"
                                                  :class="{
                                                      'bg-yellow-100 text-yellow-700': b.status === 'pending',
                                                      'bg-emerald-100 text-emerald-700': b.status === 'lunas',
                                                      'bg-blue-100 text-blue-700': b.status === 'check_in',
                                                      'bg-slate-100 text-slate-600': b.status === 'selesai',
                                                      'bg-red-100 text-red-600': b.status === 'cancelled'
                                                  }"
                                                  x-text="statusLabel(b.status)"></span>
                                        </td>
                                        <td class="py-4 px-6 text-right">
                                            <template x-if="b.status === 'lunas'">
                                                <button @click="showConfirm('checkin', b)" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl shadow-md transition-all flex items-center gap-1.5 ml-auto">
                                                    <i data-lucide="log-in" class="w-3.5 h-3.5"></i> Proses Check In
                                                </button>
                                            </template>
                                            <template x-if="b.status === 'check_in'">
                                                <button @click="showConfirm('checkout', b)" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-bold text-xs rounded-xl shadow-md transition-all flex items-center gap-1.5 ml-auto">
                                                    <i data-lucide="log-out" class="w-3.5 h-3.5"></i> Proses Check Out
                                                </button>
                                            </template>
                                            <template x-if="b.status === 'selesai'">
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

                <!-- SETTINGS VIEW -->
                <div x-show="currentTab === 'receptionist_settings'" class="space-y-6 fade-in" x-cloak>
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
                                    <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-emerald-600 to-emerald-800 text-white flex items-center justify-center font-bold text-xl font-outfit shadow-lg" x-text="profile.nama ? profile.nama.charAt(0).toUpperCase() : 'R'">
                                    </div>
                                    <div>
                                        <h2 class="text-base font-bold text-slate-900 font-outfit" x-text="profile.nama"></h2>
                                        <p class="text-xs text-slate-500 mt-0.5" x-text="profile.role_label"></p>
                                        <span class="inline-block mt-1.5 px-2 py-0.5 bg-emerald-50 text-emerald-700 text-[10px] font-bold rounded-md uppercase tracking-wide">Resepsionis</span>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                    <div class="space-y-1.5">
                                        <label class="text-[10px] text-slate-500 font-bold uppercase tracking-wide block">Nama Lengkap</label>
                                        <input type="text" x-model="settingsProfile.nama" class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl p-3 focus:ring-1 focus:ring-wisma-gold focus:bg-white focus:outline-none transition-all">
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
                                            <input :type="settingsPasswordVisible.new ? 'text' : 'password'" x-model="settingsPassword.new" placeholder="Min. 8 karakter" class="w-full pl-10 pr-10 text-xs bg-slate-50 border border-slate-200 rounded-xl p-3 focus:ring-1 focus:ring-wisma-gold focus:bg-white focus:outline-none transition-all">
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
                getInitials(name) {
                    if (!name) return '?';
                    const parts = name.trim().split(' ');
                    if (parts.length > 1) {
                        return (parts[0][0] + parts[parts.length - 1][0]).toUpperCase();
                    }
                    return (parts[0][0] || '?').toUpperCase();
                },
                isLoading: false,
                passwordVisible: false,
                loginForm: {
                    email: 'receptionist@wisma.dpr.go.id',
                    password: 'password'
                },

                currentTab: 'receptionist_dashboard',
                
                receptionistSearch: '',
                receptionistFilter: 'semua',
                
                toasts: [],
                toastCount: 0,
                
                notifications: [],
                notificationsOpen: false,

                // Settings state
                settingsTab: 'profil',
                settingsProfile: {
                    nama: '',
                    nip: '',
                    instansi: ''
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

                // Confirmation Modal State
                confirmModal: {
                    isOpen: false,
                    title: '',
                    message: '',
                    icon: 'log-in',
                    iconBg: 'bg-emerald-100 text-emerald-600',
                    btnText: 'Check In',
                    btnClass: 'bg-emerald-600 hover:bg-emerald-700 text-white',
                    booking: null,
                    action: ''
                },

                profile: {
                    id: null,
                    role: 'receptionist',
                    nama: '',
                    nip: '',
                    phone: '',
                    email: '',
                    instansi: '',
                    role_label: 'Resepsionis'
                },

                // Data from API
                facilities: [],
                bookings: [],

                // ==========================================
                // HELPER: API CALL
                // ==========================================
                async apiCall(method, path, body = null) {
                    const token = localStorage.getItem('wisma_token');
                    const headers = {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        ...(token ? { 'Authorization': 'Bearer ' + token } : {}),
                    };
                    const opts = {
                        method,
                        headers,
                        ...(body ? { body: JSON.stringify(body) } : {}),
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
                            if (me.data.role !== 'receptionist') {
                                window.location.href = '/' + (me.data.role === 'koordinator_wisma' ? 'admin' : me.data.role);
                                return;
                            }
                            this.fillProfile(me.data);
                            this.isLoggedIn = true;
                            await this.loadBookings();
                            await this.loadNotifications();
                        } else {
                            localStorage.removeItem('wisma_token');
                        }
                    }
                    setTimeout(() => {
                        if (window.lucide) window.lucide.createIcons();
                    }, 100);
                },

                fillProfile(user) {
                    const roleLabels = {
                        receptionist: 'Resepsionis',
                        koordinator_wisma: 'Koordinator Wisma',
                    };
                    this.profile.id         = user.id;
                    this.profile.role       = user.role;
                    this.profile.role_label = roleLabels[user.role] || user.role;
                    this.profile.nama       = user.name;
                    this.profile.nip        = user.nip || '';
                    this.profile.phone      = user.phone || '';
                    this.profile.email      = user.email;
                    this.profile.instansi   = user.instansi || '';

                    // Pre-fill settings forms
                    this.settingsProfile.nama    = user.name;
                    this.settingsProfile.nip     = user.nip || '';
                    this.settingsProfile.instansi= user.instansi || '';
                    this.settingsContact.email   = user.email;
                    this.settingsContact.telepon = user.phone || '';
                },

                async loadBookings() {
                    try {
                        const res = await this.apiCall('GET', '/bookings');
                        if (res.success) {
                            this.bookings = res.data;
                        }
                    } catch (e) {
                        console.error('Gagal memuat bookings:', e);
                    }
                },

                async loadNotifications() {
                    try {
                        const res = await this.apiCall('GET', '/notifications');
                        if (res.success) {
                            this.notifications = res.data.map(n => ({
                                ...n,
                                read: n.is_read || n.read,
                                time: n.created_at || n.time
                            }));
                        }
                    } catch (e) {
                        console.error('Gagal memuat notifikasi:', e);
                    }
                },

                get unreadNotificationCount() {
                    return this.notifications.filter(n => !n.read).length;
                },

                async handleNotificationClick(n) {
                    if (!n.read) {
                        try {
                            const res = await this.apiCall('PUT', `/notifications/${n.id}/read`);
                            if (res.success) {
                                n.read = true;
                            }
                        } catch (e) {
                            console.error(e);
                        }
                    }
                    this.notificationsOpen = false;
                },

                async markNotificationsRead() {
                    try {
                        const res = await this.apiCall('PUT', '/notifications/read-all');
                        if (res.success) {
                            this.notifications.forEach(n => n.read = true);
                        }
                    } catch (e) {
                        console.error(e);
                    }
                },

                async deleteNotification(id) {
                    try {
                        const res = await this.apiCall('DELETE', `/notifications/${id}`);
                        if (res.success) {
                            this.notifications = this.notifications.filter(n => n.id !== id);
                        }
                    } catch (e) {
                        console.error(e);
                    }
                },

                // ==========================================
                // LOGIN
                // ==========================================
                async login() {
                    if (!this.loginForm.email || !this.loginForm.password) {
                        this.addToast('Data Tidak Lengkap', 'Email dan Password wajib diisi.', 'error');
                        return;
                    }
                    this.isLoading = true;
                    try {
                        const res = await this.apiCall('POST', '/login', {
                            email:    this.loginForm.email,
                            password: this.loginForm.password,
                        });
                        if (res.success) {
                            const role = res.data.user.role;
                            if (role !== 'receptionist' && role !== 'koordinator_wisma') {
                                this.addToast('Akses Ditolak', 'Akun ini tidak memiliki akses ke portal Resepsionis.', 'error');
                                return;
                            }
                            localStorage.setItem('wisma_token', res.data.token);
                            this.fillProfile(res.data.user);
                            this.isLoggedIn = true;
                            this.currentTab = 'receptionist_dashboard';
                            this.addToast('Login Berhasil', `Selamat bertugas, ${this.profile.nama}.`, 'success');
                            await this.loadBookings();
                            await this.loadNotifications();
                            setTimeout(() => { if (window.lucide) window.lucide.createIcons(); }, 100);
                        } else {
                            const msg = res.message || 'Email atau password salah.';
                            this.addToast('Login Gagal', msg, 'error');
                        }
                    } catch (e) {
                        this.addToast('Koneksi Gagal', 'Tidak dapat terhubung ke server.', 'error');
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
                    this.bookings   = [];
                    this.profile    = { id: null, role: 'receptionist', nama: '', nip: '', phone: '', email: '', instansi: '', role_label: 'Resepsionis' };
                    this.addToast('Logout Sukses', 'Anda telah keluar dari portal resepsionis.', 'info');
                    setTimeout(() => { if (window.lucide) window.lucide.createIcons(); }, 50);
                },

                // ==========================================
                // CHECK-IN / CHECK-OUT
                // ==========================================
                showConfirm(action, booking) {
                    this.confirmModal.action = action;
                    this.confirmModal.booking = booking;
                    this.confirmModal.isOpen = true;

                    if (action === 'checkin') {
                        this.confirmModal.title = 'Konfirmasi Check-In';
                        this.confirmModal.message = `Apakah tamu atas nama <b>${booking.guest_name}</b> sudah berada di lobi dan siap untuk menerima kunci <b>${booking.unit_name || booking.facility?.name}</b>?`;
                        this.confirmModal.icon = 'log-in';
                        this.confirmModal.iconBg = 'bg-emerald-100 text-emerald-600';
                        this.confirmModal.btnText = 'Proses Check-In';
                        this.confirmModal.btnClass = 'bg-emerald-600 hover:bg-emerald-700 text-white';
                    } else if (action === 'checkout') {
                        this.confirmModal.title = 'Konfirmasi Check-Out';
                        this.confirmModal.message = `Apakah tamu atas nama <b>${booking.guest_name}</b> akan melakukan check-out dan telah mengembalikan kunci <b>${booking.unit_name || booking.facility?.name}</b>?`;
                        this.confirmModal.icon = 'log-out';
                        this.confirmModal.iconBg = 'bg-red-100 text-red-600';
                        this.confirmModal.btnText = 'Proses Check-Out';
                        this.confirmModal.btnClass = 'bg-red-600 hover:bg-red-700 text-white';
                    }
                    setTimeout(() => { if (window.lucide) window.lucide.createIcons(); }, 50);
                },

                async executeConfirm() {
                    this.confirmModal.isOpen = false;
                    const b = this.confirmModal.booking;
                    if (this.confirmModal.action === 'checkin') {
                        try {
                            const res = await this.apiCall('PUT', `/bookings/${b.id}/checkin`);
                            if (res.success) {
                                const idx = this.bookings.findIndex(bk => bk.id === b.id);
                                if (idx !== -1) this.bookings[idx].status = 'check_in';
                                this.addToast('Check In Sukses', `Tamu ${b.guest_name} resmi check-in ke ${b.unit_name || b.facility?.name}.`, 'success');
                            } else {
                                this.addToast('Gagal', res.message || 'Gagal memproses check-in.', 'error');
                            }
                        } catch (e) {
                            this.addToast('Koneksi Gagal', 'Tidak dapat terhubung ke server.', 'error');
                        }
                    } else if (this.confirmModal.action === 'checkout') {
                        try {
                            const res = await this.apiCall('PUT', `/bookings/${b.id}/checkout`);
                            if (res.success) {
                                const idx = this.bookings.findIndex(bk => bk.id === b.id);
                                if (idx !== -1) this.bookings[idx].status = 'selesai';
                                this.addToast('Check Out Sukses', `Masa inap tamu ${b.guest_name} selesai.`, 'success');
                            } else {
                                this.addToast('Gagal', res.message || 'Gagal memproses check-out.', 'error');
                            }
                        } catch (e) {
                            this.addToast('Koneksi Gagal', 'Tidak dapat terhubung ke server.', 'error');
                        }
                    }
                    setTimeout(() => { if (window.lucide) window.lucide.createIcons(); }, 50);
                },

                // ==========================================
                // FILTERS & HELPERS
                // ==========================================
                filteredBookings() {
                    return this.bookings.filter(b => {
                        const search = this.receptionistSearch.toLowerCase();
                        const matchesSearch = !search ||
                            (b.guest_name || '').toLowerCase().includes(search) ||
                            (b.booking_code || '').toLowerCase().includes(search) ||
                            (b.guest_nip || '').includes(search);
                        const matchesFilter = this.receptionistFilter === 'semua' || b.status === this.receptionistFilter;
                        return matchesSearch && matchesFilter;
                    });
                },

                statusLabel(status) {
                    const labels = {
                        pending: 'Menunggu Bayar',
                        lunas: 'Lunas',
                        check_in: 'Aktif Menginap',
                        selesai: 'Selesai',
                        cancelled: 'Dibatalkan',
                    };
                    return labels[status] || status;
                },

                formatIndoDate(dateStr) {
                    if (!dateStr) return '-';
                    const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agt', 'Sep', 'Okt', 'Nov', 'Des'];
                    const d = new Date(dateStr);
                    return `${d.getDate()} ${months[d.getMonth()]} ${d.getFullYear()}`;
                },

                formatTime(dateStr) {
                    if (!dateStr) return '-';
                    const d = new Date(dateStr);
                    return d.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
                },

                switchTab(tab) {
                    this.currentTab = tab;
                    setTimeout(() => { if (window.lucide) window.lucide.createIcons(); }, 50);
                },

                // ==========================================
                // SETTINGS PROFILE & PASSWORD
                // ==========================================
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
                            this.addToast('Profil Diperbarui', 'Data profil berhasil disimpan.', 'success');
                        } else {
                            const errors = res.errors ? Object.values(res.errors).flat().join(' ') : (res.message || 'Gagal memperbarui profil.');
                            this.addToast('Gagal', errors, 'error');
                        }
                    } catch (e) {
                        this.addToast('Koneksi Gagal', 'Tidak dapat terhubung ke server.', 'error');
                    }
                },

                async saveSettingsContact() {
                    await this.saveSettingsProfile();
                },

                async saveSettingsPassword() {
                    if (!this.settingsPassword.current) {
                        this.addToast('Gagal', 'Masukkan kata sandi saat ini.', 'error'); return;
                    }
                    if (this.settingsPassword.new.length < 8) {
                        this.addToast('Gagal', 'Kata sandi baru minimal 8 karakter.', 'error'); return;
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
                            this.addToast('Kata Sandi Diperbarui', 'Kata sandi berhasil diubah.', 'success');
                        } else {
                            const errors = res.errors ? Object.values(res.errors).flat().join(' ') : (res.message || 'Gagal mengubah kata sandi.');
                            this.addToast('Gagal', errors, 'error');
                        }
                    } catch (e) {
                        this.addToast('Koneksi Gagal', 'Tidak dapat terhubung ke server.', 'error');
                    }
                },

                // ==========================================
                // TOAST
                // ==========================================
                addToast(title, message, type = 'success') {
                    const id = this.toastCount++;
                    this.toasts.push({ id, title, message, type });
                    setTimeout(() => { if (window.lucide) window.lucide.createIcons(); }, 20);
                    setTimeout(() => { this.removeToast(id); }, 4000);
                },

                removeToast(id) {
                    this.toasts = this.toasts.filter(t => t.id !== id);
                },
            };
        }
    </script>
</body>
</html>