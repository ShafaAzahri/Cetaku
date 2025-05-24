@extends('user.layouts.app')

@section('title', 'Keranjang')

@section('custom-css')
<style>
    .cart-container {
        padding: 20px 0;
    }
    .cart-item {
        border: 1px solid #e5e5e5;
        border-radius: 8px;
        padding: 15px;
        margin-bottom: 15px;
        background-color: #fff;
    }
    .cart-header {
        background-color: #f8f9fa;
        padding: 12px 15px;
        border-radius: 8px;
        margin-bottom: 15px;
        font-weight: 500;
        display: flex;
        align-items: center;
    }
    .item-img {
        width: 100px;
        height: 100px;
        object-fit: cover;
        border-radius: 5px;
    }
    .item-details {
        font-size: 14px;
    }
    .item-details h5 {
        font-size: 16px;
        margin-bottom: 5px;
        font-weight: 600;
    }
    .item-details p {
        margin-bottom: 4px;
        color: #555;
    }
    .item-price {
        font-weight: 600;
        color: #4361ee;
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
        background: #f5f5f5;
        width: 30px;
        height: 30px;
        font-size: 16px;
    }
    .quantity-control input {
        width: 40px;
        text-align: center;
        border: none;
        border-left: 1px solid #e0e0e0;
        border-right: 1px solid #e0e0e0;
        font-weight: 500;
    }
    .cart-group-title {
        font-weight: 500;
        margin-bottom: 10px;
        border-left: 3px solid #4361ee;
        padding-left: 10px;
    }
    .upload-btn {
        background-color: #f2f2f2;
        border: 1px dashed #aaa;
        color: #555;
        border-radius: 4px;
        padding: 6px 12px;
        transition: all 0.3s;
    }
    .upload-btn:hover {
        background-color: #e5e5e5;
    }
    .upload-status {
        font-size: 13px;
        padding: 3px 8px;
        border-radius: 50px;
        display: inline-block;
    }
    .status-pending {
        background-color: #fff3cd;
        color: #856404;
    }
    .status-uploaded {
        background-color: #d4edda;
        color: #155724;
    }
    .item-action {
        color: #dc3545;
        font-size: 13px;
        text-decoration: none;
    }
    .item-action:hover {
        text-decoration: underline;
    }
    .summary-card {
        background: #fff;
        border-radius: 8px;
        border: 1px solid #e5e5e5;
        padding: 20px;
        position: sticky;
        top: 20px;
    }
    .summary-card h5 {
        font-weight: 600;
        margin-bottom: 15px;
        border-bottom: 1px solid #e5e5e5;
        padding-bottom: 10px;
    }
    .summary-item {
        display: flex;
        justify-content: space-between;
        margin-bottom: 10px;
    }
    .summary-total {
        font-weight: 600;
        font-size: 18px;
        margin-top: 15px;
        padding-top: 15px;
        border-top: 1px solid #e5e5e5;
    }
    .checkout-btn {
        background-color: #4361ee;
        color: white;
        width: 100%;
        padding: 12px;
        border: none;
        border-radius: 6px;
        font-weight: 600;
        margin-top: 15px;
    }
    .checkout-btn:hover {
        background-color: #3651d4;
    }
    .select-all {
        margin-right: 10px;
    }
</style>
@endsection

@section('content')
<!-- Breadcrumb -->
<div class="bg-light py-3 fw-medium">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="/" class="text-decoration-none text-secondary">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">Keranjang</li>
            </ol>
        </nav>
    </div>
</div>

<div class="container cart-container">
    <!-- Alert -->
    @include('user.components.alert')
    
    <!-- Cart Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">Keranjang Anda (3 item)</h4>
    </div>
    
    <!-- Select All -->
    <div class="cart-header">
        <div class="form-check select-all">
            <input class="form-check-input" type="checkbox" id="selectAll">
            <label class="form-check-label" for="selectAll">
                Pilih Semua
            </label>
        </div>
    </div>
    
    <div class="row">
        <!-- Cart Items -->
        <div class="col-lg-8">
            <!-- Banner Group -->
            <div class="mb-4">
                <div class="cart-group-title">Kategori: Banner</div>
                
                <div class="cart-item">
                    <div class="row g-3">
                        <div class="col-auto">
                            <div class="form-check pt-4">
                                <input class="form-check-input" type="checkbox" id="item1" checked>
                                <label class="form-check-label" for="item1"></label>
                            </div>
                        </div>
                        <div class="col-auto">
                            <img src="{{ asset('images/banner.png') }}" class="item-img" alt="Banner">
                        </div>
                        <div class="col">
                            <div class="item-details">
                                <h5>Banner Indoor 100x200cm</h5>
                                <p>Bahan: Flexi China</p>
                                <p>Ukuran: 100x200cm</p>
                                <p>Jenis: Dengan Jahitan</p>
                                <p>
                                    Upload Desain: 
                                    <span class="upload-status status-pending">Belum diupload</span>
                                    <button class="btn upload-btn btn-sm ms-2">
                                        <i class="fas fa-upload me-1"></i> Upload Desain
                                    </button>
                                </p>
                                <div class="d-block mt-1">
                                    <a href="#" class="item-action"><i class="fas fa-trash-alt me-1"></i> Hapus</a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="d-flex flex-column align-items-end h-100 justify-content-between">
                                <div class="item-price">Rp 150.000</div>
                                <div class="quantity-control">
                                    <button type="button">-</button>
                                    <input type="text" value="1" readonly>
                                    <button type="button">+</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Merchandise Group -->
            <div class="mb-4">
                <div class="cart-group-title">Kategori: Merchandise</div>
                
                <div class="cart-item">
                    <div class="row g-3">
                        <div class="col-auto">
                            <div class="form-check pt-4">
                                <input class="form-check-input" type="checkbox" id="item2" checked>
                                <label class="form-check-label" for="item2"></label>
                            </div>
                        </div>
                        <div class="col-auto">
                            <img src="{{ asset('images/kaos.png') }}" class="item-img" alt="Kaos">
                        </div>
                        <div class="col">
                            <div class="item-details">
                                <h5>Kaos Custom</h5>
                                <p>Bahan: Cotton Combed 30s</p>
                                <p>Ukuran: XL</p>
                                <p>Jenis: Lengan Pendek</p>
                                <p>
                                    Upload Desain: 
                                    <span class="upload-status status-uploaded">Sudah diupload</span>
                                    <button class="btn upload-btn btn-sm ms-2">
                                        <i class="fas fa-edit me-1"></i> Ganti Desain
                                    </button>
                                </p>
                                <div class="d-block mt-1">
                                    <a href="#" class="item-action"><i class="fas fa-trash-alt me-1"></i> Hapus</a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="d-flex flex-column align-items-end h-100 justify-content-between">
                                <div class="item-price">Rp 85.000</div>
                                <div class="quantity-control">
                                    <button type="button">-</button>
                                    <input type="text" value="2" readonly>
                                    <button type="button">+</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="cart-item">
                    <div class="row g-3">
                        <div class="col-auto">
                            <div class="form-check pt-4">
                                <input class="form-check-input" type="checkbox" id="item3">
                                <label class="form-check-label" for="item3"></label>
                            </div>
                        </div>
                        <div class="col-auto">
                            <img src="{{ asset('images/mug.png') }}" class="item-img" alt="Mug">
                        </div>
                        <div class="col">
                            <div class="item-details">
                                <h5>Mug Custom</h5>
                                <p>Bahan: Keramik</p>
                                <p>Ukuran: Standard 300ml</p>
                                <p>Jenis: Full Color</p>
                                <p>
                                    Upload Desain: 
                                    <span class="upload-status status-pending">Belum diupload</span>
                                    <button class="btn upload-btn btn-sm ms-2">
                                        <i class="fas fa-upload me-1"></i> Upload Desain
                                    </button>
                                </p>
                                <div class="d-block mt-1">
                                    <a href="#" class="item-action"><i class="fas fa-trash-alt me-1"></i> Hapus</a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="d-flex flex-column align-items-end h-100 justify-content-between">
                                <div class="item-price">Rp 45.000</div>
                                <div class="quantity-control">
                                    <button type="button">-</button>
                                    <input type="text" value="1" readonly>
                                    <button type="button">+</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Order Summary -->
        <div class="col-lg-4">
            <div class="summary-card">
                <h5>Ringkasan Pesanan</h5>
                
                <div class="summary-item">
                    <span>Subtotal (3 produk)</span>
                    <span>Rp 320.000</span>
                </div>
                
                <div class="summary-item">
                    <span>Biaya Desain</span>
                    <span>Rp 50.000</span>
                </div>
                
                <div class="summary-item summary-total">
                    <span>Total</span>
                    <span>Rp 370.000</span>
                </div>
                
                <button class="checkout-btn">CHECKOUT SEKARANG</button>
            </div>
        </div>
    </div>
    
    <!-- Empty Cart State (hidden by default) -->
    <div class="text-center py-5 d-none">
        <img src="{{ asset('images/empty-cart.png') }}" alt="Keranjang Kosong" style="max-width: 200px; opacity: 0.7;">
        <h4 class="mt-4">Keranjang Anda kosong</h4>
        <p class="text-muted">Anda belum menambahkan produk apapun ke keranjang</p>
        <a href="/" class="btn btn-primary px-4 mt-2">Belanja Sekarang</a>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Select All Functionality
    document.getElementById('selectAll').addEventListener('change', function() {
        const checkboxes = document.querySelectorAll('.cart-item .form-check-input');
        checkboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
    });
    
    // Quantity Controls
    document.querySelectorAll('.quantity-control button').forEach(button => {
        button.addEventListener('click', function() {
            const input = this.parentNode.querySelector('input');
            let value = parseInt(input.value);
            
            if (this.textContent === '+') {
                input.value = value + 1;
            } else if (value > 1) {
                input.value = value - 1;
            }
        });
    });
</script>
@endsection