/**
 * Authentication & Authorization Utility
 * Menangani redirect berdasarkan role dan proteksi halaman
 */

const Auth = {
    // Storage keys
    TOKEN_KEY: 'auth_token',
    USER_KEY: 'auth_user',
    ROLES_KEY: 'auth_roles',

    /**
     * Simpan data authentication setelah login/register
     */
    setAuth(token, user, roles) {
        sessionStorage.setItem(this.TOKEN_KEY, token);
        sessionStorage.setItem(this.USER_KEY, JSON.stringify(user));
        sessionStorage.setItem(this.ROLES_KEY, JSON.stringify(roles));
    },

    /**
     * Ambil token
     */
    getToken() {
        return sessionStorage.getItem(this.TOKEN_KEY);
    },

    /**
     * Ambil data user
     */
    getUser() {
        const user = sessionStorage.getItem(this.USER_KEY);
        return user ? JSON.parse(user) : null;
    },

    /**
     * Ambil roles user
     */
    getRoles() {
        const roles = sessionStorage.getItem(this.ROLES_KEY);
        return roles ? JSON.parse(roles) : [];
    },

    /**
     * Check apakah user sudah login
     */
    isAuthenticated() {
        return !!this.getToken();
    },

    /**
     * Check apakah user memiliki role tertentu
     */
    hasRole(roleName) {
        const roles = this.getRoles();
        return roles.some(role => role.name === roleName || role === roleName);
    },

    /**
     * Logout - hapus semua data auth
     */
    logout() {
        sessionStorage.removeItem(this.TOKEN_KEY);
        sessionStorage.removeItem(this.USER_KEY);
        sessionStorage.removeItem(this.ROLES_KEY);
        window.location.href = '/login';
    },

    /**
     * Redirect ke dashboard sesuai role
     */
    redirectToDashboard() {
        const roles = this.getRoles();
        
        if (!roles || roles.length === 0) {
            window.location.href = '/login';
            return;
        }

        // Check role dan redirect
        if (this.hasRole('master-admin')) {
            window.location.href = '/dashboard/superadmin';
        } else if (this.hasRole('supervisor')) {
            window.location.href = '/dashboard/supervisor';
        } else if (this.hasRole('helpdesk')) {
            window.location.href = '/helpdesk/incoming';
        } else if (this.hasRole('technician')) {
            window.location.href = '/technician/dashboard';
        } else if (this.hasRole('requester')) {
            window.location.href = '/dashboard/requester';
        } else {
            // Default ke requester dashboard
            window.location.href = '/dashboard/requester';
        }
    },

    /**
     * Proteksi halaman - redirect jika belum login
     */
    requireAuth() {
        if (!this.isAuthenticated()) {
            window.location.href = '/login';
            return false;
        }
        return true;
    },

    /**
     * Proteksi halaman login - redirect ke dashboard jika sudah login
     */
    requireGuest() {
        if (this.isAuthenticated()) {
            this.redirectToDashboard();
            return false;
        }
        return true;
    },

    /**
     * Proteksi halaman berdasarkan role
     * @param {Array} allowedRoles - Array nama role yang diizinkan
     */
    requireRole(allowedRoles) {
        if (!this.isAuthenticated()) {
            window.location.href = '/login';
            return false;
        }

        const hasPermission = allowedRoles.some(role => this.hasRole(role));
        
        if (!hasPermission) {
            // Redirect ke dashboard sesuai role user
            this.redirectToDashboard();
            return false;
        }

        return true;
    },

    /**
     * Get headers untuk fetch API
     */
    getHeaders() {
        const token = this.getToken();
        return {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'Authorization': token ? `Bearer ${token}` : ''
        };
    },

    /**
     * Fetch dengan authentication
     */
    async fetch(url, options = {}) {
        const defaultOptions = {
            headers: this.getHeaders()
        };

        const response = await fetch(url, { ...defaultOptions, ...options });

        // Handle 401 Unauthorized
        if (response.status === 401) {
            this.logout();
            throw new Error('Unauthorized');
        }

        return response;
    }
};

// Export untuk digunakan di module lain
if (typeof module !== 'undefined' && module.exports) {
    module.exports = Auth;
}
