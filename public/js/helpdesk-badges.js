// Global badge updater for Helpdesk sidebar + header
window.updateOpenUnassignedCount = async function () {
  try {
    const res = await fetchWithAuth(
      `${API_URL}/api/tickets?status=open&per_page=100`,
    );
    if (!res || !res.ok) return;
    const json = await res.json();
    let items = [];
    if (Array.isArray(json)) items = json;
    else if (json.data && Array.isArray(json.data)) items = json.data;
    else items = json;

    const unassignedCount = items.filter(
      (t) =>
        !t.assignment &&
        !t.assigned_to &&
        !(t.assignment && t.assignment.assigned_to),
    ).length;

    // update any header alert if present
    const alertSpan = document.querySelector(".alert-badge span");
    if (alertSpan)
      alertSpan.innerText = `${unassignedCount} Tiket Perlu Tindakan`;

    // update sidebar badge
    const menuBadge = document.querySelector(".menu-badge");
    if (menuBadge) menuBadge.innerText = `${unassignedCount}`;

    return unassignedCount;
  } catch (e) {
    console.warn("helpdesk-badges update error", e);
    return null;
  }
};

// Initial run and periodic updates
document.addEventListener("DOMContentLoaded", function () {
  if (typeof window.updateOpenUnassignedCount === "function") {
    window.updateOpenUnassignedCount();
    setInterval(() => window.updateOpenUnassignedCount(), 30000);
  }
});
