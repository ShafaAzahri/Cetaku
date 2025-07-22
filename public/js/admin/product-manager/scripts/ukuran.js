document.addEventListener("DOMContentLoaded", function () {
    const editUkuranModal = document.getElementById("editUkuranModal");

    if (editUkuranModal) {
        editUkuranModal.addEventListener("show.bs.modal", function (event) {
            const button = event.relatedTarget;

            const id = button.getAttribute("data-id");
            const size = button.getAttribute("data-size");
            const faktor = button.getAttribute("data-faktor");

            const form = document.getElementById("editUkuranForm");
            form.setAttribute("action", `/admin/ukurans/${id}`);
            document.getElementById("edit_size").value = size;

            const biayaInput = document.getElementById("edit_faktor_harga");
            if (biayaInput) {
                biayaInput.value = faktor;
            } else {
                console.warn(
                    'Input dengan id "edit_faktor_harga" tidak ditemukan!'
                );
            }

            // Fetch data item terkait
            fetch(`/api/ukurans/${id}`)
                .then((res) => res.json())
                .then((data) => {
                    const select = $("#edit_item_ids");
                    if (data.success && data.items) {
                        const ids = data.items.map((i) => String(i.id));
                        select.val(ids).trigger("change");
                    } else {
                        select.val(null).trigger("change");
                    }
                });
        });
    }

    // Select2
    $("#ukuran_item_ids").select2({
        dropdownParent: $("#addUkuranModal"),
        placeholder: "Pilih item",
        width: "100%",
    });

    $("#edit_item_ids").select2({
        dropdownParent: $("#editUkuranModal"),
        placeholder: "Pilih item",
        width: "100%",
    });
});
