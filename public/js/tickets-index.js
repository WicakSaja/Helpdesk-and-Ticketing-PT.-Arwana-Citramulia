(function () {
  const API =
    typeof API_URL !== "undefined"
      ? API_URL
      : window.location.origin.replace(/\/$/, "");

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
    if (el) el.style.display = "none";
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
      if (items.length === 0) {
        tbody.innerHTML = `<tr><td colspan="5" style="text-align:center; padding:40px; color:#999;"><i class="fa-solid fa-inbox" style="font-size:24px;"></i><p style="margin-top:10px;">Belum ada tiket.</p></td></tr>`;
        return;
      }

      let html = "";
      items.forEach((ticket) => {
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
        html += `
                    <tr>
                        <td>
                            <div style="font-weight:700; font-size: 15px;">${escapeHtml(subject)}</div>
                            <small style="color:#888;">${escapeHtml(number)}</small>
                        </td>
                        <td>${escapeHtml(category)}</td>
                        <td><span class="status-badge">${escapeHtml(status)}</span></td>
                        <td>${escapeHtml(updatedFormatted)}</td>
                        <td style="text-align: right;">
                            <a href="/tickets/${ticket.id}" class="btn-detail">
                                Lihat <i class="fa-solid fa-chevron-right" style="font-size: 10px; margin-left: 5px;"></i>
                            </a>
                        </td>
                    </tr>
                `;
      });
      tbody.innerHTML = html;
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

  async function loadTicketDetail(id) {
    const headers =
      typeof TokenManager !== "undefined" &&
      typeof TokenManager.getHeaders === "function"
        ? TokenManager.getHeaders()
        : { "Content-Type": "application/json" };
    try {
      const res = await fetch(`${API}/api/tickets/${id}`, {
        method: "GET",
        headers: headers,
      });
      if (!res.ok) throw new Error("Gagal memuat detail tiket");
      const json = await res.json();
      const t = json.data || json;
      document.getElementById("dId").innerText =
        t.ticket_number || `#TKT-${String(t.id || "").padStart(3, "0")}`;
      document.getElementById("dSub").innerText = t.subject || "-";
      document.getElementById("dCat").innerText =
        (t.category && (t.category.name || t.category)) || "-";
      document.getElementById("dStat").innerText =
        (t.status && (t.status.name || t.status)) || "-";
      const when = t.updated_at || t.created_at || null;
      document.getElementById("dTime").innerText = when
        ? formatDateTime(when)
        : "-";
      document.getElementById("dDesc").innerText = t.description || "-";
      document.getElementById("myModal").style.display = "flex";
    } catch (err) {
      console.error("loadTicketDetail error", err);
      showAlert("error", "Error", "Gagal memuat detail tiket");
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
