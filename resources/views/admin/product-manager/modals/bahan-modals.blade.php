<!-- Add Bahan Modal -->
<div class="modal fade" id="addBahanModal" tabindex="-1" aria-labelledby="addBahanModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.bahans.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="addBahanModalLabel">Tambah Bahan Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="item_id" class="form-label">Item Produk <span class="text-danger">*</span></label>
                        <select name="item_id" id="item_id" class="form-control" required>
                            <option value="">-- Pilih Item Produk --</option>
                            @foreach($items as $item)
                                <option value="{{ $item->id }}">{{ $item->nama_item }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="nama_bahan" class="form-label">Nama Bahan <span class="text-danger">*</span></label>
                        <input type="text" name="nama_bahan" id="nama_bahan" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="biaya_tambahan" class="form-label">Biaya Tambahan <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="number" name="biaya_tambahan" id="biaya_tambahan" class="form-control" value="0" min="0" step="1000" required>
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
            <form id="editBahanForm" action="" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title" id="editBahanModalLabel">Edit Bahan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="edit_item_id" class="form-label">Item Produk <span class="text-danger">*</span></label>
                        <select name="item_id" id="edit_item_id" class="form-control" required>
                            <option value="">-- Pilih Item Produk --</option>
                            @foreach($items as $item)
                                <option value="{{ $item->id }}">{{ $item->nama_item }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="edit_nama_bahan" class="form-label">Nama Bahan <span class="text-danger">*</span></label>
                        <input type="text" name="nama_bahan" id="edit_nama_bahan" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="edit_biaya_tambahan" class="form-label">Biaya Tambahan <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="number" name="biaya_tambahan" id="edit_biaya_tambahan" class="form-control" min="0" step="1000" required>
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