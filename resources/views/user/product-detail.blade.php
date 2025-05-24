@extends('user.layouts.app')

@section('title', 'Detail Produk - Kaos Custom')

@section('custom-css')
<style>
    .product-image {
        border-radius: 8px;
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
    
    .product-title {
        color: #333;
        font-weight: 600;
    }
    
    .product-price {
        color: #4361ee;
        font-weight: 700;
        font-size: 1.5rem;
    }
    
    .rating-stars {
        color: #ffc107;
    }
    
    .quantity-control {
        display: flex;
        align-items: center;
        border: 1px solid #e0e0e0;
        border-radius: 4px;
        overflow: hidden;
        width: fit-content;
    }
    
    .quantity-control button {
        border: none;
        background: #f8f9fa;
        width: 40px;
        height: 40px;
        font-size: 18px;
        font-weight: bold;
        transition: background-color 0.2s;
    }
    
    .quantity-control button:hover {
        background: #e9ecef;
    }
    
    .quantity-control input {
        width: 60px;
        text-align: center;
        border: none;
        border-left: 1px solid #e0e0e0;
        border-right: 1px solid #e0e0e0;
        font-weight: 500;
        height: 40px;
    }
    
    .btn-add-cart {
        background-color: #4361ee;
        border-color: #4361ee;
        padding: 12px 30px;
        font-weight: 600;
        font-size: 16px;
    }
    
    .btn-add-cart:hover {
        background-color: #3651d4;
        border-color: #3651d4;
    }
    
    .product-meta {
        background-color: #f8f9fa;
        border-radius: 8px;
        padding: 20px;
    }
    
    .review-card {
        border: 1px solid #e9ecef;
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 15px;
        background-color: #fff;
    }
    
    .review-user {
        font-weight: 600;
        color: #333;
    }
    
    .review-date {
        color: #6c757d;
        font-size: 0.9rem;
    }
    
    .breadcrumb {
        background-color: #f8f9fa;
        padding: 15px 0;
    }
    
    .breadcrumb-item a {
        color: #6c757d;
        text-decoration: none;
    }
    
    .breadcrumb-item a:hover {
        color: #4361ee;
    }
    
    .breadcrumb-item.active {
        color: #333;
    }
    
    .product-section {
        padding: 40px 0;
    }
    
    .form-select, .form-control {
        border-radius: 6px;
        border: 1px solid #e0e0e0;
        padding: 10px 15px;
    }
    
    .form-select:focus, .form-control:focus {
        border-color: #4361ee;
        box-shadow: 0 0 0 0.2rem rgba(67, 97, 238, 0.25);
    }
</style>
@endsection

@section('content')
<!-- Breadcrumb -->
<div class="breadcrumb">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="/">Home</a></li>
                <li class="breadcrumb-item"><a href="/kategori">Jelajah</a></li>
                <li class="breadcrumb-item"><a href="#">Produk</a></li>
                <li class="breadcrumb-item active" aria-current="page">Kaos Custom</li>
            </ol>
        </nav>
    </div>
</div>

<!-- Product Detail Section -->
<div class="product-section">
    <div class="container">
        <div class="row">
            <!-- Product Image -->
            <div class="col-lg-6 mb-4">
                <div class="text-center">
                    <img src="{{ asset('images/banner.png') }}" 
                         alt="Kaos Custom" 
                         class="img-fluid product-image"
                         style="max-height: 500px; object-fit: contain;">
                </div>
            </div>

            <!-- Product Details -->
            <div class="col-lg-6">
                <h1 class="product-title mb-3">Kaos Custom Premium</h1>
                <div class="product-price mb-3">Mulai dari Rp 50.000</div>

                <!-- Rating -->
                <div class="d-flex align-items-center mb-3">
                    <div class="rating-stars me-2">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                    </div>
                    <span class="text-muted">(4.8/5 - 24 Reviews)</span>
                </div>

                <!-- Description -->
                <p class="text-muted mb-4">
                    Kaos custom premium dengan bahan berkualitas tinggi. Tersedia berbagai ukuran dan pilihan bahan. 
                    Cocok untuk berbagai acara dan kebutuhan promosi. Desain dapat disesuaikan dengan keinginan Anda.
                </p>

                <!-- Size Selection -->
                <div class="mb-3">
                    <label class="form-label fw-semibold">Ukuran</label>
                    <select class="form-select">
                        <option value="">Pilih Ukuran</option>
                        <option value="s">S (Small)</option>
                        <option value="m" selected>M (Medium)</option>
                        <option value="l">L (Large)</option>
                        <option value="xl">XL (Extra Large)</option>
                        <option value="xxl">XXL (Double XL)</option>
                    </select>
                </div>

                <!-- Material Selection -->
                <div class="mb-3">
                    <label class="form-label fw-semibold">Bahan</label>
                    <select class="form-select">
                        <option value="">Pilih Bahan</option>
                        <option value="cotton-24s">Cotton Combed 24s (+Rp 15.000)</option>
                        <option value="cotton-30s" selected>Cotton Combed 30s (+Rp 20.000)</option>
                        <option value="fleece">Fleece (+Rp 25.000)</option>
                        <option value="drill">Drill (+Rp 30.000)</option>
                    </select>
                </div>

                <!-- Type Selection -->
                <div class="mb-4">
                    <label class="form-label fw-semibold">Jenis</label>
                    <select class="form-select">
                        <option value="">Pilih Jenis</option>
                        <option value="lengan-pendek" selected>Lengan Pendek</option>
                        <option value="lengan-panjang">Lengan Panjang (+Rp 10.000)</option>
                        <option value="hoodie">Hoodie (+Rp 25.000)</option>
                    </select>
                </div>

                <!-- Quantity and Add to Cart -->
                <div class="d-flex align-items-center gap-3 mb-4">
                    <div class="quantity-control">
                        <button type="button" class="btn-decrease">-</button>
                        <input type="text" value="1" readonly class="quantity-input">
                        <button type="button" class="btn-increase">+</button>
                    </div>
                    <button class="btn btn-primary btn-add-cart flex-grow-1">
                        <i class="fas fa-shopping-cart me-2"></i>
                        Tambah ke Keranjang
                    </button>
                </div>

                <!-- Product Meta -->
                <div class="product-meta">
                    <div class="row">
                        <div class="col-sm-6 mb-2">
                            <strong>Kategori:</strong> 
                            <span class="text-muted">Pakaian Custom</span>
                        </div>
                        <div class="col-sm-6 mb-2">
                            <strong>Tags:</strong> 
                            <span class="text-muted">Kaos, Custom, Print</span>
                        </div>
                        <div class="col-sm-6 mb-2">
                            <strong>SKU:</strong> 
                            <span class="text-muted">KCS-001</span>
                        </div>
                        <div class="col-sm-6 mb-2">
                            <strong>Estimasi:</strong> 
                            <span class="text-muted">1-3 Hari Kerja</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Product Reviews Section -->
<div class="container py-5">
    <div class="row">
        <div class="col-12">
            <h3 class="mb-4">Ulasan Produk</h3>
            
            <!-- Review Summary -->
            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="text-center p-4 bg-light rounded">
                        <div class="display-4 fw-bold text-primary">4.8</div>
                        <div class="rating-stars mb-2">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                        </div>
                        <div class="text-muted">Berdasarkan 24 ulasan</div>
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="mt-3">
                        <div class="d-flex align-items-center mb-2">
                            <span class="me-2">5</span>
                            <i class="fas fa-star text-warning me-2"></i>
                            <div class="progress flex-grow-1 me-2" style="height: 6px;">
                                <div class="progress-bar bg-warning" style="width: 70%"></div>
                            </div>
                            <span class="text-muted">17</span>
                        </div>
                        <div class="d-flex align-items-center mb-2">
                            <span class="me-2">4</span>
                            <i class="fas fa-star text-warning me-2"></i>
                            <div class="progress flex-grow-1 me-2" style="height: 6px;">
                                <div class="progress-bar bg-warning" style="width: 20%"></div>
                            </div>
                            <span class="text-muted">5</span>
                        </div>
                        <div class="d-flex align-items-center mb-2">
                            <span class="me-2">3</span>
                            <i class="fas fa-star text-warning me-2"></i>
                            <div class="progress flex-grow-1 me-2" style="height: 6px;">
                                <div class="progress-bar bg-warning" style="width: 8%"></div>
                            </div>
                            <span class="text-muted">2</span>
                        </div>
                        <div class="d-flex align-items-center mb-2">
                            <span class="me-2">2</span>
                            <i class="fas fa-star text-warning me-2"></i>
                            <div class="progress flex-grow-1 me-2" style="height: 6px;">
                                <div class="progress-bar bg-warning" style="width: 0%"></div>
                            </div>
                            <span class="text-muted">0</span>
                        </div>
                        <div class="d-flex align-items-center">
                            <span class="me-2">1</span>
                            <i class="fas fa-star text-warning me-2"></i>
                            <div class="progress flex-grow-1 me-2" style="height: 6px;">
                                <div class="progress-bar bg-warning" style="width: 2%"></div>
                            </div>
                            <span class="text-muted">0</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Individual Reviews -->
            <div class="review-card">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div>
                        <div class="review-user">Ahmad Rizky</div>
                        <div class="rating-stars">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                        </div>
                    </div>
                    <div class="review-date">2 hari yang lalu</div>
                </div>
                <p class="mb-0">Kualitas kaos sangat bagus, bahan nyaman dan hasil print rapi. Puas dengan pelayanannya, pasti order lagi!</p>
            </div>

            <div class="review-card">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div>
                        <div class="review-user">Siti Nurhaliza</div>
                        <div class="rating-stars">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                        </div>
                    </div>
                    <div class="review-date">5 hari yang lalu</div>
                </div>
                <p class="mb-0">Desain sesuai permintaan dan kualitas print tidak luntur. Pengerjaan cepat dan hasil memuaskan.</p>
            </div>

            <div class="review-card">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div>
                        <div class="review-user">Budi Santoso</div>
                        <div class="rating-stars">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="far fa-star"></i>
                        </div>
                    </div>
                    <div class="review-date">1 minggu yang lalu</div>
                </div>
                <p class="mb-0">Kaos bagus, tapi ukuran sedikit kekecilan. Mungkin next time pilih size yang lebih besar. Overall oke lah.</p>
            </div>

            <!-- Load More Reviews -->
            <div class="text-center mt-4">
                <button class="btn btn-outline-primary">Lihat Ulasan Lainnya</button>
            </div>
        </div>
    </div>
</div>

<!-- Login Modal -->
<div class="modal fade" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="loginModalLabel">Silakan Login</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <i class="fas fa-user-circle fa-3x text-primary mb-3"></i>
                <p class="text-muted mb-4">Untuk menambahkan produk ke keranjang, Anda harus login terlebih dahulu.</p>
            </div>
            <div class="modal-footer justify-content-center">
                <a href="{{ route('login') }}" class="btn btn-primary px-4">Login</a>
                <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">Batal</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Quantity controls
    let quantity = 1;
    const quantityInput = document.querySelector('.quantity-input');
    const decreaseBtn = document.querySelector('.btn-decrease');
    const increaseBtn = document.querySelector('.btn-increase');

    decreaseBtn.addEventListener('click', function() {
        if (quantity > 1) {
            quantity--;
            quantityInput.value = quantity;
        }
    });

    increaseBtn.addEventListener('click', function() {
        quantity++;
        quantityInput.value = quantity;
    });

    // Add to cart functionality
    document.querySelector('.btn-add-cart').addEventListener('click', function() {
        
        if (!isLoggedIn) {
            // Show login modal
            const loginModal = new bootstrap.Modal(document.getElementById('loginModal'));
            loginModal.show();
            return;
        }

        // Get selected options
        const size = document.querySelector('select').value;
        const material = document.querySelectorAll('select')[1].value;
        const type = document.querySelectorAll('select')[2].value;

        // Validate selections
        if (!size || !material || !type) {
            alert('Mohon pilih ukuran, bahan, dan jenis terlebih dahulu.');
            return;
        }

        // Add to cart logic here
        alert(`Berhasil menambahkan ${quantity} produk ke keranjang!\n\nDetail:\n- Ukuran: ${size}\n- Bahan: ${material}\n- Jenis: ${type}`);
        
        // Redirect to cart page (optional)
        // window.location.href = '/keranjang';
    });

    // Update price based on selections
    function updatePrice() {
        let basePrice = 50000;
        const materialSelect = document.querySelectorAll('select')[1];
        const typeSelect = document.querySelectorAll('select')[2];
        
        // Add material cost
        if (materialSelect.value === 'cotton-24s') basePrice += 15000;
        else if (materialSelect.value === 'cotton-30s') basePrice += 20000;
        else if (materialSelect.value === 'fleece') basePrice += 25000;
        else if (materialSelect.value === 'drill') basePrice += 30000;
        
        // Add type cost
        if (typeSelect.value === 'lengan-panjang') basePrice += 10000;
        else if (typeSelect.value === 'hoodie') basePrice += 25000;
        
        document.querySelector('.product-price').textContent = 'Rp ' + basePrice.toLocaleString('id-ID');
    }

    // Add event listeners to select elements
    document.querySelectorAll('select').forEach(select => {
        select.addEventListener('change', updatePrice);
    });
});
</script>
@endsection
