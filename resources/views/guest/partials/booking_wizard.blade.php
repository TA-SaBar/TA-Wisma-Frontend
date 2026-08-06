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
                                                'bg-wisma-navy text-wisma-gold font-extrabold shadow-md': (isDateSelected(day.dateStr) || isDateInRange(day.dateStr)) && !day.isBooked,
                                                'hover:bg-slate-100 text-slate-700': !day.isPast && !day.isBooked && !isDateSelected(day.dateStr) && !isDateInRange(day.dateStr)
                                            }">
                                        <span x-text="day.dayNum"></span>
                                        <span x-show="day.dateStr === checkInDate && checkInDate !== checkOutDate" class="text-[7px] text-wisma-gold absolute bottom-0.5 uppercase tracking-tighter">IN</span>
                                        <span x-show="day.dateStr === checkOutDate && checkInDate !== checkOutDate" class="text-[7px] text-wisma-gold absolute bottom-0.5 uppercase tracking-tighter">OUT</span>
                                        <span x-show="day.dateStr === checkInDate && checkInDate === checkOutDate" class="text-[7px] text-wisma-gold absolute bottom-0.5 uppercase tracking-tighter">IN/OUT</span>
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
                                    <p class="text-[10px] text-slate-400 mt-1" x-text="selectedFacility.area"></p>
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
                                <!-- <div class="flex items-center gap-2 pt-2">
                                    <input type="checkbox" x-model="bookingForm.untukOrangLain" id="untukOrangLain" class="accent-wisma-gold">
                                    <label for="untukOrangLain" class="text-xs text-slate-600 select-none cursor-pointer">Pemesanan diwakilkan untuk orang lain (Delegasi)</label>
                                </div> -->
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
                                <span>Pilih metode pembayaran dalam <span x-text="paymentTimer"></span></span>
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
                                        <span class="text-[9px] text-slate-400 font-bold block uppercase tracking-wider">Kode Booking</span>
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
                                        <span class="text-[9px] text-slate-400 font-bold block uppercase tracking-wider">Unit Fasilitas</span>
                                        <p class="font-bold text-slate-800" x-text="generatedTicket.unit_name"></p>
                                        <p class="text-[9px] text-slate-400 block mt-0.5" x-text="generatedTicket.unit_location"></p>
                                    </div>
                                    <div>
                                        <span class="text-[9px] text-slate-400 font-bold block uppercase tracking-wider">Masa Inap</span>
                                        <p class="font-bold text-slate-800" x-text="generatedTicket.nights + ((generatedTicket.unit_name || '').includes('Rapat') ? ' Hari' : ' Malam')"></p>
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