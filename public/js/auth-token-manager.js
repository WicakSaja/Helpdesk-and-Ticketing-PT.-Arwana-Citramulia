/*
 * Token Manager - Manage authentication token and user data
 * Handles token, user data, and roles in sessionStorage
 */
window.TokenManager = window.TokenManager || {
    // Storage keys
    STORAGE_TOKEN: 'auth_token',
    STORAGE_USER: 'auth_user',
    STORAGE_ROLES: 'auth_roles',
    STORAGE_EMAIL_VERIFICATION: 'email_verification_required',

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
     * Check if user is authenticated by validating token with API
     * Clears session if token is invalid or expired
     * @returns {Promise<boolean>}
     */

    
    async isAuthenticated() {
        const token = this.getToken();
        
        if (!token) {
            return false;
        }

        try {
            const apiUrl = typeof API_URL !== 'undefined' ? API_URL : window.location.origin;
            const response = await fetch(`${apiUrl}/api/validate-token`, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${token}`
                }
            });

            if (!response.ok) {
                // Token invalid or expired - clear session
                console.warn('Token validation failed with status:', response.status);
                this.clearAuth();
                return false;
            }

            const data = await response.json();
            
            if (data.valid === true) {
                return true;
            } else {
                // Token is marked as invalid - clear session
                console.warn('Token marked as invalid by server');
                this.clearAuth();
                return false;
            }
        } catch (error) {
            console.error('Token validation error:', error);
            // Clear session on network error to be safe
            this.clearAuth();
            return false;
        }
    },

    

    /**
     * Check if token exists in storage (synchronous check)
     * @returns {boolean}
     */
    hasToken() {
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
     * Validate and sync roles/user data with API
     * Fetches latest user data from API and updates session (API is source of truth)
     * @returns {Promise<boolean>} - True if valid, false otherwise
     */
    async validateRoles() {
        const token = this.getToken();
        
        if (!token) {
            console.warn('No token found in session');
            return false;
        }

        try {
            const apiUrl = typeof API_URL !== 'undefined' ? API_URL : window.location.origin;
            const response = await fetch(`${apiUrl}/api/me`, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${token}`
                }
            });

            if (!response.ok) {
                console.warn('Failed to fetch user data from API, status:', response.status);
                this.clearAuth();
                return false;
            }

            const data = await response.json();
            const apiRoles = data.roles || [];
            const apiUser = data.user || null;

            if (!apiUser || apiUser.id == null) {
                console.warn('Invalid user data from API');
                this.clearAuth();
                return false;
            }

            // API is source of truth — update session with latest data
            sessionStorage.setItem(this.STORAGE_USER, JSON.stringify(apiUser));
            sessionStorage.setItem(this.STORAGE_ROLES, JSON.stringify(apiRoles));

            // Store email verification required flag
            if (data.email_verification_required !== undefined) {
                sessionStorage.setItem(this.STORAGE_EMAIL_VERIFICATION, JSON.stringify(data.email_verification_required));
            }

            return true;
        } catch (error) {
            console.error('Error validating roles:', error);
            return false;
        }
    },

    /**
     * Clear all auth data (logout)
     */
    clearAuth() {
        sessionStorage.removeItem(this.STORAGE_TOKEN);
        sessionStorage.removeItem(this.STORAGE_USER);
        sessionStorage.removeItem(this.STORAGE_ROLES);
        sessionStorage.removeItem(this.STORAGE_EMAIL_VERIFICATION);
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
        } else if (this.hasRole('helpdesk')) {
            window.location.href = '/dashboard/helpdesk';
        } else if (this.hasRole('technician')) {
            window.location.href = '/dashboard/technician';
        } else if (this.hasRole('requester')) {
            window.location.href = '/dashboard/requester';
        } else {
            // Default ke login
            window.location.href = '/login';
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
        } else if (this.hasRole('helpdesk')) {
            return '/dashboard/helpdesk';
        } else if (this.hasRole('technician')) {
            return '/dashboard/technician';
        } else if (this.hasRole('requester')) {
            return '/dashboard/requester';
        } else {
            return '/login';
        }
    },

    /**
     * Protect page - redirect to login if not authenticated
     */
    async requireAuth() {
        const authenticated = await this.isAuthenticated();
        if (!authenticated) {
            window.location.href = '/login';
            return false;
        }
        return true;
    },

    /**
     * Protect guest pages - redirect to dashboard if authenticated
     */
    async requireGuest() {
        const authenticated = await this.isAuthenticated();
        if (authenticated) {
            this.redirectToDashboard();
            return false;
        }
        return true;
    },

    /**
     * Require specific role(s)
     * @param {array|string} allowedRoles - Role name or array of role names
     */
    async requireRole(allowedRoles) {
        const authenticated = await this.isAuthenticated();
        if (!authenticated) {
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
