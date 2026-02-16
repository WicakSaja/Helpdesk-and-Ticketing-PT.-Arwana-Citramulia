/**
 * Role-Specific Page Protection
 * Protects pages based on the user's active role (selected during login)
 */

/**
 * Require Guest (Sync version for login/register pages)
 * Use this on login/register pages to redirect if already logged in.
 * Checks both token AND role data to prevent redirect loops when session is partial/corrupted.
 */
function requireGuestSync() {
    // Check token AND role — if token exists but role is missing/invalid, clear auth data only
    if (TokenManager.hasToken()) {
        const activeRole = TokenManager.getActiveRole();
        const knownRoles = ['master-admin', 'helpdesk', 'technician', 'requester'];

        if (activeRole && knownRoles.includes(activeRole)) {
            console.log('User already authenticated with valid role, redirecting to dashboard...');
            TokenManager.redirectToDashboard();
            return false;
        }

        // Token exists but role data is missing/invalid — clear auth keys to break loop
        console.warn('Token exists but active role is missing or invalid. Clearing auth data.');
        TokenManager.clearAuth();
    }
    return true;
}

/**
 * Require Requester Role (default user)
 */
async function requireRequesterRole() {
    const authenticated = await TokenManager.requireAuth();
    if (!authenticated) return;
    
    const activeRole = TokenManager.getActiveRole();
    const allowedRoles = ['requester', 'master-admin'];
    
    if (!allowedRoles.includes(activeRole)) {
        TokenManager.redirectToDashboard();
    }
}

/**
 * Require Technician Role
 */
async function requireTechnicianRole() {
    const authenticated = await TokenManager.requireAuth();
    if (!authenticated) return;
    
    const activeRole = TokenManager.getActiveRole();
    const allowedRoles = ['technician', 'master-admin'];

    if (!allowedRoles.includes(activeRole)) {
        TokenManager.redirectToDashboard();
        return false;
    }
    return true;
}

/**
 * Require Helpdesk Role
 */
async function requireHelpdeskRole() {
    const authenticated = await TokenManager.requireAuth();
    if (!authenticated) return;
    
    const activeRole = TokenManager.getActiveRole();
    const allowedRoles = ['helpdesk', 'master-admin'];
    
    if (!allowedRoles.includes(activeRole)) {
        TokenManager.redirectToDashboard();
        return false;
    }
    return true;
}

/**
 * Require Master Admin Role
 */
async function requireMasterAdminRole() {
    const authenticated = await TokenManager.requireAuth();
    if (!authenticated) return;
    
    const activeRole = TokenManager.getActiveRole();
    
    if (activeRole !== 'master-admin') {
        TokenManager.redirectToDashboard();
        return false;
    }
    return true;
}

/**
 * Check Guest (redirect if authenticated)
 */
async function requireGuest() {
    await TokenManager.requireGuest();
}
