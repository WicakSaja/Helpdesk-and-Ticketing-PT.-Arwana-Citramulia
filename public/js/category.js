(function() {
    // === CONFIG ===
    const CAT_API_URL = '/api/categories';
    const CAT_PER_PAGE = 5;

    // === AUTHENTICATION ===
    const authToken = sessionStorage.getItem('auth_token') || localStorage.getItem('auth_token');

    // State Variables
    let catData = [];
    let catPage = 1;
    let catEditMode = false;
    let catCurrentId = null;

    // --- 1. LOAD DATA ---
    async function loadCategories() {
        const tbody = document.getElementById('table-body');
        if (!tbody) return;

        tbody.innerHTML = `<tr><td colspan="4" class="loading-row"><i class="fa-solid fa-spinner fa-spin" style="font-size: 24px;"></i><p style="margin-top: 10px;">Sedang memuat data...</p></td></tr>`;

        try {
            const headers = { 
                'Accept': 'application/json', 
                'Content-Type': 'application/json'
            };
            if (authToken) headers['Authorization'] = `Bearer ${authToken}`;

            // Fetch ke /api/categories
            const response = await fetch(CAT_API_URL, { method: 'GET', headers: headers });

            if (!response.ok) {
                if (response.status === 401) {
                    console.warn('Token Expired/Invalid'); 
                    window.location.href = '/login';
                }
                throw new Error(`Server Error: ${response.status}`);
            }

            const result = await response.json();
            // Handle wrapper { data: [...] } sesuai struktur response Laravel/Postman
            catData = result.data ? result.data : (Array.isArray(result) ? result : []);
            
            // Sort by ID Descending
            catData.sort((a, b) => b.id - a.id);

            catPage = 1;
            renderTable();

        } catch (error) {
            console.error('Category Fetch Error:', error);
            tbody.innerHTML = `<tr><td colspan="4" class="loading-row" style="color:#d62828"><i class="fa-solid fa-circle-exclamation"></i><p style="margin-top: 10px;">${error.message}</p></td></tr>`;
        }
    }

    // --- 2. RENDER TABLE ---
    function renderTable() {
        const tbody = document.getElementById('table-body');
        if (!tbody) return;

        const totalItems = catData.length;
        const totalPages = Math.ceil(totalItems / CAT_PER_PAGE);

        if (catPage < 1) catPage = 1;
        if (catPage > totalPages && totalPages > 0) catPage = totalPages;

        const startIndex = (catPage - 1) * CAT_PER_PAGE;
        const endIndex = Math.min(startIndex + CAT_PER_PAGE, totalItems);
        const pageData = catData.slice(startIndex, endIndex);

        let html = '';
        if (totalItems === 0) {
            html = `<tr><td colspan="4" class="loading-row"><i class="fa-solid fa-folder-open" style="font-size: 24px;"></i><p style="margin-top: 10px;">Belum ada data kategori.</p></td></tr>`;
        } else {
            pageData.forEach((cat, index) => {
                const rowNumber = startIndex + index + 1;
                // Escape string untuk mencegah XSS & error kutip pada onclick
                const safeName = cat.name.replace(/'/g, "\\'");
                const safeDesc = (cat.description || '').replace(/'/g, "\\'");
                
                // Truncate deskripsi jika terlalu panjang
                const displayDesc = (cat.description && cat.description.length > 50) 
                    ? cat.description.substring(0, 50) + '...' 
                    : (cat.description || '-');

                html += `
                    <tr>
                        <td><span style="font-weight:600; color:#555;">${rowNumber}</span></td>
                        <td><strong style="font-size:15px; color:#1565c0;">${cat.name}</strong></td>
                        <td style="color:#666; line-height:1.5;">${displayDesc}</td>
                        <td style="text-align: right;">
                            <div style="display:flex; justify-content:end; gap:5px;">
                                <button type="button" class="btn-icon btn-edit" onclick="openEdit(${cat.id}, '${safeName}', '${safeDesc}')" title="Edit">
                                    <i class="fa-solid fa-pen"></i>
                                </button>
                                <button type="button" class="btn-icon btn-del" onclick="handleDelete(${cat.id}, '${safeName}')" title="Hapus">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                `;
            });
        }
        tbody.innerHTML = html;

        // Pagination UI
        const infoEl = document.getElementById('paginationInfo');
        if(infoEl) infoEl.innerText = totalItems === 0 ? 'Menampilkan 0 data' : `Menampilkan ${startIndex + 1} - ${endIndex} dari ${totalItems} kategori`;
        
        const btnNum = document.getElementById('btnPageNum');
        if(btnNum) btnNum.innerText = catPage;

        const btnPrev = document.getElementById('btnPrev');
        const btnNext = document.getElementById('btnNext');
        
        if(btnPrev) {
            btnPrev.disabled = catPage === 1;
            btnPrev.onclick = () => { if(catPage > 1) { catPage--; renderTable(); } };
        }
        if(btnNext) {
            btnNext.disabled = catPage === totalPages || totalPages === 0;
            btnNext.onclick = () => { if(catPage < totalPages) { catPage++; renderTable(); } };
        }
    }

    // --- 3. GLOBAL FUNCTIONS (Window Scope) ---
    
    window.openModal = function() {
        catEditMode = false;
        catCurrentId = null;
        document.getElementById('modalTitle').innerText = "Tambah Kategori";
        document.getElementById('catName').value = "";
        document.getElementById('catDesc').value = "";
        toggleError(false);
        document.getElementById('catModal').style.display = 'flex';
    };

    window.openEdit = function(id, name, desc) {
        catEditMode = true;
        catCurrentId = id;
        document.getElementById('modalTitle').innerText = "Edit Kategori";
        document.getElementById('catName').value = name;
        document.getElementById('catDesc').value = desc === 'undefined' ? '' : desc;
        toggleError(false);
        document.getElementById('catModal').style.display = 'flex';
    };

    window.closeModal = function() {
        document.getElementById('catModal').style.display = 'none';
    };

    window.handleSave = async function(event) {
        event.preventDefault();
        
        const nameInput = document.getElementById('catName');
        const descInput = document.getElementById('catDesc');
        const saveBtn = document.getElementById('btnSave');
        
        const payload = {
            name: nameInput.value,
            description: descInput.value
        };
        
        saveBtn.innerText = 'Menyimpan...'; 
        saveBtn.disabled = true; 
        toggleError(false);

        const url = catEditMode ? `${CAT_API_URL}/${catCurrentId}` : CAT_API_URL;
        const method = catEditMode ? 'PUT' : 'POST';

        try {
            const headers = { 
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            };
            if(authToken) headers['Authorization'] = `Bearer ${authToken}`;

            const response = await fetch(url, {
                method: method,
                headers: headers,
                body: JSON.stringify(payload)
            });

            const result = await response.json();

            if (!response.ok) {
                // Handle Validation Error
                if (result.errors) {
                    if(result.errors.name) throw new Error(result.errors.name[0]);
                    if(result.errors.description) throw new Error(result.errors.description[0]);
                }
                throw new Error(result.message || 'Gagal menyimpan data');
            }

            window.closeModal();
            Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: catEditMode ? 'Kategori diperbarui!' : 'Kategori ditambahkan!',
                timer: 1500,
                showConfirmButton: false
            });
            
            loadCategories(); 

        } catch (error) {
            toggleError(true, error.message);
        } finally {
            saveBtn.innerText = 'Simpan';
            saveBtn.disabled = false;
        }
    };

    window.handleDelete = function(id, name) {
        Swal.fire({
            title: `Hapus Kategori ${name}?`,
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

                    const response = await fetch(`${CAT_API_URL}/${id}`, {
                        method: 'DELETE',
                        headers: headers
                    });

                    const json = await response.json();

                    if (!response.ok) throw new Error(json.message || 'Gagal menghapus data');
                    
                    Swal.fire('Terhapus!', 'Data berhasil dihapus.', 'success');
                    loadCategories();

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
    document.addEventListener('DOMContentLoaded', loadCategories);

})();