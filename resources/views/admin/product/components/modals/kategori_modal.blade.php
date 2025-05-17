<!-- Modal Tambah Kategori -->
<div class="modal fade" id="addKategoriModal" tabindex="-1" aria-labelledby="addKategoriModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addKategoriModalLabel">Tambah Kategori Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.kategoris.store') }}" method="POST" enctype="multipart/form-data">
                <div class="modal-body">
                    @csrf
                    <div class="mb-3">
                        <label for="nama_kategori" class="form-label">Nama Kategori</label>
                        <input type="text" class="form-control" id="nama_kategori" name="nama_kategori" required>
                    </div>
                    <div class="mb-3">
                        <label for="deskripsi" class="form-label">Deskripsi</label>
                        <textarea class="form-control" id="deskripsi" name="deskripsi" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="gambar" class="form-label">Gambar</label>
                        <input type="file" class="form-control" id="gambar" name="gambar" accept="image/*">
                        <div class="form-text">Upload gambar thumbnail untuk kategori (opsional). Ukuran maksimal 2MB.</div>
                    </div>
                    <div class="mb-3">
                        <label for="item_ids" class="form-label">Pilih Item Terkait</label>
                        <select class="form-select" id="item_ids" name="item_ids[]" multiple>
                            @foreach($items ?? [] as $item)
                                <option value="{{ $item['id'] }}">{{ $item['nama_item'] }}</option>
                            @endforeach
                        </select>
                        <div class="form-text">Pilih beberapa item yang termasuk dalam kategori ini.</div>
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

<!-- Modal Edit Kategori -->
<div class="modal fade" id="editKategoriModal" tabindex="-1" aria-labelledby="editKategoriModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editKategoriModalLabel">Edit Kategori</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editKategoriForm" method="POST" enctype="multipart/form-data">
                <div class="modal-body">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label for="edit_nama_kategori" class="form-label">Nama Kategori</label>
                        <input type="text" class="form-control" id="edit_nama_kategori" name="nama_kategori" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_deskripsi" class="form-label">Deskripsi</label>
                        <textarea class="form-control" id="edit_deskripsi" name="deskripsi" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="edit_gambar" class="form-label">Gambar</label>
                        <input type="file" class="form-control" id="edit_gambar" name="gambar" accept="image/*">
                        <div id="current_image" class="mt-2"></div>
                        <div class="form-text">Upload gambar baru untuk mengganti gambar lama (opsional). Ukuran maksimal 2MB.</div>
                    </div>
                    <div class="mb-3">
                        <label for="edit_item_ids" class="form-label">Pilih Item Terkait</label>
                        <select class="form-select" id="edit_item_ids" name="item_ids[]" multiple>
                            @foreach($items ?? [] as $item)
                                <option value="{{ $item['id'] }}">{{ $item['nama_item'] }}</option>
                            @endforeach
                        </select>
                        <div class="form-text">Pilih beberapa item yang termasuk dalam kategori ini.</div>
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