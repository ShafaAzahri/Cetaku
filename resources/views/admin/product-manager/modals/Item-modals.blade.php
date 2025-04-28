<!-- Add Item Modal -->
<div class="modal fade" id="addItemModal" tabindex="-1" aria-labelledby="addItemModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{ route('admin.items.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="addItemModalLabel">Tambah Produk Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="nama_item" class="form-label">Nama Produk <span class="text-danger">*</span></label>
                                <input type="text" name="nama_item" id="nama_item" class="form-control" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="jenis_id" class="form-label">Jenis Produk <span class="text-danger">*</span></label>
                                <select name="jenis_id" id="jenis_id" class="form-control" required>
                                    <option value="">-- Pilih Jenis --</option>
                                    @foreach($jenis as $j)
                                        <option value="{{ $j->id }}">
                                            {{ $j->kategori }} (+Rp {{ number_format($j->biaya_tambahan, 0, ',', '.') }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label for="harga_dasar" class="form-label">Harga Dasar <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number" name="harga_dasar" id="harga_dasar" class="form-control" value="0" min="0" step="1000" required>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="bahan_ids" class="form-label">Bahan yang Tersedia</label>
                                <select name="bahan_ids[]" id="bahan_ids" class="form-control" multiple>
                                    @foreach($bahans as $bahan)
                                        <option value="{{ $bahan->id }}">
                                            {{ $bahan->nama_bahan }} (+Rp {{ number_format($bahan->biaya_tambahan, 0, ',', '.') }})
                                        </option>
                                    @endforeach
                                </select>
                                <small class="form-text text-muted">Pilih beberapa bahan dengan menekan tombol Ctrl (Windows) atau Command (Mac)</small>
                            </div>
                            
                            <div class="form-group">
                                <label for="ukuran_ids" class="form-label">Ukuran yang Tersedia</label>
                                <select name="ukuran_ids[]" id="ukuran_ids" class="form-control" multiple>
                                    @foreach($ukurans as $ukuran)
                                        <option value="{{ $ukuran->id }}">
                                            {{ $ukuran->size }} (x{{ $ukuran->faktor_harga }})
                                        </option>
                                    @endforeach
                                </select>
                                <small class="form-text text-muted">Pilih beberapa ukuran dengan menekan tombol Ctrl (Windows) atau Command (Mac)</small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="deskripsi" class="form-label">Deskripsi</label>
                        <textarea name="deskripsi" id="deskripsi" rows="3" class="form-control"></textarea>
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

<!-- Edit Item Modal -->
<div class="modal fade" id="editItemModal" tabindex="-1" aria-labelledby="editItemModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="editItemForm" action="" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title" id="editItemModalLabel">Edit Produk</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_nama_item" class="form-label">Nama Produk <span class="text-danger">*</span></label>
                                <input type="text" name="nama_item" id="edit_nama_item" class="form-control" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="edit_jenis_id" class="form-label">Jenis Produk <span class="text-danger">*</span></label>
                                <select name="jenis_id" id="edit_jenis_id" class="form-control" required>
                                    <option value="">-- Pilih Jenis --</option>
                                    @foreach($jenis as $j)
                                        <option value="{{ $j->id }}">
                                            {{ $j->kategori }} (+Rp {{ number_format($j->biaya_tambahan, 0, ',', '.') }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label for="edit_harga_dasar" class="form-label">Harga Dasar <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number" name="harga_dasar" id="edit_harga_dasar" class="form-control" min="0" step="1000" required>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_bahan_ids" class="form-label">Bahan yang Tersedia</label>
                                <select name="bahan_ids[]" id="edit_bahan_ids" class="form-control" multiple>
                                    @foreach($bahans as $bahan)
                                        <option value="{{ $bahan->id }}">
                                            {{ $bahan->nama_bahan }} (+Rp {{ number_format($bahan->biaya_tambahan, 0, ',', '.') }})
                                        </option>
                                    @endforeach
                                </select>
                                <small class="form-text text-muted">Pilih beberapa bahan dengan menekan tombol Ctrl (Windows) atau Command (Mac)</small>
                            </div>
                            
                            <div class="form-group">
                                <label for="edit_ukuran_ids" class="form-label">Ukuran yang Tersedia</label>
                                <select name="ukuran_ids[]" id="edit_ukuran_ids" class="form-control" multiple>
                                    @foreach($ukurans as $ukuran)
                                        <option value="{{ $ukuran->id }}">
                                            {{ $ukuran->size }} (x{{ $ukuran->faktor_harga }})
                                        </option>
                                    @endforeach
                                </select>
                                <small class="form-text text-muted">Pilih beberapa ukuran dengan menekan tombol Ctrl (Windows) atau Command (Mac)</small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="edit_deskripsi" class="form-label">Deskripsi</label>
                        <textarea name="deskripsi" id="edit_deskripsi" rows="3" class="form-control"></textarea>
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