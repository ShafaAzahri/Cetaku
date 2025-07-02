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

<!-- Modal Detail Pesanan -->
<div class="modal fade" id="orderDetailModal" tabindex="-1" aria-labelledby="orderDetailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="orderDetailModalLabel">Detail Pesanan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="order-detail-info">
                    <h6>Barang Pesanan:</h6>
                    <div id="orderItems"></div>
                    
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <h6>Total Harga:</h6>
                            <p id="orderTotalPrice" class="fw-bold text-primary fs-5">Rp 0</p>
                        </div>
                        <div class="col-md-6">
                            <h6>Total Item:</h6>
                            <p id="orderTotalItems">-</p>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <h6>Alamat Pengiriman:</h6>
                    <p id="orderShippingAddress" class="text-muted">-</p>
                    
                    <h6>Bukti Pengiriman:</h6>
                    <div id="orderShipmentProofContainer">
                        <img id="orderShipmentProof" src="" alt="Bukti Pengiriman" class="img-fluid" style="max-height: 300px; border-radius: 8px;">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('custom-js')
<script>
// Debug untuk memastikan Bootstrap tersedia
// Debug untuk memastikan Bootstrap tersedia
// Debug untuk memastikan Bootstrap tersedia
console.log('Bootstrap version:', typeof bootstrap !== 'undefined' ? 'Available' : 'Not Available');

function showOrderDetails(orderId) {
    console.log('showOrderDetails called for order:', orderId);
    
    // Show loading state
    document.getElementById('orderItems').innerHTML = '<div class="text-center"><i class="fas fa-spinner fa-spin"></i> Memuat...</div>';
    document.getElementById('orderTotalPrice').innerText = 'Memuat...';
    document.getElementById('orderTotalItems').innerText = 'Memuat...';
    document.getElementById('orderShippingAddress').innerText = 'Memuat...';
    
    // Hide shipping proof initially
    const proofImg = document.getElementById('orderShipmentProof');
    proofImg.style.display = 'none';
    
    // Fetch actual data from API
    fetch(/pesanan/show?id=${orderId}, {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(HTTP error! status: ${response.status});
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
        
        // Show error message
        document.getElementById('orderItems').innerHTML = 
            '<div class="alert alert-danger">Gagal memuat detail pesanan: ' + error.message + '</div>';
        document.getElementById('orderTotalPrice').innerText = '-';
        document.getElementById('orderTotalItems').innerText = '-';
        document.getElementById('orderShippingAddress').innerText = '-';
    });
}

function updateModalContent(orderData) {
    // Update modal title with order ID
    document.getElementById('orderDetailModalLabel').innerText = Detail Pesanan #${orderData.id};
    
    // Update items
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
            
            // Get product image
            const defaultImage = '/images/polines.png';
            let productImage = defaultImage;
            if (item.gambar) {
                productImage = /storage/${item.gambar};
            }
            
            // Tambahkan ke total items
            totalItems += quantity;
            
            itemsHtml += `
                <div class="mb-3 pb-3 border-bottom">
                    <div class="d-flex">
                        <img src="${productImage}" alt="${itemName}" class="me-3" style="width: 60px; height: 60px; object-fit: cover; border-radius: 5px;" onerror="this.src='${defaultImage}'">
                        <div class="flex-grow-1">
                            <h6 class="mb-1">${itemName}</h6>
                            <small class="text-muted d-block">Ukuran: ${size}</small>
                            <small class="text-muted d-block">Bahan: ${material}</small>
                            <small class="text-muted d-block">Kategori: ${category}</small>
                            <small class="text-muted d-block">Tipe Desain: ${tipeDesain}</small>
                            ${biayaJasa > 0 ? <small class="text-muted d-block">Biaya Jasa: Rp ${biayaJasa.toLocaleString('id-ID')}</small> : ''}
                            <small class="text-muted d-block">Jumlah: ${quantity}</small>
                            <div class="mt-2">
                                <span class="fw-bold text-primary">Rp ${itemPrice.toLocaleString('id-ID')}</span>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        });
    } else {
        itemsHtml = '<div class="text-muted">Tidak ada detail item</div>';
        totalItems = orderData.total_items || 1; // Fallback ke total_items dari API
    }

    document.getElementById('orderItems').innerHTML = itemsHtml;
    
    // Update total price - GUNAKAN TOTAL DARI TABEL PESANAN
    const totalHarga = parseFloat(orderData.total_harga) || parseFloat(orderData.total) || 0;
    document.getElementById('orderTotalPrice').innerText = Rp ${totalHarga.toLocaleString('id-ID')};
    
    // Update total items - GUNAKAN TOTAL ITEMS YANG SUDAH DIHITUNG
    const finalTotalItems = orderData.total_items || totalItems;
    document.getElementById('orderTotalItems').innerText = ${finalTotalItems} item;
    
    // Update shipping address
    const shippingAddress = orderData.alamat_pengiriman || 'Alamat pengiriman belum diisi';
    document.getElementById('orderShippingAddress').innerText = shippingAddress;
    
    // Update shipping proof
    const proofImg = document.getElementById('orderShipmentProof');
    if (orderData.bukti_pengiriman) {
        proofImg.src = /storage/${orderData.bukti_pengiriman};
        proofImg.style.display = 'block';
        proofImg.onerror = function() {
            this.style.display = 'none';
            this.parentNode.innerHTML += '<p class="text-muted">Bukti pengiriman tidak dapat ditampilkan</p>';
        };
    } else {
        proofImg.style.display = 'none';
        proofImg.parentNode.innerHTML = '<p class="text-muted">Belum ada bukti pengiriman</p>';
    }
    
    // Add order status and date info
    const orderInfo = document.querySelector('.order-detail-info');
    const existingInfo = orderInfo.querySelector('.order-status-info');
    if (existingInfo) {
        existingInfo.remove();
    }
    
    const statusInfo = document.createElement('div');
    statusInfo.className = 'order-status-info mb-3';
    statusInfo.innerHTML = `
        <div class="row">
            <div class="col-md-6">
                <h6>Status Pesanan:</h6>
                <span class="badge status-${orderData.status.toLowerCase().replace(/\s+/g, '-')} mb-2">${orderData.status}</span>
            </div>
            <div class="col-md-6">
                <h6>Tanggal Pesanan:</h6>
                <p class="text-muted">${formatDate(orderData.created_at)}</p>
            </div>
        </div>
        <hr>
    `;
    orderInfo.insertBefore(statusInfo, orderInfo.firstChild);
}

function formatDate(dateString) {
    if (!dateString) return '-';
    
    const date = new Date(dateString);
    const options = { 
        year: 'numeric', 
        month: 'long', 
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    };
    
    return date.toLocaleDateString('id-ID', options);
}

// Alternative function jika bootstrap tidak tersedia
function showModal() {
    const modal = document.getElementById('orderDetailModal');
    modal.style.display = 'block';
    modal.classList.add('show');
    document.body.style.overflow = 'hidden';
    
    // Add backdrop
    const backdrop = document.createElement('div');
    backdrop.className = 'modal-backdrop fade show';
    backdrop.id = 'modal-backdrop';
    document.body.appendChild(backdrop);
}

function hideModal() {
    const modal = document.getElementById('orderDetailModal');
    modal.style.display = 'none';
    modal.classList.remove('show');
    document.body.style.overflow = 'auto';
    
    // Remove backdrop
    const backdrop = document.getElementById('modal-backdrop');
    if (backdrop) {
        backdrop.remove();
    }
}

// Tambahkan event listener untuk close button
document.addEventListener('DOMContentLoaded', function() {
    const closeButtons = document.querySelectorAll('[data-bs-dismiss="modal"]');
    closeButtons.forEach(button => {
        button.addEventListener('click', hideModal);
    });
});

function payOrder(orderId) {
    window.location.href = /pesanan/${orderId}/payment;
}

function trackOrder(orderId) {
    alert('Fitur tracking akan segera tersedia');
}

function reviewOrder(detailId) {
    window.location.href = /ulasan/${detailId};
}

function reorderOrder(orderId) {
    if (confirm('Beli lagi produk ini?')) {
        alert('Produk berhasil ditambahkan ke keranjang');
    }
}

function contactAdmin(orderId) {
    const message = Halo, saya ingin menanyakan tentang pesanan #${orderId};
    const phone = '628123456789';
    window.open(https://wa.me/${phone}?text=${encodeURIComponent(message)}, '_blank');
}

function cancelOrder(id) {
    if (confirm('Yakin ingin membatalkan pesanan ini?')) {
        fetch(/pesanan/${id}/cancel, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                alert(data.message || 'Pesanan berhasil dibatalkan!');
                location.reload();
            } else {
                alert(data.message || 'Gagal membatalkan pesanan.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat membatalkan pesanan.');
        });
    }
}

</script>
@endsection