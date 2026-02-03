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
  // Pending preselection when ticket detail loads before tech list
  let _pendingAssignedTechId = null;
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
      // Use the same source as Dashboard for unassigned tickets
      const dashboardUrl = window.DASHBOARD_API || `${API_URL}/api/dashboard`;
      console.debug("loadTickets: fetching dashboard from", dashboardUrl);
      const res = await fetchWithAuth(dashboardUrl);
      if (!res || !res.ok) throw new Error("Gagal load data dashboard");

      const json = await res.json();
      const data = json.data || {};
      let tickets = data.unassigned_tickets || [];

      // Update badge count based on dashboard count
      updateBadgeCount(tickets.length);
      if (window.updateOpenUnassignedCount) {
        window.updateOpenUnassignedCount();
      }

      if (tickets.length === 0) {
        ticketsBody.innerHTML =
          '<tr><td colspan="5" style="text-align:center; padding:40px 0; color:#666">Tidak ada tiket baru.</td></tr>';
        document.getElementById("ticketsPagination").innerHTML = "";
        return;
      }

      // Render using fields returned by dashboard API
      ticketsBody.innerHTML = tickets.map(renderTicketRow).join("");

      // Update requester/department if some data missing (and enrich rows with full ticket detail)
      tickets.forEach((t) => {
        updateRequesterDetails(t);
        // Also fetch full ticket detail to ensure ticket_number, requester and department display correctly in the row
        populateRowDetailsIncoming(t.id);
      });

      // Bind assign buttons
      document
        .querySelectorAll("button.btn-assign[data-ticket-id]")
        .forEach((btn) => {
          btn.addEventListener("click", function () {
            // Use ticket id (numeric) and subject from data attributes
            openAssignModal(this.dataset.ticketId, this.dataset.subject);
          });
        });

      // Dashboard response does not include pagination meta for tickets; keep it simple
      renderPagination(null, tickets.length);
    } catch (err) {
      console.error("Error loading tickets", err);
      ticketsBody.innerHTML =
        '<tr><td colspan="5" style="text-align:center; padding:40px 0; color:#d62828">Gagal memuat tiket.</td></tr>';
    }
  }

  function renderTicketRow(t) {
    const ticketNum =
      t.ticket_number || t.ticketNumber || (t.id ? `#${t.id}` : "-");
    const subject = escapeHtml(t.subject || t.title || "-");
    const category = escapeHtml(t.category?.name || t.category || "-");
    const requester = escapeHtml(
      t.requester?.name || t.requester_name || t.requester_name_text || "-",
    );
    const date = timeAgo(t.created_at || t.createdAt || t.created_at_text);

    // department: prefer ticket.department.name then requester.department
    const deptName = escapeHtml(
      t.department?.name ||
        t.requester?.department?.name ||
        t.department ||
        "-",
    );

    return `
      <tr id="ticket-row-${t.id}">
        <td>
          <div class="ticket-subject">${subject}</div>
          <div style="font-size:12px; color:#999">${ticketNum} • User: <span id="req-${t.id}">${requester}</span></div>
        </td>
        <td><span class="badge-dept bg-blue" id="dept-${t.id}">${deptName}</span></td>
        <td>${category}</td>
        <td>${date}</td>
        <td>
          <button class="btn-assign" data-ticket-id="${t.id}" data-subject="${subject}" data-ticket-number="${ticketNum}">
            <i class="fa-solid fa-user-plus"></i> Pilih Teknisi
          </button>
        </td>
      </tr>
    `;
  }

  // Poll for new tickets without full reload
  async function checkForNewTickets() {
    try {
      const pollUrl = `${window.TICKET_API_BASE || API_URL + "/api/tickets"}?status=open&per_page=1`;
      const res = await fetchWithAuth(pollUrl);
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
    modalTicketIdInput.value = id;
    modalTicketSubject.innerText = subject;
    assignModal.style.display = "flex";

    document.getElementById("modalAssignedTo").innerText = "User: Loading...";
    document.getElementById("modalTicketDesc").innerText = "Loading...";

    // Fetch ticket detail to get ticket_number, requester, and description
    const ticketDetailUrl = `${window.TICKET_API_BASE || API_URL + "/api/tickets"}/${id}`;
    console.debug("Fetching ticket detail from", ticketDetailUrl);

    fetchWithAuth(ticketDetailUrl)
      .then((r) => {
        if (!r || !r.ok) throw new Error("Detail fetch failed");
        return r.json();
      })
      .then((json) => {
        const t = json.data || json.ticket || json;
        const ticketNum = t.ticket_number || `#${t.id}`;
        const rName = t.requester?.name || t.requester_name || "-";
        const description = t.description || "-";
        modalTicketId.innerText = ticketNum;
        document.getElementById("modalAssignedTo").innerText = `User: ${rName}`;
        document.getElementById("modalTicketDesc").innerText =
          escapeHtml(description);

        // Preselect tech if already assigned (if tech list not yet loaded, store pending id)
        if (t.assignment?.technician?.id) {
          if (_techniciansCache) {
            techSelect.value = t.assignment.technician.id;
          } else {
            _pendingAssignedTechId = t.assignment.technician.id;
          }
        }
      })
      .catch((e) => {
        console.warn("Detail fetch error", e);
        modalTicketId.innerText = `#${id}`;
        document.getElementById("modalTicketDesc").innerText = "-";
      });

    // Load tech list
    loadTechnicians();
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
      const techUrl =
        window.TECHNICIANS_API || `${API_URL}/api/users/by-role/technician`;
      console.debug("loadTechnicians: fetching from", techUrl);
      const res = await fetchWithAuth(techUrl);
      if (!res || !res.ok) throw new Error("Gagal fetch teknisi");

      const json = await res.json();
      let users = json.data || (Array.isArray(json) ? json : []);
      _techniciansCache = users;
      populateTechSelect(users);
    } catch (e) {
      console.warn("Technician load error", e);
      techSelect.innerHTML = '<option value="">Gagal memuat teknisi</option>';
    } finally {
      techLoading.style.display = "none";
    }
  }

  function populateTechSelect(users) {
    console.debug("populateTechSelect: received", users?.length);
    if (!Array.isArray(users)) users = [];

    // dedupe by id
    const uniq = Array.from(new Map(users.map((u) => [u.id, u])).values());

    // Clear existing options
    techSelect.options.length = 0;

    // Default placeholder
    const defaultOpt = document.createElement("option");
    defaultOpt.value = "";
    defaultOpt.text = "-- Pilih Personil --";
    techSelect.appendChild(defaultOpt);

    // Add options with department and emoji
    uniq.forEach((u) => {
      const deptName =
        u.department?.name ||
        u.department_name ||
        u.departemen ||
        u.dept ||
        "N/A";
      const display = `👨‍🔧 ${u.name} (${deptName})`;
      const opt = document.createElement("option");
      opt.value = u.id;
      opt.text = display;
      techSelect.appendChild(opt);
    });

    // If we had a pending selected technician, apply it now
    if (_pendingAssignedTechId) {
      techSelect.value = _pendingAssignedTechId;
      _pendingAssignedTechId = null;
    }
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
      // Log ticketId/techId for debugging
      console.log("Assigning ticket", ticketId, "to tech", techId);

      const assignUrl = `${window.TICKET_API_BASE || API_URL + "/api/tickets"}/${ticketId}/assign`;
      console.debug("Assign POST to", assignUrl, "payload", {
        assigned_to: techId,
      });
      const res = await fetchWithAuth(assignUrl, {
        method: "POST",
        body: JSON.stringify({ assigned_to: techId }),
      });

      if (res && res.ok) {
        // Some APIs return 204 No Content; handle empty response gracefully
        let responseData = null;
        try {
          if (res.status !== 204) {
            responseData = await res.json();
          }
        } catch (parseErr) {
          console.debug("Assign response parse error (non-fatal):", parseErr);
        }

        console.log("Assign success response:", responseData);
        Swal.fire("Berhasil", "Tiket berhasil ditugaskan.", "success");
        closeAssignModal();
        loadTickets(currentPage); // Refresh incoming list
        if (typeof window.loadDashboard === "function") window.loadDashboard();
      } else {
        // Safely parse error body (may not be JSON)
        let errBody = null;
        let errText = `Request failed with status ${res ? res.status : "unknown"}`;
        try {
          if (res) {
            errBody = await res.clone().json();
            errText = errBody.message || JSON.stringify(errBody) || errText;
          }
        } catch (parseErr) {
          try {
            errText = res ? await res.text() : errText;
          } catch (_) {}
        }

        console.error(
          "Assign error:",
          res ? res.status : "no response",
          errBody || errText,
        );
        Swal.fire(
          "Gagal",
          errBody?.message || errText || "Gagal assign tiket.",
          "error",
        );
      }
    } catch (e) {
      console.error("Assignment error:", e);
      Swal.fire("Error", e.message || "Terjadi kesalahan sistem.", "error");
    } finally {
      saveBtn.innerText = "Simpan & Kirim";
      saveBtn.disabled = false;
    }
  }

  // --- HELPERS ---

  // Fetch full ticket detail to enrich the rendered row (shows ticket_number, requester name, department)
  async function populateRowDetailsIncoming(id) {
    try {
      const url = `${window.TICKET_API_BASE || API_URL + "/api/tickets"}/${id}`;
      console.debug("populateRowDetailsIncoming fetching", url);
      const r = await fetchWithAuth(url);
      if (!r || !r.ok) return;
      const json = await r.json();
      const t = json.data || json.ticket || json;

      const requesterName = t.requester?.name || t.requester_name || "-";
      const deptName =
        t.requester?.department?.name ||
        t.department?.name ||
        t.department_name ||
        "-";
      const ticketNumber = t.ticket_number || `#${t.id}`;

      const reqEl = document.getElementById(`req-${id}`);
      const deptEl = document.getElementById(`dept-${id}`);

      if (reqEl) {
        // Update parent line to show ticket number and user
        const parent = reqEl.parentNode;
        parent.innerHTML = `${escapeHtml(ticketNumber)} • User: <span id="req-${id}">${escapeHtml(requesterName)}</span>`;
      }

      if (deptEl) deptEl.innerText = escapeHtml(deptName);
    } catch (e) {
      console.warn("populateRowDetailsIncoming error", e);
    }
  }

  async function updateRequesterDetails(ticket) {
    const reqEl = document.getElementById(`req-${ticket.id}`);
    const deptEl = document.getElementById(`dept-${ticket.id}`);

    // Try to fill from ticket data first
    if (reqEl && ticket.requester?.name) {
      reqEl.innerText = escapeHtml(ticket.requester.name);
    }
    if (deptEl && ticket.requester?.department?.name) {
      deptEl.innerText = escapeHtml(ticket.requester.department.name);
      return;
    }

    // If still missing, fetch user detail
    if (ticket.requester?.id) {
      try {
        if (_userCache.has(ticket.requester.id)) {
          const u = _userCache.get(ticket.requester.id);
          if (reqEl && u.name) reqEl.innerText = escapeHtml(u.name);
          if (deptEl && u.department?.name)
            deptEl.innerText = escapeHtml(u.department.name);
        } else {
          const res = await fetchWithAuth(
            `${API_URL}/api/users/${ticket.requester.id}`,
          );
          if (res && res.ok) {
            const json = await res.json();
            const u = json.data || json.user || json;
            _userCache.set(ticket.requester.id, u);
            if (reqEl && u.name) reqEl.innerText = escapeHtml(u.name);
            if (deptEl && u.department?.name)
              deptEl.innerText = escapeHtml(u.department.name);
          }
        }
      } catch (e) {
        console.warn("User detail fetch error", e);
      }
    }
  }

  function updateBadgeCount(count) {
    const badge = document.querySelector(".alert-badge span");
    if (badge) {
      badge.innerText = `${count} Tiket Perlu Tindakan`;
    }

    // Also update sidebar badge if present
    const sbBadge = document.querySelector(".menu-badge");
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
