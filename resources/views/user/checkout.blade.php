@extends('user.layouts.app')

@section('title', 'Checkout')

@push('meta')
<meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('custom-css')
<style>
    .checkout-container {
        padding: 20px 0;
        background-color: #f8f9fa;
    }
    
    .checkout-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        margin-bottom: 20px;
        padding: 20px;
    }
    
    .section-title {
        font-size: 18px;
        font-weight: 600;
        margin-bottom: 15px;
        color: #333;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    
    .delivery-options {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 15px;
        margin-bottom: 20px;
    }
    
    .delivery-option {
        border: 2px solid #e5e5e5;
        border-radius: 12px;
        padding: 20px;
        cursor: pointer;
        transition: all 0.3s ease;
        text-align: center;
        position: relative;
    }
    
    .delivery-option:hover {
        border-color: #4361ee;
        background-color: #f8f9ff;
    }
    
    .delivery-option.active {
        border-color: #4361ee;
        background-color: #f8f9ff;
    }
    
    .delivery-option .icon {
        font-size: 32px;
        margin-bottom: 10px;
        display: block;
    }
    
    .delivery-option .title {
        font-weight: 600;
        font-size: 16px;
        margin-bottom: 5px;
        color: #333;
    }
    
    .delivery-option .desc {
        font-size: 14px;
        color: #666;
        margin: 0;
    }
    
    .delivery-option input[type="radio"] {
        position: absolute;
        top: 10px;
        right: 10px;
    }
    
    .address-card {
        border: 1px solid #e5e5e5;
        border-radius: 8px;
        padding: 15px;
        margin-bottom: 10px;
        cursor: pointer;
        transition: all 0.3s ease;
    }
    
    .address-card:hover,
    .address-card.selected {
        border-color: #4361ee;
        background-color: #f8f9ff;
    }
    
    .address-label {
        font-weight: 600;
        color: #4361ee;
        font-size: 12px;
        text-transform: uppercase;
        margin-bottom: 5px;
    }
    
    .address-name {
        font-weight: 600;
        margin-bottom: 3px;
    }
    
    .address-detail {
        color: #666;
        font-size: 14px;
        line-height: 1.4;
    }
    
    .ekspedisi-option {
        border: 1px solid #e5e5e5;
        border-radius: 8px;
        padding: 15px;
        margin-bottom: 10px;
        cursor: pointer;
        transition: all 0.3s ease;
        display: flex;
        justify-content: between;
        align-items: center;
    }
    
    .ekspedisi-option:hover,
    .ekspedisi-option.selected {
        border-color: #4361ee;
        background-color: #f8f9ff;
    }
    
    .ekspedisi-info {
        flex: 1;
    }
    
    .ekspedisi-name {
        font-weight: 600;
        margin-bottom: 3px;
    }
    
    .ekspedisi-service {
        color: #666;
        font-size: 14px;
    }
    
    .ekspedisi-price {
        text-align: right;
    }
    
    .ekspedisi-cost {
        font-weight: 600;
        color: #4361ee;
    }
    
    .ekspedisi-estimate {
        font-size: 12px;
        color: #666;
    }
    
    .store-info {
        background: #f8f9fa;
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 15px;
    }
    
    .store-info-item {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        margin-bottom: 15px;
    }
    
    .store-info-item:last-child {
        margin-bottom: 0;
    }
    
    .store-info-icon {
        color: #4361ee;
        font-size: 18px;
        margin-top: 2px;
    }
    
    .store-info-content h6 {
        margin: 0;
        font-weight: 600;
        color: #333;
        margin-bottom: 3px;
    }
    
    .store-info-content p {
        margin: 0;
        color: #666;
        font-size: 14px;
        line-height: 1.4;
    }
    
    .payment-option {
        border: 1px solid #e5e5e5;
        border-radius: 8px;
        padding: 15px;
        margin-bottom: 10px;
        cursor: pointer;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        gap: 12px;
    }
    
    .payment-option:hover,
    .payment-option.selected {
        border-color: #4361ee;
        background-color: #f8f9ff;
    }
    
    .payment-icon {
        font-size: 24px;
        color: #4361ee;
    }
    
    .order-item {
        display: flex;
        gap: 15px;
        padding: 15px 0;
        border-bottom: 1px solid #f0f0f0;
    }
    
    .order-item:last-child {
        border-bottom: none;
    }
    
    .order-item-image {
        width: 60px;
        height: 60px;
        border-radius: 8px;
        object-fit: cover;
    }
    
    .order-item-details {
        flex: 1;
    }
    
    .order-item-name {
        font-weight: 600;
        margin-bottom: 5px;
        font-size: 14px;
    }
    
    .order-item-specs {
        color: #666;
        font-size: 12px;
        line-height: 1.3;
    }
    
    .order-item-price {
        text-align: right;
    }
    
    .order-item-quantity {
        font-size: 12px;
        color: #666;
    }
    
    .order-item-total {
        font-weight: 600;
        color: #4361ee;
    }
    
    .summary-row {
        display: flex;
        justify-content: space-between;
        padding: 8px 0;
    }
    
    .summary-row.total {
        border-top: 1px solid #e5e5e5;
        padding-top: 15px;
        margin-top: 10px;
        font-weight: 600;
        font-size: 16px;
    }
    
    .btn-add-address {
        border: 2px dashed #4361ee;
        background: transparent;
        color: #4361ee;
        padding: 15px;
        border-radius: 8px;
        width: 100%;
        font-weight: 600;
        transition: all 0.3s ease;
    }
    
    .btn-add-address:hover {
        background: #f8f9ff;
    }
    
    .checkout-actions {
        background: white;
        border-top: 1px solid #e5e5e5;
        padding: 20px;
        position: sticky;
        bottom: 0;
        margin: 0 -15px -15px -15px;
        border-radius: 0 0 12px 12px;
    }
    
    .btn-checkout {
        background: #4361ee;
        color: white;
        border: none;
        padding: 15px 30px;
        border-radius: 8px;
        font-weight: 600;
        font-size: 16px;
        width: 100%;
        transition: all 0.3s ease;
    }
    
    .btn-checkout:hover {
        background: #3651d4;
    }
    
    .btn-back {
        background: transparent;
        color: #666;
        border: 1px solid #e5e5e5;
        padding: 12px 20px;
        border-radius: 8px;
        font-weight: 500;
        text-decoration: none;
        display: inline-block;
        transition: all 0.3s ease;
    }
    
    .btn-back:hover {
        border-color: #4361ee;
        color: #4361ee;
        text-decoration: none;
    }
    
    @media (max-width: 768px) {
        .delivery-options {
            grid-template-columns: 1fr;
        }
        
        .checkout-actions {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            margin: 0;
            border-radius: 0;
            box-shadow: 0 -2px 10px rgba(0,0,0,0.1);
        }
        
        .checkout-container {
            padding-bottom: 100px;
        }
    }
</style>
@endsection

@section('content')
<!-- Breadcrumb -->
<div class="bg-light py-3">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="/" class="text-decoration-none">Home</a></li>
                <li class="breadcrumb-item"><a href="/keranjang" class="text-decoration-none">Keranjang</a></li>
                <li class="breadcrumb-item active">Checkout</li>
            </ol>
        </nav>
    </div>
</div>

<div class="checkout-container">
    <div class="container">
        <div class="row">
            <div class="col-lg-8">
                <!-- Delivery Options -->
                <div class="checkout-card">
                    <h5 class="section-title">
                        <i class="fas fa-truck"></i>
                        Metode Pengiriman
                    </h5>
                    <div class="delivery-options">
                        <div class="delivery-option active" data-method="antar">
                            <input type="radio" name="delivery_method" value="antar" checked>
                            <span class="icon">🚗</span>
                            <div class="title">Pesan Antar</div>
                            <p class="desc">Estimasi: 2-3 hari<br>+ Ongkir</p>
                        </div>
                        <div class="delivery-option" data-method="ambil">
                            <input type="radio" name="delivery_method" value="ambil">
                            <span class="icon">🏪</span>
                            <div class="title">Ambil Sendiri</div>
                            <p class="desc">Gratis ongkir</p>
                        </div>
                    </div>
                </div>

                <!-- Alamat Pengiriman (Untuk Pesan Antar) -->
                <div class="checkout-card" id="address-section">
                    <h5 class="section-title">
                        <i class="fas fa-map-marker-alt"></i>
                        Alamat Pengiriman
                    </h5>
                    
                    <div class="address-card selected">
                        <input type="radio" name="selected_address" value="1" checked style="display: none;">
                        <div class="address-label">Rumah</div>
                        <div class="address-name">Shafa</div>
                        <div class="address-detail">
                            Jl. Prof. Hamka No. 123, RT 01/RW 02<br>
                            Tembalang, Semarang, Jawa Tengah 50275<br>
                            📱 +62 895-2686-1571
                        </div>
                    </div>
                    
                    <div class="address-card">
                        <input type="radio" name="selected_address" value="2" style="display: none;">
                        <div class="address-label">Kantor</div>
                        <div class="address-name">Shafa</div>
                        <div class="address-detail">
                            Jl. Soekarno Hatta No. 456<br>
                            Banyumanik, Semarang, Jawa Tengah 50268<br>
                            📱 +62 895-2686-1571
                        </div>
                    </div>
                    
                    <button class="btn-add-address">
                        <i class="fas fa-plus me-2"></i>Tambah Alamat Baru
                    </button>
                </div>

                <!-- Pilihan Ekspedisi (Untuk Pesan Antar) -->
                <div class="checkout-card" id="shipping-section">
                    <h5 class="section-title">
                        <i class="fas fa-shipping-fast"></i>
                        Pilihan Ekspedisi
                    </h5>
                    
                    <div class="ekspedisi-option selected">
                        <input type="radio" name="selected_ekspedisi" value="1" checked style="display: none;">
                        <div class="ekspedisi-info">
                            <div class="ekspedisi-name">JNE Regular</div>
                            <div class="ekspedisi-service">Paket reguler</div>
                        </div>
                        <div class="ekspedisi-price">
                            <div class="ekspedisi-cost">Rp 15.000</div>
                            <div class="ekspedisi-estimate">2-3 hari</div>
                        </div>
                    </div>
                    
                    <div class="ekspedisi-option">
                        <input type="radio" name="selected_ekspedisi" value="2" style="display: none;">
                        <div class="ekspedisi-info">
                            <div class="ekspedisi-name">J&T Express</div>
                            <div class="ekspedisi-service">Paket reguler</div>
                        </div>
                        <div class="ekspedisi-price">
                            <div class="ekspedisi-cost">Rp 18.000</div>
                            <div class="ekspedisi-estimate">2-3 hari</div>
                        </div>
                    </div>
                    
                    <div class="ekspedisi-option">
                        <input type="radio" name="selected_ekspedisi" value="3" style="display: none;">
                        <div class="ekspedisi-info">
                            <div class="ekspedisi-name">SiCepat REG</div>
                            <div class="ekspedisi-service">Paket reguler</div>
                        </div>
                        <div class="ekspedisi-price">
                            <div class="ekspedisi-cost">Rp 20.000</div>
                            <div class="ekspedisi-estimate">1-2 hari</div>
                        </div>
                    </div>
                </div>

                <!-- Informasi Toko (Untuk Ambil Sendiri) -->
                <div class="checkout-card" id="store-section" style="display: none;">
                    <h5 class="section-title">
                        <i class="fas fa-store"></i>
                        Informasi Toko
                    </h5>
                    
                    <div class="store-info">
                        <div class="store-info-item">
                            <i class="fas fa-map-marker-alt store-info-icon"></i>
                            <div class="store-info-content">
                                <h6>Alamat Toko</h6>
                                <p>Jl. Prof. Soedarto, Tembalang<br>Semarang, Jawa Tengah 50275</p>
                            </div>
                        </div>
                        
                        <div class="store-info-item">
                            <i class="fas fa-clock store-info-icon"></i>
                            <div class="store-info-content">
                                <h6>Jam Operasional</h6>
                                <p>Senin - Jumat: 08:00 - 17:00<br>
                                   Sabtu: 08:00 - 15:00<br>
                                   Minggu: Tutup</p>
                            </div>
                        </div>
                        
                        <div class="store-info-item">
                            <i class="fas fa-phone store-info-icon"></i>
                            <div class="store-info-content">
                                <h6>Kontak</h6>
                                <p>(024) 7460054<br>
                                   WhatsApp: +62 812-3456-7890</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label"><strong>Catatan Pengambilan</strong> <span class="text-muted">(Opsional)</span></label>
                        <textarea class="form-control" rows="3" placeholder="Contoh: Mohon SMS jika pesanan sudah siap diambil"></textarea>
                        <small class="text-muted">Kami akan menghubungi Anda ketika pesanan siap diambil</small>
                    </div>
                </div>

                <!-- Metode Pembayaran -->
                <div class="checkout-card">
                    <h5 class="section-title">
                        <i class="fas fa-credit-card"></i>
                        Metode Pembayaran
                    </h5>
                    
                    <div class="payment-option selected">
                        <input type="radio" name="payment_method" value="cod" checked style="display: none;">
                        <i class="fas fa-money-bill-wave payment-icon"></i>
                        <div>
                            <div class="fw-semibold">COD (Bayar di Tempat)</div>
                            <small class="text-muted">Bayar saat pesanan diterima</small>
                        </div>
                    </div>
                    
                    <div class="payment-option">
                        <input type="radio" name="payment_method" value="qris" style="display: none;">
                        <i class="fas fa-qrcode payment-icon"></i>
                        <div>
                            <div class="fw-semibold">QRIS</div>
                            <small class="text-muted">Scan QR Code untuk pembayaran</small>
                        </div>
                    </div>
                </div>

                <!-- Catatan Pesanan -->
                <div class="checkout-card">
                    <h5 class="section-title">
                        <i class="fas fa-sticky-note"></i>
                        Catatan Pesanan
                    </h5>
                    <textarea class="form-control" rows="3" placeholder="Catatan khusus untuk pesanan Anda (opsional)"></textarea>
                </div>
            </div>

            <!-- Ringkasan Pesanan -->
            <div class="col-lg-4">
                <div class="checkout-card">
                    <h5 class="section-title">
                        <i class="fas fa-receipt"></i>
                        Ringkasan Pesanan
                    </h5>
                    
                    <div class="order-item">
                        <img src="https://via.placeholder.com/60x60/4361ee/ffffff?text=KAOS" class="order-item-image" alt="Kaos">
                        <div class="order-item-details">
                            <div class="order-item-name">Kaos Custom</div>
                            <div class="order-item-specs">
                                Cotton Combed 24s, Ukuran: S<br>
                                Jenis: Lengan Bolong
                            </div>
                        </div>
                        <div class="order-item-price">
                            <div class="order-item-quantity">6x</div>
                            <div class="order-item-total">Rp 630.000</div>
                        </div>
                    </div>
                    
                    <div class="order-item">
                        <img src="https://via.placeholder.com/60x60/4361ee/ffffff?text=JAKET" class="order-item-image" alt="Jaket">
                        <div class="order-item-details">
                            <div class="order-item-name">Jaket Custom</div>
                            <div class="order-item-specs">
                                Fleece, Ukuran: M<br>
                                Jenis: Lengan Panjang
                            </div>
                        </div>
                        <div class="order-item-price">
                            <div class="order-item-quantity">1x</div>
                            <div class="order-item-total">Rp 180.000</div>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <div class="summary-row">
                        <span>Subtotal (7 item)</span>
                        <span>Rp 810.000</span>
                    </div>
                    
                    <div class="summary-row">
                        <span>Biaya Desain</span>
                        <span>Rp 200.000</span>
                    </div>
                    
                    <div class="summary-row" id="shipping-cost-row">
                        <span>Ongkos Kirim</span>
                        <span id="shipping-cost">Rp 15.000</span>
                    </div>
                    
                    <div class="summary-row total">
                        <span>Total</span>
                        <span id="grand-total">Rp 1.025.000</span>
                    </div>
                    
                    <div class="checkout-actions">
                        <button class="btn-checkout" type="button">
                            <i class="fas fa-shopping-bag me-2"></i>
                            Buat Pesanan
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const deliveryOptions = document.querySelectorAll('.delivery-option');
    const addressSection = document.getElementById('address-section');
    const shippingSection = document.getElementById('shipping-section');
    const storeSection = document.getElementById('store-section');
    const shippingCostRow = document.getElementById('shipping-cost-row');
    const shippingCost = document.getElementById('shipping-cost');
    const grandTotal = document.getElementById('grand-total');
    
    const addressCards = document.querySelectorAll('.address-card:not(.btn-add-address)');
    const ekspedisiOptions = document.querySelectorAll('.ekspedisi-option');
    const paymentOptions = document.querySelectorAll('.payment-option');
    
    let subtotal = 810000;
    let biayaDesain = 200000;
    let currentShippingCost = 15000;
    
    // Delivery method switching
    deliveryOptions.forEach(option => {
        option.addEventListener('click', function() {
            const method = this.dataset.method;
            const radio = this.querySelector('input[type="radio"]');
            
            // Update UI
            deliveryOptions.forEach(opt => opt.classList.remove('active'));
            this.classList.add('active');
            radio.checked = true;
            
            if (method === 'antar') {
                addressSection.style.display = 'block';
                shippingSection.style.display = 'block';
                storeSection.style.display = 'none';
                shippingCostRow.style.display = 'flex';
                currentShippingCost = 15000; // Default JNE
            } else {
                addressSection.style.display = 'none';
                shippingSection.style.display = 'none';
                storeSection.style.display = 'block';
                shippingCostRow.style.display = 'none';
                currentShippingCost = 0;
            }
            
            updateTotal();
        });
    });
    
    // Address selection
    addressCards.forEach(card => {
        card.addEventListener('click', function() {
            const radio = this.querySelector('input[type="radio"]');
            if (radio) {
                addressCards.forEach(c => c.classList.remove('selected'));
                this.classList.add('selected');
                radio.checked = true;
            }
        });
    });
    
    // Ekspedisi selection
    ekspedisiOptions.forEach(option => {
        option.addEventListener('click', function() {
            const radio = this.querySelector('input[type="radio"]');
            const cost = this.querySelector('.ekspedisi-cost').textContent;
            
            ekspedisiOptions.forEach(opt => opt.classList.remove('selected'));
            this.classList.add('selected');
            radio.checked = true;
            
            // Update shipping cost
            currentShippingCost = parseInt(cost.replace(/[^\d]/g, ''));
            shippingCost.textContent = cost;
            updateTotal();
        });
    });
    
    // Payment method selection
    paymentOptions.forEach(option => {
        option.addEventListener('click', function() {
            const radio = this.querySelector('input[type="radio"]');
            
            paymentOptions.forEach(opt => opt.classList.remove('selected'));
            this.classList.add('selected');
            radio.checked = true;
        });
    });
    
    function updateTotal() {
        const total = subtotal + biayaDesain + currentShippingCost;
        grandTotal.textContent = `Rp ${total.toLocaleString('id-ID')}`;
    }
    
    // Initialize
    updateTotal();
});
</script>
@endsection