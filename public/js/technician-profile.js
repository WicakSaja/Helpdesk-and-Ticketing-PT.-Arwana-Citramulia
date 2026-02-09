document.addEventListener("DOMContentLoaded", function () {
  const API_BASE = window.location.origin;

  // Toggle password visibility
  window.togglePass = function (inputId, iconElement) {
    const input = document.getElementById(inputId);
    const icon = iconElement.querySelector("i");
    if (input.type === "password") {
      input.type = "text";
      icon.classList.replace("fa-eye", "fa-eye-slash");
    } else {
      input.type = "password";
      icon.classList.replace("fa-eye-slash", "fa-eye");
    }
  };

  // Load profile data from /api/me
  loadProfile();

  async function loadProfile() {
    try {
      const token =
        localStorage.getItem("auth_token") ||
        sessionStorage.getItem("auth_token");
      if (!token) {
        console.warn("No auth token found");
        return;
      }

      const meResponse = await fetch(`${API_BASE}/api/me`, {
        headers: {
          Authorization: `Bearer ${token}`,
          Accept: "application/json",
        },
      });

      if (!meResponse.ok) {
        console.error("Failed to load profile data:", meResponse.status);
        return;
      }

      const meResult = await meResponse.json();
      const user = meResult.user || {};
      const roles = meResult.roles || [];

      // Update profile display
      const nameDisplay = document.getElementById("profile_name_display");
      const roleDisplay = document.getElementById("profile_role");
      const avatarImg = document.getElementById("profile_avatar");
      const statusEl = document.getElementById("profile_status");
      const ticketsCountEl = document.getElementById("profile_tickets_count");

      // Update form inputs
      const nameInput = document.getElementById("profile_name");
      const emailInput = document.getElementById("profile_email");
      const phoneInput = document.getElementById("profile_phone");

      if (nameDisplay) nameDisplay.textContent = user.name || "-";
      if (roleDisplay)
        roleDisplay.textContent =
          roles && roles.length > 0 ? roles[0] : "Technician";
      if (avatarImg)
        avatarImg.src = `https://ui-avatars.com/api/?name=${encodeURIComponent(user.name || "User")}&background=2e7d32&color=fff&size=256`;
      if (statusEl)
        statusEl.textContent = user.is_active ? "Available" : "Offline";

      // Update status styling
      const statusValue = statusEl?.closest(".profile-stat-value");
      if (statusValue) {
        statusValue.classList.remove("status-active", "status-offline");
        statusValue.classList.add(
          user.is_active ? "status-active" : "status-offline",
        );
      }

      if (nameInput) nameInput.value = user.name || "";
      if (emailInput) emailInput.value = user.email || "";
      if (phoneInput) phoneInput.value = user.phone || "";

      // Fetch resolved tickets count from API
      try {
        const resolvedRes = await fetch(
          `${API_BASE}/api/users/${user.id}/resolved-tickets`,
          {
            headers: {
              Authorization: `Bearer ${token}`,
              "Content-Type": "application/json",
              Accept: "application/json",
            },
          },
        );

        if (resolvedRes.ok) {
          const resolvedJson = await resolvedRes.json();
          const totalResolved = resolvedJson.data?.total_resolved || 0;
          if (ticketsCountEl) ticketsCountEl.textContent = totalResolved;
        } else {
          if (ticketsCountEl) ticketsCountEl.textContent = "-";
        }
      } catch (error) {
        console.error("Error fetching resolved tickets:", error);
        if (ticketsCountEl) ticketsCountEl.textContent = "-";
      }
    } catch (error) {
      console.error("Error loading profile from /api/me:", error);
    }
  }

  // Change password
  const saveBtn = document.querySelector(".btn-save");
  if (saveBtn) {
    saveBtn.addEventListener("click", async function () {
      const oldPass = document.getElementById("old_pass").value;
      const newPass = document.getElementById("new_pass").value;
      const confPass = document.getElementById("conf_pass").value;

      const showMsg = (type, title, text) => {
        if (typeof Swal !== "undefined") {
          Swal.fire({
            icon: type,
            title: title,
            text: text,
            confirmButtonColor: "#d62828",
          });
        } else {
          alert(title + ": " + text);
        }
      };

      if (!oldPass)
        return showMsg("warning", "Peringatan", "Password lama wajib diisi");
      if (!newPass || newPass.length < 8)
        return showMsg(
          "warning",
          "Peringatan",
          "Password baru minimal 8 karakter",
        );
      if (newPass !== confPass)
        return showMsg("error", "Error", "Konfirmasi password tidak cocok");

      const token =
        localStorage.getItem("auth_token") ||
        sessionStorage.getItem("auth_token");
      if (!token)
        return showMsg(
          "error",
          "Error",
          "Token tidak ditemukan, silakan login ulang",
        );

      const originalBtnText = saveBtn.innerHTML;
      saveBtn.innerHTML =
        '<i class="fa-solid fa-spinner fa-spin"></i> Menyimpan...';
      saveBtn.disabled = true;

      try {
        const res = await fetch(`${API_BASE}/api/change-password`, {
          method: "POST",
          headers: {
            Authorization: `Bearer ${token}`,
            "Content-Type": "application/json",
            Accept: "application/json",
          },
          body: JSON.stringify({
            old_password: oldPass,
            new_password: newPass,
          }),
        });

        const json = await res.json();

        if (res.ok) {
          showMsg(
            "success",
            "Berhasil",
            json.message || "Password telah diperbarui.",
          );
          document.getElementById("old_pass").value = "";
          document.getElementById("new_pass").value = "";
          document.getElementById("conf_pass").value = "";
        } else {
          const msg = json.message || "Gagal mengubah password";
          showMsg("error", "Gagal", msg);
        }
      } catch (err) {
        showMsg("error", "Error", "Gagal menghubungi server.");
      } finally {
        saveBtn.innerHTML = originalBtnText;
        saveBtn.disabled = false;
      }
    });
  }
});
