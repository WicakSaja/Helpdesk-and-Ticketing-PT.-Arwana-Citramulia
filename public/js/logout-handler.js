/**
 * Logout Handler - Handle user logout via API
 * Sends logout request to backend and clears local authentication data
 */

const LogoutHandler = {
    /**
     * Perform logout action
     * Hits API /logout endpoint with token, clears session, and redirects
     */
    async performLogout() {
        try {
            const token = TokenManager.getToken();
            
            if (!token) {
                console.warn('No token found, redirecting to login');
                this.cleanupAndRedirect();
                return;
            }

            // Show loading state
            const loadingMessage = this.showLoading();

            try {
                // Hit logout endpoint
                const apiUrl = typeof API_URL !== 'undefined' ? API_URL : window.location.origin;
                const response = await fetch(`${apiUrl}/api/logout`, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'Authorization': `Bearer ${token}`
                    }
                });

                // Parse response
                const data = await response.json();

                if (response.ok) {
                    console.log('Logout successful:', data.message);
                    this.cleanupAndRedirect();
                } else {
                    console.warn('Logout API returned error, clearing local data anyway');
                    this.cleanupAndRedirect();
                }

            } catch (fetchError) {
                console.error('Logout API error:', fetchError);
                // Even if API fails, clear local data and redirect
                this.cleanupAndRedirect();
            }

        } catch (error) {
            console.error('Logout error:', error);
            this.cleanupAndRedirect();
        }
    },

    /**
     * Clear all authentication data and redirect to login
     */
    cleanupAndRedirect() {
        // Clear all auth data from sessionStorage
        TokenManager.clearAuth();
        
        // Redirect to login page
        window.location.href = '/login';
    },

    /**
     * Show loading message (optional)
     * @returns {void}
     */
    showLoading() {
        // If SweetAlert2 is available, show a loading modal
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Logging out...',
                text: 'Please wait',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
        } else {
            console.log('Logging out...');
        }
    },

    /**
     * Initialize logout handlers for all logout forms/buttons
     */
    init() {
        // Find all logout forms
        const logoutForms = document.querySelectorAll('form[action*="logout"]');
        
        logoutForms.forEach(form => {
            form.addEventListener('submit', (e) => {
                e.preventDefault(); // Prevent default form submission
                this.performLogout();
            });
        });

        // Also handle any logout buttons with data-logout attribute
        const logoutButtons = document.querySelectorAll('[data-logout]');
        logoutButtons.forEach(button => {
            button.addEventListener('click', (e) => {
                e.preventDefault();
                this.performLogout();
            });
        });
    }
};

// Auto-initialize when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    LogoutHandler.init();
});

// Export for manual usage if needed
if (typeof window !== 'undefined') {
    window.LogoutHandler = LogoutHandler;
}
