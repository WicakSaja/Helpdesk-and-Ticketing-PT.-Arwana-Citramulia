(function() {
    // === CONFIG ===
    const DEPT_API_URL = '/api/departments';
    const DEPT_PER_PAGE = 5;

    // === AUTHENTICATION (Ambil Token) ===
    const authToken = sessionStorage.getItem('auth_token') || localStorage.getItem('auth_token');

    // State Variables
    let deptData = [];
    let deptPage = 1;
    let deptEditMode = false;
    let deptCurrentId = null;

    // --- 1. LOAD DATA ---
    async function loadDepartments() {
        const tbody = document.getElementById('table-body');
        if (!tbody) return;

        tbody.innerHTML = `<tr><td colspan="3" class="loading-row"><i class="fa-solid fa-spinner fa-spin" style="font-size: 24px;"></i><p style="margin-top: 10px;">Sedang memuat data...</p></td></tr>`;

        try {
            const headers = { 
                'Accept': 'application/json', 
                'Content-Type': 'application/json'
            };
            // Inject Token
            if (authToken) headers['Authorization'] = `Bearer ${authToken}`;

            const response = await fetch(DEPT_API_URL, { method: 'GET', headers: headers });

            if (!response.ok) {
                if (response.status === 401) {
                    console.warn('Token Expired/Invalid'); 
                    // window.location.href = '/login'; // Uncomment jika ingin auto redirect
                }
                throw new Error(`Server Error: ${response.status}`);
            }

            const result = await response.json();
            // Handle wrapper { data: [...] }
            deptData = result.data ? result.data : (Array.isArray(result) ? result : []);
            
            // Sort
            deptData.sort((a, b) => b.id - a.id);

            deptPage = 1;
            renderTable();

        } catch (error) {
            console.error('Dept Fetch Error:', error);
            tbody.innerHTML = `<tr><td colspan="3" class="loading-row" style="color:#d62828"><i class="fa-solid fa-circle-exclamation"></i><p style="margin-top: 10px;">${error.message}</p></td></tr>`;
        }
    }

    // --- 2. RENDER TABLE ---
    function renderTable() {
        const tbody = document.getElementById('table-body');
        if (!tbody) return;

        const totalItems = deptData.length;
        const totalPages = Math.ceil(totalItems / DEPT_PER_PAGE);

        if (deptPage < 1) deptPage = 1;
        if (deptPage > totalPages && totalPages > 0) deptPage = totalPages;

        const startIndex = (deptPage - 1) * DEPT_PER_PAGE;
        const endIndex = Math.min(startIndex + DEPT_PER_PAGE, totalItems);
        const pageData = deptData.slice(startIndex, endIndex);

        let html = '';
        if (totalItems === 0) {
            html = `<tr><td colspan="3" class="loading-row"><i class="fa-solid fa-folder-open" style="font-size: 24px;"></i><p style="margin-top: 10px;">Belum ada data departemen.</p></td></tr>`;
        } else {
            pageData.forEach((dept, index) => {
                const rowNumber = startIndex + index + 1;
                html += `
                    <tr>
                        <td>${rowNumber}</td>
                        <td><strong>${dept.name}</strong></td>
                        <td style="text-align: right;">
                            <button type="button" class="btn-icon btn-edit" onclick="openEdit(${dept.id}, '${dept.name}')" title="Edit">
                                <i class="fa-solid fa-pen"></i>
                            </button>
                            <button type="button" class="btn-icon btn-del" onclick="handleDelete(${dept.id}, '${dept.name}')" title="Hapus">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                `;
            });
        }
        tbody.innerHTML = html;

        // Pagination UI
        const infoEl = document.getElementById('paginationInfo');
        if(infoEl) infoEl.innerText = totalItems === 0 ? 'Menampilkan 0 data' : `Menampilkan ${startIndex + 1} - ${endIndex} dari ${totalItems} departemen`;
        
        const btnNum = document.getElementById('btnPageNum');
        if(btnNum) btnNum.innerText = deptPage;

        const btnPrev = document.getElementById('btnPrev');
        const btnNext = document.getElementById('btnNext');
        
        if(btnPrev) {
            btnPrev.disabled = deptPage === 1;
            // Gunakan addEventListener agar tidak tertimpa
            btnPrev.onclick = () => { if(deptPage > 1) { deptPage--; renderTable(); } };
        }
        if(btnNext) {
            btnNext.disabled = deptPage === totalPages || totalPages === 0;
            btnNext.onclick = () => { if(deptPage < totalPages) { deptPage++; renderTable(); } };
        }
    }

    // --- 3. EXPOSE FUNCTIONS TO WINDOW (Agar bisa dipanggil onclick HTML) ---
    
    window.openModal = function() {
        deptEditMode = false;
        deptCurrentId = null;
        document.getElementById('modalTitle').innerText = "Tambah Departemen";
        document.getElementById('deptName').value = "";
        toggleError(false);
        document.getElementById('deptModal').style.display = 'flex';
    };

    window.openEdit = function(id, name) {
        deptEditMode = true;
        deptCurrentId = id;
        document.getElementById('modalTitle').innerText = "Edit Departemen";
        document.getElementById('deptName').value = name;
        toggleError(false);
        document.getElementById('deptModal').style.display = 'flex';
    };

    window.closeModal = function() {
        document.getElementById('deptModal').style.display = 'none';
    };

    window.handleSave = async function(event) {
        event.preventDefault();
        
        const nameInput = document.getElementById('deptName');
        const name = nameInput.value;
        const saveBtn = document.getElementById('btnSave');
        
        saveBtn.innerText = 'Menyimpan...'; 
        saveBtn.disabled = true; 
        toggleError(false);

        const url = deptEditMode ? `${DEPT_API_URL}/${deptCurrentId}` : DEPT_API_URL;
        const method = deptEditMode ? 'PUT' : 'POST';

        try {
            const headers = { 
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            };
            if(authToken) headers['Authorization'] = `Bearer ${authToken}`;

            const response = await fetch(url, {
                method: method,
                headers: headers,
                body: JSON.stringify({ name: name })
            });

            const result = await response.json();

            if (!response.ok) {
                if (result.errors && result.errors.name) {
                    throw new Error(result.errors.name[0]);
                }
                throw new Error(result.message || 'Gagal menyimpan data');
            }

            window.closeModal();
            Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: deptEditMode ? 'Departemen diperbarui!' : 'Departemen ditambahkan!',
                timer: 1500,
                showConfirmButton: false
            });
            
            loadDepartments(); 

        } catch (error) {
            toggleError(true, error.message);
        } finally {
            saveBtn.innerText = 'Simpan';
            saveBtn.disabled = false;
        }
    };

    window.handleDelete = function(id, name) {
        Swal.fire({
            title: `Hapus ${name}?`,
            text: "Data yang dihapus tidak bisa dikembalikan!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d62828',
            confirmButtonText: 'Ya, Hapus',
            cancelButtonText: 'Batal'
        }).then(async (result) => {
            if (result.isConfirmed) {
                try {
                    const headers = { 
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    };
                    if(authToken) headers['Authorization'] = `Bearer ${authToken}`;

                    const response = await fetch(`${DEPT_API_URL}/${id}`, {
                        method: 'DELETE',
                        headers: headers
                    });

                    const json = await response.json();

                    if (!response.ok) throw new Error(json.message || 'Gagal menghapus data');
                    
                    Swal.fire('Terhapus!', 'Data berhasil dihapus.', 'success');
                    loadDepartments();

                } catch (error) {
                    Swal.fire('Gagal', error.message, 'error');
                }
            }
        });
    };

    function toggleError(show, msg = '') {
        const el = document.getElementById('errorMessage');
        if(el) {
            el.style.display = show ? 'block' : 'none';
            el.innerText = msg;
        }
    }

    // Klik luar modal untuk tutup
    window.onclick = function(event) {
        if (event.target.classList.contains('modal-overlay')) {
            window.closeModal();
        }
    }

    // Init Load
    document.addEventListener('DOMContentLoaded', loadDepartments);

})();