@extends('admin.layout.admin')

@section('title', 'Kelola Produk')

@section('content')
<div class="container-fluid">
    <!-- Alert container untuk menampilkan pesan -->
    <div id="alert-container"></div>
    
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">Kelola Produk</h5>
        </div>
        
        <div class="card-body">
            <!-- Nav tabs -->
            <ul class="nav nav-tabs mb-3" id="productManagerTabs" role="tablist">
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
                <div class="tab-pane fade show active" id="items" role="tabpanel" aria-labelledby="items-tab">
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
                            <tbody id="items-tbody">
                                <tr>
                                    <td colspan="6" class="text-center">Loading...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <!-- Bahans Tab -->
                <div class="tab-pane fade" id="bahans" role="tabpanel" aria-labelledby="bahans-tab">
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
                                    <th width="30%">Item Produk</th>
                                    <th width="30%">Nama Bahan</th>
                                    <th width="25%">Biaya Tambahan</th>
                                    <th width="10%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="bahans-tbody">
                                <tr>
                                    <td colspan="5" class="text-center">Loading...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <!-- Ukurans Tab -->
                <div class="tab-pane fade" id="ukurans" role="tabpanel" aria-labelledby="ukurans-tab">
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
                                    <th width="30%">Item Produk</th>
                                    <th width="30%">Ukuran</th>
                                    <th width="25%">Faktor Harga</th>
                                    <th width="10%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="ukurans-tbody">
                                <tr>
                                    <td colspan="5" class="text-center">Loading...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <!-- Jenis Tab -->
                <div class="tab-pane fade" id="jenis" role="tabpanel" aria-labelledby="jenis-tab">
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
                                    <th width="30%">Item Produk</th>
                                    <th width="30%">Kategori</th>
                                    <th width="25%">Biaya Tambahan</th>
                                    <th width="10%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="jenis-tbody">
                                <tr>
                                    <td colspan="5" class="text-center">Loading...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <!-- Biaya Desain Tab -->
                <div class="tab-pane fade" id="biaya-desain" role="tabpanel" aria-labelledby="biaya-desain-tab">
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
                            <tbody id="biaya-desain-tbody">
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
<!-- Pastikan jQuery dimuat sebelum Bootstrap -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Bootstrap Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<!-- Axios untuk API calls -->
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<!-- Product management script -->
<script src="{{ asset('js/product.js') }}"></script>

<script>
    // Debugging script untuk memastikan semua dimuat dengan benar
    $(document).ready(function() {
        console.log('Document ready');
        console.log('Bootstrap loaded:', typeof bootstrap !== 'undefined');
        console.log('Product JS loaded:', typeof initProductManagement !== 'undefined');
        
        // Cek apakah tab sudah terender
        console.log('Tab count:', $('#productManagerTabs .nav-link').length);
        
        // Test manual tab switching
        $('#productManagerTabs .nav-link').on('click', function(e) {
            console.log('Tab clicked:', $(this).attr('id'));
        });
    });
</script>
@endsection