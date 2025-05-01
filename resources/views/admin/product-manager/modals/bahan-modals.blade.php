```php
<!-- Add Bahan Modal -->
<div class="modal fade" id="addBahanModal" tabindex="-1" aria-labelledby="addBahanModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="addBahanForm" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="addBahanModalLabel">Tambah Bahan Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="form-group mb-3">
                        <label for="item_id" class="form-label">Item Produk <span class="text-danger">*</span></label>
                        <select name="item_id" id="item_id" class="form-control" required>
                            <option value="">-- Pilih Item Produk --</option>
                            <!-- Options will be filled by JavaScript -->
                        </select>
                    </div>
                    
                    <div class="form-group mb-3">
                        <label for="nama_bahan" class="form-label">Nama Bahan <span class="text-danger">*</span></label>
                        <input type="text" name="nama_bahan" id="nama_bahan" class="form-control" required>
                    </div>
                    
                    <div class="form-group mb-3">
                        <label for="biaya_tambahan" class="form-label">Biaya Tambahan <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="number" name="biaya_tambahan" id="biaya_tambahan" class="form-control" value="0" min="0" step="1000" required>
                        </div>
                    </div>

                    <div class="form-group mb-3">
                        <div class="form-check">
                            <input type="checkbox" name="is_available" id="is_available" class="form-check-input" value="1" checked>
                            <label for="is_available" class="form-check-label">Tersedia</label>
                        </div>
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

<!-- Edit Bahan Modal -->
<div class="modal fade" id="editBahanModal" tabindex="-1" aria-labelledby="editBahanModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="editBahanForm" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="editBahanModalLabel">Edit Bahan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="form-group mb-3">
                        <label for="edit_item_id" class="form-label">Item Produk <span class="text-danger">*</span></label>
                        <select name="item_id" id="edit_item_id" class="form-control" required>
                            <option value="">-- Pilih Item Produk --</option>
                            <!-- Options will be filled by JavaScript -->
                        </select>
                    </div>
                    
                    <div class="form-group mb-3">
                        <label for="edit_nama_bahan" class="form-label">Nama Bahan <span class="text-danger">*</span></label>
                        <input type="text" name="nama_bahan" id="edit_nama_bahan" class="form-control" required>
                    </div>
                    
                    <div class="form-group mb-3">
                        <label for="edit_biaya_tambahan" class="form-label">Biaya Tambahan <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="number" name="biaya_tambahan" id="edit_biaya_tambahan" class="form-control" min="0" step="1000" required>
                        </div>
                    </div>

                    <div class="form-group mb-3">
                        <div class="form-check">
                            <input type="checkbox" name="is_available" id="edit_is_available" class="form-check-input" value="1">
                            <label for="edit_is_available" class="form-check-label">Tersedia</label>
                        </div>
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
```