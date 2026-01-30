/**
 * Token Manager - Manage authentication token and user data
 * Handles token, user data, and roles in sessionStorage
 */
const TokenManager = {
    // Storage keys
    STORAGE_TOKEN: 'auth_token',
    STORAGE_USER: 'auth_user',
    STORAGE_ROLES: 'auth_roles',

    /**
     * Set authentication data (token, user, roles)
     * @param {string} token - Authentication token
     * @param {object} user - User object from API
     * @param {array} roles - Array of role objects
     * @returns {boolean} - True if saved successfully
     */
    setAuth(token, user = null, roles = null) {
        try {
            if (!token || typeof token !== 'string' || token.trim() === '') {
                console.error('Invalid token');
                return false;
            }

            sessionStorage.setItem(this.STORAGE_TOKEN, token);
            
            if (user) {
                sessionStorage.setItem(this.STORAGE_USER, JSON.stringify(user));
            }
            
            if (roles) {
                sessionStorage.setItem(this.STORAGE_ROLES, JSON.stringify(roles));
            }
            
            return true;
        } catch (error) {
            console.error('Error saving auth data:', error);
            return false;
        }
    },

    /**
     * Set token only (for backward compatibility)
     * @param {string} token - Authentication token
     * @returns {boolean}
     */
    setToken(token) {
        return this.setAuth(token);
    },

    /**
     * Get token from sessionStorage
     * @returns {string|null}
     */
    getToken() {
        return sessionStorage.getItem(this.STORAGE_TOKEN);
    },

    /**
     * Get user data from sessionStorage
     * @returns {object|null}
     */
    getUser() {
        const user = sessionStorage.getItem(this.STORAGE_USER);
        return user ? JSON.parse(user) : null;
    },

    /**
     * Get roles from sessionStorage
     * @returns {array}
     */
    getRoles() {
        const roles = sessionStorage.getItem(this.STORAGE_ROLES);
        return roles ? JSON.parse(roles) : [];
    },

    /**
     * Check if user is authenticated
     * @returns {boolean}
     */
    isAuthenticated() {
        return !!this.getToken();
    },

    /**
     * Check if user has specific role
     * @param {string} roleName - Role name to check
     * @returns {boolean}
     */
    hasRole(roleName) {
        const roles = this.getRoles();
        return roles.some(role => role.name === roleName || role === roleName);
    },

    /**
     * Clear all auth data (logout)
     */
    clearAuth() {
        sessionStorage.removeItem(this.STORAGE_TOKEN);
        sessionStorage.removeItem(this.STORAGE_USER);
        sessionStorage.removeItem(this.STORAGE_ROLES);
    },

    /**
     * Clear token only (for backward compatibility)
     */
    clearToken() {
        this.clearAuth();
    },

    /**
     * Redirect to dashboard based on user role
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
     * Get dashboard URL based on role
     * @returns {string}
     */
    getDashboardUrl() {
        const roles = this.getRoles();
        
        if (!roles || roles.length === 0) {
            return '/login';
        }

        if (this.hasRole('master-admin')) {
            return '/dashboard/superadmin';
        } else if (this.hasRole('supervisor')) {
            return '/dashboard/supervisor';
        } else if (this.hasRole('helpdesk')) {
            return '/helpdesk/incoming';
        } else if (this.hasRole('technician')) {
            return '/technician/dashboard';
        } else {
            return '/dashboard/requester';
        }
    },

    /**
     * Protect page - redirect to login if not authenticated
     */
    requireAuth() {
        if (!this.isAuthenticated()) {
            window.location.href = '/login';
            return false;
        }
        return true;
    },

    /**
     * Protect guest pages - redirect to dashboard if authenticated
     */
    requireGuest() {
        if (this.isAuthenticated()) {
            this.redirectToDashboard();
            return false;
        }
        return true;
    },

    /**
     * Require specific role(s)
     * @param {array|string} allowedRoles - Role name or array of role names
     */
    requireRole(allowedRoles) {
        if (!this.isAuthenticated()) {
            window.location.href = '/login';
            return false;
        }

        const rolesArray = Array.isArray(allowedRoles) ? allowedRoles : [allowedRoles];
        const hasPermission = rolesArray.some(role => this.hasRole(role));
        
        if (!hasPermission) {
            this.redirectToDashboard();
            return false;
        }

        return true;
    },

    /**
     * Get headers for API requests
     * @returns {object}
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
     * Logout user
     */
    logout() {
        this.clearAuth();
        window.location.href = '/login';
    }
};

// Expose to window for use in templates
if (typeof window !== 'undefined') {
    window.TokenManager = TokenManager;
}
