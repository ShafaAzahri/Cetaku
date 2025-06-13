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
        justify-content: space-between;
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

                    @foreach($addresses as $i => $address)
                        <div class="address-card{{ $i === 0 ? ' selected' : '' }}">
                            <input type="radio" name="selected_address" value="{{ $address['id'] }}" {{ $i === 0 ? 'checked' : '' }} style="display: none;">
                            <div class="address-label">{{ strtoupper($address['label']) }}</div>
                            <div class="address-name">{{ $user_name }}</div>
                            <div class="address-detail">
                                {{ $address['alamat_lengkap'] }}<br>
                                {{ $address['kelurahan'] }}, {{ $address['kecamatan'] }}, {{ $address['kota'] }}, {{ $address['provinsi'] }} {{ $address['kode_pos'] }}<br>
                                +{{ $address['nomor_hp'] }}
                            </div>
                        </div>
                    @endforeach

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
                    @if(isset($expeditions) && count($expeditions) > 0)
                        @php $first = true; @endphp
                        @foreach($expeditions as $layanan)
                            <div class="ekspedisi-option {{ $first ? 'selected' : '' }}" data-cost="{{ $layanan['cost'] ?? 0 }}">
                                <input type="radio"
                                    name="selected_ekspedisi"
                                    value="{{ $layanan['code'] . '-' . ($layanan['service'] ?? '') }}"
                                    {{ $first ? 'checked' : '' }}
                                    style="display: none;">
                                <div class="ekspedisi-info">
                                    <div class="ekspedisi-name">{{ $layanan['name'] ?? '-' }}</div>
                                    <div class="ekspedisi-service">{{ $layanan['description'] ?? '-' }}</div>
                                </div>
                                <div class="ekspedisi-price">
                                    <div class="ekspedisi-cost">
                                        Rp {{ number_format($layanan['cost'] ?? 0, 0, ',', '.') }}
                                    </div>
                                    <div class="ekspedisi-estimate">
                                        {{ $layanan['etd'] ?? '-' }}
                                    </div>
                                </div>
                            </div>
                            @php $first = false; @endphp
                        @endforeach
                    @else
                        <div class="ekspedisi-option" data-cost="0">
                            <div class="ekspedisi-info">
                                <div class="ekspedisi-name">Tidak ada ekspedisi ditemukan.</div>
                            </div>
                        </div>
                    @endif
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

                    @if(isset($produkTerpilih) && count($produkTerpilih) > 0)
                        @php
                            // Hitung subtotal
                            $subtotal = 0;
                            foreach ($produkTerpilih as $produk) {
                                $subtotal += ($produk['harga_satuan'] ?? 0) * ($produk['quantity'] ?? 1);
                            }
                            
                            // Ambil biaya desain
                            $biayaDesainFinal = $biaya_desain ?? 0;
                            
                            // Ongkir default (akan diupdate via JavaScript)
                            $defaultOngkir = 0;
                            if(isset($expeditions) && count($expeditions) > 0) {
                                $defaultOngkir = $expeditions[0]['cost'] ?? 0;
                            }
                        @endphp

                        {{-- Loop untuk menampilkan setiap produk --}}
                        @foreach($produkTerpilih as $produk)
                            @php
                                $hargaPerItem = ($produk['harga_satuan'] ?? 0) * ($produk['jumlah'] ?? 1);
                            @endphp

                            <div class="order-item">
                                <img src="{{ isset($produk['item']['gambar']) ? asset('storage/' . $produk['item']['gambar']) : asset('images/products/default.png') }}" 
                                    class="order-item-image" 
                                    alt="{{ $produk['item']['nama_item'] ?? 'Produk' }}">

                                <div class="order-item-details">
                                    <div class="order-item-name">{{ $produk['item']['nama_item'] ?? '-' }}</div>
                                    <div class="order-item-specs">
                                        {{ $produk['bahan']['nama_bahan'] ?? '-' }}, Ukuran: {{ $produk['ukuran']['size'] ?? '-' }}<br>
                                        Jenis: {{ $produk['jenis']['kategori'] ?? '-' }}
                                        
                                        {{-- Tampilkan tipe desain --}}
                                        @if(($produk['tipe_desain'] ?? 'sendiri') === 'dibuatkan')
                                            <br><span class="badge badge-info">Desain Toko</span>
                                        @else
                                            <br><span class="badge badge-secondary">Desain Sendiri</span>
                                        @endif
                                    </div>
                                </div>

                                <div class="order-item-price">
                                    <div class="order-item-quantity">{{ $produk['jumlah'] ?? 1 }}x</div>
                                    <div class="order-item-total">Rp {{ number_format($hargaPerItem, 0, ',', '.') }}</div>
                                </div>
                            </div>
                        @endforeach

                        <hr>
                        
                        {{-- Ringkasan Pesanan --}}
                        <div class="order-summary">
                            <div class="summary-row">
                                <span>Subtotal ({{ count($produkTerpilih) }} item)</span>
                                <span id="subtotal-display">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                            </div>
                            
                            @if($biayaDesainFinal > 0)
                            <div class="summary-row">
                                <span>Biaya Desain</span>
                                <span id="design-cost-display">Rp {{ number_format($biayaDesainFinal, 0, ',', '.') }}</span>
                            </div>
                            @endif
                            
                            <div class="summary-row" id="shipping-cost-row">
                                <span>Ongkos Kirim</span>
                                <span id="shipping-cost-display">Rp {{ number_format($defaultOngkir, 0, ',', '.') }}</span>
                            </div>
                            
                            <div class="summary-row total">
                                <span><strong>Total</strong></span>
                                <span id="total-display"><strong>Rp {{ number_format($subtotal + $biayaDesainFinal + $defaultOngkir, 0, ',', '.') }}</strong></span>
                            </div>
                        </div>
                    @else
                        <p>Tidak ada produk yang dipilih.</p>
                    @endif

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
    // Data dari PHP
    const subtotal = {{ $subtotal }};
    const biayaDesain = {{ $biayaDesainFinal }};
    
    // DOM Elements
    const deliveryOptions = document.querySelectorAll('.delivery-option');
    const addressSection = document.getElementById('address-section');
    const shippingSection = document.getElementById('shipping-section');
    const storeSection = document.getElementById('store-section');
    const shippingCostRow = document.getElementById('shipping-cost-row');
    const shippingCostDisplay = document.getElementById('shipping-cost-display');
    const totalDisplay = document.getElementById('total-display');
    
    const addressCards = document.querySelectorAll('.address-card:not(.btn-add-address)');
    const ekspedisiOptions = document.querySelectorAll('.ekspedisi-option');
    const paymentOptions = document.querySelectorAll('.payment-option');
    
    let currentShippingCost = {{ $defaultOngkir }};
    let currentDeliveryMethod = 'antar';
    
    // Fungsi untuk memformat rupiah
    function formatRupiah(amount) {
        return 'Rp ' + amount.toLocaleString('id-ID');
    }
    
    // Fungsi untuk update total
    function updateTotal() {
        let finalShippingCost = currentDeliveryMethod === 'ambil' ? 0 : currentShippingCost;
        let total = subtotal + biayaDesain + finalShippingCost;
        
        // Update display
        shippingCostDisplay.textContent = formatRupiah(finalShippingCost);
        totalDisplay.innerHTML = '<strong>' + formatRupiah(total) + '</strong>';
        
        // Show/hide shipping cost row
        if (currentDeliveryMethod === 'ambil') {
            shippingCostRow.style.display = 'none';
        } else {
            shippingCostRow.style.display = 'flex';
        }
    }
    
    // Delivery method switching
    deliveryOptions.forEach(option => {
        option.addEventListener('click', function() {
            const method = this.dataset.method;
            const radio = this.querySelector('input[type="radio"]');
            
            // Update UI
            deliveryOptions.forEach(opt => opt.classList.remove('active'));
            this.classList.add('active');
            radio.checked = true;
            
            // Update current method
            currentDeliveryMethod = method;
            
            if (method === 'antar') {
                // Pesan Antar
                addressSection.style.display = 'block';
                shippingSection.style.display = 'block';
                storeSection.style.display = 'none';
                
                // Set shipping cost to selected expedition or default
                const selectedEkspedisi = document.querySelector('.ekspedisi-option.selected');
                if (selectedEkspedisi) {
                    currentShippingCost = parseInt(selectedEkspedisi.dataset.cost) || 0;
                } else {
                    // Set to first expedition cost
                    const firstEkspedisi = document.querySelector('.ekspedisi-option');
                    if (firstEkspedisi) {
                        currentShippingCost = parseInt(firstEkspedisi.dataset.cost) || 0;
                        firstEkspedisi.classList.add('selected');
                        firstEkspedisi.querySelector('input[type="radio"]').checked = true;
                    }
                }
            } else {
                // Ambil Sendiri
                addressSection.style.display = 'none';
                shippingSection.style.display = 'none';
                storeSection.style.display = 'block';
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
            const cost = parseInt(this.dataset.cost) || 0;
            
            ekspedisiOptions.forEach(opt => opt.classList.remove('selected'));
            this.classList.add('selected');
            radio.checked = true;
            
            // Update shipping cost only if delivery method is 'antar'
            if (currentDeliveryMethod === 'antar') {
                currentShippingCost = cost;
                updateTotal();
            }
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
    
    // Initialize
    updateTotal();
});
</script>
@endsection