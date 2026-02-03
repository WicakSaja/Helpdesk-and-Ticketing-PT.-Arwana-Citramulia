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
  const rowsPerPage = 10;
  const authToken =
    sessionStorage.getItem("auth_token") || localStorage.getItem("auth_token");
  // Cache untuk menyimpan data user/detail agar tidak fetch berulang kali
  const _detailCache = new Map();

  let allTickets = [];
  let filteredTickets = [];
  let currentPage = 1;

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

  // === 1. LOAD DATA LIST (LITE DATA) ===
  async function loadTickets() {
    tableBody.innerHTML =
      '<tr><td colspan="6" class="loading-row"><i class="fa-solid fa-spinner fa-spin"></i> Memuat data tiket...</td></tr>';
    if (noData) noData.style.display = "none";

    try {
      // Ambil data per_page 100 agar search client-side maksimal
      const apiUrl = `${API_URL}/api/tickets?per_page=100`;
      console.log("🔄 Fetching tickets from:", apiUrl);
      const res = await fetchWithAuth(apiUrl);

      if (!res) {
        throw new Error("Network request failed (res is null)");
      }
      if (!res.ok) {
        throw new Error(`HTTP ${res.status}: ${res.statusText}`);
      }

      const json = await res.json();
      console.log("📦 Raw API Response:", json);
      let rawData = [];

      // Handle struktur response Laravel (data wrapper)
      if (json.data && Array.isArray(json.data)) {
        rawData = json.data;
        console.log(`✅ Parsed as json.data with ${rawData.length} items`);
      } else if (Array.isArray(json)) {
        rawData = json;
        console.log(`✅ Parsed as direct array with ${rawData.length} items`);
      } else if (json.tickets && Array.isArray(json.tickets)) {
        rawData = json.tickets;
        console.log(`✅ Parsed as json.tickets with ${rawData.length} items`);
      } else {
        console.warn("⚠️ Unexpected response format, raw content:", json);
        rawData = [];
      }

      // Mapping Awal (Data Lite dari List)
      allTickets = rawData.map((t) => {
        // Log first few items untuk debugging
        if (rawData.indexOf(t) < 2) {
          console.log(`📌 Ticket ${t.id} structure:`, {
            id: t.id,
            has_requester: !!t.requester,
            requester_name: t.requester?.name,
            has_department: !!(t.requester?.department || t.department),
            department_name:
              t.requester?.department?.name || t.department?.name,
            full_requester: t.requester,
          });
        }

        return {
          id: t.id,
          ticket_number: t.ticket_number || `T-${t.id}`,
          subject: t.title || t.subject || "-",
          requester: t.requester?.name || t.requester_name || "Loading...",
          requester_id: t.requester?.id,
          dept:
            t.requester?.department?.name ||
            t.department?.name ||
            t.department_name ||
            null,
          tech: t.assignment?.technician?.name || t.technician_name || "-",
          status: t.status?.name || t.status || "Pending",
          date: t.created_at || t.date,
          raw: t,
        };
      });

      // Sort (Terbaru diatas)
      allTickets.sort((a, b) => b.id - a.id);

      filteredTickets = [...allTickets];
      renderTable(1);
      console.log(`✨ Successfully loaded ${allTickets.length} tickets`);
    } catch (e) {
      console.error("❌ Load Error:", e);
      tableBody.innerHTML = `<tr><td colspan="6" style="text-align:center; padding:30px; color:#d62828;"><i class="fa-solid fa-circle-exclamation"></i> <br />Gagal memuat data tiket.<br /><small style="font-size:11px; color:#999;">${escapeHtml(e.message)}</small></td></tr>`;
    }
  }
  // === 2. RENDER TABLE ===
  function renderTable(page) {
    currentPage = page;
    tableBody.innerHTML = "";

    if (filteredTickets.length === 0) {
      if (noData) noData.style.display = "block";
      paginationControls.innerHTML = "";
      return;
    }

    if (noData) noData.style.display = "none";

    // Client-side Pagination
    const start = (page - 1) * rowsPerPage;
    const end = start + rowsPerPage;
    const paginated = filteredTickets.slice(start, end);

    paginated.forEach((t) => {
      // Mapping Status Class
      let statusClass = "status-pending";
      const s = String(t.status).toLowerCase();
      if (s.includes("open")) statusClass = "status-open";
      else if (s.includes("progress")) statusClass = "status-progress";
      else if (
        s.includes("close") ||
        s.includes("done") ||
        s.includes("solved")
      )
        statusClass = "status-resolved";
      else if (s.includes("reject")) statusClass = "status-rejected";

      // Format Tanggal
      const dateStr = new Date(t.date).toLocaleDateString("id-ID", {
        day: "numeric",
        month: "short",
        year: "numeric",
      });

      const row = `
                <tr id="row-${t.id}">
                    <td><strong>${t.ticket_number}</strong></td>
                    <td>
                        <div style="font-weight:600; color:#333;">${t.subject}</div>
                        <div style="font-size:12px; color:#888;">
                            Oleh: <span id="req-name-${t.id}">${t.requester}</span>
                        </div>
                    </td>
                    <td><span id="dept-name-${t.id}">${t.dept}</span></td>
                    <td><span id="tech-name-${t.id}">${t.tech}</span></td>
                    <td><span class="status-badge ${statusClass}">${t.status}</span></td>
                    <td style="text-align: right;">
                        <button class="btn-view" onclick="openDetailById(${t.id})" title="Lihat Detail">
                            <i class="fa-solid fa-eye"></i>
                        </button>
                    </td>
                </tr>
            `;
      tableBody.innerHTML += row;

      // === KEY FIX: JALANKAN UPDATE ROW DI BACKGROUND ===
      // Ini akan fetch detail per tiket untuk mengisi data yang 'Hancur/Hilang'
      setTimeout(() => updateRowDetails(t.id), 0);
    });

    renderPaginationControls();
  }

  // === 3. UPDATE ROW DETAILS (LOGIKA SAKTI DARI KODEMU) ===
  async function updateRowDetails(id) {
    // Cek elemen dulu, kalau tidak ada di layar (karena pagination), gak usah fetch
    const reqEl = document.getElementById(`req-name-${id}`);
    const deptEl = document.getElementById(`dept-name-${id}`);
    const techEl = document.getElementById(`tech-name-${id}`);

    if (!reqEl && !deptEl && !techEl) return;

    // Cek cache dulu
    let detail = _detailCache.get(id);

    if (!detail) {
      try {
        // Fetch Full Detail Tiket
        const res = await fetchWithAuth(`${API_URL}/api/tickets/${id}`);
        if (!res || !res.ok) return;
        const json = await res.json();
        detail = json.data || json.ticket || json;

        // Simpan ke cache
        _detailCache.set(id, detail);
      } catch (e) {
        console.warn("Detail fetch error", e);
        return;
      }
    }

    if (detail) {
      // 1. Update Requester
      if (reqEl) {
        if (detail.requester && detail.requester.name)
          reqEl.innerText = detail.requester.name;
        else if (detail.requester_name) reqEl.innerText = detail.requester_name;
      }

      // 2. Update Department
      if (deptEl) {
        let deptName =
          (detail.requester &&
            detail.requester.department &&
            detail.requester.department.name) ||
          (detail.department && detail.department.name) ||
          detail.department_name ||
          null;

        if (!deptName && detail.requester?.id) {
          try {
            const userRes = await fetchWithAuth(
              `${API_URL}/api/users/${detail.requester.id}`,
            );
            if (userRes && userRes.ok) {
              const userData = await userRes.json();
              const user = userData.data || userData.user || userData;
              deptName = user.department?.name || user.departemen || null;
            }
          } catch (e) {
            console.warn("Failed to fetch user for department", e);
          }
        }

        deptEl.innerText = deptName || "-";
      }

      // 3. Update Technician (include department if available)
      if (techEl) {
        let techText = "-";
        if (
          detail.assignment &&
          detail.assignment.technician &&
          detail.assignment.technician.name
        ) {
          const tech = detail.assignment.technician;
          const techDept =
            tech.department?.name || tech.department_name || tech.dept || null;
          techText = techDept ? `${tech.name} (${techDept})` : tech.name;
        } else if (detail.technician && detail.technician.name) {
          const tech = detail.technician;
          const techDept =
            tech.department?.name || tech.department_name || tech.dept || null;
          techText = techDept ? `${tech.name} (${techDept})` : tech.name;
        } else if (detail.assigned_to_name) {
          techText = detail.assigned_to_name;
        }
        techEl.innerText = techText;
      }
    }
  }

  // === 4. PAGINATION CONTROLS ===
  function renderPaginationControls() {
    paginationControls.innerHTML = "";
    const total = filteredTickets.length;
    const pages = Math.ceil(total / rowsPerPage);

    const info = document.getElementById("paginationInfo");
    if (info)
      info.innerText = `Menampilkan halaman ${currentPage} dari ${pages}`;

    if (pages <= 1) return;

    const prev = createBtn(
      '<i class="fa-solid fa-chevron-left"></i>',
      currentPage === 1,
      () => {
        if (currentPage > 1) renderTable(currentPage - 1);
      },
    );
    paginationControls.appendChild(prev);

    const next = createBtn(
      '<i class="fa-solid fa-chevron-right"></i>',
      currentPage === pages,
      () => {
        if (currentPage < pages) renderTable(currentPage + 1);
      },
    );
    paginationControls.appendChild(next);
  }

  function createBtn(html, disabled, onClick) {
    const div = document.createElement("div");
    div.className = `page-btn ${disabled ? "disabled" : ""}`;
    div.style.cssText = disabled ? "opacity: 0.5; cursor: not-allowed;" : "";
    div.innerHTML = html;
    if (!disabled) div.onclick = onClick;
    return div;
  }

  // === 5. SEARCH ===
  if (searchInput) {
    searchInput.addEventListener("input", function (e) {
      const q = e.target.value.toLowerCase().trim();

      if (!q) {
        filteredTickets = [...allTickets];
      } else {
        // Filter berdasarkan data yang sudah di-load (Lite Data)
        // Note: Data detail yg di-fetch background mungkin belum masuk sini,
        // tapi ticket number dan subject biasanya sudah ada.
        filteredTickets = allTickets.filter(
          (t) =>
            String(t.ticket_number).toLowerCase().includes(q) ||
            String(t.subject).toLowerCase().includes(q) ||
            String(t.requester).toLowerCase().includes(q),
        );
      }
      // Reset ke halaman 1
      renderTable(1);
    });
  }

  // === 6. MODAL DETAIL ===
  window.openDetailById = async function (id) {
    const modal = document.getElementById("detailModal");

    // Tampilkan modal loading
    modal.style.display = "flex";
    document.getElementById("mTimeline").innerHTML =
      '<div style="text-align:center; padding:20px;">Memuat...</div>';

    // Cek Cache Detail dulu (karena mungkin sudah di-load row tadi)
    let detail = _detailCache.get(id);

    if (!detail) {
      try {
        const res = await fetchWithAuth(`${API_URL}/api/tickets/${id}`);
        if (res && res.ok) {
          const json = await res.json();
          detail = json.data || json.ticket || json;
          _detailCache.set(id, detail);
        }
      } catch (e) {
        console.warn("Detail fetch failed", e);
      }
    }

    if (detail) {
      // Get data
      const requesterName = escapeHtml(
        detail.requester?.name || detail.requester_name || "-",
      );
      const technicianName = escapeHtml(
        (detail.assignment &&
          detail.assignment.technician &&
          detail.assignment.technician.name) ||
          detail.technician_name ||
          "-",
      );
      let deptTxt = "-";
      if (detail.requester?.department?.name)
        deptTxt = detail.requester.department.name;
      else if (detail.department?.name) deptTxt = detail.department.name;

      const currentStatus = detail.status?.name || detail.status || "-";
      const isResolved = currentStatus.toUpperCase() === "RESOLVED";

      // Update header
      document.getElementById("mId").innerText =
        `#${detail.ticket_number || detail.id}`;
      document.getElementById("mSubject").innerText = escapeHtml(
        detail.title || detail.subject || "-",
      );
      document.getElementById("mDept").innerText =
        `${deptTxt} • Status: ${currentStatus}`;

      // Render detail info
      const detailContent = document.getElementById("modalDetailContent");
      detailContent.innerHTML =
        `
        <div style="margin-bottom: 20px;">
          <h4 id="mSubject" style="font-size: 18px; font-weight: 700; color: #333;">` +
        escapeHtml(detail.title || detail.subject || "-") +
        `</h4>
          <p id="mDept" style="color: #666; font-size: 13px;">${deptTxt} • Status: ${currentStatus}</p>
        </div>
        
        <div style="margin-bottom: 20px; background: #f9f9f9; padding: 15px; border-radius: 8px;">
          <div style="margin-bottom: 12px;">
            <label style="font-size: 12px; color: #666; font-weight: 600;">Pengaju</label>
            <div style="font-size: 14px; color: #333; font-weight: 500;">${requesterName}</div>
          </div>
          <div style="margin-bottom: 12px;">
            <label style="font-size: 12px; color: #666; font-weight: 600;">Departemen</label>
            <div style="font-size: 14px; color: #333; font-weight: 500;">${deptTxt}</div>
          </div>
          <div>
            <label style="font-size: 12px; color: #666; font-weight: 600;">Teknisi Ditugaskan</label>
            <div style="font-size: 14px; color: #333; font-weight: 500;">${technicianName}</div>
          </div>
        </div>

        <div class="detail-group">
          <label class="detail-label">Riwayat Perjalanan</label>
          <div class="timeline" id="mTimeline" style="margin-top: 10px;"></div>
        </div>

        <div class="detail-group" style="margin-top: 20px; padding-top: 15px; border-top: 1px solid #e9e9e9;">
          <div style="display: flex; gap: 10px;">
            <button id="rejectBtn" class="btn-reject" ${!isResolved ? 'disabled style="opacity: 0.5; cursor: not-allowed;"' : ""} 
              onclick="window.rejectTicket(${detail.id})" title="${!isResolved ? "Hanya bisa reject jika status RESOLVED" : "Reject dan buat OPEN kembali"}">
              <i class="fa-solid fa-times"></i> Reject
            </button>
            <button id="closeBtn" class="btn-close" ${!isResolved ? 'disabled style="opacity: 0.5; cursor: not-allowed;"' : ""}
              onclick="window.closeTicket(${detail.id})" title="${!isResolved ? "Hanya bisa close jika status RESOLVED" : "Tutup tiket"}">
              <i class="fa-solid fa-check"></i> Close
            </button>
          </div>
        </div>
      `;

      // Render Timeline
      const logs = detail.logs || detail.histories || [];
      renderTimeline(logs, currentStatus);

      // Store detail for action buttons
      window._currentTicketDetail = detail;
    }
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
