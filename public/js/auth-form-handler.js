/**
 * Authentication Form Handler
 * Handles form validation and submission for login and register forms
 */

/**
 * Show loading state on button
 */
function setButtonLoading(button, isLoading = true) {
    if (isLoading) {
        button.disabled = true;
        button.dataset.originalText = button.innerHTML;
        button.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Loading...';
    } else {
        button.disabled = false;
        button.innerHTML = button.dataset.originalText || button.innerHTML;
    }
}

/**
 * Validate email format
 */
function isValidEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

/**
 * Validate phone number format
 */
function isValidPhone(phone) {
    return /^\d{10,15}$/.test(phone.toString());
}

/**
 * Handle register form submission
 */
async function handleRegister(event) {
    event.preventDefault();

    const name = document.getElementById('nameReg')?.value?.trim();
    const email = document.getElementById('emailReg')?.value?.trim();
    const phone = document.getElementById('phoneReg')?.value?.trim();
    const departmentId = document.getElementById('departmentSelect')?.value;
    const password = document.getElementById('passReg')?.value;
    const passwordConfirm = document.getElementById('passConfirm')?.value;
    const btn = event.target.querySelector('button[type="submit"]');

    // Validation
    if (!name) {
        Swal.fire({ icon: 'error', title: 'Validasi Gagal', text: 'Nama lengkap harus diisi' });
        return;
    }

    if (!email || !isValidEmail(email)) {
        Swal.fire({ icon: 'error', title: 'Validasi Gagal', text: 'Email tidak valid' });
        return;
    }

    if (!phone || !isValidPhone(phone)) {
        Swal.fire({ icon: 'error', title: 'Validasi Gagal', text: 'Nomor WhatsApp harus berisi 10-15 digit' });
        return;
    }

    if (!departmentId) {
        Swal.fire({ icon: 'error', title: 'Validasi Gagal', text: 'Departemen harus dipilih' });
        return;
    }

    if (!password || password.length < 8) {
        Swal.fire({ icon: 'error', title: 'Validasi Gagal', text: 'Password minimal 8 karakter' });
        return;
    }

    if (password !== passwordConfirm) {
        Swal.fire({ icon: 'error', title: 'Validasi Gagal', text: 'Password tidak cocok' });
        return;
    }

    setButtonLoading(btn, true);

    try {
        const response = await fetch(`${API_URL}/api/register`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                name,
                email,
                phone,
                department_id: departmentId,
                password,
                password_confirmation: passwordConfirm
            })
        });

        const data = await response.json();

        if (response.ok) {
            Swal.fire({
                icon: 'success',
                title: 'Registrasi Berhasil!',
                text: 'Silakan login untuk melanjutkan.',
                timer: 1500,
                showConfirmButton: false
            }).then(() => {
                window.location.href = '/login';
            });
        } else {
            const errorMsg = data.message || data.errors?.email?.[0] || data.errors?.phone?.[0] || 'Registrasi gagal';
            Swal.fire({
                icon: 'error',
                title: 'Registrasi Gagal',
                text: errorMsg,
                confirmButtonColor: '#d62828'
            });
        }
    } catch (error) {
        console.error('Register error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error Sistem',
            text: error.message || 'Tidak dapat menghubungi server API',
            confirmButtonColor: '#d62828'
        });
    } finally {
        setButtonLoading(btn, false);
    }
}

/**
 * Handle login form submission
 */
async function handleLogin(event) {
    event.preventDefault();

    const email = document.getElementById('email')?.value?.trim();
    const password = document.getElementById('password')?.value;
    const btn = event.target.querySelector('button[type="submit"]') || document.getElementById('btnLogin');

    // Validation
    if (!email || !isValidEmail(email)) {
        Swal.fire({ icon: 'error', title: 'Validasi Gagal', text: 'Email tidak valid' });
        return;
    }

    if (!password) {
        Swal.fire({ icon: 'error', title: 'Validasi Gagal', text: 'Password harus diisi' });
        return;
    }

    setButtonLoading(btn, true);

    try {
        const response = await fetch(`${API_URL}/api/login`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                login: email,
                password: password
            })
        });

        const data = await response.json();

        if (response.ok && data.token) {
            // Save token, user data, and roles
            const user = data.data || data.user || null;
            const roles = data.roles || [];
            
            const saved = TokenManager.setAuth(data.token, user, roles);

            if (!saved) {
                throw new Error('Gagal menyimpan data autentikasi');
            }

            Swal.fire({
                icon: 'success',
                title: 'Login Berhasil!',
                text: 'Mengalihkan ke dashboard...',
                timer: 1500,
                showConfirmButton: false
            }).then(() => {
                TokenManager.redirectToDashboard();
            });
        } else {
            const errorMsg = data.message || data.errors?.login?.[0] || 'Email atau password salah';
            Swal.fire({
                icon: 'error',
                title: 'Gagal Masuk',
                text: errorMsg,
                confirmButtonColor: '#d62828'
            });
        }
    } catch (error) {
        console.error('Login error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error Sistem',
            text: error.message || 'Tidak dapat menghubungi server API',
            confirmButtonColor: '#d62828'
        });
    } finally {
        setButtonLoading(btn, false);
    }
}

/**
 * Toggle password visibility
 */
function togglePassword(inputId, iconElement) {
    const input = document.getElementById(inputId);
    const icon = iconElement.querySelector('i');

    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}

/**
 * Load departments from API
 */
async function loadDepartments() {
    const selectElement = document.getElementById('departmentSelect');
    if (!selectElement) return;

    try {
        const response = await fetch(`${API_URL}/api/departments`);

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const result = await response.json();

        if (result.data && Array.isArray(result.data)) {
            selectElement.innerHTML = '<option value="" disabled selected>-- Pilih Departemen --</option>';

            result.data.forEach(dept => {
                const option = document.createElement('option');
                option.value = dept.id;
                option.textContent = dept.name;
                selectElement.appendChild(option);
            });
        } else {
            selectElement.innerHTML = '<option value="" disabled selected>-- Gagal memuat departemen --</option>';
            console.error('Invalid data format:', result);
        }
    } catch (error) {
        console.error('Error loading departments:', error);
        selectElement.innerHTML = '<option value="" disabled selected>-- Error memuat departemen --</option>';
    }
}

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    loadDepartments();
});
