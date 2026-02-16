document.addEventListener("DOMContentLoaded", function () {
  // === GLOBAL CONFIG ===
  const API_URL = typeof window.API_URL !== "undefined" ? window.API_URL : ""; // Fallback

  // === HELPER: Escape HTML ===
  function escapeHtml(text) {
    if (!text) return "";
    const map = {
      "&": "&amp;",
      "<": "&lt;",
      ">": "&gt;",
      '"': "&quot;",
      "'": "&#039;",
    };
    return String(text).replace(/[&<>"']/g, (m) => map[m]);
  }

  // === DOM ELEMENTS ===
  const tableBody = document.getElementById("ticketTableBody");
  const noData = document.getElementById("noDataMessage");
  const paginationControls = document.getElementById("paginationControls");
  const searchInput = document.getElementById("searchInput");

  // === CONFIG ===
  const rowsPerPage = 15; // Match API per_page
  const authToken =
    sessionStorage.getItem("auth_token") || localStorage.getItem("auth_token");
  // Cache only for modal details
  const _detailCache = new Map();

  let currentTickets = [];
  let paginationMeta = null;
  let currentPage = 1;
  let searchQuery = "";

  // === HELPER: Fetch Data ===
  async function fetchWithAuth(url) {
    const headers = {
      Authorization: `Bearer ${authToken}`,
      Accept: "application/json",
      "Content-Type": "application/json",
    };
    try {
      const response = await fetch(url, { headers });
      if (response.status === 401) {
        // Opsional: Redirect login
        return null;
      }
      return response;
    } catch (error) {
      console.error("Fetch Error:", error);
      return null;
    }
  }

  // === 1. LOAD DATA WITH SERVER-SIDE PAGINATION ===
  async function loadTickets(page = 1) {
    currentPage = page;
    tableBody.innerHTML =
      '<tr><td colspan="6" class="loading-row"><i class="fa-solid fa-spinner fa-spin"></i> Memuat data tiket...</td></tr>';
    if (noData) noData.style.display = "none";

    try {
      const searchParam = searchQuery
        ? `&search=${encodeURIComponent(searchQuery)}`
        : "";
      const apiUrl = `${API_URL}/api/tickets?page=${page}&per_page=${rowsPerPage}${searchParam}`;
      const res = await fetchWithAuth(apiUrl);

      if (!res) {
        throw new Error("Network request failed (res is null)");
      }
      if (!res.ok) {
        throw new Error(`HTTP ${res.status}: ${res.statusText}`);
      }

      const json = await res.json();
      const rawData = json.data || [];
      paginationMeta = json.pagination || json.meta || null;

      currentTickets = rawData.map((t) => ({
        id: t.id,
        ticket_number: t.ticket_number || `T-${t.id}`,
        subject: t.title || t.subject || "-",
        requester: t.requester?.name || t.requester_name || "-",
        dept: t.requester?.department?.name || t.department?.name || "-",
        tech: t.assignment?.technician?.name || "-",
        tech_dept: t.assignment?.technician?.department?.name || null,
        status: t.status?.name || t.status || "Pending",
        date: t.created_at || t.date,
        raw: t,
      }));

      renderTable();
    } catch (e) {
      console.error("Load Error:", e);
      tableBody.innerHTML = `<tr><td colspan="6" style="text-align:center; padding:30px; color:#d62828;"><i class="fa-solid fa-circle-exclamation"></i> <br />Gagal memuat data tiket.<br /><small style="font-size:11px; color:#999;">${escapeHtml(e.message)}</small></td></tr>`;
    }
  }

  // === 2. RENDER TABLE (Direct from API data) ===
  function renderTable() {
    tableBody.innerHTML = "";

    if (currentTickets.length === 0) {
      if (noData) noData.style.display = "block";
      paginationControls.innerHTML = "";
      return;
    }

    if (noData) noData.style.display = "none";

    currentTickets.forEach((t) => {
      let statusClass = "status-pending";
      const s = String(t.status).toLowerCase();
      if (s.includes("open")) statusClass = "status-open";
      else if (s.includes("assigned")) statusClass = "status-pending";
      else if (s.includes("progress")) statusClass = "status-progress";
      else if (s.includes("resolved")) statusClass = "status-resolved";
      else if (s.includes("close")) statusClass = "status-closed";
      else if (s.includes("reject")) statusClass = "status-rejected";

      // Teknisi badge (fa-screwdriver-wrench)
      const techDisplay = t.tech_dept
        ? `<i class='fa-solid fa-screwdriver-wrench' style='margin-right:4px;'></i>${escapeHtml(t.tech)} <span style='color:#888;font-size:11px;'>(<i class='fa-solid fa-building' style='margin-right:4px;'></i> ${escapeHtml(t.tech_dept)})</span>`
        : `<i class='fa-solid fa-screwdriver-wrench' style='margin-right:4px;'></i>${escapeHtml(t.tech)}`;

      const row = `
            <tr id="row-${t.id}">
              <td><i class='fa-solid fa-ticket' style='margin-right:4px;'></i>${escapeHtml(t.ticket_number)}</td>
              <td>
                <div style="font-weight:600; color:#333;">${escapeHtml(t.subject)}</div>
                <div style="font-size:12px; color:#888;">
                  <i class='fa-solid fa-user' style='margin-right:4px;'></i>${escapeHtml(t.requester)}
                </div>
              </td>
              <td><i class='fa-solid fa-building' style='margin-right:4px;'></i>${escapeHtml(t.dept)}</td>
              <td>${techDisplay}</td>
              <td><span class="status-badge ${statusClass}">${escapeHtml(t.status)}</span></td>
              <td style="text-align: right;">
                <button class="btn-view" onclick="openDetailById(${t.id})" title="Lihat Detail">
                  <i class="fa-solid fa-eye"></i>
                </button>
              </td>
            </tr>
          `;
      tableBody.innerHTML += row;
    });

    renderPaginationControls();
  }

  // === 4. PAGINATION CONTROLS (Server-side - Arwana Theme) ===
  function renderPaginationControls() {
    const container = document.getElementById("paginationControls");
    if (!container) return;
    container.innerHTML = "";

    if (!paginationMeta) {
      container.parentElement.style.display = "none";
      return;
    }

    const { current_page, last_page, total, from, to } = paginationMeta;

    if (last_page <= 1) {
      container.parentElement.style.display = "none";
      return;
    }

    // Info text
    const infoEl = document.getElementById("paginationInfo");
    if (infoEl) {
      infoEl.innerHTML = `Menampilkan <strong>${from || 0}</strong> hingga <strong>${to || 0}</strong> dari <strong>${total || 0}</strong> data`;
    }

    // Previous button
    let html = `<button type="button" class="pagination-btn" data-page="prev" ${current_page === 1 ? "disabled" : ""}>
      <i class="fa-solid fa-chevron-left"></i>
    </button>`;

    // Page buttons (max 5 visible)
    const maxButtons = 5;
    let startPage = Math.max(1, current_page - Math.floor(maxButtons / 2));
    let endPage = Math.min(last_page, startPage + maxButtons - 1);
    if (endPage - startPage + 1 < maxButtons) {
      startPage = Math.max(1, endPage - maxButtons + 1);
    }

    if (startPage > 1) {
      html += `<button type="button" class="pagination-btn" data-page="1">1</button>`;
      if (startPage > 2) {
        html += `<span class="pagination-btn" style="cursor:default; border:none; padding:0;">...</span>`;
      }
    }

    for (let i = startPage; i <= endPage; i++) {
      html += `<button type="button" class="pagination-btn ${i === current_page ? "active" : ""}" data-page="${i}">${i}</button>`;
    }

    if (endPage < last_page) {
      if (endPage < last_page - 1) {
        html += `<span class="pagination-btn" style="cursor:default; border:none; padding:0;">...</span>`;
      }
      html += `<button type="button" class="pagination-btn" data-page="${last_page}">${last_page}</button>`;
    }

    // Next button
    html += `<button type="button" class="pagination-btn" data-page="next" ${current_page === last_page ? "disabled" : ""}>
      <i class="fa-solid fa-chevron-right"></i>
    </button>`;

    container.innerHTML = html;
    container.parentElement.style.display = "flex";

    // Bind pagination clicks
    container.querySelectorAll(".pagination-btn[data-page]").forEach((btn) => {
      btn.addEventListener("click", function () {
        const p = this.getAttribute("data-page");
        let newPage = current_page;
        if (p === "prev") newPage = Math.max(1, current_page - 1);
        else if (p === "next") newPage = Math.min(last_page, current_page + 1);
        else newPage = Number(p);

        if (newPage !== current_page) {
          loadTickets(newPage);
        }
      });
    });
  }

  // === HELPER: No longer needed - removed createBtn ===

  // === 5. SEARCH (Client-side for current page, or trigger reload for server-side) ===
  if (searchInput) {
    let searchTimeout;
    searchInput.addEventListener("input", function (e) {
      const q = e.target.value.trim();

      // Debounce search to avoid too many requests
      clearTimeout(searchTimeout);
      searchTimeout = setTimeout(() => {
        searchQuery = q;
        loadTickets(1); // Reload from page 1 with search query
      }, 500); // Wait 500ms after user stops typing
    });
  }

  // === 6. DETAIL PAGE REDIRECT ===
  window.openDetailById = function (id) {
    window.location.href = `/helpdesk/tickets/${id}`;
  };

  function renderTimeline(logs, currentStatus) {
    const container = document.getElementById("mTimeline");

    let html = "";

    if (!logs || logs.length === 0) {
      html =
        '<div style="color:#999; font-style:italic; padding: 15px; text-align: center;">Belum ada riwayat detail tersedia.</div>';
    } else {
      // Sort logs by date ascending
      const sortedLogs = [...logs].sort((a, b) => {
        const dateA = new Date(a.created_at || a.date || 0).getTime();
        const dateB = new Date(b.created_at || b.date || 0).getTime();
        return dateA - dateB;
      });

      sortedLogs.forEach((l) => {
        const logStatus = (
          l.status?.name ||
          l.status ||
          l.action ||
          "Update"
        ).toUpperCase();
        const date = new Date(l.created_at || l.date);
        const dateStr = date.toLocaleDateString("id-ID", {
          weekday: "short",
          year: "numeric",
          month: "short",
          day: "numeric",
        });
        const timeStr = date.toLocaleTimeString("id-ID", {
          hour: "2-digit",
          minute: "2-digit",
        });

        // Color code for different statuses
        let statusColor = "#999";
        if (logStatus.includes("OPEN")) statusColor = "#1976d2";
        else if (logStatus.includes("ASSIGNED")) statusColor = "#f57c00";
        else if (logStatus.includes("PROGRESS")) statusColor = "#7b1fa2";
        else if (logStatus.includes("RESOLVED")) statusColor = "#388e3c";
        else if (logStatus.includes("CLOSED")) statusColor = "#424242";

        html += `
          <div class="timeline-item" style="margin-bottom: 15px; padding-left: 30px; position: relative;">
            <div style="position: absolute; left: 0; top: 3px; width: 12px; height: 12px; background-color: ${statusColor}; border-radius: 50%; border: 2px solid white; box-shadow: 0 0 0 2px ${statusColor};\"></div>
            <div>
              <div style="font-weight: 600; color: ${statusColor}; font-size: 13px;">${logStatus}</div>
              <div style="font-size: 12px; color: #999; margin-top: 3px;">${dateStr} • ${timeStr}</div>
              <div style="margin-top: 6px; color: #666; font-size: 13px;">${escapeHtml(l.note || l.message || l.description || "")}</div>
            </div>
          </div>
        `;
      });
    }

    container.innerHTML = html;
  }

  window.closeModal = function () {
    document.getElementById("detailModal").style.display = "none";
  };

  window.rejectTicket = async function (ticketId) {
    const detail = window._currentTicketDetail;
    if (!detail) return;

    const currentStatus = detail.status?.name || detail.status || "";
    if (currentStatus.toUpperCase() !== "RESOLVED") {
      alert("Tiket hanya bisa di-reject jika status RESOLVED");
      return;
    }

    if (
      !confirm(
        `Yakin ingin reject tiket #${detail.ticket_number}? Tiket akan kembali ke status OPEN.`,
      )
    ) {
      return;
    }

    try {
      const res = await fetchWithAuth(
        `${API_URL}/api/tickets/${ticketId}/reject`,
        {
          method: "POST",
          headers: {
            Authorization: `Bearer ${authToken}`,
            Accept: "application/json",
            "Content-Type": "application/json",
          },
        },
      );

      if (res && res.ok) {
        alert("Tiket berhasil di-reject dan kembali ke status OPEN");
        closeModal();
        loadTickets();
      } else {
        alert("Gagal reject tiket");
      }
    } catch (e) {
      console.error("Reject error:", e);
      alert("Error: " + e.message);
    }
  };

  window.closeTicket = async function (ticketId) {
    const detail = window._currentTicketDetail;
    if (!detail) return;

    const currentStatus = detail.status?.name || detail.status || "";
    if (currentStatus.toUpperCase() !== "RESOLVED") {
      alert("Tiket hanya bisa di-close jika status RESOLVED");
      return;
    }

    if (!confirm(`Yakin ingin close tiket #${detail.ticket_number}?`)) {
      return;
    }

    try {
      const res = await fetchWithAuth(
        `${API_URL}/api/tickets/${ticketId}/close`,
        {
          method: "POST",
          headers: {
            Authorization: `Bearer ${authToken}`,
            Accept: "application/json",
            "Content-Type": "application/json",
          },
        },
      );

      if (res && res.ok) {
        alert("Tiket berhasil di-close");
        closeModal();
        loadTickets();
      } else {
        alert("Gagal close tiket");
      }
    } catch (e) {
      console.error("Close error:", e);
      alert("Error: " + e.message);
    }
  };

  window.onclick = function (e) {
    const modal = document.getElementById("detailModal");
    if (e.target == modal) closeModal();
  };

  // INIT
  loadTickets();
});
