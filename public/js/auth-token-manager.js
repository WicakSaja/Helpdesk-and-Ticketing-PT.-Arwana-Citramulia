/**
 * Token Manager - Manage authentication token
 * Only stores token in localStorage
 */
const TokenManager = {
    // Storage key for token only
    STORAGE_TOKEN: 'user_token',

    /**
     * Set token (only token is stored)
     * @param {string} token - Authentication token
     * @returns {boolean} - True if saved successfully
     */
    setToken(token) {
        try {
            if (!token || typeof token !== 'string' || token.trim() === '') {
                console.error('Invalid token');
                return false;
            }

            localStorage.setItem(this.STORAGE_TOKEN, token);
            return true;
        } catch (error) {
            console.error('Error saving token:', error);
            return false;
        }
    },

    /**
     * Get token from localStorage
     * @returns {string|null} - The stored token or null
     */
    getToken() {
        return localStorage.getItem(this.STORAGE_TOKEN);
    },

    /**
     * Check if user is authenticated (has valid token)
     * @returns {boolean}
     */
    isAuthenticated() {
        return !!this.getToken();
    },

    /**
     * Clear token (logout)
     */
    clearToken() {
        localStorage.removeItem(this.STORAGE_TOKEN);
    },

    /**
     * Get default dashboard URL
     * @returns {string} - Default dashboard URL
     */
    getDashboardUrl() {
        // Return default dashboard, let backend handle role-based routing
        return '/dashboard';
    }
};

// Expose to window for use in templates
if (typeof window !== 'undefined') {
    window.TokenManager = TokenManager;
}
