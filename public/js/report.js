(function() {
    // API Setup
    const API_EXPORT_URL = '/api/reports/export'; 

    let currentTab = 'weekly'; 
    let selectedYear = new Date().getFullYear();
    let selectedMonth = new Date().getMonth() + 1;

    // Elements
    const btnWeekly = document.getElementById('tabWeekly');
    const btnMonthly = document.getElementById('tabMonthly');
    const btnYearly = document.getElementById('tabYearly');
    
    const filterYearGroup = document.getElementById('filterYearGroup');
    const filterMonthGroup = document.getElementById('filterMonthGroup');
    const selectYear = document.getElementById('selectYear');
    const selectMonth = document.getElementById('selectMonth');
    
    const tableHead = document.getElementById('tableHead');
    const tableBody = document.getElementById('tableBody');
    const labelPeriode = document.getElementById('labelPeriode');

    function init() {
        populateYears();
        setupEventListeners();
        switchTab('weekly'); 
    }

    function populateYears() {
        const currentYear = new Date().getFullYear();
        selectYear.innerHTML = '';
        for (let i = currentYear; i >= currentYear - 4; i--) {
            let option = document.createElement('option');
            option.value = i;
            option.innerText = i;
            selectYear.appendChild(option);
        }
        selectYear.value = currentYear;
        selectMonth.value = new Date().getMonth() + 1;
    }

    function setupEventListeners() {
        btnWeekly.addEventListener('click', () => switchTab('weekly'));
        btnMonthly.addEventListener('click', () => switchTab('monthly'));
        btnYearly.addEventListener('click', () => switchTab('yearly'));

        selectYear.addEventListener('change', () => { selectedYear = selectYear.value; fetchData(); });
        selectMonth.addEventListener('change', () => { selectedMonth = selectMonth.value; fetchData(); });
    }

    window.switchTab = function(type) {
        currentTab = type;
        
        [btnWeekly, btnMonthly, btnYearly].forEach(btn => btn.classList.remove('active'));
        if(type === 'weekly') btnWeekly.classList.add('active');
        if(type === 'monthly') btnMonthly.classList.add('active');
        if(type === 'yearly') btnYearly.classList.add('active');

        if (type === 'weekly') {
            filterYearGroup.style.display = 'block';
            filterMonthGroup.style.display = 'block';
            labelPeriode.innerText = `Data Mingguan (${getMonthName(selectedMonth)} ${selectedYear})`;
        } else if (type === 'monthly') {
            filterYearGroup.style.display = 'block';
            filterMonthGroup.style.display = 'none'; 
            labelPeriode.innerText = `Data Bulanan (${selectedYear})`;
        } else {
            filterYearGroup.style.display = 'none'; 
            filterMonthGroup.style.display = 'none';
            labelPeriode.innerText = `Arsip Tahunan`;
        }

        fetchData();
    }

    async function fetchData() {
        // Kolom berkurang jadi 7
        tableBody.innerHTML = `<tr><td colspan="7" class="loading-container"><i class="fa-solid fa-circle-notch fa-spin" style="font-size:24px; color:#d62828; margin-bottom:10px;"></i><br>Sedang memuat data laporan...</td></tr>`;
        
        // Simulasi delay API
        setTimeout(() => {
            const data = generateMockData(currentTab); 
            renderTable(data);
        }, 500);
    }

    function renderTable(data) {
        // 1. SETUP HEADER (Sesuai Permintaan)
        let headerHtml = `
            <th width="50">No.</th>
            <th>Nomor Tiket</th>
            <th>Tanggal Dibuat</th>
            <th>Requester</th>
            <th>Keluhan</th>
            <th>Teknisi</th>
            <th>Tanggal Selesai</th>
        `;
        tableHead.innerHTML = headerHtml;

        // 2. RENDER BODY
        let html = '';

        if (data.length === 0) {
            html = `<tr><td colspan="7" style="text-align:center; padding:40px; color:#999;">Tidak ada data tiket pada periode ini.</td></tr>`;
        } else {
            data.forEach((row, index) => {
                // Inisial untuk Avatar
                const initial = row.requester.charAt(0).toUpperCase();
                
                // HTML Teknisi
                let techHtml = `<span class="no-tech">Belum ditugaskan</span>`;
                if(row.technician) {
                    techHtml = `<div class="tech-badge"><i class="fa-solid fa-screwdriver-wrench"></i> ${row.technician}</div>`;
                }

                // HTML Tanggal Selesai
                let dateHtml = `<span style="color:#94a3b8;">-</span>`;
                if(row.resolved_at) {
                    dateHtml = `<span style="color:#15803d; font-weight:600;"><i class="fa-solid fa-check"></i> ${row.resolved_at}</span>`;
                }

                html += `
                    <tr>
                        <td style="text-align:center; color:#64748b;">${index + 1}</td>
                        <td><span class="ticket-number">${row.ticket_number}</span></td>
                        <td style="color:#475569;">${row.created_at}</td>
                        <td>
                            <div class="user-profile">
                                <div class="avatar-circle">${initial}</div>
                                <div class="user-info">
                                    <span class="user-name">${row.requester}</span>
                                    <span class="user-role">${row.dept}</span>
                                </div>
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

    window.downloadExcel = function() {
        alert("Fitur download akan mengunduh data: " + currentTab);
    }

    function getMonthName(idx) {
        const months = ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
        return months[idx - 1] || '';
    }

    // --- DATA DUMMY (Tanpa Deskripsi Panjang) ---
    function generateMockData(type) {
        return [
            {
                ticket_number: 'TKT-2026-082',
                created_at: '30 Jan 2026, 10:34',
                requester: 'Yuni Quality',
                dept: 'Quality Control',
                subject: 'Tidak bisa akses shared folder',
                technician: 'Budi Repair',
                resolved_at: '31 Jan 2026'
            },
            {
                ticket_number: 'TKT-2026-054',
                created_at: '30 Jan 2026, 09:58',
                requester: 'Vina Accounting',
                dept: 'Finance',
                subject: 'Jaringan LAN putus-putus',
                technician: 'Candra IT',
                resolved_at: null 
            },
            {
                ticket_number: 'TKT-2026-081',
                created_at: '30 Jan 2026, 09:53',
                requester: 'Rina Finance',
                dept: 'Finance',
                subject: 'Keyboard rusak beberapa tombol',
                technician: 'Candra IT',
                resolved_at: '01 Feb 2026'
            },
            {
                ticket_number: 'TKT-2026-041',
                created_at: '29 Jan 2026, 16:19',
                requester: 'Omar Coord',
                dept: 'Produksi',
                subject: 'VPN tidak konek',
                technician: 'Candra IT',
                resolved_at: null 
            },
            {
                ticket_number: 'TKT-2026-079',
                created_at: '29 Jan 2026, 13:23',
                requester: 'Doni Staff',
                dept: 'HRGA',
                subject: 'Email tidak bisa kirim attachment',
                technician: null, // Contoh belum ada teknisi
                resolved_at: null
            }
        ];
    }

    document.addEventListener('DOMContentLoaded', init);
})();