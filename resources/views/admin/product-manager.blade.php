@extends('admin.layout.admin')

@section('title', 'Kelola Produk')

@section('styles')
<style>
    .tab-content {
        padding: 20px 0;
    }
    
    .action-buttons .btn {
        margin-right: 5px;
    }
    
    .card-header {
        background-color: #fff;
        border-bottom: 1px solid rgba(0,0,0,0.125);
    }
    
    .nav-tabs .nav-link {
        color: #6c757d;
    }
    
    .nav-tabs .nav-link.active {
        color: #007bff;
        font-weight: 500;
    }
    
    .table-responsive {
        overflow-x: auto;
    }
    
    .img-thumbnail {
        object-fit: cover;
        height: 60px;
    }
    
    .badge {
        font-size: 0.8rem;
        padding: 0.4em 0.6em;
    }
</style>
@endsection

@section('content')
<!-- Row 1: Welcome & Stats -->
<div class="row mb-4">
    <div class="col-md-6">
        <div class="card h-100 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h5 class="card-title">Pengelolaan Produk</h5>
                        <p class="card-text text-muted">Kelola produk, bahan, ukuran, dan jenis sesuai kebutuhan Anda</p>
                    </div>
                    <div class="d-flex align-items-center">
                        <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                            <i class="fas fa-boxes text-white"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card h-100 shadow-sm">
            <div class="card-body">
                <div class="row row-cols-1 row-cols-md-3 g-3">
                    <div class="col">
                        <div class="card border-0 bg-light h-100">
                            <div class="card-body text-center">
                                <h3 class="text-primary mb-2" id="totalItems">?</h3>
                                <p class="card-text mb-0">Produk</p>
                                <small class="text-muted">Total</small>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card border-0 bg-light h-100">
                            <div class="card-body text-center">
                                <h3 class="text-success mb-2" id="totalBahans">?</h3>
                                <p class="card-text mb-0">Bahan</p>
                                <small class="text-muted">Total</small>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card border-0 bg-light h-100">
                            <div class="card-body text-center">
                                <h3 class="text-warning mb-2" id="totalUkurans">?</h3>
                                <p class="card-text mb-0">Ukuran</p>
                                <small class="text-muted">Total</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

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

<div class="card shadow-sm">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0">Data Produk</h5>
        <button id="refreshData" class="btn btn-sm btn-outline-secondary">
            <i class="fas fa-sync-alt me-1"></i> Refresh Data
        </button>
    </div>
    
    <div class="card-body">
        <!-- Nav tabs -->
        <ul class="nav nav-tabs" id="productManagerTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <a class="nav-link {{ $activeTab == 'items' ? 'active' : '' }}" 
                   href="{{ route('admin.product-manager', ['tab' => 'items']) }}">
                    <i class="fas fa-box me-1"></i> Produk
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link {{ $activeTab == 'bahans' ? 'active' : '' }}" 
                   href="{{ route('admin.product-manager', ['tab' => 'bahans']) }}">
                    <i class="fas fa-layer-group me-1"></i> Bahan
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link {{ $activeTab == 'ukurans' ? 'active' : '' }}" 
                   href="{{ route('admin.product-manager', ['tab' => 'ukurans']) }}">
                    <i class="fas fa-ruler-combined me-1"></i> Ukuran
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link {{ $activeTab == 'jenis' ? 'active' : '' }}" 
                   href="{{ route('admin.product-manager', ['tab' => 'jenis']) }}">
                    <i class="fas fa-tags me-1"></i> Jenis
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link {{ $activeTab == 'biaya-desain' ? 'active' : '' }}" 
                   href="{{ route('admin.product-manager', ['tab' => 'biaya-desain']) }}">
                    <i class="fas fa-paint-brush me-1"></i> Biaya Desain
                </a>
            </li>
        </ul>
        
        <!-- Tab content -->
        <div class="tab-content mt-3">
            @if($activeTab == 'items')
            <div class="tab-pane fade show active" id="items" role="tabpanel">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addItemModal">
                        <i class="fas fa-plus me-1"></i> Tambah Produk Baru
                    </button>
                    
                    <div class="d-flex">
                        <form class="me-2" action="{{ route('admin.product-manager', ['tab' => 'items']) }}" method="GET">
                            <div class="input-group">
                                <input type="text" name="search" class="form-control" placeholder="Cari produk..." value="{{ request('search') }}">
                                <button type="submit" class="btn btn-outline-secondary">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </form>
                        
                        <div class="dropdown">
                            <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="itemsPerPageDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                {{ request('per_page', 10) }} / halaman
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="itemsPerPageDropdown">
                                <li><a class="dropdown-item" href="{{ route('admin.product-manager', ['tab' => 'items', 'per_page' => 10, 'search' => request('search')]) }}">10 / halaman</a></li>
                                <li><a class="dropdown-item" href="{{ route('admin.product-manager', ['tab' => 'items', 'per_page' => 25, 'search' => request('search')]) }}">25 / halaman</a></li>
                                <li><a class="dropdown-item" href="{{ route('admin.product-manager', ['tab' => 'items', 'per_page' => 50, 'search' => request('search')]) }}">50 / halaman</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover">
                        <thead class="table-light">
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
                            @php
                                $itemsData = [];
                                $totalItems = 0;
                                if (isset($items)) {
                                    if (is_array($items)) {
                                        if (isset($items['data'])) {
                                            $itemsData = $items['data'];
                                            $totalItems = $items['total'] ?? count($itemsData);
                                        } else {
                                            $itemsData = $items;
                                            $totalItems = count($itemsData);
                                        }
                                    } elseif (is_object($items) && method_exists($items, 'toArray')) {
                                        $items = $items->toArray();
                                        if (isset($items['data'])) {
                                            $itemsData = $items['data'];
                                            $totalItems = $items['total'] ?? count($itemsData);
                                        } else {
                                            $itemsData = $items;
                                            $totalItems = count($itemsData);
                                        }
                                    } elseif (is_object($items) && isset($items['data'])) {
                                        $itemsData = $items['data'];
                                        $totalItems = $items['total'] ?? count($itemsData);
                                    }
                                }
                            @endphp
                            

                            @if(!empty($itemsData))
                                @foreach($itemsData as $index => $item)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td class="text-center">
                                        @if(isset($item['gambar']) && $item['gambar'])
                                            <img src="{{ asset('storage/' . $item['gambar']) }}" alt="{{ $item['nama_item'] ?? 'Produk' }}" class="img-thumbnail">
                                        @else
                                            <div class="text-center p-3 bg-light rounded">
                                                <i class="fas fa-image text-muted fa-lg"></i>
                                            </div>
                                        @endif
                                    </td>
                                    <td>{{ $item['nama_item'] ?? '-' }}</td>
                                    <td>{{ $item['deskripsi'] ?? '-' }}</td>
                                    <td>Rp {{ number_format($item['harga_dasar'] ?? 0, 0, ',', '.') }}</td>
                                    <td>
                                        <div class="action-buttons">
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
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="6" class="text-center py-3">
                                        <div class="py-4">
                                            <i class="fas fa-box fa-3x text-muted mb-3"></i>
                                            <p>Tidak ada data produk</p>
                                            <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addItemModal">
                                                <i class="fas fa-plus me-1"></i> Tambah Produk
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                @if(isset($items['current_page']))
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div>
                        Menampilkan {{ $items['from'] ?? 0 }} sampai {{ $items['to'] ?? 0 }} dari {{ $items['total'] ?? 0 }} data
                    </div>
                    <nav aria-label="Page navigation">
                        <ul class="pagination">
                            <!-- Previous Page Link -->
                            @if($items['current_page'] > 1)
                            <li class="page-item">
                                <a class="page-link" href="{{ route('admin.product-manager', ['tab' => 'items', 'page' => $items['current_page'] - 1, 'per_page' => request('per_page', 10), 'search' => request('search')]) }}" aria-label="Previous">
                                    <span aria-hidden="true">&laquo;</span>
                                </a>
                            </li>
                            @else
                            <li class="page-item disabled">
                                <span class="page-link" aria-hidden="true">&laquo;</span>
                            </li>
                            @endif
                            
                            <!-- Pagination Elements -->
                            @for($i = 1; $i <= $items['last_page']; $i++)
                                <li class="page-item {{ $items['current_page'] == $i ? 'active' : '' }}">
                                    <a class="page-link" href="{{ route('admin.product-manager', ['tab' => 'items', 'page' => $i, 'per_page' => request('per_page', 10), 'search' => request('search')]) }}">{{ $i }}</a>
                                </li>
                            @endfor
                            
                            <!-- Next Page Link -->
                            @if($items['current_page'] < $items['last_page'])
                            <li class="page-item">
                                <a class="page-link" href="{{ route('admin.product-manager', ['tab' => 'items', 'page' => $items['current_page'] + 1, 'per_page' => request('per_page', 10), 'search' => request('search')]) }}" aria-label="Next">
                                    <span aria-hidden="true">&raquo;</span>
                                </a>
                            </li>
                            @else
                            <li class="page-item disabled">
                                <span class="page-link" aria-hidden="true">&raquo;</span>
                            </li>
                            @endif
                        </ul>
                    </nav>
                </div>
                @endif
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Modals -->
@include('admin.product-manager.modals.item-modals')

@endsection


