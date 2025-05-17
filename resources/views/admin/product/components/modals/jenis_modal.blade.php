<!-- Modal Tambah Jenis -->
<div class="modal fade" id="addJenisModal" tabindex="-1" aria-labelledby="addJenisModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addJenisModalLabel">Tambah Jenis Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.jenis.store') }}" method="POST">
                <div class="modal-body">
                    @csrf
                    <div class="mb-3">
                        <label for="kategori" class="form-label">Kategori</label>
                        <input type="text" class="form-control" id="kategori" name="kategori" required>
                    </div>
                    <div class="mb-3">
                        <label for="biaya_tambahan" class="form-label">Biaya Tambahan</label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="number" class="form-control" id="biaya_tambahan" name="biaya_tambahan" required min="0">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="item_ids" class="form-label">Pilih Item Terkait</label>
                        <select class="form-select" id="item_ids" name="item_ids[]" multiple>
                            @foreach($items ?? [] as $item)
                                <option value="{{ $item['id'] }}">{{ $item['nama_item'] }}</option>
                            @endforeach
                        </select>
                        <div class="form-text">Pilih beberapa item yang menggunakan jenis ini.</div>
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

<!-- Modal Edit Jenis -->
<div class="modal fade" id="editJenisModal" tabindex="-1" aria-labelledby="editJenisModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editJenisModalLabel">Edit Jenis</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editJenisForm" method="POST">
                <div class="modal-body">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label for="edit_kategori" class="form-label">Kategori</label>
                        <input type="text" class="form-control" id="edit_kategori" name="kategori" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_biaya_tambahan" class="form-label">Biaya Tambahan</label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="number" class="form-control" id="edit_biaya_tambahan" name="biaya_tambahan" required min="0">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="edit_item_ids" class="form-label">Pilih Item Terkait</label>
                        <select class="form-select" id="edit_item_ids" name="item_ids[]" multiple>
                            @foreach($items ?? [] as $item)
                                <option value="{{ $item['id'] }}">{{ $item['nama_item'] }}</option>
                            @endforeach
                        </select>
                        <div class="form-text">Pilih beberapa item yang menggunakan jenis ini.</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>