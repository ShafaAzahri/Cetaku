document.addEventListener("DOMContentLoaded", function () {
    const editKategoriModal = document.getElementById("editKategoriModal");
    if (editKategoriModal) {
        editKategoriModal.addEventListener("show.bs.modal", function (event) {
            const button = event.relatedTarget;
            const id = button.getAttribute("data-id");
            const nama = button.getAttribute("data-nama");
            const deskripsi = button.getAttribute("data-deskripsi");
            const gambar = button.getAttribute("data-gambar");
            const itemIdsRaw = button.getAttribute("data-item-ids");

            let itemIds = [];
            try {
                itemIds = JSON.parse(itemIdsRaw);
            } catch (e) {
                itemIds = [];
            }

            const form = document.getElementById("editKategoriForm");
            form.setAttribute("action", `/admin/kategoris/${id}`);
            document.getElementById("edit_nama_kategori").value = nama;
            document.getElementById("edit_deskripsi").value = deskripsi || "";
            $("#edit_item_ids").val(itemIds).trigger("change");

            const currentImageDiv = document.getElementById("current_image");
            if (gambar) {
                const storageUrl = "/storage"; // atau ganti ke {{ asset('storage') }} via inline Blade
                currentImageDiv.innerHTML = `
                    <div class="text-center">
                        <img src="${storageUrl}/${gambar}" class="img-thumbnail" style="max-height: 150px" alt="${nama}">
                        <p class="small text-muted mt-1">Gambar kategori saat ini</p>
                    </div>
                `;
            } else {
                currentImageDiv.innerHTML =
                    '<p class="text-muted">Tidak ada gambar</p>';
            }
        });
    }

    $("#item_ids").select2({
        dropdownParent: $("#addKategoriModal"),
        placeholder: "Pilih item terkait",
        width: "100%",
    });

    $("#edit_item_ids").select2({
        dropdownParent: $("#editKategoriModal"),
        placeholder: "Pilih item terkait",
        width: "100%",
    });
});
