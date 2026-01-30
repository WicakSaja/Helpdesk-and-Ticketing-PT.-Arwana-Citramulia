/**
 * Role-Specific Page Protection
 * Protects pages that require specific roles
 */

/**
 * Require Requester Role (default user)
 */
function requireRequesterRole() {
    if (!TokenManager.requireAuth()) return;
    
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
function requireTechnicianRole() {
    if (!TokenManager.requireAuth()) return;
    
    const allowedRoles = ['technician', 'supervisor', 'master-admin'];
    if (!TokenManager.requireRole(allowedRoles)) {
        return false;
    }
    return true;
}

/**
 * Require Helpdesk Role
 */
function requireHelpdeskRole() {
    if (!TokenManager.requireAuth()) return;
    
    const allowedRoles = ['helpdesk', 'supervisor', 'master-admin'];
    if (!TokenManager.requireRole(allowedRoles)) {
        return false;
    }
    return true;
}

/**
 * Require Supervisor Role
 */
function requireSupervisorRole() {
    if (!TokenManager.requireAuth()) return;
    
    const allowedRoles = ['supervisor', 'master-admin'];
    if (!TokenManager.requireRole(allowedRoles)) {
        return false;
    }
    return true;
}

/**
 * Require Master Admin Role
 */
function requireMasterAdminRole() {
    if (!TokenManager.requireAuth()) return;
    
    if (!TokenManager.requireRole(['master-admin'])) {
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
