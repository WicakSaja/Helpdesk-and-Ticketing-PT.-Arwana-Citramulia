(function () {
  const API =
    typeof API_URL !== "undefined"
      ? API_URL
      : window.location.origin.replace(/\/$/, "");

  const TICKET_PAGE_SIZE = 10;
  let _allTickets = []; // Semua tiket dari API
  let _myTickets = []; // Tiket setelah filter
  let _currentTicketPage = 1;
  let _currentStatusFilter = "";

  async function loadMyTickets() {
    const tbody = document.getElementById("ticketTableBody");
    if (!tbody) return;

    tbody.innerHTML = `<tr><td colspan="5" class="loading-cell"><i class="fa-solid fa-circle-notch fa-spin loading-icon"></i><p>Memuat riwayat tiket Anda...</p></td></tr>`;

    try {
      const headers =
        typeof TokenManager !== "undefined" &&
        typeof TokenManager.getHeaders === "function"
          ? TokenManager.getHeaders()
          : { "Content-Type": "application/json" };

      const res = await fetch(`${API}/api/my-tickets`, {
        method: "GET",
        headers: headers,
      });

      if (!res.ok) throw new Error("Gagal memuat riwayat tiket");

      const json = await res.json();
      const items = json.data || (Array.isArray(json) ? json : []);

      // Cache all tickets
      _allTickets = items || [];

      // Apply current filter immediately (no delay)
      applyStatusFilter();
    } catch (err) {
      console.error("loadMyTickets error", err);
      tbody.innerHTML = `<tr><td colspan="5" class="loading-cell" style="color:#d62828;"><i class="fa-solid fa-circle-exclamation" style="font-size:24px; margin-bottom:10px;"></i><p>Gagal memuat riwayat tiket.</p></td></tr>`;
    }
  }

  function applyStatusFilter() {
    const tbody = document.getElementById("ticketTableBody");
    if (!tbody) return;

    // Filter tickets by status (case-insensitive, exact match)
    if (_currentStatusFilter) {
      _myTickets = _allTickets.filter((ticket) => {
        const status =
          (ticket.status && (ticket.status.name || ticket.status)) || "";
        return status.toLowerCase() === _currentStatusFilter.toLowerCase();
      });
    } else {
      _myTickets = [..._allTickets];
    }

    // Reset to page 1 when filter changes
    _currentTicketPage = 1;

    if (_myTickets.length === 0) {
      const filterText = _currentStatusFilter
        ? ` dengan status "${_currentStatusFilter}"`
        : "";
      tbody.innerHTML = `<tr><td colspan="5" class="loading-cell"><i class="fa-solid fa-inbox" style="font-size:24px; color:#ddd; margin-bottom:10px;"></i><p>Belum ada tiket${filterText}.</p></td></tr>`;
      const pag = document.getElementById("ticketPagination");
      if (pag) {
        pag.innerHTML = "";
        pag.style.display = "none";
      }
      return;
    }

    renderTicketsPage(1);
    renderTicketPagination();
  }

  function renderTicketsPage(page) {
    const tbody = document.getElementById("ticketTableBody");
    if (!tbody) return;

    _currentTicketPage = page;
    const start = (page - 1) * TICKET_PAGE_SIZE;
    const pageItems = _myTickets.slice(start, start + TICKET_PAGE_SIZE);

    if (!pageItems.length) {
      tbody.innerHTML = `<tr><td colspan="5" class="loading-cell">Tidak ada tiket pada halaman ini.</td></tr>`;
      return;
    }

    let html = "";
    pageItems.forEach((ticket) => {
      const number =
        ticket.ticket_number ||
        `#TKT-${String(ticket.id || "").padStart(3, "0")}`;
      const subject = ticket.subject || "-";
      const category =
        (ticket.category && (ticket.category.name || ticket.category)) || "-";
      const status =
        (ticket.status && (ticket.status.name || ticket.status)) || "-";
      const when = ticket.updated_at || ticket.created_at || null;
      const updatedFormatted = when ? formatDateTime(when) : "-";

      // Map status to badge class
      const statusNormalized = (status || "").toLowerCase();
      let stClass = "st-open";
      if (/open/.test(statusNormalized)) stClass = "st-open";
      else if (/assigned/.test(statusNormalized)) stClass = "st-assigned";
      else if (/progress|in progress/.test(statusNormalized))
        stClass = "st-progress";
      else if (/resolved/.test(statusNormalized)) stClass = "st-resolved";
      else if (/close|closed/.test(statusNormalized)) stClass = "st-closed";

      // --- PERUBAHAN DISINI: MENGGUNAKAN <A HREF> BUKAN BUTTON ONCLICK ---
      html += `
        <tr>
          <td>
            <div class="subject-wrapper">
              <div class="ticket-no-mobile"><i class='fa-solid fa-ticket' style='margin-right:3px;color:#888;'></i>${escapeHtml(number)}</div>
              <div class="ticket-subject-text">${escapeHtml(subject)}</div>
            </div>
          </td>
          <td>${escapeHtml(category)}</td>
          <td><span class="status-badge ${stClass}">${escapeHtml(status)}</span></td>
          <td><i class='fa-regular fa-clock' style='margin-right:3px;color:#bbb;'></i>${escapeHtml(updatedFormatted)}</td>
          <td class="text-end">
            <a href="/tickets/${ticket.id}" class="btn-detail" style="text-decoration:none;">
              Lihat <i class="fa-solid fa-chevron-right" style="font-size: 10px; margin-left: 5px;"></i>
            </a>
          </td> 
        </tr>
        `;
    });
    tbody.innerHTML = html;
  }

  function renderTicketPagination() {
    const container = document.getElementById("ticketPagination");
    if (!container) return;
    const total = _myTickets.length;
    const totalPages = Math.ceil(total / TICKET_PAGE_SIZE);
    if (totalPages <= 1) {
      container.innerHTML = "";
      container.style.display = "none";
      return;
    }

    const startIndex = (_currentTicketPage - 1) * TICKET_PAGE_SIZE;
    const endIndex = Math.min(startIndex + TICKET_PAGE_SIZE, total);

    // Info text
    let html = `<div class="pagination-info">
      <span>Menampilkan <strong>${startIndex + 1}</strong> hingga <strong>${endIndex}</strong> dari <strong>${total}</strong> data</span>
    </div>`;

    // Buttons
    html += `<div class="pagination-buttons">`;

    // Previous button
    html += `<button type="button" class="pagination-btn" data-page="prev" ${_currentTicketPage === 1 ? "disabled" : ""}>
      <i class="fa-solid fa-chevron-left"></i>
    </button>`;

    // Page buttons (max 5 visible)
    const maxButtons = 5;
    let startPage = Math.max(
      1,
      _currentTicketPage - Math.floor(maxButtons / 2),
    );
    let endPage = Math.min(totalPages, startPage + maxButtons - 1);

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
      html += `<button type="button" class="pagination-btn ${i === _currentTicketPage ? "active" : ""}" data-page="${i}">${i}</button>`;
    }

    if (endPage < totalPages) {
      if (endPage < totalPages - 1) {
        html += `<span class="pagination-btn" style="cursor:default; border:none; padding:0;">...</span>`;
      }
      html += `<button type="button" class="pagination-btn" data-page="${totalPages}">${totalPages}</button>`;
    }

    // Next button
    html += `<button type="button" class="pagination-btn" data-page="next" ${_currentTicketPage === totalPages ? "disabled" : ""}>
      <i class="fa-solid fa-chevron-right"></i>
    </button>`;

    html += `</div>`;

    container.innerHTML = html;
    container.style.display = "flex";

    container.querySelectorAll(".pagination-btn[data-page]").forEach((btn) => {
      btn.addEventListener("click", function () {
        const p = this.getAttribute("data-page");
        let newPage = _currentTicketPage;
        if (p === "prev") newPage = Math.max(1, newPage - 1);
        else if (p === "next") newPage = Math.min(totalPages, newPage + 1);
        else newPage = Number(p);

        if (newPage !== _currentTicketPage) {
          renderTicketsPage(newPage);
          renderTicketPagination();
        }
      });
    });
  }

  function escapeHtml(str) {
    if (!str) return "";
    return String(str).replace(/[&"'<>]/g, function (s) {
      return {
        "&": "&amp;",
        '"': "&quot;",
        "'": "&#39;",
        "<": "&lt;",
        ">": "&gt;",
      }[s];
    });
  }

  function formatDateTime(iso) {
    if (!iso) return "-";
    const d = new Date(iso);
    if (isNaN(d.getTime())) return iso;
    const months = [
      "Januari",
      "Februari",
      "Maret",
      "April",
      "Mei",
      "Juni",
      "Juli",
      "Agustus",
      "September",
      "Oktober",
      "November",
      "Desember",
    ];
    const day = d.getDate();
    const month = months[d.getMonth()];
    const year = d.getFullYear();
    const hours = d.getHours().toString().padStart(2, "0");
    const minutes = d.getMinutes().toString().padStart(2, "0");
    return `${day} ${month} ${year} ${hours}.${minutes}`;
  }

  document.addEventListener("DOMContentLoaded", function () {
    // Get status filter from URL query parameter BEFORE loading tickets
    const urlParams = new URLSearchParams(window.location.search);
    const statusFromUrl = urlParams.get("status");

    // Setup status filter dropdown and set value if present in URL
    const statusFilter = document.getElementById("statusFilter");
    if (statusFilter) {
      if (statusFromUrl) {
        statusFilter.value = statusFromUrl;
        _currentStatusFilter = statusFromUrl;
      }

      statusFilter.addEventListener("change", function () {
        _currentStatusFilter = this.value;
        applyStatusFilter();
      });
    }

    // Load tickets AFTER filter is configured
    loadMyTickets();
  });
})();
