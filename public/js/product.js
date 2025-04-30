// Script untuk form edit pada product-manager

// Simpan ini di public/js/product.js atau tambahkan ke script section di product-manager.blade.php

$(document).ready(function () {
    // Edit item button click
    $(".edit-item-btn").on("click", function () {
        var id = $(this).data("id");
        var nama = $(this).data("nama");
        var deskripsi = $(this).data("deskripsi");
        var harga = $(this).data("harga");
        var gambar = $(this).data("gambar");

        // Set form action URL
        $("#editItemForm").attr(
            "action",
            baseUrl + "/admin/products/items/" + id
        );

        // Set form values
        $("#edit_nama_item").val(nama);
        $("#edit_deskripsi").val(deskripsi);
        $("#edit_harga_dasar").val(harga);

        // Handle gambar
        if (gambar) {
            $("#current_image").attr("src", gambar);
            $("#current_image_container").show();
        } else {
            $("#current_image_container").hide();
        }

        // Show the modal
        $("#editItemModal").modal("show");
    });
});

document.addEventListener("DOMContentLoaded", function () {
    // Handle Item Edit Form
    const editItemBtns = document.querySelectorAll(".edit-item-btn");
    editItemBtns.forEach((btn) => {
        btn.addEventListener("click", function () {
            const itemId = this.getAttribute("data-id");
            const namaItem = this.getAttribute("data-nama");
            const deskripsi = this.getAttribute("data-deskripsi");
            const hargaDasar = this.getAttribute("data-harga");

            // Set form action URL
            document.getElementById(
                "editItemForm"
            ).action = `${baseUrl}/admin/products/items/${itemId}`;

            // Set form values
            document.getElementById("edit_nama_item").value = namaItem;
            document.getElementById("edit_harga_dasar").value = hargaDasar;
            document.getElementById("edit_deskripsi").value = deskripsi || "";

            // Show modal
            const editItemModal = new bootstrap.Modal(
                document.getElementById("editItemModal")
            );
            editItemModal.show();
        });
    });

    // Handle Bahan Edit Form
    const editBahanBtns = document.querySelectorAll(".edit-bahan-btn");
    editBahanBtns.forEach((btn) => {
        btn.addEventListener("click", function () {
            const bahanId = this.getAttribute("data-id");
            const namaBahan = this.getAttribute("data-nama");
            const biaya = this.getAttribute("data-biaya");
            const itemId = this.getAttribute("data-item");

            // Set form action URL
            document.getElementById(
                "editBahanForm"
            ).action = `${baseUrl}/admin/products/bahans/${bahanId}`;

            // Set form values
            document.getElementById("edit_nama_bahan").value = namaBahan;
            document.getElementById("edit_biaya_tambahan").value = biaya;

            if (itemId) {
                document.getElementById("edit_item_id").value = itemId;
            } else {
                document.getElementById("edit_item_id").value = "";
            }

            // Show modal
            const editBahanModal = new bootstrap.Modal(
                document.getElementById("editBahanModal")
            );
            editBahanModal.show();
        });
    });

    // Handle Ukuran Edit Form
    const editUkuranBtns = document.querySelectorAll(".edit-ukuran-btn");
    editUkuranBtns.forEach((btn) => {
        btn.addEventListener("click", function () {
            const ukuranId = this.getAttribute("data-id");
            const size = this.getAttribute("data-size");
            const faktor = this.getAttribute("data-faktor");
            const itemId = this.getAttribute("data-item");

            // Set form action URL
            document.getElementById(
                "editUkuranForm"
            ).action = `${baseUrl}/admin/products/ukurans/${ukuranId}`;

            // Set form values
            document.getElementById("edit_size").value = size;
            document.getElementById("edit_faktor_harga").value = faktor;

            if (itemId) {
                document.getElementById("edit_item_id_ukuran").value = itemId;
            } else {
                document.getElementById("edit_item_id_ukuran").value = "";
            }

            // Show modal
            const editUkuranModal = new bootstrap.Modal(
                document.getElementById("editUkuranModal")
            );
            editUkuranModal.show();
        });
    });

    // Handle Jenis Edit Form
    const editJenisBtns = document.querySelectorAll(".edit-jenis-btn");
    editJenisBtns.forEach((btn) => {
        btn.addEventListener("click", function () {
            const jenisId = this.getAttribute("data-id");
            const kategori = this.getAttribute("data-kategori");
            const biaya = this.getAttribute("data-biaya");
            const itemId = this.getAttribute("data-item");

            // Set form action URL
            document.getElementById(
                "editJenisForm"
            ).action = `${baseUrl}/admin/products/jenis/${jenisId}`;

            // Set form values
            document.getElementById("edit_kategori").value = kategori;
            document.getElementById("edit_biaya_tambahan_jenis").value = biaya;

            if (itemId) {
                document.getElementById("edit_item_id_jenis").value = itemId;
            } else {
                document.getElementById("edit_item_id_jenis").value = "";
            }

            // Show modal
            const editJenisModal = new bootstrap.Modal(
                document.getElementById("editJenisModal")
            );
            editJenisModal.show();
        });
    });

    // Handle BiayaDesain Edit Form
    const editBiayaDesainBtns = document.querySelectorAll(
        ".edit-biaya-desain-btn"
    );
    editBiayaDesainBtns.forEach((btn) => {
        btn.addEventListener("click", function () {
            const biayaDesainId = this.getAttribute("data-id");
            const deskripsi = this.getAttribute("data-deskripsi");
            const biaya = this.getAttribute("data-biaya");

            // Set form action URL
            document.getElementById(
                "editBiayaDesainForm"
            ).action = `${baseUrl}/admin/products/biaya-desain/${biayaDesainId}`;

            // Set form values
            document.getElementById("edit_biaya").value = biaya;
            document.getElementById("edit_deskripsi_biaya").value =
                deskripsi || "";

            // Show modal
            const editBiayaDesainModal = new bootstrap.Modal(
                document.getElementById("editBiayaDesainModal")
            );
            editBiayaDesainModal.show();
        });
    });
});
