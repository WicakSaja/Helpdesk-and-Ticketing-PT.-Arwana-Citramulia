/**
 * Role-Specific Page Protection
 * Protects pages that require specific roles
 */

/**
 * Require Guest (Sync version for login/register pages)
 * Use this on login/register pages to redirect if already logged in
 */
function requireGuestSync() {
    // Use synchronous check only
    if (TokenManager.hasToken()) {
        console.log('User already authenticated, redirecting to dashboard...');
        TokenManager.redirectToDashboard();
        return false;
    }
    return true;
}

/**
 * Require Requester Role (default user)
 */
async function requireRequesterRole() {
    const authenticated = await TokenManager.requireAuth();
    if (!authenticated) return;
    
    const roles = TokenManager.getRoles();
    const allowedRoles = ['requester', 'master-admin'];
    const hasAccess = allowedRoles.some(role => TokenManager.hasRole(role));
    
    if (!hasAccess) {
        TokenManager.redirectToDashboard();
    }
}

/**
 * Require Technician Role
 */
async function requireTechnicianRole() {
    const authenticated = await TokenManager.requireAuth();
    if (!authenticated) return;
    
    const allowedRoles = ['technician', 'supervisor', 'master-admin'];
    const hasPermission = await TokenManager.requireRole(allowedRoles);
    if (!hasPermission) {
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
    
    const allowedRoles = ['helpdesk', 'supervisor', 'master-admin'];
    const hasPermission = await TokenManager.requireRole(allowedRoles);
    if (!hasPermission) {
        return false;
    }
    return true;
}

/**
 * Require Supervisor Role
 */
async function requireSupervisorRole() {
    const authenticated = await TokenManager.requireAuth();
    if (!authenticated) return;
    
    const allowedRoles = ['supervisor', 'master-admin'];
    const hasPermission = await TokenManager.requireRole(allowedRoles);
    if (!hasPermission) {
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
    
    const hasPermission = await TokenManager.requireRole(['master-admin']);
    if (!hasPermission) {
        return false;
    }
    return true;
}

/**
 * Check Guest (redirect if authenticated)
 */
function requireGuest() {
    TokenManager.requireGuest();
}
