@extends('user.layouts.app')

@section('title', 'Keranjang')

@push('meta')
<meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('custom-css')
<style>
    .cart-container {
        padding: 20px 0;
    }
    .cart-item, .summary-card {
        border: 1px solid #e5e5e5;
        border-radius: 8px;
        padding: 15px;
        margin-bottom: 15px;
        background-color: #fff;
    }
    .item-img {
        width: 100px;
        height: 100px;
        object-fit: cover;
        border-radius: 5px;
    }
    .item-details h5 {
        font-size: 16px;
        margin-bottom: 5px;
        font-weight: 600;
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
        margin-top: 10px;
    }
    .quantity-control button {
        border: none;
        background: #f5f5f5;
        width: 30px;
        cursor: pointer;
    }
    .quantity-control input {
        width: 40px;
        text-align: center;
        border: none;
    }
    .upload-status {
        font-size: 13px;
        padding: 3px 8px;
        border-radius: 50px;
        display: inline-block;
    }
    .status-uploaded {
        background-color: #d4edda;
        color: #155724;
    }
    .status-pending {
        background-color: #fff3cd;
        color: #856404;
    }
    .summary-card {
        border: 1px solid #e5e5e5;
        border-radius: 8px;
        padding: 20px;
    }
    .summary-item {
        display: flex;
        justify-content: space-between;
    }
    .btn {
        border: none;
        border-radius: 6px;
        padding: 10px 15px;
        color: white;
        font-weight: 600;
    }
    .btn-primary {
        background-color: #4361ee;
        text-align: center;
        width: 100%;
    }
    .btn-danger {
        background-color: #dc3545;
        color: white;
        text-align: center;
        width: 100%;
    }
    .btn:hover {
        opacity: 0.8;
    }
</style>
@endsection

@section('content')
<div class="bg-light py-3 fw-medium">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('welcome') }}" class="text-decoration-none text-secondary">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">Keranjang</li>
            </ol>
        </nav>
    </div>
</div>

<div class="container cart-container">
    @include('user.components.alert')
    
    @if(isset($keranjangItems) && count($keranjangItems) > 0)
        <h4 class="mb-0">Keranjang Anda ({{ $summary['total_items'] ?? 0 }} item)</h4>
        <div class="row">
            <div class="col-lg-8">
                <div class="cart-header mb-3">
                    <div class="form-check select-all">
                        <input class="form-check-input" type="checkbox" id="selectAll">
                        <label class="form-check-label" for="selectAll">Pilih Semua</label>
                    </div>
                </div>

                @foreach($keranjangItems as $item)
                    <div class="cart-item">
                        <div class="row g-3">
                            <div class="col-auto">
                                <div class="form-check pt-4">
                                    <input class="form-check-input" type="checkbox" id="item{{ $item['id'] }}" name="selected_items[]" value="{{ $item['id'] }}">
                                    <label class="form-check-label" for="item{{ $item['id'] }}"></label>
                                </div>
                            </div>
                            <div class="col-auto">
                                @if(isset($item['item']['gambar']) && $item['item']['gambar'])
                                    <img src="{{ asset('storage/' . $item['item']['gambar']) }}" class="item-img" alt="{{ $item['item']['nama_item'] ?? 'Produk' }}">
                                @else
                                    <img src="{{ asset('images/products/default.png') }}" class="item-img" alt="{{ $item['item']['nama_item'] ?? 'Produk' }}">
                                @endif
                            </div>
                            <div class="col">
                                <div class="item-details">
                                    <h5>{{ $item['item']['nama_item'] ?? 'Nama Produk' }}</h5>
                                    <p>Bahan: {{ $item['bahan']['nama_bahan'] ?? '-' }}</p>
                                    <p>Ukuran: {{ $item['ukuran']['size'] ?? '-' }}</p>
                                    <p>Jenis: {{ $item['jenis']['kategori'] ?? '-' }}</p>
                                    <p>
                                        Upload Desain: 
                                        @if(isset($item['upload_desain']) && $item['upload_desain'])
                                            <span class="upload-status status-uploaded">Sudah diupload</span>
                                            <form method="POST" action="{{ route('keranjang.upload-design', $item['id']) }}" enctype="multipart/form-data" style="display: inline;">
                                                @csrf
                                                <input type="file" name="upload_desain" id="design{{ $item['id'] }}" style="display: none;" accept=".jpeg,.png,.jpg,.pdf,.ai,.psd" onchange="this.form.submit()">
                                                <label for="design{{ $item['id'] }}" class="btn btn-primary"><i class="fas fa-edit me-1"></i> Ganti Desain</label>
                                            </form>
                                        @else
                                            <span class="upload-status status-pending">Belum diupload</span>
                                            <form method="POST" action="{{ route('keranjang.upload-design', $item['id']) }}" enctype="multipart/form-data" style="display: inline;">
                                                @csrf
                                                <input type="file" name="upload_desain" id="design{{ $item['id'] }}" style="display: none;" accept=".jpeg,.png,.jpg,.pdf,.ai,.psd" onchange="this.form.submit()">
                                                <label for="design{{ $item['id'] }}" class="btn btn-primary"><i class="fas fa-upload me-1"></i> Upload Desain</label>
                                            </form>
                                        @endif
                                    </p>
                                    <div class="d-flex mt-1">
                                        <form method="POST" action="{{ route('keranjang.remove', $item['id']) }}" style="display: inline;" onsubmit="return confirm('Yakin ingin menghapus item ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn item-action btn-danger">
                                                <i class="fas fa-trash-alt me-1"></i> Hapus
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="d-flex flex-column align-items-end">
                                    <div class="item-price">Rp {{ number_format($item['total_harga'] ?? 0, 0, ',', '.') }}</div>
                                    <div class="quantity-control" data-item-id="{{ $item['id'] }}" data-base-price="{{ ($item['total_harga'] ?? 0) / max(1, $item['quantity'] ?? 1) }}">
                                        <button type="button" class="btn-decrease" {{ ($item['quantity'] ?? 1) <= 1 ? 'disabled' : '' }}>-</button>
                                        <input type="text" class="quantity-input" value="{{ $item['quantity'] ?? 1 }}" readonly>
                                        <button type="button" class="btn-increase">+</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            
            <div class="col-lg-4">
                <div class="summary-card">
                    <h5>Ringkasan Pesanan</h5>
                    <div id="summary-items"></div>
                    <div class="summary-item">
                        <span>Biaya Desain</span>
                        <span id="design-cost">Rp {{ number_format($summary['biaya_desain'] ?? 0, 0, ',', '.') }}</span>
                    </div>
                    <div class="summary-item summary-total">
                        <span>Total</span>
                        <span id="grand-total">Rp 0</span>
                    </div>
                    <a href="#" class="checkout-btn btn btn-primary" id="checkout-btn">CHECKOUT SEKARANG ({{ $summary['total_items'] ?? 0 }} item)</a>
                    <form method="POST" action="{{ route('keranjang.clear') }}" class="mt-3" onsubmit="return confirm('Yakin ingin mengosongkan keranjang?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger"><i class="fas fa-trash me-1"></i> Kosongkan Keranjang</button>
                    </form>
                </div>
            </div>
        </div>
    @else
        <div class="empty-cart">
            <img src="{{ asset('images/empty-cart.png') }}" alt="Keranjang Kosong">
            <h4>Keranjang Anda kosong</h4>
            <p class="text-muted">Anda belum menambahkan produk apapun ke keranjang</p>
            <a href="{{ route('welcome') }}" class="btn btn-primary px-4">Belanja Sekarang</a>
        </div>
    @endif
</div>

@if(isset($keranjangItems) && count($keranjangItems) > 0)
<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectAllCheckbox = document.getElementById('selectAll');
    const itemCheckboxes = document.querySelectorAll('input[name="selected_items[]"]');
    const quantityControls = document.querySelectorAll('.quantity-control');

    updateCartSummary();
    
    // Individual checkbox change
    itemCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateCartSummary);
    });
    
    // Select all functionality
    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function() {
            itemCheckboxes.forEach(checkbox => {
                checkbox.checked = selectAllCheckbox.checked;
            });
            updateCartSummary();
        });
    }

    // Real-time quantity update functionality
    quantityControls.forEach(control => {
        const decreaseBtn = control.querySelector('.btn-decrease');
        const increaseBtn = control.querySelector('.btn-increase');
        const quantityInput = control.querySelector('.quantity-input');
        const itemId = control.dataset.itemId;
        const basePrice = parseFloat(control.dataset.basePrice || 0);
        
        decreaseBtn.addEventListener('click', function() {
            const currentQuantity = parseInt(quantityInput.value) || 1;
            updateQuantity(itemId, Math.max(currentQuantity - 1, 1));
        });
        
        increaseBtn.addEventListener('click', function() {
            const currentQuantity = parseInt(quantityInput.value) || 1;
            updateQuantity(itemId, currentQuantity + 1);
        });
    });

    function updateQuantity(itemId, newQuantity) {
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        fetch(`/keranjang/${itemId}/quantity`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ quantity: newQuantity })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const quantityInput = document.querySelector(`.quantity-control[data-item-id="${itemId}"] .quantity-input`);
                quantityInput.value = newQuantity;
                updateCartSummary();
            }
        })
        .catch(error => console.error('Error:', error));
    }

    function updateCartSummary() {
        let totalItems = 0;
        let grandTotal = 0;
        
        itemCheckboxes.forEach(item => {
            if (item.checked) {
                const quantityInput = item.closest('.cart-item').querySelector('.quantity-input');
                const priceText = item.closest('.cart-item').querySelector('.item-price').textContent;
                
                const quantity = parseInt(quantityInput.value) || 0;
                const priceOnly = priceText.replace(/[^\d]/g, '');
                const price = parseFloat(priceOnly) || 0;
                
                totalItems += quantity;
                grandTotal += price * quantity;  // Update total based on quantity
            }
        });
        
        document.getElementById('grand-total').textContent = 'Rp ' + grandTotal.toLocaleString('id-ID');
        const cartHeaderCount = document.querySelector('h4');
        cartHeaderCount.textContent = `Keranjang Anda (${totalItems} item)`;
        
        const checkoutBtn = document.getElementById('checkout-btn');
        if (checkoutBtn) {
            checkoutBtn.style.pointerEvents = totalItems > 0 ? 'auto' : 'none';
            checkoutBtn.textContent = totalItems > 0 ? `CHECKOUT SEKARANG (${totalItems} item)` : 'PILIH ITEM UNTUK CHECKOUT';
        }
    }
});
</script>
@endif
@endsection