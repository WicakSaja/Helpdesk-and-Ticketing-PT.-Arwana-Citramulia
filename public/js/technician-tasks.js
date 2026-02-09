document.addEventListener("DOMContentLoaded", function () {
  const API_BASE = window.location.origin;

  loadTechnicianTasks();

  async function loadTechnicianTasks() {
    const token =
      localStorage.getItem("auth_token") ||
      sessionStorage.getItem("auth_token");

    try {
      const response = await fetch(
        `${API_BASE}/api/technician/tickets?page=1&per_page=15`,
        {
          method: "GET",
          headers: {
            Authorization: `Bearer ${token}`,
            "Content-Type": "application/json",
            Accept: "application/json",
          },
        },
      );

      if (!response.ok) {
        throw new Error(`HTTP error! status: ${response.status}`);
      }

      const result = await response.json();
      const tickets = result.data || [];
      document.getElementById("taskTitle").innerText =
        `Daftar Tugas (${tickets.length})`;

      renderTasks(tickets);
    } catch (error) {
      document.getElementById("taskList").innerHTML = `
                <div class="task-card">
                    <div class="task-body">
                        <h3>Gagal memuat data</h3>
                        <p>${escapeHtml(error.message)}</p>
                    </div>
                </div>
            `;
    }
  }

  function renderTasks(tickets) {
    if (!tickets.length) {
      document.getElementById("taskList").innerHTML = `
                <div class="task-card">
                    <div class="task-body">
                        <h3>Tidak ada tugas</h3>
                        <p>Belum ada ticket yang di-assign ke Anda.</p>
                    </div>
                </div>
            `;
      return;
    }

    const html = tickets
      .map((ticket) => {
        const categoryName = ticket.category?.name || "Unknown";
        const statusName = ticket.status?.name || "unknown";
        const requesterName = ticket.requester?.name || "Unknown";
        const requesterDept = ticket.requester?.department?.name || "Unknown";
        const createdAt = formatDate(ticket.created_at);
        const statusLower = statusName.toLowerCase();

        // Card border class: resolved gets green, otherwise category color
        let cardClass = getCategoryClass(categoryName);
        if (statusLower === "resolved") {
          cardClass = "bd-resolved";
        }

        // Status badge class
        const statusBadgeClass = getStatusBadgeClass(statusLower);

        // Determine action buttons based on status
        let actionButtons = "";

        if (statusLower === "assigned") {
          actionButtons = `
                    <a class="btn-action btn-detail" href="/technician/tickets/${ticket.id}">
                        <i class="fa-regular fa-eye"></i> Detail
                    </a>
                    <button class="btn-action btn-confirm"
                        onclick="confirmTicket(${ticket.id}, '${escapeHtml(ticket.ticket_number)}')">
                        <i class="fa-solid fa-check"></i> Konfirmasi
                    </button>
                    <button class="btn-action btn-reject"
                        onclick="rejectTicket(${ticket.id}, '${escapeHtml(ticket.ticket_number)}')">
                        <i class="fa-solid fa-times"></i> Tolak
                    </button>
                `;
        } else if (statusLower === "in progress") {
          actionButtons = `
                    <a class="btn-action btn-detail" href="/technician/tickets/${ticket.id}">
                        <i class="fa-regular fa-eye"></i> Detail
                    </a>
                    <button class="btn-action btn-update"
                        onclick="openResolve(${ticket.id}, '${escapeHtml(ticket.ticket_number)}', '${escapeHtml(ticket.subject)}')">
                        <i class="fa-solid fa-check-circle"></i> Selesaikan Tiket
                    </button>
                `;
        } else {
          actionButtons = `
                    <a class="btn-action btn-detail" href="/technician/tickets/${ticket.id}">
                        <i class="fa-regular fa-eye"></i> Detail
                    </a>
                `;
        }

        return `
                <div class="task-card ${cardClass}">
                    <div class="task-header">
                        <span class="task-id">#${escapeHtml(ticket.ticket_number)}</span>
                        <span class="task-time"><i class="fa-regular fa-clock"></i> ${createdAt}</span>
                    </div>
                    <div class="task-body">
                        <h3>${escapeHtml(ticket.subject)}</h3>
                        <p>${escapeHtml(ticket.description || "-")}</p>
                        <div class="task-meta">
                            <div class="meta-tag"><i class="fa-solid fa-building"></i> ${escapeHtml(requesterDept)}</div>
                            <div class="meta-tag"><i class="fa-solid fa-tag"></i> ${escapeHtml(categoryName)}</div>
                            <span class="status-badge ${statusBadgeClass}">${escapeHtml(statusName)}</span>
                        </div>
                    </div>
                    <div class="action-group">
                        ${actionButtons}
                    </div>
                </div>
            `;
      })
      .join("");

    document.getElementById("taskList").innerHTML = html;
  }

  function formatDate(dateStr) {
    if (!dateStr) return "-";
    const date = new Date(dateStr);
    return date.toLocaleString("id-ID", {
      day: "2-digit",
      month: "short",
      year: "numeric",
      hour: "2-digit",
      minute: "2-digit",
    });
  }

  function getCategoryClass(category) {
    const name = (category || "").toLowerCase();
    if (name.includes("hardware") || name.includes("mechanical"))
      return "bd-mech";
    if (name.includes("it") || name.includes("software")) return "bd-it";
    return "";
  }

  function getStatusBadgeClass(status) {
    const map = {
      assigned: "status-assigned",
      "in progress": "status-in-progress",
      resolved: "status-resolved",
      closed: "status-closed",
      open: "status-open",
    };
    return map[status] || "status-open";
  }

  function escapeHtml(str) {
    return String(str || "")
      .replace(/&/g, "&amp;")
      .replace(/</g, "&lt;")
      .replace(/>/g, "&gt;")
      .replace(/"/g, "&quot;")
      .replace(/'/g, "&#039;");
  }

  // === MODAL: Open Resolve ===
  window.openResolve = function (ticketId, ticketNumber, subject) {
    document.getElementById("resolveTicketId").value = ticketId;
    document.getElementById("uSubject").innerText =
      "#" + ticketNumber + " - " + subject;

    const now = new Date();
    const year = now.getFullYear();
    const month = String(now.getMonth() + 1).padStart(2, "0");
    const day = String(now.getDate()).padStart(2, "0");
    const hours = String(now.getHours()).padStart(2, "0");
    const minutes = String(now.getMinutes()).padStart(2, "0");
    document.getElementById("resolvedAt").value =
      `${year}-${month}-${day}T${hours}:${minutes}`;
    document.getElementById("solutionText").value = "";

    document.getElementById("modalUpdate").style.display = "flex";
  };

  // === Confirm Ticket ===
  window.confirmTicket = async function (ticketId, ticketNumber) {
    const result = await Swal.fire({
      title: "Konfirmasi Tiket",
      text: `Anda akan mengkonfirmasi tiket #${ticketNumber}. Lanjutkan?`,
      icon: "question",
      showCancelButton: true,
      confirmButtonColor: "#1976d2",
      cancelButtonColor: "#999",
      confirmButtonText: "Ya, Konfirmasi",
      cancelButtonText: "Batal",
    });

    if (!result.isConfirmed) return;

    const token =
      localStorage.getItem("auth_token") ||
      sessionStorage.getItem("auth_token");

    try {
      const response = await fetch(
        `${API_BASE}/api/tickets/${ticketId}/confirm`,
        {
          method: "POST",
          headers: {
            Authorization: `Bearer ${token}`,
            "Content-Type": "application/json",
            Accept: "application/json",
          },
        },
      );

      const result = await response.json();

      if (response.ok) {
        await Swal.fire({
          icon: "success",
          title: "Berhasil!",
          text: 'Tiket berhasil dikonfirmasi. Status berubah menjadi "In Progress".',
          confirmButtonColor: "#2e7d32",
        });
        loadTechnicianTasks();
      } else {
        throw new Error(result.message || "Gagal konfirmasi tiket");
      }
    } catch (error) {
      Swal.fire({
        icon: "error",
        title: "Error",
        text: error.message,
        confirmButtonColor: "#d62828",
      });
    }
  };

  // === Reject Ticket ===
  window.rejectTicket = async function (ticketId, ticketNumber) {
    const result = await Swal.fire({
      title: "Tolak Tiket",
      text: `Anda akan menolak tiket #${ticketNumber}. Tiket akan kembali ke status "Open".`,
      icon: "warning",
      showCancelButton: true,
      confirmButtonColor: "#d32f2f",
      cancelButtonColor: "#999",
      confirmButtonText: "Ya, Tolak",
      cancelButtonText: "Batal",
    });

    if (!result.isConfirmed) return;

    const token =
      localStorage.getItem("auth_token") ||
      sessionStorage.getItem("auth_token");

    try {
      const response = await fetch(
        `${API_BASE}/api/tickets/${ticketId}/reject`,
        {
          method: "POST",
          headers: {
            Authorization: `Bearer ${token}`,
            "Content-Type": "application/json",
            Accept: "application/json",
          },
        },
      );

      const result = await response.json();

      if (response.ok) {
        await Swal.fire({
          icon: "success",
          title: "Berhasil!",
          text: 'Tiket berhasil ditolak. Status kembali ke "Open".',
          confirmButtonColor: "#2e7d32",
        });
        loadTechnicianTasks();
      } else {
        throw new Error(result.message || "Gagal menolak tiket");
      }
    } catch (error) {
      Swal.fire({
        icon: "error",
        title: "Error",
        text: error.message,
        confirmButtonColor: "#d62828",
      });
    }
  };

  // === Close Modal ===
  window.closeModal = function (modalId) {
    document.getElementById(modalId).style.display = "none";
  };

  // === Resolve Form Submit ===
  document
    .getElementById("updateForm")
    .addEventListener("submit", async function (e) {
      e.preventDefault();

      const ticketId = document.getElementById("resolveTicketId").value;
      const solution = document.getElementById("solutionText").value.trim();
      const resolvedAt = document.getElementById("resolvedAt").value;

      if (!solution) {
        Swal.fire({
          icon: "warning",
          title: "Solusi Required",
          text: "Mohon isi solusi/tindakan perbaikan.",
          confirmButtonColor: "#d62828",
        });
        return;
      }

      const dateObj = new Date(resolvedAt);
      const mysqlDateTime =
        dateObj.getFullYear() +
        "-" +
        String(dateObj.getMonth() + 1).padStart(2, "0") +
        "-" +
        String(dateObj.getDate()).padStart(2, "0") +
        " " +
        String(dateObj.getHours()).padStart(2, "0") +
        ":" +
        String(dateObj.getMinutes()).padStart(2, "0") +
        ":00";

      closeModal("modalUpdate");

      Swal.fire({
        title: "Menyimpan...",
        text: "Sedang menyelesaikan tiket",
        allowOutsideClick: false,
        didOpen: () => {
          Swal.showLoading();
        },
      });

      const token =
        localStorage.getItem("auth_token") ||
        sessionStorage.getItem("auth_token");

      try {
        const response = await fetch(
          `${API_BASE}/api/tickets/${ticketId}/solve`,
          {
            method: "POST",
            headers: {
              Authorization: `Bearer ${token}`,
              "Content-Type": "application/json",
              Accept: "application/json",
            },
            body: JSON.stringify({
              solution: solution,
              resolved_at: mysqlDateTime,
            }),
          },
        );

        const result = await response.json();

        if (response.ok) {
          await Swal.fire({
            icon: "success",
            title: "Berhasil!",
            text: 'Tiket berhasil diselesaikan. Status berubah menjadi "Resolved".',
            confirmButtonColor: "#2e7d32",
          });
          loadTechnicianTasks();
        } else {
          throw new Error(result.message || "Gagal menyelesaikan tiket");
        }
      } catch (error) {
        Swal.fire({
          icon: "error",
          title: "Error",
          text: error.message,
          confirmButtonColor: "#d62828",
        });
      }
    });

  // Close modal on overlay click
  window.onclick = function (event) {
    if (event.target.classList.contains("modal-overlay")) {
      event.target.style.display = "none";
    }
  };
});
