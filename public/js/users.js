(function () {
  // Function to get current auth token
  function getAuthToken() {
    return (
      sessionStorage.getItem("auth_token") || localStorage.getItem("auth_token")
    );
  }

  // Helper function to check token and handle auth errors
  function ensureAuthenticated(operation = "Operation") {
    const token = getAuthToken();
    if (!token) {
      console.warn(
        `${operation}: No auth token found. Redirecting to login...`,
      );
      window.location.href = "/login";
      return null;
    }
    return token;
  }

  // Helper function to handle API errors
  function handleApiError(response, context = "API Call") {
    if (response.status === 401) {
      console.warn(
        `${context}: Token invalid or expired. Clearing auth and redirecting to login...`,
      );
      TokenManager.clearAuth();
      window.location.href = "/login";
      return true; // Indicates auth error
    }
    return false;
  }

  // Pagination state
  let currentPage = 1;
  let currentPerPage = 10;

  // Edit state
  let editingUserId = null;

  // Change per page handler (no longer used, kept for compatibility)
  window.changePerPage = function () {
    // Fixed at 10 per page, do nothing
  };

  // Fetch Departments dari API
  async function loadDepartments() {
    try {
      const response = await fetch(`${API_URL}/api/departments`, {
        method: "GET",
        headers: {
          "Content-Type": "application/json",
        },
      });

      if (!response.ok) {
        throw new Error(`HTTP error! status: ${response.status}`);
      }

      const result = await response.json();
      populateDepartmentSelect(result.data);
    } catch (error) {
      console.error("Error fetching departments:", error);
      const sel = document.getElementById("uDept");
      if (sel)
        sel.innerHTML =
          '<option value="" disabled selected>Error loading departments</option>';
    }
  }

  // Populate Department Select Dropdown
  function populateDepartmentSelect(departments) {
    const select = document.getElementById("uDept");
    if (!select) return;
    select.innerHTML =
      '<option value="" disabled selected>-- Pilih Departemen --</option>';

    departments.forEach((dept) => {
      const option = document.createElement("option");
      option.value = dept.id;
      option.textContent =
        dept.name.charAt(0).toUpperCase() + dept.name.slice(1); // Capitalize first letter
      select.appendChild(option);
    });
  }

  // Fetch Users dari API
  async function loadUsers(page = 1, perPage = null) {
    if (perPage === null) {
      perPage = currentPerPage;
    }
    currentPage = page;

    // Check if token exists
    const token = getAuthToken();
    if (!token) {
      console.warn("No auth token found. Redirecting to login...");
      window.location.href = "/login";
      return;
    }

    try {
      const response = await fetch(
        `${API_URL}/api/users?page=${page}&per_page=${perPage}`,
        {
          method: "GET",
          headers: {
            Authorization: `Bearer ${token}`,
            "Content-Type": "application/json",
          },
        },
      );

      if (!response.ok) {
        if (response.status === 401) {
          console.warn(
            "Token invalid or expired. Clearing auth and redirecting to login...",
          );
          TokenManager.clearAuth();
          window.location.href = "/login";
          return;
        }
        throw new Error(`HTTP error! status: ${response.status}`);
      }

      const result = await response.json();
      populateTable(result.data.data);
      updatePagination(result.data);
    } catch (error) {
      console.error("Error fetching users:", error);
      const tbody = document.getElementById("userTableBody");
      if (tbody)
        tbody.innerHTML = `
                <tr>
                    <td colspan="5" style="text-align: center; padding: 40px; color: #d62828;">
                        <i class="fa-solid fa-exclamation-circle" style="font-size: 24px;"></i>
                        <p style="margin-top: 10px;">Gagal memuat data pengguna</p>
                        <small style="color: #999;">Silakan refresh halaman atau login ulang</small>
                    </td>
                </tr>
            `;
    }
  }

  function updatePagination(paginationData) {
    const infoText = document.getElementById("paginationInfoText");
    const buttons = document.getElementById("paginationButtons");
    if (!infoText || !buttons) return;

    infoText.textContent = `Menampilkan ${paginationData.from || 0} - ${paginationData.to || 0} dari ${paginationData.total} users`;
    buttons.innerHTML = "";

    const prevBtn = document.createElement("button");
    prevBtn.className = "page-btn";
    prevBtn.innerHTML = '<i class="fa-solid fa-chevron-left"></i>';
    prevBtn.disabled = paginationData.current_page === 1;
    prevBtn.onclick = () => loadUsers(paginationData.current_page - 1);
    buttons.appendChild(prevBtn);

    const startPage = Math.max(1, paginationData.current_page - 2);
    const endPage = Math.min(
      paginationData.last_page,
      paginationData.current_page + 2,
    );

    if (startPage > 1) {
      const firstBtn = document.createElement("button");
      firstBtn.className = "page-btn";
      firstBtn.textContent = "1";
      firstBtn.onclick = () => loadUsers(1);
      buttons.appendChild(firstBtn);

      if (startPage > 2) {
        const dots = document.createElement("span");
        dots.textContent = "...";
        dots.style.padding = "0 8px";
        dots.style.color = "#999";
        buttons.appendChild(dots);
      }
    }

    for (let i = startPage; i <= endPage; i++) {
      const pageBtn = document.createElement("button");
      pageBtn.className =
        "page-btn" + (i === paginationData.current_page ? " active" : "");
      pageBtn.textContent = i;
      pageBtn.onclick = () => loadUsers(i);
      buttons.appendChild(pageBtn);
    }

    if (endPage < paginationData.last_page) {
      if (endPage < paginationData.last_page - 1) {
        const dots = document.createElement("span");
        dots.textContent = "...";
        dots.style.padding = "0 8px";
        dots.style.color = "#999";
        buttons.appendChild(dots);
      }

      const lastBtn = document.createElement("button");
      lastBtn.className = "page-btn";
      lastBtn.textContent = paginationData.last_page;
      lastBtn.onclick = () => loadUsers(paginationData.last_page);
      buttons.appendChild(lastBtn);
    }

    const nextBtn = document.createElement("button");
    nextBtn.className = "page-btn";
    nextBtn.innerHTML = '<i class="fa-solid fa-chevron-right"></i>';
    nextBtn.disabled = paginationData.current_page === paginationData.last_page;
    nextBtn.onclick = () => loadUsers(paginationData.current_page + 1);
    buttons.appendChild(nextBtn);
  }

  function populateTable(users) {
    const tableBody = document.getElementById("userTableBody");
    if (!tableBody) return;
    tableBody.innerHTML = "";

    if (users.length === 0) {
      tableBody.innerHTML = `
                <tr>
                    <td colspan="5" style="text-align: center; padding: 40px; color: #999;">
                        <i class="fa-solid fa-inbox" style="font-size: 24px;"></i>
                        <p style="margin-top: 10px;">Tidak ada data pengguna</p>
                    </td>
                </tr>
            `;
      return;
    }

    users.forEach((user) => {
      const primaryRole = user.roles && user.roles[0] ? user.roles[0] : "user";
      const roleClass = getRoleClass(primaryRole);
      const roleName = formatRoleName(primaryRole);
      const departmentName = user.department ? user.department.name : "-";

      const row = document.createElement("tr");
      row.id = `user-${user.id}`;
      row.innerHTML = `
                <td>
                    <div style="font-weight: 600;">${user.name}</div>
                    <small style="color:#999;">${user.email}</small>
                </td>
                <td><span class="badge ${roleClass}">${roleName}</span></td>
                <td>${departmentName}</td>
                <td><span class="badge status-active" id="badge-${user.id}">Aktif</span></td>
                <td style="text-align: right;">
                    <button type="button" class="btn-icon btn-edit" 
                        onclick="editUser(${user.id}, '${user.name}', '${user.email}', '${user.phone}', '${primaryRole}', ${user.department_id || "null"})">
                        <i class="fa-solid fa-pen"></i>
                    </button>
                    <button type="button" class="btn-icon btn-toggle-off" id="btn-status-${user.id}"
                        onclick="toggleStatus(${user.id}, '${user.name}', 'active')">
                        <i class="fa-solid fa-power-off"></i>
                    </button>
                </td>
            `;
      tableBody.appendChild(row);
    });
  }

  function getRoleClass(role) {
    const roleMap = {
      "master-admin": "role-admin",
      admin: "role-admin",
      helpdesk: "role-admin",
      technician: "role-tech",
      requester: "role-user",
    };
    return roleMap[role] || "role-user";
  }

  function formatRoleName(role) {
    const nameMap = {
      "master-admin": "Master Admin",
      admin: "Admin",
      helpdesk: "Helpdesk",
      technician: "Technician",
      requester: "Requester",
    };
    return nameMap[role] || role;
  }

  // Exposed functions for markup
  window.openModal = function () {
    editingUserId = null; // Reset edit mode
    const modalTitle = document.getElementById("modalTitle");
    if (modalTitle) modalTitle.innerText = "Tambah User Baru";

    // Reset Form
    const uName = document.getElementById("uName");
    if (uName) uName.value = "";
    const uEmail = document.getElementById("uEmail");
    if (uEmail) uEmail.value = "";
    const uPhone = document.getElementById("uPhone");
    if (uPhone) uPhone.value = "";
    const uPassword = document.getElementById("uPassword");
    if (uPassword) {
      uPassword.value = "";
      uPassword.setAttribute("required", "required");
      uPassword.placeholder = "Wajib Diisi";
    }
    const passHint = document.getElementById("passHint");
    if (passHint) passHint.style.display = "none";

    const modal = document.getElementById("userModal");
    if (modal) modal.style.display = "flex";
  };

  window.editUser = function (userId, name, email, phone, role, deptId) {
    editingUserId = userId; // Store user ID for edit mode
    const modalTitle = document.getElementById("modalTitle");
    if (modalTitle) modalTitle.innerText = "Edit User";

    const uName = document.getElementById("uName");
    if (uName) uName.value = name;
    const uEmail = document.getElementById("uEmail");
    if (uEmail) uEmail.value = email;
    const uPhone = document.getElementById("uPhone");
    if (uPhone) uPhone.value = phone;
    const uRole = document.getElementById("uRole");
    if (uRole) uRole.value = role;

    if (deptId) {
      const uDept = document.getElementById("uDept");
      if (uDept) uDept.value = deptId;
    }

    const uPassword = document.getElementById("uPassword");
    if (uPassword) {
      uPassword.value = "";
      uPassword.removeAttribute("required");
      uPassword.placeholder = "Biarkan kosong...";
    }
    const passHint = document.getElementById("passHint");
    if (passHint) passHint.style.display = "block";

    const modal = document.getElementById("userModal");
    if (modal) modal.style.display = "flex";
  };

  window.closeModal = function () {
    const modal = document.getElementById("userModal");
    if (modal) modal.style.display = "none";
  };

  window.togglePass = function () {
    const input = document.getElementById("uPassword");
    const icon = document.querySelector(".toggle-password");
    if (!input || !icon) return;
    if (input.type === "password") {
      input.type = "text";
      icon.classList.remove("fa-eye");
      icon.classList.add("fa-eye-slash");
    } else {
      input.type = "password";
      icon.classList.remove("fa-eye-slash");
      icon.classList.add("fa-eye");
    }
  };

  window.toggleStatus = function (id, name, currentStatus) {
    // Check authentication
    if (!ensureAuthenticated("Toggle Status")) return;

    let isDeactivating = currentStatus === "active";
    let titleText = isDeactivating ? "Nonaktifkan User?" : "Aktifkan Kembali?";
    let bodyText = isDeactivating
      ? `User <strong>${name}</strong> tidak akan bisa login.`
      : `User <strong>${name}</strong> akan dapat login kembali.`;
    let confirmColor = isDeactivating ? "#d62828" : "#2e7d32";

    Swal.fire({
      html: `
            <div style="text-align: center;">
                <div style="width: 80px; height: 80px; margin: 0 auto 15px auto; background: ${isDeactivating ? "#ffebee" : "#e8f5e9"}; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                    <i class="fa-solid ${isDeactivating ? "fa-user-slash" : "fa-user-check"}" style="font-size: 36px; color: ${confirmColor};"></i>
                </div>
                <h2 style="font-size: 22px; font-weight: 700; color: #333; margin-bottom: 10px;">${titleText}</h2>
                <p style="color: #666; font-size: 14px;">${bodyText}</p>
            </div>
        `,
      showCancelButton: true,
      confirmButtonColor: confirmColor,
      cancelButtonColor: "#E0E0E0",
      confirmButtonText: "Ya, Lanjutkan",
      cancelButtonText: '<span style="color:#555">Batal</span>',
      reverseButtons: true,
    }).then((result) => {
      if (result.isConfirmed) {
        const row = document.getElementById("user-" + id);
        const badge = document.getElementById("badge-" + id);
        const btn = document.getElementById("btn-status-" + id);

        if (isDeactivating) {
          if (row) row.classList.add("row-inactive");
          if (badge) {
            badge.className = "badge status-inactive";
            badge.innerText = "Nonaktif";
          }
          if (btn) {
            btn.className = "btn-icon btn-toggle-on";
            btn.innerHTML = '<i class="fa-solid fa-rotate-left"></i>';
            btn.setAttribute(
              "onclick",
              `toggleStatus(${id}, '${name}', 'inactive')`,
            );
          }
          Swal.fire({
            icon: "success",
            title: "User Nonaktif",
            timer: 1500,
            showConfirmButton: false,
          });
        } else {
          if (row) row.classList.remove("row-inactive");
          if (badge) {
            badge.className = "badge status-active";
            badge.innerText = "Aktif";
          }
          if (btn) {
            btn.className = "btn-icon btn-toggle-off";
            btn.innerHTML = '<i class="fa-solid fa-power-off"></i>';
            btn.setAttribute(
              "onclick",
              `toggleStatus(${id}, '${name}', 'active')`,
            );
          }
          Swal.fire({
            icon: "success",
            title: "User Aktif",
            timer: 1500,
            showConfirmButton: false,
          });
        }
      }
    });
  };

  // Save handler (form submit)
  function handleSaveForm(e) {
    e.preventDefault();

    // Check authentication
    if (!ensureAuthenticated("Save User")) return;

    const name = document.getElementById("uName").value;
    const email = document.getElementById("uEmail").value;
    const phone = document.getElementById("uPhone").value;
    const password = document.getElementById("uPassword").value;
    const role = document.getElementById("uRole").value;
    const departmentId = document.getElementById("uDept").value;

    const isEditMode = editingUserId !== null;

    if (!name || !email || !phone || !departmentId) {
      Swal.fire({
        icon: "error",
        title: "Data Tidak Lengkap",
        text: "Mohon isi semua field yang diperlukan",
        confirmButtonColor: "#d62828",
      });
      return;
    }

    if (!isEditMode && !password) {
      Swal.fire({
        icon: "error",
        title: "Password Wajib",
        text: "Password harus diisi saat menambah user baru",
        confirmButtonColor: "#d62828",
      });
      return;
    }

    window.closeModal();

    Swal.fire({
      title: isEditMode ? "Mengupdate..." : "Menyimpan...",
      text: isEditMode
        ? "Sedang mengupdate data user"
        : "Sedang membuat user baru",
      allowOutsideClick: false,
      didOpen: () => {
        Swal.showLoading();
      },
    });

    (async function () {
      try {
        if (isEditMode) {
          const updateData = {
            name,
            email,
            phone,
            department_id: parseInt(departmentId),
            roles: [role],
          };
          const response = await fetch(
            `${API_URL}/api/users/${editingUserId}`,
            {
              method: "PUT",
              headers: {
                Authorization: `Bearer ${getAuthToken()}`,
                "Content-Type": "application/json",
              },
              body: JSON.stringify(updateData),
            },
          );

          if (handleApiError(response, "Update User")) return;

          const result = await response.json();
          if (!response.ok) {
            if (result.errors) {
              let errorMessages = "";
              for (const [field, messages] of Object.entries(result.errors)) {
                errorMessages += `<strong>${field}:</strong> ${messages.join(", ")}<br>`;
              }
              throw new Error(errorMessages);
            }
            throw new Error(
              result.message || `HTTP error! status: ${response.status}`,
            );
          }

          if (password && password.trim()) {
            try {
              const resetResponse = await fetch(
                `${API_URL}/api/users/${editingUserId}/reset-password`,
                {
                  method: "POST",
                  headers: {
                    Authorization: `Bearer ${getAuthToken()}`,
                    "Content-Type": "application/json",
                  },
                  body: JSON.stringify({ password: password }),
                },
              );

              if (handleApiError(resetResponse, "Reset Password")) return;

              const resetResult = await resetResponse.json();
              if (!resetResponse.ok) {
                Swal.fire({
                  icon: "warning",
                  title: "Sebagian Berhasil",
                  html: `Data user berhasil diupdate, tapi gagal reset password.<br><br>Error: ${resetResult.message || "Unknown error"}`,
                  confirmButtonColor: "#f57c00",
                });
                loadUsers(currentPage, currentPerPage);
                return;
              }
            } catch (resetError) {
              Swal.fire({
                icon: "warning",
                title: "Sebagian Berhasil",
                html: `Data user berhasil diupdate, tapi gagal reset password.<br><br>Error: ${resetError.message}`,
                confirmButtonColor: "#f57c00",
              });
              loadUsers(currentPage, currentPerPage);
              return;
            }
          }

          Swal.fire({
            icon: "success",
            title: "Berhasil!",
            text: "User berhasil diupdate.",
            confirmButtonColor: "#1565c0",
            timer: 2000,
            showConfirmButton: false,
          });
        } else {
          const response = await fetch(`${API_URL}/api/users`, {
            method: "POST",
            headers: {
              Authorization: `Bearer ${getAuthToken()}`,
              "Content-Type": "application/json",
            },
            body: JSON.stringify({
              name,
              email,
              phone,
              password,
              department_id: parseInt(departmentId),
              roles: [role],
            }),
          });

          if (handleApiError(response, "Create User")) return;

          const result = await response.json();
          if (!response.ok) {
            if (result.errors) {
              let errorMessages = "";
              for (const [field, messages] of Object.entries(result.errors)) {
                errorMessages += `<strong>${field}:</strong> ${messages.join(", ")}<br>`;
              }
              throw new Error(errorMessages);
            }
            throw new Error(
              result.message || `HTTP error! status: ${response.status}`,
            );
          }

          Swal.fire({
            icon: "success",
            title: "Berhasil!",
            text: "User berhasil ditambahkan.",
            confirmButtonColor: "#1565c0",
            timer: 2000,
            showConfirmButton: false,
          });
        }

        loadUsers(currentPage, currentPerPage);
      } catch (error) {
        console.error("Error saving user:", error);
        Swal.fire({
          icon: "error",
          title: "Gagal Menyimpan",
          html: error.message || "Terjadi kesalahan saat menyimpan user",
          confirmButtonColor: "#d62828",
        });
      }
    })();
  }

  // Close modal when clicking outside
  window.onclick = function (event) {
    if (
      event.target.classList &&
      event.target.classList.contains("modal-overlay")
    ) {
      window.closeModal();
    }
  };

  // Init
  function init() {
    const form = document.getElementById("userForm");
    if (form) form.addEventListener("submit", handleSaveForm);
    loadUsers(currentPage, currentPerPage);
    loadDepartments();
  }

  document.addEventListener("DOMContentLoaded", init);
})();
