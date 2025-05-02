<!-- Add Jenis Modal -->
<div class="modal fade" id="addJenisModal" tabindex="-1" aria-labelledby="addJenisModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.jenis.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="addJenisModalLabel">Tambah Jenis Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="form-group mb-3">
                        <label for="item_id_jenis" class="form-label">Item Produk <span class="text-danger">*</span></label>
                        <select name="item_id" id="item_id_jenis" class="form-control" required>
                            <option value="">-- Pilih Item Produk --</option>
                            @if(isset($itemsDropdown) && count($itemsDropdown) > 0)
                                @foreach($itemsDropdown as $item)
                                    <option value="{{ $item['id'] }}">{{ $item['nama_item'] }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                    
                    <div class="form-group mb-3">
                        <label for="kategori" class="form-label">Kategori <span class="text-danger">*</span></label>
                        <input type="text" name="kategori" id="kategori" class="form-control" required>
                    </div>
                    
                    <div class="form-group mb-3">
                        <label for="biaya_tambahan_jenis" class="form-label">Biaya Tambahan <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="number" name="biaya_tambahan" id="biaya_tambahan_jenis" class="form-control" value="0" min="0" step="1000" required>
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