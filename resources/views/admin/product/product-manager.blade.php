@extends('admin.layout.admin')

@section('title', 'Product Manager')


@section('content')
<div class="container-fluid">
    <!-- Alert Container -->
    <div class="alert-container">
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
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <h4 class="mb-4">Manajemen Produk</h4>
            
            <!-- Improved Tabs -->
            <div class="product-tabs">
                <div class="product-tab">
                    <a href="{{ route('admin.product-manager', ['tab' => 'items']) }}" 
                       class="btn {{ $activeTab == 'items' ? 'btn-primary' : '' }}">
                        <i class="fas fa-boxes me-2"></i> Items
                    </a>
                </div>
                <div class="product-tab">
                    <a href="{{ route('admin.product-manager', ['tab' => 'bahan']) }}" 
                       class="btn {{ $activeTab == 'bahan' ? 'btn-primary' : '' }}">
                        <i class="fas fa-layer-group me-2"></i> Bahan
                    </a>
                </div>
                <div class="product-tab">
                    <a href="{{ route('admin.product-manager', ['tab' => 'ukuran']) }}" 
                       class="btn {{ $activeTab == 'ukuran' ? 'btn-primary' : '' }}">
                        <i class="fas fa-rulers me-2"></i> Ukuran
                    </a>
                </div>
                <div class="product-tab">
                    <a href="{{ route('admin.product-manager', ['tab' => 'jenis']) }}" 
                       class="btn {{ $activeTab == 'jenis' ? 'btn-primary' : '' }}">
                        <i class="fas fa-tag me-2"></i> Jenis
                    </a>
                </div>
                <div class="product-tab">
                    <a href="{{ route('admin.product-manager', ['tab' => 'biaya-desain']) }}" 
                       class="btn {{ $activeTab == 'biaya-desain' ? 'btn-primary' : '' }}">
                        <i class="fas fa-paint-brush me-2"></i> Biaya Desain
                    </a>
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
                    <table class="table">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Gambar</th>
                                <th>Nama Item</th>
                                <th>Deskripsi</th>
                                <th>Harga Dasar</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($items as $key => $item)
                            <tr>
                                <td>{{ $key + 1 }}</td>
                                <td>
                                @if(isset($item['gambar']) && $item['gambar'])
                                    <img src="{{ asset('storage/' . $item['gambar']) }}" class="img-product-thumbnail" alt="{{ $item['nama_item'] }}">
                                @else
                                    <img src="{{ asset('images/no-image.png') }}" class="img-product-thumbnail" alt="No Image">
                                @endif
                                </td>
                                <td>{{ $item['nama_item'] }}</td>
                                <td>{{ $item['deskripsi'] ?? '-' }}</td>
                                <td>Rp {{ number_format($item['harga_dasar'], 0, ',', '.') }}</td>
                                <td>
                                    <button class="btn btn-info btn-action" title="Edit" 
                                            data-bs-toggle="modal" data-bs-target="#editItemModal"
                                            data-id="{{ $item['id'] }}"
                                            data-nama="{{ $item['nama_item'] }}"
                                            data-deskripsi="{{ $item['deskripsi'] }}"
                                            data-harga="{{ $item['harga_dasar'] }}"
                                            data-gambar="{{ $item['gambar'] ?? '' }}">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <form action="{{ route('admin.items.destroy', $item['id']) }}" method="POST" class="d-inline delete-form" data-entity-type="items">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-action" title="Hapus" 
                                                onclick="return confirm('Apakah Anda yakin ingin menghapus item ini? Semua hubungan dengan bahan, ukuran, dan jenis akan dihapus juga.')">
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
            <div>
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0">Daftar Bahan</h5>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addBahanModal">
                        <i class="fas fa-plus me-1"></i> Tambah Bahan
                    </button>
                </div>
                
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Bahan</th>
                                <th>Biaya Tambahan</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($bahans ?? [] as $key => $bahan)
                            <tr>
                                <td>{{ $key + 1 }}</td>
                                <td>{{ $bahan['nama_bahan'] }}</td>
                                <td>Rp {{ number_format($bahan['biaya_tambahan'], 0, ',', '.') }}</td>
                                <td>
                                    <button class="btn btn-info btn-action" title="Edit" 
                                            data-bs-toggle="modal" data-bs-target="#editBahanModal"
                                            data-id="{{ $bahan['id'] }}"
                                            data-nama="{{ $bahan['nama_bahan'] }}"
                                            data-biaya="{{ $bahan['biaya_tambahan'] }}">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <form action="{{ route('admin.bahans.destroy', $bahan['id']) }}" method="POST" class="d-inline delete-form" data-entity-type="bahan">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-action" title="Hapus" 
                                                onclick="return confirm('Apakah Anda yakin ingin menghapus bahan ini? Semua hubungan dengan item akan dihapus juga.')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center">Tidak ada data bahan</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            @elseif($activeTab == 'ukuran')
            <div>
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0">Daftar Ukuran</h5>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUkuranModal">
                        <i class="fas fa-plus me-1"></i> Tambah Ukuran
                    </button>
                </div>
                
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Size</th>
                                <th>Faktor Harga</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($ukurans ?? [] as $key => $ukuran)
                            <tr>
                                <td>{{ $key + 1 }}</td>
                                <td>{{ $ukuran['size'] }}</td>
                                <td>{{ $ukuran['faktor_harga'] }}x</td>
                                <td>
                                    <button class="btn btn-info btn-action" title="Edit" 
                                            data-bs-toggle="modal" data-bs-target="#editUkuranModal"
                                            data-id="{{ $ukuran['id'] }}"
                                            data-size="{{ $ukuran['size'] }}"
                                            data-faktor="{{ $ukuran['faktor_harga'] }}">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <form action="{{ route('admin.ukurans.destroy', $ukuran['id']) }}" method="POST" class="d-inline delete-form" data-entity-type="ukuran">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-action" title="Hapus" 
                                                onclick="return confirm('Apakah Anda yakin ingin menghapus ukuran ini? Semua hubungan dengan item akan dihapus juga.')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center">Tidak ada data ukuran</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            @elseif($activeTab == 'jenis')
            <div>
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0">Daftar Jenis</h5>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addJenisModal">
                        <i class="fas fa-plus me-1"></i> Tambah Jenis
                    </button>
                </div>
                
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Kategori</th>
                                <th>Biaya Tambahan</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($jenis_list ?? [] as $key => $jenis)
                            <tr>
                                <td>{{ $key + 1 }}</td>
                                <td>{{ $jenis['kategori'] }}</td>
                                <td>Rp {{ number_format($jenis['biaya_tambahan'], 0, ',', '.') }}</td>
                                <td>
                                    <button class="btn btn-info btn-action" title="Edit" 
                                            data-bs-toggle="modal" data-bs-target="#editJenisModal"
                                            data-id="{{ $jenis['id'] }}"
                                            data-kategori="{{ $jenis['kategori'] }}"
                                            data-biaya="{{ $jenis['biaya_tambahan'] }}">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <form action="{{ route('admin.jenis.destroy', $jenis['id']) }}" method="POST" class="d-inline delete-form" data-entity-type="jenis">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-action" title="Hapus" 
                                                onclick="return confirm('Apakah Anda yakin ingin menghapus jenis ini? Semua hubungan dengan item akan dihapus juga.')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center">Tidak ada data jenis</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            @elseif($activeTab == 'biaya-desain')
            <div>
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0">Daftar Biaya Desain</h5>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addBiayaDesainModal">
                        <i class="fas fa-plus me-1"></i> Tambah Biaya Desain
                    </button>
                </div>
                
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Biaya</th>
                                <th>Deskripsi</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($biaya_desains ?? [] as $key => $biaya)
                            <tr>
                                <td>{{ $key + 1 }}</td>
                                <td>Rp {{ number_format($biaya['biaya'], 0, ',', '.') }}</td>
                                <td>{{ $biaya['deskripsi'] ?? '-' }}</td>
                                <td>
                                    <button class="btn btn-info btn-action" title="Edit" 
                                            data-bs-toggle="modal" data-bs-target="#editBiayaDesainModal"
                                            data-id="{{ $biaya['id'] }}"
                                            data-biaya="{{ $biaya['biaya'] }}"
                                            data-deskripsi="{{ $biaya['deskripsi'] }}">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <form action="{{ route('admin.biaya-desains.destroy', $biaya['id']) }}" method="POST" class="d-inline delete-form" data-entity-type="biaya-desain">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-action" title="Hapus" 
                                                onclick="return confirm('Apakah Anda yakin ingin menghapus biaya desain ini?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center">Tidak ada data biaya desain</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @endif
            </div>
                </div>
            </div>

    <!-- Import semua modal -->
    @include('admin.product.components.modals.item_modal')
    @include('admin.product.components.modals.bahan_modal')
    @include('admin.product.components.modals.ukuran_modal')
    @include('admin.product.components.modals.jenis_modal')
    @include('admin.product.components.modals.biaya_desain_modal')
    @endsection

    @section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Script untuk mengisi form edit Item
            const editItemModal = document.getElementById('editItemModal');
            if (editItemModal) {
                editItemModal.addEventListener('show.bs.modal', function(event) {
                    const button = event.relatedTarget;
                    const id = button.getAttribute('data-id');
                    const nama = button.getAttribute('data-nama');
                    const deskripsi = button.getAttribute('data-deskripsi');
                    const harga = button.getAttribute('data-harga');
                    const gambar = button.getAttribute('data-gambar');
                    
                    // Set action URL
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
            
            // Script untuk mengisi form edit Bahan
            const editBahanModal = document.getElementById('editBahanModal');
            if (editBahanModal) {
                editBahanModal.addEventListener('show.bs.modal', function(event) {
                    const button = event.relatedTarget;
                    const id = button.getAttribute('data-id');
                    const nama = button.getAttribute('data-nama');
                    const biaya = button.getAttribute('data-biaya');
                    
                    // Set action URL
                    const form = document.getElementById('editBahanForm');
                    form.setAttribute('action', `{{ url('admin/bahans') }}/${id}`);
                    
                    // Set nilai input
                    document.getElementById('edit_nama_bahan').value = nama;
                    document.getElementById('edit_biaya_tambahan').value = biaya;
                    
                    // Set selected items
                    const selectElement = document.getElementById('edit_item_ids');
                    if (selectElement) {
                        // Bersihkan semua pilihan
                        Array.from(selectElement.options).forEach(option => {
                            option.selected = false;
                        });
                        
                        // Ambil item yang terhubung dengan bahan ini dari server
                        const url = `{{ url('api/bahans') }}/${id}`;
                        fetch(url, {
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                const itemIds = data.items ? data.items.map(item => item.id) : [];
                                
                                // Perbarui select element
                                Array.from(selectElement.options).forEach(option => {
                                    option.selected = itemIds.includes(parseInt(option.value));
                                });
                            }
                        })
                        .catch(error => console.error('Error fetching bahan items:', error));
                    }
                });
            }
            
            // Script untuk mengisi form edit Ukuran
            const editUkuranModal = document.getElementById('editUkuranModal');
            if (editUkuranModal) {
                editUkuranModal.addEventListener('show.bs.modal', function(event) {
                    const button = event.relatedTarget;
                    const id = button.getAttribute('data-id');
                    const size = button.getAttribute('data-size');
                    const faktor = button.getAttribute('data-faktor');
                    
                    // Set action URL
                    const form = document.getElementById('editUkuranForm');
                    form.setAttribute('action', `{{ url('admin/ukurans') }}/${id}`);
                    
                    // Set nilai input
                    document.getElementById('edit_size').value = size;
                    document.getElementById('edit_faktor_harga').value = faktor;
                    
                    // Set selected items
                    const selectElement = document.getElementById('edit_item_ids');
                    if (selectElement) {
                        // Bersihkan semua pilihan
                        Array.from(selectElement.options).forEach(option => {
                            option.selected = false;
                        });
                        
                        // Ambil item yang terhubung dengan ukuran ini dari server
                        const url = `{{ url('api/ukurans') }}/${id}`;
                        fetch(url, {
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                const itemIds = data.items ? data.items.map(item => item.id) : [];
                                
                                // Perbarui select element
                                Array.from(selectElement.options).forEach(option => {
                                    option.selected = itemIds.includes(parseInt(option.value));
                                });
                            }
                        })
                        .catch(error => console.error('Error fetching ukuran items:', error));
                    }
                });
            }
            
            // Script untuk mengisi form edit Jenis
            const editJenisModal = document.getElementById('editJenisModal');
            if (editJenisModal) {
                editJenisModal.addEventListener('show.bs.modal', function(event) {
                    const button = event.relatedTarget;
                    const id = button.getAttribute('data-id');
                    const kategori = button.getAttribute('data-kategori');
                    const biaya = button.getAttribute('data-biaya');
                    
                    // Set action URL
                    const form = document.getElementById('editJenisForm');
                    form.setAttribute('action', `{{ url('admin/jenis') }}/${id}`);
                    
                    // Set nilai input
                    document.getElementById('edit_kategori').value = kategori;
                    document.getElementById('edit_biaya_tambahan').value = biaya;
                    
                    // Set selected items
                    const selectElement = document.getElementById('edit_item_ids');
                    if (selectElement) {
                        // Bersihkan semua pilihan
                        Array.from(selectElement.options).forEach(option => {
                            option.selected = false;
                        });
                        
                        // Ambil item yang terhubung dengan jenis ini dari server
                        const url = `{{ url('api/jenis') }}/${id}`;
                        fetch(url, {
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                const itemIds = data.items ? data.items.map(item => item.id) : [];
                                
                                // Perbarui select element
                                Array.from(selectElement.options).forEach(option => {
                                    option.selected = itemIds.includes(parseInt(option.value));
                                });
                            }
                        })
                        .catch(error => console.error('Error fetching jenis items:', error));
                    }
                });
            }
            
            // Script untuk mengisi form edit Biaya Desain
            const editBiayaDesainModal = document.getElementById('editBiayaDesainModal');
            if (editBiayaDesainModal) {
                editBiayaDesainModal.addEventListener('show.bs.modal', function(event) {
                    const button = event.relatedTarget;
                    const id = button.getAttribute('data-id');
                    const biaya = button.getAttribute('data-biaya');
                    const deskripsi = button.getAttribute('data-deskripsi');
                    
                    // Set action URL
                    const form = document.getElementById('editBiayaDesainForm');
                    form.setAttribute('action', `{{ url('admin/biaya-desains') }}/${id}`);
                    
                    // Set nilai input
                    document.getElementById('edit_biaya').value = biaya;
                    document.getElementById('edit_deskripsi_biaya').value = deskripsi || '';
                });
            }
            
            // Preview gambar saat upload di form add dan edit item
            const setupImagePreview = (inputId, previewContainerId) => {
                const input = document.getElementById(inputId);
                if (input) {
                    input.addEventListener('change', function() {
                        if (this.files && this.files[0]) {
                            const reader = new FileReader();
                            reader.onload = function(e) {
                                const previewContainer = document.getElementById(previewContainerId) || 
                                                        document.querySelector(`#${inputId}`).parentElement;
                                
                                const previewDiv = document.createElement('div');
                                previewDiv.classList.add('mt-2', 'text-center');
                                previewDiv.innerHTML = `
                                    <img src="${e.target.result}" class="img-thumbnail" style="max-height: 150px">
                                    <p class="small text-muted mt-1">Preview gambar</p>
                                `;
                                
                                // Hapus preview yang sudah ada (jika ada)
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
            };
            
            // Setup image preview untuk add dan edit item
            setupImagePreview('gambar', null);
            setupImagePreview('edit_gambar', 'current_image');
        });
    </script>
    @endsection