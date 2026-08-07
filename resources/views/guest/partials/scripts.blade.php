<!-- APP SCRIPT STATE MANAGEMENT -->
    <script>
        const API_URL = '{{ env('BACKEND_API_URL', 'http://localhost:8000/api') }}';

        function wismaApp() {
            return {
                sidebarOpen: false,
                isLoggedIn: false,
                isLoading: false,
                getInitials(name) {
                    if (!name) return '?';
                    const parts = name.trim().split(' ');
                    if (parts.length > 1) {
                        return (parts[0][0] + parts[parts.length - 1][0]).toUpperCase();
                    }
                    return (parts[0][0] || '?').toUpperCase();
                },
                passwordVisible: false,
                currentTime: new Date(),
                loginForm: {
                    role: 'guest',
                    email: 'budi.santoso@dpr.go.id',
                    password: 'password'
                },

                currentTab: 'dashboard',
                searchQuery: '',
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
                get unreadNotificationCount() { return this.notifications.filter(x => !x.is_read && !x.read).length; },
                notificationsOpen: false,

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
                            if (me.data.role !== 'guest') {
                                window.location.href = '/' + (me.data.role === 'koordinator_wisma' ? 'admin' : me.data.role);
                                return;
                            }
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
                                photo: f.photo ? (f.photo.startsWith('http') ? f.photo : (f.photo.startsWith('/storage') ? API_URL.replace(/\/api$/, '') + f.photo : f.photo)) : '/images/webp/bungalow_buah.webp'
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
                    
                },

                async loadBookingsFromApi() {
                    try {
                        const res = await this.apiCall('GET', '/bookings');
                        if (res.success) {
                            this.bookings = res.data.map(b => ({
                                id: b.id,
                                booking_code: b.booking_code,
                                unit_name: b.facility.name,
                                unit_photo: b.facility.photo ? (b.facility.photo.startsWith('http') ? b.facility.photo : (b.facility.photo.startsWith('/storage') ? API_URL.replace(/\/api$/, '') + b.facility.photo : b.facility.photo)) : '/images/webp/bungalow_buah.webp',
                                unit_location: b.facility.area,
                                check_in: b.check_in.substring(0,10),
                                check_out: b.check_out.substring(0,10),
                                nights: b.nights,
                                total_price: b.total_price,
                                snap_token: b.snap_token,
                                created_at: b.created_at,
                                status: ((b.status || '').toLowerCase() === 'pending' ? 'Pending' : 
                                        ((b.status || '').toLowerCase() === 'lunas' ? 'Lunas' : 
                                        ((b.status || '').toLowerCase() === 'check_in' ? 'Check In' : 
                                        ((b.status || '').toLowerCase() === 'cancelled' || (b.status || '').toLowerCase() === 'batal' || (b.status || '').toLowerCase() === 'dibatalkan' ? 'Dibatalkan' : 'Selesai')))),
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
                    if (!this.loginForm.email || !this.loginForm.password) {
                        this.addToast('Data Tidak Lengkap', 'Email dan Password tidak boleh kosong.', 'error');
                        return;
                    }
                    this.isLoading = true;
                    try {
                        const res = await this.apiCall('POST', '/login', {
                            email: this.loginForm.email,
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
                            
                        }
                    } catch (e) {
                        console.error('Gagal memuat notifikasi', e);
                    }
                },

                async deleteNotification(id) {
                    try {
                        const res = await this.apiCall('DELETE', `/notifications/${id}`);
                        if (res.success) {
                            this.notifications = this.notifications.filter(n => n.id !== id);
                            
                            this.addToast('Dihapus', 'Notifikasi berhasil dihapus.', 'success');
                        }
                    } catch (e) {
                        console.error('Gagal menghapus notifikasi', e);
                        this.addToast('Gagal', 'Tidak dapat menghapus notifikasi.', 'error');
                    }
                },

                formatTime(dateString) {
                    if (!dateString) return '';
                    const d = new Date(dateString);
                    const now = new Date();
                    const diff = Math.floor((now - d) / 1000);
                    if (diff < 60) return 'Baru saja';
                    if (diff < 3600) return Math.floor(diff / 60) + 'm lalu';
                    if (diff < 86400) return Math.floor(diff / 3600) + 'j lalu';
                    if (diff < 604800) return Math.floor(diff / 86400) + 'h lalu';
                    return d.toLocaleDateString('id-ID', { day: 'numeric', month: 'short' });
                },
                async markNotificationsRead() {
                    try {
                        const res = await this.apiCall('PUT', '/notifications/read-all');
                        if (res.success) {
                            
                            this.notifications = this.notifications.map(n => ({...n, read: true}));
                        }
                    } catch (e) {
                        console.error('Gagal menandai notifikasi dibaca', e);
                    }
                },

                async handleNotificationClick(notif) {
                    if (!notif.read && !notif.is_read) {
                        try {
                            const res = await this.apiCall('PUT', `/notifications/${notif.id}/read`);
                            if (res.success) {
                                notif.read = true;
                                notif.is_read = true;
                            }
                        } catch(e) {}
                    }
                    const historyTypes = ['booking', 'checkin', 'checkout', 'payment'];
                    const helpTypes = ['complaint', 'info'];

                    if (historyTypes.includes(notif.type)) {
                        this.currentTab = 'history';
                    } else if (helpTypes.includes(notif.type)) {
                        this.currentTab = 'help';
                    }
                    this.notificationsOpen = false;
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
                        const matchesSearch = (f.name || '').toLowerCase().includes(this.searchQuery.toLowerCase()) || 
                                              (f.description || '').toLowerCase().includes(this.searchQuery.toLowerCase());
                        const matchesLantai = this.filterLantai === '' || f.area === this.filterLantai;
                        const matchesTipe = this.filterTipe === '' || f.type === this.filterTipe;
                        const matchesStatus = this.filterStatus === 'semua' || f.status === 'READY';
                        
                        return matchesSearch && matchesLantai && matchesTipe && matchesStatus;
                    });
                },

                resetFilters() {
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
                                        unit_location: `${this.selectedFacility.area}`,
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

                async downloadPDF() {
                    try {
                        this.addToast('Memproses', 'Sedang menyiapkan dokumen PDF...', 'info');
                        const token = localStorage.getItem('wisma_token');
                        const response = await fetch(`${API_URL}/bookings/${this.generatedTicket.id}/ticket`, {
                            method: 'GET',
                            headers: {
                                'Authorization': `Bearer ${token}`,
                                'Accept': 'application/pdf'
                            }
                        });

                        if (!response.ok) {
                            if (response.status === 422 || response.status === 403) {
                                const err = await response.json();
                                this.addToast('Gagal', err.message || 'Tidak dapat mengunduh tiket.', 'error');
                            } else {
                                this.addToast('Gagal', 'Terjadi kesalahan pada server.', 'error');
                            }
                            return;
                        }

                        const blob = await response.blob();
                        const url = window.URL.createObjectURL(blob);
                        
                        const a = document.createElement('a');
                        a.href = url;
                        a.download = `E-Ticket-${this.generatedTicket.booking_code}.pdf`;
                        document.body.appendChild(a);
                        a.click();
                        document.body.removeChild(a);
                        window.URL.revokeObjectURL(url);
                        
                        this.addToast('Berhasil', 'Tiket berhasil diunduh.', 'success');
                    } catch (error) {
                        this.addToast('Gagal', 'Terjadi kesalahan saat mengunduh tiket.', 'error');
                    }
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

                
                async confirmComplaint(id) {
                    try {
                        const res = await this.apiCall('PUT', `/complaints/${id}/confirm`);
                        if (res.success) {
                            const complaint = this.complaints.find(c => c.db_id === id);
                            if (complaint) complaint.status = 'Resolved';
                            this.addToast('Terkonfirmasi', 'Terima kasih telah mengonfirmasi penyelesaian.', 'success');
                            setTimeout(() => { if (window.lucide) window.lucide.createIcons(); }, 50);
                        } else {
                            this.addToast('Gagal', res.message || 'Gagal mengonfirmasi.', 'error');
                        }
                    } catch (e) {
                        this.addToast('Gagal', e.response?.data?.message || 'Terjadi kesalahan sistem.', 'error');
                    }
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
                                    status: c.status === 'pending' ? 'Pending' : (c.status === 'processed' ? 'Processed' : (c.is_guest_confirmed ? 'Resolved' : 'NeedConfirmation')),
                                    db_id: c.id
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
                    let diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
                    if (isNaN(diffDays)) diffDays = 0;
                    
                    if (this.selectedFacility && this.selectedFacility.unit === 'day') {
                        return diffDays + 1;
                    }
                    
                    return diffDays === 0 ? 1 : diffDays;
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
                                
                                const isDayUnit = this.selectedFacility && this.selectedFacility.unit === 'day';
                                if (isDayUnit) {
                                    if (curDate >= bIn && curDate <= bOut) {
                                        isBooked = true;
                                        break;
                                    }
                                } else {
                                    if (curDate >= bIn && curDate < bOut) {
                                        isBooked = true;
                                        break;
                                    }
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
                        
                        const isSameDayAllowed = this.selectedFacility && this.selectedFacility.unit === 'day';
                        const isValidRange = isSameDayAllowed ? checkOutVal >= checkInVal : checkOutVal > checkInVal;
                        
                        if (isValidRange) {
                            // Cek tabrakan dengan rentang booking yang sudah ada
                            let overlap = false;
                            if (this.bookedDatesArr && this.bookedDatesArr.length > 0) {
                                for (const b of this.bookedDatesArr) {
                                    const bIn = new Date(b.check_in);
                                    bIn.setHours(0,0,0,0);
                                    const bOut = new Date(b.check_out);
                                    bOut.setHours(0,0,0,0);
                                    
                                    // Logika tabrakan disesuaikan dengan tipe fasilitas
                                    const isDayUnit = this.selectedFacility && this.selectedFacility.unit === 'day';
                                    if (isDayUnit) {
                                        // Rapat (Hari): overlap jika saling menyentuh
                                        if (checkInVal <= bOut && checkOutVal >= bIn) {
                                            overlap = true;
                                            break;
                                        }
                                    } else {
                                        // Kamar (Malam): overlap jika checkIn kita < checkOut dia DAN checkOut kita > checkIn dia
                                        if (checkInVal < bOut && checkOutVal > bIn) {
                                            overlap = true;
                                            break;
                                        }
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
                    let num = parseFloat(amount);
                    if (isNaN(num)) return 'Rp 0';
                    return 'Rp ' + num.toLocaleString('id-ID');
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