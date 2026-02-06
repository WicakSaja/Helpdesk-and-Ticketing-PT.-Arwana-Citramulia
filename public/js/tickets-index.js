(function () {
  const API =
    typeof API_URL !== "undefined"
      ? API_URL
      : window.location.origin.replace(/\/$/, "");

  const TICKET_PAGE_SIZE = 10;
  let _myTickets = [];
  let _currentTicketPage = 1;

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

      // Cache and render first page
      _myTickets = items || [];
      
      if (_myTickets.length === 0) {
        tbody.innerHTML = `<tr><td colspan="5" class="loading-cell"><i class="fa-solid fa-inbox" style="font-size:24px; color:#ddd; margin-bottom:10px;"></i><p>Belum ada tiket.</p></td></tr>`;
        const pag = document.getElementById("ticketPagination");
        if (pag) pag.innerHTML = "";
        return;
      }

      renderTicketsPage(1);
      renderTicketPagination();

    } catch (err) {
      console.error("loadMyTickets error", err);
      tbody.innerHTML = `<tr><td colspan="5" class="loading-cell" style="color:#d62828;"><i class="fa-solid fa-circle-exclamation" style="font-size:24px; margin-bottom:10px;"></i><p>Gagal memuat riwayat tiket.</p></td></tr>`;
    }
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
      const number = ticket.ticket_number || `#TKT-${String(ticket.id || "").padStart(3, "0")}`;
      const subject = ticket.subject || "-";
      const category = (ticket.category && (ticket.category.name || ticket.category)) || "-";
      const status = (ticket.status && (ticket.status.name || ticket.status)) || "-";
      const when = ticket.updated_at || ticket.created_at || null;
      const updatedFormatted = when ? formatDateTime(when) : "-";

      // Map status to badge class
      const statusNormalized = (status || "").toLowerCase();
      let stClass = "st-open";
      if (/open/.test(statusNormalized)) stClass = "st-open";
      else if (/assigned/.test(statusNormalized)) stClass = "st-assigned";
      else if (/progress|in progress/.test(statusNormalized)) stClass = "st-progress";
      else if (/resolved/.test(statusNormalized)) stClass = "st-resolved";
      else if (/close|closed/.test(statusNormalized)) stClass = "st-closed";

      // --- PERUBAHAN DISINI: MENGGUNAKAN <A HREF> BUKAN BUTTON ONCLICK ---
      html += `
        <tr>
            <td>
                <div class="subject-wrapper">
                    <div class="ticket-no-mobile">${escapeHtml(number)}</div>
                    <div class="ticket-subject-text">${escapeHtml(subject)}</div>
                </div>
            </td>
            <td>${escapeHtml(category)}</td>
            <td><span class="status-badge ${stClass}">${escapeHtml(status)}</span></td>
            <td>${escapeHtml(updatedFormatted)}</td>
            <td class="text-right">
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
      return;
    }

    let html = "";
    html += `<button type="button" class="pagination-btn" data-page="prev">&laquo;</button>`;
    for (let i = 1; i <= totalPages; i++) {
      html += `<button type="button" class="pagination-btn ${i === _currentTicketPage ? "active" : ""}" data-page="${i}">${i}</button>`;
    }
    html += `<button type="button" class="pagination-btn" data-page="next">&raquo;</button>`;

    container.innerHTML = html;

    container.querySelectorAll(".pagination-btn").forEach((btn) => {
      btn.addEventListener("click", function () {
        const p = this.getAttribute("data-page");
        let current = _currentTicketPage;
        if (p === "prev") current = Math.max(1, current - 1);
        else if (p === "next") current = Math.min(Math.ceil(_myTickets.length / TICKET_PAGE_SIZE), current + 1);
        else current = Number(p);

        renderTicketsPage(current);
        const active = container.querySelector(".pagination-btn.active");
        if (active) active.classList.remove("active");
        const newAct = container.querySelector(`.pagination-btn[data-page="${current}"]`);
        if (newAct) newAct.classList.add("active");
      });
    });
  }

  function escapeHtml(str) {
    if (!str) return "";
    return String(str).replace(/[&"'<>]/g, function (s) {
      return { "&": "&amp;", '"': "&quot;", "'": "&#39;", "<": "&lt;", ">": "&gt;" }[s];
    });
  }

  function formatDateTime(iso) {
    if (!iso) return "-";
    const d = new Date(iso);
    if (isNaN(d.getTime())) return iso;
    const months = ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
    const day = d.getDate();
    const month = months[d.getMonth()];
    const year = d.getFullYear();
    const hours = d.getHours().toString().padStart(2, "0");
    const minutes = d.getMinutes().toString().padStart(2, "0");
    return `${day} ${month} ${year} ${hours}.${minutes}`;
  }

  document.addEventListener("DOMContentLoaded", function () {
    loadMyTickets();
  });
})();