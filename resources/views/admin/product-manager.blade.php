@extends('admin.layout.admin')

@section('title', 'Kelola Produk')

@section('content')
<div class="container-fluid">
    <!-- Alert untuk menampilkan pesan sukses/error -->
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

    @if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <ul class="mb-0">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif
    
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">Kelola Produk</h5>
        </div>
        
        <div class="card-body">
            <!-- Nav tabs -->
            <ul class="nav nav-tabs mb-3" id="productManagerTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <a class="nav-link {{ $activeTab == 'items' ? 'active' : '' }}" href="{{ route('admin.product-manager', ['tab' => 'items']) }}" role="tab">
                        <i class="fas fa-box me-1"></i> Produk
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link {{ $activeTab == 'bahans' ? 'active' : '' }}" href="{{ route('admin.product-manager', ['tab' => 'bahans']) }}" role="tab">
                        <i class="fas fa-layer-group me-1"></i> Bahan
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link {{ $activeTab == 'ukurans' ? 'active' : '' }}" href="{{ route('admin.product-manager', ['tab' => 'ukurans']) }}" role="tab">
                        <i class="fas fa-ruler-combined me-1"></i> Ukuran
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link {{ $activeTab == 'jenis' ? 'active' : '' }}" href="{{ route('admin.product-manager', ['tab' => 'jenis']) }}" role="tab">
                        <i class="fas fa-tags me-1"></i> Jenis
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link {{ $activeTab == 'biaya-desain' ? 'active' : '' }}" href="{{ route('admin.product-manager', ['tab' => 'biaya-desain']) }}" role="tab">
                        <i class="fas fa-paint-brush me-1"></i> Biaya Desain
                    </a>
                </li>
            </ul>
            
            <!-- Tab panes -->
            <div class="tab-content">
                <!-- Items Tab -->
                @if($activeTab == 'items')
                <div class="tab-pane fade show active" id="items" role="tabpanel">
                    <div class="mb-3">
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addItemModal">
                            <i class="fas fa-plus"></i> Tambah Produk Baru
                        </button>
                    </div>
                    
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover">
                            <thead>
                                <tr>
                                    <th width="5%">No</th>
                                    <th width="15%">Gambar</th>
                                    <th width="25%">Nama Produk</th>
                                    <th width="30%">Deskripsi</th>
                                    <th width="15%">Harga Dasar</th>
                                    <th width="10%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(isset($items) && is_array($items) && count($items) > 0)
                                    @foreach($items as $index => $item)
                                    <tr>
                                        <td>{{ (int)$index + 1 }}</td>
                                        <td>
                                            @if(is_array($item) && isset($item['gambar']) && $item['gambar'])
                                                <img src="{{ asset('storage/' . $item['gambar']) }}" alt="{{ $item['nama_item'] ?? 'Produk' }}" class="img-thumbnail" width="80">
                                            @else
                                                <div class="text-center"><i class="fas fa-image text-muted"></i></div>
                                            @endif
                                        </td>
                                        <td>{{ is_array($item) && isset($item['nama_item']) ? $item['nama_item'] : '-' }}</td>
                                        <td>{{ is_array($item) && isset($item['deskripsi']) ? $item['deskripsi'] : '-' }}</td>
                                        <td>Rp {{ is_array($item) && isset($item['harga_dasar']) ? number_format($item['harga_dasar'], 0, ',', '.') : '0' }}</td>
                                        <td>
                                            <div class="action-buttons">
                                                @if(is_array($item) && isset($item['id']))
                                                <a href="{{ route('admin.items.edit', $item['id']) }}" class="btn btn-sm btn-info">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('admin.items.destroy', $item['id']) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus produk ini?')">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="6" class="text-center">Tidak ada data produk</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
                @endif
                
                <!-- Bahans Tab -->
                @if($activeTab == 'bahans')
                <div class="tab-pane fade show active" id="bahans" role="tabpanel">
                    <div class="mb-3">
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addBahanModal">
                            <i class="fas fa-plus"></i> Tambah Bahan Baru
                        </button>
                    </div>
                    
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover">
                            <thead>
                                <tr>
                                    <th width="5%">No</th>
                                    <th width="30%">Nama Bahan</th>
                                    <th width="25%">Biaya Tambahan</th>
                                    <th width="10%">Status</th>
                                    <th width="10%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(isset($bahans) && is_array($bahans) && count($bahans) > 0)
                                    @foreach($bahans as $index => $bahan)
                                    <tr>
                                        <td>{{ (int)$index + 1 }}</td>
                                        <td>{{ is_array($bahan) && isset($bahan['nama_bahan']) ? $bahan['nama_bahan'] : '-' }}</td>
                                        <td>Rp {{ is_array($bahan) && isset($bahan['biaya_tambahan']) ? number_format($bahan['biaya_tambahan'], 0, ',', '.') : '0' }}</td>
                                        <td>
                                            @if(is_array($bahan) && isset($bahan['is_available']))
                                                <span class="badge {{ $bahan['is_available'] ? 'bg-success' : 'bg-danger' }}">
                                                    {{ $bahan['is_available'] ? 'Tersedia' : 'Tidak Tersedia' }}
                                                </span>
                                            @else
                                                <span class="badge bg-secondary">Tidak Diketahui</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="action-buttons">
                                                @if(is_array($bahan) && isset($bahan['id']))
                                                <a href="{{ route('admin.bahans.edit', $bahan['id']) }}" class="btn btn-sm btn-info">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('admin.bahans.destroy', $bahan['id']) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus bahan ini?')">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="5" class="text-center">Tidak ada data bahan</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
                @endif
                
                <!-- Ukurans Tab -->
                @if($activeTab == 'ukurans')
                <div class="tab-pane fade show active" id="ukurans" role="tabpanel">
                    <div class="mb-3">
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUkuranModal">
                            <i class="fas fa-plus"></i> Tambah Ukuran Baru
                        </button>
                    </div>
                    
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover">
                            <thead>
                                <tr>
                                    <th width="5%">No</th>
                                    <th width="30%">Ukuran</th>
                                    <th width="25%">Faktor Harga</th>
                                    <th width="10%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(isset($ukurans) && is_array($ukurans) && count($ukurans) > 0)
                                    @foreach($ukurans as $index => $ukuran)
                                    <tr>
                                        <td>{{ (int)$index + 1 }}</td>
                                        <td>{{ is_array($ukuran) && isset($ukuran['size']) ? $ukuran['size'] : '-' }}</td>
                                        <td>{{ is_array($ukuran) && isset($ukuran['faktor_harga']) ? $ukuran['faktor_harga'] . 'x' : '-' }}</td>
                                        <td>
                                            <div class="action-buttons">
                                                @if(is_array($ukuran) && isset($ukuran['id']))
                                                <a href="{{ route('admin.ukurans.edit', $ukuran['id']) }}" class="btn btn-sm btn-info">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('admin.ukurans.destroy', $ukuran['id']) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus ukuran ini?')">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="4" class="text-center">Tidak ada data ukuran</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
                @endif
                
                <!-- Jenis Tab -->
                @if($activeTab == 'jenis')
                <div class="tab-pane fade show active" id="jenis" role="tabpanel">
                    <div class="mb-3">
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addJenisModal">
                            <i class="fas fa-plus"></i> Tambah Jenis Baru
                        </button>
                    </div>
                    
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover">
                            <thead>
                                <tr>
                                    <th width="5%">No</th>
                                    <th width="30%">Kategori</th>
                                    <th width="25%">Biaya Tambahan</th>
                                    <th width="10%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(isset($jenis) && is_array($jenis) && count($jenis) > 0)
                                    @foreach($jenis as $index => $j)
                                    <tr>
                                        <td>{{ (int)$index + 1 }}</td>
                                        <td>{{ is_array($j) && isset($j['kategori']) ? $j['kategori'] : '-' }}</td>
                                        <td>Rp {{ is_array($j) && isset($j['biaya_tambahan']) ? number_format($j['biaya_tambahan'], 0, ',', '.') : '0' }}</td>
                                        <td>
                                            <div class="action-buttons">
                                                @if(is_array($j) && isset($j['id']))
                                                <a href="{{ route('admin.jenis.edit', $j['id']) }}" class="btn btn-sm btn-info">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('admin.jenis.destroy', $j['id']) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus jenis ini?')">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="4" class="text-center">Tidak ada data jenis</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
                @endif
                
                <!-- Biaya Desain Tab -->
                @if($activeTab == 'biaya-desain')
                <div class="tab-pane fade show active" id="biaya-desain" role="tabpanel">
                    <div class="mb-3">
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addBiayaDesainModal">
                            <i class="fas fa-plus"></i> Tambah Biaya Desain Baru
                        </button>
                    </div>
                    
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover">
                            <thead>
                                <tr>
                                    <th width="5%">No</th>
                                    <th width="30%">Biaya</th>
                                    <th width="55%">Deskripsi</th>
                                    <th width="10%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(isset($biayaDesain) && is_array($biayaDesain) && count($biayaDesain) > 0)
                                    @foreach($biayaDesain as $index => $biaya)
                                    <tr>
                                        <td>{{ (int)$index + 1 }}</td>
                                        <td>Rp {{ is_array($biaya) && isset($biaya['biaya']) ? number_format($biaya['biaya'], 0, ',', '.') : '0' }}</td>
                                        <td>{{ is_array($biaya) && isset($biaya['deskripsi']) ? $biaya['deskripsi'] : '-' }}</td>
                                        <td>
                                            <div class="action-buttons">
                                                @if(is_array($biaya) && isset($biaya['id']))
                                                <a href="{{ route('admin.biaya-desain.edit', $biaya['id']) }}" class="btn btn-sm btn-info">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('admin.biaya-desain.destroy', $biaya['id']) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus biaya desain ini?')">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="4" class="text-center">Tidak ada data biaya desain</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Modals -->
@include('admin.product-manager.modals.item-modals')
@include('admin.product-manager.modals.bahan-modals')
@include('admin.product-manager.modals.ukuran-modals')
@include('admin.product-manager.modals.jenis-modals')
@include('admin.product-manager.modals.biaya-desain-modals')
@endsection

@section('styles')
<style>
    .card {
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.05);
    }
    
    .table th, .table td {
        vertical-align: middle;
    }
    
    .table img {
        max-width: 80px;
        height: auto;
        border-radius: 4px;
    }
    
    .action-buttons {
        display: flex;
        gap: 5px;
    }
    
    .nav-tabs .nav-link {
        color: #495057;
        cursor: pointer;
    }
    
    .nav-tabs .nav-link.active {
        font-weight: 600;
        background-color: #f8f9fa;
        border-color: #dee2e6 #dee2e6 #f8f9fa;
    }
</style>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize Bootstrap components
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });
    });
</script>
@endsection