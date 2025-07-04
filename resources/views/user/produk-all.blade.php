@extends('user.layouts.app')

@section('title', 'Semua Produk - CETAKU Percetakan Digital')

@section('content')
<!-- Breadcrumb -->
<div class="bg-light py-3 fw-medium">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="/" class="text-decoration-none text-secondary">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">Semua Produk</li>
            </ol>
        </nav>
    </div>
</div>

<!-- Products Section -->
<section class="products-section py-5">
    <div class="container">
        <h2 class="section-title text-center mb-4">Semua Produk Kami</h2>
        
        @if(isset($error))
            <div class="alert alert-danger" role="alert">
                {{ $error }}
            </div>
        @endif
        
        <!-- Filter and Sort -->
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
            <div class="filter-buttons d-none d-md-block">
                <a href="{{ route('produk-all') }}"
                   class="btn btn-sm btn-outline-secondary me-2 {{ is_null($kategoriNama) ? 'active' : '' }}">
                    Semua
                </a>

                @foreach ($kategoris as $kategori)
                    <a href="{{ route('produk-all', array_merge(request()->query(), ['kategori' => $kategori['nama_kategori']])) }}"
                       class="btn btn-sm btn-outline-secondary me-2 {{ $kategoriNama === $kategori['nama_kategori'] ? 'active' : '' }}">
                        {{ $kategori['nama_kategori'] }}
                    </a>
                @endforeach
            </div>

            <!-- Sort Dropdown -->
            <div class="dropdown">
                <button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    @switch($currentSort)
                        @case('harga_rendah') Harga: Rendah ke Tinggi @break
                        @case('harga_tinggi') Harga: Tinggi ke Rendah @break
                        @case('nama_az') Nama: A-Z @break
                        @case('nama_za') Nama: Z-A @break
                        @case('terlaris') Terlaris @break
                        @default Terbaru
                    @endswitch
                </button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item sort-option {{ $currentSort == 'terbaru' ? 'active' : '' }}" href="{{ request()->fullUrlWithQuery(['sort' => 'terbaru']) }}">Terbaru</a></li>
                    <li><a class="dropdown-item sort-option {{ $currentSort == 'harga_rendah' ? 'active' : '' }}" href="{{ request()->fullUrlWithQuery(['sort' => 'harga_rendah']) }}">Harga: Rendah ke Tinggi</a></li>
                    <li><a class="dropdown-item sort-option {{ $currentSort == 'harga_tinggi' ? 'active' : '' }}" href="{{ request()->fullUrlWithQuery(['sort' => 'harga_tinggi']) }}">Harga: Tinggi ke Rendah</a></li>
                    <li><a class="dropdown-item sort-option {{ $currentSort == 'terlaris' ? 'active' : '' }}" href="{{ request()->fullUrlWithQuery(['sort' => 'terlaris']) }}">Terlaris</a></li>
                    <li><a class="dropdown-item sort-option {{ $currentSort == 'nama_az' ? 'active' : '' }}" href="{{ request()->fullUrlWithQuery(['sort' => 'nama_az']) }}">Nama: A-Z</a></li>
                    <li><a class="dropdown-item sort-option {{ $currentSort == 'nama_za' ? 'active' : '' }}" href="{{ request()->fullUrlWithQuery(['sort' => 'nama_za']) }}">Nama: Z-A</a></li>
                </ul>
            </div>
        </div>

        <!-- Loading indicator -->
        <div id="loadingIndicator" class="text-center py-4" style="display: none;">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-2 text-muted">Memuat produk...</p>
        </div>
        
        <!-- Products Grid -->
        <div class="row g-4" id="productGrid">
            @forelse ($items as $item)
                <div class="col-6 col-md-4 col-lg-3">
                    <div class="product-card h-100">
                        <div class="card border-0 shadow-sm h-100">
                            <img src="{{ isset($item['gambar']) && $item['gambar'] ? asset('storage/' . $item['gambar']) : asset('images/products/default.png') }}" 
                                 alt="{{ $item['nama_item'] }}" 
                                 class="img-fluid product-image card-img-top">

                            <div class="card-body">
                                <h5 class="card-title">{{ $item['nama_item'] }}</h5>
                                <p class="card-text small text-muted">{{ Str::limit($item['deskripsi'], 50) }}</p>
                                <div class="d-flex justify-content-between align-items-center mt-3">
                                    <span class="fw-bold text-primary">Rp {{ number_format($item['harga_dasar'], 0, ',', '.') }}</span>
                                    <small class="text-muted">
                                        {{ isset($item['total_terjual']) && $item['total_terjual'] > 0 ? $item['total_terjual'] . ' terjual' : '0 terjual' }}
                                    </small>
                                    {{-- @if(isset($item['total_sold']))
                                        <small class="text-muted">Terjual: {{ $item['total_sold'] }}</small>
                                    @endif --}}
                                </div>
                            </div>
                            
                            <div class="card-footer bg-transparent">
                                <a href="{{ url('/produk/' . $item['id']) }}" class="btn btn-sm btn-outline-primary w-100">
                                    <i class="fas fa-eye me-2"></i>Lihat Detail
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12 text-center">
                    <div class="py-5">
                        <i class="fas fa-box-open text-muted" style="font-size: 4rem;"></i>
                        <h4 class="mt-3 text-muted">Produk Tidak Ditemukan</h4>
                        <p class="text-muted">
                            @if($kategoriNama)
                                Tidak ada produk dalam kategori "{{ $kategoriNama }}".
                                <br><a href="{{ route('produk-all') }}" class="btn btn-sm btn-outline-primary mt-2">Lihat Semua Produk</a>
                            @else
                                Produk belum tersedia saat ini.
                            @endif
                        </p>
                    </div>
                </div>
            @endforelse
        </div>

        @if(count($items) > 0)
            <div class="row mt-4">
                <div class="col-12 text-center">
                    <p class="text-muted">
                        Menampilkan {{ count($items) }} produk
                        @if($kategoriNama)
                            dalam kategori "{{ $kategoriNama }}"
                        @endif
                    </p>
                </div>
            </div>
        @endif
        
        <!-- Pagination (for future implementation) -->
        {{-- <nav class="mt-5">
            <ul class="pagination justify-content-center">
                <li class="page-item disabled">
                    <a class="page-link" href="#" tabindex="-1" aria-disabled="true">Previous</a>
                </li>
                <li class="page-item active"><a class="page-link" href="#">1</a></li>
                <li class="page-item"><a class="page-link" href="#">2</a></li>
                <li class="page-item"><a class="page-link" href="#">3</a></li>
                <li class="page-item">
                    <a class="page-link" href="#">Next</a>
                </li>
            </ul>
        </nav> --}}
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const sortOptions = document.querySelectorAll('.sort-option');
    const productGrid = document.getElementById('productGrid');
    const loadingIndicator = document.getElementById('loadingIndicator');
    
    sortOptions.forEach(option => {
        option.addEventListener('click', function(e) {
            // Show loading indicator
            if (loadingIndicator && productGrid) {
                loadingIndicator.style.display = 'block';
                productGrid.style.opacity = '0.5';
            }
            
            // Let the default link behavior handle the navigation
            // The page will reload with new sorting
        });
    });
});
</script>

<!-- Add some custom styling -->
<style>
    .product-card {
        transition: transform 0.3s;
        height: 100%;
    }
    
    .product-card:hover {
        transform: translateY(-5px);
    }
    
    .card-img-top, .product-image {
        height: 200px;
        object-fit: cover;
    }
    
    .section-title {
        position: relative;
        display: inline-block;
        padding-bottom: 10px;
        margin-bottom: 30px;
    }
    
    .section-title::after {
        content: '';
        position: absolute;
        width: 80px;
        height: 3px;
        background-color: #4361ee;
        bottom: 0;
        left: 50%;
        transform: translateX(-50%);
    }

    /* Dropdown styling */
    .dropdown-item.active {
        background-color: #4361ee;
        color: white;
    }
    
    .dropdown-item:hover {
        background-color: #f8f9fa;
    }
    
    .dropdown-item.active:hover {
        background-color: #3651d4;
    }

    /* Filter buttons styling */
    .filter-buttons .btn.active {
        background-color: #4361ee;
        border-color: #4361ee;
        color: white;
    }

    /* Loading transition */
    #productGrid {
        transition: opacity 0.3s ease-in-out;
    }
    
    /* Mobile responsiveness */
    @media (max-width: 576px) {
        .card-img-top, .product-image {
            height: 150px;
        }
        
        .filter-buttons {
            display: block !important;
            margin-bottom: 1rem;
        }
        
        .filter-buttons .btn {
            margin-bottom: 0.5rem;
        }
    }

    @media (max-width: 768px) {
        .d-flex.justify-content-between {
            flex-direction: column;
            align-items: stretch !important;
        }
        
        .dropdown {
            align-self: flex-end;
        }
    }
</style>
@endsection