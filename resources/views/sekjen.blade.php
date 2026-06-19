<!DOCTYPE html>
<html lang="id" x-data="wismaSekjenApp()" x-init="initApp()">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Portal Sekjen Wisma DPR RI - Monitoring & Laporan</title>

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
        @keyframes slideInRight {
            from { transform: translateX(100%); }
            to { transform: translateX(0); }
        }

        .glass-panel {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.08);
        }

        /* Printable area formatting */
        @media print {
            body * {
                visibility: hidden;
            }
            #printable-report, #printable-report * {
                visibility: visible;
            }
            #printable-report {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
            }
        }
    </style>
</head>
<body class="bg-[#F8FAFC] text-slate-800 font-sans min-h-screen flex overflow-hidden">

    <!-- Global Toast Notification -->
    <div class="fixed top-5 right-5 z-[100] space-y-2 pointer-events-none">
        <template x-for="toast in toasts" :key="toast.id">
            <div class="flex items-center gap-3 bg-white text-slate-800 border-l-4 border-cyan-500 px-4 py-3 rounded-lg shadow-xl pointer-events-auto transform translate-y-0 transition-all duration-300 max-w-sm fade-in"
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

    <!-- REPORT DOWNLOAD LOADING MODAL -->
    <div x-show="downloading" class="fixed inset-0 z-[110] flex items-center justify-center overflow-hidden" x-cloak>
        <div class="absolute inset-0 bg-slate-950/70 backdrop-blur-sm transition-opacity"></div>
        <div class="bg-white rounded-3xl shadow-2xl p-8 max-w-md w-full relative z-10 space-y-6 text-center fade-in">
            <div class="w-16 h-16 bg-cyan-50 text-cyan-600 rounded-full flex items-center justify-center mx-auto animate-pulse">
                <i data-lucide="file-down" class="w-8 h-8"></i>
            </div>
            <div class="space-y-2">
                <h3 class="text-lg font-extrabold text-slate-950 font-outfit" x-text="downloadStatusTitle">Menyiapkan Laporan</h3>
                <p class="text-xs text-slate-500" x-text="downloadStatusDesc">Harap tunggu sebentar...</p>
            </div>

            <!-- Progress Bar -->
            <div class="space-y-1">
                <div class="w-full h-2 bg-slate-100 rounded-full overflow-hidden">
                    <div class="bg-gradient-to-r from-cyan-500 to-blue-600 h-full rounded-full transition-all duration-300" :style="'width: ' + downloadProgress + '%'"></div>
                </div>
                <div class="flex justify-between text-[10px] text-slate-400 font-bold">
                    <span x-text="downloadProgress + '%'"></span>
                    <span>100%</span>
                </div>
            </div>
        </div>
    </div>

    <!-- 1. LOGIN SCREEN -->
    <div x-show="!isLoggedIn" class="w-full h-screen flex relative z-30 fade-in">
        <!-- Cover Section Left -->
        <div class="w-[55%] h-full bg-slate-900 relative overflow-hidden hidden md:block">
            <!-- Background wallpaper using dynamic image tool recommendation -->
            <img class="absolute inset-0 w-full h-full object-cover opacity-35" 
                 src="https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?auto=format&fit=crop&w=1200&q=80">
            <div class="absolute inset-0 bg-gradient-to-t from-wisma-dark via-wisma-dark/55 to-transparent"></div>
            
            <div class="absolute inset-x-12 bottom-16 space-y-8 z-10">
                <div class="space-y-4">
                    <span class="px-3 py-1 bg-cyan-500/20 text-cyan-400 text-[10px] uppercase font-bold tracking-widest rounded-full border border-cyan-400/30 flex items-center gap-1.5 w-max">
                        <i data-lucide="shield" class="w-3 h-3"></i> Sekretariat Jenderal Portal
                    </span>
                    <h1 class="text-4xl font-outfit font-extrabold text-white tracking-tight leading-tight max-w-lg">
                        Pusat Monitoring Eksekutif & Laporan Wisma DPR RI
                    </h1>
                    <p class="text-xs text-slate-300 leading-relaxed max-w-md font-light">
                        Portal monitoring resmi untuk Sekretariat Jenderal. Lakukan pengawasan okupansi secara real-time, evaluasi log keluhan pelayanan tamu, pantau pemakaian ruang rapat komisi, dan buat laporan pertanggungjawaban anggaran DIPA.
                    </p>
                </div>

                <div class="grid grid-cols-3 gap-6 pt-4 border-t border-white/10 max-w-lg text-white">
                    <div>
                        <h4 class="text-2xl font-bold font-outfit text-cyan-400" x-text="getOccupancyRate() + '%'">78%</h4>
                        <span class="text-[10px] text-slate-400 block mt-0.5">Okupansi Kamar</span>
                    </div>
                    <div>
                        <h4 class="text-2xl font-bold font-outfit text-cyan-400" x-text="complaints.filter(c => c.status !== 'Resolved').length">2</h4>
                        <span class="text-[10px] text-slate-400 block mt-0.5">Keluhan Aktif</span>
                    </div>
                    <div>
                        <h4 class="text-2xl font-bold font-outfit text-cyan-400">DIPA</h4>
                        <span class="text-[10px] text-slate-400 block mt-0.5">Realisasi Laporan</span>
                    </div>
                </div>

                <div class="flex items-center gap-2.5 text-white/50 text-xs">
                    <i data-lucide="landmark" class="w-4 h-4"></i>
                    <span class="uppercase tracking-widest font-semibold text-[10px]">Sekretariat Jenderal DPR RI</span>
                </div>
            </div>
        </div>

        <!-- Login Form Right -->
        <div class="flex-1 h-full bg-white flex flex-col justify-between p-12">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-lg bg-cyan-50 text-cyan-600 flex items-center justify-center border border-cyan-100">
                        <i data-lucide="landmark" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <h2 class="font-outfit font-bold text-sm text-slate-900 tracking-wider leading-none">Wisma DPR RI</h2>
                        <span class="text-[9px] text-slate-400 font-medium uppercase tracking-widest">Sekretariat Jenderal</span>
                    </div>
                </div>
                <a href="/" class="flex items-center gap-1.5 text-slate-500 hover:text-slate-900 text-xs font-bold transition-colors">
                    <i data-lucide="arrow-left" class="w-4 h-4"></i> Kembali
                </a>
            </div>

            <div class="max-w-md w-full mx-auto space-y-8">
                <div class="space-y-2 text-center md:text-left">
                    <h2 class="text-2xl font-extrabold text-slate-950 font-outfit tracking-tight">Portal Sekjen</h2>
                    <p class="text-xs text-slate-500">Silakan masuk menggunakan kredensial Sekretariat Jenderal Anda.</p>
                </div>

                <form @submit.prevent="login()" class="space-y-5">
                    <!-- Username -->
                    <div class="space-y-1">
                        <label class="text-[10px] text-slate-500 font-bold uppercase tracking-wide block">Username</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                <i data-lucide="user" class="w-4.5 h-4.5"></i>
                            </span>
                            <input type="text" 
                                   x-model="loginForm.username"
                                   placeholder="Contoh: sekjen" 
                                   class="w-full pl-10 pr-4 py-3 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-1 focus:ring-cyan-500 focus:outline-none transition-all">
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
                                   class="w-full pl-10 pr-12 py-3 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-1 focus:ring-cyan-500 focus:outline-none transition-all">
                            <button type="button" @click="passwordVisible = !passwordVisible" class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600">
                                <i :data-lucide="passwordVisible ? 'eye-off' : 'eye'" class="w-4.5 h-4.5"></i>
                            </button>
                        </div>
                    </div>

                    <button type="submit" class="w-full py-3 bg-slate-950 hover:bg-slate-900 text-white font-bold text-xs rounded-xl shadow-lg transition-colors flex items-center justify-center gap-2">
                        Masuk Portal Sekjen <i data-lucide="arrow-right" class="w-4 h-4"></i>
                    </button>
                </form>
            </div>

            <!-- Footer copyright -->
            <div class="flex items-center justify-between text-[10px] text-slate-400 pt-8 border-t border-slate-100 w-full">
                <span>© 2026 Sekretariat Jenderal DPR RI. Semua Hak Dilindungi.</span>
            </div>
        </div>
    </div>

    <!-- MAIN PORTAL DASHBOARD (Visible if logged in) -->
    <div x-show="isLoggedIn" class="flex-1 flex h-screen overflow-hidden" x-cloak>
        
        <!-- SIDEBAR -->
        <aside class="w-72 bg-wisma-navy text-white flex flex-col shrink-0 h-screen shadow-2xl relative z-20">
            <!-- Logo Area -->
            <div class="p-6 border-b border-slate-800 flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-cyan-500 to-blue-500 flex items-center justify-center shadow-lg shadow-cyan-500/20">
                    <i data-lucide="pie-chart" class="w-6 h-6 text-slate-950"></i>
                </div>
                <div>
                    <h2 class="font-outfit font-bold text-base tracking-wider leading-none">Wisma DPR RI</h2>
                    <span class="text-[10px] text-wisma-textMuted font-medium uppercase tracking-widest font-outfit">Sekretariat Jenderal</span>
                </div>
            </div>

            <!-- Sidebar Navigation Menu -->
            <nav class="flex-1 px-4 py-6 space-y-1.5 overflow-y-auto scrollbar-hide">
                <p class="text-[10px] text-slate-500 font-semibold px-3 mb-2 uppercase tracking-widest">Dashboard & Analitis</p>
                
                <button @click="switchTab('sekjen_dashboard')"
                        class="w-full flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 group text-left"
                        :class="currentTab === 'sekjen_dashboard' ? 'bg-gradient-to-r from-cyan-600 to-cyan-500 text-white font-semibold shadow-lg shadow-cyan-600/15' : 'text-wisma-textMuted hover:bg-slate-800/50 hover:text-white'">
                    <i data-lucide="layout-dashboard" class="w-5 h-5 transition-transform group-hover:scale-110"></i>
                    <span class="text-sm">Ringkasan Eksekutif</span>
                </button>

                <button @click="switchTab('sekjen_reports')"
                        class="w-full flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 group text-left"
                        :class="currentTab === 'sekjen_reports' ? 'bg-gradient-to-r from-cyan-600 to-cyan-500 text-white font-semibold shadow-lg shadow-cyan-600/15' : 'text-wisma-textMuted hover:bg-slate-800/50 hover:text-white'">
                    <i data-lucide="file-bar-chart" class="w-5 h-5 transition-transform group-hover:scale-110"></i>
                    <span class="text-sm">Laporan & Analitik</span>
                </button>

                <p class="text-[10px] text-slate-500 font-semibold px-3 pt-6 mb-2 uppercase tracking-widest">Pemantauan Lapangan</p>

                <button @click="switchTab('sekjen_rooms')"
                        class="w-full flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 group text-left"
                        :class="currentTab === 'sekjen_rooms' ? 'bg-gradient-to-r from-cyan-600 to-cyan-500 text-white font-semibold shadow-lg shadow-cyan-600/15' : 'text-wisma-textMuted hover:bg-slate-800/50 hover:text-white'">
                    <i data-lucide="home" class="w-5 h-5 transition-transform group-hover:scale-110"></i>
                    <span class="text-sm">Okupansi Kamar</span>
                    <span class="ml-auto px-2 py-0.5 bg-wisma-dark/25 rounded-md text-[10px]" x-text="getOccupancyRate() + '%'"></span>
                </button>

                <button @click="switchTab('sekjen_meetings')"
                        class="w-full flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 group text-left"
                        :class="currentTab === 'sekjen_meetings' ? 'bg-gradient-to-r from-cyan-600 to-cyan-500 text-white font-semibold shadow-lg shadow-cyan-600/15' : 'text-wisma-textMuted hover:bg-slate-800/50 hover:text-white'">
                    <i data-lucide="calendar" class="w-5 h-5 transition-transform group-hover:scale-110"></i>
                    <span class="text-sm">Jadwal Rapat & Kegiatan</span>
                    <span class="ml-auto px-2 py-0.5 bg-wisma-dark/25 rounded-md text-[10px]" x-text="bookings.filter(b => b.type === 'Ruang Rapat' && b.status === 'Check In').length"></span>
                </button>

                <button @click="switchTab('sekjen_complaints')"
                        class="w-full flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 group text-left"
                        :class="currentTab === 'sekjen_complaints' ? 'bg-gradient-to-r from-cyan-600 to-cyan-500 text-white font-semibold shadow-lg shadow-cyan-600/15' : 'text-wisma-textMuted hover:bg-slate-800/50 hover:text-white'">
                    <i data-lucide="alert-triangle" class="w-5 h-5 transition-transform group-hover:scale-110"></i>
                    <span class="text-sm">Keluhan & Evaluasi</span>
                    <span class="ml-auto px-2 py-0.5 bg-red-950/45 text-red-400 rounded-md text-[10px] font-bold" x-text="complaints.filter(c => c.status !== 'Resolved').length"></span>
                </button>
            </nav>

            <!-- Sidebar Footer/User Profile Summary -->
            <div class="p-4 border-t border-slate-800 bg-wisma-dark/40 flex items-center gap-3">
                <img class="w-10 h-10 rounded-full border border-cyan-500/30 object-cover" 
                     src="https://images.unsplash.com/photo-1560250097-0b93528c311a?auto=format&fit=crop&w=100&h=100&q=80" 
                     alt="Sekjen Avatar">
                <div class="overflow-hidden font-sans">
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
                    <i data-lucide="shield-check" class="w-5 h-5 text-cyan-500"></i>
                    <span class="text-xs font-bold font-outfit uppercase tracking-wider">Mode Pengawasan Sekretariat Jenderal Aktif</span>
                </div>

                <div class="flex items-center gap-4">
                    <div class="text-xs font-bold text-slate-500 bg-slate-100 py-1.5 px-3 rounded-lg flex items-center gap-2">
                        <i data-lucide="clock" class="w-4 h-4 text-cyan-600"></i>
                        <span x-text="getFormattedTime()"></span>
                    </div>

                    <div class="w-px h-6 bg-slate-200 mx-1"></div>

                    <div class="flex items-center gap-3">
                        <div class="text-right">
                            <p class="text-xs font-semibold text-slate-800" x-text="profile.nama"></p>
                            <p class="text-[10px] text-slate-500" x-text="profile.instansi"></p>
                        </div>
                        <div class="w-10 h-10 rounded-xl bg-cyan-650 bg-cyan-950 text-cyan-400 flex items-center justify-center font-bold text-sm border border-cyan-800/30 shadow-sm">SJ</div>
                    </div>
                </div>
            </header>

            <!-- SCROLLABLE PAGE CONTENT -->
            <main class="flex-1 overflow-y-auto p-8 relative">

                <!-- 1. DASHBOARD VIEW -->
                <div x-show="currentTab === 'sekjen_dashboard'" class="space-y-8 fade-in">
                    <!-- Welcome Banner -->
                    <div class="relative overflow-hidden bg-gradient-to-r from-wisma-navy to-slate-900 text-white rounded-3xl p-8 shadow-xl">
                        <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_top_right,_var(--tw-gradient-stops))] from-cyan-500/15 via-transparent to-transparent"></div>
                        <div class="relative z-10 max-w-xl">
                            <span class="px-3 py-1 bg-cyan-500/20 text-cyan-400 text-[10px] uppercase font-bold tracking-widest rounded-full border border-cyan-400/30">Executive Cockpit</span>
                            <h1 class="text-3xl font-outfit font-extrabold mt-4 mb-2 tracking-tight" x-text="'Selamat Datang Sekjen, ' + profile.nama"></h1>
                            <p class="text-xs text-slate-300 leading-relaxed font-light">
                                Halaman Utama Pengawasan Wisma DPR RI. Lakukan pemantauan okupansi kamar delegasi, jadwal pemakaian ruang rapat komisi, dan saring laporan pertanggungjawaban anggaran DIPA.
                            </p>
                            <div class="mt-6 flex gap-3">
                                <button @click="switchTab('sekjen_reports')" class="px-5 py-2.5 bg-cyan-500 hover:bg-cyan-600 text-slate-950 font-bold text-xs rounded-xl shadow-lg shadow-cyan-500/20 transition-all flex items-center gap-1.5">
                                    <i data-lucide="file-bar-chart" class="w-4 h-4"></i> Buat Laporan DIPA
                                </button>
                                <button @click="switchTab('sekjen_rooms')" class="px-5 py-2.5 bg-white/10 hover:bg-white/20 text-white border border-white/10 font-semibold text-xs rounded-xl transition-all flex items-center gap-1.5">
                                    <i data-lucide="home" class="w-4 h-4"></i> Monitor Okupansi
                                </button>
                            </div>
                        </div>
                        <div class="absolute right-10 bottom-0 top-0 hidden lg:flex items-center text-white/5 pointer-events-none select-none">
                            <i data-lucide="landmark" class="w-64 h-64"></i>
                        </div>
                    </div>

                    <!-- Executive Stats Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-5 gap-6">
                        <!-- Stat 1: Occupancy -->
                        <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex flex-col justify-between group hover:shadow-md transition-shadow">
                            <div class="flex items-center justify-between">
                                <span class="text-xs text-slate-500 font-medium">Tingkat Okupansi</span>
                                <div class="w-9 h-9 rounded-lg bg-cyan-50 text-cyan-600 flex items-center justify-center">
                                    <i data-lucide="home" class="w-5 h-5"></i>
                                </div>
                            </div>
                            <div class="mt-4">
                                <h3 class="text-2xl font-bold font-outfit text-slate-900" x-text="getOccupancyRate() + '%'">78%</h3>
                                <div class="w-full bg-slate-100 h-1.5 rounded-full mt-2 overflow-hidden">
                                    <div class="bg-cyan-500 h-full rounded-full" :style="'width: ' + getOccupancyRate() + '%'"></div>
                                </div>
                            </div>
                        </div>
                        <!-- Stat 2: VIP Guests -->
                        <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex flex-col justify-between group hover:shadow-md transition-shadow">
                            <div class="flex items-center justify-between">
                                <span class="text-xs text-slate-500 font-medium">Delegasi VIP Menginap</span>
                                <div class="w-9 h-9 rounded-lg bg-amber-50 text-amber-500 flex items-center justify-center">
                                    <i data-lucide="crown" class="w-5 h-5"></i>
                                </div>
                            </div>
                            <div class="mt-4">
                                <h3 class="text-2xl font-bold font-outfit text-slate-900" x-text="bookings.filter(b => b.status === 'Check In' && b.vip).length + ' Tamu'">3 Tamu</h3>
                                <p class="text-[10px] text-slate-400 mt-2 font-medium">Aktif dalam pengawasan protokoler</p>
                            </div>
                        </div>
                        <!-- Stat 3: Active Rapat -->
                        <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex flex-col justify-between group hover:shadow-md transition-shadow">
                            <div class="flex items-center justify-between">
                                <span class="text-xs text-slate-500 font-medium">Kegiatan Rapat Hari Ini</span>
                                <div class="w-9 h-9 rounded-lg bg-blue-50 text-blue-500 flex items-center justify-center">
                                    <i data-lucide="calendar" class="w-5 h-5"></i>
                                </div>
                            </div>
                            <div class="mt-4">
                                <h3 class="text-2xl font-bold font-outfit text-slate-900" x-text="bookings.filter(b => b.type === 'Ruang Rapat' && b.status === 'Check In').length + ' Rapat'">2 Rapat</h3>
                                <p class="text-[10px] text-slate-400 mt-2 font-medium">Sedang berlangsung di Gedung Utama</p>
                            </div>
                        </div>
                        <!-- Stat 4: Complaints unresolved -->
                        <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex flex-col justify-between group hover:shadow-md transition-shadow">
                            <div class="flex items-center justify-between">
                                <span class="text-xs text-slate-500 font-medium">Keluhan Belum Selesai</span>
                                <div class="w-9 h-9 rounded-lg bg-red-50 text-red-500 flex items-center justify-center">
                                    <i data-lucide="alert-triangle" class="w-5 h-5"></i>
                                </div>
                            </div>
                            <div class="mt-4">
                                <h3 class="text-2xl font-bold font-outfit text-red-600" x-text="complaints.filter(c => c.status !== 'Resolved').length + ' Tiket'">2 Tiket</h3>
                                <p class="text-[10px] text-slate-400 mt-2 font-medium">Butuh tindak lanjut unit sarpras</p>
                            </div>
                        </div>
                        <!-- Stat 5: Budget progress -->
                        <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex flex-col justify-between group hover:shadow-md transition-shadow">
                            <div class="flex items-center justify-between">
                                <span class="text-xs text-slate-500 font-medium">Realisasi DIPA Wisma</span>
                                <div class="w-9 h-9 rounded-lg bg-emerald-50 text-emerald-500 flex items-center justify-center">
                                    <i data-lucide="wallet" class="w-5 h-5"></i>
                                </div>
                            </div>
                            <div class="mt-4">
                                <h3 class="text-sm font-bold font-outfit text-slate-900" x-text="formatRupiah(bookings.reduce((sum, b) => b.status !== 'Batal' ? sum + b.total_price : sum, 0))">Rp 26.200.000</h3>
                                <p class="text-[10px] text-emerald-600 mt-2 font-bold flex items-center gap-1">
                                    <i data-lucide="trending-up" class="w-3 h-3"></i> Kuartal II Berjalan
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Analytics Charts Area -->
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                        <!-- Chart 1: Occupancy trends (SVG curve) -->
                        <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm lg:col-span-2 space-y-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h3 class="text-sm font-extrabold text-slate-900 font-outfit">Tren Okupansi Kamar & Ruangan</h3>
                                    <p class="text-[10px] text-slate-400">Tingkat okupansi harian dalam 7 hari terakhir</p>
                                </div>
                                <span class="text-[10px] text-cyan-600 bg-cyan-50 px-2 py-1 rounded font-bold">Rata-rata: 74%</span>
                            </div>
                            
                            <!-- Beautiful SVG Line Chart Mock -->
                            <div class="pt-6 relative">
                                <svg class="w-full h-48" viewBox="0 0 700 200" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <!-- Grid lines -->
                                    <line x1="0" y1="20" x2="700" y2="20" stroke="#F1F5F9" stroke-width="1.5" />
                                    <line x1="0" y1="70" x2="700" y2="70" stroke="#F1F5F9" stroke-width="1.5" />
                                    <line x1="0" y1="120" x2="700" y2="120" stroke="#F1F5F9" stroke-width="1.5" />
                                    <line x1="0" y1="170" x2="700" y2="170" stroke="#F1F5F9" stroke-width="1.5" stroke-dasharray="4 4" />

                                    <!-- Chart Area Gradient Fill -->
                                    <path d="M 20 170 C 80 130, 120 150, 180 80 C 240 70, 300 120, 380 60 C 460 50, 520 110, 580 40 C 640 30, 680 50, 680 170 Z" fill="url(#chartGrad)" />

                                    <!-- Chart Line Curve -->
                                    <path d="M 20 170 C 80 130, 120 150, 180 80 C 240 70, 300 120, 380 60 C 460 50, 520 110, 580 40 C 640 30, 680 50, 680 50" stroke="#06B6D4" stroke-width="3" stroke-linecap="round" />

                                    <!-- Hover tooltips dots -->
                                    <circle cx="180" cy="80" r="5" fill="#0B1A30" stroke="#06B6D4" stroke-width="2" />
                                    <circle cx="380" cy="60" r="5" fill="#0B1A30" stroke="#06B6D4" stroke-width="2" />
                                    <circle cx="580" cy="40" r="5" fill="#0B1A30" stroke="#06B6D4" stroke-width="2" />

                                    <defs>
                                        <linearGradient id="chartGrad" x1="350" y1="20" x2="350" y2="170" gradientUnits="userSpaceOnUse">
                                            <stop stop-color="#06B6D4" stop-opacity="0.25" />
                                            <stop offset="1" stop-color="#06B6D4" stop-opacity="0.0" />
                                        </linearGradient>
                                    </defs>
                                </svg>
                                
                                <!-- Chart Labels -->
                                <div class="flex justify-between text-[10px] text-slate-400 font-bold px-2 pt-2">
                                    <span>Senin (55%)</span>
                                    <span>Selasa (68%)</span>
                                    <span>Rabu (80%)</span>
                                    <span>Kamis (72%)</span>
                                    <span>Jumat (84%)</span>
                                    <span>Sabtu (90%)</span>
                                    <span>Minggu (88%)</span>
                                </div>
                            </div>
                        </div>

                        <!-- Chart 2: Complaint Categories & Status -->
                        <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm space-y-4">
                            <h3 class="text-sm font-extrabold text-slate-900 font-outfit">Status Tindak Lanjut Keluhan</h3>
                            <p class="text-[10px] text-slate-400">Distribusi keluhan tamu masuk minggu ini</p>
                            
                            <div class="flex flex-col items-center justify-center py-4 relative">
                                <!-- Simple SVG Pie/Donut Chart -->
                                <svg class="w-32 h-32 transform -rotate-90" viewBox="0 0 32 32">
                                    <!-- Resolved: 60% (circumference = 100) -->
                                    <circle cx="16" cy="16" r="14" fill="transparent" stroke="#10B981" stroke-width="4" stroke-dasharray="60 100" />
                                    <!-- Processed: 25% (offset = 60) -->
                                    <circle cx="16" cy="16" r="14" fill="transparent" stroke="#3B82F6" stroke-width="4" stroke-dasharray="25 100" stroke-dashoffset="-60" />
                                    <!-- Pending: 15% (offset = 85) -->
                                    <circle cx="16" cy="16" r="14" fill="transparent" stroke="#EF4444" stroke-width="4" stroke-dasharray="15 100" stroke-dashoffset="-85" />
                                </svg>

                                <div class="absolute inset-0 flex flex-col items-center justify-center font-outfit mt-4">
                                    <span class="text-xs text-slate-400 font-medium">Total Masuk</span>
                                    <span class="text-lg font-bold text-slate-900" x-text="complaints.length">3</span>
                                </div>
                            </div>

                            <div class="space-y-2 text-xs pt-2">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                                        <span class="text-slate-500">Selesai (Resolved)</span>
                                    </div>
                                    <span class="font-bold text-slate-800" x-text="complaints.filter(c => c.status === 'Resolved').length">1</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <span class="w-2.5 h-2.5 rounded-full bg-blue-500"></span>
                                        <span class="text-slate-500">Diproses (Processed)</span>
                                    </div>
                                    <span class="font-bold text-slate-800" x-text="complaints.filter(c => c.status === 'Processed').length">1</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <span class="w-2.5 h-2.5 rounded-full bg-red-500"></span>
                                        <span class="text-slate-500">Menunggu (Pending)</span>
                                    </div>
                                    <span class="font-bold text-slate-800" x-text="complaints.filter(c => c.status === 'Pending').length">1</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- VIP Tamu Live Logs -->
                    <div class="bg-white rounded-3xl border border-slate-100 shadow-sm p-6 space-y-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-sm font-extrabold text-slate-900 font-outfit">Log Delegasi Tamu VIP & Pejabat Aktif</h3>
                                <p class="text-xs text-slate-400">Daftar tamu penting DPR RI yang sedang menginap di Wisma hari ini.</p>
                            </div>
                            <button @click="switchTab('sekjen_rooms')" class="text-xs text-cyan-600 font-bold hover:underline flex items-center gap-1">
                                Kelola Seluruh Kamar <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
                            </button>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr class="bg-slate-50 border-b border-slate-100 text-[10px] text-slate-400 font-bold uppercase tracking-wider">
                                        <th class="py-3.5 px-6">Nama Delegasi VIP</th>
                                        <th class="py-3.5 px-6">NIP / Jabatan</th>
                                        <th class="py-3.5 px-6">Unit Kamar</th>
                                        <th class="py-3.5 px-6">Lokasi Wing</th>
                                        <th class="py-3.5 px-6">Masa Inap (Durasi)</th>
                                        <th class="py-3.5 px-6">Protokoler Status</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 text-xs text-slate-700">
                                    <template x-for="b in bookings.filter(b => b.status === 'Check In' && b.vip)" :key="b.id">
                                        <tr class="hover:bg-slate-50/50 transition-colors">
                                            <td class="py-4 px-6 font-bold text-slate-900" x-text="b.nama"></td>
                                            <td class="py-4 px-6 text-slate-500" x-text="b.nip"></td>
                                            <td class="py-4 px-6 font-semibold" x-text="b.unit_name"></td>
                                            <td class="py-4 px-6" x-text="b.unit_location || 'Wing VVIP'"></td>
                                            <td class="py-4 px-6 font-medium" x-text="formatIndoDate(b.check_in) + ' - ' + formatIndoDate(b.check_out)"></td>
                                            <td class="py-4 px-6">
                                                <span class="px-2 py-0.5 bg-amber-100 text-amber-800 rounded-full font-bold text-[8px] uppercase tracking-wider flex items-center gap-1 w-max">
                                                    <i data-lucide="shield-check" class="w-2.5 h-2.5"></i> Protokol VIP
                                                </span>
                                            </td>
                                        </tr>
                                    </template>
                                    <tr x-show="bookings.filter(b => b.status === 'Check In' && b.vip).length === 0">
                                        <td colspan="6" class="text-center py-8 text-slate-400">
                                            <i data-lucide="inbox" class="w-8 h-8 mx-auto mb-2 text-slate-200"></i>
                                            Tidak ada tamu VIP yang sedang menginap hari ini.
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- 2. REPORTS AND ANALYTICS TAB (Report Builder) -->
                <div x-show="currentTab === 'sekjen_reports'" class="space-y-6 fade-in" x-cloak>
                    <div>
                        <h1 class="text-2xl font-outfit font-extrabold text-slate-950">Laporan Eksekutif & Analitik Wisma</h1>
                        <p class="text-xs text-slate-500">Pilih tipe laporan, saring berdasarkan parameter periode, lalu unduh dokumen resmi berformat Kop Surat Sekretariat Jenderal.</p>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6 items-start">
                        <!-- Report Filter Configuration Panel -->
                        <div class="bg-white border border-slate-100 p-6 rounded-3xl shadow-sm space-y-4">
                            <h3 class="text-sm font-extrabold text-slate-950 font-outfit border-b border-slate-100 pb-2 flex items-center gap-2">
                                <i data-lucide="sliders" class="w-4 h-4 text-cyan-600"></i> Saring Laporan
                            </h3>

                            <form class="space-y-4 text-xs">
                                <div class="space-y-1">
                                    <label class="text-[10px] text-slate-400 font-bold block uppercase tracking-wide">Tipe Laporan</label>
                                    <select x-model="reportConfig.type" class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 focus:ring-1 focus:ring-cyan-500 focus:outline-none transition-all">
                                        <option value="okupansi">Laporan Okupansi Kamar</option>
                                        <option value="kegiatan">Laporan Kegiatan & Ruang Rapat</option>
                                        <option value="keluhan">Laporan Penanganan Keluhan Tamu</option>
                                        <option value="dipa">Laporan Pemanfaatan Anggaran DIPA</option>
                                    </select>
                                </div>

                                <div class="space-y-1">
                                    <label class="text-[10px] text-slate-400 font-bold block uppercase tracking-wide">Periode Laporan</label>
                                    <select x-model="reportConfig.period" class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 focus:ring-1 focus:ring-cyan-500 focus:outline-none transition-all">
                                        <option value="bulan_ini">Bulan Juni 2026 (Berjalan)</option>
                                        <option value="bulan_lalu">Bulan Mei 2026</option>
                                        <option value="tahun_ini">Tahun Anggaran 2026</option>
                                    </select>
                                </div>

                                <div class="space-y-1">
                                    <label class="text-[10px] text-slate-400 font-bold block uppercase tracking-wide">Lokasi Wisma</label>
                                    <select x-model="reportConfig.wisma" class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 focus:ring-1 focus:ring-cyan-500 focus:outline-none transition-all">
                                        <option value="semua">Semua Lokasi Wisma</option>
                                        <option value="kopo">Wisma Kopo DPR RI</option>
                                        <option value="griya_sabha">Wisma Griya Sabha DPR RI</option>
                                        <option value="senayan">Wisma DPR RI Senayan</option>
                                    </select>
                                </div>

                                <div class="pt-4 border-t border-slate-100 flex flex-col gap-2.5">
                                    <button type="button" @click="generateReport()" class="w-full py-2.5 bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs rounded-xl transition-colors flex items-center justify-center gap-1.5 shadow">
                                        <i data-lucide="refresh-cw" class="w-4 h-4"></i> Muat Ulang Preview
                                    </button>
                                    <button type="button" @click="triggerDownload('pdf')" class="w-full py-2.5 bg-cyan-600 hover:bg-cyan-700 text-white font-bold text-xs rounded-xl transition-all flex items-center justify-center gap-1.5 shadow shadow-cyan-600/15">
                                        <i data-lucide="file-text" class="w-4 h-4"></i> Unduh Laporan PDF
                                    </button>
                                    <button type="button" @click="triggerDownload('excel')" class="w-full py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl transition-colors flex items-center justify-center gap-1.5 shadow shadow-emerald-600/15">
                                        <i data-lucide="file-spreadsheet" class="w-4 h-4"></i> Unduh Format Excel
                                    </button>
                                    <button type="button" @click="window.print()" class="w-full py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-xl transition-colors flex items-center justify-center gap-1.5">
                                        <i data-lucide="printer" class="w-4 h-4"></i> Cetak Laporan (Print)
                                    </button>
                                </div>
                            </form>
                        </div>

                        <!-- Report Live Paper Document Preview -->
                        <div class="lg:col-span-3 bg-white border border-slate-200 rounded-3xl shadow-lg p-10 max-w-4xl mx-auto w-full select-text relative" id="printable-report">
                            <!-- Kop Surat Republik Indonesia (Official Government Header) -->
                            <div class="border-b-4 border-double border-slate-950 pb-4 text-center space-y-1 relative">
                                <div class="absolute left-2 top-0 w-16 h-16 flex items-center justify-center bg-slate-50 rounded-lg text-slate-400 border border-slate-200 text-[10px] select-none font-bold">
                                    GARUDA MOCK
                                </div>
                                <h2 class="text-sm font-extrabold font-outfit text-slate-950 tracking-wider">DEWAN PERWAKILAN RAKYAT REPUBLIK INDONESIA</h2>
                                <h1 class="text-base font-extrabold font-outfit text-slate-950 tracking-wider uppercase">SEKRETARIAT JENDERAL</h1>
                                <p class="text-[10px] text-slate-500 font-medium">Jalan Jenderal Gatot Subroto, Jakarta 10270 • Telepon (021) 5715349</p>
                                <p class="text-[10px] text-cyan-600 hover:underline">www.dpr.go.id • setjen@dpr.go.id</p>
                            </div>

                            <!-- Document Content -->
                            <div class="pt-8 space-y-6 text-slate-800 font-sans text-xs leading-relaxed">
                                <div class="text-center space-y-1">
                                    <h3 class="font-extrabold text-slate-950 underline uppercase tracking-wide text-sm" x-text="getReportTitle()">LAPORAN TINGKAT OKUPANSI KAMAR WISMA</h3>
                                    <p class="text-[10px] text-slate-500 font-bold uppercase tracking-widest" x-text="'Nomor: Setjen/Wisma/' + reportConfig.period.toUpperCase() + '/2026'"></p>
                                </div>

                                <div class="space-y-2">
                                    <p><strong>I. Ringkasan Eksekutif</strong></p>
                                    <p x-text="getReportSummary()"></p>
                                </div>

                                <div class="space-y-3">
                                    <p><strong>II. Rekapitulasi Data Terkait</strong></p>
                                    
                                    <!-- Dinamis Table based on Report Type -->
                                    <div class="border border-slate-300 rounded-lg overflow-hidden">
                                        
                                        <!-- TABLE 1: OKUPANSI -->
                                        <table x-show="reportConfig.type === 'okupansi'" class="w-full text-left border-collapse">
                                            <thead>
                                                <tr class="bg-slate-50 border-b border-slate-300 text-[10px] font-bold text-slate-700">
                                                    <th class="py-2.5 px-4 border-r border-slate-300">Nama Unit Fasilitas</th>
                                                    <th class="py-2.5 px-4 border-r border-slate-300">Gedung / Wing</th>
                                                    <th class="py-2.5 px-4 border-r border-slate-300">Kapasitas</th>
                                                    <th class="py-2.5 px-4 border-r border-slate-300">Tarif / Malam</th>
                                                    <th class="py-2.5 px-4">Status Saat Ini</th>
                                                </tr>
                                            </thead>
                                            <tbody class="divide-y divide-slate-200">
                                                <template x-for="f in facilities" :key="f.id">
                                                    <tr class="hover:bg-slate-50">
                                                        <td class="py-2 px-4 border-r border-slate-300 font-semibold" x-text="f.name"></td>
                                                        <td class="py-2 px-4 border-r border-slate-300" x-text="f.gedung"></td>
                                                        <td class="py-2 px-4 border-r border-slate-300" x-text="f.capacity + ' Orang'"></td>
                                                        <td class="py-2 px-4 border-r border-slate-300 font-bold" x-text="formatRupiah(f.price)"></td>
                                                        <td class="py-2 px-4">
                                                            <span class="px-2 py-0.5 rounded text-[8px] font-bold"
                                                                  :class="{ 'bg-emerald-100 text-emerald-700': f.status === 'READY', 'bg-amber-100 text-amber-700': f.status === 'CLEANING', 'bg-red-100 text-red-700': f.status === 'OCCUPIED' || f.status === 'MAINTENANCE' }"
                                                                  x-text="f.status"></span>
                                                        </td>
                                                    </tr>
                                                </template>
                                            </tbody>
                                        </table>

                                        <!-- TABLE 2: KEGIATAN & RAPAT -->
                                        <table x-show="reportConfig.type === 'kegiatan'" class="w-full text-left border-collapse" x-cloak>
                                            <thead>
                                                <tr class="bg-slate-50 border-b border-slate-300 text-[10px] font-bold text-slate-700">
                                                    <th class="py-2.5 px-4 border-r border-slate-300">Nama Kegiatan Komisi</th>
                                                    <th class="py-2.5 px-4 border-r border-slate-300">Fasilitas Ruang</th>
                                                    <th class="py-2.5 px-4 border-r border-slate-300">Penanggung Jawab NIP</th>
                                                    <th class="py-2.5 px-4 border-r border-slate-300">Masa Rapat</th>
                                                    <th class="py-2.5 px-4">Status</th>
                                                </tr>
                                            </thead>
                                            <tbody class="divide-y divide-slate-200">
                                                <template x-for="b in bookings.filter(b => b.type === 'Ruang Rapat')" :key="b.id">
                                                    <tr class="hover:bg-slate-50">
                                                        <td class="py-2 px-4 border-r border-slate-300 font-bold" x-text="b.event_name || 'Rapat Anggota Dewan'"></td>
                                                        <td class="py-2 px-4 border-r border-slate-300" x-text="b.unit_name"></td>
                                                        <td class="py-2 px-4 border-r border-slate-300" x-text="b.nama + ' (' + b.nip + ')'"></td>
                                                        <td class="py-2 px-4 border-r border-slate-300" x-text="formatIndoDate(b.check_in)"></td>
                                                        <td class="py-2 px-4">
                                                            <span class="px-2 py-0.5 rounded text-[8px] font-bold bg-blue-100 text-blue-700" x-text="b.status"></span>
                                                        </td>
                                                    </tr>
                                                </template>
                                                <tr x-show="bookings.filter(b => b.type === 'Ruang Rapat').length === 0">
                                                    <td colspan="5" class="text-center py-6 text-slate-400">Tidak ada log rapat tercatat untuk periode ini.</td>
                                                </tr>
                                            </tbody>
                                        </table>

                                        <!-- TABLE 3: KELUHAN -->
                                        <table x-show="reportConfig.type === 'keluhan'" class="w-full text-left border-collapse" x-cloak>
                                            <thead>
                                                <tr class="bg-slate-50 border-b border-slate-300 text-[10px] font-bold text-slate-700">
                                                    <th class="py-2.5 px-4 border-r border-slate-300">Deskripsi Kendala</th>
                                                    <th class="py-2.5 px-4 border-r border-slate-300">Kategori</th>
                                                    <th class="py-2.5 px-4 border-r border-slate-300">Lokasi</th>
                                                    <th class="py-2.5 px-4 border-r border-slate-300">Pelapor (Tamu)</th>
                                                    <th class="py-2.5 px-4">Tindak Lanjut</th>
                                                </tr>
                                            </thead>
                                            <tbody class="divide-y divide-slate-200">
                                                <template x-for="c in complaints" :key="c.id">
                                                    <tr class="hover:bg-slate-50">
                                                        <td class="py-2 px-4 border-r border-slate-300 font-semibold" x-text="c.title"></td>
                                                        <td class="py-2 px-4 border-r border-slate-300" x-text="c.category"></td>
                                                        <td class="py-2 px-4 border-r border-slate-300" x-text="c.location"></td>
                                                        <td class="py-2 px-4 border-r border-slate-300" x-text="c.guestName"></td>
                                                        <td class="py-2 px-4">
                                                            <span class="px-2 py-0.5 rounded text-[8px] font-bold"
                                                                  :class="{ 'bg-red-100 text-red-700': c.status === 'Pending', 'bg-blue-100 text-blue-700': c.status === 'Processed', 'bg-emerald-100 text-emerald-700': c.status === 'Resolved' }"
                                                                  x-text="c.status"></span>
                                                        </td>
                                                    </tr>
                                                </template>
                                            </tbody>
                                        </table>

                                        <!-- TABLE 4: KEUANGAN / DIPA -->
                                        <table x-show="reportConfig.type === 'dipa'" class="w-full text-left border-collapse" x-cloak>
                                            <thead>
                                                <tr class="bg-slate-50 border-b border-slate-300 text-[10px] font-bold text-slate-700">
                                                    <th class="py-2.5 px-4 border-r border-slate-300">No. Booking DIPA</th>
                                                    <th class="py-2.5 px-4 border-r border-slate-300">Nama Pejabat NIP</th>
                                                    <th class="py-2.5 px-4 border-r border-slate-300">Fasilitas</th>
                                                    <th class="py-2.5 px-4 border-r border-slate-300">Durasi (Malam)</th>
                                                    <th class="py-2.5 px-4">Jumlah Dana DIPA</th>
                                                </tr>
                                            </thead>
                                            <tbody class="divide-y divide-slate-200">
                                                <template x-for="b in bookings" :key="b.id">
                                                    <tr class="hover:bg-slate-50">
                                                        <td class="py-2 px-4 border-r border-slate-300 font-bold" x-text="b.id"></td>
                                                        <td class="py-2 px-4 border-r border-slate-300" x-text="b.nama + ' (' + b.nip + ')'"></td>
                                                        <td class="py-2 px-4 border-r border-slate-300 truncate max-w-[120px]" x-text="b.unit_name"></td>
                                                        <td class="py-2 px-4 border-r border-slate-300" x-text="b.nights || 1"></td>
                                                        <td class="py-2 px-4 font-extrabold text-slate-900 text-right" x-text="formatRupiah(b.total_price)"></td>
                                                    </tr>
                                                </template>
                                                <!-- Total Row -->
                                                <tr class="bg-slate-50 font-bold">
                                                    <td colspan="4" class="py-2 px-4 text-right border-r border-slate-300">Total Pemanfaatan Anggaran DIPA:</td>
                                                    <td class="py-2 px-4 text-right font-extrabold text-slate-950" x-text="formatRupiah(bookings.reduce((sum, b) => b.status !== 'Batal' ? sum + b.total_price : sum, 0))"></td>
                                                </tr>
                                            </tbody>
                                        </table>

                                    </div>
                                </div>

                                <!-- Signature Section -->
                                <div class="pt-12 flex justify-between select-none">
                                    <div></div>
                                    <div class="text-center space-y-12">
                                        <p class="font-medium">Mengetahui,<br><strong>Sekretaris Jenderal DPR RI</strong></p>
                                        <div>
                                            <p class="font-extrabold text-slate-950">Drs. Indra Wijaya, M.Si</p>
                                            <p class="text-[10px] text-slate-500 border-t border-slate-300 pt-0.5">NIP. 19680312 199303 1 002</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                <!-- 3. OKUPANSI KAMAR MONITORING TAB -->
                <div x-show="currentTab === 'sekjen_rooms'" class="space-y-6 fade-in" x-cloak>
                    <div>
                        <h1 class="text-2xl font-outfit font-extrabold text-slate-950">Monitoring Okupansi Kamar & Wing</h1>
                        <p class="text-xs text-slate-500">Pantau status unit kamar wisma secara terperinci per lantai dan wing bangunan.</p>
                    </div>

                    <!-- Room Grid Filter Options -->
                    <div class="bg-white p-4 border border-slate-100 rounded-3xl shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
                        <div class="relative w-80 text-xs">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                                <i data-lucide="search" class="w-4 h-4"></i>
                            </span>
                            <input type="text" x-model="roomSearch" placeholder="Cari nama kamar atau wing..." class="w-full pl-9 pr-4 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-1 focus:ring-cyan-500 transition-all">
                        </div>

                        <div class="flex gap-2 text-xs">
                            <button @click="roomStatusFilter = 'semua'" :class="roomStatusFilter === 'semua' ? 'bg-cyan-600 text-white font-bold' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'" class="px-4 py-2 rounded-xl font-bold transition-all">Semua Kamar</button>
                            <button @click="roomStatusFilter = 'OCCUPIED'" :class="roomStatusFilter === 'OCCUPIED' ? 'bg-cyan-600 text-white font-bold' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'" class="px-4 py-2 rounded-xl font-bold transition-all">Terisi (Occupied)</button>
                            <button @click="roomStatusFilter = 'READY'" :class="roomStatusFilter === 'READY' ? 'bg-cyan-600 text-white font-bold' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'" class="px-4 py-2 rounded-xl font-bold transition-all">Tersedia (Ready)</button>
                            <button @click="roomStatusFilter = 'CLEANING'" :class="roomStatusFilter === 'CLEANING' ? 'bg-cyan-600 text-white font-bold' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'" class="px-4 py-2 rounded-xl font-bold transition-all">Pembersihan</button>
                        </div>
                    </div>

                    <!-- Rooms Cards Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                        <template x-for="f in filteredFacilities()" :key="f.id">
                            <div class="bg-white border border-slate-100 rounded-3xl shadow-sm overflow-hidden flex flex-col justify-between group hover:shadow-md transition-shadow">
                                <div class="relative overflow-hidden h-40 bg-slate-100 shrink-0">
                                    <img :src="f.photo" class="w-full h-full object-cover">
                                    <div class="absolute top-3 left-3">
                                        <span class="px-2.5 py-0.5 rounded text-[8px] font-extrabold uppercase tracking-wider text-white shadow"
                                              :class="{
                                                  'bg-red-500/90': f.status === 'OCCUPIED',
                                                  'bg-emerald-500/90': f.status === 'READY',
                                                  'bg-amber-500/90': f.status === 'CLEANING',
                                                  'bg-slate-500/90': f.status === 'MAINTENANCE'
                                              }"
                                              x-text="f.status"></span>
                                    </div>
                                </div>
                                <div class="p-5 flex-1 flex flex-col justify-between space-y-4 font-sans">
                                    <div>
                                        <span class="text-[9px] text-slate-400 font-bold uppercase tracking-wider block" x-text="f.gedung + ' • ' + f.lantai"></span>
                                        <h4 class="text-sm font-bold text-slate-900 mt-1 font-outfit" x-text="f.name"></h4>
                                        <div class="flex items-center gap-1.5 text-xs text-slate-500 mt-2">
                                            <i data-lucide="users" class="w-3.5 h-3.5 text-slate-400"></i>
                                            <span x-text="'Kapasitas: ' + f.capacity + ' Orang'"></span>
                                        </div>
                                    </div>
                                    <div class="pt-3 border-t border-slate-50 flex items-center justify-between">
                                        <div>
                                            <span class="text-[8px] text-slate-400 block uppercase font-bold tracking-wider">Tarif DIPA</span>
                                            <span class="text-xs font-bold text-slate-900" x-text="formatRupiah(f.price)"></span>
                                        </div>
                                        <div class="text-right">
                                            <span class="text-[8px] text-slate-400 block uppercase font-bold tracking-wider">Tipe</span>
                                            <span class="text-xs font-semibold text-slate-600" x-text="f.type"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- 4. SCHEDULES & MEETINGS MONITORING TAB -->
                <div x-show="currentTab === 'sekjen_meetings'" class="space-y-6 fade-in" x-cloak>
                    <div>
                        <h1 class="text-2xl font-outfit font-extrabold text-slate-950">Monitoring Ruang Rapat & Kegiatan Komisi</h1>
                        <p class="text-xs text-slate-500">Monitor seluruh log pemesanan dan jadwal Rapat Dengar Pendapat (RDP) atau Rapat Kerja Anggota DPR RI.</p>
                    </div>

                    <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-slate-50 border-b border-slate-100 text-[10px] text-slate-400 font-bold uppercase tracking-wider">
                                    <th class="py-4 px-6">Nama Kegiatan Komisi / Dewan</th>
                                    <th class="py-4 px-6">Tempat Ruangan</th>
                                    <th class="py-4 px-6">NIP Penanggung Jawab</th>
                                    <th class="py-4 px-6">Durasi Kegiatan</th>
                                    <th class="py-4 px-6">Status Acara</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-xs text-slate-700">
                                <template x-for="b in bookings.filter(b => b.type === 'Ruang Rapat')" :key="b.id">
                                    <tr class="hover:bg-slate-50/50 transition-colors">
                                        <td class="py-4 px-6">
                                            <h4 class="font-bold text-slate-900" x-text="b.event_name || 'Rapat Komisi DPR RI'"></h4>
                                            <p class="text-[9px] text-slate-400 mt-0.5" x-text="'No. Reservasi: ' + b.id"></p>
                                        </td>
                                        <td class="py-4 px-6">
                                            <p class="font-semibold text-slate-800" x-text="b.unit_name"></p>
                                            <p class="text-[9px] text-slate-400 mt-0.5" x-text="b.unit_location || 'Gedung Utama'"></p>
                                        </td>
                                        <td class="py-4 px-6">
                                            <p class="font-medium text-slate-800" x-text="b.nama"></p>
                                            <p class="text-[9px] text-slate-400 mt-0.5" x-text="'NIP: ' + b.nip"></p>
                                        </td>
                                        <td class="py-4 px-6 font-semibold" x-text="formatIndoDate(b.check_in)"></td>
                                        <td class="py-4 px-6">
                                            <span class="px-2.5 py-1 bg-blue-100 text-blue-800 rounded-full font-bold text-[8px] uppercase tracking-wider flex items-center gap-1 w-max">
                                                <span class="w-1.5 h-1.5 rounded-full bg-blue-600 animate-ping"></span> Berlangsung
                                            </span>
                                        </td>
                                    </tr>
                                </template>
                                <tr x-show="bookings.filter(b => b.type === 'Ruang Rapat').length === 0">
                                    <td colspan="5" class="text-center py-12 text-slate-400">
                                        <i data-lucide="calendar" class="w-12 h-12 mx-auto mb-2 text-slate-200"></i>
                                        Tidak ada jadwal kegiatan rapat yang tercatat hari ini.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- 5. COMPLAINTS & SERVICES TAB -->
                <div x-show="currentTab === 'sekjen_complaints'" class="space-y-6 fade-in" x-cloak>
                    <div>
                        <h1 class="text-2xl font-outfit font-extrabold text-slate-950">Monitoring Keluhan & Penanganan Layanan</h1>
                        <p class="text-xs text-slate-500">Pantau indeks respon keluhan tamu masuk dan pastikan kualitas operasional wisma tetap terjaga.</p>
                    </div>

                    <!-- Statistics Complains Overview Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex items-center justify-between">
                            <div>
                                <span class="text-xs text-slate-500 font-medium">Tiket Menunggu Respon</span>
                                <h3 class="text-2xl font-bold font-outfit text-red-500 mt-1" x-text="complaints.filter(c => c.status === 'Pending').length + ' Keluhan'"></h3>
                            </div>
                            <div class="w-12 h-12 bg-red-50 text-red-500 rounded-xl flex items-center justify-center">
                                <i data-lucide="alert-circle" class="w-6 h-6"></i>
                            </div>
                        </div>

                        <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex items-center justify-between">
                            <div>
                                <span class="text-xs text-slate-500 font-medium">Sedang Diproses Sarpas</span>
                                <h3 class="text-2xl font-bold font-outfit text-blue-600 mt-1" x-text="complaints.filter(c => c.status === 'Processed').length + ' Tiket'"></h3>
                            </div>
                            <div class="w-12 h-12 bg-blue-50 text-blue-500 rounded-xl flex items-center justify-center">
                                <i data-lucide="wrench" class="w-6 h-6"></i>
                            </div>
                        </div>

                        <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex items-center justify-between">
                            <div>
                                <span class="text-xs text-slate-500 font-medium">Telah Selesai Diperbaiki</span>
                                <h3 class="text-2xl font-bold font-outfit text-emerald-600 mt-1" x-text="complaints.filter(c => c.status === 'Resolved').length + ' Selesai'"></h3>
                            </div>
                            <div class="w-12 h-12 bg-emerald-50 text-emerald-500 rounded-xl flex items-center justify-center">
                                <i data-lucide="check-circle" class="w-6 h-6"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Complaints List Table -->
                    <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-slate-50 border-b border-slate-100 text-[10px] text-slate-400 font-bold uppercase tracking-wider">
                                    <th class="py-4 px-6">Tiket ID / Kategori</th>
                                    <th class="py-4 px-6">Deskripsi Permasalahan</th>
                                    <th class="py-4 px-6">Lokasi Area</th>
                                    <th class="py-4 px-6">Tamu Pelapor</th>
                                    <th class="py-4 px-6">Status Tindak Lanjut</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-xs text-slate-700">
                                <template x-for="c in complaints" :key="c.id">
                                    <tr class="hover:bg-slate-50/50 transition-colors">
                                        <td class="py-4 px-6">
                                            <p class="font-bold text-slate-900" x-text="c.id"></p>
                                            <p class="text-[9px] text-slate-400 uppercase font-bold mt-0.5" x-text="c.category"></p>
                                        </td>
                                        <td class="py-4 px-6">
                                            <h4 class="font-bold text-slate-900" x-text="c.title"></h4>
                                            <p class="text-[10px] text-slate-500 mt-1 leading-relaxed" x-text="c.description"></p>
                                            <p class="text-[8px] text-slate-400 mt-1" x-text="'Dilaporkan: ' + c.date"></p>
                                        </td>
                                        <td class="py-4 px-6 font-semibold text-slate-800" x-text="c.location"></td>
                                        <td class="py-4 px-6 font-medium text-slate-800" x-text="c.guestName"></td>
                                        <td class="py-4 px-6">
                                            <span class="px-2.5 py-1 rounded-full font-bold text-[8px] uppercase tracking-wider"
                                                  :class="{
                                                      'bg-red-100 text-red-700': c.status === 'Pending',
                                                      'bg-blue-100 text-blue-700': c.status === 'Processed',
                                                      'bg-emerald-100 text-emerald-700': c.status === 'Resolved'
                                                  }"
                                                  x-text="c.status === 'Pending' ? 'Menunggu' : (c.status === 'Processed' ? 'Diproses' : 'Selesai')"></span>
                                        </td>
                                    </tr>
                                </template>
                                <tr x-show="complaints.length === 0">
                                    <td colspan="5" class="text-center py-12 text-slate-400">
                                        <i data-lucide="smile" class="w-12 h-12 mx-auto mb-2 text-slate-200"></i>
                                        Sangat Baik! Tidak ada keluhan yang dilaporkan oleh tamu wisma.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

            </main>
        </div>
    </div>

    <!-- APP SCRIPT STATE MANAGEMENT -->
    <script>
        function wismaSekjenApp() {
            return {
                isLoggedIn: false,
                passwordVisible: false,
                loginForm: {
                    username: 'sekjen',
                    password: 'sekjen'
                },

                currentTab: 'sekjen_dashboard',
                roomSearch: '',
                roomStatusFilter: 'semua',
                downloading: false,
                downloadProgress: 0,
                downloadStatusTitle: '',
                downloadStatusDesc: '',

                toasts: [],
                toastCount: 0,

                profile: {
                    role: 'sekjen',
                    nama: 'Drs. Indra Wijaya, M.Si',
                    role_label: 'Sekretaris Jenderal',
                    instansi: 'Sekretariat Jenderal DPR RI'
                },

                reportConfig: {
                    type: 'okupansi',
                    period: 'bulan_ini',
                    wisma: 'semua'
                },

                // Shared LocalStorage data keys
                facilities: [],
                bookings: [],
                guests: [],
                complaints: [],

                initApp() {
                    this.loadAndSyncState();
                    setTimeout(() => {
                        if (window.lucide) {
                            window.lucide.createIcons();
                        }
                    }, 100);
                },

                login() {
                    if (this.loginForm.username !== 'sekjen' || this.loginForm.password !== 'sekjen') {
                        this.addToast('Login Gagal', 'Username atau Password Sekjen salah.', 'error');
                        return;
                    }
                    
                    this.isLoggedIn = true;
                    this.profile.role = 'sekjen';
                    this.profile.nama = 'Drs. Indra Wijaya, M.Si';
                    this.profile.role_label = 'Sekretaris Jenderal';
                    this.currentTab = 'sekjen_dashboard';
                    
                    this.addToast('Login Berhasil', `Selamat datang di Executive Portal Sekjen, ${this.profile.nama}.`, 'success');
                    
                    setTimeout(() => {
                        if (window.lucide) {
                            window.lucide.createIcons();
                        }
                    }, 50);
                },

                logout() {
                    this.isLoggedIn = false;
                    this.loginForm.username = 'sekjen';
                    this.loginForm.password = 'sekjen';
                    this.addToast('Logout Sukses', 'Anda telah keluar dari sesi Sekjen.', 'info');
                    
                    setTimeout(() => {
                        if (window.lucide) {
                            window.lucide.createIcons();
                        }
                    }, 50);
                },

                // Loading and synchronizing database state
                loadAndSyncState() {
                    const savedFacilities = localStorage.getItem('wisma_facilities');
                    const savedBookings = localStorage.getItem('wisma_bookings');
                    const savedGuests = localStorage.getItem('wisma_guests');
                    const savedComplaints = localStorage.getItem('wisma_complaints');

                    // If local storage is completely empty, initialize default datasets so it shows rich content!
                    if (!savedFacilities || JSON.parse(savedFacilities).length === 0) {
                        const defaultFacilities = [
                            { id: 1, name: 'Deluxe Room 101', type: 'Kamar', gedung: 'Wing A', lantai: 'Lantai 1', capacity: 2, price: 1200000, unit: 'night', luas: '32 m²', bed: 'King Size Bed', status: 'READY', photo: 'https://images.unsplash.com/photo-1566665797739-1674de7a421a?auto=format&fit=crop&w=600&q=80', description: 'Kamar premium yang didesain khusus untuk delegasi anggota dewan dengan kamar mandi dalam, akses WiFi cepat, Smart TV, dan pembuat kopi mini.' },
                            { id: 2, name: 'Deluxe Room 102', type: 'Kamar', gedung: 'Wing A', lantai: 'Lantai 1', capacity: 2, price: 1200000, unit: 'night', luas: '32 m²', bed: 'King Size Bed', status: 'OCCUPIED', photo: 'https://images.unsplash.com/photo-1590490360182-c33d57733427?auto=format&fit=crop&w=600&q=80', description: 'Kamar representatif dengan pendingin udara sentral, sofa lounge kecil, lemari kabinet pakaian besar, dan meja kerja eksklusif.' },
                            { id: 3, name: 'Executive Room 201', type: 'Kamar', gedung: 'Wing B', lantai: 'Lantai 2', capacity: 2, price: 1800000, unit: 'night', luas: '45 m²', bed: 'Super King Bed', status: 'READY', photo: 'https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?auto=format&fit=crop&w=600&q=80', description: 'Kamar VIP di Wing B yang luas dilengkapi mini bar kulkas, perlengkapan mandi merk premium, ruang makan kecil terpisah, dan pemandangan luar taman.' },
                            { id: 4, name: 'Executive Suite VVIP', type: 'Kamar', gedung: 'Wing VVIP', lantai: 'Lantai 3', capacity: 4, price: 3500000, unit: 'night', luas: '80 m²', bed: 'Double Super King', status: 'OCCUPIED', photo: 'https://images.unsplash.com/photo-1631049307264-da0ec9d70304?auto=format&fit=crop&w=600&q=80', description: 'Suite mewah eksklusif VVIP dengan keamanan ekstra. Memiliki ruang tamu privat untuk rapat kecil, dapur kecil terintegrasi, dan layanan room service 24 jam.' },
                            { id: 5, name: 'Ruang Rapat Nusantara', type: 'Ruang Rapat', gedung: 'Gedung Utama', lantai: 'Lantai 1', capacity: 30, price: 800000, unit: '4 jam', luas: '60 m²', bed: 'Conference Layout', status: 'READY', photo: 'https://images.unsplash.com/photo-1517502884422-41eaaced0168?auto=format&fit=crop&w=600&q=80', description: 'Ruang rapat formal berkapasitas 30 orang dilengkapi projector resolusi tinggi, sound system digital, microfon terpisah di meja, dan papan tulis whiteboard kaca.' },
                            { id: 6, name: 'Ruang Rapat Paripurna Kecil', type: 'Ruang Rapat', gedung: 'Gedung Utama', lantai: 'Lantai 2', capacity: 50, price: 1500000, unit: '4 jam', luas: '120 m²', bed: 'Theater Layout', status: 'OCCUPIED', photo: 'https://images.unsplash.com/photo-1431540015161-0bf868a2d407?auto=format&fit=crop&w=600&q=80', description: 'Ruang aula rapat komisi berukuran besar dengan tata letak teater. Memiliki meja podium pimpinan sidang, ruang asisten, dan sistem pencahayaan pintar.' },
                            { id: 7, name: 'Room 301 Superior', type: 'Kamar', gedung: 'Wing B', lantai: 'Lantai 3', capacity: 2, price: 900000, unit: 'night', luas: '30 m²', bed: 'Twin Single Bed', status: 'CLEANING', photo: 'https://images.unsplash.com/photo-1505691938895-1758d7feb511?auto=format&fit=crop&w=600&q=80', description: 'Kamar tipe superior dengan tempat tidur kembar (Twin Bed) sangat cocok untuk ajudan delegasi resmi atau protokoler.' },
                            { id: 8, name: 'Room 302 Superior', type: 'Kamar', gedung: 'Wing B', lantai: 'Lantai 3', capacity: 2, price: 900000, unit: 'night', luas: '30 m²', bed: 'Twin Single Bed', status: 'MAINTENANCE', photo: 'https://images.unsplash.com/photo-1522771739844-6a9f6d5f14af?auto=format&fit=crop&w=600&q=80', description: 'Unit superior dalam perawatan sarpas berkala untuk perbaikan instalasi pipa air kamar mandi.' }
                        ];
                        this.facilities = defaultFacilities;
                        localStorage.setItem('wisma_facilities', JSON.stringify(defaultFacilities));
                    } else {
                        this.facilities = JSON.parse(savedFacilities);
                    }

                    if (!savedBookings || JSON.parse(savedBookings).length === 0) {
                        const defaultBookings = [
                            { id: 'BK-8821', nama: 'Bpk. H. Bambang Soesatyo, SE, MBA', nip: '19620910202', unit_name: 'Executive Suite VVIP', unit_location: 'Wing VVIP, Lantai 3', check_in: '2026-06-18', check_out: '2026-06-22', status: 'Check In', total_price: 14000000, nights: 4, type: 'Kamar', vip: true, unit_photo: 'https://images.unsplash.com/photo-1631049307264-da0ec9d70304?auto=format&fit=crop&w=600&q=80' },
                            { id: 'BK-8822', nama: 'Ibu Puan Maharani', NIP: '19730906202', nip: '19730906202', unit_name: 'Deluxe Room 102', unit_location: 'Wing A, Lantai 1', check_in: '2026-06-19', check_out: '2026-06-21', status: 'Check In', total_price: 2400000, nights: 2, type: 'Kamar', vip: true, unit_photo: 'https://images.unsplash.com/photo-1590490360182-c33d57733427?auto=format&fit=crop&w=600&q=80' },
                            { id: 'BK-8823', nama: 'Bpk. Sufmi Dasco Ahmad', nip: '19671007202', unit_name: 'Executive Room 201', unit_location: 'Wing B, Lantai 2', check_in: '2026-06-20', check_out: '2026-06-24', status: 'Lunas', total_price: 7200000, nights: 4, type: 'Kamar', vip: true, unit_photo: 'https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?auto=format&fit=crop&w=600&q=80' },
                            { id: 'BK-8824', nama: 'Rapat Anggota Komisi III', nip: 'KOMISI-III-DPR', unit_name: 'Ruang Rapat Nusantara', unit_location: 'Gedung Utama, Lantai 1', check_in: '2026-06-19', check_out: '2026-06-19', status: 'Check In', total_price: 800000, nights: 1, type: 'Ruang Rapat', event_name: 'Rapat Dengar Pendapat Hukum Komisi III', vip: false, unit_photo: 'https://images.unsplash.com/photo-1517502884422-41eaaced0168?auto=format&fit=crop&w=600&q=80' },
                            { id: 'BK-8825', nama: 'Badan Anggaran DPR RI', nip: 'BANGGAR-DPR', unit_name: 'Ruang Rapat Paripurna Kecil', unit_location: 'Gedung Utama, Lantai 2', check_in: '2026-06-19', check_out: '2026-06-19', status: 'Check In', total_price: 1500000, nights: 1, type: 'Ruang Rapat', event_name: 'Penyusunan RUU APBN 2027', vip: false, unit_photo: 'https://images.unsplash.com/photo-1431540015161-0bf868a2d407?auto=format&fit=crop&w=600&q=80' }
                        ];
                        this.bookings = defaultBookings;
                        localStorage.setItem('wisma_bookings', JSON.stringify(defaultBookings));
                    } else {
                        this.bookings = JSON.parse(savedBookings);
                    }

                    if (!savedGuests || JSON.parse(savedGuests).length === 0) {
                        const defaultGuests = [
                            { id: 'GST-001', nama: 'Bpk. H. Bambang Soesatyo, SE, MBA', nip: '19620910202', email: 'bamsoet@dpr.go.id', phone: '081122334455', status: 'Member', terakhir: 'Check-in Kamar VVIP' },
                            { id: 'GST-002', nama: 'Ibu Puan Maharani', nip: '19730906202', email: 'puan.maharani@dpr.go.id', phone: '081234567890', status: 'Member', terakhir: 'Check-in Kamar 102' },
                            { id: 'GST-003', nama: 'Bpk. Sufmi Dasco Ahmad', nip: '19671007202', email: 'sufmi.dasco@dpr.go.id', phone: '081345678901', status: 'Member', terakhir: 'Lunas Booking Kamar 201' }
                        ];
                        this.guests = defaultGuests;
                        localStorage.setItem('wisma_guests', JSON.stringify(defaultGuests));
                    } else {
                        this.guests = JSON.parse(savedGuests);
                    }

                    if (!savedComplaints || JSON.parse(savedComplaints).length === 0) {
                        const defaultComplaints = [
                            { id: 'KP-101', title: 'Kebocoran AC Kamar', category: 'Fasilitas', location: 'Deluxe Room 102', guestName: 'Ibu Puan Maharani', description: 'Instalasi pendingin udara mengeluarkan tetesan air sehingga merembes ke karpet kamar tidur.', status: 'Processed', date: '2026-06-19 10:30' },
                            { id: 'KP-102', title: 'WiFi Putus di Area Rapat', category: 'Internet / Wifi', location: 'Ruang Rapat Nusantara', guestName: 'Rapat Anggota Komisi III', description: 'Kecepatan koneksi internet wifi terputus dan drop berkala menyulitkan jalannya rapat paripurna online.', status: 'Pending', date: '2026-06-19 14:15' },
                            { id: 'KP-103', title: 'Lampu Utama Toilet Padam', category: 'Fasilitas', location: 'Room 302 Superior', guestName: 'Petugas Protokol', description: 'Lampu penerangan kamar mandi mati total, butuh penggantian bohlam secepatnya.', status: 'Resolved', date: '2026-06-18 09:00' }
                        ];
                        this.complaints = defaultComplaints;
                        localStorage.setItem('wisma_complaints', JSON.stringify(defaultComplaints));
                    } else {
                        this.complaints = JSON.parse(savedComplaints);
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

                getOccupancyRate() {
                    const occupied = this.facilities.filter(f => f.status === 'OCCUPIED').length;
                    const total = this.facilities.length || 1;
                    return Math.round((occupied / total) * 100);
                },

                filteredFacilities() {
                    return this.facilities.filter(f => {
                        const matchesSearch = f.name.toLowerCase().includes(this.roomSearch.toLowerCase()) || 
                                              f.gedung.toLowerCase().includes(this.roomSearch.toLowerCase());
                        if (this.roomStatusFilter === 'semua') return matchesSearch;
                        return matchesSearch && f.status === this.roomStatusFilter;
                    });
                },

                getReportTitle() {
                    switch (this.reportConfig.type) {
                        case 'okupansi': return 'LAPORAN REKAPITULASI HUNIAN & OKUPANSI KAMAR';
                        case 'kegiatan': return 'LAPORAN REKAPITULASI KEGIATAN & RUANG RAPAT DEWAN';
                        case 'keluhan': return 'LAPORAN PENANGANAN KELUHAN & LAYANAN TAMU';
                        case 'dipa': return 'LAPORAN AUDIT ANGGARAN & REALISASI DIPA WISMA';
                        default: return 'LAPORAN OPERASIONAL WISMA';
                    }
                },

                getReportSummary() {
                    const monthText = this.reportConfig.period === 'bulan_ini' ? 'Juni 2026' : (this.reportConfig.period === 'bulan_lalu' ? 'Mei 2026' : 'Tahun Anggaran 2026');
                    const locationText = this.reportConfig.wisma === 'semua' ? 'seluruh unit wisma' : `Wisma ${this.reportConfig.wisma.replace('_', ' ').toUpperCase()}`;

                    switch (this.reportConfig.type) {
                        case 'okupansi':
                            return `Berdasarkan hasil pemantauan tingkat hunian kamar pada periode ${monthText} di ${locationText}, tercatat rata-rata tingkat hunian (occupancy rate) mencapai ${this.getOccupancyRate()}%. Tipe Kamar Executive dan VVIP Suite menjadi kamar paling diminati dengan total durasi sewa kumulatif tertinggi guna menyambut kunjungan kerja dinas luar kota.`;
                        case 'kegiatan':
                            return `Laporan pertanggungjawaban kegiatan ruang rapat komisi selama periode ${monthText} di ${locationText} mencatat sebanyak ${this.bookings.filter(b => b.type === 'Ruang Rapat').length} agenda rapat dengar pendapat (RDP), rapat kerja badan anggaran, serta seminar telah terselenggara dengan lancar dengan kepatuhan kapasitas ruangan 100%.`;
                        case 'keluhan':
                            const totalC = this.complaints.length;
                            const resolvedC = this.complaints.filter(c => c.status === 'Resolved').length;
                            return `Data rekap penanganan keluhan fasilitas wisma pada periode ${monthText} di ${locationText} mencatat total ${totalC} pengaduan masuk dari tamu. Biro Umum Sarpas telah berhasil menyelesaikan ${resolvedC} laporan permasalahan sarana prasarana dengan rata-rata waktu penyelesaian di bawah 3 jam.`;
                        case 'dipa':
                            const totalRevenue = this.bookings.reduce((sum, b) => b.status !== 'Batal' ? sum + b.total_price : sum, 0);
                            return `Laporan keuangan realisasi penyerapan anggaran DIPA pengelolaan wisma DPR RI untuk periode ${monthText} di ${locationText} mencatat pemanfaatan dana kumulatif sebesar ${this.formatRupiah(totalRevenue)}. Seluruh log transaksi terdokumentasi terintegrasi dengan e-Budgeting keuangan negara secara akurat.`;
                        default:
                            return 'Laporan operasional internal Wisma DPR RI.';
                    }
                },

                generateReport() {
                    this.addToast('Data Dimuat', 'Laporan preview berhasil diperbarui berdasarkan filter.', 'info');
                },

                triggerDownload(format) {
                    this.downloadProgress = 0;
                    this.downloading = true;
                    this.downloadStatusTitle = 'Menyiapkan Data Laporan';
                    this.downloadStatusDesc = 'Mengumpulkan log rekapitulasi data dari LocalStorage...';

                    let interval = setInterval(() => {
                        this.downloadProgress += 15;
                        if (this.downloadProgress === 30) {
                            this.downloadStatusTitle = 'Menyusun Struktur Dokumen';
                            this.downloadStatusDesc = 'Membuat tata letak lembar resmi Kop Surat Sekretariat Jenderal...';
                        }
                        if (this.downloadProgress === 60) {
                            this.downloadStatusTitle = 'Menandatangani Berkas Digital';
                            this.downloadStatusDesc = 'Melakukan enkripsi QR-Code otorisasi Sekretaris Jenderal...';
                        }
                        if (this.downloadProgress === 90) {
                            this.downloadStatusTitle = 'Hampir Selesai';
                            this.downloadStatusDesc = 'Mengonversi berkas dokumen laporan ke format ' + format.toUpperCase() + '...';
                        }
                        if (this.downloadProgress >= 100) {
                            this.downloadProgress = 100;
                            clearInterval(interval);
                            setTimeout(() => {
                                this.downloading = false;
                                this.addToast('Unduhan Berhasil', `Berkas laporan_${this.reportConfig.type}_${format}.${format} telah tersimpan di folder downloads.`, 'success');
                                this.saveReportAsFile(format);
                            }, 500);
                        }
                    }, 350);
                },

                saveReportAsFile(format) {
                    // Create simulated download file download
                    const title = this.getReportTitle();
                    const summary = this.getReportSummary();
                    const fileContent = `========================================================\n` +
                                        `DEWAN PERWAKILAN RAKYAT REPUBLIK INDONESIA\n` +
                                        `SEKRETARIAT JENDERAL\n` +
                                        `========================================================\n` +
                                        `DOKUMEN RESMI NEGARA: ${title}\n` +
                                        `Periode: ${this.reportConfig.period.toUpperCase()} 2026\n` +
                                        `Wisma: ${this.reportConfig.wisma.toUpperCase()}\n` +
                                        `Format File: ${format.toUpperCase()}\n` +
                                        `--------------------------------------------------------\n\n` +
                                        `RINGKASAN EKSEKUTIF:\n${summary}\n\n` +
                                        `Dikeluarkan oleh: Sekretariat Jenderal Wisma DPR RI\n` +
                                        `Mengetahui: Drs. Indra Wijaya, M.Si\n` +
                                        `Tanggal Unduh: ${new Date().toLocaleDateString('id-ID')}\n` +
                                        `Status: SAH & TERSERTIFIKASI ELEKTRONIK\n` +
                                        `========================================================\n`;

                    const blob = new Blob([fileContent], { type: 'text/plain;charset=utf-8' });
                    const link = document.createElement('a');
                    link.href = URL.createObjectURL(blob);
                    link.download = `laporan_${this.reportConfig.type}_${this.reportConfig.period}_2026.${format === 'pdf' ? 'pdf' : 'xlsx'}`;
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);
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

                formatRupiah(amount) {
                    if (amount === undefined || amount === null) return 'Rp 0';
                    return 'Rp ' + amount.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
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

                getFormattedTime() {
                    const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
                    return new Date().toLocaleDateString('id-ID', options);
                }
            };
        }
    </script>
</body>
</html>
