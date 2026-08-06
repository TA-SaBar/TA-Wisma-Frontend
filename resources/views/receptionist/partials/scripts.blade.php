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