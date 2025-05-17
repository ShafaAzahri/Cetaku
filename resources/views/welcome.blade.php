
@extends('user.layouts.app')

@section('content')
    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <div class="row">
                <div class="col-md-6 hero-content">
                    <h1 class="hero-title">Desain Produk</h1>
                    <p class="hero-text">
                        Buat produk merchandise, kemasan, untuk kafe, kantor, startup, 
                        protokol, merch dan produk lain yang dapat diseleksi menggunakan 
                        mesin print terbaik. Pastikan dengan harga terjangkau dengan 
                        kualitas bahan terbaik.
                    </p>
                    <a href="#" class="btn btn-primary btn-order">Pesan Sekarang <i class="fas fa-arrow-right ms-2"></i></a>
                </div>
                <div class="col-md-6 hero-image">
                    <img src="{{ asset('images/banner.png') }}" alt="Hero Image" class="img-fluid">
                </div>
            </div>
            <div class="hero-footer">
                <span class="small-text">*Kaos Custom</span>
                <a href="#" class="chat-admin">Chat Admin</a>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features-section">
        <div class="container">
            <div class="row">
                <div class="col-md-3 col-sm-6 feature-item">
                    <div class="feature-icon">
                        <i class="fas fa-truck"></i>
                    </div>
                    <h5 class="feature-title">Gratis Ongkir</h5>
                    <p class="feature-text">Dapatkan kemudahan gratis ongkir untuk pemesanan.</p>
                </div>
                
                <div class="col-md-3 col-sm-6 feature-item">
                    <div class="feature-icon">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <h5 class="feature-title">Garansi Produk</h5>
                    <p class="feature-text">Semua produk untuk klaim garansi produk.</p>
                </div>
                
                <div class="col-md-3 col-sm-6 feature-item">
                    <div class="feature-icon">
                        <i class="fas fa-headset"></i>
                    </div>
                    <h5 class="feature-title">Bantuan Online</h5>
                    <p class="feature-text">24/7 siap menerima pertanyaan pelanggan.</p>
                </div>
                
                <div class="col-md-3 col-sm-6 feature-item">
                    <div class="feature-icon">
                        <i class="fas fa-credit-card"></i>
                    </div>
                    <h5 class="feature-title">Pembayaran Fleksibel</h5>
                    <p class="feature-text">Layanan transaksi dengan beberapa pilihan platform.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Best Seller Section -->
    <section class="bestseller-section">
        <div class="container">
            <h2 class="section-title text-center">Terlaris</h2>
            
            <div class="row mt-4">
                <div class="col-md-3 col-sm-6 mb-4">
                    <div class="product-card">
                        <div class="product-img">
                            <img src="{{ asset('images/products/banner.png') }}" alt="Banner" class="img-fluid">
                        </div>
                        <div class="product-info">
                            <h5 class="product-title">Banner</h5>
                            <p class="product-price">Rp. 80.000</p>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-3 col-sm-6 mb-4">
                    <div class="product-card">
                        <div class="product-img">
                            <img src="{{ asset('images/products/foto.png') }}" alt="Foto" class="img-fluid">
                        </div>
                        <div class="product-info">
                            <h5 class="product-title">Foto</h5>
                            <p class="product-price">Rp. 120.000</p>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-3 col-sm-6 mb-4">
                    <div class="product-card">
                        <div class="product-img">
                            <img src="{{ asset('images/products/print.png') }}" alt="Print on Paper" class="img-fluid">
                        </div>
                        <div class="product-info">
                            <h5 class="product-title">Print on Paper</h5>
                            <p class="product-price">Rp. 75.000</p>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-3 col-sm-6 mb-4">
                    <div class="product-card">
                        <div class="product-img">
                            <img src="{{ asset('images/products/stiker.png') }}" alt="Stiker" class="img-fluid">
                        </div>
                        <div class="product-info">
                            <h5 class="product-title">Stiker</h5>
                            <p class="product-price">Rp. 15.000</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="text-center mt-4">
                <a href="#" class="btn btn-outline-primary btn-explore">Jelajahi <i class="fas fa-arrow-right ms-2"></i></a>
            </div>
        </div>
    </section>

    <!-- Categories Section -->
    <section class="categories-section">
        <div class="container">
            <h2 class="section-title text-center">Kategori</h2>
            
            <div class="row mt-4">
                <div class="col-md-4 mb-4">
                    <div class="category-card">
                        <img src="{{ asset('images/categories/pakaian.jpg') }}" alt="Pakaian" class="img-fluid">
                        <div class="category-overlay">
                            <div class="category-name">Pakaian</div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4 mb-4">
                    <div class="category-card">
                        <img src="{{ asset('images/categories/banner.jpg') }}" alt="Banner / MMT" class="img-fluid">
                        <div class="category-overlay">
                            <div class="category-name">Banner / MMT</div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4 mb-4">
                    <div class="category-card">
                        <img src="{{ asset('images/categories/print.jpg') }}" alt="Print on paper" class="img-fluid">
                        <div class="category-overlay">
                            <div class="category-name">Print on paper</div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6 mb-4">
                    <div class="category-card">
                        <img src="{{ asset('images/categories/sticker.jpg') }}" alt="Sticker" class="img-fluid">
                        <div class="category-overlay">
                            <div class="category-name">Sticker</div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6 mb-4">
                    <div class="category-card">
                        <img src="{{ asset('images/categories/fotografi.jpg') }}" alt="Fotografi" class="img-fluid">
                        <div class="category-overlay">
                            <div class="category-name">Fotografi</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- How To Order Section -->
    <section class="how-to-order-section">
        <div class="container">
            <div class="row">
                <div class="col-md-5">
                    <h2 class="section-title">Kemudahan dalam Percetakan</h2>
                    <p class="section-description">
                        Raih kemudahan dalam mencetak berbagai kebutuhan Anda mulai dari kaos, merchandise, poster, kartu nama, hingga banner. Kami hadir untuk Anda dengan pengerjaan cepat 1-3 hari. Kami terbuka untuk pemesanan dalam jumlah bulk baik untuk maupun untuk berbagai merk.
                    </p>
                    <a href="#" class="chat-admin-link">Chat Admin</a>
                </div>
                
                <div class="col-md-7">
                    <div class="steps-container">
                        <div class="step-item">
                            <div class="step-number">01</div>
                            <div class="step-content">
                                <h4 class="step-title">Tentukan Pilihanmu</h4>
                                <p class="step-description">Temukan gaya yang kamu inginkan ke keranjang.</p>
                            </div>
                        </div>
                        
                        <div class="step-item">
                            <div class="step-number">02</div>
                            <div class="step-content">
                                <h4 class="step-title">Lakukan Transaksi Pemesanan</h4>
                                <p class="step-description">Tentukan detail pesananmu dan lakukan transaksi.</p>
                            </div>
                        </div>
                        
                        <div class="step-item">
                            <div class="step-number">03</div>
                            <div class="step-content">
                                <h4 class="step-title">Pantau Pesananmu</h4>
                                <p class="step-description">Tunggu pesananmu diproses hingga sampai ditanganmu.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection