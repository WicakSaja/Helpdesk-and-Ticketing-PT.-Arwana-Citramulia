(function () {
  // === CONFIGURATION ===
  const REPORTS_URL = "/api/tickets";
  const EXPORT_URL = "/api/export";
  const ITEMS_PER_PAGE = 10;

  // State Variables
  let currentTab = "weekly";
  let selectedYear = new Date().getFullYear();
  let selectedMonth = new Date().getMonth() + 1;
  let selectedWeek = 1;
  let currentPage = 1;
  let allData = [];
  let dataFetched = false;
  let isFetching = false; // Prevent multiple concurrent fetches
  let lastFetchId = null; // Track which fetch is the latest

  // Elements
  const btnWeekly = document.getElementById("tabWeekly");
  const btnMonthly = document.getElementById("tabMonthly");
  const btnYearly = document.getElementById("tabYearly");

  const filterYearGroup = document.getElementById("filterYearGroup");
  const filterMonthGroup = document.getElementById("filterMonthGroup");
  const filterWeekGroup = document.getElementById("filterWeekGroup");
  const selectYear = document.getElementById("selectYear");
  const selectMonth = document.getElementById("selectMonth");
  const selectWeek = document.getElementById("selectWeek");

  const tableHead = document.getElementById("tableHead");
  const tableBody = document.getElementById("tableBody");
  const labelPeriode = document.getElementById("labelPeriode");
  const paginationContainer = document.getElementById("paginationContainer");
  const paginationButtons = document.getElementById("paginationButtons");
  const paginationStart = document.getElementById("paginationStart");
  const paginationEnd = document.getElementById("paginationEnd");
  const paginationTotal = document.getElementById("paginationTotal");

  // === INITIALIZATION ===
  function init() {
    populateYears();
    populateWeeks();

    // Set nilai dropdown SEBELUM attach listeners untuk hindari multiple fetch
    selectYear.value = selectedYear;
    selectMonth.value = selectedMonth;
    selectWeek.value = selectedWeek;

    // Attach listeners SETELAH nilai dropdown diset
    setupEventListeners();

    // Panggil switchTab terakhir untuk trigger fetch sekali saja
    switchTab("weekly");
  }

  function populateYears() {
    const currentYear = new Date().getFullYear();
    selectYear.innerHTML = "";
    // 3 tahun ke depan dan 3 tahun sebelumnya
    for (let i = currentYear + 3; i >= currentYear - 3; i--) {
      let option = document.createElement("option");
      option.value = i;
      option.innerText = i;
      selectYear.appendChild(option);
    }
  }

  function populateWeeks() {
    selectWeek.innerHTML = "";
    for (let i = 1; i <= 4; i++) {
      let option = document.createElement("option");
      option.value = i;
      option.innerText = `Minggu ${i}`;
      selectWeek.appendChild(option);
    }
  }

  function setupEventListeners() {
    btnWeekly.addEventListener("click", () => switchTab("weekly"));
    btnMonthly.addEventListener("click", () => switchTab("monthly"));
    btnYearly.addEventListener("click", () => switchTab("yearly"));

    selectYear.addEventListener("change", () => {
      selectedYear = parseInt(selectYear.value);
      currentPage = 1;
      updateLabelPeriode();
      fetchData();
    });
    selectMonth.addEventListener("change", () => {
      selectedMonth = parseInt(selectMonth.value);
      currentPage = 1;
      updateLabelPeriode();
      fetchData();
    });
    selectWeek.addEventListener("change", () => {
      selectedWeek = parseInt(selectWeek.value);
      currentPage = 1;
      updateLabelPeriode();
      fetchData();
    });
  }

  // === LOGIC TAB & FILTER ===
  window.switchTab = function (type) {
    currentTab = type;
    currentPage = 1;

    [btnWeekly, btnMonthly, btnYearly].forEach((btn) =>
      btn.classList.remove("active")
    );
    if (type === "weekly") btnWeekly.classList.add("active");
    if (type === "monthly") btnMonthly.classList.add("active");
    if (type === "yearly") btnYearly.classList.add("active");

    // Kontrol visibility filter
    filterYearGroup.style.display = "block";
    // Tampilkan dropdown bulan saat mingguan dan bulanan
    filterMonthGroup.style.display =
      type === "weekly" || type === "monthly" ? "block" : "none";
    // Tampilkan dropdown minggu hanya saat mingguan
    filterWeekGroup.style.display = type === "weekly" ? "block" : "none";

    updateLabelPeriode();
    fetchData();
  };

  // === UPDATE DYNAMIC LABEL ===
  function updateLabelPeriode() {
    let label = "";
    if (currentTab === "weekly") {
      const weeks = getWeekDateRange();
      label = `Data Tiket Minggu ${selectedWeek} (${weeks.startDate} - ${weeks.endDate}) ${getMonthName(selectedMonth)} ${selectedYear}`;
    } else if (currentTab === "monthly") {
      label = `Data Tiket ${getMonthName(selectedMonth)} ${selectedYear}`;
    } else {
      label = `Laporan Tahunan ${selectedYear}`;
    }
    labelPeriode.innerText = label;
  }

  // === LOGIC HITUNG TANGGAL ===
  function getWeekDateRange() {
    const startDay = (selectedWeek - 1) * 7 + 1;
    const endDay = Math.min(
      selectedWeek * 7,
      new Date(selectedYear, selectedMonth, 0).getDate()
    );

    const formatDate = (day) => {
      return String(day).padStart(2, "0");
    };

    return {
      startDate: formatDate(startDay),
      endDate: formatDate(endDay),
    };
  }

  function getDateRange() {
    let startDate, endDate;

    if (currentTab === "weekly") {
      const startDay = (selectedWeek - 1) * 7 + 1;
      const endDay = Math.min(
        selectedWeek * 7,
        new Date(selectedYear, selectedMonth, 0).getDate()
      );

      startDate = `${selectedYear}-${String(selectedMonth).padStart(2, "0")}-${String(startDay).padStart(2, "0")}`;
      endDate = `${selectedYear}-${String(selectedMonth).padStart(2, "0")}-${String(endDay).padStart(2, "0")}`;
    } else if (currentTab === "monthly") {
      const lastDay = new Date(selectedYear, selectedMonth, 0).getDate();
      startDate = `${selectedYear}-${String(selectedMonth).padStart(2, "0")}-01`;
      endDate = `${selectedYear}-${String(selectedMonth).padStart(2, "0")}-${lastDay}`;
    } else {
      startDate = `${selectedYear}-01-01`;
      endDate = `${selectedYear}-12-31`;
    }

    return { start_date: startDate, end_date: endDate };
  }

  // === FETCH DATA ===
  async function fetchData() {
    // Prevent multiple concurrent fetches
    if (isFetching) {
      console.log("Fetch already in progress, skipping...");
      return;
    }

    isFetching = true;
    const currentFetchId = Date.now(); // Create unique ID for this fetch
    lastFetchId = currentFetchId;

    tableBody.innerHTML = `<tr><td colspan="7" class="loading-container"><i class="fa-solid fa-circle-notch fa-spin" style="font-size:24px; color:#d62828; margin-bottom:10px;"></i><br>Mengambil data...</td></tr>`;

    // Clear allData sebelum fetch untuk hindari stale data
    allData = [];
    dataFetched = false;

    const token =
      sessionStorage.getItem("auth_token") ||
      localStorage.getItem("auth_token");
    const dates = getDateRange();

    console.log("Fetching Data Range:", dates, "FetchID:", currentFetchId);

    // Buat URL dengan filter date range
    const url = new URL(REPORTS_URL, window.location.origin);
    url.searchParams.append("start_date", dates.start_date);
    url.searchParams.append("end_date", dates.end_date);
    url.searchParams.append("per_page", 1000); // Ambil banyak data untuk pagination client-side

    try {
      const response = await fetch(url.toString(), {
        method: "GET",
        headers: {
          Authorization: `Bearer ${token}`,
          Accept: "application/json",
        },
      });

      if (!response.ok) throw new Error("Gagal mengambil data dari server");

      const result = await response.json();

      // Only update data if this is still the latest fetch
      if (currentFetchId === lastFetchId) {
        allData = result.data || [];
        dataFetched = true;
        console.log("Data updated for FetchID:", currentFetchId);

        renderTable();
        renderPagination();
      } else {
        console.log("Ignoring stale fetch response FetchID:", currentFetchId, "Latest FetchID:", lastFetchId);
      }
    } catch (error) {
      console.error("Error:", error);
      if (currentFetchId === lastFetchId) {
        dataFetched = false;
        tableBody.innerHTML = `<tr><td colspan="7" style="text-align:center; padding:40px; color:#d62828;">Gagal memuat data.<br><small>${error.message}</small></td></tr>`;
        paginationContainer.style.display = "none";
      }
    } finally {
      isFetching = false;
    }
  }

  // === RENDER TABEL DENGAN PAGINATION ===
  function renderTable() {
    // Jangan render jika data belum berhasil di-fetch dari API
    if (!dataFetched) {
      return;
    }

    let headerHtml = `
      <th width="50" style="text-align:center;">No.</th>
      <th>Nomor Tiket</th>
      <th>Tanggal</th>
      <th>Requester</th>
      <th>Keluhan Utama</th>
      <th>Teknisi</th>
      <th>Selesai</th>
    `;
    tableHead.innerHTML = headerHtml;

    let html = "";

    if (!allData || allData.length === 0) {
      html = `<tr><td colspan="7" style="text-align:center; padding:40px; color:#999;">Tidak ada data tiket pada periode ini.</td></tr>`;
      paginationContainer.style.display = "none";
    } else {
      // Hitung range untuk halaman ini
      const startIndex = (currentPage - 1) * ITEMS_PER_PAGE;
      const endIndex = startIndex + ITEMS_PER_PAGE;
      const pageData = allData.slice(startIndex, endIndex);

      pageData.forEach((row, index) => {
        // Format data dari API tiket
        const requesterName = row.requester?.name || "Unknown";
        const deptName = row.requester?.department?.name || "-";
        const ticketNumber = row.ticket_number || "-";
        const subject = row.subject || "-";
        const createdAt = formatDate(row.created_at);
        const technicianName = row.assignment?.technician?.name || null;

        // Cek status tiket - lebih comprehensive
        const statusName = row.status?.name || row.ticket_status?.name || row.status || "OPEN";

        // Cek apakah tiket sudah di-close atau resolved
        const isClosed = String(statusName).toUpperCase() === "CLOSED";
        const isResolved = String(statusName).toUpperCase() === "RESOLVED";
        let closedDate = null;
        let resolvedDate = null;

        if (isClosed) {
          closedDate = formatDate(row.closed_at || row.updated_at);
        }
        if (isResolved) {
          resolvedDate = formatDate(row.resolved_at || row.updated_at);
        }

        let techHtml = `<span class="no-tech"><i class="fa-regular fa-clock"></i> Menunggu...</span>`;
        if (technicianName) {
          techHtml = `<div class="tech-badge"><i class="fa-solid fa-screwdriver-wrench"></i> ${technicianName}</div>`;
        }

        let dateHtml = `<span class="date-pending">-</span>`;
        if (closedDate) {
          // Jika tiket sudah CLOSED, tampilkan tanggal close dengan icon check (inline)
          dateHtml = `<span class="date-done"><i class="fa-solid fa-check-circle"></i> <strong>${closedDate}</strong></span>`;
        } else if (resolvedDate) {
          // Jika tiket RESOLVED, tampilkan status badge dan tanggal inline di tengah
          dateHtml = `<div class="status-resolved-container">
            <span class="status-resolved-badge"><i class="fa-solid fa-circle"></i> RESOLVED</span>
            <span class="resolved-date">${resolvedDate}</span>
          </div>`;
        } else {
          // Jika belum selesai, tampilkan status tiket
          const statusClass = String(statusName).toLowerCase().replace(/\s+/g, '-');
          dateHtml = `<span class="status-badge status-${statusClass}"><i class="fa-solid fa-circle"></i> ${statusName}</span>`;
        }

        html += `
          <tr>
            <td style="text-align:center; color:#64748b;">${startIndex + index + 1}</td>
            <td><span class="ticket-number">${ticketNumber}</span></td>
            <td style="color:#475569; font-size:13px;">${createdAt}</td>
            <td>
              <div class="user-info">
                <span data-requester="name">${requesterName}</span>
                <span data-requester="dept">${deptName}</span>
              </div>
            </td>
            <td><strong class="keluhan-utama">${subject}</strong></td>
            <td>${techHtml}</td>
            <td>${dateHtml}</td>
          </tr>
        `;
      });

      // Update pagination info
      const totalItems = allData.length;
      const totalPages = Math.ceil(totalItems / ITEMS_PER_PAGE);
      const infoStart = startIndex + 1;
      const infoEnd = Math.min(endIndex, totalItems);

      paginationStart.innerText = infoStart;
      paginationEnd.innerText = infoEnd;
      paginationTotal.innerText = totalItems;

      paginationContainer.style.display =
        allData.length > ITEMS_PER_PAGE ? "flex" : "none";
    }

    tableBody.innerHTML = html;
  }

  // === FORMAT DATE HELPER ===
  function formatDate(dateString) {
    if (!dateString) return "-";
    try {
      const date = new Date(dateString);
      const day = String(date.getDate()).padStart(2, "0");
      const month = String(date.getMonth() + 1).padStart(2, "0");
      const year = date.getFullYear();
      return `${day}-${month}-${year}`;
    } catch {
      return dateString;
    }
  }

  // === RENDER PAGINATION BUTTONS ===
  function renderPagination() {
    if (!allData || allData.length === 0) {
      paginationButtons.innerHTML = "";
      return;
    }

    const totalPages = Math.ceil(allData.length / ITEMS_PER_PAGE);
    let buttonsHtml = "";

    // Previous button
    buttonsHtml += `
      <button class="pagination-btn" onclick="goToPage(${currentPage - 1})"
        ${currentPage === 1 ? "disabled" : ""}>
        <i class="fa-solid fa-chevron-left"></i>
      </button>
    `;

    // Page buttons (max 5 buttons visible)
    const maxButtons = 5;
    let startPage = Math.max(1, currentPage - Math.floor(maxButtons / 2));
    let endPage = Math.min(totalPages, startPage + maxButtons - 1);

    if (endPage - startPage + 1 < maxButtons) {
      startPage = Math.max(1, endPage - maxButtons + 1);
    }

    if (startPage > 1) {
      buttonsHtml += `<button class="pagination-btn" onclick="goToPage(1)">1</button>`;
      if (startPage > 2) {
        buttonsHtml += `<span class="pagination-btn" style="cursor:default; border:none; padding:0;">...</span>`;
      }
    }

    for (let i = startPage; i <= endPage; i++) {
      buttonsHtml += `
        <button class="pagination-btn ${i === currentPage ? "active" : ""}" onclick="goToPage(${i})">
          ${i}
        </button>
      `;
    }

    if (endPage < totalPages) {
      if (endPage < totalPages - 1) {
        buttonsHtml += `<span class="pagination-btn" style="cursor:default; border:none; padding:0;">...</span>`;
      }
      buttonsHtml += `<button class="pagination-btn" onclick="goToPage(${totalPages})">${totalPages}</button>`;
    }

    // Next button
    buttonsHtml += `
      <button class="pagination-btn" onclick="goToPage(${currentPage + 1})"
        ${currentPage === totalPages ? "disabled" : ""}>
        <i class="fa-solid fa-chevron-right"></i>
      </button>
    `;

    paginationButtons.innerHTML = buttonsHtml;
  }

  // === GO TO PAGE ===
  window.goToPage = function (page) {
    const totalPages = Math.ceil(allData.length / ITEMS_PER_PAGE);
    if (page >= 1 && page <= totalPages) {
      currentPage = page;
      renderTable();
      renderPagination();
      // Scroll to top of table
      document
        .querySelector(".table-card")
        .scrollIntoView({ behavior: "smooth", block: "nearest" });
    }
  };

  // === DOWNLOAD EXCEL ===
  window.downloadExcel = function (evt) {
    const btn = evt.currentTarget;
    const originalHTML = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = `<i class="fa-solid fa-spinner fa-spin"></i> Memproses...`;

    try {
      const dates = getDateRange();
      console.log("Download Excel - Date Range:", dates);

      const token =
        sessionStorage.getItem("auth_token") ||
        localStorage.getItem("auth_token");

      if (!token) {
        console.error("Token tidak ditemukan!");
        alert("Session berakhir, silakan login kembali");
        btn.disabled = false;
        btn.innerHTML = originalHTML;
        return;
      }

      const params = new URLSearchParams({
        type: "all-tickets",
        start_date: dates.start_date,
        end_date: dates.end_date,
      });

      const url = `${EXPORT_URL}?${params.toString()}`;
      console.log("Download Excel - URL:", url);

      // Fetch dengan Authorization header
      fetch(url, {
        method: "GET",
        headers: {
          "Authorization": `Bearer ${token}`,
          "Accept": "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
        },
      })
        .then((response) => {
          if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
          }
          return response.blob();
        })
        .then((blob) => {
          // Create blob URL dan download
          const blobUrl = window.URL.createObjectURL(blob);
          const link = document.createElement("a");
          link.href = blobUrl;
          link.download = `laporan-tiket-${dates.start_date}-${dates.end_date}.xlsx`;
          document.body.appendChild(link);
          link.click();
          document.body.removeChild(link);
          window.URL.revokeObjectURL(blobUrl);

          console.log("Download Excel - Berhasil!");
          btn.disabled = false;
          btn.innerHTML = originalHTML;
        })
        .catch((error) => {
          console.error("Download Excel Error:", error);
          alert("Error: " + error.message);
          btn.disabled = false;
          btn.innerHTML = originalHTML;
        });
    } catch (error) {
      console.error("Download Excel Error:", error);
      alert("Error: " + error.message);
      btn.disabled = false;
      btn.innerHTML = originalHTML;
    }
  };

  function getMonthName(idx) {
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
    return months[idx - 1] || "";
  }

  document.addEventListener("DOMContentLoaded", init);
})();
