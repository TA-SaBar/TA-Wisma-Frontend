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
                adminManagementFilter: '',
                deleteModalOpen: false,
                itemToDelete: null,
                notificationsOpen: false,
                notifications: [],
                get unreadNotificationCount() { return this.notifications.filter(x => !x.is_read && !x.read).length; },
                isLoading: false,
                passwordVisible: false,
                loginForm: {
                    role: 'admin',
                    email: 'koordinator@wisma.dpr.go.id',
                    password: 'password'
                },

                currentTab: 'admin_dashboard',
                
                adminManagementSearch: '',
                adminManagementSubTab: 'Buah',
                
                adminGuestSearch: '',
                adminLogSearch: '',
                adminLogFilter: 'semua',
                adminGuestFilter: 'semua',
                adminGuestViewTab: 'tamu',

                reportGuestSearch: '',
                reportGuestStatus: 'semua',
                reportGuestSearch: '',
                reportGuestStatus: 'semua',
                
                dashboardStartDate: new Date(new Date().getFullYear(), new Date().getMonth(), 1).toLocaleDateString('en-CA'),
                dashboardEndDate: new Date(new Date().getFullYear(), new Date().getMonth() + 1, 0).toLocaleDateString('en-CA'),
                activeQuickPeriodDashboard: 'this_month',
                activeQuickPeriod: 'all',
                reportStartDate: '',
                reportEndDate: '',


                crudModalOpen: false,
                crudAction: 'create',
                crudType: 'Buah',
                crudForm: {
                    id: null,
                    name: '',
                    type: 'Buah',
                    area: 'Area Bawah',
                    capacity: 2,
                    price: 387000,
                    unit: 'night',
                    bed: 'Queen Size',
                    status: 'READY',
                    photo: '',
                    description: ''
                },
                crudPhotoFile: null,

                toasts: [],
                toastCount: 0,

                profile: {
                    id: null,
                    role: 'koordinator_wisma',
                    nama: '',
                    role_label: 'Koordinator Wisma',
                    instansi: 'Wisma DPR RI'
                },

                // Settings state
                settingsTab: 'profil',
                settingsProfile: {
                    nama: '',
                    jabatan: 'Koordinator Wisma',
                    nip: '',
                    instansi: 'Wisma DPR RI'
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

                // Shared data
                facilities: [],
                bookings: [],
                guests: [],
                complaints: [],

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
                        const me = await this.apiCall('GET', '/me');
                        if (me.success) {
                            if (me.data.role !== 'admin' && me.data.role !== 'koordinator_wisma') {
                                window.location.href = '/' + (me.data.role === 'koordinator_wisma' ? 'admin' : me.data.role);
                                return;
                            }
                            this.fillProfile(me.data);
                            this.isLoggedIn = true;
                            this.setQuickPeriod('this_month');
                            await this.loadFacilitiesFromApi();
                            await this.loadBookingsFromApi();
                            await this.loadGuestsFromApi();
                            await this.loadNotifications();
                            await this.fetchFinancialReport();
                            await this.loadNotifications();
                        } else {
                            localStorage.removeItem('wisma_token');
                        }
                    }
                    
                    // Re-render icons when filters change
                    this.$watch('adminManagementSearch', () => setTimeout(() => { if (window.lucide) window.lucide.createIcons(); }, 50));
                    this.$watch('adminManagementFilter', () => setTimeout(() => { if (window.lucide) window.lucide.createIcons(); }, 50));
                    this.$watch('adminGuestSearch', () => setTimeout(() => { if (window.lucide) window.lucide.createIcons(); }, 50));
                    this.$watch('adminLogSearch', () => setTimeout(() => { if (window.lucide) window.lucide.createIcons(); }, 50));
                    this.$watch('reportGuestSearch', () => this.fetchFinancialReport());
                    this.$watch('reportGuestStatus', () => this.fetchFinancialReport());
                    this.$watch('reportStartDate', () => this.fetchFinancialReport());
                    this.$watch('reportEndDate', () => this.fetchFinancialReport());

                    setTimeout(() => {
                        if (window.lucide) window.lucide.createIcons();
                    }, 100);
                },

                fillProfile(user) {
                    this.profile.id       = user.id;
                    this.profile.role     = user.role;
                    this.profile.nama     = user.name;
                    this.profile.instansi = user.instansi || 'Wisma DPR RI';
                    this.settingsProfile.nama     = user.name;
                    this.settingsProfile.nip      = user.nip || '';
                    this.settingsProfile.instansi = user.instansi || 'Wisma DPR RI';
                    this.settingsContact.email    = user.email;
                    this.settingsContact.telepon  = user.phone || '';
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

                                // State for Financial Report API
                reportFinancialData: null,
                isFetchingFinancial: false,
                
                async fetchFinancialReport() {
                    this.isFetchingFinancial = true;
                    try {
                        const params = new URLSearchParams();
                        if (this.reportGuestStatus && this.reportGuestStatus !== 'semua') {
                            params.append('status', this.reportGuestStatus);
                        }
                        if (this.reportGuestSearch) {
                            params.append('search', this.reportGuestSearch);
                        }
                        if (this.reportStartDate) {
                            params.append('start_date', this.reportStartDate);
                        }
                        if (this.reportEndDate) {
                            params.append('end_date', this.reportEndDate);
                        }
                        
                        const res = await this.apiCall('GET', `/reports/financial?${params.toString()}`);
                        if (res.success) {
                            // Map the status back for UI (just in case they need to be displayed correctly)
                            res.data.transaksi = res.data.transaksi.map(b => ({
                                ...b,
                                nama: b.guest_name || (b.user ? b.user.name : '-'),
                                nip: b.guest_nip || (b.user ? b.user.nip : '-'),
                                unit_name: b.facility ? b.facility.name : (b.unit_name || '-'),
                                status: ((b.status || '').toLowerCase() === 'pending' ? 'Pending' : 
                                        ((b.status || '').toLowerCase() === 'lunas' ? 'Lunas' : 
                                        ((b.status || '').toLowerCase() === 'check_in' ? 'Check In' : 
                                        ((b.status || '').toLowerCase() === 'cancelled' || (b.status || '').toLowerCase() === 'batal' || (b.status || '').toLowerCase() === 'dibatalkan' ? 'Dibatalkan' : 'Selesai'))))
                            }));
                            this.reportFinancialData = res.data;
                        }
                    } catch (error) {
                        console.error('Failed to fetch financial report', error);
                    } finally {
                        this.isFetchingFinancial = false;
                    }
                },

                async loadBookingsFromApi() {
                    try {
                        const res = await this.apiCall('GET', '/bookings');
                        if (res.success) {
                            this.bookings = res.data.map(b => ({
                                id: b.id,
                                booking_code: b.booking_code,
                                unit_name: b.facility.name,
                                unit_type: b.facility.type,
                                check_in: b.check_in.substring(0,10),
                                check_out: b.check_out.substring(0,10),
                                nights: b.nights,
                                total_price: b.total_price,
                                status: ((b.status || '').toLowerCase() === 'pending' ? 'Pending' : 
                                        ((b.status || '').toLowerCase() === 'lunas' ? 'Lunas' : 
                                        ((b.status || '').toLowerCase() === 'check_in' ? 'Check In' : 
                                        ((b.status || '').toLowerCase() === 'cancelled' || (b.status || '').toLowerCase() === 'batal' || (b.status || '').toLowerCase() === 'dibatalkan' ? 'Dibatalkan' : 'Selesai')))),
                                nama: b.guest_name,
                                nip: b.guest_nip,
                            }));
                        }
                    } catch (e) {
                        console.error('Gagal memuat bookings:', e);
                    }
                },

                async loadGuestsFromApi() {
                    try {
                        const res = await this.apiCall('GET', '/reports/master-guests');
                        if (res.success) {
                            this.guests = res.data.map(g => ({
                                id: g.id,
                                nama: g.name,
                                nip: g.nip || '-',
                                instansi: g.instansi || '-',
                                email: g.email,
                                phone: g.phone || '-',
                                kunjungan: g.total_booking,
                                status: g.total_booking > 0 ? 'Member' : 'Reguler',
                                joinDate: g.last_visit_at ? g.last_visit_at.substring(0,10) : '2026-07-09'
                            }));
                        }
                    } catch (e) {
                        console.error('Gagal memuat guests:', e);
                    }
                },

                // ==========================================
                // LOGIN
                // ==========================================
                async login() {
                    if (this.isLoading) return;
                    if (!this.loginForm.email || !this.loginForm.password) {
                        this.addToast('Data Tidak Lengkap', 'Email dan Password tidak boleh kosong.', 'error');
                        return;
                    }
                    this.isLoading = true;
                    try {
                        const res = await this.apiCall('POST', '/login', {
                            email:    this.loginForm.email,
                            password: this.loginForm.password
                        });
                        if (res.success) {
                            if (res.data.user.role !== 'koordinator_wisma') {
                                this.addToast('Akses Ditolak', 'Portal ini hanya untuk Koordinator Wisma.', 'error');
                                return;
                            }
                            localStorage.setItem('wisma_token', res.data.token);
                            this.fillProfile(res.data.user);
                            this.isLoggedIn = true;
                            this.currentTab = 'admin_dashboard';
                            this.setQuickPeriod('this_month');
                            this.addToast('Login Berhasil', `Selamat datang kembali, ${this.profile.nama}.`, 'success');
                            await this.loadFacilitiesFromApi();
                            await this.loadBookingsFromApi();
                            await this.loadGuestsFromApi();
                            await this.loadNotifications();
                            await this.fetchFinancialReport();
                        } else {
                            this.addToast('Login Gagal', res.message || 'Email atau password salah.', 'error');
                        }
                    } catch (e) {
                        this.addToast('Koneksi Gagal', 'Tidak dapat terhubung ke server backend.', 'error');
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
                    this.facilities = [];
                    this.addToast('Logout Sukses', 'Anda telah keluar dari sesi admin.', 'info');
                    setTimeout(() => { if (window.lucide) window.lucide.createIcons(); }, 50);
                },

                // ==========================================
                // NOTIFICATIONS
                // ==========================================
                async loadNotifications() {
                    try {
                        const res = await this.apiCall('GET', '/notifications');
                        if (res.success) {
                            this.notifications = res.data;
                            this.unreadCount = this.notifications.filter(n => !n.is_read).length;
                            this.$nextTick(() => { if (window.lucide) window.lucide.createIcons(); });
                        }
                    } catch (e) {
                        console.error('Failed to load notifications:', e);
                    }
                },
                async handleNotificationClick(n) {
                    try {
                        const res = await this.apiCall('PUT', `/notifications/${n.id}/read`);
                        if (res.success) {
                            n.is_read = true;
                            n.read = true;
                        }
                    } catch (e) {
                        console.error(e);
                    }
                    this.notificationsOpen = false;
                },
                async deleteNotification(id) {
                    try {
                        const res = await this.apiCall('DELETE', `/notifications/${id}`);
                        if (res.success) {
                            this.notifications = this.notifications.filter(x => x.id !== id);
                        }
                    } catch (e) {}
                },
                async markNotificationsRead() {
                    try {
                        const res = await this.apiCall('PUT', `/notifications/read-all`);
                        if (res.success) {
                            this.notifications.forEach(n => n.is_read = true);
                            
                        }
                    } catch (e) {
                        console.error(e);
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
                switchTab(tab) {
                    this.currentTab = tab;
                    setTimeout(() => {
                        if (window.lucide) {
                            window.lucide.createIcons();
                        }
                    }, 50);
                },

                async printReport() {
                    try {
                        this.addToast('Memproses', 'Sedang menyiapkan Laporan PDF...', 'info');
                        const token = localStorage.getItem('wisma_token');
                        
                        const params = new URLSearchParams();
                        if (this.reportGuestStatus) params.append('status', this.reportGuestStatus);
                        if (this.reportGuestSearch) params.append('search', this.reportGuestSearch);
                        if (this.reportStartDate) params.append('start_date', this.reportStartDate);
                        if (this.reportEndDate) params.append('end_date', this.reportEndDate);

                        const response = await fetch(`${API_URL}/reports/financial/export-pdf?${params.toString()}`, {
                            method: 'GET',
                            headers: {
                                'Authorization': `Bearer ${token}`,
                                'Accept': 'application/pdf'
                            }
                        });

                        if (!response.ok) {
                            this.addToast('Gagal', 'Gagal mengunduh laporan PDF.', 'error');
                            return;
                        }

                        const blob = await response.blob();
                        const url = window.URL.createObjectURL(blob);
                        
                        const a = document.createElement('a');
                        a.href = url;
                        a.download = `Laporan-Keuangan-Wisma-${new Date().toISOString().slice(0, 10)}.pdf`;
                        document.body.appendChild(a);
                        a.click();
                        document.body.removeChild(a);
                        window.URL.revokeObjectURL(url);
                        
                        this.addToast('Berhasil', 'Laporan berhasil diunduh.', 'success');
                    } catch (error) {
                        this.addToast('Gagal', 'Terjadi kesalahan saat mengunduh laporan.', 'error');
                    }
                },

                openAddModal(type) {
                    this.crudAction = 'create';
                    this.crudType = type;
                    this.crudForm = {
                        id: null,
                        name: '',
                        type: type,
                        area: type === 'Buah' ? 'Area Bawah' : (type === 'Bunga' ? 'Area Atas' : 'Area Bawah'),
                        capacity: type === 'Rapat' ? 30 : 2,
                        price: type === 'Buah' ? 387000 : (type === 'Bunga' ? 549000 : 250000),
                        unit: type === 'Rapat' ? 'day' : 'night',
                        bed: type === 'Buah' ? 'Queen Size' : (type === 'Bunga' ? 'Twin Bed' : 'Meja Rapat Oval'),
                        status: 'READY',
                        photo: type === 'Buah' ? '/images/webp/bungalow_buah.webp' : (type === 'Bunga' ? '/images/webp/bungalow_bunga.webp' : '/images/webp/ruang_rapat.webp'),
                        description: ''
                    };
                    this.crudModalOpen = true;
                    setTimeout(() => {
                        if (window.lucide) window.lucide.createIcons();
                    }, 50);
                },

                openEditModal(item) {
                    this.crudAction = 'edit';
                    this.crudType = item.type;
                    this.crudForm = { ...item };
                    this.crudForm.capacity = parseInt(this.crudForm.capacity, 10);
                    this.crudModalOpen = true;
                    setTimeout(() => {
                         if (window.lucide) window.lucide.createIcons();
                    }, 50);
                },

                handlePhotoUpload(event) {
                    const file = event.target.files[0];
                    if (!file) return;

                    this.crudPhotoFile = file;

                    const reader = new FileReader();
                    reader.onload = (e) => {
                        const img = new Image();
                        img.onload = () => {
                            const canvas = document.createElement('canvas');
                            const ctx = canvas.getContext('2d');
                            
                            const maxW = 400;
                            const maxH = 300;
                            let w = img.width;
                            let h = img.height;
                            
                            if (w > h) {
                                if (w > maxW) {
                                    h *= maxW / w;
                                    w = maxW;
                                }
                            } else {
                                if (h > maxH) {
                                    w *= maxH / h;
                                    h = maxH;
                                }
                            }
                            
                            canvas.width = w;
                            canvas.height = h;
                            ctx.drawImage(img, 0, 0, w, h);
                            this.crudForm.photo = canvas.toDataURL('image/jpeg', 0.7);
                        };
                        img.src = e.target.result;
                    };
                    reader.readAsDataURL(file);
                },

                async saveCrudItem() {
                    if (!this.crudForm.name || !this.crudForm.price) {
                        this.addToast('Data Tidak Lengkap', 'Nama dan harga harus diisi.', 'error');
                        return;
                    }

                    const formData = new FormData();
                    formData.append('name', this.crudForm.name);
                    formData.append('type', this.crudForm.type);
                    formData.append('area', this.crudForm.area);
                    formData.append('capacity', this.crudForm.capacity || 2);
                    formData.append('price', parseInt(this.crudForm.price));
                    const unitValue = (this.crudForm.type || '').toLowerCase().includes('rapat') ? 'day' : 'night';
                    formData.append('unit', unitValue);
                    
                    
                    formData.append('bed', this.crudForm.bed || '');
                    formData.append('status', this.crudForm.status);
                    formData.append('description', this.crudForm.description || '');
                    
                    if (this.crudPhotoFile) {
                        formData.append('photo', this.crudPhotoFile);
                    }
                    
                    if (this.crudAction === 'edit') {
                        formData.append('_method', 'PUT'); // Laravel requirement for FormData PUT
                    }

                    try {
                        let res;
                        if (this.crudAction === 'create') {
                            res = await this.apiCall('POST', '/facilities', formData, true);
                        } else {
                            res = await this.apiCall('POST', `/facilities/${this.crudForm.id}`, formData, true);
                        }

                        if (res.success) {
                            this.addToast('Berhasil', res.message, 'success');
                            this.crudModalOpen = false;
                            this.crudPhotoFile = null;
                            await this.loadFacilitiesFromApi();
                        } else {
                            const errors = res.errors ? Object.values(res.errors).flat().join(' ') : res.message;
                            this.addToast('Gagal', errors, 'error');
                        }
                    } catch (e) {
                        this.addToast('Koneksi Gagal', 'Gagal menyimpan data ke server.', 'error');
                    }

                    setTimeout(() => {
                        if (window.lucide) window.lucide.createIcons();
                    }, 50);
                },

                confirmDelete(id) {
                    const item = this.facilities.find(f => f.id === id);
                    if (!item) return;
                    this.deleteModalOpen = true;
                    this.itemToDelete = item;
                },

                async executeDelete() {
                    if (!this.itemToDelete) return;
                    try {
                        const res = await this.apiCall('DELETE', `/facilities/${this.itemToDelete.id}`);
                        if (res.success) {
                            this.addToast('Dihapus', res.message, 'success');
                            await this.loadFacilitiesFromApi();
                            this.deleteModalOpen = false;
                            this.itemToDelete = null;
                        } else {
                            this.addToast('Gagal', res.message || 'Gagal menghapus data.', 'error');
                        }
                    } catch (e) {
                        this.addToast('Error', 'Gagal menghubungi server.', 'error');
                    }
                },
                
                isDateInPeriod(dateStr, startStr, endStr) {
                    if (!dateStr) return false;
                    const d = new Date(dateStr);
                    d.setHours(0,0,0,0);
                    
                    if (startStr) {
                        const s = new Date(startStr);
                        s.setHours(0,0,0,0);
                        if (d < s) return false;
                    }
                    if (endStr) {
                        const e = new Date(endStr);
                        e.setHours(0,0,0,0);
                        if (d > e) return false;
                    }
                    return true;
                },

                getFilteredIncome() {
                    let total = 0;
                    this.bookings.forEach(b => {
                        if (this.isDateInPeriod(b.check_in, this.dashboardStartDate, this.dashboardEndDate)) {
                            if (b.status === 'Lunas' || b.status === 'Check In' || b.status === 'Selesai') {
                                                total += (parseFloat(b.total_price) || 0);
                                            }
                        }
                    });
                    return total;
                },

                getFilteredGuestsCount() {
                    let total = 0;
                    const seenNips = new Set();
                    this.bookings.forEach(b => {
                        if (this.isDateInPeriod(b.check_in, this.dashboardStartDate, this.dashboardEndDate)) {
                            if (b.status === 'Lunas' || b.status === 'Check In' || b.status === 'Selesai') {
                                if (b.nip && !seenNips.has(b.nip)) {
                                    seenNips.add(b.nip);
                                    total++;
                                }
                            }
                        }
                    });
                    return total;
                },

                
                setQuickPeriodDashboard(period) {
                    this.activeQuickPeriodDashboard = period;
                    const today = new Date();
                    if (period === 'all') {
                        this.dashboardStartDate = '';
                        this.dashboardEndDate = '';
                    } else if (period === 'this_month') {
                        this.dashboardStartDate = new Date(today.getFullYear(), today.getMonth(), 1).toLocaleDateString('en-CA');
                        this.dashboardEndDate = new Date(today.getFullYear(), today.getMonth() + 1, 0).toLocaleDateString('en-CA');
                    } else if (period === 'last_month') {
                        this.dashboardStartDate = new Date(today.getFullYear(), today.getMonth() - 1, 1).toLocaleDateString('en-CA');
                        this.dashboardEndDate = new Date(today.getFullYear(), today.getMonth(), 0).toLocaleDateString('en-CA');
                    } else if (period === 'this_year') {
                        this.dashboardStartDate = new Date(today.getFullYear(), 0, 1).toLocaleDateString('en-CA');
                        this.dashboardEndDate = new Date(today.getFullYear(), 11, 31).toLocaleDateString('en-CA');
                    }
                },
                
                setQuickPeriod(period) {
                    this.activeQuickPeriod = period;
                    const today = new Date();
                    if (period === 'all') {
                        this.reportStartDate = '';
                        this.reportEndDate = '';
                    } else if (period === 'this_month') {
                        this.reportStartDate = new Date(today.getFullYear(), today.getMonth(), 1).toLocaleDateString('en-CA');
                        this.reportEndDate = new Date(today.getFullYear(), today.getMonth() + 1, 0).toLocaleDateString('en-CA');
                    } else if (period === 'last_month') {
                        this.reportStartDate = new Date(today.getFullYear(), today.getMonth() - 1, 1).toLocaleDateString('en-CA');
                        this.reportEndDate = new Date(today.getFullYear(), today.getMonth(), 0).toLocaleDateString('en-CA');
                    } else if (period === 'this_year') {
                        this.reportStartDate = new Date(today.getFullYear(), 0, 1).toLocaleDateString('en-CA');
                        this.reportEndDate = new Date(today.getFullYear(), 11, 31).toLocaleDateString('en-CA');
                    }
                },

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
                            this.addToast('Profil Diperbarui', 'Data diri Anda berhasil diperbarui.', 'success');
                        } else {
                            const errors = res.errors ? Object.values(res.errors).flat().join(' ') : (res.message || 'Gagal memperbarui profil.');
                            this.addToast('Gagal', errors, 'error');
                        }
                    } catch (e) {
                        this.addToast('Koneksi Gagal', 'Tidak dapat terhubung ke server.', 'error');
                    }
                },

                async saveSettingsContact() {
                    // Contact settings use the same endpoint as profile settings
                    await this.saveSettingsProfile();
                },

                async saveSettingsPassword() {
                    if (!this.settingsPassword.current) {
                        this.addToast('Gagal', 'Masukkan kata sandi saat ini.', 'error'); return;
                    }
                    if (this.settingsPassword.new.length < 6) {
                        this.addToast('Gagal', 'Kata sandi baru minimal 6 karakter.', 'error'); return;
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
                            this.addToast('Kata Sandi Diperbarui', 'Kata sandi Anda berhasil diubah.', 'success');
                        } else {
                            const errors = res.errors ? Object.values(res.errors).flat().join(' ') : (res.message || 'Gagal mengubah kata sandi.');
                            this.addToast('Gagal', errors, 'error');
                        }
                    } catch (e) {
                        this.addToast('Koneksi Gagal', 'Tidak dapat terhubung ke server.', 'error');
                    }
                },

                filteredAdminFacilities() {
                    return this.facilities.filter(f => {
                        const matchesTab = f.type === this.adminManagementSubTab;
                        const matchesSearch = f.name.toLowerCase().includes(this.adminManagementSearch.toLowerCase()) || 
                                              (f.type || '').toLowerCase().includes(this.adminManagementSearch.toLowerCase()) ||
                                              (f.area || '').toLowerCase().includes(this.adminManagementSearch.toLowerCase());
                        return matchesTab && matchesSearch;
                    });
                },

                filteredGuests() {
                    return this.guests.filter(g => {
                        const matchesSearch = g.nama.toLowerCase().includes(this.adminGuestSearch.toLowerCase()) || 
                                              g.email.toLowerCase().includes(this.adminGuestSearch.toLowerCase()) ||
                                              g.phone.includes(this.adminGuestSearch);
                        if (this.adminGuestFilter === 'member') {
                            return matchesSearch && g.status === 'Member';
                        }
                        if (this.adminGuestFilter === 'menginap') {
                            return matchesSearch && g.status === 'Reguler';
                        }
                        return matchesSearch;
                    });
                },

                getMonthlyChart() {
                    const months = [];
                    const now = new Date();
                    for (let i = 2; i >= 0; i--) {
                        const d = new Date(now.getFullYear(), now.getMonth() - i, 1);
                        const label = d.toLocaleString('id-ID', { month: 'short' });
                        let kamar = 0;
                        let rapat = 0;
                        const count = this.bookings.filter(b => {
                            if (!b.check_in) return false;
                            const isSuccessful = b.status === 'Lunas' || b.status === 'Selesai' || b.status === 'Check In';
                            if (!isSuccessful) return false;
                            const bDate = new Date(b.check_in);
                            if (bDate.getMonth() === d.getMonth() && bDate.getFullYear() === d.getFullYear()) {
                                const type = (b.unit_type || '').toLowerCase();
                                if (type.includes('rapat')) rapat++;
                                else kamar++;
                                return true;
                            }
                            return false;
                        }).length;
                        months.push({ label, count, kamar, rapat });
                    }
                    const maxCount = Math.max(...months.map(m => m.count), 1);
                    return months.map(m => ({
                        label: m.label,
                        count: m.count,
                        kamar: m.kamar,
                        rapat: m.rapat,
                        percent: Math.floor((m.count / maxCount) * 100)
                    }));
                },

                getTypeOccupancy() {
                    const activeBookings = this.bookings.filter(b => {
                        if (!this.isDateInPeriod(b.check_in, this.dashboardStartDate, this.dashboardEndDate)) return false;
                        return b.status === 'Lunas' || b.status === 'Selesai' || b.status === 'Check In';
                    });
                    const total = activeBookings.length || 1;
                    let kamar = 0, rapat = 0;
                    activeBookings.forEach(b => {
                        const type = (b.unit_type || '').toLowerCase();
                        if (type.includes('rapat')) rapat++;
                        else kamar++;
                    });
                    return [
                        { label: 'Kamar / Bungalow', percent: Math.round((kamar / total) * 100) },
                        { label: 'Ruang Rapat', percent: Math.round((rapat / total) * 100) }
                    ];
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

                saveSettingsProfile() {
                    this.profile.nama = this.settingsProfile.nama;
                    this.profile.instansi = this.settingsProfile.instansi;
                    this.addToast('Profil Diperbarui', 'Data profil berhasil disimpan.', 'success');
                },

                saveSettingsContact() {
                    this.addToast('Kontak Diperbarui', 'Email dan nomor telepon berhasil disimpan.', 'success');
                },

                saveSettingsPassword() {
                    if (!this.settingsPassword.current) {
                        this.addToast('Gagal', 'Masukkan kata sandi saat ini.', 'error'); return;
                    }
                    if (this.settingsPassword.new.length < 6) {
                        this.addToast('Gagal', 'Kata sandi baru minimal 6 karakter.', 'error'); return;
                    }
                    if (this.settingsPassword.new !== this.settingsPassword.confirm) {
                        this.addToast('Gagal', 'Konfirmasi kata sandi tidak cocok.', 'error'); return;
                    }
                    this.settingsPassword = { current: '', new: '', confirm: '' };
                    this.addToast('Kata Sandi Diperbarui', 'Kata sandi berhasil diubah.', 'success');
                },

                formatRupiah(amount) {
                    if (amount === undefined || amount === null) return 'Rp 0';
                    let num = parseFloat(amount);
                    if (isNaN(num)) return 'Rp 0';
                    return 'Rp ' + num.toLocaleString('id-ID');
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
                }
            };
        }
    </script>