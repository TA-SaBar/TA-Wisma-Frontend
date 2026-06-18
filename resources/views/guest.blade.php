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
                 src="https://images.unsplash.com/photo-1540555700478-4be289fbecef?auto=format&fit=crop&w=1200&q=80">
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
                    <button @click="showFeatureMuted('Notifikasi')" class="w-10 h-10 rounded-xl bg-slate-50 border border-slate-100 flex items-center justify-center text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition-colors relative">
                        <i data-lucide="bell" class="w-5 h-5"></i>
                        <span class="absolute top-2.5 right-2.5 w-2 h-2 bg-red-500 rounded-full border border-white"></span>
                    </button>
                    
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
                                                <span x-text="b.nights + ' Malam'"></span>
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
                        <h1 class="text-2xl font-outfit font-extrabold text-slate-900">Katalog & Booking Kamar / Ruang Rapat</h1>
                        <p class="text-xs text-slate-500">Jelajahi dan pesan inventaris fasilitas wisma yang tersedia.</p>
                    </div>

                    <!-- Filters Bar -->
                    <div class="bg-white p-4 rounded-2xl border border-slate-100 shadow-sm grid grid-cols-1 md:grid-cols-5 gap-4 items-center">
                        <div>
                            <label class="text-[10px] text-slate-500 font-bold block mb-1 uppercase tracking-wide">Gedung / Wing</label>
                            <select x-model="filterGedung" class="w-full text-xs bg-slate-50 border border-slate-200 rounded-lg p-2 focus:ring-1 focus:ring-wisma-gold focus:outline-none">
                                <option value="">Semua Gedung</option>
                                <option value="Gedung Utama">Gedung Utama</option>
                                <option value="Wing A">Wing A</option>
                                <option value="Wing B">Wing B</option>
                                <option value="Wing VVIP">Wing VVIP</option>
                            </select>
                        </div>
                        <div>
                            <label class="text-[10px] text-slate-500 font-bold block mb-1 uppercase tracking-wide">Lantai</label>
                            <select x-model="filterLantai" class="w-full text-xs bg-slate-50 border border-slate-200 rounded-lg p-2 focus:ring-1 focus:ring-wisma-gold focus:outline-none">
                                <option value="">Semua Lantai</option>
                                <option value="Ground Floor">Ground Floor</option>
                                <option value="Lantai 2">Lantai 2</option>
                                <option value="Lantai 3">Lantai 3</option>
                                <option value="Lantai 5">Lantai 5</option>
                                <option value="Lantai 12">Lantai 12</option>
                                <option value="Lantai 15">Lantai 15</option>
                            </select>
                        </div>
                        <div>
                            <label class="text-[10px] text-slate-500 font-bold block mb-1 uppercase tracking-wide">Tipe Fasilitas</label>
                            <select x-model="filterTipe" class="w-full text-xs bg-slate-50 border border-slate-200 rounded-lg p-2 focus:ring-1 focus:ring-wisma-gold focus:outline-none">
                                <option value="">Semua Tipe</option>
                                <option value="Kamar">Kamar</option>
                                <option value="Ruang Rapat">Ruang Rapat</option>
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
                                            <span class="text-[9px] text-slate-400 font-bold uppercase tracking-wide block mt-0.5" x-text="'per ' + (f.unit === 'night' ? 'Malam' : f.unit)"></p>
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
                                    <button @click="if(!day.isPast) selectCalendarDate(day.dateStr)" 
                                            :disabled="day.isPast"
                                            class="py-2.5 rounded-xl font-bold transition-all relative flex flex-col items-center justify-center"
                                            :class="{
                                                'text-slate-300 cursor-not-allowed': day.isPast,
                                                'bg-wisma-navy text-wisma-gold font-extrabold shadow-md': isDateSelected(day.dateStr),
                                                'bg-indigo-50 text-indigo-700': isDateInRange(day.dateStr),
                                                'hover:bg-slate-100 text-slate-700': !day.isPast && !isDateSelected(day.dateStr) && !isDateInRange(day.dateStr)
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
                                    <span class="text-slate-500">Durasi Masa Inap</span>
                                    <span class="font-semibold text-slate-900" x-text="calculateNights() + ' Malam'"></span>
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
                                    <span class="text-slate-500" x-text="formatRupiah(selectedFacility.price) + ' x ' + calculateNights() + ' Malam'"></span>
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

                    <!-- STEP 3: VIRTUAL ACCOUNT / CC PAYMENT -->
                    <div x-show="wizardStep === 3" class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                        <div class="bg-white border border-slate-100 rounded-3xl p-6 shadow-sm lg:col-span-2 space-y-6">
                            <div class="flex justify-between items-center pb-3 border-b border-slate-100">
                                <h3 class="text-sm font-bold text-slate-900">Metode Pembayaran Kedinasan</h3>
                                <div class="flex items-center gap-1.5 text-red-500 font-bold text-xs">
                                    <i data-lucide="clock" class="w-4 h-4"></i>
                                    <span x-text="paymentTimer"></span>
                                </div>
                            </div>

                            <!-- Payment Choice -->
                            <div class="flex bg-slate-100 p-1 rounded-xl w-full select-none">
                                <button type="button" 
                                        @click="paymentMethod = 'va'"
                                        class="flex-1 py-2 text-xs font-bold rounded-lg transition-all flex items-center justify-center gap-1.5"
                                        :class="paymentMethod === 'va' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-500 hover:text-slate-800'">
                                    Virtual Account
                                </button>
                                <button type="button" 
                                        @click="paymentMethod = 'cc'"
                                        class="flex-1 py-2 text-xs font-bold rounded-lg transition-all flex items-center justify-center gap-1.5"
                                        :class="paymentMethod === 'cc' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-500 hover:text-slate-800'">
                                    Kartu Kredit Korporat
                                </button>
                            </div>

                            <!-- VA Section -->
                            <div x-show="paymentMethod === 'va'" class="space-y-4">
                                <div class="p-4 bg-amber-50/50 border border-amber-200/50 rounded-2xl flex items-center gap-3">
                                    <i data-lucide="info" class="w-5 h-5 text-amber-500"></i>
                                    <p class="text-[11px] text-slate-600 leading-normal">Nomor Virtual Account ini terhubung langsung dengan sistem e-Budgeting DIPA Setjen DPR RI.</p>
                                </div>
                                <div class="p-6 bg-slate-50 border border-slate-100 rounded-2xl flex items-center justify-between">
                                    <div>
                                        <span class="text-[9px] text-slate-400 block font-bold uppercase tracking-wide">Bank Mandiri (DPR Channel)</span>
                                        <p class="text-base font-extrabold text-slate-900 tracking-wider mt-1">8807 1989 0412 2015</p>
                                    </div>
                                    <button @click="copyVA()" class="px-3 py-1.5 bg-white border border-slate-200 rounded-lg text-slate-600 hover:text-slate-900 hover:bg-slate-50 text-xs font-bold transition-all flex items-center gap-1">
                                        <i data-lucide="copy" class="w-3.5 h-3.5"></i> Salin
                                    </button>
                                </div>
                            </div>

                            <!-- Credit Card Section -->
                            <div x-show="paymentMethod === 'cc'" class="space-y-4">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div class="space-y-1">
                                        <label class="text-[10px] text-slate-500 font-bold uppercase tracking-wide block">Nama Pemegang Kartu</label>
                                        <input type="text" x-model="ccName" placeholder="Contoh: BUDI SANTOSO" class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl p-3 focus:ring-1 focus:ring-wisma-gold focus:outline-none focus:bg-white transition-all">
                                    </div>
                                    <div class="space-y-1">
                                        <label class="text-[10px] text-slate-500 font-bold uppercase tracking-wide block">Nomor Kartu</label>
                                        <input type="text" x-model="ccNumber" placeholder="4111 2222 3333 4444" class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl p-3 focus:ring-1 focus:ring-wisma-gold focus:outline-none focus:bg-white transition-all">
                                    </div>
                                </div>
                                <div class="grid grid-cols-2 gap-4">
                                    <div class="space-y-1">
                                        <label class="text-[10px] text-slate-500 font-bold uppercase tracking-wide block">Masa Berlaku</label>
                                        <input type="text" x-model="ccExpiry" placeholder="MM/YY" class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl p-3 focus:ring-1 focus:ring-wisma-gold focus:outline-none focus:bg-white transition-all">
                                    </div>
                                    <div class="space-y-1">
                                        <label class="text-[10px] text-slate-500 font-bold uppercase tracking-wide block">CVV</label>
                                        <input type="text" x-model="ccCvv" placeholder="123" class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl p-3 focus:ring-1 focus:ring-wisma-gold focus:outline-none focus:bg-white transition-all">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Summary Column Right -->
                        <div class="bg-white border border-slate-100 rounded-3xl p-6 shadow-sm space-y-4">
                            <h3 class="text-sm font-bold text-slate-900">Total Pembayaran</h3>
                            <div class="space-y-2.5 text-xs">
                                <div class="flex justify-between font-bold text-slate-900">
                                    <span>Total Pembayaran</span>
                                    <span class="text-wisma-navy text-sm font-extrabold" x-text="formatRupiah(calculateTotal())"></span>
                                </div>
                            </div>
                            <div class="flex gap-2.5 pt-2">
                                <button @click="proceedToStep(2)" class="flex-1 py-3 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-xl transition-colors">
                                    Kembali
                                </button>
                                <button @click="simulatePaymentProcess()" class="flex-[2] py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl shadow-lg transition-colors">
                                    Simulasi Konfirmasi
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
                        <div class="bg-slate-50 border border-slate-200 rounded-2xl overflow-hidden relative">
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
                                        <p class="font-bold text-slate-800" x-text="generatedTicket.id"></p>
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
                                        <p class="font-bold text-slate-800" x-text="generatedTicket.nights + ' Malam'"></p>
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
                                                          'bg-emerald-100 text-emerald-700': b.status === 'Lunas',
                                                          'bg-blue-100 text-blue-700': b.status === 'Check In',
                                                          'bg-slate-100 text-slate-600': b.status === 'Selesai'
                                                      }"
                                                      x-text="b.status === 'Check In' ? 'Aktif Menginap' : b.status"></span>
                                            </div>
                                            <p class="text-xs text-slate-500 mt-1 flex items-center gap-1.5 flex-wrap">
                                                <span x-text="b.id"></span>
                                                <span class="text-slate-300">•</span>
                                                <i data-lucide="calendar" class="w-3.5 h-3.5"></i>
                                                <span x-text="formatIndoDate(b.check_in) + ' - ' + formatIndoDate(b.check_out)"></span>
                                                <span class="text-slate-300">•</span>
                                                <span x-text="b.nights + ' Malam'"></span>
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
                                            <button @click="viewTicket(b)" class="px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-xl transition-all flex items-center gap-1">
                                                <i data-lucide="ticket" class="w-3.5 h-3.5"></i> Tiket
                                            </button>
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

                    <div class="grid grid-cols-2 gap-4">
                        <div class="bg-slate-50 p-3.5 rounded-2xl border border-slate-100 flex items-center gap-3">
                            <i data-lucide="users" class="w-5 h-5 text-slate-500"></i>
                            <div>
                                <span class="text-[9px] text-slate-400 block font-bold uppercase tracking-wide">Kapasitas</span>
                                <p class="text-xs font-bold text-slate-800" x-text="drawerFacility.capacity + ' Orang'"></p>
                            </div>
                        </div>
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
        function wismaApp() {
            return {
                isLoggedIn: false,
                passwordVisible: false,
                loginForm: {
                    role: 'guest',
                    dprId: '1989041220',
                    password: 'password123'
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
                checkInDate: '2026-06-20',
                checkOutDate: '2026-06-22',
                guestCount: 2,
                bookingForm: {
                    nama: 'Budi Santoso',
                    nip: '198904122015031002',
                    whatsapp: '+62 812-3456-7890',
                    email: 'budi.santoso@dpr.go.id',
                    untukOrangLain: false
                },
                paymentMethod: 'va',
                ccName: '',
                ccNumber: '',
                ccExpiry: '',
                ccCvv: '',
                paymentTimer: '29:59',
                timerInterval: null,
                
                toasts: [],
                toastCount: 0,
                generatedTicket: {},
                
                calendarYear: 2026,
                calendarMonth: 5,
                calendarDays: [],
                calendarBlanks: [],

                profile: {
                    role: 'guest',
                    nama: 'Budi Santoso',
                    nip: '198904122015031002',
                    whatsapp: '+62 812-3456-7890',
                    email: 'budi.santoso@dpr.go.id',
                    instansi: 'Sekretariat Jenderal DPR RI',
                    role_label: 'Anggota Kehormatan'
                },

                // Shared LocalStorage data collections
                facilities: [],
                bookings: [],
                guests: [],
                complaints: [],

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

                initApp() {
                    this.loadState();
                    this.selectedFacility = this.facilities[0] || {};
                    this.buildCalendar();
                    setTimeout(() => {
                        if (window.lucide) {
                            window.lucide.createIcons();
                        }
                    }, 100);
                },

                login() {
                    if (!this.loginForm.dprId || !this.loginForm.password) {
                        this.addToast('Data Tidak Lengkap', 'DPR ID dan Password tidak boleh kosong.', 'error');
                        return;
                    }
                    
                    this.isLoggedIn = true;
                    this.profile.role = 'guest';
                    this.profile.role_label = 'Anggota Kehormatan';
                    this.profile.nama = 'Budi Santoso';
                    this.profile.instansi = 'Sekretariat Jenderal DPR RI';
                    this.currentTab = 'dashboard';
                    
                    this.addToast('Login Berhasil', `Selamat datang di Portal Tamu Wisma, ${this.profile.nama}.`, 'success');
                    
                    setTimeout(() => {
                        if (window.lucide) {
                            window.lucide.createIcons();
                        }
                    }, 50);
                },

                logout() {
                    this.isLoggedIn = false;
                    this.loginForm.dprId = '1989041220';
                    this.loginForm.password = 'password123';
                    this.addToast('Logout Sukses', 'Anda telah keluar dari sesi portal tamu.', 'info');
                    
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
                    localStorage.setItem('wisma_complaints', JSON.stringify(this.complaints));
                },

                loadState() {
                    const savedFacilities = localStorage.getItem('wisma_facilities');
                    const savedBookings = localStorage.getItem('wisma_bookings');
                    const savedGuests = localStorage.getItem('wisma_guests');
                    const savedComplaints = localStorage.getItem('wisma_complaints');
                    
                    // Fallback to defaults if not exists in localStorage
                    if (savedFacilities) {
                        this.facilities = JSON.parse(savedFacilities);
                    } else {
                        this.facilities = [
                            {
                                id: 1,
                                name: 'VIP Suite Nusantara',
                                type: 'Kamar',
                                gedung: 'Wing A',
                                lantai: 'Lantai 12',
                                capacity: 2,
                                price: 2500000,
                                unit: 'night',
                                luas: '45 m²',
                                bed: 'King Size',
                                status: 'READY',
                                photo: 'https://images.unsplash.com/photo-1590490360182-c33d57733427?auto=format&fit=crop&w=600&q=80',
                                description: 'Fasilitas utama untuk tamu kenegaraan tingkat tinggi dengan desain mewah, ruang lounge pribadi, kamar mandi marmer berpemanas, dan pemandangan panorama kota Jakarta.'
                            },
                            {
                                id: 2,
                                name: 'Ruang Rapat Nusantara III',
                                type: 'Ruang Rapat',
                                gedung: 'Gedung Utama',
                                lantai: 'Lantai 2',
                                capacity: 25,
                                price: 1200000,
                                unit: '4 jam',
                                luas: '80 m²',
                                bed: 'Conference Table',
                                status: 'CLEANING',
                                photo: 'https://images.unsplash.com/photo-1517502884422-41eaaced0168?auto=format&fit=crop&w=600&q=80',
                                description: 'Ruang rapat medium dengan sistem audio-visual terintegrasi, layar proyeksi otomatis, mikrofon konferensi nirkabel, dan layanan asisten rapat siap sedia.'
                            },
                            {
                                id: 3,
                                name: 'Auditorium Sasana Bhakti',
                                type: 'Ruang Rapat',
                                gedung: 'Gedung Utama',
                                lantai: 'Ground Floor',
                                capacity: 500,
                                price: 10000000,
                                unit: 'day',
                                luas: '600 m²',
                                bed: 'Theater Seating',
                                status: 'MAINTENANCE',
                                photo: 'https://images.unsplash.com/photo-1503095396549-807759245b35?auto=format&fit=crop&w=600&q=80',
                                description: 'Balai serbaguna berkapasitas besar untuk acara formal, pelantikan, seminar internasional, atau pameran seni. Dilengkapi akustik profesional dan pencahayaan panggung lengkap.'
                            },
                            {
                                id: 4,
                                name: 'Executive Suite - Wing A',
                                type: 'Kamar',
                                gedung: 'Wing A',
                                lantai: 'Lantai 5',
                                capacity: 2,
                                price: 1250000,
                                unit: 'night',
                                luas: '40 m²',
                                bed: 'King Size',
                                status: 'READY',
                                photo: 'https://images.unsplash.com/photo-1618773928121-c32242e63f39?auto=format&fit=crop&w=600&q=80',
                                description: 'Suite Eksekutif dirancang khusus untuk memenuhi standar kenyamanan pejabat negara dan tamu penting. Memiliki ruang kerja luas terpisah, smart home system, dan lounge bar mini.'
                            },
                            {
                                id: 5,
                                name: 'Superior Room - Wing B',
                                type: 'Kamar',
                                gedung: 'Wing B',
                                lantai: 'Lantai 3',
                                capacity: 2,
                                price: 1050000,
                                unit: 'night',
                                luas: '32 m²',
                                bed: 'Queen Size',
                                status: 'READY',
                                photo: 'https://images.unsplash.com/photo-1566665797739-1674de7a421a?auto=format&fit=crop&w=600&q=80',
                                description: 'Kamar superior bernuansa modern minimalis dengan fasilitas lengkap, kasur berkualitas tinggi, meja kerja ergonomis, dan akses Wi-Fi berkecepatan tinggi.'
                            }
                        ];
                        localStorage.setItem('wisma_facilities', JSON.stringify(this.facilities));
                    }

                    if (savedBookings) {
                        this.bookings = JSON.parse(savedBookings);
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
                                hasFeedback: false,
                                rating: 0
                            }
                        ];
                        localStorage.setItem('wisma_bookings', JSON.stringify(this.bookings));
                    }

                    if (savedGuests) {
                        this.guests = JSON.parse(savedGuests);
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

                    if (savedComplaints) {
                        this.complaints = JSON.parse(savedComplaints);
                    } else {
                        this.complaints = [
                            {
                                id: 'COMP-101',
                                title: 'AC Kamar 402 Tidak Dingin',
                                category: 'Fasilitas (Kamar, Gedung)',
                                category_slug: 'facility',
                                location: 'Kamar 402',
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

                startBookingFlow(facility) {
                    this.selectedFacility = facility;
                    this.drawerOpen = false;
                    this.wizardStep = 1;
                    this.switchTab('booking_wizard');
                },

                proceedToStep(step) {
                    if (step === 2) {
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

                simulatePaymentProcess() {
                    if (this.paymentMethod === 'cc' && (!this.ccName || !this.ccNumber || !this.ccExpiry || !this.ccCvv)) {
                        this.addToast('Detail Kartu Tidak Lengkap', 'Harap lengkapi informasi kartu kredit Anda.', 'error');
                        return;
                    }

                    clearInterval(this.timerInterval);
                    this.addToast('Memproses Otorisasi', 'Menghubungkan ke server e-Budgeting Setjen DPR...', 'info');

                    setTimeout(() => {
                        const newBookingId = 'WDPR-2026-' + String(Math.floor(1000 + Math.random() * 9000));
                        const totalNights = this.calculateNights();
                        
                        this.generatedTicket = {
                            id: newBookingId,
                            unit_id: this.selectedFacility.id,
                            unit_name: this.selectedFacility.name,
                            unit_photo: this.selectedFacility.photo,
                            unit_location: `${this.selectedFacility.gedung} • ${this.selectedFacility.lantai}`,
                            check_in: this.checkInDate,
                            check_out: this.checkOutDate,
                            nights: totalNights,
                            total_price: this.calculateTotal(),
                            status: 'Lunas',
                            nama: this.bookingForm.untukOrangLain ? 'Tamu Delegasi: ' + this.bookingForm.nama : this.bookingForm.nama,
                            nip: this.bookingForm.nip,
                            whatsapp: this.bookingForm.whatsapp,
                            email: this.bookingForm.email,
                            hasFeedback: false,
                            rating: 0
                        };

                        this.bookings.unshift(this.generatedTicket);

                        // Dynamic guest registration
                        const guestNip = this.bookingForm.nip;
                        const existingGuestIdx = this.guests.findIndex(g => g.nip === guestNip);
                        if (existingGuestIdx === -1) {
                            this.guests.push({
                                id: 'T-2026-' + String(Math.floor(100 + Math.random() * 900)),
                                nama: this.bookingForm.nama,
                                nip: guestNip,
                                phone: this.bookingForm.whatsapp,
                                email: this.bookingForm.email,
                                status: 'Reguler',
                                kunjungan: 1,
                                terakhir: 'Hari ini (Booking)'
                            });
                        } else {
                            this.guests[existingGuestIdx].kunjungan += 1;
                            this.guests[existingGuestIdx].terakhir = 'Hari ini (Booking)';
                        }
                        
                        this.persistState();
                        
                        this.wizardStep = 4;
                        this.addToast('Pembayaran Sukses!', 'Reservasi Anda telah terkonfirmasi oleh sistem DIPA.', 'success');
                        
                        setTimeout(() => {
                            if (window.lucide) {
                                window.lucide.createIcons();
                            }
                        }, 50);
                    }, 1500);
                },

                saveProfile() {
                    this.addToast('Profil Disimpan', 'Data diri Anda berhasil diperbarui.', 'success');
                },

                downloadPDF() {
                    this.addToast('Mengunduh Tiket', 'Boarding pass PDF berhasil diunduh.', 'success');
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

                submitRating() {
                    const avg = (this.feedbackRating.cleanliness + this.feedbackRating.facilities + this.feedbackRating.service) / 3;
                    const idx = this.bookings.findIndex(b => b.id === this.feedbackBooking.id);
                    if (idx !== -1) {
                        this.bookings[idx].hasFeedback = true;
                        this.bookings[idx].rating = avg;
                    }

                    this.ratingModalOpen = false;
                    this.persistState();
                    this.addToast('Feedback Terkirim', 'Terima kasih atas ulasan Anda untuk meningkatkan layanan kami.', 'success');
                    
                    setTimeout(() => {
                        if (window.lucide) {
                            window.lucide.createIcons();
                        }
                    }, 50);
                },

                submitComplaint() {
                    if (!this.complaintForm.location || !this.complaintForm.description) {
                        this.addToast('Gagal Mengirim', 'Harap isi lokasi dan deskripsi keluhan Anda.', 'error');
                        return;
                    }

                    const categoryNames = {
                        facility: 'Fasilitas (Kamar, Gedung)',
                        laundry: 'Layanan Laundry',
                        internet: 'Internet / Wifi',
                        food: 'Layanan Makanan'
                    };

                    const now = new Date();
                    const hours = String(now.getHours()).padStart(2, '0');
                    const mins = String(now.getMinutes()).padStart(2, '0');
                    const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
                    const dateStr = `${now.getDate()} ${months[now.getMonth()]} ${now.getFullYear()}, ${hours}:${mins}`;

                    const newComplaint = {
                        id: 'COMP-' + String(Math.floor(104 + Math.random() * 800)),
                        title: this.complaintForm.description.length > 30 ? this.complaintForm.description.substring(0, 30) + '...' : this.complaintForm.description,
                        category: categoryNames[this.complaintForm.category],
                        category_slug: this.complaintForm.category,
                        location: this.complaintForm.location,
                        date: dateStr,
                        status: 'Pending'
                    };

                    this.complaints.unshift(newComplaint);
                    this.persistState();

                    this.complaintForm.location = '';
                    this.complaintForm.description = '';

                    this.addToast('Keluhan Terkirim', 'Laporan Anda sudah diterima front office untuk segera ditangani.', 'success');
                    
                    setTimeout(() => {
                        if (window.lucide) {
                            window.lucide.createIcons();
                        }
                    }, 50);
                },

                calculateNights() {
                    const checkIn = new Date(this.checkInDate);
                    const checkOut = new Date(this.checkOutDate);
                    const diffTime = Math.abs(checkOut - checkIn);
                    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
                    return isNaN(diffDays) ? 1 : (diffDays === 0 ? 1 : diffDays);
                },

                calculateTax() {
                    return Math.floor((this.selectedFacility.price * this.calculateNights()) * 0.11);
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
                        
                        daysArr.push({
                            dayNum: i,
                            dateStr: dateStr,
                            isPast: curDate < today
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
                            this.checkOutDate = dateStr;
                            this.addToast('Check-out Dipilih', `Masa inap: ${this.calculateNights()} malam.`, 'success');
                        } else {
                            this.checkInDate = dateStr;
                            this.checkOutDate = null;
                            this.addToast('Check-in Direset', `Tanggal check-in baru diatur ke ${this.formatIndoDate(dateStr)}.`, 'info');
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
