document.addEventListener("DOMContentLoaded", function () {
  // Elements
  const ticketsBody = document.getElementById("ticketsBody");
  const assignModal = document.getElementById("assignModal");
  const modalTicketId = document.getElementById("modalTicketId");
  const modalTicketSubject = document.getElementById("modalTicketSubject");
  const modalTicketIdInput = document.getElementById("modalTicketIdInput");
  const techSelect = document.getElementById("technicianSelect");
  const techLoading = document.getElementById("technicianLoading");
  const refreshBtn = document.getElementById("refreshTicketsBtn");

  let _techniciansCache = null;
  // Use a map for caching user details to reduce redundant network calls
  const _userCache = new Map();

  // Pagination / State
  let currentPage = 1;
  const PER_PAGE = 15;
  const API_URL = typeof window.API_URL !== "undefined" ? window.API_URL : ""; // Fallback

  // Auth Helper (matches your token manager pattern)
  const getAuthHeaders = () => {
    const token =
      sessionStorage.getItem("auth_token") ||
      localStorage.getItem("auth_token");
    return {
      Authorization: `Bearer ${token}`,
      Accept: "application/json",
      "Content-Type": "application/json",
    };
  };

  const fetchWithAuth = async (url, options = {}) => {
    const headers = { ...getAuthHeaders(), ...options.headers };
    try {
      const response = await fetch(url, { ...options, headers });
      if (response.status === 401) {
        window.location.href = "/login";
        return null;
      }
      return response;
    } catch (error) {
      console.error("Fetch Error:", error);
      return null;
    }
  };

  // Initial Load
  loadTickets(currentPage);

  // Poll for new tickets
  setInterval(() => checkForNewTickets(), 20000);

  // Event Listeners
  if (refreshBtn) {
    refreshBtn.addEventListener("click", () => {
      refreshBtn.disabled = true;
      refreshBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i>';
      loadTickets(currentPage).finally(() => {
        refreshBtn.disabled = false;
        refreshBtn.innerHTML = '<i class="fa-solid fa-arrows-rotate"></i>';
      });
    });
  }

  document.addEventListener("click", function (e) {
    if (e.target && e.target.id === "assignCancelBtn") closeAssignModal();
    if (e.target && e.target.id === "assignSaveBtn") saveAssignment();
  });

  // --- CORE FUNCTIONS ---

  async function loadTickets(page = 1) {
    currentPage = page;
    ticketsBody.innerHTML =
      '<tr><td colspan="5" style="text-align:center; padding:40px 0; color:#666">Memuat tiket...</td></tr>';

    try {
      const res = await fetchWithAuth(
        `${API_URL}/api/tickets?page=${page}&per_page=${PER_PAGE}&status=open`,
      );
      if (!res || !res.ok) throw new Error("Gagal load data");

      const json = await res.json();
      let tickets = json.data || (Array.isArray(json) ? json : []);
      const meta = json.meta || null;

      // Filter: Status Open + Not Assigned
      tickets = tickets.filter((t) => {
        const status = (t.status?.name || t.status || "").toLowerCase();
        const isAssigned = t.assignment || t.assigned_to || t.technician_id;
        return (status === "open" || status === "new") && !isAssigned;
      });

      if (tickets.length === 0) {
        ticketsBody.innerHTML =
          '<tr><td colspan="5" style="text-align:center; padding:40px 0; color:#666">Tidak ada tiket baru.</td></tr>';
        document.getElementById("ticketsPagination").innerHTML = "";
        return;
      }

      ticketsBody.innerHTML = tickets.map(renderTicketRow).join("");

      // Update badge counts
      updateBadgeCount(tickets.length);

      // Bind assign buttons
      document.querySelectorAll(".btn-assign").forEach((btn) => {
        btn.addEventListener("click", function () {
          openAssignModal(this.dataset.ticketId, this.dataset.subject);
        });
      });

      renderPagination(meta, tickets.length);

      // Async update details (requester dept, etc)
      tickets.forEach((t) => updateRequesterDetails(t));
    } catch (err) {
      console.error("Error loading tickets", err);
      ticketsBody.innerHTML =
        '<tr><td colspan="5" style="text-align:center; padding:40px 0; color:#d62828">Gagal memuat tiket.</td></tr>';
    }
  }

  function renderTicketRow(t) {
    const ticketNum = t.ticket_number || `#${t.id}`;
    const subject = escapeHtml(t.subject || t.title || "-");
    const category = escapeHtml(t.category?.name || "-");
    const requester = escapeHtml(t.requester?.name || t.requester_name || "-");
    const date = timeAgo(t.created_at || t.createdAt);

    return `
      <tr id="ticket-row-${t.id}">
        <td>
          <div style="font-weight:700; font-size:15px; margin-bottom:6px">${subject}</div>
          <div style="font-size:12px; color:#888">${ticketNum} • Requester: <span id="req-${t.id}">${requester}</span></div>
        </td>
        <td><span class="badge-dept bg-blue" id="dept-${t.id}">-</span></td>
        <td>${category}</td>
        <td>${date}</td>
        <td>
          <button class="btn-assign" data-ticket-id="${t.id}" data-subject="${subject}">
            <i class="fa-solid fa-user-plus"></i> Pilih Teknisi
          </button>
        </td>
      </tr>
    `;
  }

  // Poll for new tickets without full reload
  async function checkForNewTickets() {
    try {
      const res = await fetchWithAuth(
        `${API_URL}/api/tickets?status=open&per_page=1`,
      );
      if (!res || !res.ok) return;

      const json = await res.json();
      const latest =
        json.data && json.data[0]
          ? json.data[0]
          : Array.isArray(json)
            ? json[0]
            : null;

      if (latest) {
        // You could logic here to check against a stored ID to show a toast
        // For now, we can just silently update the badge if we have the count
        // Or trigger a reload if user is on page 1
        if (currentPage === 1) {
          // Optional: loadTickets(1); // Auto reload? Maybe intrusive.
        }
      }
    } catch (e) {
      console.warn("Polling error", e);
    }
  }

  // --- MODAL & LOGIC ---

  function openAssignModal(id, subject) {
    modalTicketId.innerText = "#" + id;
    modalTicketSubject.innerText = subject;
    modalTicketIdInput.value = id;
    assignModal.style.display = "flex";

    document.getElementById("modalAssignedTo").innerText =
      "Requester: Loading...";

    // Load tech list
    loadTechnicians();

    // Fetch detail to show requester in modal
    fetchWithAuth(`${API_URL}/api/tickets/${id}`)
      .then((r) => r.json())
      .then((json) => {
        const t = json.data || json.ticket || json;
        const rName = t.requester?.name || t.requester_name || "-";
        document.getElementById("modalAssignedTo").innerText =
          `Requester: ${rName}`;
      });
  }

  function closeAssignModal() {
    assignModal.style.display = "none";
  }

  async function loadTechnicians() {
    if (_techniciansCache) {
      populateTechSelect(_techniciansCache);
      return;
    }

    techLoading.style.display = "block";
    try {
      // Assuming endpoint exists for technicians
      const res = await fetchWithAuth(`${API_URL}/api/users?role=technician`); // Adjust endpoint if needed
      // Fallback if specific endpoint doesn't exist, you might need to filter all users

      if (res && res.ok) {
        const json = await res.json();
        let users = json.data || (Array.isArray(json) ? json : []);
        // Filter if endpoint returns all users
        users = users.filter(
          (u) =>
            (u.roles && u.roles.includes("technician")) ||
            u.role === "technician",
        );
        _techniciansCache = users;
        populateTechSelect(users);
      }
    } catch (e) {
      techSelect.innerHTML = '<option value="">Gagal memuat</option>';
    } finally {
      techLoading.style.display = "none";
    }
  }

  function populateTechSelect(users) {
    techSelect.innerHTML =
      '<option value="">-- Pilih Personil --</option>' +
      users
        .map((u) => `<option value="${u.id}">${escapeHtml(u.name)}</option>`)
        .join("");
  }

  async function saveAssignment() {
    const ticketId = modalTicketIdInput.value;
    const techId = techSelect.value;
    const saveBtn = document.getElementById("assignSaveBtn");

    if (!techId) {
      Swal.fire(
        "Pilih Teknisi",
        "Silakan pilih teknisi terlebih dahulu.",
        "warning",
      );
      return;
    }

    saveBtn.innerText = "Mengirim...";
    saveBtn.disabled = true;

    try {
      const res = await fetchWithAuth(
        `${API_URL}/api/tickets/${ticketId}/assign`,
        {
          method: "POST",
          body: JSON.stringify({ assigned_to: techId }), // Adjust payload key if needed (e.g. technician_id)
        },
      );

      if (res && res.ok) {
        Swal.fire("Berhasil", "Tiket berhasil ditugaskan.", "success");
        closeAssignModal();
        loadTickets(currentPage); // Refresh list
      } else {
        const err = await res.json();
        Swal.fire("Gagal", err.message || "Gagal assign tiket.", "error");
      }
    } catch (e) {
      Swal.fire("Error", "Terjadi kesalahan sistem.", "error");
    } finally {
      saveBtn.innerText = "Simpan & Kirim";
      saveBtn.disabled = false;
    }
  }

  // --- HELPERS ---

  async function updateRequesterDetails(ticket) {
    // If dept is missing in list, try to fetch user detail
    const deptEl = document.getElementById(`dept-${ticket.id}`);
    if (!deptEl || deptEl.innerText !== "-") return;

    if (ticket.requester?.department?.name) {
      deptEl.innerText = ticket.requester.department.name;
      return;
    }

    // If we have requester ID but no dept details, fetch user
    if (ticket.requester?.id) {
      if (_userCache.has(ticket.requester.id)) {
        const u = _userCache.get(ticket.requester.id);
        if (u.department?.name) deptEl.innerText = u.department.name;
      } else {
        try {
          const res = await fetchWithAuth(
            `${API_URL}/api/users/${ticket.requester.id}`,
          );
          if (res.ok) {
            const json = await res.json();
            const u = json.data || json.user;
            _userCache.set(ticket.requester.id, u);
            if (u.department?.name) deptEl.innerText = u.department.name;
          }
        } catch (e) {}
      }
    }
  }

  function updateBadgeCount(count) {
    const badge = document.querySelector(".alert-badge span");
    if (badge) badge.innerText = `${count} Tiket Perlu Tindakan`;

    // Also update sidebar badge if present
    const sbBadge = document.getElementById("pendingCount");
    if (sbBadge) {
      sbBadge.innerText = count;
      sbBadge.style.display = count > 0 ? "inline-block" : "none";
    }
  }

  function renderPagination(meta, count) {
    const container = document.getElementById("ticketsPagination");
    if (!container) return;

    container.innerHTML = "";
    if (!meta && count < PER_PAGE) return;

    const current = meta ? meta.current_page : currentPage;
    const last = meta ? meta.last_page : 1;

    let html = `<button class="btn btn-sm" onclick="loadTickets(${Math.max(1, current - 1)})">Prev</button>`;
    html += `<button class="btn btn-sm" onclick="loadTickets(${current + 1})">Next</button>`; // Simplified for now

    // You can implement full pagination buttons logic here if meta is robust
    container.innerHTML = html;
  }

  function escapeHtml(text) {
    if (!text) return "";
    return text.replace(/[&<>"']/g, function (m) {
      return {
        "&": "&amp;",
        "<": "&lt;",
        ">": "&gt;",
        '"': "&quot;",
        "'": "&#039;",
      }[m];
    });
  }

  function timeAgo(dateString) {
    if (!dateString) return "-";
    const date = new Date(dateString);
    const now = new Date();
    const seconds = Math.floor((now - date) / 1000);

    if (seconds < 60) return "Baru saja";
    const minutes = Math.floor(seconds / 60);
    if (minutes < 60) return `${minutes} menit lalu`;
    const hours = Math.floor(minutes / 60);
    if (hours < 24) return `${hours} jam lalu`;
    const days = Math.floor(hours / 24);
    return `${days} hari lalu`;
  }

  // Expose loadTickets globally if needed for pagination onclicks
  window.loadTickets = loadTickets;
});
