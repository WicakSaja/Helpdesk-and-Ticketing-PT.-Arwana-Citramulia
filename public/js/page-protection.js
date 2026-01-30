/**
 * Page Protection Script
 * Protect dashboard pages and handle role-based access
 */

document.addEventListener('DOMContentLoaded', function() {
    // Check if user is authenticated
    if (!TokenManager.isAuthenticated()) {
        console.log('User not authenticated, redirecting to login...');
        window.location.href = '/login';
        return;
    }

    // Display user info if elements exist
    displayUserInfo();
    
    // Setup logout buttons
    setupLogoutButtons();
});

/**
 * Display user information in the UI
 */
function displayUserInfo() {
    const user = TokenManager.getUser();
    const roles = TokenManager.getRoles();
    
    if (!user) return;

    // Update user name displays
    const userNameElements = document.querySelectorAll('.user-name, #userName, [data-user-name]');
    userNameElements.forEach(el => {
        el.textContent = user.name || 'User';
    });

    // Update user email displays
    const userEmailElements = document.querySelectorAll('.user-email, #userEmail, [data-user-email]');
    userEmailElements.forEach(el => {
        el.textContent = user.email || '';
    });

    // Update role badge displays
    if (roles && roles.length > 0) {
        const roleElements = document.querySelectorAll('.user-role, #userRole, [data-user-role]');
        const roleName = roles[0].name || roles[0];
        const roleDisplay = formatRoleName(roleName);
        
        roleElements.forEach(el => {
            el.textContent = roleDisplay;
        });
    }
}

/**
 * Format role name for display
 */
function formatRoleName(roleName) {
    const roleMap = {
        'master-admin': 'Super Admin',
        'supervisor': 'Supervisor',
        'helpdesk': 'Helpdesk',
        'technician': 'Teknisi',
        'requester': 'User'
    };
    
    return roleMap[roleName] || roleName;
}

/**
 * Setup logout button event listeners
 */
function setupLogoutButtons() {
    const logoutButtons = document.querySelectorAll('[data-logout], .btn-logout, #btnLogout');
    
    logoutButtons.forEach(btn => {
        btn.addEventListener('click', handleLogout);
    });
}

/**
 * Handle logout action
 */
async function handleLogout(event) {
    event.preventDefault();
    
    const result = await Swal.fire({
        icon: 'question',
        title: 'Konfirmasi Logout',
        text: 'Apakah Anda yakin ingin keluar?',
        showCancelButton: true,
        confirmButtonText: 'Ya, Keluar',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#d62828',
        cancelButtonColor: '#6c757d'
    });

    if (result.isConfirmed) {
        // Call API logout endpoint (optional)
        const token = TokenManager.getToken();
        if (token) {
            try {
                await fetch(`${API_URL}/api/logout`, {
                    method: 'POST',
                    headers: TokenManager.getHeaders()
                });
            } catch (error) {
                console.error('Logout API error:', error);
            }
        }

        // Clear local auth data
        TokenManager.logout();
    }
}

/**
 * Get API headers with auth token
 */
function getAuthHeaders() {
    return TokenManager.getHeaders();
}

/**
 * Make authenticated API request
 */
async function fetchWithAuth(url, options = {}) {
    const defaultOptions = {
        headers: TokenManager.getHeaders()
    };

    try {
        const response = await fetch(url, { ...defaultOptions, ...options });

        // Handle 401 Unauthorized
        if (response.status === 401) {
            Swal.fire({
                icon: 'error',
                title: 'Sesi Berakhir',
                text: 'Silakan login kembali',
                confirmButtonColor: '#d62828'
            }).then(() => {
                TokenManager.logout();
            });
            return null;
        }

        return response;
    } catch (error) {
        console.error('Fetch error:', error);
        throw error;
    }
}
