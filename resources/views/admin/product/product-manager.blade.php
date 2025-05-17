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

            <!-- Tabs -->
            <div class="product-tabs mb-4">
                <div class="product-tab">
                    <a href="{{ route('admin.product-manager', ['tab' => 'kategori']) }}" class="btn {{ $activeTab == 'kategori' ? 'btn-primary' : '' }}">
                        <i class="fas fa-folder me-2"></i> Kategori
                    </a>
                </div>
                <div class="product-tab">
                    <a href="{{ route('admin.product-manager', ['tab' => 'items']) }}" class="btn {{ $activeTab == 'items' ? 'btn-primary' : '' }}">
                        <i class="fas fa-boxes me-2"></i> Items
                    </a>
                </div>
                <div class="product-tab">
                    <a href="{{ route('admin.product-manager', ['tab' => 'bahan']) }}" class="btn {{ $activeTab == 'bahan' ? 'btn-primary' : '' }}">
                        <i class="fas fa-layer-group me-2"></i> Bahan
                    </a>
                </div>
                <div class="product-tab">
                    <a href="{{ route('admin.product-manager', ['tab' => 'ukuran']) }}" class="btn {{ $activeTab == 'ukuran' ? 'btn-primary' : '' }}">
                        <i class="fas fa-rulers me-2"></i> Ukuran
                    </a>
                </div>
                <div class="product-tab">
                    <a href="{{ route('admin.product-manager', ['tab' => 'jenis']) }}" class="btn {{ $activeTab == 'jenis' ? 'btn-primary' : '' }}">
                        <i class="fas fa-tag me-2"></i> Jenis
                    </a>
                </div>
                <div class="product-tab">
                    <a href="{{ route('admin.product-manager', ['tab' => 'biaya-desain']) }}" class="btn {{ $activeTab == 'biaya-desain' ? 'btn-primary' : '' }}">
                        <i class="fas fa-paint-brush me-2"></i> Biaya Desain
                    </a>
                </div>
            </div>

            <!-- Tab Content -->
            @if($activeTab == 'kategori')
                @include('admin.product.components.tabs.kategori')
            @elseif($activeTab == 'items')
                @include('admin.product.components.tabs.item')
            @elseif($activeTab == 'bahan')
                @include('admin.product.components.tabs.bahan')
            @elseif($activeTab == 'ukuran')
                @include('admin.product.components.tabs.ukuran')
            @elseif($activeTab == 'jenis')
                @include('admin.product.components.tabs.jenis')
            @elseif($activeTab == 'biaya-desain')
                @include('admin.product.components.tabs.biaya-desain')
            @endif

        </div>
    </div>
</div>

<!-- Import semua modal -->
@include('admin.product.components.modals.kategori_modal')
@include('admin.product.components.modals.item_modal')
@include('admin.product.components.modals.bahan_modal')
@include('admin.product.components.modals.ukuran_modal')
@include('admin.product.components.modals.jenis_modal')
@include('admin.product.components.modals.biaya_desain_modal')

@endsection

@section('scripts')
<script src="{{ asset('js/admin/product-manager/scripts/kategori.js') }}"></script>
<script src="{{ asset('js/admin/product-manager/scripts/item.js') }}"></script>
<script src="{{ asset('js/admin/product-manager/scripts/bahan.js') }}"></script>
<script src="{{ asset('js/admin/product-manager/scripts/ukuran.js') }}"></script>
<script src="{{ asset('js/admin/product-manager/scripts/jenis.js') }}"></script>
<script src="{{ asset('js/admin/product-manager/scripts/biaya-desain.js') }}"></script>
@endsection
