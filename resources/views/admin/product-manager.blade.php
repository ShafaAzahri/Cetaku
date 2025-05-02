{{-- resources/views/admin/product-manager.blade.php --}}
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
            <div class="tab-content">
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
                                @php
                                $itemsArray = isset($items) ? (is_array($items) ? $items : (is_object($items) && method_exists($items, 'toArray') ? $items->toArray() : [])) : [];
                                @endphp
                                
                                @if(!empty($itemsArray))
                                    @foreach($itemsArray as $index => $item)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>
                                            @if(isset($item['gambar']) && $item['gambar'])
                                                <img src="{{ asset('storage/' . $item['gambar']) }}" alt="{{ $item['nama_item'] ?? 'Produk' }}" class="img-thumbnail" width="80">
                                            @else
                                                <div class="text-center"><i class="fas fa-image text-muted"></i></div>
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
                                        <td colspan="6" class="text-center">Tidak ada data produk</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
                @endif
                
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
                                @php
                                $bahansArray = isset($bahans) ? (is_array($bahans) ? $bahans : (is_object($bahans) && method_exists($bahans, 'toArray') ? $bahans->toArray() : [])) : [];
                                @endphp
                                
                                @if(!empty($bahansArray))
                                    @foreach($bahansArray as $index => $bahan)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $bahan['nama_bahan'] ?? '-' }}</td>
                                        <td>Rp {{ number_format($bahan['biaya_tambahan'] ?? 0, 0, ',', '.') }}</td>
                                        <td>
                                            @php
                                            $isAvailable = isset($bahan['is_available']) ? (bool)$bahan['is_available'] : true;
                                            @endphp
                                            <span class="badge {{ $isAvailable ? 'bg-success' : 'bg-danger' }}">
                                                {{ $isAvailable ? 'Tersedia' : 'Tidak Tersedia' }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="action-buttons">
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
                
                <!-- Ukurans Tab Content -->
                @if($activeTab == 'ukurans')
                <div class="tab-pane fade show active" id="ukurans" role="tabpanel">
                    <!-- Similar structure as above -->
                </div>
                @endif
                
                <!-- Jenis Tab Content -->
                @if($activeTab == 'jenis')
                <div class="tab-pane fade show active" id="jenis" role="tabpanel">
                    <!-- Similar structure as above -->
                </div>
                @endif
                
                <!-- Biaya Desain Tab Content -->
                @if($activeTab == 'biaya-desain')
                <div class="tab-pane fade show active" id="biaya-desain" role="tabpanel">
                    <!-- Similar structure as above -->
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Modals -->
@include('admin.product-manager.modals.item-modals')

@if($activeTab == 'bahans')
    @include('admin.product-manager.modals.bahan-modals')
@endif

@if($activeTab == 'ukurans')
    @include('admin.product-manager.modals.ukuran-modals')
@endif

@if($activeTab == 'jenis')
    @include('admin.product-manager.modals.jenis-modals')
@endif

@if($activeTab == 'biaya-desain')
    @include('admin.product-manager.modals.biaya-desain-modals')
@endif

@endsection