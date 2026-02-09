/**
 * ticket-detail.js
 * Shared JS untuk halaman detail tiket (helpdesk, requester, technician, superadmin)
 *
 * Requires global variables set by blade:
 *   - TICKET_ID (string)
 *   - API_URL_BASE (string) e.g. "http://localhost:8000/api/tickets"
 *   - LOGIN_URL (string) e.g. "/login"
 */
(function () {
  // Action label mapping — konsisten dengan TicketCrudService
  const ACTION_LABELS = {
    open: "Open",
    assigned: "Assigned",
    in_progress: "In Progress",
    rejected: "Rejected",
    resolved: "Resolved",
    unresolved: "Unresolved",
    closed: "Closed",
  };

  // Action color mapping untuk timeline marker
  const ACTION_COLORS = {
    open: "#d62828",
    assigned: "#f59f00",
    in_progress: "#206bc4",
    rejected: "#d62828",
    resolved: "#198754",
    unresolved: "#f59f00",
    closed: "#64748b",
  };

  // Status badge mapping
  const STATUS_BADGES = {
    open: { class: "bg-red-solid", label: "OPEN" },
    assigned: { class: "bg-yellow-solid", label: "ASSIGNED" },
    "in progress": { class: "bg-blue-solid", label: "IN PROGRESS" },
    in_progress: { class: "bg-blue-solid", label: "IN PROGRESS" },
    resolved: { class: "bg-green-solid", label: "RESOLVED" },
    closed: { class: "bg-grey-solid", label: "CLOSED" },
  };

  function getAuthToken() {
    return (
      sessionStorage.getItem("auth_token") || localStorage.getItem("auth_token")
    );
  }

  async function loadTicketDetail() {
    const token = getAuthToken();
    if (!token) {
      window.location.href =
        typeof LOGIN_URL !== "undefined" ? LOGIN_URL : "/login";
      return;
    }

    try {
      const res = await fetch(`${API_URL_BASE}/${TICKET_ID}`, {
        headers: {
          Authorization: `Bearer ${token}`,
          Accept: "application/json",
        },
      });

      if (!res.ok) throw new Error("Gagal mengambil data tiket.");

      const json = await res.json();
      const ticket = json.data || json.ticket || json;
      renderTicket(ticket);
    } catch (error) {
      console.error(error);
      const descEl = document.getElementById("ticket-description");
      if (descEl) {
        descEl.innerHTML = `<div class="text-danger">Gagal memuat data: ${error.message}</div>`;
      }
    }
  }

  function renderTicket(t) {
    // Header
    const idDisplay = document.getElementById("ticket-id-display");
    if (idDisplay) idDisplay.innerText = `#${t.ticket_number || t.id}`;

    const subjectEl = document.getElementById("ticket-subject");
    if (subjectEl) subjectEl.innerText = t.subject || "(Tanpa Subjek)";

    // Deskripsi (XSS safe)
    const descEl = document.getElementById("ticket-description");
    if (descEl) {
      const rawDesc = t.description || "Tidak ada deskripsi.";
      const safeDesc = rawDesc
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/\n/g, "<br>");
      descEl.innerHTML = safeDesc;
    }

    // Sidebar — Requester
    const reqName = t.requester?.name || t.requester_name || "User";
    const reqEl = document.getElementById("ticket-requester");
    if (reqEl) reqEl.innerText = reqName;

    const reqInitial = document.getElementById("req-initial");
    if (reqInitial) reqInitial.innerText = reqName.charAt(0).toUpperCase();

    const deptEl = document.getElementById("ticket-dept");
    if (deptEl)
      deptEl.innerText =
        t.requester?.department?.name || t.department?.name || "";

    // No. Telepon Requester
    const phoneEl = document.getElementById("ticket-requester-phone");
    if (phoneEl) {
      const phone = t.requester?.phone || "";
      phoneEl.innerText = phone ? phone : "-";
    }

    // Email Requester
    const emailEl = document.getElementById("ticket-requester-email");
    if (emailEl) {
      const email = t.requester?.email || "";
      emailEl.innerText = email ? email : "-";
    }

    // Kategori
    const catEl = document.getElementById("ticket-category");
    if (catEl) catEl.innerText = t.category?.name || "-";

    // Tanggal
    const dateEl = document.getElementById("ticket-created-at");
    if (dateEl && t.created_at) {
      const d = new Date(t.created_at);
      dateEl.innerText = d.toLocaleDateString("id-ID", {
        day: "numeric",
        month: "long",
        year: "numeric",
        hour: "2-digit",
        minute: "2-digit",
      });
    }

    // Teknisi
    const techName = t.technician?.name || t.assignment?.technician?.name;
    const agentEl = document.getElementById("ticket-agent");
    if (agentEl) {
      if (techName) {
        agentEl.innerHTML = `<div class="d-flex align-items-center gap-2 p-3 rounded" style="background:#f0fdf4; border:1px solid #bbf7d0;">
                        <i class="fe fe-check-circle text-success fs-4"></i>
                        <span class="fw-bold text-dark">${techName}</span>
                     </div>`;
      } else {
        agentEl.innerHTML = `<div class="text-muted small fst-italic border rounded p-2 text-center bg-light">Belum ditugaskan</div>`;
      }
    }

    // Status Badge
    const statusName = (t.status?.name || t.status || "open").toLowerCase();
    const badgeInfo = STATUS_BADGES[statusName] || {
      class: "bg-grey-solid",
      label: statusName.toUpperCase(),
    };
    const badgeEl = document.getElementById("ticket-status-badge");
    if (badgeEl) {
      badgeEl.innerHTML = `<span class="badge-status-lg ${badgeInfo.class}">${badgeInfo.label}</span>`;
    }

    // Timeline
    renderTimeline(t.logs || t.histories || []);
  }

  function renderTimeline(logs) {
    const container = document.getElementById("ticket-timeline");
    if (!container) return;

    if (!logs || logs.length === 0) {
      container.innerHTML = `<div class="text-center py-3 text-muted small">Belum ada aktivitas.</div>`;
      return;
    }

    // Urutkan dari terbaru
    logs.sort((a, b) => new Date(b.created_at) - new Date(a.created_at));

    let html = '<div class="timeline">';
    logs.forEach(function (log) {
      const date = new Date(log.created_at);
      const dateStr = date.toLocaleDateString("id-ID", {
        day: "numeric",
        month: "short",
        hour: "2-digit",
        minute: "2-digit",
      });

      const actionRaw = (log.action || log.status || "update").toLowerCase();
      const actionLabel =
        ACTION_LABELS[actionRaw] ||
        actionRaw.replace(/_/g, " ").replace(/\b\w/g, (c) => c.toUpperCase());
      const color = ACTION_COLORS[actionRaw] || "#206bc4";

      html += `
                <div class="timeline-item">
                    <div class="timeline-marker" style="border-color: ${color};"></div>
                    <div class="timeline-content">
                        <span class="timeline-time">${dateStr}</span>
                        <div class="timeline-title" style="color:${color}">${actionLabel}</div>
                        <div class="timeline-desc">${log.description || log.note || "-"}</div>
                    </div>
                </div>`;
    });
    html += "</div>";
    container.innerHTML = html;
  }

  // Init on DOMContentLoaded
  document.addEventListener("DOMContentLoaded", loadTicketDetail);
})();
