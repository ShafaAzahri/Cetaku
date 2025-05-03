<!-- Modal Tambah Ukuran -->
<div class="modal fade" id="addUkuranModal" tabindex="-1" aria-labelledby="addUkuranModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addUkuranModalLabel">Tambah Ukuran Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.ukurans.store') }}" method="POST">
                <div class="modal-body">
                    @csrf
                    <div class="mb-3">
                        <label for="size" class="form-label">Size</label>
                        <input type="text" class="form-control" id="size" name="size" required>
                    </div>
                    <div class="mb-3">
                        <label for="faktor_harga" class="form-label">Faktor Harga</label>
                        <div class="input-group">
                            <input type="number" class="form-control" id="faktor_harga" name="faktor_harga" required min="0" step="0.01" value="1.00">
                            <span class="input-group-text">x</span>
                        </div>
                        <div class="form-text">Faktor pengali untuk harga dasar (1.00 = tidak ada perubahan).</div>
                    </div>
                    <div class="mb-3">
                        <label for="item_ids" class="form-label">Pilih Item Terkait</label>
                        <select class="form-select" id="item_ids" name="item_ids[]" multiple>
                            @foreach($items ?? [] as $item)
                                <option value="{{ $item['id'] }}">{{ $item['nama_item'] }}</option>
                            @endforeach
                        </select>
                        <div class="form-text">Pilih beberapa item yang menggunakan ukuran ini.</div>
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

<!-- Modal Edit Ukuran -->
<div class="modal fade" id="editUkuranModal" tabindex="-1" aria-labelledby="editUkuranModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editUkuranModalLabel">Edit Ukuran</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editUkuranForm" method="POST">
                <div class="modal-body">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label for="edit_size" class="form-label">Size</label>
                        <input type="text" class="form-control" id="edit_size" name="size" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_faktor_harga" class="form-label">Faktor Harga</label>
                        <div class="input-group">
                            <input type="number" class="form-control" id="edit_faktor_harga" name="faktor_harga" required min="0" step="0.01">
                            <span class="input-group-text">x</span>
                        </div>
                        <div class="form-text">Faktor pengali untuk harga dasar (1.00 = tidak ada perubahan).</div>
                    </div>
                    <div class="mb-3">
                        <label for="edit_item_ids" class="form-label">Pilih Item Terkait</label>
                        <select class="form-select" id="edit_item_ids" name="item_ids[]" multiple>
                            @foreach($items ?? [] as $item)
                                <option value="{{ $item['id'] }}">{{ $item['nama_item'] }}</option>
                            @endforeach
                        </select>
                        <div class="form-text">Pilih beberapa item yang menggunakan ukuran ini.</div>
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