document.addEventListener("DOMContentLoaded", function () {
    const editJenisModal = document.getElementById("editJenisModal");
    if (editJenisModal) {
        editJenisModal.addEventListener("show.bs.modal", function (event) {
            const button = event.relatedTarget;
            const id = button.getAttribute("data-id");
            const kategori = button.getAttribute("data-kategori");
            const biaya = button.getAttribute("data-biaya");

            const form = document.getElementById("editJenisForm");
            form.setAttribute("action", `/admin/jenis/${id}`);
            document.getElementById("edit_kategori").value = kategori;
            document.getElementById("edit_biaya_tambahan").value = biaya;

            fetch(`/api/jenis/${id}`)
                .then((res) => res.json())
                .then((data) => {
                    const select = $("#edit_item_ids");
                    if (data.success && data.items) {
                        const ids = data.items.map((i) => String(i.id));
                        select.val(ids).trigger("change");
                    }
                });
        });
    }

    $("#jenis_item_ids").select2({
        dropdownParent: $("#addJenisModal"),
        placeholder: "Pilih item",
        width: "100%",
    });

    $("#edit_item_ids").select2({
        dropdownParent: $("#editJenisModal"),
        placeholder: "Pilih item",
        width: "100%",
    });
});
