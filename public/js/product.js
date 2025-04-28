// JavaScript untuk halaman Product Manager
$(document).ready(function () {
    // Initialize select2 for better multi-select experience (if available)
    if ($.fn.select2) {
        $("#bahan_ids, #ukuran_ids, #edit_bahan_ids, #edit_ukuran_ids").select2(
            {
                placeholder: "Pilih opsi",
                dropdownParent: $(".modal"),
            }
        );
    }

    // Auto close alerts after 5 seconds
    setTimeout(function () {
        $(".alert").alert("close");
    }, 5000);

    // Tab persistence using sessionStorage
    $('button[data-bs-toggle="tab"]').on("shown.bs.tab", function (e) {
        sessionStorage.setItem(
            "activeProductManagerTab",
            $(e.target).attr("id")
        );
    });

    // Check if there is a saved tab and show it
    var activeTab = sessionStorage.getItem("activeProductManagerTab");
    if (activeTab) {
        $("#" + activeTab).tab("show");
    }

    // ================= Item ====================
    // Edit Item Modal Setup
    $(".edit-item-btn").click(function () {
        var itemId = $(this).data("id");
        var nama = $(this).data("nama");
        var deskripsi = $(this).data("deskripsi");
        var jenisId = $(this).data("jenis");
        var harga = $(this).data("harga");
        var bahans = $(this).data("bahans");
        var ukurans = $(this).data("ukurans");

        // Set the form action
        $("#editItemForm").attr(
            "action",
            baseUrl + "/admin/products/items/" + itemId
        );

        // Fill the form fields
        $("#edit_nama_item").val(nama);
        $("#edit_deskripsi").val(deskripsi || "");
        $("#edit_jenis_id").val(jenisId);
        $("#edit_harga_dasar").val(harga);

        // Handle multi-select for bahans and ukurans
        if ($.fn.select2) {
            // Convert to array if it's a string
            if (typeof bahans === "string") {
                bahans = bahans.split(",");
            }
            $("#edit_bahan_ids").val(bahans).trigger("change");

            if (typeof ukurans === "string") {
                ukurans = ukurans.split(",");
            }
            $("#edit_ukuran_ids").val(ukurans).trigger("change");
        } else {
            // Standard select handling
            $("#edit_bahan_ids option").prop("selected", false);
            bahans.forEach(function (id) {
                $('#edit_bahan_ids option[value="' + id + '"]').prop(
                    "selected",
                    true
                );
            });

            $("#edit_ukuran_ids option").prop("selected", false);
            ukurans.forEach(function (id) {
                $('#edit_ukuran_ids option[value="' + id + '"]').prop(
                    "selected",
                    true
                );
            });
        }

        // Show the modal
        $("#editItemModal").modal("show");
    });

    // ================= Bahan ====================
    // Edit Bahan Modal Setup
    $(".edit-bahan-btn").click(function () {
        var bahanId = $(this).data("id");
        var nama = $(this).data("nama");
        var biaya = $(this).data("biaya");

        // Set the form action
        $("#editBahanForm").attr(
            "action",
            baseUrl + "/admin/products/bahans/" + bahanId
        );

        // Fill the form fields
        $("#edit_nama_bahan").val(nama);
        $("#edit_biaya_tambahan").val(biaya);

        // Show the modal
        $("#editBahanModal").modal("show");
    });

    // ================= Ukuran ====================
    // Edit Ukuran Modal Setup
    $(".edit-ukuran-btn").click(function () {
        var ukuranId = $(this).data("id");
        var size = $(this).data("size");
        var faktor = $(this).data("faktor");

        // Set the form action
        $("#editUkuranForm").attr(
            "action",
            baseUrl + "/admin/products/ukurans/" + ukuranId
        );

        // Fill the form fields
        $("#edit_size").val(size);
        $("#edit_faktor_harga").val(faktor);

        // Show the modal
        $("#editUkuranModal").modal("show");
    });

    // ================= Jenis ====================
    // Edit Jenis Modal Setup
    $(".edit-jenis-btn").click(function () {
        var jenisId = $(this).data("id");
        var kategori = $(this).data("kategori");
        var biaya = $(this).data("biaya");

        // Set the form action
        $("#editJenisForm").attr(
            "action",
            baseUrl + "/admin/products/jenis/" + jenisId
        );

        // Fill the form fields
        $("#edit_kategori").val(kategori);
        $("#edit_biaya_tambahan_jenis").val(biaya);

        // Show the modal
        $("#editJenisModal").modal("show");
    });

    // ================= Biaya Desain ====================
    // Edit Biaya Desain Modal Setup
    $(".edit-biaya-desain-btn").click(function () {
        var biayaDesainId = $(this).data("id");
        var nama = $(this).data("nama");
        var deskripsi = $(this).data("deskripsi");
        var biaya = $(this).data("biaya");

        // Set the form action
        $("#editBiayaDesainForm").attr(
            "action",
            baseUrl + "/admin/products/biaya-desain/" + biayaDesainId
        );

        // Fill the form fields
        $("#edit_nama_tingkat").val(nama);
        $("#edit_biaya").val(biaya);
        $("#edit_deskripsi_biaya").val(deskripsi || "");

        // Show the modal
        $("#editBiayaDesainModal").modal("show");
    });
});
