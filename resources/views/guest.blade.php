<!DOCTYPE html>
<html lang="id" x-data="wismaApp()" x-init="initApp()">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Portal Tamu Wisma DPR RI - Reservasi & Keluhan</title>

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

    <!-- Midtrans Snap Sandbox CDN -->
    <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="SB-Mid-client-wisma-dpr-123"></script>

    <style>
        [x-cloak] { display: none !important; }
        .scrollbar-hide::-webkit-scrollbar { display: none; }
        .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
        .ticket-clip-left { clip-path: circle(12px at 0% 50%); }
        .ticket-clip-right { clip-path: circle(12px at 100% 50%); }
        
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
                        <i data-lucide="shield" class="w-3 h-3"></i> Portal Tamu Wisma
                    </span>
                    <h1 class="text-4xl font-outfit font-extrabold text-white tracking-tight leading-tight max-w-lg">
                        Pelayanan Menginap yang Nyaman & Terintegrasi
                    </h1>
                    <p class="text-xs text-slate-300 leading-relaxed max-w-md font-light">
                        Portal khusus tamu Wisma DPR RI. Akses katalog fasilitas, kelola pemesanan, dan sampaikan keluhan Anda secara instan kepada tim front office kami.
                    </p>
                </div>

                <div class="grid grid-cols-3 gap-6 pt-4 border-t border-white/10 max-w-lg text-white">
                    <div>
                        <h4 class="text-2xl font-bold font-outfit text-wisma-gold">450+</h4>
                        <span class="text-[10px] text-slate-400 block mt-0.5">Kamar Tersedia</span>
                    </div>
                    <div>
                        <h4 class="text-2xl font-bold font-outfit text-wisma-gold">24/7</h4>
                        <span class="text-[10px] text-slate-400 block mt-0.5">Layanan Front Office</span>
                    </div>
                    <div>
                        <h4 class="text-2xl font-bold font-outfit text-wisma-gold">Respon</h4>
                        <span class="text-[10px] text-slate-400 block mt-0.5">Keluhan Cepat</span>
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
            <!-- Header Logo & Back to Home -->
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

            <!-- Login Form block -->
            <div class="max-w-md w-full mx-auto space-y-8">
                <div class="space-y-2 text-center md:text-left">
                    <h2 class="text-2xl font-extrabold text-slate-900 font-outfit tracking-tight">Login Portal Tamu</h2>
                    <p class="text-xs text-slate-500">Silakan masukkan DPR ID Anda untuk melakukan reservasi.</p>
                </div>

                <form @submit.prevent="login()" class="space-y-5">
                    <!-- Username / DPR ID -->
                    <div class="space-y-1">
                        <label class="text-[10px] text-slate-500 font-bold uppercase tracking-wide block">DPR ID</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                <i data-lucide="contact" class="w-4.5 h-4.5"></i>
                            </span>
                            <input type="text" 
                                   x-model="loginForm.dprId"
                                   placeholder="Contoh: 1989041220" 
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

                    <!-- Remember me / Forgot pwd -->
                    <div class="flex items-center justify-between text-xs pt-1">
                        <label class="flex items-center gap-2 text-slate-600 cursor-pointer">
                            <input type="checkbox" class="accent-wisma-gold rounded border-slate-300">
                            <span>Ingat saya</span>
                        </label>
                        <a href="#" @click.prevent="showFeatureMuted('Lupa Password')" class="text-indigo-600 hover:underline">Lupa password?</a>
                    </div>

                    <!-- Button submit -->
                    <button type="submit" class="w-full py-3 bg-slate-950 hover:bg-slate-900 text-white font-bold text-xs rounded-xl shadow-lg transition-colors flex items-center justify-center gap-2">
                        Masuk Portal <i data-lucide="arrow-right" class="w-4 h-4"></i>
                    </button>
                </form>

                <div class="text-center pt-2">
                    <p class="text-xs text-slate-500">
                        Belum memiliki akses? <a href="#" @click.prevent="showFeatureMuted('Hubungi Admin')" class="text-indigo-600 hover:underline">Hubungi Administrator</a>
                    </p>
                </div>
            </div>

            <!-- Footer copyright -->
            <div class="flex items-center justify-between text-[10px] text-slate-400 pt-8 border-t border-slate-100 w-full">
                <span>© 2026 Sekretariat Jenderal DPR RI. Semua Hak Dilindungi.</span>
                <div class="flex gap-4">
                    <a href="#" @click.prevent="showFeatureMuted('Kebijakan Privasi')" class="hover:underline">Kebijakan Privasi</a>
                    <a href="#" @click.prevent="showFeatureMuted('Syarat Ketentuan')" class="hover:underline">Syarat & Ketentuan</a>
                </div>
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
                    <i data-lucide="hotel" class="w-6 h-6 text-wisma-dark"></i>
                </div>
                <div>
                    <h2 class="font-outfit font-bold text-base tracking-wider leading-none">Wisma DPR RI</h2>
                    <span class="text-[10px] text-wisma-textMuted font-medium uppercase tracking-widest">Portal Tamu</span>
                </div>
            </div>

            <!-- Sidebar Navigation Menu -->
            <nav class="flex-1 px-4 py-6 space-y-1.5 overflow-y-auto scrollbar-hide">
                <p class="text-[10px] text-slate-500 font-semibold px-3 mb-2 uppercase tracking-widest">Utama</p>
                
                <button @click="switchTab('dashboard')"
                        class="w-full flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 group text-left"
                        :class="currentTab === 'dashboard' ? 'bg-gradient-to-r from-wisma-gold to-amber-500 text-wisma-dark font-semibold shadow-lg shadow-wisma-gold/15' : 'text-wisma-textMuted hover:bg-slate-800/50 hover:text-white'">
                    <i data-lucide="layout-dashboard" class="w-5 h-5 transition-transform group-hover:scale-110"></i>
                    <span class="text-sm">Dashboard</span>
                </button>

                <button @click="switchTab('facilities')"
                        class="w-full flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 group text-left"
                        :class="currentTab === 'facilities' || currentTab === 'booking_wizard' ? 'bg-gradient-to-r from-wisma-gold to-amber-500 text-wisma-dark font-semibold shadow-lg shadow-wisma-gold/15' : 'text-wisma-textMuted hover:bg-slate-800/50 hover:text-white'">
                    <i data-lucide="search" class="w-5 h-5 transition-transform group-hover:scale-110"></i>
                    <span class="text-sm">Cari & Booking</span>
                    <span class="ml-auto px-2 py-0.5 bg-wisma-dark/25 rounded-md text-[10px]" x-text="facilities.filter(f => f.status === 'READY').length + ' Tersedia'"></span>
                </button>

                <button @click="switchTab('history')"
                        class="w-full flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 group text-left"
                        :class="currentTab === 'history' ? 'bg-gradient-to-r from-wisma-gold to-amber-500 text-wisma-dark font-semibold shadow-lg shadow-wisma-gold/15' : 'text-wisma-textMuted hover:bg-slate-800/50 hover:text-white'">
                    <i data-lucide="history" class="w-5 h-5 transition-transform group-hover:scale-110"></i>
                    <span class="text-sm">Riwayat Booking</span>
                    <span class="ml-auto w-2 h-2 bg-emerald-500" x-show="bookings.some(b => b.status === 'Lunas' || b.status === 'Check In')"></span>
                </button>

                <p class="text-[10px] text-slate-500 font-semibold px-3 pt-6 mb-2 uppercase tracking-widest">Layanan Mandiri</p>

                <button @click="switchTab('profile')"
                        class="w-full flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 group text-left"
                        :class="currentTab === 'profile' ? 'bg-gradient-to-r from-wisma-gold to-amber-500 text-wisma-dark font-semibold shadow-lg shadow-wisma-gold/15' : 'text-wisma-textMuted hover:bg-slate-800/50 hover:text-white'">
                    <i data-lucide="user" class="w-5 h-5 transition-transform group-hover:scale-110"></i>
                    <span class="text-sm">Profil Saya</span>
                </button>

                <button @click="switchTab('help')"
                        class="w-full flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 group text-left"
                        :class="currentTab === 'help' ? 'bg-gradient-to-r from-wisma-gold to-amber-500 text-wisma-dark font-semibold shadow-lg shadow-wisma-gold/15' : 'text-wisma-textMuted hover:bg-slate-800/50 hover:text-white'">
                    <i data-lucide="help-circle" class="w-5 h-5 transition-transform group-hover:scale-110"></i>
                    <span class="text-sm">Keluhan & Bantuan</span>
                </button>
            </nav>

            <!-- Sidebar Footer/User Profile Summary -->
            <div class="p-4 border-t border-slate-800 bg-wisma-dark/40 flex items-center gap-3">
                <img class="w-10 h-10 rounded-full border border-wisma-gold/30 object-cover" 
                     src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&w=100&h=100&q=80" 
                     alt="User Avatar">
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
                <div class="relative w-96">
                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <i data-lucide="search" class="w-4.5 h-4.5"></i>
                    </span>
                    <input type="text" 
                           x-model="searchQuery" 
                           @input="if(currentTab !== 'facilities') currentTab = 'facilities'"
                           placeholder="Cari fasilitas kamar atau ruang rapat..." 
                           class="w-full pl-10 pr-4 py-2 text-sm bg-slate-100 border-none rounded-xl focus:bg-white focus:ring-2 focus:ring-wisma-gold/30 focus:outline-none transition-all">
                </div>

                <div class="flex items-center gap-4">
                    <div class="relative" @click.away="showNotifications = false">
                        <button @click="showNotifications = !showNotifications; if(showNotifications && unreadNotificationCount > 0) markNotificationsRead()" class="w-10 h-10 rounded-xl bg-slate-50 border border-slate-100 flex items-center justify-center text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition-colors relative">
                            <i data-lucide="bell" class="w-5 h-5"></i>
                            <span x-show="unreadNotificationCount > 0" class="absolute top-2.5 right-2.5 w-2 h-2 bg-red-500 rounded-full border border-white"></span>
                        </button>
                        
                        <!-- Dropdown Notifikasi -->
                        <div x-show="showNotifications" 
                             class="absolute right-0 mt-2 w-80 bg-white rounded-2xl shadow-xl border border-slate-100 z-50 overflow-hidden fade-in"
                             x-cloak>
                            <div class="px-4 py-3 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                                <h3 class="font-bold text-sm text-slate-900">Notifikasi</h3>
                                <span class="text-[10px] px-2 py-0.5 bg-slate-200 text-slate-600 rounded-full font-semibold" x-text="notifications.length + ' Pesan'"></span>
                            </div>
                            <div class="max-h-80 overflow-y-auto">
                                <template x-if="notifications.length === 0">
                                    <div class="px-4 py-6 text-center">
                                        <i data-lucide="bell-off" class="w-8 h-8 mx-auto text-slate-300 mb-2"></i>
                                        <p class="text-xs text-slate-500">Belum ada notifikasi terbaru.</p>
                                    </div>
                                </template>
                                <template x-for="notif in notifications" :key="notif.id">
                                    <div class="px-4 py-3 border-b border-slate-50 hover:bg-slate-50 transition-colors cursor-pointer group" :class="notif.is_read ? 'opacity-70' : 'bg-blue-50/30'">
                                        <p class="text-xs font-semibold text-slate-900 group-hover:text-wisma-navy transition-colors" x-text="notif.title"></p>
                                        <p class="text-[11px] text-slate-500 mt-0.5 leading-snug" x-text="notif.message"></p>
                                        <p class="text-[9px] text-slate-400 mt-1.5 font-medium flex items-center gap-1">
                                            <i data-lucide="clock" class="w-3 h-3"></i> 
                                            <span x-text="new Date(notif.created_at).toLocaleString('id-ID', {day: 'numeric', month: 'short', hour: '2-digit', minute: '2-digit'})"></span>
                                        </p>
                                    </div>
                                </template>
                            </div>
                            <div class="px-4 py-2 border-t border-slate-100 bg-slate-50 text-center">
                                <button class="text-xs text-indigo-600 font-semibold hover:underline">Lihat Semua Notifikasi</button>
                            </div>
                        </div>
                    </div>
                    
                    <div class="w-px h-6 bg-slate-200 mx-2"></div>
                    
                    <div class="flex items-center gap-3">
                        <div class="text-right">
                            <p class="text-xs font-semibold text-slate-800" x-text="profile.nama"></p>
                            <p class="text-[10px] text-slate-500" x-text="profile.instansi"></p>
                        </div>
                        <div class="w-10 h-10 rounded-xl bg-wisma-navy text-wisma-gold flex items-center justify-center font-bold text-sm border border-wisma-gold/20 shadow-sm">BS</div>
                    </div>
                </div>
            </header>

            <!-- SCROLLABLE PAGE CONTENT -->
            <main class="flex-1 overflow-y-auto p-8 relative">

                <!-- 1. DASHBOARD VIEW -->
                <div x-show="currentTab === 'dashboard'" class="space-y-8 fade-in">
                    <!-- Welcome Banner -->
                    <div class="relative overflow-hidden bg-gradient-to-r from-wisma-navy to-slate-900 text-white rounded-3xl p-8 shadow-xl">
                        <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_top_right,_var(--tw-gradient-stops))] from-amber-500/20 via-transparent to-transparent"></div>
                        <div class="relative z-10 max-w-xl">
                            <span class="px-3 py-1 bg-amber-500/20 text-wisma-gold text-[10px] uppercase font-bold tracking-widest rounded-full border border-wisma-gold/30">Portal Reservasi Tamu</span>
                            <h1 class="text-3xl font-outfit font-extrabold mt-4 mb-2 tracking-tight" x-text="'Selamat Datang, ' + profile.nama"></h1>
                            <p class="text-xs text-slate-300 leading-relaxed font-light">
                                Portal Layanan Wisma DPR RI. Lakukan pemesanan unit kamar penginapan atau ruang rapat secara dinamis. Pantau status check-in Anda hari ini.
                            </p>
                            <div class="mt-6 flex gap-3">
                                <button @click="switchTab('facilities')" class="px-5 py-2.5 bg-wisma-gold hover:bg-wisma-goldHover text-wisma-dark font-semibold text-xs rounded-xl shadow-lg shadow-wisma-gold/20 transition-all">
                                    Cari Fasilitas & Booking
                                </button>
                                <button @click="switchTab('history')" class="px-5 py-2.5 bg-white/10 hover:bg-white/20 text-white border border-white/10 font-semibold text-xs rounded-xl transition-all">
                                    Lihat Tiket Reservasi
                                </button>
                            </div>
                        </div>
                        <div class="absolute right-10 bottom-0 top-0 hidden lg:flex items-center text-white/5 pointer-events-none select-none">
                            <i data-lucide="hotel" class="w-64 h-64"></i>
                        </div>
                    </div>

                    <!-- Stats Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex items-center justify-between group hover:shadow-md transition-shadow">
                            <div>
                                <span class="text-xs text-slate-500 font-medium">Reservasi Aktif</span>
                                <h3 class="text-2xl font-bold font-outfit mt-1 text-slate-900" x-text="bookings.filter(b => b.status === 'Lunas' || b.status === 'Check In').length"></h3>
                            </div>
                            <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-500 flex items-center justify-center transition-transform group-hover:scale-110">
                                <i data-lucide="calendar-check" class="w-6 h-6"></i>
                            </div>
                        </div>
                        <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex items-center justify-between group hover:shadow-md transition-shadow">
                            <div>
                                <span class="text-xs text-slate-500 font-medium">Reservasi Selesai</span>
                                <h3 class="text-2xl font-bold font-outfit mt-1 text-slate-900" x-text="bookings.filter(b => b.status === 'Selesai').length"></h3>
                            </div>
                            <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-500 flex items-center justify-center transition-transform group-hover:scale-110">
                                <i data-lucide="check-circle" class="w-6 h-6"></i>
                            </div>
                        </div>
                        <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex items-center justify-between group hover:shadow-md transition-shadow">
                            <div>
                                <span class="text-xs text-slate-500 font-medium">Keluhan Anda Diajukan</span>
                                <h3 class="text-2xl font-bold font-outfit mt-1 text-slate-900" x-text="complaints.length"></h3>
                            </div>
                            <div class="w-12 h-12 rounded-xl bg-amber-50 text-wisma-gold flex items-center justify-center transition-transform group-hover:scale-110">
                                <i data-lucide="alert-triangle" class="w-6 h-6"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Active Bookings Summary -->
                    <div class="bg-white rounded-3xl border border-slate-100 shadow-sm p-6">
                        <div class="flex items-center justify-between mb-6">
                            <div>
                                <h2 class="text-base font-bold text-slate-900">Reservasi Aktif Anda</h2>
                                <p class="text-xs text-slate-500">Boarding pass digital Anda. Tunjukkan tiket ini kepada resepsionis saat check-in.</p>
                            </div>
                            <button @click="switchTab('history')" class="text-xs text-wisma-gold font-semibold hover:underline flex items-center gap-1">
                                Lihat Semua Riwayat <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
                            </button>
                        </div>

                        <div class="space-y-4">
                            <template x-for="b in bookings.filter(x => x.status === 'Lunas' || x.status === 'Check In')" :key="b.id">
                                <div class="flex flex-col md:flex-row items-start md:items-center justify-between p-4 bg-slate-50 hover:bg-slate-100/70 border border-slate-100 rounded-2xl transition-colors">
                                    <div class="flex items-center gap-4">
                                        <img :src="b.unit_photo" class="w-16 h-16 rounded-xl object-cover border border-slate-200">
                                        <div>
                                            <div class="flex items-center gap-2">
                                                <h4 class="text-sm font-bold text-slate-900" x-text="b.unit_name"></h4>
                                                <span class="px-2 py-0.5 text-[9px] uppercase font-bold rounded"
                                                      :class="b.status === 'Check In' ? 'bg-blue-100 text-blue-700' : 'bg-emerald-100 text-emerald-700'"
                                                      x-text="b.status === 'Check In' ? 'Aktif Menginap' : b.status"></span>
                                            </div>
                                            <p class="text-xs text-slate-500 mt-1 flex items-center gap-2">
                                                <i data-lucide="calendar" class="w-3.5 h-3.5"></i>
                                                <span x-text="formatIndoDate(b.check_in) + ' - ' + formatIndoDate(b.check_out)"></span>
                                                <span class="text-slate-300">|</span>
                                                <span x-text="b.nights + (b.unit_name.includes('Rapat') ? ' Hari' : ' Malam')"></span>
                                            </p>
                                        </div>
                                    </div>
                                    <div class="mt-4 md:mt-0 flex items-center gap-3">
                                        <div class="text-right">
                                            <span class="text-[10px] text-slate-500">Total Pembayaran</span>
                                            <p class="text-sm font-bold text-slate-900" x-text="formatRupiah(b.total_price)"></p>
                                        </div>
                                        <button @click="viewTicket(b)" class="px-4 py-2 bg-wisma-navy hover:bg-slate-800 text-white font-semibold text-xs rounded-xl shadow-md transition-colors flex items-center gap-1.5">
                                            <i data-lucide="ticket" class="w-3.5 h-3.5"></i> Lihat Tiket
                                        </button>
                                    </div>
                                </div>
                            </template>
                            <div x-show="bookings.filter(x => x.status === 'Lunas' || x.status === 'Check In').length === 0" class="text-center py-8 text-slate-400">
                                <i data-lucide="calendar-x" class="w-12 h-12 mx-auto mb-2 text-slate-300"></i>
                                <p class="text-xs">Tidak ada reservasi aktif saat ini.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 2. FACILITIES CATALOG VIEW -->
                <div x-show="currentTab === 'facilities'" class="space-y-6 fade-in" x-cloak>
                    <div>
                        <h1 class="text-2xl font-outfit font-extrabold text-slate-900">Katalog & Booking Bungalow Wisma</h1>
                        <p class="text-xs text-slate-500">Jelajahi dan pesan bungalow wisma yang tersedia.</p>
                    </div>

                    <!-- Filters Bar -->
                    <div class="bg-white p-4 rounded-2xl border border-slate-100 shadow-sm grid grid-cols-1 md:grid-cols-5 gap-4 items-center">
                        <div>
                            <label class="text-[10px] text-slate-500 font-bold block mb-1 uppercase tracking-wide">Gedung</label>
                            <select x-model="filterGedung" class="w-full text-xs bg-slate-50 border border-slate-200 rounded-lg p-2 focus:ring-1 focus:ring-wisma-gold focus:outline-none">
                                <option value="">Semua Gedung</option>
                                <option value="Wisma">Wisma</option>
                            </select>
                        </div>
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
                                    <img :src="f.photo" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110">
                                    <div class="absolute top-4 left-4 flex gap-1.5 flex-wrap">
                                        <span class="px-2 py-0.5 bg-slate-900/70 text-white backdrop-blur-md rounded text-[9px] uppercase font-bold tracking-wide" x-text="f.type"></span>
                                        <span class="px-2 py-0.5 rounded text-[9px] uppercase font-bold tracking-wide text-white"
                                              :class="{ 'bg-emerald-500/80': f.status === 'READY', 'bg-amber-500/80': f.status === 'CLEANING', 'bg-red-500/80': f.status === 'MAINTENANCE' || f.status === 'OCCUPIED' }"
                                              x-text="f.status"></span>
                                    </div>
                                </div>
                                <div class="p-6 flex-1 flex flex-col justify-between space-y-4">
                                    <div>
                                        <span class="text-[9px] text-slate-400 font-bold uppercase tracking-wider block" x-text="f.gedung + ' • ' + f.lantai"></span>
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
                <div x-show="currentTab === 'booking_wizard'" class="space-y-6 fade-in" x-cloak>
                    <!-- Stepper Progress Header -->
                    <div class="bg-white border border-slate-100 rounded-2xl p-6 shadow-sm">
                        <div class="flex items-center justify-between max-w-xl mx-auto text-xs">
                            <div class="flex flex-col items-center gap-1.5">
                                <span class="w-7 x-7 h-7 rounded-full flex items-center justify-center font-bold"
                                      :class="wizardStep >= 1 ? 'bg-wisma-navy text-wisma-gold' : 'bg-slate-100 text-slate-500'">1</span>
                                <span class="font-semibold" :class="wizardStep >= 1 ? 'text-slate-900' : 'text-slate-400'">Tanggal</span>
                            </div>
                            <div class="flex-1 h-0.5 bg-slate-200 mx-2" :class="wizardStep >= 2 ? 'bg-wisma-navy' : ''"></div>
                            <div class="flex flex-col items-center gap-1.5">
                                <span class="w-7 h-7 rounded-full flex items-center justify-center font-bold"
                                      :class="wizardStep >= 2 ? 'bg-wisma-navy text-wisma-gold' : 'bg-slate-100 text-slate-500'">2</span>
                                <span class="font-semibold" :class="wizardStep >= 2 ? 'text-slate-900' : 'text-slate-400'">Data Diri</span>
                            </div>
                            <div class="flex-1 h-0.5 bg-slate-200 mx-2" :class="wizardStep >= 3 ? 'bg-wisma-navy' : ''"></div>
                            <div class="flex flex-col items-center gap-1.5">
                                <span class="w-7 h-7 rounded-full flex items-center justify-center font-bold"
                                      :class="wizardStep >= 3 ? 'bg-wisma-navy text-wisma-gold' : 'bg-slate-100 text-slate-500'">3</span>
                                <span class="font-semibold" :class="wizardStep >= 3 ? 'text-slate-900' : 'text-slate-400'">Pembayaran</span>
                            </div>
                            <div class="flex-1 h-0.5 bg-slate-200 mx-2" :class="wizardStep >= 4 ? 'bg-wisma-navy' : ''"></div>
                            <div class="flex flex-col items-center gap-1.5">
                                <span class="w-7 h-7 rounded-full flex items-center justify-center font-bold"
                                      :class="wizardStep >= 4 ? 'bg-wisma-navy text-wisma-gold' : 'bg-slate-100 text-slate-500'">4</span>
                                <span class="font-semibold" :class="wizardStep >= 4 ? 'text-slate-900' : 'text-slate-400'">Selesai</span>
                            </div>
                        </div>
                    </div>

                    <!-- STEP 1: SELECT DATES -->
                    <div x-show="wizardStep === 1" class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                        <!-- Calendar Selector Left -->
                        <div class="bg-white border border-slate-100 rounded-3xl p-6 shadow-sm lg:col-span-2 space-y-4">
                            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                                <h3 class="text-sm font-bold text-slate-900">Pilih Tanggal Check-in & Check-out</h3>
                                <div class="flex items-center gap-1">
                                    <button @click="calendarPrevMonth()" class="p-1 hover:bg-slate-100 rounded-lg text-slate-600"><i data-lucide="chevron-left" class="w-4 h-4"></i></button>
                                    <span class="text-xs font-bold text-slate-800 font-outfit" x-text="calendarMonthLabel()"></span>
                                    <button @click="calendarNextMonth()" class="p-1 hover:bg-slate-100 rounded-lg text-slate-600"><i data-lucide="chevron-right" class="w-4 h-4"></i></button>
                                </div>
                            </div>
                            
                            <!-- Calendar Grid -->
                            <div class="grid grid-cols-7 gap-1 text-center text-[10px] font-bold text-slate-400 uppercase tracking-widest pb-2">
                                <div>Min</div><div>Sen</div><div>Sel</div><div>Rab</div><div>Kam</div><div>Jum</div><div>Sab</div>
                            </div>
                            <div class="grid grid-cols-7 gap-1.5 text-center text-xs">
                                <template x-for="blank in calendarBlanks">
                                    <div class="py-2.5"></div>
                                </template>
                                <template x-for="day in calendarDays" :key="day.dateStr">
                                    <button @click="if(!day.isPast && !day.isBooked) selectCalendarDate(day.dateStr)" 
                                            :disabled="day.isPast || day.isBooked"
                                            class="py-2.5 rounded-xl font-bold transition-all relative flex flex-col items-center justify-center"
                                            :class="{
                                                'text-slate-300 cursor-not-allowed': day.isPast,
                                                'text-red-400 bg-red-50 cursor-not-allowed line-through': day.isBooked,
                                                'bg-wisma-navy text-wisma-gold font-extrabold shadow-md': isDateSelected(day.dateStr) && !day.isBooked,
                                                'bg-indigo-50 text-indigo-700': isDateInRange(day.dateStr) && !day.isBooked,
                                                'hover:bg-slate-100 text-slate-700': !day.isPast && !day.isBooked && !isDateSelected(day.dateStr) && !isDateInRange(day.dateStr)
                                            }">
                                        <span x-text="day.dayNum"></span>
                                        <span x-show="day.dateStr === checkInDate" class="text-[7px] text-wisma-gold absolute bottom-0.5 uppercase tracking-tighter">IN</span>
                                        <span x-show="day.dateStr === checkOutDate" class="text-[7px] text-wisma-gold absolute bottom-0.5 uppercase tracking-tighter">OUT</span>
                                    </button>
                                </template>
                            </div>
                        </div>

                        <!-- Summary Column Right -->
                        <div class="bg-white border border-slate-100 rounded-3xl p-6 shadow-sm space-y-4">
                            <h3 class="text-sm font-bold text-slate-900">Rangkuman Booking</h3>
                            <div class="flex items-center gap-3 bg-slate-50 p-3 rounded-xl border border-slate-100">
                                <img :src="selectedFacility.photo" class="w-16 h-16 rounded-xl object-cover border border-slate-200 shrink-0">
                                <div>
                                    <h4 class="text-xs font-bold text-slate-900" x-text="selectedFacility.name"></h4>
                                    <p class="text-[10px] text-slate-400 mt-1" x-text="selectedFacility.gedung + ' • ' + selectedFacility.lantai"></p>
                                </div>
                            </div>
                            <div class="space-y-2.5 pt-2 text-xs">
                                <div class="flex justify-between">
                                    <span class="text-slate-500">Check-in</span>
                                    <span class="font-semibold text-slate-900" x-text="formatIndoDate(checkInDate)"></span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-slate-500">Check-out</span>
                                    <span class="font-semibold text-slate-900" x-text="formatIndoDate(checkOutDate)"></span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-slate-500">Durasi Booking</span>
                                    <span class="font-semibold text-slate-900" x-text="calculateNights() + (selectedFacility.unit === 'day' ? ' Hari' : ' Malam')"></span>
                                </div>
                            </div>
                            <button @click="proceedToStep(2)" class="w-full py-3 bg-slate-950 hover:bg-slate-900 text-white font-bold text-xs rounded-xl shadow-lg transition-colors mt-4">
                                Lanjutkan Data Diri
                            </button>
                        </div>
                    </div>

                    <!-- STEP 2: GUEST DATA ENTRY -->
                    <div x-show="wizardStep === 2" class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                        <div class="bg-white border border-slate-100 rounded-3xl p-6 shadow-sm lg:col-span-2 space-y-6">
                            <h3 class="text-sm font-bold text-slate-900">Informasi Lengkap Tamu</h3>
                            
                            <div class="space-y-4">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div class="space-y-1">
                                        <label class="text-[10px] text-slate-500 font-bold uppercase tracking-wide block">Nama Lengkap</label>
                                        <input type="text" x-model="bookingForm.nama" class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl p-3 focus:ring-1 focus:ring-wisma-gold focus:outline-none focus:bg-white transition-all">
                                    </div>
                                    <div class="space-y-1">
                                        <label class="text-[10px] text-slate-500 font-bold uppercase tracking-wide block">NIP / DPR ID</label>
                                        <input type="text" x-model="bookingForm.nip" class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl p-3 focus:ring-1 focus:ring-wisma-gold focus:outline-none focus:bg-white transition-all">
                                    </div>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div class="space-y-1">
                                        <label class="text-[10px] text-slate-500 font-bold uppercase tracking-wide block">No. WhatsApp</label>
                                        <input type="text" x-model="bookingForm.whatsapp" class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl p-3 focus:ring-1 focus:ring-wisma-gold focus:outline-none focus:bg-white transition-all">
                                    </div>
                                    <div class="space-y-1">
                                        <label class="text-[10px] text-slate-500 font-bold uppercase tracking-wide block">Alamat Email</label>
                                        <input type="email" x-model="bookingForm.email" class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl p-3 focus:ring-1 focus:ring-wisma-gold focus:outline-none focus:bg-white transition-all">
                                    </div>
                                </div>
                                <div class="flex items-center gap-2 pt-2">
                                    <input type="checkbox" x-model="bookingForm.untukOrangLain" id="untukOrangLain" class="accent-wisma-gold">
                                    <label for="untukOrangLain" class="text-xs text-slate-600 select-none cursor-pointer">Pemesanan diwakilkan untuk orang lain (Delegasi)</label>
                                </div>
                            </div>
                        </div>

                        <!-- Summary Column Right -->
                        <div class="bg-white border border-slate-100 rounded-3xl p-6 shadow-sm space-y-4">
                            <h3 class="text-sm font-bold text-slate-900">Rincian Pembayaran</h3>
                            <div class="space-y-2.5 text-xs">
                                <div class="flex justify-between">
                                    <span class="text-slate-500" x-text="formatRupiah(selectedFacility.price) + ' x ' + calculateNights() + (selectedFacility.unit === 'day' ? ' Hari' : ' Malam')"></span>
                                    <span class="font-semibold text-slate-900" x-text="formatRupiah(selectedFacility.price * calculateNights())"></span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-slate-500">Pajak PPN (11%)</span>
                                    <span class="font-semibold text-slate-900" x-text="formatRupiah(calculateTax())"></span>
                                </div>
                                <div class="border-t border-slate-100 pt-2.5 flex justify-between font-bold text-slate-900">
                                    <span>Total Tagihan</span>
                                    <span class="text-wisma-navy text-sm font-extrabold" x-text="formatRupiah(calculateTotal())"></span>
                                </div>
                            </div>
                            <div class="flex gap-2.5 pt-2">
                                <button @click="proceedToStep(1)" class="flex-1 py-3 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-xl transition-colors">
                                    Kembali
                                </button>
                                <button @click="proceedToStep(3)" class="flex-[2] py-3 bg-slate-950 hover:bg-slate-900 text-white font-bold text-xs rounded-xl shadow-lg transition-colors">
                                    Lanjut ke Pembayaran
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- STEP 3: PAYMENT METHOD (MIDTRANS) -->
                    <div x-show="wizardStep === 3" class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                        <div class="bg-white border border-slate-100 rounded-3xl p-6 shadow-sm lg:col-span-2 flex flex-col justify-center items-center space-y-6 text-center py-12">
                            <div class="w-20 h-20 bg-blue-50 text-blue-500 rounded-full flex items-center justify-center mx-auto shadow-inner mb-2">
                                <i data-lucide="credit-card" class="w-10 h-10"></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-slate-900 font-outfit mb-2">Pilih Metode Pembayaran</h3>
                                <p class="text-xs text-slate-500 max-w-sm mx-auto">Kami menggunakan *Payment Gateway* Midtrans yang aman untuk memproses pembayaran reservasi Anda. Tersedia berbagai opsi seperti Virtual Account, QRIS, dan Kartu Kredit.</p>
                            </div>
                            
                            <div class="p-4 bg-amber-50/50 border border-amber-200/50 rounded-2xl flex items-start gap-3 text-left w-full max-w-md">
                                <i data-lucide="info" class="w-5 h-5 text-amber-500 shrink-0 mt-0.5"></i>
                                <div>
                                    <h4 class="text-xs font-bold text-amber-900">Pembayaran Terlindungi</h4>
                                    <p class="text-[11px] text-slate-600 mt-1">Sistem kami terhubung langsung dengan API Midtrans. Pastikan Anda menyelesaikan pembayaran dalam waktu yang ditentukan setelah jendela pop-up terbuka.</p>
                                </div>
                            </div>
                        </div>

                        <!-- Summary Column Right -->
                        <div class="bg-white border border-slate-100 rounded-3xl p-6 shadow-sm space-y-4">
                            <div class="flex justify-between items-center pb-3 border-b border-slate-100 mb-2">
                                <h3 class="text-sm font-bold text-slate-900">Total Tagihan</h3>
                                <span class="text-wisma-navy text-lg font-extrabold" x-text="formatRupiah(calculateTotal())"></span>
                            </div>
                            <div class="space-y-2.5 text-xs pb-3 border-b border-slate-100">
                                <div class="flex justify-between">
                                    <span class="text-slate-500" x-text="formatRupiah(selectedFacility.price) + ' x ' + calculateNights() + (selectedFacility.unit === 'day' ? ' Hari' : ' Malam')"></span>
                                    <span class="font-semibold text-slate-900" x-text="formatRupiah(selectedFacility.price * calculateNights())"></span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-slate-500">Pajak PPN (11%)</span>
                                    <span class="font-semibold text-slate-900" x-text="formatRupiah(calculateTax())"></span>
                                </div>
                            </div>
                            <div class="flex items-center gap-2 p-3 bg-red-50 border border-red-100 rounded-xl text-red-600 font-bold text-xs justify-center mt-4">
                                <i data-lucide="clock" class="w-4 h-4"></i>
                                <span>Selesaikan pembayaran dalam <span x-text="paymentTimer"></span></span>
                            </div>
                            <div class="flex flex-col gap-3 pt-4 border-t border-slate-100 mt-2">
                                <button @click="payWithMidtrans()" class="w-full py-3.5 bg-[#0091FF] hover:bg-[#007CE6] text-white font-bold text-xs rounded-xl shadow-lg shadow-blue-500/30 transition-all flex items-center justify-center gap-2">
                                    <i data-lucide="shield-check" class="w-4 h-4"></i> Bayar via Midtrans
                                </button>
                                <button @click="proceedToStep(2)" class="w-full py-3 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-xl transition-colors">
                                    Kembali Edit Data
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- STEP 4: BOOKING SUCCESS TICKET -->
                    <div x-show="wizardStep === 4" class="max-w-xl mx-auto bg-white border border-slate-100 rounded-3xl p-8 shadow-xl space-y-6">
                        <div class="text-center space-y-2">
                            <div class="w-14 h-14 bg-emerald-50 text-emerald-500 rounded-full flex items-center justify-center mx-auto mb-2 shadow-inner">
                                <i data-lucide="check-circle" class="w-8 h-8"></i>
                            </div>
                            <h3 class="text-lg font-extrabold text-slate-900 font-outfit">Reservasi Terkonfirmasi</h3>
                            <p class="text-xs text-slate-500">Boarding pass digital Anda siap digunakan. Tunjukkan tiket ini kepada Resepsionis.</p>
                        </div>

                        <!-- Ticket Layout -->
                        <div id="ticket-print-area" class="bg-slate-50 border border-slate-200 rounded-2xl overflow-hidden relative">
                            <!-- Header Ticket -->
                            <div class="bg-wisma-navy text-white p-4 flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <i data-lucide="hotel" class="w-5 h-5 text-wisma-gold"></i>
                                    <span class="text-xs font-bold uppercase tracking-wider font-outfit">Boarding Pass Wisma</span>
                                </div>
                                <span class="px-2 py-0.5 bg-emerald-500 text-white font-bold text-[9px] rounded" x-text="generatedTicket.status"></span>
                            </div>

                            <!-- Body Ticket -->
                            <div class="p-6 space-y-4 text-xs">
                                <div class="grid grid-cols-2 gap-4 border-b border-dashed border-slate-200 pb-4">
                                    <div>
                                        <span class="text-[9px] text-slate-400 font-bold block uppercase tracking-wider">No. Booking</span>
                                        <p class="font-bold text-slate-800" x-text="generatedTicket.booking_code"></p>
                                    </div>
                                    <div>
                                        <span class="text-[9px] text-slate-400 font-bold block uppercase tracking-wider">Nama Tamu</span>
                                        <p class="font-bold text-slate-800 truncate" x-text="generatedTicket.nama"></p>
                                    </div>
                                </div>

                                <div class="grid grid-cols-2 gap-4 border-b border-dashed border-slate-200 pb-4">
                                    <div>
                                        <span class="text-[9px] text-slate-400 font-bold block uppercase tracking-wider">Check-in</span>
                                        <p class="font-bold text-slate-800" x-text="formatIndoDate(generatedTicket.check_in)"></p>
                                    </div>
                                    <div>
                                        <span class="text-[9px] text-slate-400 font-bold block uppercase tracking-wider">Check-out</span>
                                        <p class="font-bold text-slate-800" x-text="formatIndoDate(generatedTicket.check_out)"></p>
                                    </div>
                                </div>

                                <div class="grid grid-cols-2 gap-4 pb-2">
                                    <div>
                                        <span class="text-[9px] text-slate-400 font-bold block uppercase tracking-wider">Unit Kamar / Ruang</span>
                                        <p class="font-bold text-slate-800" x-text="generatedTicket.unit_name"></p>
                                        <p class="text-[9px] text-slate-400 block mt-0.5" x-text="generatedTicket.unit_location"></p>
                                    </div>
                                    <div>
                                        <span class="text-[9px] text-slate-400 font-bold block uppercase tracking-wider">Masa Inap</span>
                                        <p class="font-bold text-slate-800" x-text="generatedTicket.nights + (generatedTicket.unit_name.includes('Rapat') ? ' Hari' : ' Malam')"></p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Ticket Action Buttons -->
                        <div class="flex gap-3 pt-2">
                            <button @click="downloadPDF()" class="flex-1 py-3 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-xl transition-all flex items-center justify-center gap-1.5">
                                <i data-lucide="download" class="w-4 h-4"></i> Unduh Boarding Pass (PDF)
                            </button>
                            <button @click="switchTab('dashboard')" class="flex-1 py-3 bg-wisma-navy hover:bg-slate-800 text-white font-bold text-xs rounded-xl shadow-lg transition-all flex items-center justify-center gap-1.5">
                                <i data-lucide="layout-dashboard" class="w-4 h-4"></i> Kembali ke Dashboard
                            </button>
                        </div>
                    </div>
                </div>

                <!-- 4. BOOKING HISTORY VIEW -->
                <div x-show="currentTab === 'history'" class="space-y-6 fade-in" x-cloak>
                    <div>
                        <h1 class="text-2xl font-outfit font-extrabold text-slate-900">Riwayat & Log Pemesanan Anda</h1>
                        <p class="text-xs text-slate-500">Log transaksi booking kamar dan ruang rapat kenegaraan.</p>
                    </div>

                    <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
                        <div class="p-6 border-b border-slate-100">
                            <h3 class="text-sm font-bold text-slate-900">Seluruh Transaksi Reservasi</h3>
                        </div>
                        <div class="divide-y divide-slate-100">
                            <template x-for="b in bookings" :key="b.id">
                                <div class="p-6 flex flex-col md:flex-row md:items-center justify-between gap-4 hover:bg-slate-50/50 transition-colors">
                                    <div class="flex items-center gap-4">
                                        <img :src="b.unit_photo" class="w-16 h-16 rounded-xl object-cover border border-slate-200 shrink-0">
                                        <div>
                                            <div class="flex items-center gap-2">
                                                <h4 class="text-sm font-bold text-slate-900" x-text="b.unit_name"></h4>
                                                <span class="px-2 py-0.5 text-[9px] uppercase font-bold rounded"
                                                      :class="{
                                                          'bg-amber-100 text-amber-700': b.status === 'Pending',
                                                          'bg-emerald-100 text-emerald-700': b.status === 'Lunas',
                                                          'bg-blue-100 text-blue-700': b.status === 'Check In',
                                                          'bg-red-100 text-red-700': b.status === 'Dibatalkan' || b.status === 'Cancelled',
                                                          'bg-slate-100 text-slate-600': b.status === 'Selesai'
                                                      }"
                                                      x-text="b.status === 'Check In' ? 'Aktif Menginap' : b.status"></span>
                                            </div>
                                            <p class="text-xs text-slate-500 mt-1 flex items-center gap-1.5 flex-wrap">
                                                <span x-text="b.booking_code"></span>
                                                <span class="text-slate-300">•</span>
                                                <i data-lucide="calendar" class="w-3.5 h-3.5"></i>
                                                <span x-text="formatIndoDate(b.check_in) + ' - ' + formatIndoDate(b.check_out)"></span>
                                                <span class="text-slate-300">•</span>
                                                <span x-text="b.nights + (b.unit_name.includes('Rapat') ? ' Hari' : ' Malam')"></span>
                                            </p>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-4">
                                        <div class="text-right">
                                            <span class="text-[9px] text-slate-400 block font-bold uppercase tracking-wide">Total Biaya</span>
                                            <p class="text-sm font-bold text-slate-900" x-text="formatRupiah(b.total_price)"></p>
                                        </div>
                                        
                                        <!-- Actions -->
                                        <div class="flex items-center gap-2">
                                            <template x-if="b.status !== 'Pending' && b.status !== 'Cancelled' && b.status !== 'Dibatalkan'">
                                                <button @click="viewTicket(b)" class="px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-xl transition-all flex items-center gap-1">
                                                    <i data-lucide="ticket" class="w-3.5 h-3.5"></i> Tiket
                                                </button>
                                            </template>
                                            <template x-if="b.status === 'Pending'">
                                                <div class="flex flex-col items-end gap-1.5">
                                                    <div class="text-[10px] text-red-500 font-bold flex items-center gap-1 bg-red-50 px-2 py-1 rounded-md border border-red-100">
                                                        <i data-lucide="clock" class="w-3 h-3"></i> 
                                                        <span x-text="'Batas Waktu: ' + formatExpiryTime(b.created_at)"></span>
                                                    </div>
                                                    <div class="flex items-center gap-1.5 mt-1">
                                                        <button @click="resumePayment(b)" class="px-3 py-2 bg-[#0091FF] hover:bg-[#007CE6] text-white font-bold text-xs rounded-xl shadow-md transition-all flex items-center gap-1">
                                                            <i data-lucide="credit-card" class="w-3.5 h-3.5"></i> Bayar
                                                        </button>
                                                        <button @click="checkPaymentStatus(b)" class="px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-xl shadow-sm transition-all" title="Cek Status Manual">
                                                            <i data-lucide="refresh-cw" class="w-3.5 h-3.5"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </template>
                                            <template x-if="b.status === 'Selesai' && !b.hasFeedback">
                                                <button @click="openRatingModal(b)" class="px-3 py-2 bg-wisma-gold hover:bg-wisma-goldHover text-wisma-dark font-bold text-xs rounded-xl shadow-sm transition-all flex items-center gap-1">
                                                    <i data-lucide="star" class="w-3.5 h-3.5"></i> Ulas
                                                </button>
                                            </template>
                                            <template x-if="b.hasFeedback">
                                                <div class="flex items-center gap-1 text-wisma-gold px-2 py-1 bg-amber-50 rounded-lg text-xs font-extrabold border border-amber-100">
                                                    <i data-lucide="star" class="w-3.5 h-3.5 fill-current"></i>
                                                    <span x-text="b.rating.toFixed(1)"></span>
                                                </div>
                                            </template>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>

                <!-- 5. GUEST PROFILE VIEW -->
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
                <div x-show="currentTab === 'help'" class="space-y-6 fade-in" x-cloak>
                    <div>
                        <h1 class="text-2xl font-outfit font-extrabold text-slate-900">Keluhan & Bantuan Layanan</h1>
                        <p class="text-xs text-slate-500">Laporkan kendala fasilitas selama Anda menginap untuk penanganan cepat.</p>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                        <!-- Submit Complaint Form -->
                        <div class="bg-white border border-slate-100 rounded-3xl p-6 shadow-sm space-y-6">
                            <h3 class="text-sm font-bold text-slate-900">Buat Tiket Keluhan Baru</h3>
                            <form @submit.prevent="submitComplaint()" class="space-y-4">
                                <div class="space-y-1">
                                    <label class="text-[10px] text-slate-500 font-bold uppercase tracking-wide block">Kategori Keluhan</label>
                                    <select x-model="complaintForm.category" class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl p-3 focus:ring-1 focus:ring-wisma-gold focus:outline-none focus:bg-white transition-all">
                                        <option value="facility">Fasilitas (Kamar, Gedung)</option>
                                        <option value="laundry">Layanan Laundry</option>
                                        <option value="internet">Internet / Wifi</option>
                                        <option value="food">Layanan Makanan</option>
                                    </select>
                                </div>
                                <div class="space-y-1">
                                    <label class="text-[10px] text-slate-500 font-bold uppercase tracking-wide block">Lokasi (No. Kamar / Area)</label>
                                    <input type="text" x-model="complaintForm.location" placeholder="Contoh: Kamar 402 atau Lobi Utama" required class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl p-3 focus:ring-1 focus:ring-wisma-gold focus:outline-none focus:bg-white transition-all">
                                </div>
                                <div class="space-y-1">
                                    <label class="text-[10px] text-slate-500 font-bold uppercase tracking-wide block">Penjelasan Kendala</label>
                                    <textarea x-model="complaintForm.description" rows="4" placeholder="Jelaskan secara detail keluhan Anda (misal: AC bocor air, Wifi terputus)..." required class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl p-3 focus:ring-1 focus:ring-wisma-gold focus:outline-none focus:bg-white transition-all resize-none"></textarea>
                                </div>
                                <button type="submit" class="w-full py-3 bg-red-600 hover:bg-red-700 text-white font-bold text-xs rounded-xl shadow-lg transition-colors flex items-center justify-center gap-1.5">
                                    <i data-lucide="alert-triangle" class="w-4 h-4"></i> Kirim Laporan Keluhan
                                </button>
                            </form>
                        </div>

                        <!-- Active Complaints Ticket List -->
                        <div class="bg-white border border-slate-100 rounded-3xl p-6 shadow-sm lg:col-span-2 space-y-4">
                            <h3 class="text-sm font-bold text-slate-900">Status Tiket Keluhan Anda</h3>
                            
                            <div class="divide-y divide-slate-100">
                                <template x-for="c in complaints" :key="c.id">
                                    <div class="py-4 flex items-center justify-between hover:bg-slate-50/50 rounded-xl px-2 transition-colors">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-xl bg-red-50 text-red-500 flex items-center justify-center">
                                                <i data-lucide="alert-circle" class="w-5 h-5"></i>
                                            </div>
                                            <div>
                                                <h4 class="text-xs font-bold text-slate-900" x-text="c.title"></h4>
                                                <p class="text-[10px] text-slate-400 mt-0.5" x-text="c.category + ' • ' + c.location"></p>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <span class="px-2 py-0.5 rounded text-[9px] uppercase font-bold tracking-wide"
                                                  :class="{
                                                      'bg-red-100 text-red-700': c.status === 'Pending',
                                                      'bg-blue-100 text-blue-700': c.status === 'Processed',
                                                      'bg-emerald-100 text-emerald-700': c.status === 'Resolved'
                                                  }"
                                                  x-text="c.status === 'Pending' ? 'Menunggu' : (c.status === 'Processed' ? 'Diproses' : 'Selesai')"></span>
                                            <span class="text-[9px] text-slate-400 block mt-1" x-text="c.date"></span>
                                        </div>
                                    </div>
                                </template>
                                <div x-show="complaints.length === 0" class="text-center py-12 text-slate-400">
                                    <i data-lucide="check-circle-2" class="w-12 h-12 mx-auto mb-2 text-slate-300"></i>
                                    <p class="text-xs">Tidak ada keluhan aktif dari Anda saat ini.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </main>
        </div>
    </div>

    <!-- 7. FACILITY DETAIL DRAWER (SLIDE IN RIGHT) -->
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
                        <p class="text-xs text-slate-500 font-medium mt-1 flex items-center gap-1"><i data-lucide="map-pin" class="w-3.5 h-3.5"></i> <span x-text="drawerFacility.gedung + ' • ' + drawerFacility.lantai"></span></p>
                    </div>

                    <div class="grid grid-cols-1 gap-4">
                        <div class="bg-slate-50 p-3.5 rounded-2xl border border-slate-100 flex items-center gap-3">
                            <i data-lucide="maximize" class="w-5 h-5 text-slate-500"></i>
                            <div>
                                <span class="text-[9px] text-slate-400 block font-bold uppercase tracking-wide">Luas Area</span>
                                <p class="text-xs font-bold text-slate-800" x-text="drawerFacility.luas || '30 m²'"></p>
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
                <button @click="startBookingFlow(drawerFacility)" class="px-6 py-3 bg-wisma-navy hover:bg-slate-800 text-white font-bold text-xs rounded-xl shadow-lg transition-colors flex items-center gap-1.5">
                    <i data-lucide="calendar" class="w-4 h-4"></i> Booking Sekarang
                </button>
            </div>
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
                currentTime: new Date(),
                loginForm: {
                    role: 'guest',
                    dprId: 'budi.santoso@dpr.go.id',
                    password: 'password'
                },

                currentTab: 'dashboard',
                searchQuery: '',
                filterGedung: '',
                filterLantai: '',
                filterTipe: '',
                filterStatus: 'semua',
                
                drawerOpen: false,
                drawerFacility: {},
                
                selectedFacility: {},
                wizardStep: 1,
                checkInDate: '',
                checkOutDate: '',
                guestCount: 2,
                bookingForm: {
                    nama: '',
                    nip: '',
                    whatsapp: '',
                    email: '',
                    untukOrangLain: false
                },
                // Midtrans payment handled externally
                paymentTimer: '29:59',
                timerInterval: null,
                
                toasts: [],
                toastCount: 0,
                generatedTicket: {},
                
                calendarYear: new Date().getFullYear(),
                calendarMonth: new Date().getMonth(),
                calendarDays: [],
                calendarDays: [],
                calendarBlanks: [],
                bookedDatesArr: [],

                profile: {
                    id: null,
                    role: 'guest',
                    nama: '',
                    nip: '',
                    whatsapp: '',
                    email: '',
                    instansi: '',
                    role_label: 'Tamu'
                },

                // Shared data collections
                facilities: [],
                bookings: [],
                guests: [],
                complaints: [],
                notifications: [],
                unreadNotificationCount: 0,
                showNotifications: false,

                ratingModalOpen: false,
                feedbackBooking: {},
                feedbackRating: {
                    cleanliness: 5,
                    facilities: 5,
                    service: 5
                },
                feedbackComment: '',

                complaintForm: {
                    category: 'facility',
                    location: '',
                    description: ''
                },

                passwordForm: {
                    current_password: '',
                    password: '',
                    password_confirmation: ''
                },

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
                        // Try restore session
                        const me = await this.apiCall('GET', '/me');
                        if (me.success) {
                            this.fillProfile(me.data);
                            this.isLoggedIn = true;
                            await this.loadFacilitiesFromApi();
                            await this.loadBookingsFromApi();
                            await this.loadComplaintsFromApi();
                            await this.loadNotifications();
                        } else {
                            localStorage.removeItem('wisma_token');
                            this.loadFallbackState();
                        }
                    } else {
                        this.loadFallbackState();
                    }
                    
                    // Set timer for current time updates every second
                    setInterval(() => {
                        this.currentTime = new Date();
                    }, 1000);

                    // Re-initialize lucide icons periodically if DOM changes (or use MutationObserver)
                    setInterval(() => {
                        if (window.lucide) window.lucide.createIcons();
                    }, 1000);
                    
                    this.selectedFacility = this.facilities[0] || {};
                    this.buildCalendar();
                    setTimeout(() => {
                        if (window.lucide) window.lucide.createIcons();
                    }, 100);
                },

                fillProfile(user) {
                    const roleLabels = {
                        guest: 'Anggota Kehormatan',
                        receptionist: 'Resepsionis',
                        koordinator_wisma: 'Koordinator Wisma',
                        customer_service: 'Customer Service'
                    };
                    this.profile.id        = user.id;
                    this.profile.role      = user.role;
                    this.profile.role_label= roleLabels[user.role] || user.role;
                    this.profile.nama      = user.name;
                    this.profile.nip       = user.nip || '';
                    this.profile.whatsapp  = user.phone || '';
                    this.profile.email     = user.email;
                    this.profile.instansi  = user.instansi || '';
                    // Pre-fill booking form with logged-in user data
                    this.bookingForm.nama  = user.name;
                    this.bookingForm.nip   = user.nip || '';
                    this.bookingForm.whatsapp = user.phone || '';
                    this.bookingForm.email = user.email;
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

                loadFallbackState() {
                    // Tampilkan data kosong saat belum login
                    this.facilities = [];
                    this.bookings   = [];
                    this.guests     = [];
                    this.complaints = [];
                    this.notifications = [];
                    this.unreadNotificationCount = 0;
                },

                async loadBookingsFromApi() {
                    try {
                        const res = await this.apiCall('GET', '/bookings');
                        if (res.success) {
                            this.bookings = res.data.map(b => ({
                                id: b.id,
                                booking_code: b.booking_code,
                                unit_name: b.facility.name,
                                unit_photo: b.facility.photo ? (b.facility.photo.startsWith('http') ? b.facility.photo : 'http://localhost:8000' + b.facility.photo) : '/images/bungalow_buah.jpg',
                                unit_location: b.facility.gedung + ' • ' + b.facility.lantai,
                                check_in: b.check_in.substring(0,10),
                                check_out: b.check_out.substring(0,10),
                                nights: b.nights,
                                total_price: b.total_price,
                                snap_token: b.snap_token,
                                created_at: b.created_at,
                                status: (b.status === 'pending' ? 'Pending' : 
                                        (b.status === 'lunas' ? 'Lunas' : 
                                        (b.status === 'check_in' ? 'Check In' : 
                                        (b.status === 'cancelled' ? 'Dibatalkan' : 'Selesai')))),
                                nama: b.guest_name,
                                nip: b.guest_nip,
                                hasFeedback: b.has_feedback,
                                rating: b.has_feedback ? 5 : 0 // The exact rating is not returned by default unless feedback relation is loaded, but hasFeedback boolean is enough for the UI to hide the button.
                            }));
                        }
                    } catch (e) {
                        console.error('Gagal memuat bookings dari API:', e);
                    }
                },

                // ==========================================
                // LOGIN
                // ==========================================
                async login() {
                    if (!this.loginForm.dprId || !this.loginForm.password) {
                        this.addToast('Data Tidak Lengkap', 'Email dan Password tidak boleh kosong.', 'error');
                        return;
                    }
                    this.isLoading = true;
                    try {
                        const res = await this.apiCall('POST', '/login', {
                            email: this.loginForm.dprId,
                            password: this.loginForm.password
                        });
                        if (res.success) {
                            localStorage.setItem('wisma_token', res.data.token);
                            this.fillProfile(res.data.user);
                            this.isLoggedIn = true;
                            this.currentTab = 'dashboard';
                            await this.loadFacilitiesFromApi();
                            await this.loadBookingsFromApi();
                            await this.loadComplaintsFromApi();
                            await this.loadNotifications();
                            this.addToast('Login Berhasil', `Selamat datang, ${this.profile.nama}.`, 'success');
                        } else {
                            const msg = res.message || 'Email atau password salah.';
                            this.addToast('Login Gagal', msg, 'error');
                        }
                    } catch (e) {
                        this.addToast('Koneksi Gagal', 'Tidak dapat terhubung ke server. Pastikan backend berjalan.', 'error');
                    } finally {
                        this.isLoading = false;
                        setTimeout(() => { if (window.lucide) window.lucide.createIcons(); }, 50);
                    }
                },

                // ==========================================
                // NOTIFICATIONS
                // ==========================================
                async loadNotifications() {
                    try {
                        const res = await this.apiCall('GET', '/notifications');
                        if (res.success) {
                            this.notifications = res.data;
                            this.unreadNotificationCount = res.unread_count;
                        }
                    } catch (e) {
                        console.error('Gagal memuat notifikasi', e);
                    }
                },

                async markNotificationsRead() {
                    try {
                        const res = await this.apiCall('PUT', '/notifications/read-all');
                        if (res.success) {
                            this.unreadNotificationCount = 0;
                            this.notifications = this.notifications.map(n => ({...n, is_read: true}));
                        }
                    } catch (e) {
                        console.error('Gagal menandai notifikasi dibaca', e);
                    }
                },

                // ==========================================
                // LOGOUT
                // ==========================================
                async logout() {
                    try {
                        await this.apiCall('POST', '/logout');
                    } catch (e) { /* ignore */ }
                    localStorage.removeItem('wisma_token');
                    this.isLoggedIn = false;
                    this.profile = { id: null, role: 'guest', nama: '', nip: '', whatsapp: '', email: '', instansi: '', role_label: 'Tamu' };
                    this.loadFallbackState();
                    this.addToast('Logout Sukses', 'Anda telah keluar dari sesi portal tamu.', 'info');
                    setTimeout(() => { if (window.lucide) window.lucide.createIcons(); }, 50);
                },

                persistState() {
                    localStorage.setItem('wisma_facilities', JSON.stringify(this.facilities));
                    localStorage.setItem('wisma_bookings', JSON.stringify(this.bookings));
                    localStorage.setItem('wisma_guests', JSON.stringify(this.guests));
                    localStorage.setItem('wisma_complaints', JSON.stringify(this.complaints));
                },

                loadState() {
                    const savedFacilities = localStorage.getItem('wisma_facilities');
                    const savedBookings = localStorage.getItem('wisma_bookings');
                    const savedGuests = localStorage.getItem('wisma_guests');
                    const savedComplaints = localStorage.getItem('wisma_complaints');
                    
                    // Force refresh schema if old structure exists
                    let needForceRefresh = false;
                    if (savedFacilities) {
                        try {
                            const facilitiesList = JSON.parse(savedFacilities);
                            if (facilitiesList.length === 0 || !facilitiesList.some(f => f.name === 'Ruang Panja (Rapat)') || facilitiesList.some(f => f.price === 750000 || f.price === 850000 || f.lantai.includes('Lantai') || f.name.includes('Kamar') || f.photo.includes('unsplash.com') || (f.photo.includes('bungalow.jpg') && !f.photo.includes('_buah') && !f.photo.includes('_bunga')))) {
                                needForceRefresh = true;
                            }
                        } catch (e) {
                            needForceRefresh = true;
                        }
                    } else {
                        needForceRefresh = true;
                    }

                    if (needForceRefresh) {
                        localStorage.removeItem('wisma_facilities');
                        localStorage.removeItem('wisma_bookings');
                        localStorage.removeItem('wisma_guests');
                        localStorage.removeItem('wisma_complaints');
                    }

                    const freshFacilities = localStorage.getItem('wisma_facilities');
                    const freshBookings = localStorage.getItem('wisma_bookings');
                    const freshGuests = localStorage.getItem('wisma_guests');
                    const freshComplaints = localStorage.getItem('wisma_complaints');

                    // Fallback to defaults if not exists in localStorage
                    if (freshFacilities) {
                        this.facilities = JSON.parse(freshFacilities);
                    } else {
                        const fruitNames = [
                            'Kedondong', 'Kesemek', 'Jamblang', 'Jeruk', 'Jambu', 'Delima', 'Duku', 'Durian',
                            'Apel', 'Anggur', 'Leci', 'Alpukat', 'Belimbing', 'Buni', 'Cempedal', 'Ceremai',
                            'Kelengkeng', 'Kecapi', 'Kepel', 'Kelapa', 'Salak', 'Langsat', 'Mundhu', 'Mangga',
                            'Manggis', 'Markisa', 'Mengkudu', 'Melon', 'Nana', 'Maja', 'Nangka', 'Pepaya'
                        ];
                        const flowerNames = [
                            'Widelia', 'Gladiol', 'Krisan', 'Tanjung', 'Teratai', 'Lotus', 'Seroja', 'Anthurium',
                            'Aster', 'Kemuning', 'Lili', 'Alamanda', 'Dahlia', 'Gardenia', 'Nusa Indah', 'Kana',
                            'Asoka', 'Raflesia', 'Lavender', 'Kenanga', 'Anyelir', 'Kamboja', 'Rosalia', 'Bugenvile'
                        ];
                        this.facilities = [];
                        fruitNames.forEach((name, idx) => {
                            const id = idx + 1;
                            let status = 'READY';
                            if (name === 'Durian') status = 'MAINTENANCE';
                            if (name === 'Alpukat') status = 'CLEANING';
                            
                            this.facilities.push({
                                id: id,
                                name: 'Bungalow ' + name,
                                type: 'Buah',
                                gedung: 'Wisma',
                                lantai: 'Area Bawah',
                                capacity: 2,
                                price: 387000,
                                unit: 'night',
                                luas: '24 m²',
                                bed: 'Queen Size',
                                status: status,
                                photo: '/images/bungalow_buah.jpg',
                                description: 'Bungalow Standard tipe Buah yang nyaman dengan fasilitas tempat tidur Queen Size, AC, TV, kamar mandi dalam, dan perlengkapan mandi lengkap.'
                            });
                        });
                        flowerNames.forEach((name, idx) => {
                            const id = idx < 12 ? (idx + 39) : (idx - 12 + 55);
                            let status = 'READY';
                            if (name === 'Dahlia') status = 'CLEANING';
                            if (name === 'Kenanga') status = 'MAINTENANCE';

                            this.facilities.push({
                                id: id,
                                name: 'Bungalow ' + name,
                                type: 'Bunga',
                                gedung: 'Wisma',
                                lantai: 'Area Atas',
                                capacity: 2,
                                price: 549000,
                                unit: 'night',
                                luas: '28 m²',
                                bed: 'Twin Bed',
                                status: status,
                                photo: '/images/bungalow_bunga.jpg',
                                description: 'Bungalow Standard tipe Bunga yang tenang dan bersih di lantai atas, dilengkapi dengan Twin Bed, AC, TV, Wi-Fi, dan pemandangan luar wisma.'
                            });
                        });

                        // Add Ruang Panja (Rapat)
                        this.facilities.push({
                            id: 100,
                            name: 'Ruang Panja (Rapat)',
                            type: 'Rapat',
                            gedung: 'Wisma',
                            lantai: 'Area Bawah',
                            capacity: 30,
                            price: 250000,
                            unit: 'day',
                            luas: '60 m²',
                            bed: 'Meja Rapat Oval',
                            status: 'READY',
                            photo: '/images/ruang_rapat.jpeg',
                            description: 'Ruang rapat/sidang Panja Wisma DPR RI yang nyaman, dilengkapi dengan meja oval rapat, kursi ergonomis, sound system, proyektor, AC, dan Wi-Fi cepat.'
                        });

                        localStorage.setItem('wisma_facilities', JSON.stringify(this.facilities));
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

                    if (freshGuests) {
                        this.guests = JSON.parse(freshGuests);
                    } else {
                        this.guests = [
                            {
                                id: 'T-2024-001',
                                nama: 'Budi Santoso',
                                nip: '198904122015031002',
                                phone: '0812-3456-7890',
                                email: 'budi.santoso@dpr.go.id',
                                status: 'Member',
                                kunjungan: 12,
                                terakhir: 'Hari ini (Check-in)'
                            }
                        ];
                        localStorage.setItem('wisma_guests', JSON.stringify(this.guests));
                    }

                    if (freshComplaints) {
                        this.complaints = JSON.parse(freshComplaints);
                    } else {
                        this.complaints = [
                            {
                                id: 'COMP-101',
                                title: 'AC Bungalow Kedondong Kurang Dingin',
                                category: 'Fasilitas (Kamar, Gedung)',
                                category_slug: 'facility',
                                location: 'Bungalow Kedondong',
                                date: '24 Okt 2023, 09:15',
                                status: 'Pending',
                                icon: 'wind'
                            }
                        ];
                        localStorage.setItem('wisma_complaints', JSON.stringify(this.complaints));
                    }
                },

                switchTab(tab) {
                    this.currentTab = tab;
                    this.closeDrawer();
                    setTimeout(() => {
                        if (window.lucide) {
                            window.lucide.createIcons();
                        }
                    }, 50);
                },

                showFeatureMuted(featureName) {
                    this.addToast('Info Simulasi', `Fitur '${featureName}' sukses disimulasikan pada versi UI prototype ini.`, 'info');
                },

                filteredFacilities() {
                    return this.facilities.filter(f => {
                        const matchesSearch = f.name.toLowerCase().includes(this.searchQuery.toLowerCase()) || 
                                              f.description.toLowerCase().includes(this.searchQuery.toLowerCase());
                        const matchesGedung = this.filterGedung === '' || f.gedung === this.filterGedung;
                        const matchesLantai = this.filterLantai === '' || f.lantai === this.filterLantai;
                        const matchesTipe = this.filterTipe === '' || f.type === this.filterTipe;
                        const matchesStatus = this.filterStatus === 'semua' || f.status === 'READY';
                        
                        return matchesSearch && matchesGedung && matchesLantai && matchesTipe && matchesStatus;
                    });
                },

                resetFilters() {
                    this.filterGedung = '';
                    this.filterLantai = '';
                    this.filterTipe = '';
                    this.filterStatus = 'semua';
                    this.searchQuery = '';
                    this.addToast('Filter Direset', 'Menampilkan seluruh katalog fasilitas.', 'success');
                },

                openDrawer(facility) {
                    this.drawerFacility = facility;
                    this.drawerOpen = true;
                    setTimeout(() => {
                        if (window.lucide) {
                            window.lucide.createIcons();
                        }
                    }, 50);
                },

                closeDrawer() {
                    this.drawerOpen = false;
                },

                async startBookingFlow(facility) {
                    this.selectedFacility = facility;
                    this.drawerOpen = false;
                    this.wizardStep = 1;
                    
                    // Reset tanggal agar tidak terbawa dari fasilitas sebelumnya
                    this.checkInDate = null;
                    this.checkOutDate = null;

                    try {
                        const res = await this.apiCall('GET', `/facilities/${facility.id}/booked-dates`);
                        if (res.success) {
                            this.bookedDatesArr = res.data.map(d => ({
                                check_in: new Date(d.check_in),
                                check_out: new Date(d.check_out)
                            }));
                        } else {
                            this.bookedDatesArr = [];
                        }
                    } catch (e) {
                        this.bookedDatesArr = [];
                    }
                    this.buildCalendar(); // Rebuild calendar to apply booked dates

                    this.switchTab('booking_wizard');
                },

                proceedToStep(step) {
                    if (step === 2) {
                        if (!this.checkInDate || !this.checkOutDate) {
                            this.addToast('Pilih Tanggal', 'Silakan pilih tanggal Check-in dan Check-out terlebih dahulu.', 'error');
                            return;
                        }
                        const diff = this.calculateNights();
                        if (diff <= 0) {
                            this.addToast('Tanggal Tidak Valid', 'Tanggal check-out harus setelah tanggal check-in.', 'error');
                            return;
                        }
                    }
                    if (step === 3) {
                        if (!this.bookingForm.nama || !this.bookingForm.nip || !this.bookingForm.whatsapp || !this.bookingForm.email) {
                            this.addToast('Data Tidak Lengkap', 'Harap isi seluruh formulir informasi tamu.', 'error');
                            return;
                        }
                        this.startPaymentTimer();
                    }
                    this.wizardStep = step;
                    setTimeout(() => {
                        if (window.lucide) {
                            window.lucide.createIcons();
                        }
                    }, 50);
                },

                async payWithMidtrans() {
                    clearInterval(this.timerInterval);
                    this.addToast('Menghubungkan Midtrans', 'Mendapatkan Snap Token dari server...', 'info');

                    try {
                        const payload = {
                            facility_id: this.selectedFacility.id,
                            check_in: this.checkInDate,
                            check_out: this.checkOutDate,
                            guest_name: this.bookingForm.untukOrangLain ? 'Delegasi: ' + this.bookingForm.nama : this.bookingForm.nama,
                            guest_nip: this.bookingForm.nip,
                            guest_phone: this.bookingForm.whatsapp,
                            guest_email: this.bookingForm.email,
                        };

                        const res = await this.apiCall('POST', '/bookings', payload);

                        if (res.success) {
                            const snapToken = res.data.snap_token;
                            const createdBooking = res.data;
                            
                            window.snap.pay(snapToken, {
                                onSuccess: (result) => {
                                    this.addToast('Pembayaran Berhasil', 'Transaksi Midtrans berhasil diselesaikan.', 'success');
                                    
                                    // Generate ticket for display
                                    this.generatedTicket = {
                                        id: createdBooking.id,
                                        booking_code: createdBooking.booking_code,
                                        unit_name: this.selectedFacility.name,
                                        unit_location: `${this.selectedFacility.gedung} • ${this.selectedFacility.lantai}`,
                                        check_in: this.checkInDate,
                                        check_out: this.checkOutDate,
                                        nights: this.calculateNights(),
                                        status: 'Lunas',
                                        nama: payload.guest_name
                                    };
                                    
                                    this.wizardStep = 4;
                                    this.loadBookingsFromApi();
                                    
                                    setTimeout(() => {
                                        if (window.lucide) window.lucide.createIcons();
                                    }, 50);
                                },
                                onPending: (result) => {
                                    this.addToast('Menunggu Pembayaran', 'Silakan selesaikan pembayaran sesuai instruksi Midtrans.', 'info');
                                    this.loadBookingsFromApi();
                                    this.switchTab('history');
                                },
                                onError: (result) => {
                                    this.addToast('Pembayaran Gagal', 'Terjadi kesalahan pada transaksi pembayaran.', 'error');
                                    this.loadBookingsFromApi();
                                    this.switchTab('history');
                                },
                                onClose: () => {
                                    this.addToast('Dibatalkan', 'Anda menutup pop-up pembayaran sebelum selesai.', 'error');
                                    this.loadBookingsFromApi();
                                    this.switchTab('history');
                                }
                            });
                        } else {
                            const errors = res.errors ? Object.values(res.errors).flat().join(' ') : (res.message || 'Gagal membuat reservasi.');
                            this.addToast('Gagal', errors, 'error');
                            this.proceedToStep(2); // Kembali ke form
                        }
                    } catch (e) {
                        this.addToast('Koneksi Gagal', 'Tidak dapat terhubung ke server.', 'error');
                    }
                },

                resumePayment(booking) {
                    if (!booking.snap_token) {
                        this.addToast('Token Tidak Valid', 'Booking ini tidak memiliki token pembayaran aktif.', 'error');
                        return;
                    }
                    this.addToast('Menghubungkan Midtrans', 'Membuka jendela pembayaran...', 'info');
                    window.snap.pay(booking.snap_token, {
                        onSuccess: (result) => {
                            this.addToast('Pembayaran Berhasil', 'Transaksi Midtrans berhasil diselesaikan.', 'success');
                            this.loadBookingsFromApi();
                        },
                        onPending: (result) => {
                            this.addToast('Menunggu Pembayaran', 'Silakan selesaikan pembayaran.', 'info');
                            this.loadBookingsFromApi();
                        },
                        onError: (result) => {
                            this.addToast('Pembayaran Gagal', 'Terjadi kesalahan pada transaksi pembayaran.', 'error');
                            this.loadBookingsFromApi();
                        },
                        onClose: () => {
                            this.addToast('Dibatalkan', 'Anda menutup pop-up pembayaran sebelum selesai.', 'info');
                            this.loadBookingsFromApi();
                        }
                    });
                },

                async checkPaymentStatus(booking) {
                    this.addToast('Mengecek Status', 'Menghubungkan ke server Midtrans...', 'info');
                    try {
                        const res = await this.apiCall('POST', `/bookings/${booking.id}/check-status`);
                        if (res.success) {
                            if (res.data && res.data.status === 'lunas') {
                                this.addToast('Sukses', 'Pembayaran telah diverifikasi lunas!', 'success');
                            } else if (res.data && res.data.status === 'cancelled') {
                                this.addToast('Dibatalkan', 'Transaksi telah dibatalkan atau kedaluwarsa.', 'error');
                            } else {
                                this.addToast('Pending', 'Pembayaran masih tertunda.', 'info');
                            }
                            this.loadBookingsFromApi();
                        } else {
                            this.addToast('Gagal', res.message || 'Gagal mengecek status.', 'error');
                        }
                    } catch (e) {
                        this.addToast('Koneksi Gagal', 'Tidak dapat terhubung ke server.', 'error');
                    }
                    
                    setTimeout(() => {
                        if (window.lucide) window.lucide.createIcons();
                    }, 50);
                },

                async saveProfile() {
                    try {
                        const res = await this.apiCall('PUT', '/profile', {
                            name:     this.profile.nama,
                            phone:    this.profile.whatsapp,
                            email:    this.profile.email,
                            instansi: this.profile.instansi,
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

                async savePassword() {
                    if (!this.passwordForm.current_password) {
                        this.addToast('Gagal', 'Masukkan kata sandi saat ini.', 'error'); return;
                    }
                    if (this.passwordForm.password.length < 8) {
                        this.addToast('Gagal', 'Kata sandi baru minimal 8 karakter.', 'error'); return;
                    }
                    if (this.passwordForm.password !== this.passwordForm.password_confirmation) {
                        this.addToast('Gagal', 'Konfirmasi kata sandi tidak cocok.', 'error'); return;
                    }
                    try {
                        const res = await this.apiCall('PUT', '/profile/password', {
                            current_password:      this.passwordForm.current_password,
                            password:              this.passwordForm.password,
                            password_confirmation: this.passwordForm.password_confirmation,
                        });
                        if (res.success) {
                            this.passwordForm = { current_password: '', password: '', password_confirmation: '' };
                            this.addToast('Kata Sandi Diperbarui', 'Kata sandi Anda berhasil diubah.', 'success');
                        } else {
                            const errors = res.errors ? Object.values(res.errors).flat().join(' ') : (res.message || 'Gagal mengubah kata sandi.');
                            this.addToast('Gagal', errors, 'error');
                        }
                    } catch (e) {
                        this.addToast('Koneksi Gagal', 'Tidak dapat terhubung ke server.', 'error');
                    }
                },

                downloadPDF() {
                    // FR-07.02: Cetak / Unduh E-Tiket (Boarding Pass) via browser print
                    const printContents = document.getElementById('ticket-print-area');
                    if (!printContents) {
                        this.addToast('Gagal', 'Tidak dapat menemukan area tiket untuk dicetak.', 'error');
                        return;
                    }
                    const html = `<!DOCTYPE html>
<html>
<head>
<title>Boarding Pass - ${this.generatedTicket.booking_code}</title>
<style>
  body { font-family: sans-serif; padding: 24px; color: #1e293b; }
  .header { background: #0B1A30; color: white; padding: 16px; border-radius: 8px 8px 0 0; display: flex; justify-content: space-between; align-items: center; }
  .header .title { font-weight: bold; font-size: 14px; letter-spacing: 1px; }
  .badge { background: #10b981; color: white; font-size: 10px; font-weight: bold; padding: 2px 8px; border-radius: 4px; }
  .body { border: 1px solid #e2e8f0; border-top: none; padding: 20px; border-radius: 0 0 8px 8px; }
  .grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; padding-bottom: 16px; margin-bottom: 16px; border-bottom: 1px dashed #cbd5e1; }
  .label { font-size: 9px; color: #94a3b8; font-weight: bold; text-transform: uppercase; letter-spacing: 1px; display: block; margin-bottom: 2px; }
  .value { font-size: 13px; font-weight: bold; color: #0f172a; }
  .total { text-align: center; padding-top: 12px; }
  .total .label { font-size: 11px; }
  .total .value { font-size: 20px; color: #0B1A30; }
  .footer { margin-top: 20px; text-align: center; font-size: 10px; color: #94a3b8; }
</style>
</head>
<body>
${printContents.innerHTML}
<div class="footer">Dicetak dari Sistem Informasi Wisma DPR RI Kopo — ${new Date().toLocaleDateString('id-ID', {weekday:'long', year:'numeric', month:'long', day:'numeric'})}</div>
</body>
</html>`;
                    const printWindow = window.open('', '_blank');
                    printWindow.document.write(html);
                    printWindow.document.close();
                    printWindow.focus();
                    setTimeout(() => { printWindow.print(); }, 300);
                    this.addToast('Boarding Pass Siap', 'Jendela cetak dibuka. Pilih "Save as PDF" untuk mengunduh.', 'success');
                },

                copyVA() {
                    navigator.clipboard.writeText('8807198904122015');
                    this.addToast('VA Disalin', 'Nomor Virtual Account disalin ke clipboard.', 'success');
                },

                viewTicket(booking) {
                    this.generatedTicket = booking;
                    this.wizardStep = 4;
                    this.currentTab = 'booking_wizard';
                    setTimeout(() => {
                        if (window.lucide) {
                            window.lucide.createIcons();
                        }
                    }, 50);
                },

                openRatingModal(booking) {
                    this.feedbackBooking = booking;
                    this.feedbackRating.cleanliness = 5;
                    this.feedbackRating.facilities = 5;
                    this.feedbackRating.service = 5;
                    this.feedbackComment = '';
                    this.ratingModalOpen = true;
                },

                async submitRating() {
                    const avg = (this.feedbackRating.cleanliness + this.feedbackRating.facilities + this.feedbackRating.service) / 3;
                    const bookingId = this.feedbackBooking.id;
                    
                    try {
                        const res = await this.apiCall('POST', `/bookings/${bookingId}/feedback`, {
                            rating_cleanliness: this.feedbackRating.cleanliness,
                            rating_facilities: this.feedbackRating.facilities,
                            rating_service: this.feedbackRating.service,
                            comment: this.feedbackComment
                        });

                        if (res.success) {
                            this.ratingModalOpen = false;
                            this.addToast('Feedback Terkirim', 'Terima kasih atas ulasan Anda untuk meningkatkan layanan kami.', 'success');
                            this.loadBookingsFromApi(); // Reload to update status
                        } else {
                            const errors = res.errors ? Object.values(res.errors).flat().join(' ') : (res.message || 'Gagal mengirim ulasan.');
                            this.addToast('Gagal', errors, 'error');
                        }
                    } catch (e) {
                        this.addToast('Koneksi Gagal', 'Tidak dapat terhubung ke server.', 'error');
                    }
                    
                    setTimeout(() => {
                        if (window.lucide) {
                            window.lucide.createIcons();
                        }
                    }, 50);
                },

                async submitComplaint() {
                    if (!this.complaintForm.location || !this.complaintForm.description) {
                        this.addToast('Gagal Mengirim', 'Harap isi lokasi dan deskripsi keluhan Anda.', 'error');
                        return;
                    }

                    try {
                        const title = this.complaintForm.description.length > 30 ? this.complaintForm.description.substring(0, 30) + '...' : this.complaintForm.description;
                        
                        const res = await this.apiCall('POST', '/complaints', {
                            title: title,
                            category: this.complaintForm.category,
                            location: this.complaintForm.location,
                            description: this.complaintForm.description
                        });

                        if (res.success) {
                            this.complaintForm.location = '';
                            this.complaintForm.description = '';
                            this.addToast('Keluhan Terkirim', 'Laporan Anda sudah diterima front office untuk segera ditangani.', 'success');
                            this.loadComplaintsFromApi();
                        } else {
                            const errors = res.errors ? Object.values(res.errors).flat().join(' ') : (res.message || 'Gagal mengirim keluhan.');
                            this.addToast('Gagal', errors, 'error');
                        }
                    } catch (e) {
                        this.addToast('Koneksi Gagal', 'Tidak dapat terhubung ke server.', 'error');
                    }
                    
                    setTimeout(() => {
                        if (window.lucide) {
                            window.lucide.createIcons();
                        }
                    }, 50);
                },

                async loadComplaintsFromApi() {
                    try {
                        const res = await this.apiCall('GET', '/complaints');
                        if (res.success) {
                            this.complaints = res.data.map(c => {
                                const categoryNames = {
                                    facility: 'Fasilitas',
                                    laundry: 'Layanan Laundry',
                                    internet: 'Internet / Wifi',
                                    food: 'Layanan Makanan'
                                };
                                const d = new Date(c.created_at);
                                const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
                                const dateStr = `${d.getDate()} ${months[d.getMonth()]} ${d.getFullYear()}, ${String(d.getHours()).padStart(2,'0')}:${String(d.getMinutes()).padStart(2,'0')}`;
                                
                                return {
                                    id: c.complaint_code,
                                    title: c.title,
                                    category: categoryNames[c.category] || c.category,
                                    location: c.location,
                                    date: dateStr,
                                    status: c.status === 'pending' ? 'Pending' : (c.status === 'processed' ? 'Processed' : 'Resolved')
                                };
                            });
                        }
                    } catch (e) {
                        console.error('Gagal memuat keluhan', e);
                    }
                },

                calculateNights() {
                    const checkIn = new Date(this.checkInDate);
                    const checkOut = new Date(this.checkOutDate);
                    const diffTime = Math.abs(checkOut - checkIn);
                    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
                    return isNaN(diffDays) ? 1 : (diffDays === 0 ? 1 : diffDays);
                },

                calculateTax() {
                    if (!this.selectedFacility) return 0;
                    const subtotal = this.selectedFacility.price * this.calculateNights();
                    return subtotal * 0.11;
                },

                formatExpiryTime(createdAtStr) {
                    // Update trigger for Alpine reactivity using currentTime
                    const trigger = this.currentTime; 
                    
                    const createdDate = new Date(createdAtStr);
                    // Add 60 minutes
                    createdDate.setMinutes(createdDate.getMinutes() + 60);
                    
                    const now = new Date();
                    const diffMs = createdDate - now;
                    
                    if (diffMs <= 0) return 'Kedaluwarsa';
                    
                    const diffMins = Math.floor(diffMs / 60000);
                    const diffSecs = Math.floor((diffMs % 60000) / 1000);
                    
                    return `${String(diffMins).padStart(2, '0')}:${String(diffSecs).padStart(2, '0')}`;
                },

                calculateTotal() {
                    return (this.selectedFacility.price * this.calculateNights()) + this.calculateTax();
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

                startPaymentTimer() {
                    if (this.timerInterval) clearInterval(this.timerInterval);
                    let duration = 30 * 60 - 1;
                    
                    this.timerInterval = setInterval(() => {
                        let minutes = Math.floor(duration / 60);
                        let seconds = duration % 60;
                        
                        minutes = minutes < 10 ? '0' + minutes : minutes;
                        seconds = seconds < 10 ? '0' + seconds : seconds;
                        
                        this.paymentTimer = minutes + ':' + seconds;
                        
                        if (--duration < 0) {
                            clearInterval(this.timerInterval);
                            this.addToast('Waktu Habis', 'Sesi pembayaran telah kedaluwarsa.', 'error');
                            this.switchTab('facilities');
                        }
                    }, 1000);
                },

                buildCalendar() {
                    const firstDayOfMonth = new Date(this.calendarYear, this.calendarMonth, 1);
                    const lastDayOfMonth = new Date(this.calendarYear, this.calendarMonth + 1, 0);
                    const totalDays = lastDayOfMonth.getDate();
                    const startDayOfWeek = firstDayOfMonth.getDay();
                    
                    this.calendarBlanks = Array(startDayOfWeek).fill(0);
                    
                    const today = new Date();
                    today.setHours(0,0,0,0);
                    
                    const daysArr = [];
                    for (let i = 1; i <= totalDays; i++) {
                        const curDate = new Date(this.calendarYear, this.calendarMonth, i);
                        const dateStr = `${this.calendarYear}-${String(this.calendarMonth+1).padStart(2, '0')}-${String(i).padStart(2, '0')}`;
                        
                        let isBooked = false;
                        if (this.bookedDatesArr && this.bookedDatesArr.length > 0) {
                            for (const b of this.bookedDatesArr) {
                                const bIn = new Date(b.check_in);
                                bIn.setHours(0,0,0,0);
                                const bOut = new Date(b.check_out);
                                bOut.setHours(0,0,0,0);
                                
                                if (curDate >= bIn && curDate < bOut) {
                                    isBooked = true;
                                    break;
                                }
                            }
                        }

                        daysArr.push({
                            dayNum: i,
                            dateStr: dateStr,
                            isPast: curDate < today,
                            isBooked: isBooked
                        });
                    }
                    this.calendarDays = daysArr;
                },

                calendarMonthLabel() {
                    const months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                    return months[this.calendarMonth] + ' ' + this.calendarYear;
                },

                calendarNextMonth() {
                    if (this.calendarMonth === 11) {
                        this.calendarMonth = 0;
                        this.calendarYear++;
                    } else {
                        this.calendarMonth++;
                    }
                    this.buildCalendar();
                },

                calendarPrevMonth() {
                    if (this.calendarMonth === 0) {
                        this.calendarMonth = 11;
                        this.calendarYear--;
                    } else {
                        this.calendarMonth--;
                    }
                    this.buildCalendar();
                },

                selectCalendarDate(dateStr) {
                    if (!this.checkInDate || (this.checkInDate && this.checkOutDate)) {
                        this.checkInDate = dateStr;
                        this.checkOutDate = null;
                        this.addToast('Check-in Dipilih', `Tanggal check-in diatur ke ${this.formatIndoDate(dateStr)}.`, 'info');
                    } else {
                        const checkInVal = new Date(this.checkInDate);
                        const checkOutVal = new Date(dateStr);
                        
                        if (checkOutVal > checkInVal) {
                            // Cek tabrakan dengan rentang booking yang sudah ada
                            let overlap = false;
                            if (this.bookedDatesArr && this.bookedDatesArr.length > 0) {
                                for (const b of this.bookedDatesArr) {
                                    const bIn = new Date(b.check_in);
                                    bIn.setHours(0,0,0,0);
                                    const bOut = new Date(b.check_out);
                                    bOut.setHours(0,0,0,0);
                                    
                                    // Overlap terjadi jika checkIn kita < checkOut dia DAN checkOut kita > checkIn dia
                                    if (checkInVal < bOut && checkOutVal > bIn) {
                                        overlap = true;
                                        break;
                                    }
                                }
                            }
                            
                            if (overlap) {
                                this.addToast('Tanggal Bertabrakan', 'Rentang tanggal yang Anda pilih bertabrakan dengan pesanan yang sudah ada.', 'error');
                                this.checkInDate = dateStr; // Reset checkin to selected date
                                this.checkOutDate = null;
                            } else {
                                this.checkOutDate = dateStr;
                                this.addToast('Check-out Dipilih', `Durasi: ${this.calculateNights()} ${this.selectedFacility.unit === 'day' ? 'hari' : 'malam'}.`, 'success');
                            }
                        } else {
                            this.checkInDate = dateStr;
                            this.checkOutDate = null;
                            this.addToast('Check-in Diubah', `Tanggal check-in diatur ke ${this.formatIndoDate(dateStr)}.`, 'info');
                        }
                    }
                },

                isDateSelected(dateStr) {
                    return this.checkInDate === dateStr || this.checkOutDate === dateStr;
                },

                isDateInRange(dateStr) {
                    if (!this.checkInDate || !this.checkOutDate) return false;
                    const cur = new Date(dateStr);
                    const checkIn = new Date(this.checkInDate);
                    const checkOut = new Date(this.checkOutDate);
                    return cur > checkIn && cur < checkOut;
                },

                formatRupiah(amount) {
                    if (amount === undefined || amount === null) return 'Rp 0';
                    return 'Rp ' + amount.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
                },

                formatIndoDate(dateStr) {
                    if (!dateStr) return 'Pilih Tanggal';
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
