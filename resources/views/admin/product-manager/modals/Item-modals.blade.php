<!-- Add Item Modal -->
<div class="modal fade" id="addItemModal" tabindex="-1" aria-labelledby="addItemModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.product.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="addItemModalLabel">Tambah Produk Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="form-group mb-3">
                        <label for="nama_item" class="form-label">Nama Produk <span class="text-danger">*</span></label>
                        <input type="text" name="nama_item" id="nama_item" class="form-control" required>
                    </div>
                    
                    <div class="form-group mb-3">
                        <label for="harga_dasar" class="form-label">Harga Dasar <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="number" name="harga_dasar" id="harga_dasar" class="form-control" value="0" min="0" step="1000" required>
                        </div>
                    </div>
                    
                    <div class="form-group mb-3">
                        <label for="deskripsi" class="form-label">Deskripsi</label>
                        <textarea name="deskripsi" id="deskripsi" rows="3" class="form-control"></textarea>
                    </div>
                    
                    <div class="form-group mb-3">
                        <label for="gambar" class="form-label">Gambar Produk</label>
                        <input type="file" name="gambar" id="gambar" class="form-control" accept="image/*">
                        <small class="form-text text-muted">Format: JPG, JPEG, PNG, GIF. Maksimal 2MB.</small>
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