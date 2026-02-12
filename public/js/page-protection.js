/**
 * Page Protection Script
 * Protect dashboard pages and handle role-based access
 */

document.addEventListener("DOMContentLoaded", async function () {
  // Skip protection for login/register pages and email verification pages
  const currentPath = window.location.pathname;
  const guestPages = ["/login", "/register", "/forgot-password", "/email/verify-result", "/email/verify-reminder"];

  // Check if current page is a guest page (should not be protected)
  if (guestPages.some((page) => currentPath.includes(page))) {
    console.log("Guest/verification page detected, skipping protection");
    return;
  }

  // Check if user is authenticated
  const authenticated = await TokenManager.isAuthenticated();
  const rolesValid = await TokenManager.validateRoles();
  if (!authenticated) {
    console.log("User not authenticated, redirecting to login...");
    window.location.href = "/login";
    return;
  }

  if (!rolesValid) {
    console.log("User roles invalid, redirecting to login...");
    window.location.href = "/login";
    return;
  }

  // Check email verification status (skip if verification is disabled)
  const emailVerificationRequired = JSON.parse(sessionStorage.getItem('email_verification_required') ?? 'true');
  const user = TokenManager.getUser();
  if (emailVerificationRequired && user && !user.email_verified_at) {
    console.log("Email not verified, redirecting to verification reminder...");
    window.location.href = "/email/verify-reminder";
    return;
  }

  // Display user info if elements exist
  displayUserInfo();

  // Setup logout buttons
  setupLogoutButtons();

  // Periodic revalidation to catch token changes after page load
  startAuthRevalidation();
});

const AUTH_CHECK_INTERVAL_MS = 5 * 60 * 1000; // 5 minutes
let authCheckRunning = false;

async function runAuthCheck() {
  if (authCheckRunning) return;
  authCheckRunning = true;

  try {
    const authenticated = await TokenManager.isAuthenticated();
    const rolesValid = authenticated
      ? await TokenManager.validateRoles()
      : false;

    if (!authenticated || !rolesValid) {
      console.log("Auth check failed, redirecting to login...");
      TokenManager.logout();
      return;
    }

    // Check email verification during revalidation (skip if verification is disabled)
    const emailVerificationRequired = JSON.parse(sessionStorage.getItem('email_verification_required') ?? 'true');
    const user = TokenManager.getUser();
    if (emailVerificationRequired && user && !user.email_verified_at) {
      console.log("Email not verified, redirecting to verification reminder...");
      window.location.href = "/email/verify-reminder";
      return;
    }
  } finally {
    authCheckRunning = false;
  }
}

function startAuthRevalidation() {
  runAuthCheck();

  setInterval(runAuthCheck, AUTH_CHECK_INTERVAL_MS);

  window.addEventListener("focus", runAuthCheck);
  document.addEventListener("visibilitychange", () => {
    if (document.visibilityState === "visible") {
      runAuthCheck();
    }
  });
}

/**
 * Synchronous check for guest pages (use only for login/register pages)
 * Returns true if user is NOT authenticated (safe to display guest page)
 */
TokenManager.isGuestSafe = function () {
  // Quick check - if no token, it's safe to show guest page
  return !this.hasToken();
};

/**
 * Display user information in the UI
 */
function displayUserInfo() {
  const user = TokenManager.getUser();
  const roles = TokenManager.getRoles();

  if (!user) return;

  // Update user name displays
  const userNameElements = document.querySelectorAll(
    ".user-name, #userName, [data-user-name]",
  );
  userNameElements.forEach((el) => {
    el.textContent = user.name || "User";
  });

  // Update user email displays
  const userEmailElements = document.querySelectorAll(
    ".user-email, #userEmail, [data-user-email]",
  );
  userEmailElements.forEach((el) => {
    el.textContent = user.email || "";
  });

  // Update role badge displays
  if (roles && roles.length > 0) {
    const roleElements = document.querySelectorAll(
      ".user-role, #userRole, [data-user-role]",
    );
    const roleName = roles[0].name || roles[0];
    const roleDisplay = formatRoleName(roleName);

    roleElements.forEach((el) => {
      el.textContent = roleDisplay;
    });
  }
}

/**
 * Format role name for display
 */
function formatRoleName(roleName) {
  const roleMap = {
    "master-admin": "Super Admin",
    helpdesk: "Helpdesk",
    technician: "Technician",
    requester: "Requester",
  };

  return roleMap[roleName] || roleName;
}

/**
 * Setup logout button event listeners
 */
function setupLogoutButtons() {
  const logoutButtons = document.querySelectorAll(
    "[data-logout], .btn-logout, #btnLogout",
  );

  logoutButtons.forEach((btn) => {
    btn.addEventListener("click", handleLogout);
  });
}

/**
 * Handle logout action
 */
async function handleLogout(event) {
  event.preventDefault();

  const result = await Swal.fire({
    icon: "question",
    title: "Konfirmasi Logout",
    text: "Apakah Anda yakin ingin keluar?",
    showCancelButton: true,
    confirmButtonText: "Ya, Keluar",
    cancelButtonText: "Batal",
    confirmButtonColor: "#d62828",
    cancelButtonColor: "#6c757d",
  });

  if (result.isConfirmed) {
    // Call API logout endpoint (optional)
    const token = TokenManager.getToken();
    if (token) {
      try {
        const apiUrl = typeof API_URL !== 'undefined' ? API_URL : window.location.origin;
        await fetch(`${apiUrl}/api/logout`, {
          method: "POST",
          headers: TokenManager.getHeaders(),
        });
      } catch (error) {
        console.error("Logout API error:", error);
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
    headers: TokenManager.getHeaders(),
  };

  try {
    const response = await fetch(url, { ...defaultOptions, ...options });

    // Handle 401 Unauthorized
    if (response.status === 401) {
      Swal.fire({
        icon: "error",
        title: "Sesi Berakhir",
        text: "Silakan login kembali",
        confirmButtonColor: "#d62828",
      }).then(() => {
        TokenManager.logout();
      });
      return null;
    }

    return response;
  } catch (error) {
    console.error("Fetch error:", error);
    throw error;
  }
}
