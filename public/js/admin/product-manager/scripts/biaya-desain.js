document.addEventListener("DOMContentLoaded", function () {
    const editModal = document.getElementById("editBiayaDesainModal");
    if (editModal) {
        editModal.addEventListener("show.bs.modal", function (event) {
            const button = event.relatedTarget;
            const id = button.getAttribute("data-id");
            const biaya = button.getAttribute("data-biaya");
            const deskripsi = button.getAttribute("data-deskripsi");

            const form = document.getElementById("editBiayaDesainForm");
            form.setAttribute("action", `/admin/biaya-desains/${id}`);
            document.getElementById("edit_biaya").value = biaya;
            document.getElementById("edit_deskripsi_biaya").value =
                deskripsi || "";
        });
    }

    $("#biaya_desain_item_ids").select2({
        dropdownParent: $("#addBiayaDesainModal"),
        placeholder: "Pilih item",
        width: "100%",
    });

    $("#edit_item_ids").select2({
        dropdownParent: $("#editBiayaDesainModal"),
        placeholder: "Pilih item",
        width: "100%",
    });
});
