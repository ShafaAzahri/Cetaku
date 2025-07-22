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
                        <label for="biaya_tambahan" class="form-label">Biaya Tambahan</label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="number" class="form-control" id="biaya_tambahan" name="biaya_tambahan" required min="0" step="1000" value="0">
                        </div>
                        <div class="form-text">Biaya tambahan untuk ukuran ini (0 = tidak ada biaya tambahan).</div>
                    </div>
                    <div class="mb-3">
                        <label for="ukuran_item_ids" class="form-label">Pilih Item Terkait</label>
                        <select class="form-select" id="ukuran_item_ids" name="item_ids[]" multiple>
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
    <form method="POST" id="editUkuranForm">
      @csrf
      @method('PUT')
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Edit Ukuran</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label for="edit_size" class="form-label">Size</label>
            <input type="text" class="form-control" id="edit_size" name="size" required>
          </div>
          <div class="mb-3">
            <label for="edit_faktor_harga" class="form-label">Biaya Tambahan</label>
            <div class="input-group">
              <span class="input-group-text">Rp</span>
              <input type="number" class="form-control" id="edit_faktor_harga" name="biaya_tambahan" required>
            </div>
            <div class="form-text">Biaya tambahan untuk ukuran ini (0 = tidak ada biaya tambahan).</div>
          </div>
          <div class="mb-3">
            <label for="edit_item_ids" class="form-label">Pilih Item Terkait</label>
            <select name="item_ids[]" id="edit_item_ids" class="form-select" multiple>
              @foreach ($items ?? [] as $item)
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
      </div>
    </form>
  </div>
</div>