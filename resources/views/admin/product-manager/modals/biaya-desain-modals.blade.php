<!-- Add Biaya Desain Modal -->
<div class="modal fade" id="addBiayaDesainModal" tabindex="-1" aria-labelledby="addBiayaDesainModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.biaya-desain.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="addBiayaDesainModalLabel">Tambah Biaya Desain Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="biaya" class="form-label">Biaya <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="number" name="biaya" id="biaya" class="form-control" value="0" min="0" step="1000" required>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="deskripsi_biaya" class="form-label">Deskripsi</label>
                        <textarea name="deskripsi" id="deskripsi_biaya" rows="3" class="form-control"></textarea>
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

<!-- Edit Biaya Desain Modal -->
<div class="modal fade" id="editBiayaDesainModal" tabindex="-1" aria-labelledby="editBiayaDesainModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="editBiayaDesainForm" action="" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title" id="editBiayaDesainModalLabel">Edit Biaya Desain</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="edit_biaya" class="form-label">Biaya <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="number" name="biaya" id="edit_biaya" class="form-control" min="0" step="1000" required>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="edit_deskripsi_biaya" class="form-label">Deskripsi</label>
                        <textarea name="deskripsi" id="edit_deskripsi_biaya" rows="3" class="form-control"></textarea>
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