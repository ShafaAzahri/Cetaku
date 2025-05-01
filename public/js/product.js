// Variabel global
const apiBaseUrl = "/api/admin";

// Fungsi untuk mendapatkan token dari localStorage
function getToken() {
    return localStorage.getItem("api_token");
}

// Fungsi untuk memformat angka
function formatNumber(number) {
    return new Intl.NumberFormat("id-ID").format(number || 0);
}

// Fungsi untuk menampilkan alert
function showAlert(message, type = "success") {
    const alertContainer = document.getElementById("alert-container");

    if (!alertContainer) {
        return;
    }

    const alert = document.createElement("div");
    alert.className = `alert alert-${type} alert-dismissible fade show`;
    alert.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    `;

    alertContainer.appendChild(alert);

    // Auto dismiss after 5 seconds
    setTimeout(() => {
        if (alert && alert.parentNode) {
            alert.classList.remove("show");
            setTimeout(() => {
                if (alert && alert.parentNode) {
                    alert.parentNode.removeChild(alert);
                }
            }, 150);
        }
    }, 5000);
}

// Fungsi untuk memuat data produk
function loadItems() {
    console.log("Loading items...");

    // Tampilkan loading
    const tbody = document.getElementById("items-tbody");
    if (tbody) {
        tbody.innerHTML =
            '<tr><td colspan="6" class="text-center">Loading...</td></tr>';
    }

    // Dapatkan token
    const token = getToken();
    if (!token) {
        showAlert(
            "Token autentikasi tidak ditemukan. Silakan login kembali.",
            "danger"
        );
        if (tbody) {
            tbody.innerHTML =
                '<tr><td colspan="6" class="text-center">Gagal memuat data: Token tidak ditemukan</td></tr>';
        }
        return;
    }

    // Log token untuk debugging (hilangkan pada produksi)
    console.log("Using token:", token);

    // Set header Axios
    axios.defaults.headers.common["Authorization"] = `Bearer ${token}`;

    // Lakukan request
    axios
        .get(`${apiBaseUrl}/items`)
        .then((response) => {
            console.log("Items response:", response);

            if (response.data.success) {
                const items = response.data.data.data || response.data.data;
                displayItems(items);
            } else {
                showAlert("Gagal memuat data produk.", "danger");
                if (tbody) {
                    tbody.innerHTML =
                        '<tr><td colspan="6" class="text-center">Gagal memuat data</td></tr>';
                }
            }
        })
        .catch((error) => {
            console.error("API Error:", error);

            // Cek jika error 401 Unauthorized
            if (error.response && error.response.status === 401) {
                showAlert(
                    "Sesi Anda telah berakhir. Silakan login kembali.",
                    "danger"
                );
                localStorage.removeItem("api_token"); // Hapus token yang tidak valid

                // Redirect ke login setelah 2 detik
                setTimeout(() => {
                    window.location.href = "/login";
                }, 2000);
            } else {
                showAlert(
                    "Gagal memuat data produk. Silakan coba lagi.",
                    "danger"
                );
            }

            if (tbody) {
                tbody.innerHTML =
                    '<tr><td colspan="6" class="text-center">Gagal memuat data</td></tr>';
            }
        });
}

// Fungsi untuk menampilkan data produk
function displayItems(items) {
    console.log("Displaying items:", items);

    const tbody = document.getElementById("items-tbody");

    if (!tbody) {
        console.error("Table body not found");
        return;
    }

    if (!items || items.length === 0) {
        tbody.innerHTML =
            '<tr><td colspan="6" class="text-center">Tidak ada data</td></tr>';
        return;
    }

    let html = "";

    items.forEach((item, index) => {
        const imageUrl = item.gambar
            ? `/storage/${item.gambar}`
            : "https://via.placeholder.com/100x100?text=No+Image";

        html += `
        <tr>
            <td>${index + 1}</td>
            <td><img src="${imageUrl}" alt="${
            item.nama_item
        }" width="100" class="img-thumbnail"></td>
            <td>${item.nama_item}</td>
            <td>${item.deskripsi || "-"}</td>
            <td>Rp ${formatNumber(item.harga_dasar)}</td>
            <td class="action-buttons">
                <button type="button" class="btn btn-sm btn-info edit-item" data-id="${
                    item.id
                }">
                    <i class="fas fa-edit"></i>
                </button>
                <button type="button" class="btn btn-sm btn-danger delete-item" data-id="${
                    item.id
                }">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        </tr>
        `;
    });

    tbody.innerHTML = html;

    // Setup edit dan delete buttons
    setupItemButtons();
}

// Setup buttons untuk edit dan delete
function setupItemButtons() {
    // Edit buttons
    document.querySelectorAll(".edit-item").forEach((button) => {
        button.addEventListener("click", function () {
            const id = this.getAttribute("data-id");
            editItem(id);
        });
    });

    // Delete buttons
    document.querySelectorAll(".delete-item").forEach((button) => {
        button.addEventListener("click", function () {
            const id = this.getAttribute("data-id");
            confirmDeleteItem(id);
        });
    });
}

// Fungsi untuk menampilkan konfirmasi hapus item
function confirmDeleteItem(id) {
    if (confirm("Anda yakin ingin menghapus produk ini?")) {
        deleteItem(id);
    }
}

// Fungsi untuk menghapus item
function deleteItem(id) {
    // Dapatkan token
    const token = getToken();
    if (!token) {
        showAlert(
            "Token autentikasi tidak ditemukan. Silakan login kembali.",
            "danger"
        );
        return;
    }

    // Set header Axios
    axios.defaults.headers.common["Authorization"] = `Bearer ${token}`;

    axios
        .delete(`${apiBaseUrl}/items/${id}`)
        .then((response) => {
            if (response.data.success) {
                showAlert("Produk berhasil dihapus!", "success");
                loadItems(); // Reload data
            } else {
                showAlert("Gagal menghapus produk.", "danger");
            }
        })
        .catch((error) => {
            console.error("API Error:", error);
            showAlert("Gagal menghapus produk. Silakan coba lagi.", "danger");
        });
}

// Fungsi untuk mengambil data item untuk diedit
function editItem(id) {
    // Dapatkan token
    const token = getToken();
    if (!token) {
        showAlert(
            "Token autentikasi tidak ditemukan. Silakan login kembali.",
            "danger"
        );
        return;
    }

    // Set header Axios
    axios.defaults.headers.common["Authorization"] = `Bearer ${token}`;

    axios
        .get(`${apiBaseUrl}/items/${id}`)
        .then((response) => {
            if (response.data.success) {
                const item = response.data.data;

                // Set modal data-item-id
                document
                    .getElementById("editItemModal")
                    .setAttribute("data-item-id", item.id);

                // Fill form
                document.getElementById("edit_nama_item").value =
                    item.nama_item;
                document.getElementById("edit_deskripsi").value =
                    item.deskripsi || "";
                document.getElementById("edit_harga_dasar").value =
                    item.harga_dasar;

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

                // Show modal
                const editItemModal = new bootstrap.Modal(
                    document.getElementById("editItemModal")
                );
                editItemModal.show();
            } else {
                showAlert("Gagal mengambil data produk.", "danger");
            }
        })
        .catch((error) => {
            console.error("API Error:", error);
            showAlert(
                "Gagal mengambil data produk. Silakan coba lagi.",
                "danger"
            );
        });
}

// Setup form submit handlers
function setupFormHandlers() {
    // Add Item Form
    const addItemForm = document.getElementById("addItemForm");
    if (addItemForm) {
        addItemForm.addEventListener("submit", function (e) {
            e.preventDefault();

            // Dapatkan token
            const token = getToken();
            if (!token) {
                showAlert(
                    "Token autentikasi tidak ditemukan. Silakan login kembali.",
                    "danger"
                );
                return;
            }

            // Buat FormData
            const formData = new FormData(this);

            // Set header Axios untuk multipart/form-data
            axios.defaults.headers.common["Authorization"] = `Bearer ${token}`;

            // Kirim request
            axios
                .post(`${apiBaseUrl}/items`, formData)
                .then((response) => {
                    if (response.data.success) {
                        showAlert("Produk berhasil ditambahkan!", "success");
                        this.reset();
                        const modal = bootstrap.Modal.getInstance(
                            document.getElementById("addItemModal")
                        );
                        if (modal) {
                            modal.hide();
                        }
                        loadItems(); // Reload data
                    } else {
                        showAlert("Gagal menambahkan produk.", "danger");
                    }
                })
                .catch((error) => {
                    console.error("API Error:", error);

                    if (
                        error.response &&
                        error.response.data &&
                        error.response.data.errors
                    ) {
                        const errors = error.response.data.errors;
                        let errorMessage = "Terjadi kesalahan:<br>";

                        for (const field in errors) {
                            errorMessage += `- ${errors[field].join(
                                "<br>- "
                            )}<br>`;
                        }

                        showAlert(errorMessage, "danger");
                    } else {
                        showAlert(
                            "Gagal menambahkan produk. Silakan coba lagi.",
                            "danger"
                        );
                    }
                });
        });
    }

    // Edit Item Form
    const editItemForm = document.getElementById("editItemForm");
    if (editItemForm) {
        editItemForm.addEventListener("submit", function (e) {
            e.preventDefault();

            // Dapatkan token
            const token = getToken();
            if (!token) {
                showAlert(
                    "Token autentikasi tidak ditemukan. Silakan login kembali.",
                    "danger"
                );
                return;
            }

            // Dapatkan ID item
            const itemId = document
                .getElementById("editItemModal")
                .getAttribute("data-item-id");
            if (!itemId) {
                showAlert("ID produk tidak ditemukan.", "danger");
                return;
            }

            // Buat FormData
            const formData = new FormData(this);
            formData.append("_method", "PUT"); // Laravel method spoofing

            // Set header Axios untuk multipart/form-data
            axios.defaults.headers.common["Authorization"] = `Bearer ${token}`;

            // Kirim request
            axios
                .post(`${apiBaseUrl}/items/${itemId}`, formData)
                .then((response) => {
                    if (response.data.success) {
                        showAlert("Produk berhasil diperbarui!", "success");
                        const modal = bootstrap.Modal.getInstance(
                            document.getElementById("editItemModal")
                        );
                        if (modal) {
                            modal.hide();
                        }
                        loadItems(); // Reload data
                    } else {
                        showAlert("Gagal memperbarui produk.", "danger");
                    }
                })
                .catch((error) => {
                    console.error("API Error:", error);

                    if (
                        error.response &&
                        error.response.data &&
                        error.response.data.errors
                    ) {
                        const errors = error.response.data.errors;
                        let errorMessage = "Terjadi kesalahan:<br>";

                        for (const field in errors) {
                            errorMessage += `- ${errors[field].join(
                                "<br>- "
                            )}<br>`;
                        }

                        showAlert(errorMessage, "danger");
                    } else {
                        showAlert(
                            "Gagal memperbarui produk. Silakan coba lagi.",
                            "danger"
                        );
                    }
                });
        });
    }
}

// Fungsi utama yang dipanggil ketika halaman dimuat
document.addEventListener("DOMContentLoaded", function () {
    console.log("DOM content loaded for product.js");

    // Setup form handlers
    setupFormHandlers();

    // Load data
    loadItems();
});
