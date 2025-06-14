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
                <!-- Di bagian Best Seller Section -->
                @forelse($terlaris as $item)
                <div class="col-md-3 col-sm-6 mb-4">
                    <div class="product-card">
                        <a href="{{ route('product.detail', $item['id']) }}" class="text-decoration-none">
                            <div class="product-img">
                                @if(isset($item['gambar']) && $item['gambar'])
                                    <img src="{{ asset('storage/' . $item['gambar']) }}" alt="{{ $item['nama_item'] }}" class="img-fluid">
                                @else
                                    <img src="{{ asset('images/products/default.png') }}" alt="{{ $item['nama_item'] }}" class="img-fluid">
                                @endif
                            </div>
                            <div class="product-info">
                                <h5 class="product-title">{{ $item['nama_item'] }}</h5>
                                <p class="product-price">Rp {{ number_format($item['harga_dasar'], 0, ',', '.') }}</p>
                                <small class="text-muted">
                                    {{ isset($item['total_terjual']) && $item['total_terjual'] > 0 ? $item['total_terjual'] . ' terjual' : '0 terjual' }}
                                </small>
                            </div>
                        </a>
                    </div>
                </div>
                @empty
                <div class="col-12 text-center">
                    <p class="text-muted">Belum ada produk terlaris.</p>
                </div>
                @endforelse
            </div>
            
            <div class="text-center mt-4">
                <a href="{{ route('produk-all') }}" class="btn btn-outline-primary btn-explore">Jelajahi <i class="fas fa-arrow-right ms-2"></i></a>
            </div>
        </div>
    </section>

    <!-- Categories Section -->
    <section class="categories-section">
        <div class="container">
            <div class="row align-items-center mb-4">
                <div class="col">
                    <h2 class="section-title text-center">Kategori</h2>
                </div>
                @if(count($kategoris) > 0)
                <div class="col-auto">
                    <a href="#" class="btn btn-outline-primary btn-sm">
                        Lihat Semua <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                </div>
                @endif
            </div>
            
            <div class="row mt-4">
                @forelse($kategoris as $kategori)
                <div class="col-md-{{ count($kategoris) >= 3 ? '4' : (count($kategoris) == 2 ? '6' : '12') }} mb-4">
                    <a href="#" class="text-decoration-none">
                        <div class="category-card">
                            @if(isset($kategori['gambar']) && $kategori['gambar'])
                                <img src="{{ asset('storage/' . $kategori['gambar']) }}" alt="{{ $kategori['nama_kategori'] }}" class="img-fluid">
                            @else
                                <!-- Default gambar berdasarkan nama kategori -->
                                @if(stripos($kategori['nama_kategori'], 'pakaian') !== false)
                                    <img src="{{ asset('images/categories/pakaian.jpg') }}" alt="{{ $kategori['nama_kategori'] }}" class="img-fluid">
                                @elseif(stripos($kategori['nama_kategori'], 'banner') !== false || stripos($kategori['nama_kategori'], 'mmt') !== false)
                                    <img src="{{ asset('images/categories/banner.jpg') }}" alt="{{ $kategori['nama_kategori'] }}" class="img-fluid">
                                @elseif(stripos($kategori['nama_kategori'], 'sticker') !== false || stripos($kategori['nama_kategori'], 'stiker') !== false)
                                    <img src="{{ asset('images/categories/sticker.jpg') }}" alt="{{ $kategori['nama_kategori'] }}" class="img-fluid">
                                @elseif(stripos($kategori['nama_kategori'], 'print') !== false || stripos($kategori['nama_kategori'], 'paper') !== false)
                                    <img src="{{ asset('images/categories/print.jpg') }}" alt="{{ $kategori['nama_kategori'] }}" class="img-fluid">
                                @elseif(stripos($kategori['nama_kategori'], 'fotografi') !== false || stripos($kategori['nama_kategori'], 'foto') !== false)
                                    <img src="{{ asset('images/categories/fotografi.jpg') }}" alt="{{ $kategori['nama_kategori'] }}" class="img-fluid">
                                @else
                                    <img src="{{ asset('images/categories/default.jpg') }}" alt="{{ $kategori['nama_kategori'] }}" class="img-fluid">
                                @endif
                            @endif
                            <div class="category-overlay">
                                <div class="category-name">{{ $kategori['nama_kategori'] }}</div>
                                @if(isset($kategori['deskripsi']) && $kategori['deskripsi'])
                                    <div class="category-description">{{ Str::limit($kategori['deskripsi'], 50) }}</div>
                                @endif
                            </div>
                        </div>
                    </a>
                </div>
                @empty
                <!-- Fallback ke kategori static jika tidak ada data dari API -->
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
                @endforelse
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
                    <a href="" class="chat-admin-link">Chat Admin</a>
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