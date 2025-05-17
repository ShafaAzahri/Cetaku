document.addEventListener("DOMContentLoaded", function () {
    const editItemModal = document.getElementById("editItemModal");
    if (editItemModal) {
        editItemModal.addEventListener("show.bs.modal", function (event) {
            const button = event.relatedTarget;
            const id = button.getAttribute("data-id");
            const nama = button.getAttribute("data-nama");
            const deskripsi = button.getAttribute("data-deskripsi");
            const harga = button.getAttribute("data-harga");
            const gambar = button.getAttribute("data-gambar");

            const form = document.getElementById("editItemForm");
            form.setAttribute("action", `/admin/items/${id}`);
            document.getElementById("edit_nama_item").value = nama;
            document.getElementById("edit_deskripsi").value = deskripsi || "";
            document.getElementById("edit_harga_dasar").value = harga;

            const currentImageDiv = document.getElementById("current_image");
            if (gambar) {
                const storageUrl = "/storage";
                currentImageDiv.innerHTML = `
                    <div class="text-center">
                        <img src="${storageUrl}/${gambar}" class="img-thumbnail" style="max-height: 150px" alt="${nama}">
                        <p class="small text-muted mt-1">Gambar saat ini</p>
                    </div>
                `;
            } else {
                currentImageDiv.innerHTML =
                    '<p class="text-muted">Tidak ada gambar</p>';
            }
        });
    }
});
