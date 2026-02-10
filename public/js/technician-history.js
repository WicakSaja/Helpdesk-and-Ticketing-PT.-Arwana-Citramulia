document.addEventListener("DOMContentLoaded", function () {
  const PAGE_SIZE = 15;
  let currentPage = 1;
  let totalPages = 1;

  loadHistory();

  // Expose filter change to global scope
  window.loadHistory = loadHistory;

  async function loadHistory() {
    const tbody = document.getElementById("historyTableBody");
    const paginationContainer = document.getElementById("historyPagination");
    const statusFilter = document.getElementById("statusFilter").value;

    tbody.innerHTML =
      '<tr><td colspan="6" class="loading-spinner"><i class="fa-solid fa-spinner fa-spin"></i> Memuat data...</td></tr>';
    if (paginationContainer) paginationContainer.style.display = "none";

    try {
      const token =
        localStorage.getItem("auth_token") ||
        sessionStorage.getItem("auth_token");
      if (!token) {
        throw new Error("No authentication token found");
      }

      let url = `/api/technician/completed-tickets?page=${currentPage}&per_page=${PAGE_SIZE}&sort_by=updated_at&sort_order=desc`;
      if (statusFilter) {
        url += `&status=${statusFilter}`;
      }

      const response = await fetch(url, {
        headers: {
          Authorization: `Bearer ${token}`,
          Accept: "application/json",
        },
      });

      if (!response.ok) {
        throw new Error(`HTTP error! status: ${response.status}`);
      }

      const result = await response.json();
      renderHistory(result.data);

      if (result.pagination) {
        totalPages = result.pagination.last_page;
        renderPagination(result.pagination);
      }

      // Load pending tickets count separately
      loadPendingCount();
    } catch (error) {
      console.error("Error loading history:", error);
      tbody.innerHTML = `
                <tr>
                    <td colspan="6" class="empty-state">
                        <i class="fa-solid fa-circle-exclamation"></i>
                        <div>Gagal memuat data. Silakan refresh halaman.</div>
                    </td>
                </tr>
            `;
    }
  }

  function renderHistory(tickets) {
    const tbody = document.getElementById("historyTableBody");

    if (!tickets || tickets.length === 0) {
      tbody.innerHTML = `
                <tr>
                    <td colspan="6" class="empty-state">
                        <i class="fa-solid fa-folder-open"></i>
                        <div>Belum ada riwayat pekerjaan yang selesai</div>
                    </td>
                </tr>
            `;
      return;
    }

    tbody.innerHTML = tickets
      .map((ticket) => {
        const statusName = ticket.status?.name || ticket.status || "unknown";
        const statusLabel = getStatusLabel(statusName);
        const badgeClass =
          statusName.toLowerCase() === "closed"
            ? "badge-closed"
            : "badge-resolved";
        const requesterName = ticket.requester?.name || "-";
        const requesterDept = ticket.requester?.department?.name || "-";
        const categoryName = ticket.category?.name || "-";

        return `
                <tr data-ticket-id="${ticket.id}" onclick="goToDetail(${ticket.id})" title="Klik untuk melihat detail">
                    <td><b>${escapeHtml(ticket.ticket_number)}</b></td>
                    <td>${escapeHtml(ticket.subject)}</td>
                    <td>
                        <div class="requester-info">
                            <div class="requester-name">${escapeHtml(requesterName)}</div>
                            <div class="requester-dept">${escapeHtml(requesterDept)}</div>
                        </div>
                    </td>
                    <td>${escapeHtml(categoryName)}</td>
                    <td>${formatDate(ticket.updated_at)}</td>
                    <td><span class="${badgeClass}">${escapeHtml(statusLabel)}</span></td>
                </tr>
            `;
      })
      .join("");
  }

  function renderPagination(pagination) {
    const container = document.getElementById("historyPagination");
    if (!container) return;

    const { current_page, last_page, total, from, to } = pagination;

    if (last_page <= 1) {
      container.innerHTML = "";
      container.style.display = "none";
      return;
    }

    let html = `<div class="pagination-info">
            <span>Menampilkan <strong>${from || 0}</strong> hingga <strong>${to || 0}</strong> dari <strong>${total || 0}</strong> data</span>
        </div>`;

    html += `<div class="pagination-buttons">`;

    // Previous
    html += `<button type="button" class="pagination-btn" data-page="prev" ${current_page === 1 ? "disabled" : ""}>
            <i class="fa-solid fa-chevron-left"></i>
        </button>`;

    // Page numbers
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

    // Next
    html += `<button type="button" class="pagination-btn" data-page="next" ${current_page === last_page ? "disabled" : ""}>
            <i class="fa-solid fa-chevron-right"></i>
        </button>`;

    html += `</div>`;

    container.innerHTML = html;
    container.style.display = "flex";

    // Bind clicks
    container.querySelectorAll(".pagination-btn[data-page]").forEach((btn) => {
      btn.addEventListener("click", function () {
        const p = this.getAttribute("data-page");
        let newPage = current_page;
        if (p === "prev") newPage = Math.max(1, current_page - 1);
        else if (p === "next") newPage = Math.min(last_page, current_page + 1);
        else newPage = Number(p);

        if (newPage !== current_page) {
          currentPage = newPage;
          loadHistory();
        }
      });
    });
  }

  function formatDate(dateString) {
    if (!dateString) return "-";
    const date = new Date(dateString);
    return date.toLocaleDateString("id-ID", {
      day: "numeric",
      month: "short",
      year: "numeric",
    });
  }

  function getStatusLabel(status) {
    const labels = {
      resolved: "Resolved",
      closed: "Closed",
    };
    return labels[status] || status;
  }

  function escapeHtml(text) {
    if (!text) return "";
    const div = document.createElement("div");
    div.textContent = text;
    return div.innerHTML;
  }

  function updateCompletedCount(total) {
    const countEl = document.getElementById("completedCountNum");
    if (countEl) {
      countEl.textContent = total || 0;
    }
  }

  async function loadPendingCount() {
    try {
      const token =
        localStorage.getItem("auth_token") ||
        sessionStorage.getItem("auth_token");
      if (!token) return;

      const response = await fetch(`/api/technician/tickets?per_page=1`, {
        headers: {
          Authorization: `Bearer ${token}`,
          Accept: "application/json",
        },
      });

      if (response.ok) {
        const result = await response.json();
        const countEl = document.getElementById("pendingCountNum");
        if (countEl && result.pagination) {
          countEl.textContent = result.pagination.total || 0;
        }
      }
    } catch (error) {
      console.error("Error loading pending count:", error);
    }
  }

  // Expose goToDetail to global scope
  window.goToDetail = function (ticketId) {
    window.location.href = `/technician/tickets/${ticketId}`;
  };
});
