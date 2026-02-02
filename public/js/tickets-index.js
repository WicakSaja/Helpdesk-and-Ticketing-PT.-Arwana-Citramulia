(function () {
  const API =
    typeof API_URL !== "undefined"
      ? API_URL
      : window.location.origin.replace(/\/$/, "");

  // Pagination & modal helpers
  const TICKET_PAGE_SIZE = 10;
  let _myTickets = [];
  let _currentTicketPage = 1;
  let _modalFocusHandler = null;
  let _lastFocusedElement = null;

  // Expose openModal globally so inline onclick works
  window.openModal = async function (ticketIdOrNumber) {
    // parse id
    const parsed = Number(ticketIdOrNumber);
    const id = Number.isInteger(parsed) && parsed > 0 ? parsed : null;
    if (id) {
      await loadTicketDetail(id);
      return;
    }
    // fallback: this function may be called with non-id args in legacy usage
    if (arguments.length >= 6) {
      document.getElementById("dId").innerText = ticketIdOrNumber || "-";
      document.getElementById("dSub").innerText = arguments[1] || "-";
      document.getElementById("dCat").innerText = arguments[2] || "-";
      document.getElementById("dStat").innerText = arguments[3] || "-";
      document.getElementById("dTime").innerText = arguments[5] || "-";
      document.getElementById("dDesc").innerText = arguments[4] || "-";
      document.getElementById("myModal").style.display = "flex";
    }
  };

  window.closeModal = function () {
    const el = document.getElementById("myModal");
    if (el) {
      el.style.display = "none";
      el.setAttribute("aria-hidden", "true");
    }
    // remove modal focus handler
    if (_modalFocusHandler) {
      document.removeEventListener("keydown", _modalFocusHandler);
      _modalFocusHandler = null;
    }
    // restore focus
    try {
      if (
        _lastFocusedElement &&
        typeof _lastFocusedElement.focus === "function"
      )
        _lastFocusedElement.focus();
      _lastFocusedElement = null;
    } catch (e) {}
  };

  function showAlert(type, title, text) {
    if (typeof Swal !== "undefined") {
      Swal.fire({
        icon: type,
        title: title,
        text: text,
        confirmButtonColor: "#d62828",
      });
      return;
    }
    alert(`${title}: ${text}`);
  }

  async function loadMyTickets() {
    const tbody = document.getElementById("ticketTableBody");
    if (!tbody) return;
    tbody.innerHTML = `<tr><td colspan="5" style="text-align:center; padding:40px; color:#999;"><i class="fa-solid fa-spinner fa-spin" style="font-size:24px;"></i><p style="margin-top:10px;">Memuat riwayat tiket Anda...</p></td></tr>`;
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
        tbody.innerHTML = `<tr><td colspan="5" style="text-align:center; padding:40px; color:#999;"><i class="fa-solid fa-inbox" style="font-size:24px;"></i><p style="margin-top:10px;">Belum ada tiket.</p></td></tr>`;
        const pag = document.getElementById("ticketPagination");
        if (pag) pag.innerHTML = "";
        return;
      }

      renderTicketsPage(1);
      renderTicketPagination();

      // auto-open if last created exists
      try {
        const last = sessionStorage.getItem("last_created_ticket");
        if (last) {
          openModal(Number(last));
          sessionStorage.removeItem("last_created_ticket");
        }
      } catch (e) {}
    } catch (err) {
      console.error("loadMyTickets error", err);
      tbody.innerHTML = `<tr><td colspan="5" style="text-align:center; padding:40px; color:#d62828;"><i class="fa-solid fa-circle-exclamation" style="font-size:24px;"></i><p style="margin-top:10px;">Gagal memuat riwayat tiket.</p></td></tr>`;
    }
  }

  function renderTicketsPage(page) {
    const tbody = document.getElementById("ticketTableBody");
    if (!tbody) return;
    _currentTicketPage = page;
    const start = (page - 1) * TICKET_PAGE_SIZE;
    const pageItems = _myTickets.slice(start, start + TICKET_PAGE_SIZE);
    if (!pageItems.length) {
      tbody.innerHTML = `<tr><td colspan="5" style="text-align:center; padding:40px; color:#999;">Tidak ada tiket pada halaman ini.</td></tr>`;
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

      html += `
                    <tr>
                        <td>
                            <div style="font-weight:700; font-size: 15px;">${escapeHtml(subject)}</div>
                            <small style="color:#888;">${escapeHtml(number)}</small>
                        </td>
                        <td>${escapeHtml(category)}</td>
                        <td><span class="status-badge ${stClass}">${escapeHtml(status)}</span></td>
                        <td>${escapeHtml(updatedFormatted)}</td>
                        <td class="text-right">
                            <button type="button" class="btn-detail" onclick="openModal(${ticket.id})">
                                Lihat <i class="fa-solid fa-chevron-right" style="font-size: 10px; margin-left: 5px;"></i>
                            </button>
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
        else if (p === "next")
          current = Math.min(
            Math.ceil(_myTickets.length / TICKET_PAGE_SIZE),
            current + 1,
          );
        else current = Number(p);

        renderTicketsPage(current);
        const active = container.querySelector(".pagination-btn.active");
        if (active) active.classList.remove("active");
        const newAct = container.querySelector(
          `.pagination-btn[data-page="${current}"]`,
        );
        if (newAct) newAct.classList.add("active");
      });
    });
  }

  async function loadTicketDetail(id) {
    const headers =
      typeof TokenManager !== "undefined" &&
      typeof TokenManager.getHeaders === "function"
        ? TokenManager.getHeaders()
        : { "Content-Type": "application/json" };

    // show spinner and hide content
    const loadingEl = document.getElementById("detailLoading");
    const contentEl = document.getElementById("detailContent");
    const modal = document.getElementById("myModal");
    if (loadingEl) loadingEl.style.display = "block";
    if (contentEl) contentEl.style.display = "none";
    if (modal) {
      modal.style.display = "flex";
      modal.setAttribute("aria-hidden", "false");
    }

    try {
      const res = await fetch(`${API}/api/tickets/${id}`, {
        method: "GET",
        headers: headers,
      });
      if (!res.ok) throw new Error("Gagal memuat detail tiket");
      const json = await res.json();
      const t = json.ticket || json.data || json;

      document.getElementById("dId").innerText =
        t.ticket_number || `#TKT-${String(t.id || "").padStart(3, "0")}`;
      document.getElementById("dSub").innerText = t.subject || "-";
      // requester (try common shapes)
      const requesterName =
        (t.requester && (t.requester.name || t.requester)) ||
        t.requester_name ||
        (t.requester_id && t.requester_name ? t.requester_name : "-");
      document.getElementById("dRequester").innerText = requesterName || "-";
      document.getElementById("dCat").innerText =
        (t.category && (t.category.name || t.category)) || "-";
      document.getElementById("dStat").innerText =
        (t.status && (t.status.name || t.status)) || "-";
      const when = t.updated_at || t.created_at || null;
      document.getElementById("dTime").innerText = when
        ? formatDateTime(when)
        : "-";
      document.getElementById("dDesc").innerText = t.description || "-";

      // show content and hide spinner
      if (loadingEl) loadingEl.style.display = "none";
      if (contentEl) contentEl.style.display = "block";

      // focus trap: set focus to modal and trap TAB
      const modalBox = document.querySelector("#myModal .modal-box");
      _lastFocusedElement = document.activeElement;
      if (modalBox) {
        modalBox.focus();

        _modalFocusHandler = function (e) {
          if (e.key === "Escape") {
            closeModal();
            return;
          }
          if (e.key !== "Tab") return;
          const focusable = modalBox.querySelectorAll(
            'a[href],button:not([disabled]),textarea, input, select, [tabindex]:not([tabindex="-1"])',
          );
          if (!focusable.length) return;
          const first = focusable[0];
          const last = focusable[focusable.length - 1];
          if (e.shiftKey) {
            if (document.activeElement === first) {
              e.preventDefault();
              last.focus();
            }
          } else {
            if (document.activeElement === last) {
              e.preventDefault();
              first.focus();
            }
          }
        };
        document.addEventListener("keydown", _modalFocusHandler);
      }
    } catch (err) {
      console.error("loadTicketDetail error", err);
      showAlert("error", "Error", "Gagal memuat detail tiket");
      if (loadingEl) loadingEl.style.display = "none";
      if (contentEl) contentEl.style.display = "block";
    }
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
    loadMyTickets();
    // close modal on overlay click
    const modal = document.getElementById("myModal");
    if (modal) {
      modal.addEventListener("click", function (e) {
        if (e.target === modal) closeModal();
      });
    }
  });
})();
