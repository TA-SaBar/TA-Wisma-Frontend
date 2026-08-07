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
                isLoading: false,
                passwordVisible: false,
                loginForm: {
                    email: 'cs@wisma.dpr.go.id',
                    password: 'password'
                },

                currentTab: 'cs_dashboard',
                
                complaintSearch: '',
                complaintFilterTab: 'semua',

                csReportSubTab: 'keluhan',

                reportStartDate: '',
                reportEndDate: '',
                activeQuickPeriod: 'all',

                parseIndoDate(dateStr) {
                    if (!dateStr) return null;
                    if (dateStr.includes('-')) return new Date(dateStr);
                    try {
                        const cleanStr = dateStr.split(',')[0].trim();
                        const parts = cleanStr.split(' ');
                        if (parts.length !== 3) return null;
                        const day = parseInt(parts[0]);
                        const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
                        const monthIdx = months.indexOf(parts[1]);
                        const year = parseInt(parts[2]);
                        if (monthIdx === -1) return null;
                        return new Date(year, monthIdx, day);
                    } catch (e) {
                        return null;
                    }
                },

                isDateInPeriod(dateStr, startStr, endStr) {
                    const itemDate = this.parseIndoDate(dateStr);
                    if (!itemDate) return true;
                    itemDate.setHours(0,0,0,0);
                    const itemTime = itemDate.getTime();
                    
                    if (startStr) {
                        const startDate = new Date(startStr);
                        startDate.setHours(0,0,0,0);
                        if (itemTime < startDate.getTime()) return false;
                    }
                    if (endStr) {
                        const endDate = new Date(endStr);
                        endDate.setHours(0,0,0,0);
                        if (itemTime > endDate.getTime()) return false;
                    }
                    return true;
                },

                formatISODate(date) {
                    const year = date.getFullYear();
                    const month = String(date.getMonth() + 1).padStart(2, '0');
                    const day = String(date.getDate()).padStart(2, '0');
                    return `${year}-${month}-${day}`;
                },

                setQuickPeriod(period) {
                    this.activeQuickPeriod = period;
                    const now = new Date();
                    if (period === 'this_month') {
                        const firstDay = new Date(now.getFullYear(), now.getMonth(), 1);
                        const lastDay = new Date(now.getFullYear(), now.getMonth() + 1, 0);
                        this.reportStartDate = this.formatISODate(firstDay);
                        this.reportEndDate = this.formatISODate(lastDay);
                    } else if (period === 'last_month') {
                        const firstDay = new Date(now.getFullYear(), now.getMonth() - 1, 1);
                        const lastDay = new Date(now.getFullYear(), now.getMonth(), 0);
                        this.reportStartDate = this.formatISODate(firstDay);
                        this.reportEndDate = this.formatISODate(lastDay);
                    } else if (period === 'this_year') {
                        const firstDay = new Date(now.getFullYear(), 0, 1);
                        const lastDay = new Date(now.getFullYear(), 11, 31);
                        this.reportStartDate = this.formatISODate(firstDay);
                        this.reportEndDate = this.formatISODate(lastDay);
                    } else if (period === 'all') {
                        this.reportStartDate = '';
                        this.reportEndDate = '';
                    }
                },

                reportComplaintFilterCategory: 'semua',
                reportComplaintFilterStatus: 'semua',
                reportRatingFilter: 'semua',
                
                inputComplaintModalOpen: false,
                newComplaintForm: {
                    category: 'facility',
                    location: '',
                    userId: '',
                    description: ''
                },

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
                        if (me.success && (me.data.role === 'receptionist' || me.data.role === 'koordinator_wisma' || me.data.role === 'customer_service')) {
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
                        customer_service: 'Customer Service',
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
                            setTimeout(() => { if (window.lucide) window.lucide.createIcons(); }, 50);
                        }
                    } catch (e) {
                        console.error('Gagal memuat notifikasi:', e);
                    }
                },

                get unreadNotificationCount() {
                    return this.notifications.filter(n => !n.read).length;
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

                async handleNotificationClick(n) {
                    if (!n.read) {
                        try {
                            const res = await this.apiCall('PUT', `/notifications/${n.id}/read`);
                            if (res.success) {
                                n.read = true;
                                n.is_read = true;
                            }
                        } catch(e) {}
                    }
                    
                    this.showNotifications = false;

                    if (n.type === 'complaint' || n.related_type === 'new_complaint') {
                        this.currentTab = 'cs_complaints';
                    } else if (n.type === 'rating' || n.related_type === 'new_rating') {
                        this.currentTab = 'cs_reports';
                        this.csReportSubTab = 'ulasan';
                    }
                    
                    window.scrollTo({ top: 0, behavior: 'smooth' });
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

                // Settings state
                settingsTab: 'profil',
                settingsProfile: {
                    nama: 'Amira CS',
                    jabatan: 'Customer Service & Pelayanan Keluhan',
                    nip: '199308122018022003',
                    instansi: 'Layanan Customer Service Wisma'
                },
                settingsContact: {
                    email: 'amira.cs@dpr.go.id',
                    telepon: '+62 814-3456-7891'
                },
                settingsPassword: {
                    current: '',
                    new: '',
                    confirm: ''
                },
                settingsPasswordVisible: { current: false, new: false, confirm: false },

                profile: {
                    role: 'customer_service',
                    nama: 'Amira CS',
                    role_label: 'Customer Service & Pelayanan Keluhan',
                    instansi: 'Layanan Customer Service Wisma'
                },

                // Shared LocalStorage data
                
                    complaints: [],
                    feedbacks: [],
                    feedbacksAgg: { total: 0, avg_cleanliness: 0, avg_facilities: 0, avg_service: 0, avg_overall: 0 },
                
                get filteredFeedbacksList() {
                    return this.feedbacks.filter(f => {
                        const dateMatch = this.isDateInPeriod(f.created_at, this.reportStartDate, this.reportEndDate);
                        if (!dateMatch) return false;
                        if (this.reportRatingFilter === '5') return Math.floor(f.average_rating) === 5;
                        if (this.reportRatingFilter === '4') return Math.floor(f.average_rating) === 4;
                        if (this.reportRatingFilter === '3') return f.average_rating < 4;
                        return true;
                    });
                },

                get filteredFeedbacksAgg() {
                    const filtered = this.feedbacks.filter(f => this.isDateInPeriod(f.created_at, this.reportStartDate, this.reportEndDate));
                    if (filtered.length === 0) return { total: 0, avg_cleanliness: 0, avg_facilities: 0, avg_service: 0, avg_overall: 0 };
                    return {
                        total: filtered.length,
                        avg_cleanliness: (filtered.reduce((sum, f) => sum + parseFloat(f.rating_cleanliness), 0) / filtered.length).toFixed(1),
                        avg_facilities: (filtered.reduce((sum, f) => sum + parseFloat(f.rating_facilities), 0) / filtered.length).toFixed(1),
                        avg_service: (filtered.reduce((sum, f) => sum + parseFloat(f.rating_service), 0) / filtered.length).toFixed(1),
                        avg_overall: (filtered.reduce((sum, f) => sum + parseFloat(f.average_rating), 0) / filtered.length).toFixed(1)
                    };
                },

                    guests: [],


                
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

                async initApp() {
                    const token = localStorage.getItem('wisma_token');
                    if (token) {
                        const me = await this.apiCall('GET', '/me');
                        if (me.success) {
                            if (me.data.role !== 'customer_service') {
                                window.location.href = '/' + (me.data.role === 'koordinator_wisma' ? 'admin' : me.data.role);
                                return;
                            }
                            const userData = me.data;
                            this.isLoggedIn = true;
                            this.profile.role = userData.role;
                            this.profile.nama = userData.name;
                            this.profile.email = userData.email;
                            
                            this.settingsProfile.nama = userData.name;
                            this.settingsContact.email = userData.email;
                            this.settingsContact.telepon = userData.phone || '';
                            this.settingsProfile.nip = userData.nip || '';
                            this.settingsProfile.instansi = userData.institution || 'Wisma DPR';
                            
                            await this.loadData();
                            await this.loadNotifications();
                        } else {
                            this.isLoggedIn = false;
                        }
                    } else {
                        this.isLoggedIn = false;
                    }
                    
                    if (this.isLoggedIn) {
                        this.startPolling();
                    }
                    
                    setTimeout(() => {
                        if (window.lucide) {
                            window.lucide.createIcons();
                        }
                    }, 100);
                },

                async loadData() {
                    await this.loadGuestsFromApi();
                    await this.loadComplaintsFromApi();
                    await this.loadFeedbacksFromApi();
                },

                async loadGuestsFromApi() {
                    try {
                        const res = await this.apiCall('GET', '/guests');
                        if (res.success || Array.isArray(res)) {
                            this.guests = Array.isArray(res) ? res : res.data;
                        }
                    } catch (e) {
                        console.error('Gagal memuat data tamu:', e);
                    }
                },

                async loadComplaintsFromApi() {
                    try {
                        const res = await this.apiCall('GET', '/complaints');
                        if (res.success) {
                            this.complaints = res.data.map(c => ({
                                id: c.complaint_code,
                                db_id: c.id,
                                title: c.title,
                                category: c.category,
                                category_label: c.category === 'facility' ? 'Fasilitas (Bungalow, Gedung)' : (c.category === 'laundry' ? 'Layanan Laundry' : (c.category === 'internet' ? 'Internet / Wifi' : 'Layanan Makanan')),
                                category_slug: c.category.toLowerCase().replace(/\s+/g, '-'),
                                location: c.location,
                                date: new Date(c.created_at).toLocaleDateString('id-ID', {day: 'numeric', month: 'short', year: 'numeric', hour: '2-digit', minute:'2-digit'}),
                                status: c.status === 'pending' ? 'Pending' : (c.status === 'processed' ? 'Processed' : (c.is_guest_confirmed ? 'Resolved' : 'NeedConfirmation')),
                                description: c.description
                            }));
                                setTimeout(() => { if (window.lucide) window.lucide.createIcons(); }, 50);
                        }
                    } catch (e) {
                        console.error('Gagal memuat keluhan', e);
                    }
                },

                async loadFeedbacksFromApi() {
                    try {
                        const res = await this.apiCall('GET', '/feedbacks');
                        if (res.success) {
                            this.feedbacks = res.data;
                            this.feedbacksAgg = res.aggregation;
                        }
                    } catch (e) {
                        console.error('Gagal memuat feedback', e);
                    }
                },

                
                async login() {
                    this.isLoading = true;
                    try {
                        const res = await this.apiCall('POST', '/login', {
                            email: this.loginForm.email,
                            password: this.loginForm.password
                        });
                        
                        if (res.success) {
                            const userData = res.data.user;
                            if (userData.role !== 'customer_service') {
                                await this.apiCall('POST', '/logout');
                                this.addToast('Akses Ditolak', 'Akun ini bukan Customer Service.', 'error');
                                return;
                            }
                            
                            const token = res.data.token;
                            localStorage.setItem('wisma_token', token);
                            
                            this.isLoggedIn = true;
                            this.profile.role = userData.role;
                            this.profile.nama = userData.name;
                            this.profile.email = userData.email;
                            this.currentTab = 'cs_dashboard';
                            
                            this.addToast('Login Berhasil', `Selamat datang kembali, ${this.profile.nama}.`, 'success');
                            await this.loadData();
                            await this.loadNotifications();
                            this.startPolling();
                            
                            setTimeout(() => { if (window.lucide) window.lucide.createIcons(); }, 50);
                        } else {
                            this.addToast('Login Gagal', res.message || 'Email atau password salah.', 'error');
                        }
                    } catch (e) {
                        this.addToast('Login Gagal', 'Terjadi kesalahan sistem.', 'error');
                    } finally {
                        this.isLoading = false;
                    }
                },


                
                async logout() {
                    try {
                        await this.apiCall('POST', '/logout');
                        localStorage.removeItem('wisma_token');
                        this.isLoggedIn = false;
                        this.stopPolling();
                        this.loginForm.email = '';
                        this.loginForm.password = '';
                        this.addToast('Sesi Berakhir', 'Anda telah logout dari portal customer service.', 'info');
                    } catch (e) {
                        this.addToast('Gagal', 'Terjadi kesalahan saat logout.', 'error');
                    }
                },
                
                startPolling() {
                    let self = this;
                    if (!window._csPollingInterval) {
                        window._csPollingInterval = setInterval(() => {
                            if (typeof self.loadNotifications === 'function') self.loadNotifications();
                            if (typeof self.loadComplaintsFromApi === 'function') self.loadComplaintsFromApi();
                            if (typeof self.loadFeedbacksFromApi === 'function') self.loadFeedbacksFromApi();
                        }, 5000);
                    }
                },
                
                stopPolling() {
                    if (window._csPollingInterval) {
                        clearInterval(window._csPollingInterval);
                        window._csPollingInterval = null;
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

                async printReport() {
                    const token = localStorage.getItem('wisma_token');
                    if (!token) return;
                    
                    try {
                        let endpoint = '';
                        let params = new URLSearchParams();
                        let filename = '';

                        if (this.csReportSubTab === 'keluhan') {
                            endpoint = '/complaints/export-pdf';
                            if (this.reportComplaintFilterCategory && this.reportComplaintFilterCategory !== 'semua') {
                                params.append('category', this.reportComplaintFilterCategory);
                            }
                            if (this.reportComplaintFilterStatus && this.reportComplaintFilterStatus !== 'semua') {
                                params.append('status', this.reportComplaintFilterStatus);
                            }
                            if (this.reportStartDate) params.append('start_date', this.reportStartDate);
                            if (this.reportEndDate) params.append('end_date', this.reportEndDate);
                            filename = `Laporan-Keluhan-${new Date().toISOString().slice(0, 10)}.pdf`;
                        } else if (this.csReportSubTab === 'ulasan') {
                            endpoint = '/feedbacks/export-pdf';
                            if (this.reportStartDate) params.append('start_date', this.reportStartDate);
                            if (this.reportEndDate) params.append('end_date', this.reportEndDate);
                            // Note: we might filter rating if backend supports it later
                            filename = `Laporan-Ulasan-${new Date().toISOString().slice(0, 10)}.pdf`;
                        }

                        this.addToast('Memproses', 'Sedang menyiapkan laporan PDF...', 'info');

                        const response = await fetch(`${API_URL}${endpoint}?${params.toString()}`, {
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
                        a.download = filename;
                        document.body.appendChild(a);
                        a.click();
                        document.body.removeChild(a);
                        window.URL.revokeObjectURL(url);
                        
                    } catch (error) {
                        console.error('Print Error:', error);
                        this.addToast('Error', 'Terjadi kesalahan saat mengunduh laporan.', 'error');
                    }
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

                filteredComplaints() {
                    return this.complaints.filter(c => {
                        const matchesSearch = c.title.toLowerCase().includes(this.complaintSearch.toLowerCase()) || 
                                              c.location.toLowerCase().includes(this.complaintSearch.toLowerCase());
                        const matchesFilter = this.complaintFilterTab === 'semua' || c.status === this.complaintFilterTab;
                        return matchesSearch && matchesFilter;
                    });
                },

                async processComplaint(id) {
                    try {
                        const complaint = this.complaints.find(c => c.id === id);
                        if (!complaint) return;
                        
                        const res = await this.apiCall('PUT', `/complaints/${complaint.db_id}/process`);
                        
                        if (res.success) {
                            complaint.status = 'Processed';
                            this.addToast('Keluhan Diproses', 'Tim teknis/layanan telah ditugaskan ke lokasi.', 'success');
                            setTimeout(() => { if (window.lucide) window.lucide.createIcons(); }, 50);
                        }
                    } catch (e) {
                        this.addToast('Gagal', e.response?.data?.message || 'Gagal memproses keluhan', 'error');
                    }
                },

                async resolveComplaint(id) {
                    try {
                        const complaint = this.complaints.find(c => c.id === id);
                        if (!complaint) return;
                        
                        const res = await this.apiCall('PUT', `/complaints/${complaint.db_id}/resolve`);
                        
                        if (res.success) {
                            complaint.status = 'NeedConfirmation';
                            this.addToast('Menunggu Konfirmasi', 'Menunggu konfirmasi tamu untuk menyelesaikan keluhan.', 'success');
                            setTimeout(() => { if (window.lucide) window.lucide.createIcons(); }, 50);
                        }
                    } catch (e) {
                        this.addToast('Gagal', e.response?.data?.message || 'Gagal menyelesaikan keluhan', 'error');
                    }
                },

                openNewComplaintModal() {
                    this.newComplaintForm = {
                        category: 'facility',
                        location: '',
                        userId: '',
                        description: ''
                    };
                    this.inputComplaintModalOpen = true;
                },

                async saveNewComplaint() {
                    try {
                        const titleText = this.newComplaintForm.description.length > 30 
                            ? this.newComplaintForm.description.substring(0, 30) + '...' 
                            : this.newComplaintForm.description;
                            
                        const res = await this.apiCall('POST', '/complaints/manual', {
                            user_id: this.newComplaintForm.userId,
                            title: titleText || 'Keluhan Manual',
                            category: this.newComplaintForm.category,
                            location: this.newComplaintForm.location,
                            description: this.newComplaintForm.description
                        });
                        
                        if (res.success) {
                            this.inputComplaintModalOpen = false;
                            this.addToast('Keluhan Berhasil Dicatat', 'Laporan keluhan lisan tamu telah dimasukkan ke sistem.', 'success');
                            this.loadComplaintsFromApi();
                        }
                    } catch (e) {
                        this.addToast('Gagal', e.response?.data?.message || 'Gagal menyimpan keluhan', 'error');
                    }
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

                async saveSettingsProfile() {
                    try {
                        const res = await this.apiCall('PUT', '/profile', {
                            name: this.settingsProfile.nama,
                            phone: this.settingsContact.telepon,
                            email: this.settingsContact.email,
                            nip: this.settingsProfile.nip,
                            institution: this.settingsProfile.instansi
                        });
                        if (res.success) {
                            this.profile.nama = this.settingsProfile.nama;
                            this.profile.instansi = this.settingsProfile.instansi;
                            this.addToast('Profil Diperbarui', 'Data profil berhasil disimpan.', 'success');
                        }
                    } catch (e) {
                        this.addToast('Gagal', e.response?.data?.message || 'Gagal menyimpan profil', 'error');
                    }
                },

                async saveSettingsContact() {
                    try {
                        const res = await this.apiCall('PUT', '/profile', {
                            name: this.settingsProfile.nama,
                            phone: this.settingsContact.telepon,
                            email: this.settingsContact.email,
                            nip: this.settingsProfile.nip,
                            institution: this.settingsProfile.instansi
                        });
                        if (res.success) {
                            this.addToast('Kontak Diperbarui', 'Email dan nomor telepon berhasil disimpan.', 'success');
                        }
                    } catch (e) {
                        this.addToast('Gagal', e.response?.data?.message || 'Gagal menyimpan kontak', 'error');
                    }
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
                            current_password: this.settingsPassword.current,
                            password: this.settingsPassword.new,
                            password_confirmation: this.settingsPassword.confirm
                        });
                        if (res.success) {
                            this.settingsPassword = { current: '', new: '', confirm: '' };
                            this.addToast('Kata Sandi Diperbarui', 'Kata sandi berhasil diubah.', 'success');
                        }
                    } catch (e) {
                        this.addToast('Gagal', e.response?.data?.message || 'Gagal mengubah kata sandi', 'error');
                    }
                }
            };
        }
    </script>