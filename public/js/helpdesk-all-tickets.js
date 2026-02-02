document.addEventListener("DOMContentLoaded", function () {
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
      const res = await fetchWithAuth(`${API_URL}/api/tickets?per_page=100`);

      if (!res || !res.ok) throw new Error("Gagal mengambil data");

      const json = await res.json();
      let rawData = [];

      // Handle struktur response Laravel (data wrapper)
      if (json.data && Array.isArray(json.data)) rawData = json.data;
      else if (Array.isArray(json)) rawData = json;
      else if (json.tickets) rawData = json.tickets;

      // Mapping Awal (Data Lite dari List)
      allTickets = rawData.map((t) => ({
        id: t.id,
        ticket_number: t.ticket_number || `T-${t.id}`,
        subject: t.title || t.subject || "-",
        // Data ini mungkin null di list, nanti diupdate via updateRowDetails
        requester: t.requester
          ? t.requester.name
          : t.requester_name || "Loading...",
        dept:
          t.requester && t.requester.department
            ? t.requester.department.name
            : t.department_name || "-",
        tech:
          t.assignment && t.assignment.technician
            ? t.assignment.technician.name
            : t.technician_name || "-",
        status: t.status || "Pending",
        date: t.created_at || t.date,
        // Simpan raw object untuk referensi
        raw: t,
      }));

      // Sort (Terbaru diatas)
      allTickets.sort((a, b) => b.id - a.id);

      filteredTickets = [...allTickets];
      renderTable(1);
    } catch (e) {
      console.error("Load Error:", e);
      tableBody.innerHTML =
        '<tr><td colspan="6" style="text-align:center; padding:30px; color:#d62828;"><i class="fa-solid fa-circle-exclamation"></i> Gagal memuat data tiket.</td></tr>';
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
        if (
          detail.requester &&
          detail.requester.department &&
          detail.requester.department.name
        )
          deptEl.innerText = detail.requester.department.name;
        else if (detail.department && detail.department.name)
          deptEl.innerText = detail.department.name;
        else if (detail.department_name)
          deptEl.innerText = detail.department_name;
      }

      // 3. Update Technician
      if (techEl) {
        if (
          detail.assignment &&
          detail.assignment.technician &&
          detail.assignment.technician.name
        )
          techEl.innerText = detail.assignment.technician.name;
        else if (detail.technician && detail.technician.name)
          techEl.innerText = detail.technician.name;
        else if (detail.assigned_to_name)
          techEl.innerText = detail.assigned_to_name;
        else techEl.innerText = "-";
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
      // Render basic data
      document.getElementById("mId").innerText =
        `#${detail.ticket_number || detail.id}`;
      document.getElementById("mSubject").innerText =
        detail.title || detail.subject;

      let deptTxt = "-";
      if (detail.requester?.department?.name)
        deptTxt = detail.requester.department.name;
      else if (detail.department?.name) deptTxt = detail.department.name;

      document.getElementById("mDept").innerText =
        `${deptTxt} • ${detail.status}`;

      // Render Timeline (Logs/Histories)
      const logs = detail.histories || detail.logs || [];
      renderTimeline(logs);
    }
  };

  function renderTimeline(logs) {
    const container = document.getElementById("mTimeline");
    if (!logs || logs.length === 0) {
      container.innerHTML =
        '<div style="color:#999; font-style:italic;">Belum ada riwayat.</div>';
      return;
    }

    let html = "";
    logs
      .sort((a, b) => (b.id || 0) - (a.id || 0))
      .forEach((l) => {
        const date = new Date(l.created_at || l.date).toLocaleString("id-ID");
        html += `
                <div class="timeline-item">
                    <div class="timeline-dot"></div>
                    <span class="timeline-date">${date}</span>
                    <div class="timeline-content">
                        <strong>${l.status || l.action || "Update"}</strong>
                        <div style="margin-top:4px; color:#666;">${l.note || l.message || l.description || ""}</div>
                    </div>
                </div>
            `;
      });
    container.innerHTML = html;
  }

  window.closeModal = function () {
    document.getElementById("detailModal").style.display = "none";
  };

  window.onclick = function (e) {
    const modal = document.getElementById("detailModal");
    if (e.target == modal) closeModal();
  };

  // INIT
  loadTickets();
});
