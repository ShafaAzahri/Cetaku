// Fungsi untuk menampilkan pesan alert
const showAlert = (type, message) => {
    const alertContainer = document.getElementById("alert-container");
    const alertHTML = `
        <div class="alert alert-${type} alert-dismissible fade show" role="alert">
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    `;
    alertContainer.innerHTML = alertHTML;

    // Auto dismiss after 5 seconds
    setTimeout(() => {
        const alertElement = alertContainer.querySelector(".alert");
        if (alertElement) {
            const bsAlert = new bootstrap.Alert(alertElement);
            bsAlert.close();
        }
    }, 5000);
};

// Fungsi untuk menutup modal dengan benar
const closeModal = (modalId) => {
    const modalEl = document.getElementById(modalId);
    if (modalEl) {
        const modal = bootstrap.Modal.getInstance(modalEl);
        if (modal) {
            modal.hide();
        }

        // Remove backdrop and fix body
        document.body.classList.remove("modal-open");
        document.body.style.overflow = "";
        document.body.style.paddingRight = "";

        // Remove all backdrops
        const backdrops = document.querySelectorAll(".modal-backdrop");
        backdrops.forEach((backdrop) => backdrop.remove());
    }
};

// Fungsi untuk reset modal setelah ditutup
const resetModal = (modalId) => {
    const modalEl = document.getElementById(modalId);
    if (modalEl) {
        modalEl.addEventListener("hidden.bs.modal", function () {
            // Reset form if exists
            const form = modalEl.querySelector("form");
            if (form) {
                form.reset();
            }

            // Clean up any remaining elements
            document.body.classList.remove("modal-open");
            document.body.style.overflow = "";
            document.body.style.paddingRight = "";

            const backdrops = document.querySelectorAll(".modal-backdrop");
            backdrops.forEach((backdrop) => backdrop.remove());
        });
    }
};

// ================================
// ITEMS MANAGEMENT
// ================================

// Load data items
const loadItems = async () => {
    try {
        const token = localStorage.getItem("api_token");
        const response = await axios.get("/api/admin/items", {
            headers: {
                Authorization: `Bearer ${token}`,
                Accept: "application/json",
            },
        });

        if (response.data.success) {
            const items = response.data.data.data;
            const tbody = document.getElementById("items-tbody");

            if (items.length === 0) {
                tbody.innerHTML =
                    '<tr><td colspan="6" class="text-center">Tidak ada data produk</td></tr>';
                return;
            }

            let html = "";
            items.forEach((item, index) => {
                const imageUrl = item.gambar
                    ? `/storage/${item.gambar}`
                    : "/images/no-image.png";
                html += `
                    <tr>
                        <td>${index + 1}</td>
                        <td><img src="${imageUrl}" alt="${
                    item.nama_item
                }" class="img-thumbnail" style="max-width: 80px;"></td>
                        <td>${item.nama_item}</td>
                        <td>${item.deskripsi || "-"}</td>
                        <td>Rp ${parseFloat(item.harga_dasar).toLocaleString(
                            "id-ID"
                        )}</td>
                        <td>
                            <div class="action-buttons">
                                <button class="btn btn-sm btn-warning edit-item" data-id="${
                                    item.id
                                }" data-bs-toggle="modal" data-bs-target="#editItemModal">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-danger delete-item" data-id="${
                                    item.id
                                }" data-name="${item.nama_item}">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                `;
            });
            tbody.innerHTML = html;

            // Add event listeners
            document.querySelectorAll(".edit-item").forEach((button) => {
                button.addEventListener("click", handleEditItem);
            });

            document.querySelectorAll(".delete-item").forEach((button) => {
                button.addEventListener("click", handleDeleteItem);
            });
        }
    } catch (error) {
        console.error("Error loading items:", error);
        showAlert("danger", "Gagal memuat data produk");
    }
};

// Handle add item
const handleAddItem = async (event) => {
    event.preventDefault();

    try {
        const token = localStorage.getItem("api_token");
        const formData = new FormData(event.target);

        const response = await axios.post("/api/admin/items", formData, {
            headers: {
                Authorization: `Bearer ${token}`,
                Accept: "application/json",
                "Content-Type": "multipart/form-data",
            },
        });

        if (response.data.success) {
            showAlert("success", "Produk berhasil ditambahkan");
            event.target.reset();
            closeModal("addItemModal");
            loadItems();
        }
    } catch (error) {
        console.error("Error adding item:", error);
        showAlert(
            "danger",
            error.response?.data?.message || "Gagal menambahkan produk"
        );
    }
};

// Handle edit item
const handleEditItem = async (event) => {
    const itemId = event.currentTarget.dataset.id;

    try {
        const token = localStorage.getItem("api_token");
        const response = await axios.get(`/api/admin/items/${itemId}`, {
            headers: {
                Authorization: `Bearer ${token}`,
                Accept: "application/json",
            },
        });

        if (response.data.success) {
            const item = response.data.data;

            // Fill form
            document.getElementById("edit_nama_item").value = item.nama_item;
            document.getElementById("edit_harga_dasar").value =
                item.harga_dasar;
            document.getElementById("edit_deskripsi").value =
                item.deskripsi || "";

            // Show current image if exists
            const currentImageContainer = document.getElementById(
                "current_image_container"
            );
            const currentImage = document.getElementById("current_image");

            if (item.gambar) {
                currentImage.src = `/storage/${item.gambar}`;
                currentImageContainer.style.display = "block";
            } else {
                currentImageContainer.style.display = "none";
            }

            // Store item ID for update
            document.getElementById("edit_item_id").value = itemId;
        }
    } catch (error) {
        console.error("Error loading item:", error);
        showAlert("danger", "Gagal memuat data produk");
    }
};

// Handle update item
const handleUpdateItem = async (event) => {
    event.preventDefault();

    const itemId = document.getElementById("edit_item_id").value;

    try {
        const token = localStorage.getItem("api_token");
        const formData = new FormData(event.target);

        // Laravel expects PUT method to be spoofed with POST
        formData.append("_method", "PUT");

        const response = await axios.post(
            `/api/admin/items/${itemId}`,
            formData,
            {
                headers: {
                    Authorization: `Bearer ${token}`,
                    Accept: "application/json",
                    "Content-Type": "multipart/form-data",
                },
            }
        );

        if (response.data.success) {
            showAlert("success", "Produk berhasil diperbarui");
            closeModal("editItemModal");
            loadItems();
        }
    } catch (error) {
        console.error("Error updating item:", error);
        showAlert(
            "danger",
            error.response?.data?.message || "Gagal memperbarui produk"
        );
    }
};

// Handle delete item
const handleDeleteItem = async (event) => {
    const itemId = event.currentTarget.dataset.id;
    const itemName = event.currentTarget.dataset.name;

    if (confirm(`Apakah Anda yakin ingin menghapus produk "${itemName}"?`)) {
        try {
            const token = localStorage.getItem("api_token");
            const response = await axios.delete(`/api/admin/items/${itemId}`, {
                headers: {
                    Authorization: `Bearer ${token}`,
                    Accept: "application/json",
                },
            });

            if (response.data.success) {
                showAlert("success", "Produk berhasil dihapus");
                loadItems();
            }
        } catch (error) {
            console.error("Error deleting item:", error);
            showAlert(
                "danger",
                error.response?.data?.message || "Gagal menghapus produk"
            );
        }
    }
};

// ================================
// BAHAN MANAGEMENT
// ================================

// Load items untuk dropdown bahan
const loadItemsForBahanDropdown = async () => {
    try {
        const token = localStorage.getItem("api_token");
        const response = await axios.get("/api/admin/items/all", {
            headers: {
                Authorization: `Bearer ${token}`,
                Accept: "application/json",
            },
        });

        if (response.data.success) {
            const items = response.data.data;
            const itemSelect = document.getElementById("item_id");
            const editItemSelect = document.getElementById("edit_item_id");

            // Clear existing options
            itemSelect.innerHTML =
                '<option value="">-- Pilih Item Produk --</option>';
            editItemSelect.innerHTML =
                '<option value="">-- Pilih Item Produk --</option>';

            // Add new options
            items.forEach((item) => {
                const option = `<option value="${item.id}">${item.nama_item}</option>`;
                itemSelect.innerHTML += option;
                editItemSelect.innerHTML += option;
            });
        }
    } catch (error) {
        console.error("Error loading items:", error);
        showAlert("danger", "Gagal memuat daftar item");
    }
};

// Load data bahan
const loadBahans = async () => {
    try {
        const token = localStorage.getItem("api_token");
        const response = await axios.get("/api/admin/bahans", {
            headers: {
                Authorization: `Bearer ${token}`,
                Accept: "application/json",
            },
        });

        if (response.data.success) {
            const bahans = response.data.data.data;
            const tbody = document.getElementById("bahans-tbody");

            if (bahans.length === 0) {
                tbody.innerHTML =
                    '<tr><td colspan="5" class="text-center">Tidak ada data bahan</td></tr>';
                return;
            }

            let html = "";
            bahans.forEach((bahan, index) => {
                html += `
                    <tr>
                        <td>${index + 1}</td>
                        <td>${
                            bahan.items && bahan.items.length > 0
                                ? bahan.items[0].nama_item
                                : "-"
                        }</td>
                        <td>${bahan.nama_bahan}</td>
                        <td>Rp ${parseFloat(
                            bahan.biaya_tambahan
                        ).toLocaleString("id-ID")}</td>
                        <td>
                            <div class="action-buttons">
                                <button class="btn btn-sm btn-warning edit-bahan" data-id="${
                                    bahan.id
                                }" data-bs-toggle="modal" data-bs-target="#editBahanModal">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-danger delete-bahan" data-id="${
                                    bahan.id
                                }" data-name="${bahan.nama_bahan}">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                `;
            });
            tbody.innerHTML = html;

            // Add event listeners
            document.querySelectorAll(".edit-bahan").forEach((button) => {
                button.addEventListener("click", handleEditBahan);
            });

            document.querySelectorAll(".delete-bahan").forEach((button) => {
                button.addEventListener("click", handleDeleteBahan);
            });
        }
    } catch (error) {
        console.error("Error loading bahans:", error);
        showAlert("danger", "Gagal memuat data bahan");
    }
};

// Handle add bahan
const handleAddBahan = async (event) => {
    event.preventDefault();

    try {
        const token = localStorage.getItem("api_token");
        const formData = new FormData(event.target);

        // Convert FormData to JSON
        const data = {
            item_id: formData.get("item_id"),
            nama_bahan: formData.get("nama_bahan"),
            biaya_tambahan: formData.get("biaya_tambahan"),
            is_available: formData.get("is_available") === "1" ? true : false,
        };

        const response = await axios.post("/api/admin/bahans", data, {
            headers: {
                Authorization: `Bearer ${token}`,
                Accept: "application/json",
                "Content-Type": "application/json",
            },
        });

        if (response.data.success) {
            showAlert("success", "Bahan berhasil ditambahkan");
            event.target.reset();
            closeModal("addBahanModal");
            loadBahans();
        }
    } catch (error) {
        console.error("Error adding bahan:", error);
        showAlert(
            "danger",
            error.response?.data?.message || "Gagal menambahkan bahan"
        );
    }
};

// Handle edit bahan
const handleEditBahan = async (event) => {
    const bahanId = event.currentTarget.dataset.id;

    try {
        const token = localStorage.getItem("api_token");

        // Load items for dropdown first
        await loadItemsForBahanDropdown();

        const response = await axios.get(`/api/admin/bahans/${bahanId}`, {
            headers: {
                Authorization: `Bearer ${token}`,
                Accept: "application/json",
            },
        });

        if (response.data.success) {
            const bahan = response.data.data;

            // Fill form
            document.getElementById("edit_nama_bahan").value = bahan.nama_bahan;
            document.getElementById("edit_biaya_tambahan").value =
                bahan.biaya_tambahan;

            // Check if edit_is_available exists before setting
            const isAvailableCheckbox =
                document.getElementById("edit_is_available");
            if (isAvailableCheckbox) {
                isAvailableCheckbox.checked = bahan.is_available || false;
            }

            // Set item dropdown value
            const editItemSelect = document.getElementById("edit_item_id");
            if (editItemSelect && bahan.items && bahan.items.length > 0) {
                editItemSelect.value = bahan.items[0].id;
            }

            // Store bahan ID for update
            document.getElementById("editBahanForm").dataset.bahanId = bahanId;
        }
    } catch (error) {
        console.error("Error loading bahan:", error);
        showAlert("danger", "Gagal memuat data bahan");
    }
};

// Handle update bahan
const handleUpdateBahan = async (event) => {
    event.preventDefault();

    const bahanId = event.target.dataset.bahanId;

    try {
        const token = localStorage.getItem("api_token");
        const formData = new FormData(event.target);

        // Convert FormData to JSON
        const data = {
            item_id: formData.get("item_id"),
            nama_bahan: formData.get("nama_bahan"),
            biaya_tambahan: formData.get("biaya_tambahan"),
            is_available: formData.get("is_available") === "1" ? true : false,
        };

        const response = await axios.put(`/api/admin/bahans/${bahanId}`, data, {
            headers: {
                Authorization: `Bearer ${token}`,
                Accept: "application/json",
                "Content-Type": "application/json",
            },
        });

        if (response.data.success) {
            showAlert("success", "Bahan berhasil diperbarui");
            closeModal("editBahanModal");
            loadBahans();
        }
    } catch (error) {
        console.error("Error updating bahan:", error);
        showAlert(
            "danger",
            error.response?.data?.message || "Gagal memperbarui bahan"
        );
    }
};

// Handle delete bahan
const handleDeleteBahan = async (event) => {
    const bahanId = event.currentTarget.dataset.id;
    const bahanName = event.currentTarget.dataset.name;

    if (confirm(`Apakah Anda yakin ingin menghapus bahan "${bahanName}"?`)) {
        try {
            const token = localStorage.getItem("api_token");
            const response = await axios.delete(
                `/api/admin/bahans/${bahanId}`,
                {
                    headers: {
                        Authorization: `Bearer ${token}`,
                        Accept: "application/json",
                    },
                }
            );

            if (response.data.success) {
                showAlert("success", "Bahan berhasil dihapus");
                loadBahans();
            }
        } catch (error) {
            console.error("Error deleting bahan:", error);
            showAlert(
                "danger",
                error.response?.data?.message || "Gagal menghapus bahan"
            );
        }
    }
};

// ================================
// UKURAN MANAGEMENT
// ================================

// Load items untuk dropdown ukuran
const loadItemsForUkuranDropdown = async () => {
    try {
        const token = localStorage.getItem("api_token");
        const response = await axios.get("/api/admin/items/all", {
            headers: {
                Authorization: `Bearer ${token}`,
                Accept: "application/json",
            },
        });

        if (response.data.success) {
            const items = response.data.data;
            const itemSelect = document.getElementById("item_id_ukuran");
            const editItemSelect = document.getElementById(
                "edit_item_id_ukuran"
            );

            // Clear existing options
            itemSelect.innerHTML =
                '<option value="">-- Pilih Item Produk --</option>';
            editItemSelect.innerHTML =
                '<option value="">-- Pilih Item Produk --</option>';

            // Add new options
            items.forEach((item) => {
                const option = `<option value="${item.id}">${item.nama_item}</option>`;
                itemSelect.innerHTML += option;
                editItemSelect.innerHTML += option;
            });
        }
    } catch (error) {
        console.error("Error loading items:", error);
        showAlert("danger", "Gagal memuat daftar item");
    }
};

// Load data ukuran
const loadUkurans = async () => {
    try {
        const token = localStorage.getItem("api_token");
        const response = await axios.get("/api/admin/ukurans", {
            headers: {
                Authorization: `Bearer ${token}`,
                Accept: "application/json",
            },
        });

        if (response.data.success) {
            const ukurans = response.data.data.data;
            const tbody = document.getElementById("ukurans-tbody");

            if (ukurans.length === 0) {
                tbody.innerHTML =
                    '<tr><td colspan="5" class="text-center">Tidak ada data ukuran</td></tr>';
                return;
            }

            let html = "";
            ukurans.forEach((ukuran, index) => {
                html += `
                    <tr>
                        <td>${index + 1}</td>
                        <td>${
                            ukuran.items && ukuran.items.length > 0
                                ? ukuran.items[0].nama_item
                                : "-"
                        }</td>
                        <td>${ukuran.size}</td>
                        <td>${ukuran.faktor_harga}x</td>
                        <td>
                            <div class="action-buttons">
                                <button class="btn btn-sm btn-warning edit-ukuran" data-id="${
                                    ukuran.id
                                }" data-bs-toggle="modal" data-bs-target="#editUkuranModal">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-danger delete-ukuran" data-id="${
                                    ukuran.id
                                }" data-name="${ukuran.size}">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                `;
            });
            tbody.innerHTML = html;

            // Add event listeners
            document.querySelectorAll(".edit-ukuran").forEach((button) => {
                button.addEventListener("click", handleEditUkuran);
            });

            document.querySelectorAll(".delete-ukuran").forEach((button) => {
                button.addEventListener("click", handleDeleteUkuran);
            });
        }
    } catch (error) {
        console.error("Error loading ukurans:", error);
        showAlert("danger", "Gagal memuat data ukuran");
    }
};

// Handle add ukuran
const handleAddUkuran = async (event) => {
    event.preventDefault();

    try {
        const token = localStorage.getItem("api_token");
        const formData = new FormData(event.target);

        // Convert FormData to JSON
        const data = {
            item_id: formData.get("item_id"),
            size: formData.get("size"),
            faktor_harga: formData.get("faktor_harga"),
        };

        const response = await axios.post("/api/admin/ukurans", data, {
            headers: {
                Authorization: `Bearer ${token}`,
                Accept: "application/json",
                "Content-Type": "application/json",
            },
        });

        if (response.data.success) {
            showAlert("success", "Ukuran berhasil ditambahkan");
            event.target.reset();
            closeModal("addUkuranModal");
            loadUkurans();
        }
    } catch (error) {
        console.error("Error adding ukuran:", error);
        showAlert(
            "danger",
            error.response?.data?.message || "Gagal menambahkan ukuran"
        );
    }
};

// Handle edit ukuran
const handleEditUkuran = async (event) => {
    const ukuranId = event.currentTarget.dataset.id;

    try {
        const token = localStorage.getItem("api_token");

        // Load items for dropdown first
        await loadItemsForUkuranDropdown();

        const response = await axios.get(`/api/admin/ukurans/${ukuranId}`, {
            headers: {
                Authorization: `Bearer ${token}`,
                Accept: "application/json",
            },
        });

        if (response.data.success) {
            const ukuran = response.data.data;

            // Fill form
            document.getElementById("edit_size").value = ukuran.size;
            document.getElementById("edit_faktor_harga").value =
                ukuran.faktor_harga;

            // Set item dropdown value
            const editItemSelect = document.getElementById(
                "edit_item_id_ukuran"
            );
            if (editItemSelect && ukuran.items && ukuran.items.length > 0) {
                editItemSelect.value = ukuran.items[0].id;
            }

            // Store ukuran ID for update
            document.getElementById("editUkuranForm").dataset.ukuranId =
                ukuranId;
        }
    } catch (error) {
        console.error("Error loading ukuran:", error);
        showAlert("danger", "Gagal memuat data ukuran");
    }
};

// Handle update ukuran
const handleUpdateUkuran = async (event) => {
    event.preventDefault();

    const ukuranId = event.target.dataset.ukuranId;

    try {
        const token = localStorage.getItem("api_token");
        const formData = new FormData(event.target);

        // Convert FormData to JSON
        const data = {
            item_id: formData.get("item_id"),
            size: formData.get("size"),
            faktor_harga: formData.get("faktor_harga"),
        };

        const response = await axios.put(
            `/api/admin/ukurans/${ukuranId}`,
            data,
            {
                headers: {
                    Authorization: `Bearer ${token}`,
                    Accept: "application/json",
                    "Content-Type": "application/json",
                },
            }
        );

        if (response.data.success) {
            showAlert("success", "Ukuran berhasil diperbarui");
            closeModal("editUkuranModal");
            loadUkurans();
        }
    } catch (error) {
        console.error("Error updating ukuran:", error);
        showAlert(
            "danger",
            error.response?.data?.message || "Gagal memperbarui ukuran"
        );
    }
};

// Handle delete ukuran
const handleDeleteUkuran = async (event) => {
    const ukuranId = event.currentTarget.dataset.id;
    const ukuranName = event.currentTarget.dataset.name;

    if (confirm(`Apakah Anda yakin ingin menghapus ukuran "${ukuranName}"?`)) {
        try {
            const token = localStorage.getItem("api_token");
            const response = await axios.delete(
                `/api/admin/ukurans/${ukuranId}`,
                {
                    headers: {
                        Authorization: `Bearer ${token}`,
                        Accept: "application/json",
                    },
                }
            );

            if (response.data.success) {
                showAlert("success", "Ukuran berhasil dihapus");
                loadUkurans();
            }
        } catch (error) {
            console.error("Error deleting ukuran:", error);
            showAlert(
                "danger",
                error.response?.data?.message || "Gagal menghapus ukuran"
            );
        }
    }
};

// ================================
// JENIS MANAGEMENT
// ================================

// Load items untuk dropdown jenis
const loadItemsForJenisDropdown = async () => {
    try {
        const token = localStorage.getItem("api_token");
        const response = await axios.get("/api/admin/items/all", {
            headers: {
                Authorization: `Bearer ${token}`,
                Accept: "application/json",
            },
        });

        if (response.data.success) {
            const items = response.data.data;
            const itemSelect = document.getElementById("item_id_jenis");
            const editItemSelect =
                document.getElementById("edit_item_id_jenis");

            // Clear existing options
            itemSelect.innerHTML =
                '<option value="">-- Pilih Item Produk --</option>';
            editItemSelect.innerHTML =
                '<option value="">-- Pilih Item Produk --</option>';

            // Add new options
            items.forEach((item) => {
                const option = `<option value="${item.id}">${item.nama_item}</option>`;
                itemSelect.innerHTML += option;
                editItemSelect.innerHTML += option;
            });
        }
    } catch (error) {
        console.error("Error loading items:", error);
        showAlert("danger", "Gagal memuat daftar item");
    }
};

// Load data jenis
const loadJenis = async () => {
    try {
        const token = localStorage.getItem("api_token");
        const response = await axios.get("/api/admin/jenis", {
            headers: {
                Authorization: `Bearer ${token}`,
                Accept: "application/json",
            },
        });

        if (response.data.success) {
            const jenisList = response.data.data.data;
            const tbody = document.getElementById("jenis-tbody");

            if (jenisList.length === 0) {
                tbody.innerHTML =
                    '<tr><td colspan="5" class="text-center">Tidak ada data jenis</td></tr>';
                return;
            }

            let html = "";
            jenisList.forEach((jenis, index) => {
                html += `
                    <tr>
                        <td>${index + 1}</td>
                        <td>${
                            jenis.items && jenis.items.length > 0
                                ? jenis.items[0].nama_item
                                : "-"
                        }</td>
                        <td>${jenis.kategori}</td>
                        <td>Rp ${parseFloat(
                            jenis.biaya_tambahan
                        ).toLocaleString("id-ID")}</td>
                        <td>
                            <div class="action-buttons">
                                <button class="btn btn-sm btn-warning edit-jenis" data-id="${
                                    jenis.id
                                }" data-bs-toggle="modal" data-bs-target="#editJenisModal">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-danger delete-jenis" data-id="${
                                    jenis.id
                                }" data-name="${jenis.kategori}">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                `;
            });
            tbody.innerHTML = html;

            // Add event listeners
            document.querySelectorAll(".edit-jenis").forEach((button) => {
                button.addEventListener("click", handleEditJenis);
            });

            document.querySelectorAll(".delete-jenis").forEach((button) => {
                button.addEventListener("click", handleDeleteJenis);
            });
        }
    } catch (error) {
        console.error("Error loading jenis:", error);
        showAlert("danger", "Gagal memuat data jenis");
    }
};

// Handle add jenis
const handleAddJenis = async (event) => {
    event.preventDefault();

    try {
        const token = localStorage.getItem("api_token");
        const formData = new FormData(event.target);

        // Convert FormData to JSON
        const data = {
            item_id: formData.get("item_id"),
            kategori: formData.get("kategori"),
            biaya_tambahan: formData.get("biaya_tambahan"),
        };

        const response = await axios.post("/api/admin/jenis", data, {
            headers: {
                Authorization: `Bearer ${token}`,
                Accept: "application/json",
                "Content-Type": "application/json",
            },
        });

        if (response.data.success) {
            showAlert("success", "Jenis berhasil ditambahkan");
            event.target.reset();
            closeModal("addJenisModal");
            loadJenis();
        }
    } catch (error) {
        console.error("Error adding jenis:", error);
        showAlert(
            "danger",
            error.response?.data?.message || "Gagal menambahkan jenis"
        );
    }
};

// Handle edit jenis
const handleEditJenis = async (event) => {
    const jenisId = event.currentTarget.dataset.id;

    try {
        const token = localStorage.getItem("api_token");

        // Load items for dropdown first
        await loadItemsForJenisDropdown();

        const response = await axios.get(`/api/admin/jenis/${jenisId}`, {
            headers: {
                Authorization: `Bearer ${token}`,
                Accept: "application/json",
            },
        });

        if (response.data.success) {
            const jenis = response.data.data;

            // Fill form
            document.getElementById("edit_kategori").value = jenis.kategori;
            document.getElementById("edit_biaya_tambahan_jenis").value =
                jenis.biaya_tambahan;

            // Set item dropdown value
            const editItemSelect =
                document.getElementById("edit_item_id_jenis");
            if (editItemSelect && jenis.items && jenis.items.length > 0) {
                editItemSelect.value = jenis.items[0].id;
            }

            // Store jenis ID for update
            document.getElementById("editJenisForm").dataset.jenisId = jenisId;
        }
    } catch (error) {
        console.error("Error loading jenis:", error);
        showAlert("danger", "Gagal memuat data jenis");
    }
};

// Handle update jenis
const handleUpdateJenis = async (event) => {
    event.preventDefault();

    const jenisId = event.target.dataset.jenisId;

    try {
        const token = localStorage.getItem("api_token");
        const formData = new FormData(event.target);

        // Convert FormData to JSON
        const data = {
            item_id: formData.get("item_id"),
            kategori: formData.get("kategori"),
            biaya_tambahan: formData.get("biaya_tambahan"),
        };

        const response = await axios.put(`/api/admin/jenis/${jenisId}`, data, {
            headers: {
                Authorization: `Bearer ${token}`,
                Accept: "application/json",
                "Content-Type": "application/json",
            },
        });

        if (response.data.success) {
            showAlert("success", "Jenis berhasil diperbarui");
            closeModal("editJenisModal");
            loadJenis();
        }
    } catch (error) {
        console.error("Error updating jenis:", error);
        showAlert(
            "danger",
            error.response?.data?.message || "Gagal memperbarui jenis"
        );
    }
};

// Handle delete jenis
const handleDeleteJenis = async (event) => {
    const jenisId = event.currentTarget.dataset.id;
    const jenisName = event.currentTarget.dataset.name;

    if (confirm(`Apakah Anda yakin ingin menghapus jenis "${jenisName}"?`)) {
        try {
            const token = localStorage.getItem("api_token");
            const response = await axios.delete(`/api/admin/jenis/${jenisId}`, {
                headers: {
                    Authorization: `Bearer ${token}`,
                    Accept: "application/json",
                },
            });

            if (response.data.success) {
                showAlert("success", "Jenis berhasil dihapus");
                loadJenis();
            }
        } catch (error) {
            console.error("Error deleting jenis:", error);
            showAlert(
                "danger",
                error.response?.data?.message || "Gagal menghapus jenis"
            );
        }
    }
};

// ================================
// BIAYA DESAIN MANAGEMENT
// ================================

// Load data biaya desain
const loadBiayaDesain = async () => {
    try {
        const token = localStorage.getItem("api_token");
        const response = await axios.get("/api/admin/biaya-desain", {
            headers: {
                Authorization: `Bearer ${token}`,
                Accept: "application/json",
            },
        });

        if (response.data.success) {
            const biayaDesainList = response.data.data.data;
            const tbody = document.getElementById("biaya-desain-tbody");

            if (biayaDesainList.length === 0) {
                tbody.innerHTML =
                    '<tr><td colspan="4" class="text-center">Tidak ada data biaya desain</td></tr>';
                return;
            }

            let html = "";
            biayaDesainList.forEach((biaya, index) => {
                html += `
                    <tr>
                        <td>${index + 1}</td>
                        <td>Rp ${parseFloat(biaya.biaya).toLocaleString(
                            "id-ID"
                        )}</td>
                        <td>${biaya.deskripsi || "-"}</td>
                        <td>
                            <div class="action-buttons">
                                <button class="btn btn-sm btn-warning edit-biaya-desain" data-id="${
                                    biaya.id
                                }" data-bs-toggle="modal" data-bs-target="#editBiayaDesainModal">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-danger delete-biaya-desain" data-id="${
                                    biaya.id
                                }" data-name="${biaya.biaya}">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                `;
            });
            tbody.innerHTML = html;

            // Add event listeners
            document
                .querySelectorAll(".edit-biaya-desain")
                .forEach((button) => {
                    button.addEventListener("click", handleEditBiayaDesain);
                });

            document
                .querySelectorAll(".delete-biaya-desain")
                .forEach((button) => {
                    button.addEventListener("click", handleDeleteBiayaDesain);
                });
        }
    } catch (error) {
        console.error("Error loading biaya desain:", error);
        showAlert("danger", "Gagal memuat data biaya desain");
    }
};

// Handle add biaya desain
const handleAddBiayaDesain = async (event) => {
    event.preventDefault();

    try {
        const token = localStorage.getItem("api_token");
        const formData = new FormData(event.target);

        // Convert FormData to JSON
        const data = {
            biaya: formData.get("biaya"),
            deskripsi: formData.get("deskripsi"),
        };

        const response = await axios.post("/api/admin/biaya-desain", data, {
            headers: {
                Authorization: `Bearer ${token}`,
                Accept: "application/json",
                "Content-Type": "application/json",
            },
        });

        if (response.data.success) {
            showAlert("success", "Biaya desain berhasil ditambahkan");
            event.target.reset();
            closeModal("addBiayaDesainModal");
            loadBiayaDesain();
        }
    } catch (error) {
        console.error("Error adding biaya desain:", error);
        showAlert(
            "danger",
            error.response?.data?.message || "Gagal menambahkan biaya desain"
        );
    }
};

// Handle edit biaya desain
const handleEditBiayaDesain = async (event) => {
    const biayaDesainId = event.currentTarget.dataset.id;

    try {
        const token = localStorage.getItem("api_token");
        const response = await axios.get(
            `/api/admin/biaya-desain/${biayaDesainId}`,
            {
                headers: {
                    Authorization: `Bearer ${token}`,
                    Accept: "application/json",
                },
            }
        );

        if (response.data.success) {
            const biayaDesain = response.data.data;

            // Fill form
            document.getElementById("edit_biaya").value = biayaDesain.biaya;
            document.getElementById("edit_deskripsi_biaya").value =
                biayaDesain.deskripsi || "";

            // Store biaya desain ID for update
            document.getElementById(
                "editBiayaDesainForm"
            ).dataset.biayaDesainId = biayaDesainId;
        }
    } catch (error) {
        console.error("Error loading biaya desain:", error);
        showAlert("danger", "Gagal memuat data biaya desain");
    }
};

// Handle update biaya desain
const handleUpdateBiayaDesain = async (event) => {
    event.preventDefault();

    const biayaDesainId = event.target.dataset.biayaDesainId;

    try {
        const token = localStorage.getItem("api_token");
        const formData = new FormData(event.target);

        // Convert FormData to JSON
        const data = {
            biaya: formData.get("biaya"),
            deskripsi: formData.get("deskripsi"),
        };

        const response = await axios.put(
            `/api/admin/biaya-desain/${biayaDesainId}`,
            data,
            {
                headers: {
                    Authorization: `Bearer ${token}`,
                    Accept: "application/json",
                    "Content-Type": "application/json",
                },
            }
        );

        if (response.data.success) {
            showAlert("success", "Biaya desain berhasil diperbarui");
            closeModal("editBiayaDesainModal");
            loadBiayaDesain();
        }
    } catch (error) {
        console.error("Error updating biaya desain:", error);
        showAlert(
            "danger",
            error.response?.data?.message || "Gagal memperbarui biaya desain"
        );
    }
};

// Handle delete biaya desain
const handleDeleteBiayaDesain = async (event) => {
    const biayaDesainId = event.currentTarget.dataset.id;
    const biayaDesainName = event.currentTarget.dataset.name;

    if (
        confirm(
            `Apakah Anda yakin ingin menghapus biaya desain "Rp ${biayaDesainName}"?`
        )
    ) {
        try {
            const token = localStorage.getItem("api_token");
            const response = await axios.delete(
                `/api/admin/biaya-desain/${biayaDesainId}`,
                {
                    headers: {
                        Authorization: `Bearer ${token}`,
                        Accept: "application/json",
                    },
                }
            );

            if (response.data.success) {
                showAlert("success", "Biaya desain berhasil dihapus");
                loadBiayaDesain();
            }
        } catch (error) {
            console.error("Error deleting biaya desain:", error);
            showAlert(
                "danger",
                error.response?.data?.message || "Gagal menghapus biaya desain"
            );
        }
    }
};

// ================================
// INITIALIZE ALL PRODUCT MANAGEMENT
// ================================

const initProductManagement = () => {
    // Initialize items management
    loadItems();

    // Setup modal reset handlers
    const modalIds = [
        "addItemModal",
        "editItemModal",
        "addBahanModal",
        "editBahanModal",
        "addUkuranModal",
        "editUkuranModal",
        "addJenisModal",
        "editJenisModal",
        "addBiayaDesainModal",
        "editBiayaDesainModal",
    ];

    modalIds.forEach((modalId) => {
        resetModal(modalId);
    });

    // Add form submit handler for items
    const addItemForm = document.getElementById("addItemForm");
    if (addItemForm) {
        addItemForm.addEventListener("submit", handleAddItem);
    }

    // Edit form submit handler for items
    const editItemForm = document.getElementById("editItemForm");
    if (editItemForm) {
        editItemForm.addEventListener("submit", handleUpdateItem);
    }

    // Load data saat tab dibuka
    const bahanTab = document.getElementById("bahans-tab");
    if (bahanTab) {
        bahanTab.addEventListener("shown.bs.tab", () => {
            loadBahans();
            loadItemsForBahanDropdown();
        });
    }

    // Add form submit handler for bahan
    const addBahanForm = document.getElementById("addBahanForm");
    if (addBahanForm) {
        addBahanForm.addEventListener("submit", handleAddBahan);
    }

    // Edit form submit handler for bahan
    const editBahanForm = document.getElementById("editBahanForm");
    if (editBahanForm) {
        editBahanForm.addEventListener("submit", handleUpdateBahan);
    }

    // Load data saat tab dibuka
    const ukuranTab = document.getElementById("ukurans-tab");
    if (ukuranTab) {
        ukuranTab.addEventListener("shown.bs.tab", () => {
            loadUkurans();
            loadItemsForUkuranDropdown();
        });
    }

    // Add form submit handler for ukuran
    const addUkuranForm = document.getElementById("addUkuranForm");
    if (addUkuranForm) {
        addUkuranForm.addEventListener("submit", handleAddUkuran);
    }

    // Edit form submit handler for ukuran
    const editUkuranForm = document.getElementById("editUkuranForm");
    if (editUkuranForm) {
        editUkuranForm.addEventListener("submit", handleUpdateUkuran);
    }

    // Load data saat tab dibuka
    const jenisTab = document.getElementById("jenis-tab");
    if (jenisTab) {
        jenisTab.addEventListener("shown.bs.tab", () => {
            loadJenis();
            loadItemsForJenisDropdown();
        });
    }

    // Add form submit handler for jenis
    const addJenisForm = document.getElementById("addJenisForm");
    if (addJenisForm) {
        addJenisForm.addEventListener("submit", handleAddJenis);
    }

    // Edit form submit handler for jenis
    const editJenisForm = document.getElementById("editJenisForm");
    if (editJenisForm) {
        editJenisForm.addEventListener("submit", handleUpdateJenis);
    }

    // Load data saat tab dibuka
    const biayaDesainTab = document.getElementById("biaya-desain-tab");
    if (biayaDesainTab) {
        biayaDesainTab.addEventListener("shown.bs.tab", () => {
            loadBiayaDesain();
        });
    }

    // Add form submit handler for biaya desain
    const addBiayaDesainForm = document.getElementById("addBiayaDesainForm");
    if (addBiayaDesainForm) {
        addBiayaDesainForm.addEventListener("submit", handleAddBiayaDesain);
    }

    // Edit form submit handler for biaya desain
    const editBiayaDesainForm = document.getElementById("editBiayaDesainForm");
    if (editBiayaDesainForm) {
        editBiayaDesainForm.addEventListener("submit", handleUpdateBiayaDesain);
    }
};

// Initialize when document is ready
document.addEventListener("DOMContentLoaded", () => {
    console.log("Product management initialized");
    initProductManagement();
});
