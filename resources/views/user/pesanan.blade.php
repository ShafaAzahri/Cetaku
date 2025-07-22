@extends('user.layouts.app')
@section('custom-css')
<style>
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
    .order-date {
        color: #6c757d;
        font-size: 12px;
        margin-top: 2px;
    }
    .status-badge {
        padding: 5px 10px;
        border-radius: 50px;
        font-size: 12px;
        font-weight: 500;
        text-transform: uppercase;
    }
    .status-belum-dibayar {
        background-color: #fff4de;
        color: #ffa426;
    }
    .status-pemesanan {
        background-color: #e0f4ff;
        color: #3498db;
    }
    .status-sedang-diproses {
        background-color: #e0f4ff;
        color: #3498db;
    }
    .status-sedang-dikirim {
        background-color: #e7f9ed;
        color: #2ecc71;
    }
    .status-selesai {
        background-color: #dcf7e8;
        color: #27ae60;
    }
    .status-dibatalkan {
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
    
    /* Modal Detail Styles - Tokopedia inspired */
    .modal-lg {
        max-width: 800px;
    }
    .modal-header {
        border-bottom: 2px solid #f0f0f0;
        padding: 20px;
    }
    .modal-title {
        font-size: 18px;
        font-weight: 600;
        color: #333;
    }
    .modal-body {
        padding: 0;
    }
    .order-status-section {
        background: linear-gradient(135deg, #4361ee 0%, #3c54d8 100%);
        color: white;
        padding: 20px;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .order-status-info h6 {
        margin: 0;
        font-size: 14px;
        opacity: 0.9;
    }
    .order-status-info .status-text {
        font-size: 16px;
        font-weight: 600;
        margin-top: 5px;
    }
    .order-date-info {
        text-align: right;
        opacity: 0.9;
    }
    .order-date-info small {
        font-size: 12px;
    }
    .section-divider {
        background-color: #f8f9fa;
        height: 8px;
        border-top: 1px solid #e9ecef;
        border-bottom: 1px solid #e9ecef;
    }
    .detail-section {
        padding: 20px;
    }
    .detail-section h6 {
        font-size: 16px;
        font-weight: 600;
        color: #333;
        margin-bottom: 15px;
        display: flex;
        align-items: center;
    }
    .detail-section h6 i {
        margin-right: 8px;
        color: #4361ee;
    }
    .product-detail-item {
        display: flex;
        padding: 15px 0;
        border-bottom: 1px solid #f0f0f0;
    }
    .product-detail-item:last-child {
        border-bottom: none;
    }
    .product-detail-image {
        width: 70px;
        height: 70px;
        border-radius: 8px;
        object-fit: cover;
        margin-right: 15px;
        border: 1px solid #e9ecef;
    }
    .product-detail-content {
        flex: 1;
    }
    .product-detail-title {
        font-weight: 600;
        color: #333;
        margin-bottom: 8px;
        font-size: 15px;
    }
    .product-detail-specs {
        display: flex;
        flex-wrap: wrap;
        gap: 15px;
        margin-bottom: 8px;
    }
    .product-spec {
        color: #6c757d;
        font-size: 13px;
    }
    .product-detail-price {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 10px;
    }
    .price-info {
        font-weight: 600;
        color: #333;
    }
    .quantity-info {
        color: #6c757d;
        font-size: 14px;
    }
    .shipping-info {
        background-color: #f8f9fa;
        border-radius: 8px;
        padding: 15px;
        margin-bottom: 15px;
    }
    .shipping-info h6 {
        font-size: 14px;
        font-weight: 600;
        color: #333;
        margin-bottom: 10px;
    }
    .shipping-address {
        color: #6c757d;
        font-size: 14px;
        line-height: 1.5;
    }
    .tracking-section {
        background-color: #f8f9fa;
        border-radius: 8px;
        padding: 15px;
        margin-bottom: 15px;
    }
    .tracking-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 10px;
    }
    .tracking-number {
        font-family: monospace;
        font-size: 16px;
        font-weight: 600;
        color: #333;
        background-color: white;
        padding: 8px 12px;
        border-radius: 4px;
        border: 1px solid #dee2e6;
    }
    .copy-btn {
        background-color: #4361ee;
        color: white;
        border: none;
        padding: 6px 12px;
        border-radius: 4px;
        font-size: 12px;
        cursor: pointer;
        transition: background-color 0.2s;
    }
    .copy-btn:hover {
        background-color: #3c54d8;
    }
    .copy-btn:active {
        transform: scale(0.98);
    }
    .proof-section {
        text-align: center;
        margin-top: 15px;
    }
    .proof-image {
        max-width: 100%;
        max-height: 300px;
        border-radius: 8px;
        border: 1px solid #dee2e6;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    .no-proof {
        color: #6c757d;
        font-style: italic;
        padding: 20px;
        text-align: center;
    }
    .payment-summary {
        background-color: #f8f9fa;
        border-radius: 8px;
        padding: 20px;
        margin-top: 20px;
    }
    .payment-summary h6 {
        font-size: 16px;
        font-weight: 600;
        color: #333;
        margin-bottom: 15px;
    }
    .payment-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: 10px;
        font-size: 14px;
    }
    .payment-row.total {
        font-weight: 600;
        font-size: 16px;
        color: #4361ee;
        border-top: 1px solid #dee2e6;
        padding-top: 10px;
        margin-top: 10px;
    }
    .modal-footer {
        border-top: 2px solid #f0f0f0;
        padding: 20px;
        justify-content: center;
    }
    .toast-notification {
        position: fixed;
        top: 20px;
        right: 20px;
        background-color: #4361ee;
        color: white;
        padding: 12px 20px;
        border-radius: 6px;
        font-size: 14px;
        z-index: 9999;
        opacity: 0;
        transform: translateX(100%);
        transition: all 0.3s ease;
    }
    .toast-notification.show {
        opacity: 1;
        transform: translateX(0);
    }
</style>
@endsection

@section('content')
<section class="order-page">
    <div class="container">
        <h2 class="section-title">Pesanan Saya</h2>
       
        @php
            $statuses = ['Semua', 'Pemesanan', 'Dikonfirmasi', 'Sedang Diproses', 'Menunggu Pengambilan', 'Sedang Dikirim', 'Selesai', 'Dibatalkan'];
        @endphp

        <ul class="nav nav-tabs mb-4" id="orderTabs" role="tablist">
            @foreach ($statuses as $s)
                <li class="nav-item" role="presentation">
                    <a class="nav-link {{ ($status === $s || ($s === 'Semua' && !$status)) ? 'active' : '' }}" href="{{ route('user.pesanan', ['status' => $s !== 'Semua' ? $s : null]) }}" role="tab">
                        {{ $s }}
                    </a>
                </li>
            @endforeach
        </ul>

        <div class="tab-content">
            @if(isset($pesanans) && count($pesanans) > 0)
                @foreach($pesanans as $pesanan)
                <div class="order-card">
                    <div class="order-header">
                        <div>
                            <span class="order-id">Pesanan #{{ $pesanan['id'] }}</span>
                            @if(isset($pesanan['created_at']))
                            <div class="order-date">{{ \Carbon\Carbon::parse($pesanan['created_at'])->format('d M Y, H:i') }}</div>
                            @endif
                        </div>
                        <span class="status-badge status-{{ strtolower(str_replace(' ', '-', $pesanan['status'])) }}">
                            {{ $pesanan['status'] }}
                        </span>
                    </div>
                   
                    <div class="order-body">
                        @if(isset($pesanan['detail_pesanans']) && is_array($pesanan['detail_pesanans']) && count($pesanan['detail_pesanans']) > 0)
                            @foreach($pesanan['detail_pesanans'] as $detail)
                            <div class="product-item">
                                @php
                                    $gambar = $detail['custom']['item']['gambar'] ?? null;
                                    $namaProduk = $detail['custom']['item']['nama_item'] ?? 'Custom Product';
                                    $harga = $detail['custom']['harga'] ?? 0;
                                    $jumlah = $detail['jumlah'] ?? 1;
                                    $ukuran = $detail['custom']['ukuran']['size'] ?? null;
                                    $bahan = $detail['custom']['bahan']['nama_bahan'] ?? null;
                                    $jenis = $detail['custom']['jenis']['kategori'] ?? null;
                                @endphp
                               
                                <img src="{{ $gambar ? asset('storage/' . $gambar) : asset('images/products/default.jpg') }}" alt="{{ $namaProduk }}" class="product-image" onerror="this.src='{{ asset('images/polines.png') }}'">
                                <div class="product-details">
                                    <h5 class="product-title">{{ $namaProduk }}</h5>
                                    @if($ukuran || $bahan || $jenis)
                                    <p class="product-variant">
                                        @if($ukuran)Ukuran: {{ $ukuran }}@endif
                                        @if($bahan)@if($ukuran), @endif Bahan: {{ $bahan }}@endif
                                        @if($jenis)@if($ukuran || $bahan), @endif Kategori: {{ $jenis }}@endif
                                    </p>
                                    @endif
                                    <p class="product-price">Rp {{ number_format($harga, 0, ',', '.') }} x {{ $jumlah }}</p>
                                </div>
                            </div>
                            @endforeach
                        @else
                            <div class="product-item">
                                <img src="{{ asset('images/products/default.jpg') }}" alt="Produk" class="product-image">
                                <div class="product-details">
                                    <h5 class="product-title">Custom Order</h5>
                                    <p class="product-price">Rp {{ number_format($pesanan['total_harga'] ?? 0, 0, ',', '.') }}</p>
                                </div>
                            </div>
                        @endif
                    </div>
                   
                    <div class="order-footer">
                        <div class="total-section">
                            <p class="total-items">Total {{ isset($pesanan['detail_pesanans']) ? count($pesanan['detail_pesanans']) : 1 }} item</p>
                            <p class="total-price">
                                @php
                                    $totalHarga = 0;
                                    if (isset($pesanan['detail_pesanans']) && is_array($pesanan['detail_pesanans'])) {
                                        foreach ($pesanan['detail_pesanans'] as $detail) {
                                            $totalHarga += ($detail['total_harga'] ?? 0);
                                        }
                                    }
                                @endphp
                                Rp {{ number_format($totalHarga, 0, ',', '.') }}
                            </p>
                        </div>
                       
                        <div class="action-buttons">
                            @switch($pesanan['status'])
                                @case('Pemesanan')
                                    <button class="btn action-btn btn-cancel" onclick="cancelOrder('{{ $pesanan['id'] }}')">Batalkan</button>
                                    <button class="btn action-btn btn-help" onclick="contactAdmin('{{ $pesanan['id'] }}')">Hubungi Admin</button>
                                    @break
                                @case('Sedang Diproses')
                                    <button class="btn action-btn btn-help" onclick="contactAdmin('{{ $pesanan['id'] }}')">Hubungi Admin</button>
                                    @break
                                @case('Sedang Dikirim')
                                    <button class="btn action-btn btn-track" onclick="trackOrder('{{ $pesanan['id'] }}')">Lacak Pengiriman</button>
                                    @break
                                @case('Selesai')
                                    <button class="btn action-btn btn-review" onclick="reviewOrder('{{ $detail['id'] }}')">Beri Ulasan</button>
                                    <a href="{{ route('produk-all') }}" class="btn action-btn btn-pay">Beli Lagi<i class="fas fa-arrow-right ms-2"></i></a>
                                    @break
                                @case('Dibatalkan')
                                    <a href="{{ route('produk-all') }}" class="btn action-btn btn-pay">Beli Lagi<i class="fas fa-arrow-right ms-2"></i></a>
                                    @break
                            @endswitch
                            <button class="btn action-btn btn-info" data-bs-toggle="modal" data-bs-target="#orderDetailModal" onclick="showOrderDetails('{{ $pesanan['id'] }}')">Detail</button>
                        </div>
                    </div>
                </div>
                @endforeach
            @else
                <div class="empty-order">
                    <div class="empty-icon">
                        <i class="fas fa-shopping-bag"></i>
                    </div>
                    <h4>Belum Ada Pesanan</h4>
                    <p class="text-muted">
                        @if($status && $status !== 'Semua')
                            Tidak ada pesanan dengan status "{{ $status }}"
                        @else
                            Anda belum memiliki pesanan apapun
                        @endif
                    </p>
                    <a href="#" class="btn btn-primary mt-3">Mulai Berbelanja</a>
                </div>
            @endif
        </div>
    </div>
</section>

<!-- Modal Detail Pesanan - Tokopedia Style -->
<div class="modal fade" id="orderDetailModal" tabindex="-1" aria-labelledby="orderDetailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="orderDetailModalLabel">Detail Pesanan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Status Section -->
                <div class="order-status-section">
                    <div class="order-status-info">
                        <h6>Status Pesanan</h6>
                        <div class="status-text" id="orderStatusText">-</div>
                    </div>
                    <div class="order-date-info">
                        <div id="orderDateText">-</div>
                        <small>Tanggal Pesanan</small>
                    </div>
                </div>

                <!-- Products Section -->
                <div class="detail-section">
                    <h6><i class="fas fa-shopping-bag"></i> Produk Pesanan</h6>
                    <div id="orderProductItems">
                        <div class="text-center py-3">
                            <i class="fas fa-spinner fa-spin"></i> Memuat...
                        </div>
                    </div>
                </div>

                <div class="section-divider"></div>

                <!-- Shipping Info -->
                <div class="detail-section">
                    <h6><i class="fas fa-truck"></i> Informasi Pengiriman</h6>
                    <div class="shipping-info">
                        <h6>Alamat Pengiriman</h6>
                        <div class="shipping-address" id="orderShippingAddress">-</div>
                    </div>
                    
                    <div class="tracking-section" id="trackingSection" style="display: none;">
                        <div class="tracking-header">
                            <h6 style="margin: 0;">Nomor Resi</h6>
                            <button class="copy-btn" onclick="copyToClipboard()">Salin</button>
                        </div>
                        <div class="tracking-number" id="orderTrackingNumber" 
                            style="user-select: all; cursor: pointer;" 
                            onclick="selectText(this)" 
                            title="Klik untuk memilih teks">-</div>
                    </div>

                    <div class="proof-section" id="proofSection">
                        <h6>Bukti Pengiriman</h6>
                        <div id="orderProofContainer">
                            <div class="no-proof">Belum ada bukti pengiriman</div>
                        </div>
                    </div>
                </div>

                <div class="section-divider"></div>

                <!-- Payment Summary -->
                <div class="detail-section">
                    <h6><i class="fas fa-receipt"></i> Ringkasan Pembayaran</h6>
                    <div class="payment-summary">
                        <div class="payment-row">
                            <span>Total Item:</span>
                            <span id="orderTotalItems">-</span>
                        </div>
                        <div class="payment-row total">
                            <span>Total Pembayaran:</span>
                            <span id="orderTotalPrice">Rp 0</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- Toast Notification -->
<div id="toastNotification" class="toast-notification">
    Nomor resi berhasil disalin!
</div>
@endsection

@section('custom-js')
<script>
console.log('Bootstrap version:', typeof bootstrap !== 'undefined' ? 'Available' : 'Not Available');

function showOrderDetails(orderId) {
    console.log('showOrderDetails called for order:', orderId);
    
    // Reset modal content
    resetModalContent();
    
    // Fetch data from API
    fetch(`/pesanan/show?id=${orderId}`, {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        console.log('API Response:', data);
        
        if (data.status === 'success') {
            const orderData = data.data;
            updateModalContent(orderData);
        } else {
            throw new Error(data.message || 'Gagal mengambil detail pesanan');
        }
    })
    .catch(error => {
        console.error('Error fetching order details:', error);
        showErrorState(error.message);
    });
}

function resetModalContent() {
    document.getElementById('orderDetailModalLabel').innerText = 'Detail Pesanan';
    document.getElementById('orderStatusText').innerText = '-';
    document.getElementById('orderDateText').innerText = '-';
    document.getElementById('orderProductItems').innerHTML = '<div class="text-center py-3"><i class="fas fa-spinner fa-spin"></i> Memuat...</div>';
    document.getElementById('orderShippingAddress').innerText = '-';
    document.getElementById('orderTrackingNumber').innerText = '-';
    document.getElementById('orderTotalItems').innerText = '-';
    document.getElementById('orderTotalPrice').innerText = 'Rp 0';
    document.getElementById('trackingSection').style.display = 'none';
    document.getElementById('orderProofContainer').innerHTML = '<div class="no-proof">Belum ada bukti pengiriman</div>';
}

function updateModalContent(orderData) {
    // Update modal title
    document.getElementById('orderDetailModalLabel').innerText = `Detail Pesanan #${orderData.id}`;
    
    // Update status
    document.getElementById('orderStatusText').innerText = orderData.status;
    
    // Update date
    document.getElementById('orderDateText').innerText = formatDate(orderData.created_at);
    
    // Update products
    updateProductItems(orderData);
    
    // Update shipping address
    document.getElementById('orderShippingAddress').innerText = orderData.alamat_pengiriman || 'Alamat pengiriman belum diisi';
    
    // Update tracking number
    const trackingNumber = orderData.resi_pesanan;
    if (trackingNumber && trackingNumber !== '-') {
        document.getElementById('orderTrackingNumber').innerText = trackingNumber;
        document.getElementById('trackingSection').style.display = 'block';
    } else {
        document.getElementById('trackingSection').style.display = 'none';
    }
    
    // Update shipping proof
    updateShippingProof(orderData.bukti_pengiriman);
    
    // Update payment summary
    updatePaymentSummary(orderData);
}

function updateProductItems(orderData) {
    let itemsHtml = '';
    let totalItems = 0;
    
    if (orderData.detail_pesanans && orderData.detail_pesanans.length > 0) {
        orderData.detail_pesanans.forEach(detail => {
            const custom = detail.custom || {};
            const item = custom.item || {};
            const bahan = custom.bahan || {};
            const jenis = custom.jenis || {};
            const ukuran = custom.ukuran || {};
            
            const itemName = item.nama_item || 'Custom Product';
            const itemPrice = parseFloat(detail.total_harga) || 0;
            const quantity = detail.jumlah || 1;
            const size = ukuran.size || '-';
            const material = bahan.nama_bahan || '-';
            const category = jenis.kategori || '-';
            const tipeDesain = detail.tipe_desain || '-';
            const biayaJasa = parseFloat(detail.biaya_jasa) || 0;
            
            const defaultImage = '/images/polines.png';
            let productImage = defaultImage;
            if (item.gambar) {
                productImage = `/storage/${item.gambar}`;
            }
            
            totalItems += quantity;
            
            itemsHtml += `
                <div class="product-detail-item">
                    <img src="${productImage}" alt="${itemName}" class="product-detail-image" onerror="this.src='${defaultImage}'">
                    <div class="product-detail-content">
                        <div class="product-detail-title">${itemName}</div>
                        <div class="product-detail-specs">
                            <div class="product-spec">Ukuran: ${size}</div>
                            <div class="product-spec">Bahan: ${material}</div>
                            <div class="product-spec">Kategori: ${category}</div>
                        </div>
                                                ${tipeDesain !== '-' ? `<div class="product-spec">Tipe Desain: ${tipeDesain}</div>` : ''}
                        <div class="product-detail-price">
                            <span class="price-info">Rp ${itemPrice.toLocaleString('id-ID')}</span>
                            <span class="quantity-info">x${quantity}</span>
                        </div>
                    </div>
                </div>
            `;
        });
    } else {
        itemsHtml = `
            <div class="text-center py-4 text-muted">
                <i class="fas fa-box-open fa-2x mb-2"></i><br>
                Tidak ada detail produk ditemukan.
            </div>
        `;
    }

    document.getElementById('orderProductItems').innerHTML = itemsHtml;
    document.getElementById('orderTotalItems').innerText = `${totalItems} item`;
}

function updateShippingProof(buktiUrl) {
    const container = document.getElementById('orderProofContainer');
    if (buktiUrl && buktiUrl !== '-') {
        container.innerHTML = `
            <img src="/storage/${buktiUrl}" class="proof-image" alt="Bukti Pengiriman" onerror="this.src='/images/polines.png'">
        `;
    } else {
        container.innerHTML = '<div class="no-proof">Belum ada bukti pengiriman</div>';
    }
}

function updatePaymentSummary(orderData) {
    const total = parseFloat(orderData.total_harga) || 0;
    document.getElementById('orderTotalPrice').innerText = `Rp ${total.toLocaleString('id-ID')}`;
}

function formatDate(dateStr) {
    const date = new Date(dateStr);
    const options = { day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' };
    return date.toLocaleDateString('id-ID', options);
}

function copyTrackingNumber() {
    const trackingText = document.getElementById('orderTrackingNumber').innerText;
    navigator.clipboard.writeText(trackingText)
        .then(() => {
            showToast('Nomor resi berhasil disalin!');
        })
        .catch(() => {
            showToast('Gagal menyalin nomor resi.');
        });
}

function showToast(message) {
    const toast = document.getElementById('toastNotification');
    toast.innerText = message;
    toast.classList.add('show');
    setTimeout(() => {
        toast.classList.remove('show');
    }, 2500);
}

function copyToClipboard() {
    const text = document.getElementById('orderTrackingNumber').innerText;
    
    if (navigator.clipboard) {
        navigator.clipboard.writeText(text).then(() => {
            showToast('Nomor resi berhasil disalin!');
        }).catch(() => {
            fallbackCopy(text);
        });
    } else {
        fallbackCopy(text);
    }
}

function fallbackCopy(text) {
    const textArea = document.createElement('textarea');
    textArea.value = text;
    textArea.style.position = 'fixed';
    textArea.style.left = '-999999px';
    textArea.style.top = '-999999px';
    document.body.appendChild(textArea);
    textArea.focus();
    textArea.select();
    
    try {
        document.execCommand('copy');
        showToast('Nomor resi berhasil disalin!');
    } catch (err) {
        showToast('Gagal menyalin. Silakan pilih teks dan tekan Ctrl+C');
    }
    
    document.body.removeChild(textArea);
}

function selectText(element) {
    const range = document.createRange();
    range.selectNodeContents(element);
    const selection = window.getSelection();
    selection.removeAllRanges();
    selection.addRange(range);
    showToast('Teks telah dipilih, tekan Ctrl+C untuk menyalin');
}
</script>
@endsection
