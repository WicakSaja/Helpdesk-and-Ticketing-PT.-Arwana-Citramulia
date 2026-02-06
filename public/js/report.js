(function () {
  // === CONFIGURATION ===
  // GUNAKAN ENDPOINT REPORT YANG BARU KITA BUAT
  const REPORTS_URL = "/api/reports/raw-data";
  const EXPORT_URL = "/api/export";

  // State Variables
  let currentTab = "weekly";
  let selectedYear = 2026;
  let selectedMonth = 1; // Januari

  // Elements
  const btnWeekly = document.getElementById("tabWeekly");
  const btnMonthly = document.getElementById("tabMonthly");
  const btnYearly = document.getElementById("tabYearly");

  const filterYearGroup = document.getElementById("filterYearGroup");
  const filterMonthGroup = document.getElementById("filterMonthGroup");
  const selectYear = document.getElementById("selectYear");
  const selectMonth = document.getElementById("selectMonth");

  const tableHead = document.getElementById("tableHead");
  const tableBody = document.getElementById("tableBody");
  const labelPeriode = document.getElementById("labelPeriode");

  // === INITIALIZATION ===
  function init() {
    populateYears();
    setupEventListeners();

    selectYear.value = selectedYear;
    selectMonth.value = selectedMonth;

    switchTab("weekly");
  }

  function populateYears() {
    const currentYear = new Date().getFullYear() + 1;
    selectYear.innerHTML = "";
    for (let i = currentYear; i >= currentYear - 5; i--) {
      let option = document.createElement("option");
      option.value = i;
      option.innerText = i;
      selectYear.appendChild(option);
    }
  }

  function setupEventListeners() {
    btnWeekly.addEventListener("click", () => switchTab("weekly"));
    btnMonthly.addEventListener("click", () => switchTab("monthly"));
    btnYearly.addEventListener("click", () => switchTab("yearly"));

    selectYear.addEventListener("change", () => {
      selectedYear = parseInt(selectYear.value);
      fetchData();
    });
    selectMonth.addEventListener("change", () => {
      selectedMonth = parseInt(selectMonth.value);
      fetchData();
    });
  }

  // === LOGIC TAB & FILTER ===
  window.switchTab = function (type) {
    currentTab = type;

    [btnWeekly, btnMonthly, btnYearly].forEach((btn) =>
      btn.classList.remove("active"),
    );
    if (type === "weekly") btnWeekly.classList.add("active");
    if (type === "monthly") btnMonthly.classList.add("active");
    if (type === "yearly") btnYearly.classList.add("active");

    if (type === "weekly") {
      filterYearGroup.style.display = "block";
      filterMonthGroup.style.display = "block";
      labelPeriode.innerText = `Data Tiket (${getMonthName(selectedMonth)} ${selectedYear})`;
    } else if (type === "monthly") {
      filterYearGroup.style.display = "block";
      filterMonthGroup.style.display = "none";
      labelPeriode.innerText = `Data Tiket Tahun ${selectedYear}`;
    } else {
      filterYearGroup.style.display = "block";
      filterMonthGroup.style.display = "none";
      labelPeriode.innerText = `Arsip Tiket Tahun ${selectedYear}`;
    }

    fetchData();
  };

  // === LOGIC HITUNG TANGGAL (FIXED) ===
  function getDateRange() {
    let startDate, endDate;

    if (currentTab === "weekly") {
      // Logic: Ambil 1 Bulan Penuh sesuai dropdown
      // Format: YYYY-MM-DD
      const lastDay = new Date(selectedYear, selectedMonth, 0).getDate(); // Cari tanggal terakhir bulan itu (28/29/30/31)
      startDate = `${selectedYear}-${String(selectedMonth).padStart(2, "0")}-01`;
      endDate = `${selectedYear}-${String(selectedMonth).padStart(2, "0")}-${lastDay}`;
    } else {
      // Logic: Ambil 1 Tahun Penuh sesuai dropdown tahun
      startDate = `${selectedYear}-01-01`;
      endDate = `${selectedYear}-12-31`;
    }

    return { start_date: startDate, end_date: endDate };
  }

  // === FETCH DATA ===
  async function fetchData() {
    tableBody.innerHTML = `<tr><td colspan="7" class="loading-container"><i class="fa-solid fa-circle-notch fa-spin" style="font-size:24px; color:#d62828; margin-bottom:10px;"></i><br>Mengambil data...</td></tr>`;

    const token =
      sessionStorage.getItem("auth_token") ||
      localStorage.getItem("auth_token");
    const dates = getDateRange();

    // Debugging: Cek di console apakah tanggal benar
    console.log("Fetching Data Range:", dates);

    const params = new URLSearchParams({
      start_date: dates.start_date,
      end_date: dates.end_date,
      // Kita gunakan ReportController, jadi parameter type bisa dikirim untuk validasi tambahan
      type: currentTab,
    });

    try {
      // Panggil API Report yang baru
      const response = await fetch(`${REPORTS_URL}?${params.toString()}`, {
        method: "GET",
        headers: {
          Authorization: `Bearer ${token}`,
          Accept: "application/json",
        },
      });

      if (!response.ok) throw new Error("Gagal mengambil data dari server");

      const result = await response.json();
      const tickets = result.data || [];

      renderTable(tickets);
    } catch (error) {
      console.error("Error:", error);
      tableBody.innerHTML = `<tr><td colspan="7" style="text-align:center; padding:40px; color:#d62828;">Gagal memuat data.<br><small>${error.message}</small></td></tr>`;
    }
  }

  // === RENDER TABEL ===
  function renderTable(data) {
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

    if (!data || data.length === 0) {
      html = `<tr><td colspan="7" style="text-align:center; padding:40px; color:#999;">Tidak ada data tiket pada periode ini.</td></tr>`;
    } else {
      data.forEach((row, index) => {
        // Formatting
        const requesterName = row.requester || "Unknown";
        const deptName = row.dept || "-";

        let techHtml = `<span class="no-tech"><i class="fa-regular fa-clock"></i> Menunggu...</span>`;
        if (row.technician) {
          techHtml = `<div class="tech-badge"><i class="fa-solid fa-screwdriver-wrench"></i> ${row.technician}</div>`;
        }

        let dateHtml = `<span class="date-pending">-</span>`;
        if (row.resolved_at) {
          dateHtml = `<span class="date-done"><i class="fa-solid fa-check-circle"></i> ${row.resolved_at}</span>`;
        }

        html += `
                    <tr>
                        <td style="text-align:center; color:#64748b;">${index + 1}</td>
                        <td><span class="ticket-number">${row.ticket_number}</span></td>
                        <td style="color:#475569; font-size:13px;">${row.created_at}</td>
                        <td>
                            <div class="user-info">
                                <span class="user-name">${requesterName}</span>
                                <span class="user-dept">${deptName}</span>
                            </div>
                        </td>
                        <td><strong style="color:#334155;">${row.subject}</strong></td>
                        <td>${techHtml}</td>
                        <td>${dateHtml}</td>
                    </tr>
                `;
      });
    }

    tableBody.innerHTML = html;
  }

  // === DOWNLOAD EXCEL ===
  window.downloadExcel = function (evt) {
    const btn = evt.currentTarget;
    const originalHTML = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = `<i class="fa-solid fa-spinner fa-spin"></i> Memproses...`;

    const dates = getDateRange();
    const params = new URLSearchParams({
      type: "all-tickets", // Atau sesuaikan dengan logic backend export
      start_date: dates.start_date,
      end_date: dates.end_date,
    });

    const token =
      sessionStorage.getItem("auth_token") ||
      localStorage.getItem("auth_token");
    const url = `${EXPORT_URL}?${params.toString()}&token=${token}`;

    window.open(url, "_blank");

    setTimeout(() => {
      btn.disabled = false;
      btn.innerHTML = originalHTML;
    }, 2000);
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
