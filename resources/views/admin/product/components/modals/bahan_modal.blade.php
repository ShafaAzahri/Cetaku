<!-- Modal Tambah Bahan -->
<div class="modal fade" id="addBahanModal" tabindex="-1" aria-labelledby="addBahanModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addBahanModalLabel">Tambah Bahan Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.bahans.store') }}" method="POST">
                <div class="modal-body">
                    @csrf
                    <div class="mb-3">
                        <label for="nama_bahan" class="form-label">Nama Bahan</label>
                        <input type="text" class="form-control" id="nama_bahan" name="nama_bahan" required>
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
                        <div class="form-text">Pilih beberapa item yang menggunakan bahan ini.</div>
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

<!-- Modal Edit Bahan -->
<div class="modal fade" id="editBahanModal" tabindex="-1" aria-labelledby="editBahanModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editBahanModalLabel">Edit Bahan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editBahanForm" method="POST">
                <div class="modal-body">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label for="edit_nama_bahan" class="form-label">Nama Bahan</label>
                        <input type="text" class="form-control" id="edit_nama_bahan" name="nama_bahan" required>
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
                        <div class="form-text">Pilih beberapa item yang menggunakan bahan ini.</div>
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