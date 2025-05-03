@extends('admin.layout.admin')

@section('title', 'Product Manager')

@section('content')
<div class="container-fluid">
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card shadow-sm">
                <div class="card-body">
                    <!-- Tabs -->
                    <div class="mb-4">
                        <div class="row">
                            <div class="col-md-3">
                                <a href="{{ route('admin.product-manager', ['tab' => 'items']) }}" 
                                   class="btn {{ $activeTab == 'items' ? 'btn-primary' : 'btn-light' }} w-100 py-3">
                                    <i class="fas fa-boxes me-2"></i> Items
                                </a>
                            </div>
                            <div class="col-md-3">
                                <a href="{{ route('admin.product-manager', ['tab' => 'bahan']) }}" 
                                   class="btn {{ $activeTab == 'bahan' ? 'btn-primary' : 'btn-light' }} w-100 py-3">
                                    <i class="fas fa-layer-group me-2"></i> Bahan
                                </a>
                            </div>
                            <div class="col-md-3">
                                <a href="{{ route('admin.product-manager', ['tab' => 'ukuran']) }}" 
                                   class="btn {{ $activeTab == 'ukuran' ? 'btn-primary' : 'btn-light' }} w-100 py-3">
                                    <i class="fas fa-rulers me-2"></i> Ukuran
                                </a>
                            </div>
                            <div class="col-md-3">
                                <a href="{{ route('admin.product-manager', ['tab' => 'jenis']) }}" 
                                   class="btn {{ $activeTab == 'jenis' ? 'btn-primary' : 'btn-light' }} w-100 py-3">
                                    <i class="fas fa-tag me-2"></i> Jenis
                                </a>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Tab Content -->
                    @if($activeTab == 'items')
                    <div>
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="mb-0">Daftar Item</h5>
                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addItemModal">
                                <i class="fas fa-plus me-1"></i> Tambah Item
                            </button>
                        </div>
                        
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Gambar</th>
                                        <th>Nama Item</th>
                                        <th>Deskripsi</th>
                                        <th>Harga Dasar</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($items as $item)
                                    <tr>
                                        <td>{{ $item['id'] }}</td>
                                        <td>
                                        @if(isset($item['gambar']) && $item['gambar'])
                                            <img src="{{ asset('storage/' . $item['gambar']) }}" class="img-thumbnail" width="80" height="80" alt="{{ $item['nama_item'] }}">
                                        @else
                                            <img src="{{ asset('images/no-image.png') }}" class="img-thumbnail" width="80" height="80" alt="No Image">
                                        @endif
                                        </td>
                                        <td>{{ $item['nama_item'] }}</td>
                                        <td>{{ $item['deskripsi'] ?? '-' }}</td>
                                        <td>Rp {{ number_format($item['harga_dasar'], 0, ',', '.') }}</td>
                                        <td>
                                            <button class="btn btn-sm btn-info me-1" title="Edit" 
                                                    data-bs-toggle="modal" data-bs-target="#editItemModal"
                                                    data-id="{{ $item['id'] }}"
                                                    data-nama="{{ $item['nama_item'] }}"
                                                    data-deskripsi="{{ $item['deskripsi'] }}"
                                                    data-harga="{{ $item['harga_dasar'] }}"
                                                    data-gambar="{{ $item['gambar'] ?? '' }}">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <form action="{{ route('admin.items.destroy', $item['id']) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" title="Hapus" 
                                                        onclick="return confirm('Apakah Anda yakin ingin menghapus item ini?')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="6" class="text-center">Tidak ada data item</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @elseif($activeTab == 'bahan')
                    <div class="text-center py-5">
                        <p class="text-muted">Fitur Bahan masih dalam pengembangan</p>
                    </div>
                    @elseif($activeTab == 'ukuran')
                    <div class="text-center py-5">
                        <p class="text-muted">Fitur Ukuran masih dalam pengembangan</p>
                    </div>
                    @elseif($activeTab == 'jenis')
                    <div class="text-center py-5">
                        <p class="text-muted">Fitur Jenis masih dalam pengembangan</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah Item -->
<div class="modal fade" id="addItemModal" tabindex="-1" aria-labelledby="addItemModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addItemModalLabel">Tambah Item Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.items.store') }}" method="POST" enctype="multipart/form-data">
                <div class="modal-body">
                    @csrf
                    <div class="mb-3">
                        <label for="nama_item" class="form-label">Nama Item</label>
                        <input type="text" class="form-control" id="nama_item" name="nama_item" required>
                    </div>
                    <div class="mb-3">
                        <label for="deskripsi" class="form-label">Deskripsi</label>
                        <textarea class="form-control" id="deskripsi" name="deskripsi" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="harga_dasar" class="form-label">Harga Dasar</label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="number" class="form-control" id="harga_dasar" name="harga_dasar" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="gambar" class="form-label">Gambar</label>
                        <input type="file" class="form-control" id="gambar" name="gambar" accept="image/*">
                        <div class="form-text">Upload gambar produk (opsional). Ukuran maksimal 2MB.</div>
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

<!-- Modal Edit Item -->
<div class="modal fade" id="editItemModal" tabindex="-1" aria-labelledby="editItemModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editItemModalLabel">Edit Item</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editItemForm" method="POST" enctype="multipart/form-data">
                <div class="modal-body">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label for="edit_nama_item" class="form-label">Nama Item</label>
                        <input type="text" class="form-control" id="edit_nama_item" name="nama_item" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_deskripsi" class="form-label">Deskripsi</label>
                        <textarea class="form-control" id="edit_deskripsi" name="deskripsi" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="edit_harga_dasar" class="form-label">Harga Dasar</label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="number" class="form-control" id="edit_harga_dasar" name="harga_dasar" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="edit_gambar" class="form-label">Gambar</label>
                        <input type="file" class="form-control" id="edit_gambar" name="gambar" accept="image/*">
                        <div id="current_image" class="mt-2"></div>
                        <div class="form-text">Upload gambar baru untuk mengganti gambar lama (opsional). Ukuran maksimal 2MB.</div>
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
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Script untuk mengisi form edit
        const editItemModal = document.getElementById('editItemModal');
        if (editItemModal) {
            editItemModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                const id = button.getAttribute('data-id');
                const nama = button.getAttribute('data-nama');
                const deskripsi = button.getAttribute('data-deskripsi');
                const harga = button.getAttribute('data-harga');
                const gambar = button.getAttribute('data-gambar');
                
                // Set action URL dengan URL lengkap
                const form = document.getElementById('editItemForm');
                form.setAttribute('action', `{{ url('admin/items') }}/${id}`);
                
                // Set nilai input
                document.getElementById('edit_nama_item').value = nama;
                document.getElementById('edit_deskripsi').value = deskripsi || '';
                document.getElementById('edit_harga_dasar').value = harga;
                
                // Tampilkan gambar saat ini jika ada
                const currentImageDiv = document.getElementById('current_image');
                if (gambar) {
                    const storageUrl = "{{ asset('storage') }}";
                    currentImageDiv.innerHTML = `
                        <div class="text-center">
                            <img src="${storageUrl}/${gambar}" class="img-thumbnail" style="max-height: 150px" alt="${nama}">
                            <p class="small text-muted mt-1">Gambar saat ini</p>
                        </div>
                    `;
                } else {
                    currentImageDiv.innerHTML = '<p class="text-muted">Tidak ada gambar</p>';
                }
            });
        }
        
        // Preview gambar saat upload
        const gambarInput = document.getElementById('gambar');
        if (gambarInput) {
            gambarInput.addEventListener('change', function() {
                if (this.files && this.files[0]) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const previewDiv = document.createElement('div');
                        previewDiv.classList.add('mt-2', 'text-center');
                        previewDiv.innerHTML = `
                            <img src="${e.target.result}" class="img-thumbnail" style="max-height: 150px">
                            <p class="small text-muted mt-1">Preview gambar</p>
                        `;
                        
                        const previewContainer = gambarInput.parentElement;
                        const existingPreview = previewContainer.querySelector('.text-center');
                        if (existingPreview) {
                            previewContainer.removeChild(existingPreview);
                        }
                        previewContainer.appendChild(previewDiv);
                    }
                    reader.readAsDataURL(this.files[0]);
                }
            });
        }
        
        // Preview gambar saat upload di form edit
        const editGambarInput = document.getElementById('edit_gambar');
        if (editGambarInput) {
            editGambarInput.addEventListener('change', function() {
                if (this.files && this.files[0]) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const previewDiv = document.createElement('div');
                        previewDiv.classList.add('mt-2', 'text-center');
                        previewDiv.innerHTML = `
                            <img src="${e.target.result}" class="img-thumbnail" style="max-height: 150px">
                            <p class="small text-muted mt-1">Preview gambar baru</p>
                        `;
                        
                        const currentImageDiv = document.getElementById('current_image');
                        currentImageDiv.innerHTML = '';
                        currentImageDiv.appendChild(previewDiv);
                    }
                    reader.readAsDataURL(this.files[0]);
                }
            });
        }
    });
</script>
@endsection