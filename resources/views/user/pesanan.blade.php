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
    .status-pending {
        background-color: #fff4de;
        color: #ffa426;
    }
    .status-belum-dibayar {
        background-color: #fff4de;
        color: #ffa426;
    }
    .status-pemesanan {
        background-color: #e0f4ff;
        color: #3498db;
    }
    .status-processing {
        background-color: #e0f4ff;
        color: #3498db;
    }
    .status-sedang-diproses {
        background-color: #e0f4ff;
        color: #3498db;
    }
    .status-shipping {
        background-color: #e7f9ed;
        color: #2ecc71;
    }
    .status-sedang-dikirim {
        background-color: #e7f9ed;
        color: #2ecc71;
    }
    .status-complete {
        background-color: #dcf7e8;
        color: #27ae60;
    }
    .status-selesai {
        background-color: #dcf7e8;
        color: #27ae60;
    }
    .status-cancelled {
        background-color: #ffe5e5;
        color: #e74c3c;
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
    .loading {
        text-align: center;
        padding: 40px 0;
    }
    .spinner-border {
        width: 3rem;
        height: 3rem;
    }
</style>
@endsection

@section('content')
<section class="order-page">
    <div class="container">
        <h2 class="section-title">Pesanan Saya</h2>
       
        @php
    $statuses = ['Semua', 'Belum Dibayar', 'Pemesanan', 'Dikonfirmasi', 'Sedang Diproses', 'Menunggu Pengambilan', 'Sedang Dikirim', 'Selesai', 'Dibatalkan'];
@endphp

<ul class="nav nav-tabs mb-4" id="orderTabs" role="tablist">
    @foreach ($statuses as $s)
        <li class="nav-item" role="presentation">
            <a
                class="nav-link {{ ($status === $s || ($s === 'Semua' && !$status)) ? 'active' : '' }}"
                href="{{ route('user.pesanan', ['status' => $s !== 'Semua' ? $s : null]) }}"
                role="tab"
            >
                {{ $s }}
            </a>
        </li>
    @endforeach
</ul>

       
        <!-- Content -->
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
                                    // Initialize variables with defaults
                                    $gambar = null;
                                    $namaProduk = 'Custom Product';
                                    $harga = $detail['custom']['harga'] ?? 0;
                                    $jumlah = $detail['jumlah'] ?? 1;
                                    $ukuran = null;
                                    $bahan = null;
                                    $jenis = null;
                                   
                                    // Extract data from custom relationship
                                    if (isset($detail['custom'])) {
                                        $custom = $detail['custom'];
                                        
                                        // Get item data
                                        if (isset($custom['item'])) {
                                            $namaProduk = $custom['item']['nama_item'] ?? $namaProduk;
                                            $gambar = $custom['item']['gambar'] ?? null;
                                        }
                                        
                                        // Get ukuran data
                                        if (isset($custom['ukuran'])) {
                                            $ukuran = $custom['ukuran']['size'] ?? null;
                                        }
                                        
                                        // Get bahan data
                                        if (isset($custom['bahan'])) {
                                            $bahan = $custom['bahan']['nama_bahan'] ?? null;
                                        }
                                        
                                        // Get jenis data
                                        if (isset($custom['jenis'])) {
                                            $jenis = $custom['jenis']['kategori'] ?? null;
                                        }
                                    }
                                @endphp
                               
                                <img src="{{ $gambar ? asset('storage/' . $gambar) : asset('images/products/default.jpg') }}"
                                     alt="{{ $namaProduk }}"
                                     class="product-image"
                                     onerror="this.src='{{ asset('images/products/default.jpg') }}'">
                                <div class="product-details">
                                    <h5 class="product-title">{{ $namaProduk }}</h5>
                                    @if($ukuran || $bahan || $jenis)
                                    <p class="product-variant">
                                        @if($ukuran)Ukuran: {{ $ukuran }}@endif
                                        @if($bahan)@if($ukuran), @endif Bahan: {{ $bahan }}@endif
                                        @if($jenis)@if($ukuran || $bahan), @endif Kategori: {{ $jenis }}@endif
                                    </p>
                                    @endif
                                    <p class="product-price">
                                        Rp {{ number_format($harga, 0, ',', '.') }}
                                        x {{ $jumlah }}
                                    </p>
                                </div>
                            </div>
                            @endforeach
                        @else
                            <!-- Fallback jika detail pesanan tidak tersedia -->
                            <div class="product-item">
                                <img src="{{ asset('images/products/default.jpg') }}"
                                     alt="Produk"
                                     class="product-image">
                                <div class="product-details">
                                    <h5 class="product-title">Custom Order</h5>
                                    <p class="product-price">
                                        @php
                                            // Calculate total from detail_pesanans if available
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
                            </div>
                        @endif
                    </div>
                   
                    <div class="order-footer">
                        <div class="total-section">
                            <p class="total-items">
                                Total {{ isset($pesanan['detail_pesanans']) ? count($pesanan['detail_pesanans']) : 1 }} item
                            </p>
                            <p class="total-price">
                                @php
                                    // Calculate total from detail_pesanans
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
                                @case('Belum Dibayar')
                                    <button class="btn action-btn btn-cancel"
                                            onclick="cancelOrder('{{ $pesanan['id'] }}')">Batalkan</button>
                                    <button class="btn action-btn btn-pay"
                                            onclick="payOrder('{{ $pesanan['id'] }}')">Bayar Sekarang</button>
                                    @break
                               
                                @case('Pemesanan')
                                    <button class="btn action-btn btn-cancel"
                                            onclick="cancelOrder('{{ $pesanan['id'] }}')">Batalkan</button>
                                    <button class="btn action-btn btn-help"
                                            onclick="contactAdmin('{{ $pesanan['id'] }}')">Hubungi Admin</button>
                                    @break
                               
                                @case('Sedang Diproses')
                                    <button class="btn action-btn btn-help"
                                            onclick="contactAdmin('{{ $pesanan['id'] }}')">Hubungi Admin</button>
                                    @break
                               
                                @case('Sedang Dikirim')
                                    <button class="btn action-btn btn-track"
                                            onclick="trackOrder('{{ $pesanan['id'] }}')">Lacak Pengiriman</button>
                                    @break
                               
                                @case('Selesai')
                                    @php
                                        $hasUnreviewedItems = false;
                                        if (isset($pesanan['detail_pesanans']) && is_array($pesanan['detail_pesanans'])) {
                                            foreach ($pesanan['detail_pesanans'] as $detail) {
                                                if (!isset($detail['reviewed_at']) || $detail['reviewed_at'] === null) {
                                                    $hasUnreviewedItems = true;
                                                    break;
                                                }
                                            }
                                        }
                                    @endphp
                                    @if($hasUnreviewedItems)
                                    <button class="btn action-btn btn-review"
                                            onclick="reviewOrder('{{ $pesanan['id'] }}')">Beri Ulasan</button>
                                    @endif
                                    <button class="btn action-btn btn-pay"
                                            onclick="reorderOrder('{{ $pesanan['id'] }}')">Beli Lagi</button>
                                    @break
                               
                                @case('Dibatalkan')
                                    <button class="btn action-btn btn-pay"
                                            onclick="reorderOrder('{{ $pesanan['id'] }}')">Beli Lagi</button>
                                    @break
                            @endswitch
                        </div>
                    </div>
                </div>
                @endforeach
            @else
                <!-- Empty State -->
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
                    <a href="#" class="btn btn-primary mt-3">
                        Mulai Berbelanja
                    </a>
                </div>
            @endif
        </div>
    </div>
</section>

<!-- Loading Modal -->
<div class="modal fade" id="loadingModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-2 mb-0">Memproses...</p>
            </div>
        </div>
    </div>
</div>
@endsection

@section('custom-js')
<script>
// Fungsi untuk membatalkan pesanan
function cancelOrder(orderId) {
    if (confirm('Apakah Anda yakin ingin membatalkan pesanan ini?')) {
        showLoading();
       
        fetch(`/pesanan/${orderId}/cancel`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            hideLoading();
            if (data.success) {
                alert('Pesanan berhasil dibatalkan');
                location.reload();
            } else {
                alert('Gagal membatalkan pesanan: ' + (data.message || 'Terjadi kesalahan'));
            }
        })
        .catch(error => {
            hideLoading();
            console.error('Error:', error);
            alert('Terjadi kesalahan saat membatalkan pesanan');
        });
    }
}

// Fungsi untuk membayar pesanan
function payOrder(orderId) {
    showLoading();
    window.location.href = `/pesanan/${orderId}/payment`;
}

// Fungsi untuk melacak pengiriman
function trackOrder(orderId) {
    showLoading();
    window.location.href = `/pesanan/${orderId}/track`;
}

// Fungsi untuk memberikan ulasan
function reviewOrder(orderId) {
    window.location.href = `/pesanan/${orderId}/review`;
}

// Fungsi untuk memesan lagi
function reorderOrder(orderId) {
    if (confirm('Apakah Anda ingin memesan produk yang sama lagi?')) {
        showLoading();
       
        fetch(`/pesanan/${orderId}/reorder`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            hideLoading();
            if (data.success) {
                alert('Produk berhasil ditambahkan ke keranjang');
                window.location.href = '/cart';
            } else {
                alert('Gagal menambahkan ke keranjang: ' + (data.message || 'Terjadi kesalahan'));
            }
        })
        .catch(error => {
            hideLoading();
            console.error('Error:', error);
            alert('Terjadi kesalahan saat menambahkan ke keranjang');
        });
    }
}

// Fungsi untuk menghubungi admin
function contactAdmin(orderId) {
    const message = `Halo, saya ingin menanyakan tentang pesanan #${orderId}`;
    const phoneNumber = '628123456789'; // Ganti dengan nomor WhatsApp admin
    const whatsappUrl = `https://wa.me/${phoneNumber}?text=${encodeURIComponent(message)}`;
    window.open(whatsappUrl, '_blank');
}

// Fungsi untuk menampilkan loading
function showLoading() {
    const modal = new bootstrap.Modal(document.getElementById('loadingModal'));
    modal.show();
}

// Fungsi untuk menyembunyikan loading
function hideLoading() {
    const modal = bootstrap.Modal.getInstance(document.getElementById('loadingModal'));
    if (modal) {
        modal.hide();
    }
}

// Auto refresh setiap 30 detik untuk update status
setInterval(function() {
    // Hanya refresh jika ada pesanan yang sedang diproses atau dikirim
    const processingOrders = document.querySelectorAll('.status-sedang-diproses, .status-sedang-dikirim');
    if (processingOrders.length > 0) {
        location.reload();
    }
}, 30000);
</script>
@endsection