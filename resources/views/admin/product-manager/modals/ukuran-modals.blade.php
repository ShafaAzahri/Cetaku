<!-- Add Ukuran Modal -->
<div class="modal fade" id="addUkuranModal" tabindex="-1" aria-labelledby="addUkuranModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.ukurans.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="addUkuranModalLabel">Tambah Ukuran Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="size" class="form-label">Ukuran <span class="text-danger">*</span></label>
                        <input type="text" name="size" id="size" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="faktor_harga" class="form-label">Faktor Harga <span class="text-danger">*</span></label>
                        <input type="number" name="faktor_harga" id="faktor_harga" class="form-control" value="1" min="0.1" step="0.1" required>
                        <small class="form-text text-muted">Contoh: 1.5 berarti harga akan dikalikan 1.5 kali</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Ukuran Modal -->
<div class="modal fade" id="editUkuranModal" tabindex="-1" aria-labelledby="editUkuranModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="editUkuranForm" action="" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title" id="editUkuranModalLabel">Edit Ukuran</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="edit_size" class="form-label">Ukuran <span class="text-danger">*</span></label>
                        <input type="text" name="size" id="edit_size" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="edit_faktor_harga" class="form-label">Faktor Harga <span class="text-danger">*</span></label>
                        <input type="number" name="faktor_harga" id="edit_faktor_harga" class="form-control" min="0.1" step="0.1" required>
                        <small class="form-text text-muted">Contoh: 1.5 berarti harga akan dikalikan 1.5 kali</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>