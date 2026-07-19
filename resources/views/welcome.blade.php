<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Portal Layanan Wisma DPR RI - Resmi & Terintegrasi</title>

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

    <style>
        .glass-panel {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(0, 0, 0, 0.08);
        }
        .glass-card {
            background: rgba(255, 255, 255, 0.75);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(0, 0, 0, 0.06);
            transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .glass-card:hover {
            background: rgba(255, 255, 255, 0.9);
            border-color: rgba(229, 169, 60, 0.4);
            transform: translateY(-6px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.08);
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 font-sans min-h-screen flex flex-col justify-between relative overflow-x-hidden">

    <!-- Background Wallpaper -->
    <div class="absolute inset-0 z-0">
        <img class="w-full h-full object-cover" 
             src="/images/wisma_dpr.jpg">
    </div>

    <!-- Header Logo & Nav -->
    <header class="relative z-10 w-full px-8 lg:px-16 py-6 flex items-center justify-between border-b border-slate-200 bg-white/60 backdrop-blur-md">
        <div class="flex items-center gap-3">
            <img src="/images/logo.png" class="h-10 w-auto object-contain rounded-xl" alt="Logo Wisma DPR RI">
            <div>
                <h2 class="font-outfit font-bold text-base tracking-wider leading-none text-slate-900">Wisma DPR RI</h2>
                <span class="text-[9px] text-slate-500 font-medium uppercase tracking-widest">Government Hospitality</span>
            </div>
        </div>
        <div class="flex items-center gap-6 text-xs text-slate-600 font-semibold">
            <span class="px-3 py-1 bg-amber-500/10 text-amber-700 font-bold uppercase tracking-wider rounded-full border border-wisma-gold/20 flex items-center gap-1.5">
                <i data-lucide="shield-check" class="w-3 h-3"></i> Prototype v2.2
            </span>
        </div>
    </header>

    <!-- Main Content Hero & Cards -->
    <main class="relative z-10 w-full max-w-7xl mx-auto px-6 lg:px-16 py-12 flex-1 flex flex-col justify-center space-y-12">
        
        <!-- Hero Title -->
        <div class="text-center space-y-4 max-w-3xl mx-auto">
            <span class="text-[10px] text-amber-900 font-extrabold uppercase tracking-widest bg-white/80 backdrop-blur-md px-4 py-1.5 rounded-full border border-wisma-gold/40 inline-block shadow-sm">
                Sistem Terpadu Pelayanan Wisma
            </span>
            <h1 class="text-3xl lg:text-5xl font-outfit font-extrabold text-slate-900 tracking-tight leading-none" style="text-shadow: -2px -2px 0 #fff, 2px -2px 0 #fff, -2px 2px 0 #fff, 2px 2px 0 #fff, 0 0 8px #fff;">
                Portal Pelayanan Digital Wisma DPR RI
            </h1>
            <p class="text-xs lg:text-sm text-slate-900 font-semibold leading-relaxed max-w-xl mx-auto" style="text-shadow: -1.5px -1.5px 0 #fff, 1.5px -1.5px 0 #fff, -1.5px 1.5px 0 #fff, 1.5px 1.5px 0 #fff, 0 0 6px #fff;">
                Integrasi penuh hospitality kenegaraan. Temukan reservasi yang nyaman untuk tamu delegasi, layanan front-desk responsif, serta kontrol manajemen backend yang transparan.
            </p>
        </div>

        <!-- Cards Navigation Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 max-w-7xl mx-auto w-full pt-4">
            
            <!-- PORTAL TAMU -->
            <div class="glass-card rounded-3xl p-8 flex flex-col justify-between h-96 group">
                <div class="space-y-4">
                    <div class="w-12 h-12 bg-amber-500/10 text-wisma-gold rounded-2xl flex items-center justify-center border border-wisma-gold/20">
                        <i data-lucide="user" class="w-6 h-6"></i>
                    </div>
                    <h3 class="text-lg font-bold text-slate-900 font-outfit min-h-[3rem] flex items-end">Portal Tamu</h3>
                    <p class="text-xs text-slate-600 leading-relaxed font-light">
                        Pintu masuk untuk tamu DPR RI. Lakukan pemesanan bungalow wisma, download boarding pass digital, beri rating, dan kirim tiket keluhan pelayanan.
                    </p>
                    <ul class="text-[10px] text-slate-500 space-y-1.5 pt-2">
                        <li class="flex items-center gap-1.5"><i data-lucide="check" class="w-3.5 h-3.5 text-wisma-gold"></i> Booking & E-Payment</li>
                        <li class="flex items-center gap-1.5"><i data-lucide="check" class="w-3.5 h-3.5 text-wisma-gold"></i> Boarding Pass PDF</li>
                        <li class="flex items-center gap-1.5"><i data-lucide="check" class="w-3.5 h-3.5 text-wisma-gold"></i> Input Keluhan Bungalow</li>
                    </ul>
                </div>
                <a href="/guest" class="w-full py-3 bg-slate-900 hover:bg-slate-800 text-white font-extrabold text-xs rounded-xl shadow-lg transition-colors flex items-center justify-center gap-2 group-hover:bg-wisma-gold group-hover:text-wisma-dark">
                    Portal Tamu <i data-lucide="arrow-right" class="w-4 h-4"></i>
                </a>
            </div>

            <!-- PORTAL RESEPSIONIS -->
            <div class="glass-card rounded-3xl p-8 flex flex-col justify-between h-96 group">
                <div class="space-y-4">
                    <div class="w-12 h-12 bg-emerald-500/10 text-emerald-600 rounded-2xl flex items-center justify-center border border-emerald-500/20">
                        <i data-lucide="concierge-bell" class="w-6 h-6"></i>
                    </div>
                    <h3 class="text-lg font-bold text-slate-900 font-outfit min-h-[3rem] flex items-end">Portal Resepsionis</h3>
                    <p class="text-xs text-slate-600 leading-relaxed font-light">
                        Portal operasional Front Office. Kelola kedatangan & check-in tamu kenegaraan, update status check-out instan, serta monitoring ketersediaan bungalow secara real-time.
                    </p>
                    <ul class="text-[10px] text-slate-500 space-y-1.5 pt-2">
                        <li class="flex items-center gap-1.5"><i data-lucide="check" class="w-3.5 h-3.5 text-emerald-600"></i> Proses Check-In Instan</li>
                        <li class="flex items-center gap-1.5"><i data-lucide="check" class="w-3.5 h-3.5 text-emerald-600"></i> Proses Check-Out & Release</li>
                        <li class="flex items-center gap-1.5"><i data-lucide="check" class="w-3.5 h-3.5 text-emerald-600"></i> Monitoring Status Hunian</li>
                    </ul>
                </div>
                <a href="/receptionist" class="w-full py-3 bg-slate-900 hover:bg-slate-800 text-white font-extrabold text-xs rounded-xl shadow-lg transition-colors flex items-center justify-center gap-2 group-hover:bg-emerald-500 group-hover:text-white">
                    Portal Resepsionis <i data-lucide="arrow-right" class="w-4 h-4"></i>
                </a>
            </div>

            <!-- PORTAL CUSTOMER SERVICE -->
            <div class="glass-card rounded-3xl p-8 flex flex-col justify-between h-96 group">
                <div class="space-y-4">
                    <div class="w-12 h-12 bg-rose-500/10 text-rose-600 rounded-2xl flex items-center justify-center border border-rose-500/20">
                        <i data-lucide="life-buoy" class="w-6 h-6"></i>
                    </div>
                    <h3 class="text-lg font-bold text-slate-900 font-outfit min-h-[3rem] flex items-end">Portal Customer Service</h3>
                    <p class="text-xs text-slate-600 leading-relaxed font-light">
                        Portal pelayanan keluhan & hubungan tamu. Catat laporan kerusakan, keluhan fasilitas bungalow, laundry, makanan, internet, serta penugasan tim teknis secara real-time.
                    </p>
                    <ul class="text-[10px] text-slate-500 space-y-1.5 pt-2">
                        <li class="flex items-center gap-1.5"><i data-lucide="check" class="w-3.5 h-3.5 text-rose-600"></i> Pencatatan Keluhan Masuk</li>
                        <li class="flex items-center gap-1.5"><i data-lucide="check" class="w-3.5 h-3.5 text-rose-600"></i> Penugasan Tim Teknis</li>
                        <li class="flex items-center gap-1.5"><i data-lucide="check" class="w-3.5 h-3.5 text-rose-600"></i> Pemantauan Status Resolusi</li>
                    </ul>
                </div>
                <a href="/customer-service" class="w-full py-3 bg-slate-900 hover:bg-slate-800 text-white font-extrabold text-xs rounded-xl shadow-lg transition-colors flex items-center justify-center gap-2 group-hover:bg-rose-500 group-hover:text-white">
                    Portal Customer Service <i data-lucide="arrow-right" class="w-4 h-4"></i>
                </a>
            </div>

            <!-- PORTAL KOORDINATOR WISMA -->
            <div class="glass-card rounded-3xl p-8 flex flex-col justify-between h-96 group">
                <div class="space-y-4">
                    <div class="w-12 h-12 bg-indigo-500/10 text-indigo-600 rounded-2xl flex items-center justify-center border border-indigo-500/20">
                        <i data-lucide="shield" class="w-6 h-6"></i>
                    </div>
                    <h3 class="text-lg font-bold text-slate-900 font-outfit min-h-[3rem] flex items-end">Portal Koordinator Wisma</h3>
                    <p class="text-xs text-slate-600 leading-relaxed font-light">
                        Pusat kendali inventaris & manajemen. Lakukan input data CRUD bungalow wisma, audit daftar tamu terdaftar, riwayat log transaksi, serta monitoring occupancy.
                    </p>
                    <ul class="text-[10px] text-slate-500 space-y-1.5 pt-2">
                        <li class="flex items-center gap-1.5"><i data-lucide="check" class="w-3.5 h-3.5 text-indigo-400"></i> CRUD Bungalow Wisma</li>
                        <li class="flex items-center gap-1.5"><i data-lucide="check" class="w-3.5 h-3.5 text-indigo-400"></i> Audit Log Pemesanan DIPA</li>
                        <li class="flex items-center gap-1.5"><i data-lucide="check" class="w-3.5 h-3.5 text-indigo-400"></i> Laporan Hunian & Okupansi</li>
                    </ul>
                </div>
                <a href="/admin" class="w-full py-3 bg-slate-900 hover:bg-slate-800 text-white font-extrabold text-xs rounded-xl shadow-lg transition-colors flex items-center justify-center gap-2 group-hover:bg-indigo-500 group-hover:text-white">
                    Portal Koordinator Wisma <i data-lucide="arrow-right" class="w-4 h-4"></i>
                </a>
            </div>

        </div>

    </main>

    <!-- Footer Copyright -->
    <footer class="relative z-10 w-full px-8 py-6 text-center border-t border-slate-200 bg-white/60 backdrop-blur-md">
        <p class="text-[10px] text-slate-500">
            © 2026 Sekretariat Jenderal DPR RI. Biro Umum & Hospitality Kenegaraan. Semua Hak Dilindungi.
        </p>
    </footer>

    <!-- Lucide Icon Generator -->
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            if (window.lucide) {
                window.lucide.createIcons();
            }
        });
    </script>
</body>
</html>
