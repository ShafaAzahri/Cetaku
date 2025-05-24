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
        
        <!-- Filter and Sort (optional for future) -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div class="filter-buttons d-none d-md-block">
                <button class="btn btn-sm btn-outline-secondary me-2 active">Semua</button>
                <button class="btn btn-sm btn-outline-secondary me-2">Banner</button>
                <button class="btn btn-sm btn-outline-secondary me-2">Merchandise</button>
                <button class="btn btn-sm btn-outline-secondary">Kartu</button>
            </div>
            <div class="dropdown">
                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" id="sortDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    Urutkan
                </button>
                <ul class="dropdown-menu" aria-labelledby="sortDropdown">
                    <li><a class="dropdown-item" href="#">Terbaru</a></li>
                    <li><a class="dropdown-item" href="#">Harga: Rendah ke Tinggi</a></li>
                    <li><a class="dropdown-item" href="#">Harga: Tinggi ke Rendah</a></li>
                    <li><a class="dropdown-item" href="#">Terlaris</a></li>
                </ul>
            </div>
        </div>
        
        <!-- Products Grid -->
        <div class="row g-4">
            <!-- Product 1 -->
            <div class="col-6 col-md-4 col-lg-3">
                <div class="product-card h-100">
                    <div class="card border-0 shadow-sm h-100">
                        <img src="https://via.placeholder.com/400x300/4361ee/ffffff?text=Banner+Indoor" class="card-img-top" alt="Banner Indoor">
                        <div class="card-body">
                            <h5 class="card-title">Banner Indoor</h5>
                            <p class="card-text small text-muted">Banner berkualitas tinggi untuk kebutuhan indoor</p>
                            <div class="d-flex justify-content-between align-items-center mt-3">
                                <span class="fw-bold text-primary">Rp 150.000</span>
                                <a href="#" class="btn btn-sm btn-outline-primary">Lihat Detail</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Product 2 -->
            <div class="col-6 col-md-4 col-lg-3">
                <div class="product-card h-100">
                    <div class="card border-0 shadow-sm h-100">
                        <img src="https://via.placeholder.com/400x300/4361ee/ffffff?text=Banner+Outdoor" class="card-img-top" alt="Banner Outdoor">
                        <div class="card-body">
                            <h5 class="card-title">Banner Outdoor</h5>
                            <p class="card-text small text-muted">Banner tahan cuaca untuk penggunaan di luar ruangan</p>
                            <div class="d-flex justify-content-between align-items-center mt-3">
                                <span class="fw-bold text-primary">Rp 200.000</span>
                                <a href="#" class="btn btn-sm btn-outline-primary">Lihat Detail</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Product 3 -->
            <div class="col-6 col-md-4 col-lg-3">
                <div class="product-card h-100">
                    <div class="card border-0 shadow-sm h-100">
                        <img src="https://via.placeholder.com/400x300/4361ee/ffffff?text=X-Banner" class="card-img-top" alt="X-Banner">
                        <div class="card-body">
                            <h5 class="card-title">X-Banner</h5>
                            <p class="card-text small text-muted">Stand banner portable untuk promosi dan pameran</p>
                            <div class="d-flex justify-content-between align-items-center mt-3">
                                <span class="fw-bold text-primary">Rp 180.000</span>
                                <a href="#" class="btn btn-sm btn-outline-primary">Lihat Detail</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Product 4 -->
            <div class="col-6 col-md-4 col-lg-3">
                <div class="product-card h-100">
                    <div class="card border-0 shadow-sm h-100">
                        <img src="https://via.placeholder.com/400x300/4361ee/ffffff?text=Roll+Banner" class="card-img-top" alt="Roll Banner">
                        <div class="card-body">
                            <h5 class="card-title">Roll Banner</h5>
                            <p class="card-text small text-muted">Roll up banner dengan stand kokoh dan mudah dibawa</p>
                            <div class="d-flex justify-content-between align-items-center mt-3">
                                <span class="fw-bold text-primary">Rp 250.000</span>
                                <a href="#" class="btn btn-sm btn-outline-primary">Lihat Detail</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Product 5 -->
            <div class="col-6 col-md-4 col-lg-3">
                <div class="product-card h-100">
                    <div class="card border-0 shadow-sm h-100">
                        <img src="https://via.placeholder.com/400x300/4361ee/ffffff?text=Kaos+Custom" class="card-img-top" alt="Kaos Custom">
                        <div class="card-body">
                            <h5 class="card-title">Kaos Custom</h5>
                            <p class="card-text small text-muted">Kaos cotton combed 30s dengan desain sesuai keinginan</p>
                            <div class="d-flex justify-content-between align-items-center mt-3">
                                <span class="fw-bold text-primary">Rp 85.000</span>
                                <a href="#" class="btn btn-sm btn-outline-primary">Lihat Detail</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Product 6 -->
            <div class="col-6 col-md-4 col-lg-3">
                <div class="product-card h-100">
                    <div class="card border-0 shadow-sm h-100">
                        <img src="https://via.placeholder.com/400x300/4361ee/ffffff?text=Hoodie+Custom" class="card-img-top" alt="Hoodie Custom">
                        <div class="card-body">
                            <h5 class="card-title">Hoodie Custom</h5>
                            <p class="card-text small text-muted">Hoodie fleece premium dengan desain custom</p>
                            <div class="d-flex justify-content-between align-items-center mt-3">
                                <span class="fw-bold text-primary">Rp 200.000</span>
                                <a href="#" class="btn btn-sm btn-outline-primary">Lihat Detail</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Product 7 -->
            <div class="col-6 col-md-4 col-lg-3">
                <div class="product-card h-100">
                    <div class="card border-0 shadow-sm h-100">
                        <img src="https://via.placeholder.com/400x300/4361ee/ffffff?text=Mug+Custom" class="card-img-top" alt="Mug Custom">
                        <div class="card-body">
                            <h5 class="card-title">Mug Custom</h5>
                            <p class="card-text small text-muted">Mug keramik dengan desain dan foto custom</p>
                            <div class="d-flex justify-content-between align-items-center mt-3">
                                <span class="fw-bold text-primary">Rp 45.000</span>
                                <a href="#" class="btn btn-sm btn-outline-primary">Lihat Detail</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Product 8 -->
            <div class="col-6 col-md-4 col-lg-3">
                <div class="product-card h-100">
                    <div class="card border-0 shadow-sm h-100">
                        <img src="https://via.placeholder.com/400x300/4361ee/ffffff?text=Topi+Custom" class="card-img-top" alt="Topi Custom">
                        <div class="card-body">
                            <h5 class="card-title">Topi Custom</h5>
                            <p class="card-text small text-muted">Topi snapback dengan bordir atau printing desain custom</p>
                            <div class="d-flex justify-content-between align-items-center mt-3">
                                <span class="fw-bold text-primary">Rp 75.000</span>
                                <a href="#" class="btn btn-sm btn-outline-primary">Lihat Detail</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Product 9 -->
            <div class="col-6 col-md-4 col-lg-3">
                <div class="product-card h-100">
                    <div class="card border-0 shadow-sm h-100">
                        <img src="https://via.placeholder.com/400x300/4361ee/ffffff?text=Stiker+Custom" class="card-img-top" alt="Stiker Custom">
                        <div class="card-body">
                            <h5 class="card-title">Stiker Custom</h5>
                            <p class="card-text small text-muted">Stiker vinyl dengan cutting custom sesuai bentuk desain</p>
                            <div class="d-flex justify-content-between align-items-center mt-3">
                                <span class="fw-bold text-primary">Rp 5.000</span>
                                <a href="#" class="btn btn-sm btn-outline-primary">Lihat Detail</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Product 10 -->
            <div class="col-6 col-md-4 col-lg-3">
                <div class="product-card h-100">
                    <div class="card border-0 shadow-sm h-100">
                        <img src="https://via.placeholder.com/400x300/4361ee/ffffff?text=Kartu+Nama" class="card-img-top" alt="Kartu Nama">
                        <div class="card-body">
                            <h5 class="card-title">Kartu Nama</h5>
                            <p class="card-text small text-muted">Kartu nama dengan berbagai pilihan kertas premium</p>
                            <div class="d-flex justify-content-between align-items-center mt-3">
                                <span class="fw-bold text-primary">Rp 100.000</span>
                                <a href="#" class="btn btn-sm btn-outline-primary">Lihat Detail</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Product 11 -->
            <div class="col-6 col-md-4 col-lg-3">
                <div class="product-card h-100">
                    <div class="card border-0 shadow-sm h-100">
                        <img src="https://via.placeholder.com/400x300/4361ee/ffffff?text=Flyer" class="card-img-top" alt="Flyer">
                        <div class="card-body">
                            <h5 class="card-title">Flyer</h5>
                            <p class="card-text small text-muted">Flyer promosi dengan printing full color</p>
                            <div class="d-flex justify-content-between align-items-center mt-3">
                                <span class="fw-bold text-primary">Rp 150.000</span>
                                <a href="#" class="btn btn-sm btn-outline-primary">Lihat Detail</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Product 12 -->
            <div class="col-6 col-md-4 col-lg-3">
                <div class="product-card h-100">
                    <div class="card border-0 shadow-sm h-100">
                        <img src="https://via.placeholder.com/400x300/4361ee/ffffff?text=Brosur" class="card-img-top" alt="Brosur">
                        <div class="card-body">
                            <h5 class="card-title">Brosur</h5>
                            <p class="card-text small text-muted">Brosur dengan lipatan dan finishing yang rapi</p>
                            <div class="d-flex justify-content-between align-items-center mt-3">
                                <span class="fw-bold text-primary">Rp 200.000</span>
                                <a href="#" class="btn btn-sm btn-outline-primary">Lihat Detail</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Pagination -->
        <nav class="mt-5">
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
        </nav>
    </div>
</section>

<!-- Add some custom styling -->
<style>
    .product-card {
        transition: transform 0.3s;
        height: 100%;
    }
    
    .product-card:hover {
        transform: translateY(-5px);
    }
    
    .card-img-top {
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
    
    /* Mobile responsiveness */
    @media (max-width: 576px) {
        .card-img-top {
            height: 150px;
        }
    }
</style>
@endsection