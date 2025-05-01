```php
@extends('admin.layout.admin')

@section('title', 'Kelola Produk')

@section('styles')
<style>
    .add-button {
        margin-bottom: 1.5rem;
    }
    
    .item-actions, .bahan-actions, .ukuran-actions, .jenis-actions, .desain-actions {
        display: flex;
        gap: 0.5rem;
    }
    
    .table th, .table td {
        vertical-align: middle;
    }
    
    .nav-tabs {
        margin-bottom: 1.5rem;
    }
    
    .tab-content {
        padding-top: 1rem;
    }
    
    #current_image_container {
        margin-bottom: 1rem;
    }
    
    #current_image {
        max-height: 150px;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Placeholder untuk alert messages -->
    <div id="alert-container"></div>
    
    <div class="card">
        <div class="card-header bg-white">
            <h5 class="mb-0">Kelola Produk</h5>
        </div>
        
        <div class="card-body">
            <!-- Nav tabs -->
            <ul class="nav nav-tabs" id="productManagerTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="items-tab" data-bs-toggle="tab" data-bs-target="#items" type="button" role="tab" aria-controls="items" aria-selected="true">
                        <i class="fas fa-box me-1"></i> Produk
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="bahans-tab" data-bs-toggle="tab" data-bs-target="#bahans" type="button" role="tab" aria-controls="bahans" aria-selected="false">
                        <i class="fas fa-layer-group me-1"></i> Bahan
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="ukurans-tab" data-bs-toggle="tab" data-bs-target="#ukurans" type="button" role="tab" aria-controls="ukurans" aria-selected="false">
                        <i class="fas fa-ruler-combined me-1"></i> Ukuran
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="jenis-tab" data-bs-toggle="tab" data-bs-target="#jenis" type="button" role="tab" aria-controls="jenis" aria-selected="false">
                        <i class="fas fa-tags me-1"></i> Jenis
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="biaya-desain-tab" data-bs-toggle="tab" data-bs-target="#biaya-desain" type="button" role="tab" aria-controls="biaya-desain" aria-selected="false">
                        <i class="fas fa-paint-brush me-1"></i> Biaya Desain
                    </button>
                </li>
            </ul>
            
            <!-- Tab panes -->
            <div class="tab-content">
                <!-- Items Tab -->
                <div class="tab-pane active" id="items" role="tabpanel" aria-labelledby="items-tab">
                    <button type="button" class="btn btn-primary add-button" data-bs-toggle="modal" data-bs-target="#addItemModal">
                        <i class="fas fa-plus"></i> Tambah Produk Baru
                    </button>
                    
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Gambar</th>
                                    <th>Nama Produk</th>
                                    <th>Deskripsi</th>
                                    <th>Harga Dasar</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td colspan="6" class="text-center">Loading...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <!-- Bahans Tab -->
                <div class="tab-pane" id="bahans" role="tabpanel" aria-labelledby="bahans-tab">
                    <button type="button" class="btn btn-primary add-button" data-bs-toggle="modal" data-bs-target="#addBahanModal">
                        <i class="fas fa-plus"></i> Tambah Bahan Baru
                    </button>
                    
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Item Produk</th>
                                    <th>Nama Bahan</th>
                                    <th>Biaya Tambahan</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td colspan="5" class="text-center">Loading...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <!-- Ukurans Tab -->
                <div class="tab-pane" id="ukurans" role="tabpanel" aria-labelledby="ukurans-tab">
                    <button type="button" class="btn btn-primary add-button" data-bs-toggle="modal" data-bs-target="#addUkuranModal">
                        <i class="fas fa-plus"></i> Tambah Ukuran Baru
                    </button>
                    
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Item Produk</th>
                                    <th>Ukuran</th>
                                    <th>Faktor Harga</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td colspan="5" class="text-center">Loading...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <!-- Jenis Tab -->
                <div class="tab-pane" id="jenis" role="tabpanel" aria-labelledby="jenis-tab">
                    <button type="button" class="btn btn-primary add-button" data-bs-toggle="modal" data-bs-target="#addJenisModal">
                        <i class="fas fa-plus"></i> Tambah Jenis Baru
                    </button>
                    
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Item Produk</th>
                                    <th>Kategori</th>
                                    <th>Biaya Tambahan</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td colspan="5" class="text-center">Loading...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <!-- Biaya Desain Tab -->
                <div class="tab-pane" id="biaya-desain" role="tabpanel" aria-labelledby="biaya-desain-tab">
                    <button type="button" class="btn btn-primary add-button" data-bs-toggle="modal" data-bs-target="#addBiayaDesainModal">
                        <i class="fas fa-plus"></i> Tambah Biaya Desain Baru
                    </button>
                    
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Biaya</th>
                                    <th>Deskripsi</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td colspan="4" class="text-center">Loading...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
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

@section('scripts')
@section('scripts')
<script>
    // Check authentication before loading content
    document.addEventListener('DOMContentLoaded', function() {
        // Define base URL for assets
        var baseUrl = "{{ url('/') }}";
        
        // Check if API token exists
        if (!localStorage.getItem('api_token')) {
            // Redirect to login page if token doesn't exist
            window.location.href = "{{ route('login') }}";
        }
    });
</script>
<script src="{{ asset('js/product.js') }}"></script>
@endsection
<script src="{{ asset('js/product.js') }}"></script>
<script src="{{ asset('js/auth.js') }}"></script>
<script src="{{ asset('js/check-auth.js') }}"></script>
@endsection
```