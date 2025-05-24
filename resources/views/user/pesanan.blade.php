@extends('user.layouts.app')

@section('custom-css')
<style>
    /* Tab styling */
    .nav-tabs {
        border-bottom: 1px solid #dee2e6;
        margin-bottom: 20px;
    }
    .nav-tabs .nav-item .nav-link {
        color: #6c757d;
        border: none;
        font-weight: 500;
        padding: 10px 15px;
        border-bottom: 3px solid transparent;
        transition: all 0.3s;
    }
    .nav-tabs .nav-item .nav-link.active {
        color: #4361ee;
        border-bottom: 3px solid #4361ee;
        background-color: transparent;
    }
    .nav-tabs .nav-item .nav-link:hover {
        color: #4361ee;
        border-bottom-color: #a8b2ff;
    }
    
    /* Order card styling */
    .order-card {
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        margin-bottom: 20px;
        background-color: #fff;
        transition: transform 0.2s;
    }
    .order-card:hover {
        transform: translateY(-5px);
    }
    .order-header {
        padding: 15px;
        border-bottom: 1px solid #eee;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .order-body {
        padding: 15px;
    }
    .order-footer {
        padding: 15px;
        border-top: 1px solid #eee;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .product-item {
        display: flex;
        margin-bottom: 15px;
        padding-bottom: 15px;
        border-bottom: 1px solid #f5f5f5;
    }
    .product-item:last-child {
        margin-bottom: 0;
        padding-bottom: 0;
        border-bottom: none;
    }
    .product-image {
        width: 80px;
        height: 80px;
        border-radius: 5px;
        object-fit: cover;
        margin-right: 15px;
    }
    .product-details {
        flex: 1;
    }
    .product-title {
        font-weight: 500;
        margin-bottom: 5px;
        font-size: 16px;
    }
    .product-variant {
        color: #6c757d;
        font-size: 14px;
        margin-bottom: 5px;
    }
    .product-price {
        color: #333;
        font-weight: 600;
    }
    .order-id {
        color: #6c757d;
        font-size: 14px;
    }
    .status-badge {
        padding: 5px 10px;
        border-radius: 50px;
        font-size: 12px;
        font-weight: 500;
    }
    .status-pending {
        background-color: #fff4de;
        color: #ffa426;
    }
    .status-processing {
        background-color: #e0f4ff;
        color: #3498db;
    }
    .status-shipping {
        background-color: #e7f9ed;
        color: #2ecc71;
    }
    .status-complete {
        background-color: #dcf7e8;
        color: #27ae60;
    }
    .status-cancelled {
        background-color: #ffe5e5;
        color: #e74c3c;
    }
    .total-section {
        text-align: right;
    }
    .total-items {
        color: #6c757d;
        font-size: 14px;
        margin-bottom: 5px;
    }
    .total-price {
        font-size: 18px;
        font-weight: 600;
        color: #4361ee;
    }
    .action-btn {
        border-radius: 5px;
        padding: 8px 15px;
        margin-left: 10px;
        font-weight: 500;
        font-size: 14px;
    }
    .btn-pay {
        background-color: #4361ee;
        color: white;
    }
    .btn-track {
        background-color: #4361ee;
        color: white;
    }
    .btn-review {
        background-color: white;
        color: #4361ee;
        border: 1px solid #4361ee;
    }
    .btn-cancel {
        background-color: white;
        color: #e74c3c;
        border: 1px solid #e74c3c;
    }
    .btn-help {
        background-color: white;
        color: #6c757d;
        border: 1px solid #6c757d;
    }
    .empty-order {
        text-align: center;
        padding: 40px 0;
    }
    .empty-icon {
        font-size: 50px;
        color: #d1d1d1;
        margin-bottom: 15px;
    }
    .section-title {
        font-weight: 600;
        margin-bottom: 25px;
        color: #333;
    }
    .order-page {
        background-color: #f9fafb;
        padding: 30px 0;
        min-height: 70vh;
    }
</style>
@endsection

@section('content')
<section class="order-page">
    <div class="container">
        <h2 class="section-title">Pesanan Saya</h2>
        
        <!-- Tabs -->
        <ul class="nav nav-tabs" id="orderTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <a class="nav-link active" id="all-tab" data-bs-toggle="tab" href="#all" role="tab" aria-controls="all" aria-selected="true">Semua</a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link" id="unpaid-tab" data-bs-toggle="tab" href="#unpaid" role="tab" aria-controls="unpaid" aria-selected="false">Belum Dibayar</a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link" id="order-tab" data-bs-toggle="tab" href="#order" role="tab" aria-controls="order" aria-selected="false">Pemesanan</a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link" id="processing-tab" data-bs-toggle="tab" href="#processing" role="tab" aria-controls="processing" aria-selected="false">Sedang Diproses</a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link" id="shipping-tab" data-bs-toggle="tab" href="#shipping" role="tab" aria-controls="shipping" aria-selected="false">Sedang Dikirim</a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link" id="completed-tab" data-bs-toggle="tab" href="#completed" role="tab" aria-controls="completed" aria-selected="false">Selesai</a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link" id="cancelled-tab" data-bs-toggle="tab" href="#cancelled" role="tab" aria-controls="cancelled" aria-selected="false">Dibatalkan</a>
            </li>
        </ul>
        
        <!-- Tab Content -->
        <div class="tab-content" id="orderTabsContent">
            <!-- All Orders Tab -->
            <div class="tab-pane fade show active" id="all" role="tabpanel" aria-labelledby="all-tab">
                <!-- Belum Dibayar Order Card -->
                <div class="order-card">
                    <div class="order-header">
                        <span class="order-id">Pesanan #PO123456</span>
                        <span class="status-badge status-pending">Belum Dibayar</span>
                    </div>
                    <div class="order-body">
                        <div class="product-item">
                            <img src="{{ asset('images/products/kaos.jpg') }}" alt="Kaos Custom" class="product-image">
                            <div class="product-details">
                                <h5 class="product-title">Kaos Custom Lengan Pendek</h5>
                                <p class="product-variant">Ukuran: XL, Bahan: Cotton Combed 30s, Warna: Hitam</p>
                                <p class="product-price">Rp 85.000 x 2</p>
                            </div>
                        </div>
                    </div>
                    <div class="order-footer">
                        <div class="total-section">
                            <p class="total-items">Total 2 item</p>
                            <p class="total-price">Rp 170.000</p>
                        </div>
                        <div class="action-buttons">
                            <button class="btn action-btn btn-cancel">Batalkan</button>
                            <button class="btn action-btn btn-pay">Bayar Sekarang</button>
                        </div>
                    </div>
                </div>
                
                <!-- Pemesanan Order Card -->
                <div class="order-card">
                    <div class="order-header">
                        <span class="order-id">Pesanan #PO123457</span>
                        <span class="status-badge status-pending">Pemesanan</span>
                    </div>
                    <div class="order-body">
                        <div class="product-item">
                            <img src="{{ asset('images/products/hoodie.jpg') }}" alt="Hoodie Custom" class="product-image">
                            <div class="product-details">
                                <h5 class="product-title">Hoodie Custom</h5>
                                <p class="product-variant">Ukuran: L, Bahan: Fleece, Warna: Navy</p>
                                <p class="product-price">Rp 200.000 x 1</p>
                            </div>
                        </div>
                    </div>
                    <div class="order-footer">
                        <div class="total-section">
                            <p class="total-items">Total 1 item</p>
                            <p class="total-price">Rp 200.000</p>
                        </div>
                        <div class="action-buttons">
                            <button class="btn action-btn btn-cancel">Batalkan</button>
                            <button class="btn action-btn btn-help">Hubungi Admin</button>
                        </div>
                    </div>
                </div>
                
                <!-- Sedang Diproses Order Card -->
                <div class="order-card">
                    <div class="order-header">
                        <span class="order-id">Pesanan #PO123458</span>
                        <span class="status-badge status-processing">Sedang Diproses</span>
                    </div>
                    <div class="order-body">
                        <div class="product-item">
                            <img src="{{ asset('images/products/topi.jpg') }}" alt="Topi Custom" class="product-image">
                            <div class="product-details">
                                <h5 class="product-title">Topi Custom Snapback</h5>
                                <p class="product-variant">Ukuran: All Size, Bahan: Premium, Warna: Hitam</p>
                                <p class="product-price">Rp 75.000 x 3</p>
                            </div>
                        </div>
                    </div>
                    <div class="order-footer">
                        <div class="total-section">
                            <p class="total-items">Total 3 item</p>
                            <p class="total-price">Rp 225.000</p>
                        </div>
                        <div class="action-buttons">
                            <button class="btn action-btn btn-help">Hubungi Admin</button>
                        </div>
                    </div>
                </div>
                
                <!-- Sedang Dikirim Order Card -->
                <div class="order-card">
                    <div class="order-header">
                        <span class="order-id">Pesanan #PO123459</span>
                        <span class="status-badge status-shipping">Sedang Dikirim</span>
                    </div>
                    <div class="order-body">
                        <div class="product-item">
                            <img src="{{ asset('images/products/banner.jpg') }}" alt="Banner Custom" class="product-image">
                            <div class="product-details">
                                <h5 class="product-title">Banner Custom Outdoor</h5>
                                <p class="product-variant">Ukuran: 1x3m, Bahan: Flexi Korea</p>
                                <p class="product-price">Rp 150.000 x 1</p>
                            </div>
                        </div>
                    </div>
                    <div class="order-footer">
                        <div class="total-section">
                            <p class="total-items">Total 1 item</p>
                            <p class="total-price">Rp 150.000</p>
                        </div>
                        <div class="action-buttons">
                            <button class="btn action-btn btn-track">Lacak Pengiriman</button>
                        </div>
                    </div>
                </div>
                
                <!-- Selesai Order Card -->
                <div class="order-card">
                    <div class="order-header">
                        <span class="order-id">Pesanan #PO123460</span>
                        <span class="status-badge status-complete">Selesai</span>
                    </div>
                    <div class="order-body">
                        <div class="product-item">
                            <img src="{{ asset('images/products/stiker.jpg') }}" alt="Stiker Custom" class="product-image">
                            <div class="product-details">
                                <h5 class="product-title">Stiker Custom Die-Cut</h5>
                                <p class="product-variant">Ukuran: 10x10cm, Bahan: Vinyl Glossy</p>
                                <p class="product-price">Rp 5.000 x 20</p>
                            </div>
                        </div>
                    </div>
                    <div class="order-footer">
                        <div class="total-section">
                            <p class="total-items">Total 20 item</p>
                            <p class="total-price">Rp 100.000</p>
                        </div>
                        <div class="action-buttons">
                            <button class="btn action-btn btn-review">Beri Ulasan</button>
                            <button class="btn action-btn btn-pay">Beli Lagi</button>
                        </div>
                    </div>
                </div>
                
                <!-- Dibatalkan Order Card -->
                <div class="order-card">
                    <div class="order-header">
                        <span class="order-id">Pesanan #PO123461</span>
                        <span class="status-badge status-cancelled">Dibatalkan</span>
                    </div>
                    <div class="order-body">
                        <div class="product-item">
                            <img src="{{ asset('images/products/jaket.jpg') }}" alt="Jaket Custom" class="product-image">
                            <div class="product-details">
                                <h5 class="product-title">Jaket Coach Custom</h5>
                                <p class="product-variant">Ukuran: M, Bahan: Taslan, Warna: Merah</p>
                                <p class="product-price">Rp 175.000 x 2</p>
                            </div>
                        </div>
                    </div>
                    <div class="order-footer">
                        <div class="total-section">
                            <p class="total-items">Total 2 item</p>
                            <p class="total-price">Rp 350.000</p>
                        </div>
                        <div class="action-buttons">
                            <button class="btn action-btn btn-pay">Beli Lagi</button>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Belum Dibayar Tab -->
            <div class="tab-pane fade" id="unpaid" role="tabpanel" aria-labelledby="unpaid-tab">
                <div class="order-card">
                    <div class="order-header">
                        <span class="order-id">Pesanan #PO123456</span>
                        <span class="status-badge status-pending">Belum Dibayar</span>
                    </div>
                    <div class="order-body">
                        <div class="product-item">
                            <img src="{{ asset('images/products/kaos.jpg') }}" alt="Kaos Custom" class="product-image">
                            <div class="product-details">
                                <h5 class="product-title">Kaos Custom Lengan Pendek</h5>
                                <p class="product-variant">Ukuran: XL, Bahan: Cotton Combed 30s, Warna: Hitam</p>
                                <p class="product-price">Rp 85.000 x 2</p>
                            </div>
                        </div>
                    </div>
                    <div class="order-footer">
                        <div class="total-section">
                            <p class="total-items">Total 2 item</p>
                            <p class="total-price">Rp 170.000</p>
                        </div>
                        <div class="action-buttons">
                            <button class="btn action-btn btn-cancel">Batalkan</button>
                            <button class="btn action-btn btn-pay">Bayar Sekarang</button>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Pemesanan Tab -->
            <div class="tab-pane fade" id="order" role="tabpanel" aria-labelledby="order-tab">
                <div class="order-card">
                    <div class="order-header">
                        <span class="order-id">Pesanan #PO123457</span>
                        <span class="status-badge status-pending">Pemesanan</span>
                    </div>
                    <div class="order-body">
                        <div class="product-item">
                            <img src="{{ asset('images/products/hoodie.jpg') }}" alt="Hoodie Custom" class="product-image">
                            <div class="product-details">
                                <h5 class="product-title">Hoodie Custom</h5>
                                <p class="product-variant">Ukuran: L, Bahan: Fleece, Warna: Navy</p>
                                <p class="product-price">Rp 200.000 x 1</p>
                            </div>
                        </div>
                    </div>
                    <div class="order-footer">
                        <div class="total-section">
                            <p class="total-items">Total 1 item</p>
                            <p class="total-price">Rp 200.000</p>
                        </div>
                        <div class="action-buttons">
                            <button class="btn action-btn btn-cancel">Batalkan</button>
                            <button class="btn action-btn btn-help">Hubungi Admin</button>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Sedang Diproses Tab -->
            <div class="tab-pane fade" id="processing" role="tabpanel" aria-labelledby="processing-tab">
                <div class="order-card">
                    <div class="order-header">
                        <span class="order-id">Pesanan #PO123458</span>
                        <span class="status-badge status-processing">Sedang Diproses</span>
                    </div>
                    <div class="order-body">
                        <div class="product-item">
                            <img src="{{ asset('images/products/topi.jpg') }}" alt="Topi Custom" class="product-image">
                            <div class="product-details">
                                <h5 class="product-title">Topi Custom Snapback</h5>
                                <p class="product-variant">Ukuran: All Size, Bahan: Premium, Warna: Hitam</p>
                                <p class="product-price">Rp 75.000 x 3</p>
                            </div>
                        </div>
                    </div>
                    <div class="order-footer">
                        <div class="total-section">
                            <p class="total-items">Total 3 item</p>
                            <p class="total-price">Rp 225.000</p>
                        </div>
                        <div class="action-buttons">
                            <button class="btn action-btn btn-help">Hubungi Admin</button>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Sedang Dikirim Tab -->
            <div class="tab-pane fade" id="shipping" role="tabpanel" aria-labelledby="shipping-tab">
                <div class="order-card">
                    <div class="order-header">
                        <span class="order-id">Pesanan #PO123459</span>
                        <span class="status-badge status-shipping">Sedang Dikirim</span>
                    </div>
                    <div class="order-body">
                        <div class="product-item">
                            <img src="{{ asset('images/products/banner.jpg') }}" alt="Banner Custom" class="product-image">
                            <div class="product-details">
                                <h5 class="product-title">Banner Custom Outdoor</h5>
                                <p class="product-variant">Ukuran: 1x3m, Bahan: Flexi Korea</p>
                                <p class="product-price">Rp 150.000 x 1</p>
                            </div>
                        </div>
                    </div>
                    <div class="order-footer">
                        <div class="total-section">
                            <p class="total-items">Total 1 item</p>
                            <p class="total-price">Rp 150.000</p>
                        </div>
                        <div class="action-buttons">
                            <button class="btn action-btn btn-track">Lacak Pengiriman</button>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Selesai Tab -->
            <div class="tab-pane fade" id="completed" role="tabpanel" aria-labelledby="completed-tab">
                <div class="order-card">
                    <div class="order-header">
                        <span class="order-id">Pesanan #PO123460</span>
                        <span class="status-badge status-complete">Selesai</span>
                    </div>
                    <div class="order-body">
                        <div class="product-item">
                            <img src="{{ asset('images/products/stiker.jpg') }}" alt="Stiker Custom" class="product-image">
                            <div class="product-details">
                                <h5 class="product-title">Stiker Custom Die-Cut</h5>
                                <p class="product-variant">Ukuran: 10x10cm, Bahan: Vinyl Glossy</p>
                                <p class="product-price">Rp 5.000 x 20</p>
                            </div>
                        </div>
                    </div>
                    <div class="order-footer">
                        <div class="total-section">
                            <p class="total-items">Total 20 item</p>
                            <p class="total-price">Rp 100.000</p>
                        </div>
                        <div class="action-buttons">
                            <button class="btn action-btn btn-review">Beri Ulasan</button>
                            <button class="btn action-btn btn-pay">Beli Lagi</button>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Dibatalkan Tab -->
            <div class="tab-pane fade" id="cancelled" role="tabpanel" aria-labelledby="cancelled-tab">
                <div class="order-card">
                    <div class="order-header">
                        <span class="order-id">Pesanan #PO123461</span>
                        <span class="status-badge status-cancelled">Dibatalkan</span>
                    </div>
                    <div class="order-body">
                        <div class="product-item">
                            <img src="{{ asset('images/products/jaket.jpg') }}" alt="Jaket Custom" class="product-image">
                            <div class="product-details">
                                <h5 class="product-title">Jaket Coach Custom</h5>
                                <p class="product-variant">Ukuran: M, Bahan: Taslan, Warna: Merah</p>
                                <p class="product-price">Rp 175.000 x 2</p>
                            </div>
                        </div>
                    </div>
                    <div class="order-footer">
                        <div class="total-section">
                            <p class="total-items">Total 2 item</p>
                            <p class="total-price">Rp 350.000</p>
                        </div>
                        <div class="action-buttons">
                            <button class="btn action-btn btn-pay">Beli Lagi</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection