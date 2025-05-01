/**
 * Product Manager JavaScript - Handles API communication for product management
 *
 * This file provides functionality for all product management operations including:
 * - Items (Products)
 * - Bahans (Materials)
 * - Ukurans (Sizes)
 * - Jenis (Categories)
 * - Biaya Desain (Design Costs)
 */

// Set default AJAX settings
$.ajaxSetup({
    headers: {
        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
    },
});

/**
 * API Service - Handles API communication
 */
const ApiService = {
    /**
     * Get API token from local storage
     * @returns {string} API token
     */
    getToken: function () {
        return localStorage.getItem("api_token");
    },

    /**
     * Get default headers for API requests
     * @returns {Object} Headers object
     */
    getHeaders: function () {
        return {
            Authorization: "Bearer " + this.getToken(),
        };
    },

    /**
     * Make a GET request to the API
     * @param {string} url - API endpoint
     * @param {Object} params - URL parameters
     * @param {Function} successCallback - Success callback
     * @param {Function} errorCallback - Error callback
     */
    get: function (url, params, successCallback, errorCallback) {
        $.ajax({
            url: url,
            type: "GET",
            data: params,
            headers: this.getHeaders(),
            success: successCallback,
            error:
                errorCallback ||
                function (xhr) {
                    handleApiError(xhr);
                },
        });
    },

    /**
     * Make a POST request to the API
     * @param {string} url - API endpoint
     * @param {Object|FormData} data - Request data
     * @param {Function} successCallback - Success callback
     * @param {Function} errorCallback - Error callback
     * @param {boolean} isFormData - Whether data is FormData
     */
    post: function (
        url,
        data,
        successCallback,
        errorCallback,
        isFormData = false
    ) {
        const ajaxOptions = {
            url: url,
            type: "POST",
            headers: this.getHeaders(),
            success: successCallback,
            error:
                errorCallback ||
                function (xhr) {
                    handleApiError(xhr);
                },
        };

        if (isFormData) {
            ajaxOptions.data = data;
            ajaxOptions.contentType = false;
            ajaxOptions.processData = false;
        } else {
            ajaxOptions.data = JSON.stringify(data);
            ajaxOptions.contentType = "application/json";
        }

        $.ajax(ajaxOptions);
    },

    /**
     * Make a PUT request to the API
     * @param {string} url - API endpoint
     * @param {Object} data - Request data
     * @param {Function} successCallback - Success callback
     * @param {Function} errorCallback - Error callback
     */
    put: function (url, data, successCallback, errorCallback) {
        $.ajax({
            url: url,
            type: "PUT",
            data: JSON.stringify(data),
            contentType: "application/json",
            headers: this.getHeaders(),
            success: successCallback,
            error:
                errorCallback ||
                function (xhr) {
                    handleApiError(xhr);
                },
        });
    },

    /**
     * Make a DELETE request to the API
     * @param {string} url - API endpoint
     * @param {Function} successCallback - Success callback
     * @param {Function} errorCallback - Error callback
     */
    delete: function (url, successCallback, errorCallback) {
        $.ajax({
            url: url,
            type: "DELETE",
            headers: this.getHeaders(),
            success: successCallback,
            error:
                errorCallback ||
                function (xhr) {
                    handleApiError(xhr);
                },
        });
    },
};

/**
 * Item Manager - Handles item (product) operations
 */
const ItemManager = {
    /**
     * Load items from API
     */
    loadItems: function () {
        ApiService.get(
            "/api/admin/items",
            {},
            function (response) {
                if (response.success) {
                    const items = response.data.data;

                    // Clear existing items
                    $("#items tbody").empty();

                    if (items.length === 0) {
                        $("#items tbody").append(
                            '<tr><td colspan="6" class="text-center">Tidak ada produk</td></tr>'
                        );
                        return;
                    }

                    // Add items to table
                    $.each(items, function (index, item) {
                        const imageHtml = item.gambar
                            ? `<img src="${baseUrl}/storage/${item.gambar}" alt="${item.nama_item}" class="img-thumbnail" style="max-height: 50px;">`
                            : '<span class="text-muted">Tidak ada gambar</span>';

                        const row = `
                        <tr>
                            <td>${index + 1}</td>
                            <td>${imageHtml}</td>
                            <td>${item.nama_item}</td>
                            <td>${
                                item.deskripsi
                                    ? item.deskripsi.substring(0, 50) +
                                      (item.deskripsi.length > 50 ? "..." : "")
                                    : ""
                            }</td>
                            <td>Rp ${formatCurrency(item.harga_dasar)}</td>
                            <td>
                                <div class="item-actions d-flex gap-2">
                                    <button type="button" class="btn btn-sm btn-info edit-item-btn" 
                                            data-id="${item.id}" 
                                            data-nama="${item.nama_item}"
                                            data-deskripsi="${
                                                item.deskripsi || ""
                                            }"
                                            data-harga="${item.harga_dasar}"
                                            data-gambar="${
                                                item.gambar
                                                    ? baseUrl +
                                                      "/storage/" +
                                                      item.gambar
                                                    : ""
                                            }">
                                        <i class="fas fa-edit"></i> Edit
                                    </button>
                                    <button type="button" class="btn btn-sm btn-danger delete-item-btn" data-id="${
                                        item.id
                                    }">
                                        <i class="fas fa-trash"></i> Hapus
                                    </button>
                                </div>
                            </td>
                        </tr>
                    `;
                        $("#items tbody").append(row);
                    });

                    // Bind event handlers
                    this.bindEvents();
                } else {
                    showAlert(
                        "danger",
                        "Error loading items: " + response.message
                    );
                }
            }.bind(this)
        );
    },

    /**
     * Bind event handlers
     */
    bindEvents: function () {
        // Edit button click
        $(".edit-item-btn").on("click", function () {
            const id = $(this).data("id");
            const nama = $(this).data("nama");
            const deskripsi = $(this).data("deskripsi");
            const harga = $(this).data("harga");
            const gambar = $(this).data("gambar");

            // Reset form
            $("#editItemForm")[0].reset();

            // Set form action
            $("#editItemForm").data("id", id);

            // Fill form fields
            $("#edit_nama_item").val(nama);
            $("#edit_deskripsi").val(deskripsi);
            $("#edit_harga_dasar").val(harga);

            // Show current image if exists
            if (gambar) {
                $("#current_image").attr("src", gambar);
                $("#current_image_container").show();
            } else {
                $("#current_image_container").hide();
            }

            // Show modal
            $("#editItemModal").modal("show");
        });

        // Delete button click
        $(".delete-item-btn").on("click", function () {
            const id = $(this).data("id");

            if (confirm("Apakah Anda yakin ingin menghapus produk ini?")) {
                ApiService.delete(
                    "/api/admin/items/" + id,
                    function (response) {
                        if (response.success) {
                            showAlert("success", "Produk berhasil dihapus!");
                            ItemManager.loadItems();
                        } else {
                            showAlert("danger", "Error: " + response.message);
                        }
                    }
                );
            }
        });
    },

    /**
     * Initialize item manager
     */
    init: function () {
        // Load items
        this.loadItems();

        // Add item form submit
        $("#addItemModal form").on("submit", function (e) {
            e.preventDefault();

            const formData = new FormData(this);

            ApiService.post(
                "/api/admin/items",
                formData,
                function (response) {
                    if (response.success) {
                        $("#addItemModal").modal("hide");
                        showAlert("success", "Produk berhasil ditambahkan!");
                        ItemManager.loadItems();
                        $(this)[0].reset();
                    } else {
                        showAlert("danger", "Error: " + response.message);
                    }
                }.bind(this),
                null,
                true
            );
        });

        // Edit item form submit
        $("#editItemForm").on("submit", function (e) {
            e.preventDefault();

            const id = $(this).data("id");
            const formData = new FormData(this);
            formData.append("_method", "PUT");

            ApiService.post(
                "/api/admin/items/" + id,
                formData,
                function (response) {
                    if (response.success) {
                        $("#editItemModal").modal("hide");
                        showAlert("success", "Produk berhasil diperbarui!");
                        ItemManager.loadItems();
                    } else {
                        showAlert("danger", "Error: " + response.message);
                    }
                },
                null,
                true
            );
        });

        // Load items data
        this.loadSelectOptions();
    },

    /**
     * Load item options for select elements
     */
    loadSelectOptions: function () {
        ApiService.get("/api/admin/items/all", {}, function (response) {
            if (response.success) {
                const items = response.data;

                // Clear existing options
                $('select[name="item_id"]').empty();
                $('select[name="item_id"]').append(
                    '<option value="">-- Pilih Item Produk --</option>'
                );

                // Add new options
                $.each(items, function (index, item) {
                    $('select[name="item_id"]').append(
                        `<option value="${item.id}">${item.nama_item}</option>`
                    );
                });
            }
        });
    },
};

/**
 * Bahan Manager - Handles material operations
 */
const BahanManager = {
    /**
     * Load materials from API
     */
    loadBahans: function () {
        ApiService.get(
            "/api/admin/bahans",
            {},
            function (response) {
                if (response.success) {
                    const bahans = response.data.data;

                    // Clear existing bahans
                    $("#bahans tbody").empty();

                    if (bahans.length === 0) {
                        $("#bahans tbody").append(
                            '<tr><td colspan="5" class="text-center">Tidak ada bahan</td></tr>'
                        );
                        return;
                    }

                    // Add bahans to table
                    $.each(bahans, function (index, bahan) {
                        const itemName =
                            bahan.items && bahan.items.length > 0
                                ? bahan.items[0].nama_item
                                : '<span class="text-muted">Tidak terkait</span>';

                        const row = `
                        <tr>
                            <td>${index + 1}</td>
                            <td>${itemName}</td>
                            <td>${bahan.nama_bahan}</td>
                            <td>Rp ${formatCurrency(bahan.biaya_tambahan)}</td>
                            <td>
                                <div class="bahan-actions d-flex gap-2">
                                    <button type="button" class="btn btn-sm btn-info edit-bahan-btn" 
                                            data-id="${bahan.id}" 
                                            data-nama="${bahan.nama_bahan}"
                                            data-biaya="${bahan.biaya_tambahan}"
                                            data-item="${
                                                bahan.items &&
                                                bahan.items.length > 0
                                                    ? bahan.items[0].id
                                                    : ""
                                            }">
                                        <i class="fas fa-edit"></i> Edit
                                    </button>
                                    <button type="button" class="btn btn-sm btn-danger delete-bahan-btn" data-id="${
                                        bahan.id
                                    }">
                                        <i class="fas fa-trash"></i> Hapus
                                    </button>
                                </div>
                            </td>
                        </tr>
                    `;
                        $("#bahans tbody").append(row);
                    });

                    // Bind event handlers
                    this.bindEvents();
                } else {
                    showAlert(
                        "danger",
                        "Error loading materials: " + response.message
                    );
                }
            }.bind(this)
        );
    },

    /**
     * Bind event handlers
     */
    bindEvents: function () {
        // Edit button click
        $(".edit-bahan-btn").on("click", function () {
            const id = $(this).data("id");
            const nama = $(this).data("nama");
            const biaya = $(this).data("biaya");
            const item = $(this).data("item");

            // Reset form
            $("#editBahanForm")[0].reset();

            // Set form action
            $("#editBahanForm").data("id", id);

            // Fill form fields
            $("#edit_nama_bahan").val(nama);
            $("#edit_biaya_tambahan").val(biaya);
            $("#edit_item_id").val(item);

            // Show modal
            $("#editBahanModal").modal("show");
        });

        // Delete button click
        $(".delete-bahan-btn").on("click", function () {
            const id = $(this).data("id");

            if (confirm("Apakah Anda yakin ingin menghapus bahan ini?")) {
                ApiService.delete(
                    "/api/admin/bahans/" + id,
                    function (response) {
                        if (response.success) {
                            showAlert("success", "Bahan berhasil dihapus!");
                            BahanManager.loadBahans();
                        } else {
                            showAlert("danger", "Error: " + response.message);
                        }
                    }
                );
            }
        });
    },

    /**
     * Initialize bahan manager
     */
    init: function () {
        // Load bahans
        this.loadBahans();

        // Add bahan form submit
        $("#addBahanModal form").on("submit", function (e) {
            e.preventDefault();

            const formData = new FormData(this);
            const data = formDataToObject(formData);

            ApiService.post(
                "/api/admin/bahans",
                data,
                function (response) {
                    if (response.success) {
                        $("#addBahanModal").modal("hide");
                        showAlert("success", "Bahan berhasil ditambahkan!");
                        BahanManager.loadBahans();
                        $(this)[0].reset();
                    } else {
                        showAlert("danger", "Error: " + response.message);
                    }
                }.bind(this)
            );
        });

        // Edit bahan form submit
        $("#editBahanForm").on("submit", function (e) {
            e.preventDefault();

            const id = $(this).data("id");
            const formData = new FormData(this);
            const data = formDataToObject(formData);

            ApiService.put(
                "/api/admin/bahans/" + id,
                data,
                function (response) {
                    if (response.success) {
                        $("#editBahanModal").modal("hide");
                        showAlert("success", "Bahan berhasil diperbarui!");
                        BahanManager.loadBahans();
                    } else {
                        showAlert("danger", "Error: " + response.message);
                    }
                }
            );
        });
    },
};

/**
 * Ukuran Manager - Handles size operations
 */
const UkuranManager = {
    /**
     * Load sizes from API
     */
    loadUkurans: function () {
        ApiService.get(
            "/api/admin/ukurans",
            {},
            function (response) {
                if (response.success) {
                    const ukurans = response.data.data;

                    // Clear existing ukurans
                    $("#ukurans tbody").empty();

                    if (ukurans.length === 0) {
                        $("#ukurans tbody").append(
                            '<tr><td colspan="5" class="text-center">Tidak ada ukuran</td></tr>'
                        );
                        return;
                    }

                    // Add ukurans to table
                    $.each(ukurans, function (index, ukuran) {
                        const itemName =
                            ukuran.items && ukuran.items.length > 0
                                ? ukuran.items[0].nama_item
                                : '<span class="text-muted">Tidak terkait</span>';

                        const row = `
                        <tr>
                            <td>${index + 1}</td>
                            <td>${itemName}</td>
                            <td>${ukuran.size}</td>
                            <td>x${ukuran.faktor_harga}</td>
                            <td>
                                <div class="ukuran-actions d-flex gap-2">
                                    <button type="button" class="btn btn-sm btn-info edit-ukuran-btn" 
                                            data-id="${ukuran.id}" 
                                            data-size="${ukuran.size}"
                                            data-faktor="${ukuran.faktor_harga}"
                                            data-item="${
                                                ukuran.items &&
                                                ukuran.items.length > 0
                                                    ? ukuran.items[0].id
                                                    : ""
                                            }">
                                        <i class="fas fa-edit"></i> Edit
                                    </button>
                                    <button type="button" class="btn btn-sm btn-danger delete-ukuran-btn" data-id="${
                                        ukuran.id
                                    }">
                                        <i class="fas fa-trash"></i> Hapus
                                    </button>
                                </div>
                            </td>
                        </tr>
                    `;
                        $("#ukurans tbody").append(row);
                    });

                    // Bind event handlers
                    this.bindEvents();
                } else {
                    showAlert(
                        "danger",
                        "Error loading sizes: " + response.message
                    );
                }
            }.bind(this)
        );
    },

    /**
     * Bind event handlers
     */
    bindEvents: function () {
        // Edit button click
        $(".edit-ukuran-btn").on("click", function () {
            const id = $(this).data("id");
            const size = $(this).data("size");
            const faktor = $(this).data("faktor");
            const item = $(this).data("item");

            // Reset form
            $("#editUkuranForm")[0].reset();

            // Set form action
            $("#editUkuranForm").data("id", id);

            // Fill form fields
            $("#edit_size").val(size);
            $("#edit_faktor_harga").val(faktor);
            $("#edit_item_id_ukuran").val(item);

            // Show modal
            $("#editUkuranModal").modal("show");
        });

        // Delete button click
        $(".delete-ukuran-btn").on("click", function () {
            const id = $(this).data("id");

            if (confirm("Apakah Anda yakin ingin menghapus ukuran ini?")) {
                ApiService.delete(
                    "/api/admin/ukurans/" + id,
                    function (response) {
                        if (response.success) {
                            showAlert("success", "Ukuran berhasil dihapus!");
                            UkuranManager.loadUkurans();
                        } else {
                            showAlert("danger", "Error: " + response.message);
                        }
                    }
                );
            }
        });
    },

    /**
     * Initialize ukuran manager
     */
    init: function () {
        // Load ukurans
        this.loadUkurans();

        // Add ukuran form submit
        $("#addUkuranModal form").on("submit", function (e) {
            e.preventDefault();

            const formData = new FormData(this);
            const data = formDataToObject(formData);

            ApiService.post(
                "/api/admin/ukurans",
                data,
                function (response) {
                    if (response.success) {
                        $("#addUkuranModal").modal("hide");
                        showAlert("success", "Ukuran berhasil ditambahkan!");
                        UkuranManager.loadUkurans();
                        $(this)[0].reset();
                    } else {
                        showAlert("danger", "Error: " + response.message);
                    }
                }.bind(this)
            );
        });

        // Edit ukuran form submit
        $("#editUkuranForm").on("submit", function (e) {
            e.preventDefault();

            const id = $(this).data("id");
            const formData = new FormData(this);
            const data = formDataToObject(formData);

            ApiService.put(
                "/api/admin/ukurans/" + id,
                data,
                function (response) {
                    if (response.success) {
                        $("#editUkuranModal").modal("hide");
                        showAlert("success", "Ukuran berhasil diperbarui!");
                        UkuranManager.loadUkurans();
                    } else {
                        showAlert("danger", "Error: " + response.message);
                    }
                }
            );
        });
    },
};

/**
 * Jenis Manager - Handles category operations
 */
const JenisManager = {
    /**
     * Load categories from API
     */
    loadJenis: function () {
        ApiService.get(
            "/api/admin/jenis",
            {},
            function (response) {
                if (response.success) {
                    const jenisList = response.data.data;

                    // Clear existing jenis
                    $("#jenis tbody").empty();

                    if (jenisList.length === 0) {
                        $("#jenis tbody").append(
                            '<tr><td colspan="5" class="text-center">Tidak ada jenis</td></tr>'
                        );
                        return;
                    }

                    // Add jenis to table
                    $.each(jenisList, function (index, jenis) {
                        const itemName =
                            jenis.items && jenis.items.length > 0
                                ? jenis.items[0].nama_item
                                : '<span class="text-muted">Tidak terkait</span>';

                        const row = `
                        <tr>
                            <td>${index + 1}</td>
                            <td>${itemName}</td>
                            <td>${jenis.kategori}</td>
                            <td>Rp ${formatCurrency(jenis.biaya_tambahan)}</td>
                            <td>
                                <div class="jenis-actions d-flex gap-2">
                                    <button type="button" class="btn btn-sm btn-info edit-jenis-btn" 
                                            data-id="${jenis.id}" 
                                            data-kategori="${jenis.kategori}"
                                            data-biaya="${jenis.biaya_tambahan}"
                                            data-item="${
                                                jenis.items &&
                                                jenis.items.length > 0
                                                    ? jenis.items[0].id
                                                    : ""
                                            }">
                                        <i class="fas fa-edit"></i> Edit
                                    </button>
                                    <button type="button" class="btn btn-sm btn-danger delete-jenis-btn" data-id="${
                                        jenis.id
                                    }">
                                        <i class="fas fa-trash"></i> Hapus
                                    </button>
                                </div>
                            </td>
                        </tr>
                    `;
                        $("#jenis tbody").append(row);
                    });

                    // Bind event handlers
                    this.bindEvents();
                } else {
                    showAlert(
                        "danger",
                        "Error loading categories: " + response.message
                    );
                }
            }.bind(this)
        );
    },

    /**
     * Bind event handlers
     */
    bindEvents: function () {
        // Edit button click
        $(".edit-jenis-btn").on("click", function () {
            const id = $(this).data("id");
            const kategori = $(this).data("kategori");
            const biaya = $(this).data("biaya");
            const item = $(this).data("item");

            // Reset form
            $("#editJenisForm")[0].reset();

            // Set form action
            $("#editJenisForm").data("id", id);

            // Fill form fields
            $("#edit_kategori").val(kategori);
            $("#edit_biaya_tambahan_jenis").val(biaya);
            $("#edit_item_id_jenis").val(item);

            // Show modal
            $("#editJenisModal").modal("show");
        });

        // Delete button click
        $(".delete-jenis-btn").on("click", function () {
            const id = $(this).data("id");

            if (confirm("Apakah Anda yakin ingin menghapus jenis ini?")) {
                ApiService.delete(
                    "/api/admin/jenis/" + id,
                    function (response) {
                        if (response.success) {
                            showAlert("success", "Jenis berhasil dihapus!");
                            JenisManager.loadJenis();
                        } else {
                            showAlert("danger", "Error: " + response.message);
                        }
                    }
                );
            }
        });
    },

    /**
     * Initialize jenis manager
     */
    init: function () {
        // Load jenis
        this.loadJenis();

        // Add jenis form submit
        $("#addJenisModal form").on("submit", function (e) {
            e.preventDefault();

            const formData = new FormData(this);
            const data = formDataToObject(formData);

            ApiService.post(
                "/api/admin/jenis",
                data,
                function (response) {
                    if (response.success) {
                        $("#addJenisModal").modal("hide");
                        showAlert("success", "Jenis berhasil ditambahkan!");
                        JenisManager.loadJenis();
                        $(this)[0].reset();
                    } else {
                        showAlert("danger", "Error: " + response.message);
                    }
                }.bind(this)
            );
        });

        // Edit jenis form submit
        $("#editJenisForm").on("submit", function (e) {
            e.preventDefault();

            const id = $(this).data("id");
            const formData = new FormData(this);
            const data = formDataToObject(formData);

            ApiService.put("/api/admin/jenis/" + id, data, function (response) {
                if (response.success) {
                    $("#editJenisModal").modal("hide");
                    showAlert("success", "Jenis berhasil diperbarui!");
                    JenisManager.loadJenis();
                } else {
                    showAlert("danger", "Error: " + response.message);
                }
            });
        });
    },
};

/**
 * Biaya Desain Manager - Handles design cost operations
 */
const BiayaDesainManager = {
    /**
     * Load design costs from API
     */
    loadBiayaDesain: function () {
        ApiService.get(
            "/api/admin/biaya-desain",
            {},
            function (response) {
                if (response.success) {
                    const biayaDesains = response.data.data;

                    // Clear existing biaya desain
                    $("#biaya-desain tbody").empty();

                    if (biayaDesains.length === 0) {
                        $("#biaya-desain tbody").append(
                            '<tr><td colspan="4" class="text-center">Tidak ada biaya desain</td></tr>'
                        );
                        return;
                    }

                    // Add biaya desain to table
                    $.each(biayaDesains, function (index, biayaDesain) {
                        const row = `
                        <tr>
                            <td>${index + 1}</td>
                            <td>Rp ${formatCurrency(biayaDesain.biaya)}</td>
                            <td>${
                                biayaDesain.deskripsi
                                    ? biayaDesain.deskripsi.substring(0, 50) +
                                      (biayaDesain.deskripsi.length > 50
                                          ? "..."
                                          : "")
                                    : ""
                            }</td>
                            <td>
                                <div class="desain-actions d-flex gap-2">
                                    <button type="button" class="btn btn-sm btn-info edit-biaya-desain-btn" 
                                            data-id="${biayaDesain.id}" 
                                            data-deskripsi="${
                                                biayaDesain.deskripsi || ""
                                            }"
                                            data-biaya="${biayaDesain.biaya}">
                                        <i class="fas fa-edit"></i> Edit
                                    </button>
                                    <button type="button" class="btn btn-sm btn-danger delete-biaya-desain-btn" data-id="${
                                        biayaDesain.id
                                    }">
                                        <i class="fas fa-trash"></i> Hapus
                                    </button>
                                </div>
                            </td>
                        </tr>
                    `;
                        $("#biaya-desain tbody").append(row);
                    });

                    // Bind event handlers
                    this.bindEvents();
                } else {
                    showAlert(
                        "danger",
                        "Error loading design costs: " + response.message
                    );
                }
            }.bind(this)
        );
    },

    /**
     * Bind event handlers
     */
    bindEvents: function () {
        // Edit button click
        $(".edit-biaya-desain-btn").on("click", function () {
            const id = $(this).data("id");
            const deskripsi = $(this).data("deskripsi");
            const biaya = $(this).data("biaya");

            // Reset form
            $("#editBiayaDesainForm")[0].reset();

            // Set form action
            $("#editBiayaDesainForm").data("id", id);

            // Fill form fields
            $("#edit_deskripsi_biaya").val(deskripsi);
            $("#edit_biaya").val(biaya);

            // Show modal
            $("#editBiayaDesainModal").modal("show");
        });

        // Delete button click
        $(".delete-biaya-desain-btn").on("click", function () {
            const id = $(this).data("id");

            if (
                confirm("Apakah Anda yakin ingin menghapus biaya desain ini?")
            ) {
                ApiService.delete(
                    "/api/admin/biaya-desain/" + id,
                    function (response) {
                        if (response.success) {
                            showAlert(
                                "success",
                                "Biaya desain berhasil dihapus!"
                            );
                            BiayaDesainManager.loadBiayaDesain();
                        } else {
                            showAlert("danger", "Error: " + response.message);
                        }
                    }
                );
            }
        });
    },

    /**
     * Initialize biaya desain manager
     */
    init: function () {
        // Load biaya desain
        this.loadBiayaDesain();

        // Add biaya desain form submit
        $("#addBiayaDesainModal form").on("submit", function (e) {
            e.preventDefault();

            const formData = new FormData(this);
            const data = formDataToObject(formData);

            ApiService.post(
                "/api/admin/biaya-desain",
                data,
                function (response) {
                    if (response.success) {
                        $("#addBiayaDesainModal").modal("hide");
                        showAlert(
                            "success",
                            "Biaya desain berhasil ditambahkan!"
                        );
                        BiayaDesainManager.loadBiayaDesain();
                        $(this)[0].reset();
                    } else {
                        showAlert("danger", "Error: " + response.message);
                    }
                }.bind(this)
            );
        });

        // Edit biaya desain form submit
        $("#editBiayaDesainForm").on("submit", function (e) {
            e.preventDefault();

            const id = $(this).data("id");
            const formData = new FormData(this);
            const data = formDataToObject(formData);

            ApiService.put(
                "/api/admin/biaya-desain/" + id,
                data,
                function (response) {
                    if (response.success) {
                        $("#editBiayaDesainModal").modal("hide");
                        showAlert(
                            "success",
                            "Biaya desain berhasil diperbarui!"
                        );
                        BiayaDesainManager.loadBiayaDesain();
                    } else {
                        showAlert("danger", "Error: " + response.message);
                    }
                }
            );
        });
    },
};

/**
 * Utility Functions
 */

/**
 * Format currency with thousand separator
 * @param {number} amount - Amount to format
 * @returns {string} Formatted currency
 */
function formatCurrency(amount) {
    return new Intl.NumberFormat("id-ID").format(amount);
}

/**
 * Convert FormData to object
 * @param {FormData} formData - Form data
 * @returns {Object} Object with form data
 */
function formDataToObject(formData) {
    const data = {};
    formData.forEach((value, key) => {
        data[key] = value;
    });
    return data;
}

/**
 * Show alert message
 * @param {string} type - Alert type (success, danger, warning, info)
 * @param {string} message - Alert message
 */
function showAlert(type, message) {
    const alertHtml = `
        <div class="alert alert-${type} alert-dismissible fade show" role="alert">
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    `;

    // Remove existing alerts
    $(".alert").alert("close");

    // Add new alert
    $(".container-fluid").prepend(alertHtml);

    // Auto close alert after 5 seconds
    setTimeout(function () {
        $(".alert").alert("close");
    }, 5000);
}

/**
 * Handle API errors
 * @param {object} xhr - XHR object
 */
function handleApiError(xhr) {
    console.error("API Error:", xhr);

    let errorMessage = "Terjadi kesalahan pada server.";

    if (xhr.responseJSON) {
        if (xhr.responseJSON.message) {
            errorMessage = xhr.responseJSON.message;
        }

        if (xhr.responseJSON.errors) {
            errorMessage = '<ul class="mb-0">';

            for (const field in xhr.responseJSON.errors) {
                xhr.responseJSON.errors[field].forEach((message) => {
                    errorMessage += `<li>${message}</li>`;
                });
            }

            errorMessage += "</ul>";
        }
    }

    // Show error alert
    showAlert("danger", errorMessage);

    // If unauthorized, redirect to login
    if (xhr.status === 401) {
        setTimeout(function () {
            window.location.href = "/login";
        }, 2000);
    }
}

/**
 * Initialize all managers when page is ready
 */
$(document).ready(function () {
    // Set base URL for assets if not defined
    if (typeof baseUrl === "undefined") {
        window.baseUrl = window.location.origin;
    }

    // Initialize managers based on active tab
    function initActiveTab() {
        const activeTab = $("#productManagerTabs .nav-link.active").attr("id");

        if (activeTab === "items-tab") {
            ItemManager.init();
        } else if (activeTab === "bahans-tab") {
            BahanManager.init();
        } else if (activeTab === "ukurans-tab") {
            UkuranManager.init();
        } else if (activeTab === "jenis-tab") {
            JenisManager.init();
        } else if (activeTab === "biaya-desain-tab") {
            BiayaDesainManager.init();
        }
    }

    // Initialize on tab change
    $("#productManagerTabs .nav-link").on("shown.bs.tab", function (e) {
        initActiveTab();
    });

    // Initialize on page load
    initActiveTab();

    // Load all item select options once for all forms
    ItemManager.loadSelectOptions();
});
