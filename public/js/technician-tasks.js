document.addEventListener("DOMContentLoaded", function () {
  // === Real-time polling for notification badge ===
  let notifInterval = setInterval(() => {
    loadPendingCount(currentFilter);
  }, 5000); // 5 detik polling
  const API_BASE = window.location.origin;

  // State variables for pagination and filtering
  let currentPage = 1;
  let perPage = 15;
  let currentFilter = "all";
  let currentSearch = ""; // Add search state
  let allTickets = [];
  let hasMoreData = true;
  let totalTickets = 0; // Total from API

  loadTechnicianTasks();
  loadPendingCount("all"); // Load notification badge count with "all" filter initially

  // Search input event listener with debounce
  const searchInput = document.getElementById("searchInput");
  if (searchInput) {
    let searchTimeout;
    searchInput.addEventListener("input", function (e) {
      currentSearch = e.target.value.trim();
      clearTimeout(searchTimeout);
      // Debounce search for 300ms
      searchTimeout = setTimeout(() => {
        currentPage = 1;
        allTickets = [];
        loadTechnicianTasks();
      }, 300);
    });
  }

  // Filter button event listeners
  document.querySelectorAll(".filter-btn").forEach((btn) => {
    btn.addEventListener("click", function () {
      // Update active state
      document
        .querySelectorAll(".filter-btn")
        .forEach((b) => b.classList.remove("active"));
      this.classList.add("active");

      // Update filter and reload
      currentFilter = this.getAttribute("data-status");
      currentPage = 1;
      allTickets = [];
      totalTickets = 0; // Reset total
      loadTechnicianTasks();
      loadPendingCount(currentFilter); // Update notification count based on filter
    });
  });

  // Load more link event listener (now <a> tag)
  const loadMoreBtn = document.getElementById("loadMoreBtn");
  if (loadMoreBtn) {
    loadMoreBtn.addEventListener("click", function (e) {
      e.preventDefault();
      currentPage++;
      loadTechnicianTasks(true); // true = append mode
    });
  }

  async function loadTechnicianTasks(appendMode = false) {
    const token =
      localStorage.getItem("auth_token") ||
      sessionStorage.getItem("auth_token");

    // Build API URL with filter parameter
    let apiUrl = `${API_BASE}/api/technician/tickets?page=${currentPage}&per_page=${perPage}`;
    if (currentFilter !== "all") {
      const statusParam =
        currentFilter === "in_progress" ? "In Progress" : "Assigned";
      apiUrl += `&status=${encodeURIComponent(statusParam)}`;
    }
    // Add search parameter if exists
    if (currentSearch) {
      apiUrl += `&search=${encodeURIComponent(currentSearch)}`;
    }

    try {
      const response = await fetch(apiUrl, {
        method: "GET",
        headers: {
          Authorization: `Bearer ${token}`,
          "Content-Type": "application/json",
          Accept: "application/json",
        },
      });

      if (!response.ok) {
        throw new Error(`HTTP error! status: ${response.status}`);
      }

      const result = await response.json();
      const tickets = result.data || [];
      const pagination = result.pagination || {};

      // Get total from pagination object (NOT result.total)
      totalTickets = pagination.total || tickets.length;

      // Update hasMoreData based on result
      hasMoreData = tickets.length === perPage;

      if (appendMode) {
        // Append new tickets to existing list
        allTickets = allTickets.concat(tickets);
      } else {
        // Replace tickets
        allTickets = tickets;
      }

      // Update title without count - just "Daftar Tugas"
      document.getElementById("taskTitle").innerText = `Daftar Tugas`;

      // Update info counter and load more button visibility
      const bottomActions = document.getElementById("bottomActions");
      const taskInfoCounter = document.getElementById("taskInfoCounter");
      const taskCounterText = document.getElementById("taskCounterText");
      const loadMoreContainer = document.getElementById("loadMoreContainer");

      if (allTickets.length > 0) {
        // Update counter text
        if (allTickets.length < totalTickets) {
          taskCounterText.innerText = `Menampilkan ${allTickets.length} dari ${totalTickets} data`;
        } else {
          taskCounterText.innerText = `Menampilkan semua data`;
        }

        // Show/hide load more button
        if (hasMoreData) {
          loadMoreContainer.style.display = "block";
        } else {
          loadMoreContainer.style.display = "none";
        }

        // Show bottom actions
        bottomActions.style.display = "flex";
      } else {
        // Hide bottom actions when no data
        bottomActions.style.display = "none";
      }

      renderTasks(allTickets);
    } catch (error) {
      document.getElementById("taskList").innerHTML = `
                <div class="task-card">
                    <div class="task-body">
                        <h3>Gagal memuat data</h3>
                        <p>${escapeHtml(error.message)}</p>
                    </div>
                </div>
            `;
      document.getElementById("bottomActions").style.display = "none";
    }
  }

  function renderTasks(tickets) {
    if (!tickets.length) {
      const emptyMsg = currentSearch
        ? `Tidak ada tiket yang cocok dengan pencarian "${currentSearch}"`
        : "Tidak ada tugas";
      const emptySubMsg = currentSearch
        ? "Coba gunakan kata kunci lain."
        : "Belum ada ticket yang di-assign ke Anda.";

      document.getElementById("taskList").innerHTML = `
                <div class="task-card">
                    <div class="task-body">
                        <h3>${emptyMsg}</h3>
                        <p>${emptySubMsg}</p>
                    </div>
                </div>
            `;
      // Hide bottom actions when empty
      document.getElementById("bottomActions").style.display = "none";
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

        // Card border class based on status: assigned=orange, in progress=blue, resolved=green
        let cardClass = getCardBorderClass(statusLower);

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
                        <i class="fa-solid fa-check"></i> Accept
                    </button>
                    <button class="btn-action btn-reject"
                        onclick="rejectTicket(${ticket.id}, '${escapeHtml(ticket.ticket_number)}')">
                        <i class="fa-solid fa-times"></i> Reject
                    </button>
                `;
        } else if (statusLower === "in progress") {
          actionButtons = `
                    <a class="btn-action btn-detail" href="/technician/tickets/${ticket.id}">
                        <i class="fa-regular fa-eye"></i> Detail
                    </a>
                    <button class="btn-action btn-update"
                        onclick="openResolve(${ticket.id}, '${escapeHtml(ticket.ticket_number)}', '${escapeHtml(ticket.subject)}')">
                        <i class="fa-solid fa-check-circle"></i> Resolve Ticket
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

  // Get card border class based on status (orange = assigned, blue = in progress, green = resolved)
  function getCardBorderClass(status) {
    if (status === "assigned") return "bd-assigned";
    if (status === "in progress") return "bd-in-progress";
    if (status === "resolved") return "bd-resolved";
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
        loadPendingCount(currentFilter); // Update notification count with current filter
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
        loadPendingCount(currentFilter); // Update notification count with current filter
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
          loadPendingCount(currentFilter); // Refresh notif real-time setelah resolve
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

  // === Load Pending Count for Notification Badge ===
  async function loadPendingCount(filterStatus = "all") {
    try {
      const token =
        localStorage.getItem("auth_token") ||
        sessionStorage.getItem("auth_token");
      if (!token) return;

      // Build API URL with filter if not "all"
      let apiUrl = `${API_BASE}/api/technician/tickets?per_page=1`;
      if (filterStatus !== "all") {
        const statusParam =
          filterStatus === "in_progress" ? "In Progress" : "Assigned";
        apiUrl += `&status=${encodeURIComponent(statusParam)}`;
      }

      const response = await fetch(apiUrl, {
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

  // Close modal on overlay click
  window.onclick = function (event) {
    if (event.target.classList.contains("modal-overlay")) {
      event.target.style.display = "none";
    }
  };
});
