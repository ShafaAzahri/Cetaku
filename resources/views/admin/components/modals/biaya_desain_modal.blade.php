<!-- Modal Tambah Biaya Desain -->
<div class="modal fade" id="addBiayaDesainModal" tabindex="-1" aria-labelledby="addBiayaDesainModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addBiayaDesainModalLabel">Tambah Biaya Desain Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.biaya-desains.store') }}" method="POST">
                <div class="modal-body">
                    @csrf
                    <div class="mb-3">
                        <label for="biaya" class="form-label">Biaya</label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="number" class="form-control" id="biaya" name="biaya" required value="0">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="deskripsi_biaya" class="form-label">Deskripsi</label>
                        <textarea class="form-control" id="deskripsi_biaya" name="deskripsi" rows="3"></textarea>
                        <div class="form-text">Berikan deskripsi tentang biaya desain ini (opsional).</div>
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

<!-- Modal Edit Biaya Desain -->
<div class="modal fade" id="editBiayaDesainModal" tabindex="-1" aria-labelledby="editBiayaDesainModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editBiayaDesainModalLabel">Edit Biaya Desain</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editBiayaDesainForm" method="POST">
                <div class="modal-body">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label for="edit_biaya" class="form-label">Biaya</label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="number" class="form-control" id="edit_biaya" name="biaya" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="edit_deskripsi_biaya" class="form-label">Deskripsi</label>
                        <textarea class="form-control" id="edit_deskripsi_biaya" name="deskripsi" rows="3"></textarea>
                        <div class="form-text">Berikan deskripsi tentang biaya desain ini (opsional).</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>