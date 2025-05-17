document.addEventListener("DOMContentLoaded", function () {
    const editBahanModal = document.getElementById("editBahanModal");
    if (editBahanModal) {
        editBahanModal.addEventListener("show.bs.modal", function (event) {
            const button = event.relatedTarget;
            const id = button.getAttribute("data-id");
            const nama = button.getAttribute("data-nama");
            const biaya = button.getAttribute("data-biaya");

            const form = document.getElementById("editBahanForm");
            form.setAttribute("action", `/admin/bahans/${id}`);
            document.getElementById("edit_nama_bahan").value = nama;
            document.getElementById("edit_biaya_tambahan").value = biaya;

            // Ambil item terkait via API
            fetch(`/api/bahans/${id}`)
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

    $("#bahan_item_ids").select2({
        dropdownParent: $("#addBahanModal"),
        placeholder: "Pilih item",
        width: "100%",
    });

    $("#edit_item_ids").select2({
        dropdownParent: $("#editBahanModal"),
        placeholder: "Pilih item",
        width: "100%",
    });
});
