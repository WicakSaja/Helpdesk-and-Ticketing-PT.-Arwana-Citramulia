(function () {
  // Ensure API_URL is defined by layout; fallback to origin
  const API =
    typeof API_URL !== "undefined"
      ? API_URL
      : window.location.origin.replace(/\/$/, "");

  document.addEventListener("DOMContentLoaded", function () {
    // Role protection from existing script
    if (typeof requireRequesterRole === "function") {
      try {
        requireRequesterRole();
      } catch (err) {
        console.warn("requireRequesterRole error", err);
      }
    }

    const form = document.getElementById("ticketCreateForm");
    const btn = document.getElementById("btnSubmitTicket");
    const categorySelect = document.getElementById("category_id");

    if (categorySelect) loadCategories();

    if (form) form.addEventListener("submit", submitHandler);

    async function loadCategories() {
      if (!categorySelect) return;
      categorySelect.innerHTML =
        '<option value="" disabled selected>Memuat kategori...</option>';

      try {
        const res = await fetch(`${API}/api/categories`, {
          method: "GET",
          headers: { "Content-Type": "application/json" },
        });
        if (!res.ok) throw new Error("Gagal memuat kategori");
        const json = await res.json();

        const items = json.data || (Array.isArray(json) ? json : []);
        if (items.length === 0) {
          categorySelect.innerHTML =
            '<option value="" disabled selected>Tidak ada kategori</option>';
          return;
        }

        categorySelect.innerHTML =
          '<option value="" disabled selected>-- Pilih Kategori --</option>';
        items.forEach((cat) => {
          const opt = document.createElement("option");
          opt.value = cat.id;
          opt.textContent = cat.name; // assume 'name' exists
          categorySelect.appendChild(opt);
        });
      } catch (err) {
        console.error("loadCategories error", err);
        categorySelect.innerHTML =
          '<option value="" disabled selected>Error loading categories</option>';
      }
    }

    async function submitHandler(event) {
      event.preventDefault();
      if (!form) return;

      const subject = document.getElementById("subject").value.trim();
      const description = document.getElementById("description").value.trim();
      const categoryId = document.getElementById("category_id").value;

      if (!subject || !description || !categoryId) {
        showAlert("error", "Validasi Gagal", "Semua field harus diisi.");
        return;
      }

      setButtonLoading(btn, true);

      try {
        const headers =
          typeof TokenManager !== "undefined" &&
          typeof TokenManager.getHeaders === "function"
            ? TokenManager.getHeaders()
            : { "Content-Type": "application/json" };

        const response = await fetch(`${API}/api/tickets`, {
          method: "POST",
          headers: headers,
          body: JSON.stringify({
            subject: subject,
            description: description,
            category_id: Number(categoryId),
            channel: "web",
          }),
        });

        const data = await response.json();

        if (response.ok) {
          // Prefer showing a nice modal with ticket info instead of immediate redirect
          // Pass the full response JSON to the modal normalizer so it accepts different shapes
          showCreatedModal(data);
        } else {
          const errorMsg = data.message || "Gagal membuat tiket.";
          showAlert("error", "Gagal", errorMsg);
        }
      } catch (error) {
        console.error("submit ticket error", error);
        showAlert(
          "error",
          "Error",
          error.message || "Tidak dapat menghubungi server API",
        );
      } finally {
        setButtonLoading(btn, false);
      }
    }

    function setButtonLoading(button, isLoading) {
      if (!button) return;
      if (isLoading) {
        button.disabled = true;
        button.dataset.originalText = button.innerHTML;
        button.innerHTML =
          '<i class="fa-solid fa-spinner fa-spin"></i> Mengirim...';
      } else {
        button.disabled = false;
        button.innerHTML = button.dataset.originalText || button.innerHTML;
      }
    }

    function showAlert(type, title, text) {
      if (typeof Swal !== "undefined") {
        Swal.fire({
          icon: type,
          title: title,
          text: text,
          confirmButtonColor: "#d62828",
        });
        return;
      }
      alert(`${title}: ${text}`);
    }

    // --- Modal helpers for create success ---
    // Normalize different possible API response shapes and extract ticket object
    function normalizeTicket(resp) {
      if (!resp) return null;
      let t = resp;
      if (t.data) t = t.data; // { data: {...} }
      if (t.ticket) t = t.ticket; // { ticket: {...} }
      if (t.data && (t.data.id || t.data.ticket_number)) t = t.data; // nested data
      if (Array.isArray(t) && t.length) t = t[0];
      return t;
    }

    function formatDateTime(iso) {
      if (!iso) return "-";
      const d = new Date(iso);
      if (isNaN(d.getTime())) return iso;
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
      const day = d.getDate();
      const month = months[d.getMonth()];
      const year = d.getFullYear();
      const hours = d.getHours().toString().padStart(2, "0");
      const minutes = d.getMinutes().toString().padStart(2, "0");
      return `${day} ${month} ${year} ${hours}.${minutes}`;
    }

    function showCreatedModal(raw) {
      try {
        const ticket = normalizeTicket(raw) || {};
        const number =
          ticket.ticket_number ||
          `#TKT-${String(ticket.id || "").padStart(3, "0")}`;
        const subject = ticket.subject || "-";
        const createdAt = ticket.created_at || ticket.createdAt || null;
        const desc = ticket.description || "-";

        // Resolve category name: prefer nested object, otherwise try to lookup from select options
        let catName =
          (ticket.category && (ticket.category.name || ticket.category)) ||
          (ticket.category_id ? ticket.category_id : "-");
        try {
          const catId = ticket.category_id || ticket.category;
          const sel = document.getElementById("category_id");
          if (sel) {
            // try find by id value first
            if (catId !== undefined && catId !== null) {
              const opt = sel.querySelector(`option[value="${catId}"]`);
              if (opt) catName = opt.textContent;
            }
            // fallback to currently selected option (user's selection during create)
            if (
              (catName === "-" || !catName) &&
              sel.selectedOptions &&
              sel.selectedOptions.length
            ) {
              catName = sel.selectedOptions[0].textContent;
            }
          }
        } catch (e) {
          /* ignore */
        }

        const el = document.getElementById("createSuccessModal");
        if (!el) {
          showAlert("success", "Tiket Dibuat", `${number} - ${subject}`);
          return;
        }

        const cTicketNo = document.getElementById("cTicketNo");
        if (cTicketNo) cTicketNo.innerText = number;
        const cSub = document.getElementById("cSub");
        if (cSub) cSub.innerText = subject;
        const cCat = document.getElementById("cCat");
        if (cCat) cCat.innerText = catName;
        const cTime = document.getElementById("cTime");
        if (cTime)
          cTime.innerText = createdAt ? formatDateTime(createdAt) : "-";
        const cDesc = document.getElementById("cDesc");
        if (cDesc) cDesc.innerText = desc;

        el.style.display = "flex";

        // Ensure we do NOT auto-open detail on history page: remove any previous flag
        try {
          sessionStorage.removeItem("last_created_ticket");
        } catch (e) {}
      } catch (e) {
        console.error("showCreatedModal error", e);
      }
    }

    function closeCreateModal() {
      const el = document.getElementById("createSuccessModal");
      if (el) el.style.display = "none";
    }

    function resetCreateForm() {
      const subj = document.getElementById("subject");
      if (subj) subj.value = "";
      const desc = document.getElementById("description");
      if (desc) desc.value = "";
      const cat = document.getElementById("category_id");
      if (cat) cat.selectedIndex = 0;
      closeCreateModal();
    }

    // expose close/reset so inline onclick handlers work
    window.closeCreateModal = closeCreateModal;
    window.resetCreateForm = resetCreateForm;

    // close modal when clicking on overlay background
    const _overlay = document.getElementById("createSuccessModal");
    if (_overlay)
      _overlay.addEventListener("click", function (e) {
        if (e.target === _overlay) closeCreateModal();
      });
  });
})();
