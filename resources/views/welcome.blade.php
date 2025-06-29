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
                    <a href="{{ route('produk-all') }}" class="btn btn-outline-primary btn-explore">Pesan Sekarang<i class="fas fa-arrow-right ms-2"></i></a>
                </div>
<div class="col-md-6 hero-image">
    <div id="heroCarousel" class="carousel slide" data-bs-ride="carousel" data-bs-interval="1500" data-bs-pause="false" style="height: 300px; overflow: hidden;">
        <div class="carousel-inner h-100">
            <div class="carousel-item active h-100">
                <img src="{{ asset('images/banner.png') }}" class="d-block w-100 h-100 object-fit-cover" alt="Hero Image 1">
            </div>
            <div class="carousel-item h-100">
                <img src="{{ asset('images/cover.png') }}" class="d-block w-100 h-100 object-fit-cover" alt="Hero Image 2">
            </div>
            <div class="carousel-item h-100">
                <img src="{{ asset('images/polines.png') }}" class="d-block w-100 h-100 object-fit-cover" alt="Hero Image 3">
            </div>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
        </button>
    </div>
</div>



            </div>
            <div class="hero-footer">
                <span class="small-text">*Kaos Custom</span>
                <a href="https://wa.me/62895383002709" class="">Chat Admin</a>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features-section">
        <div class="container">
            <div class="row justify-content-center text-center">

                
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
                <a href="{{ route('produk-all') }}" class="btn btn-outline-primary btn-sm">
                    Lihat Semua <i class="fas fa-arrow-right ms-1"></i>
                </a>
            </div>
            @endif
        </div>
        
        <div class="row mt-4">
            @forelse($kategoris as $kategori)
            <div class="col-md-{{ count($kategoris) >= 3 ? '4' : (count($kategoris) == 2 ? '6' : '12') }} mb-4">
               <a href="{{ route('produk-all', ['kategori' => $kategori['nama_kategori']]) }}" class="text-decoration-none">
                    <div class="category-card position-relative">
                        @if(isset($kategori['gambar']) && $kategori['gambar'])
                            <img src="{{ asset('storage/' . $kategori['gambar']) }}" alt="{{ $kategori['nama_kategori'] }}" class="img-fluid">
                            <div class="category-label">{{ $kategori['nama_kategori'] }}</div>
                        @else
                            <!-- Default gambar berdasarkan nama kategori -->
                            @if(stripos($kategori['nama_kategori'], 'pakaian') !== false)
                                <img src="{{ asset('images/categories/pakaian.jpg') }}" alt="{{ $kategori['nama_kategori'] }}" class="img-fluid">
                                <div class="category-label">Pakaian</div>
                            @elseif(stripos($kategori['nama_kategori'], 'banner') !== false || stripos($kategori['nama_kategori'], 'mmt') !== false)
                                <img src="{{ asset('images/categories/banner.jpg') }}" alt="{{ $kategori['nama_kategori'] }}" class="img-fluid">
                                <div class="category-label">Banner & MMT</div>
                            @elseif(stripos($kategori['nama_kategori'], 'sticker') !== false || stripos($kategori['nama_kategori'], 'stiker') !== false)
                                <img src="{{ asset('images/categories/sticker.jpg') }}" alt="{{ $kategori['nama_kategori'] }}" class="img-fluid">
                                <div class="category-label">Sticker</div>
                            @elseif(stripos($kategori['nama_kategori'], 'print') !== false || stripos($kategori['nama_kategori'], 'paper') !== false)
                                <img src="{{ asset('images/categories/print.jpg') }}" alt="{{ $kategori['nama_kategori'] }}" class="img-fluid">
                                <div class="category-label">Print & Paper</div>
                            @elseif(stripos($kategori['nama_kategori'], 'fotografi') !== false || stripos($kategori['nama_kategori'], 'foto') !== false)
                                <img src="{{ asset('images/categories/fotografi.jpg') }}" alt="{{ $kategori['nama_kategori'] }}" class="img-fluid">
                                <div class="category-label">Fotografi</div>
                            @else
                                <img src="{{ asset('images/categories/default.jpg') }}" alt="{{ $kategori['nama_kategori'] }}" class="img-fluid">
                                <div class="category-label">{{ $kategori['nama_kategori'] }}</div>
                            @endif
                        @endif
                        <div class="category-overlay">
                            <div class="category-name">{{ $kategori['nama_kategori'] }}</div>
                            @if(isset($kategori['deskripsi']) && $kategori['deskripsi'])
                                <div class="category-description">{{ Str::limit($kategori['deskripsi'], 50) }}</div>
                            @else
                                @php
                                    $defaultDescription = '';
                                    if(stripos($kategori['nama_kategori'], 'pakaian') !== false) {
                                        $defaultDescription = 'Layanan sablon dan bordir untuk berbagai jenis pakaian';
                                    } elseif(stripos($kategori['nama_kategori'], 'banner') !== false || stripos($kategori['nama_kategori'], 'mmt') !== false) {
                                        $defaultDescription = 'Cetak banner dan MMT untuk promosi dan iklan';
                                    } elseif(stripos($kategori['nama_kategori'], 'sticker') !== false || stripos($kategori['nama_kategori'], 'stiker') !== false) {
                                        $defaultDescription = 'Sticker custom dengan berbagai bahan dan ukuran';
                                    } elseif(stripos($kategori['nama_kategori'], 'print') !== false || stripos($kategori['nama_kategori'], 'paper') !== false) {
                                        $defaultDescription = 'Layanan cetak dokumen dan material berbasis kertas';
                                    } elseif(stripos($kategori['nama_kategori'], 'fotografi') !== false || stripos($kategori['nama_kategori'], 'foto') !== false) {
                                        $defaultDescription = 'Jasa fotografi profesional untuk berbagai acara';
                                    } else {
                                        $defaultDescription = 'Layanan percetakan berkualitas tinggi';
                                    }
                                @endphp
                                <div class="category-description">{{ Str::limit($defaultDescription, 50) }}</div>
                            @endif
                        </div>
                    </div>
                </a>
            </div>
            @empty
            <!-- Fallback ke kategori static jika tidak ada data dari API -->
            <div class="col-md-4 mb-4">
                <div class="category-card position-relative">
                    <img src="{{ asset('images/categories/pakaian.jpg') }}" alt="Pakaian" class="img-fluid">
                    <div class="category-label">Pakaian</div>
                    <div class="category-overlay">
                        <div class="category-name">Pakaian</div>
                        <div class="category-description">Layanan sablon dan bordir untuk berbagai jenis pakaian</div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4 mb-4">
                <div class="category-card position-relative">
                    <img src="{{ asset('images/categories/banner.jpg') }}" alt="Banner / MMT" class="img-fluid">
                    <div class="category-label">Banner & MMT</div>
                    <div class="category-overlay">
                        <div class="category-name">Banner / MMT</div>
                        <div class="category-description">Cetak banner dan MMT untuk promosi dan iklan</div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4 mb-4">
                <div class="category-card position-relative">
                    <img src="{{ asset('images/categories/print.jpg') }}" alt="Print on paper" class="img-fluid">
                    <div class="category-label">Print & Paper</div>
                    <div class="category-overlay">
                        <div class="category-name">Print on paper</div>
                        <div class="category-description">Layanan cetak dokumen dan material berbasis kertas</div>
                    </div>
                </div>
            </div>
            @endforelse
        </div>
    </div>
</section>

<style>
.category-label {
    position: absolute;
    top: 75%;
    left: 50%;
    transform: translate(-50%, -50%);
    background: rgba(0, 0, 0, 0.7);
    color: white;
    padding: 8px 16px;
    border-radius: 5px;
    font-weight: bold;
    font-size: 18px;
    text-align: center;
    white-space: nowrap;
    z-index: 2;
}

.category-card {
    overflow: hidden;
    border-radius: 8px;
    transition: transform 0.3s ease;
}

.category-card:hover {
    transform: translateY(-5px);
}

.category-card:hover .category-label {
    background: rgba(0, 0, 0, 0.9);
}
</style>
    <!-- How To Order Section -->
    <section class="how-to-order-section">
        <div class="container">
            <div class="row">
                <div class="col-md-5">
                    <h2 class="section-title">Kemudahan dalam Percetakan</h2>
                    <p class="section-description">
                        Raih kemudahan dalam mencetak berbagai kebutuhan Anda mulai dari kaos, merchandise, poster, kartu nama, hingga banner. Kami hadir untuk Anda dengan pengerjaan cepat 1-3 hari. Kami terbuka untuk pemesanan dalam jumlah bulk baik untuk maupun untuk berbagai merk.
                    </p>
                    <a href="https://wa.me/62895383002709" class="">Chat Admin</a>
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