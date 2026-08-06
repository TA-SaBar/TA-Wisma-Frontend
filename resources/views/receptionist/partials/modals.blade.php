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
