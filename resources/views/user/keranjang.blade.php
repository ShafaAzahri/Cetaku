@extends('user.layouts.app')

@section('title', 'Keranjang')

@push('meta')
<meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('custom-css')
<style>
    .cart-container {
        padding: 20px 0;
        background-color: #f8f9fa;
    }
    
    .cart-card {
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
    
    .select-all {
        margin-bottom: 20px;
        padding-bottom: 15px;
        border-bottom: 1px solid #f0f0f0;
    }
    
    .cart-item {
        display: flex;
        gap: 15px;
        padding: 15px 0;
        border-bottom: 1px solid #f0f0f0;
        position: relative;
    }
    
    .cart-item:last-child {
        border-bottom: none;
    }
    
    .item-checkbox {
        position: absolute;
        top: 15px;
        left: -5px;
    }
    
    .item-image {
        width: 80px;
        height: 80px;
        border-radius: 8px;
        object-fit: cover;
        margin-left: 20px;
    }
    
    .item-details {
        flex: 1;
        margin-left: 10px;
    }
    
    .item-name {
        font-weight: 600;
        margin-bottom: 5px;
        font-size: 16px;
        color: #333;
    }
    
    .item-specs {
        color: #666;
        font-size: 14px;
        line-height: 1.4;
        margin-bottom: 8px;
    }
    
    .item-status {
        display: inline-block;
        padding: 2px 8px;
        border-radius: 12px;
        font-size: 11px;
        font-weight: 500;
    }
    
    .status-uploaded {
        background: #d4edda;
        color: #155724;
    }
    
    .status-pending {
        background: #fff3cd;
        color: #856404;
    }
    
    .item-actions {
        margin-top: 8px;
        display: flex;
        gap: 8px;
    }
    
    .btn-small {
        padding: 4px 8px;
        font-size: 11px;
        border-radius: 4px;
        border: none;
        cursor: pointer;
        transition: all 0.3s ease;
    }
    
    .btn-upload {
        background: #4361ee;
        color: white;
    }
    
    .btn-delete {
        background: #dc3545;
        color: white;
    }
    
    .item-price-section {
        text-align: right;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        align-items: flex-end;
        min-width: 120px;
    }
    
    .item-price {
        font-weight: 600;
        color: #4361ee;
        font-size: 16px;
    }
    
    .quantity-control {
        display: flex;
        align-items: center;
        border: 1px solid #e0e0e0;
        border-radius: 6px;
        margin-top: 8px;
    }
    
    .quantity-control button {
        border: none;
        background: #f8f9fa;
        width: 28px;
        height: 28px;
        cursor: pointer;
        font-size: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .quantity-control input {
        width: 40px;
        text-align: center;
        border: none;
        height: 28px;
        font-size: 14px;
    }
    
    .summary-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        padding: 20px;
        position: sticky;
        top: 20px;
    }
    
    .summary-row {
        display: flex;
        justify-content: space-between;
        padding: 8px 0;
        font-size: 14px;
    }
    
    .summary-row.total {
        border-top: 1px solid #e5e5e5;
        padding-top: 15px;
        margin-top: 10px;
        font-weight: 600;
        font-size: 16px;
    }
    
    .btn-checkout {
        background: #4361ee;
        color: white;
        border: none;
        padding: 15px;
        border-radius: 8px;
        font-weight: 600;
        font-size: 16px;
        width: 100%;
        margin-top: 15px;
        transition: all 0.3s ease;
    }
    
    .btn-checkout:hover {
        background: #3651d4;
    }
    
    .btn-checkout:disabled,
    .btn-checkout[style*="pointer-events: none"] {
        background: #e9ecef !important;
        color: #6c757d !important;
        cursor: not-allowed;
    }
    
    .btn-clear {
        background: transparent;
        color: #dc3545;
        border: 1px solid #dc3545;
        padding: 10px 15px;
        border-radius: 6px;
        font-size: 14px;
        width: 100%;
        margin-top: 10px;
        transition: all 0.3s ease;
    }
    
    .btn-clear:hover {
        background: #dc3545;
        color: white;
    }
    
    .empty-cart {
        text-align: center;
        padding: 60px 20px;
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    
    .empty-cart img {
        width: 120px;
        margin-bottom: 20px;
        opacity: 0.7;
    }
    
    .empty-cart h4 {
        color: #666;
        margin-bottom: 10px;
    }
    
    .empty-cart p {
        color: #999;
        margin-bottom: 20px;
    }
    
    .btn-shop {
        background: #4361ee;
        color: white;
        padding: 12px 24px;
        border-radius: 8px;
        text-decoration: none;
        font-weight: 600;
        display: inline-block;
        transition: all 0.3s ease;
    }
    
    .btn-shop:hover {
        background: #3651d4;
        color: white;
        text-decoration: none;
    }
    
    @media (max-width: 768px) {
        .cart-item {
            flex-direction: column;
            gap: 10px;
        }
        
        .item-image {
            width: 60px;
            height: 60px;
            margin-left: 20px;
        }
        
        .item-price-section {
            flex-direction: row;
            justify-content: space-between;
            align-items: center;
            min-width: auto;
        }
        
        .quantity-control {
            margin-top: 0;
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
                <li class="breadcrumb-item active">Keranjang</li>
            </ol>
        </nav>
    </div>
</div>

<div class="cart-container">
    <div class="container">
        @include('user.components.alert')
        
        @if(isset($keranjangItems) && count($keranjangItems) > 0)
            <div class="row">
                <div class="col-lg-8">
                    <div class="cart-card">
                        <h5 class="section-title">
                            <i class="fas fa-shopping-cart"></i>
                            Keranjang Anda
                        </h5>
                        
                        <div class="select-all">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="selectAll">
                                <label class="form-check-label fw-semibold" for="selectAll">
                                    Pilih Semua (<span id="total-items">{{ $summary['count_products'] ?? 0 }}</span> produk)
                                </label>
                            </div>
                        </div>

                        @foreach($keranjangItems as $item)
                            <div class="cart-item" data-item-id="{{ $item['id'] }}">
                                <input class="form-check-input item-checkbox" type="checkbox" name="selected_items[]" value="{{ $item['id'] }}">
                                
                                @if(isset($item['item']['gambar']) && $item['item']['gambar'])
                                    <img src="{{ asset('storage/' . $item['item']['gambar']) }}" class="item-image" alt="{{ $item['item']['nama_item'] ?? 'Produk' }}">
                                @else
                                    <img src="{{ asset('images/products/default.png') }}" class="item-image" alt="{{ $item['item']['nama_item'] ?? 'Produk' }}">
                                @endif
                                
                                <div class="item-details">
                                    <div class="item-name">{{ $item['item']['nama_item'] ?? 'Nama Produk' }}</div>
                                    <div class="item-specs">
                                        {{ $item['bahan']['nama_bahan'] ?? '-' }} • {{ $item['ukuran']['size'] ?? '-' }} • {{ $item['jenis']['kategori'] ?? '-' }}
                                    </div>
                                    <div class="mb-2">
                                        @if(isset($item['upload_desain']) && $item['upload_desain'])
                                            <span class="item-status status-uploaded">Desain Ready</span>
                                        @else
                                            <span class="item-status status-pending">Perlu Upload</span>
                                        @endif
                                    </div>
                                    <div class="item-actions">
                                        @if(isset($item['upload_desain']) && $item['upload_desain'])
                                            <form method="POST" action="{{ route('keranjang.upload-design', $item['id']) }}" enctype="multipart/form-data" style="display: inline;">
                                                @csrf
                                                <input type="file" name="upload_desain" id="design{{ $item['id'] }}" style="display: none;" accept=".jpeg,.png,.jpg,.pdf,.ai,.psd" onchange="this.form.submit()">
                                                <label for="design{{ $item['id'] }}" class="btn-small btn-upload">Ganti</label>
                                            </form>
                                        @else
                                            <form method="POST" action="{{ route('keranjang.upload-design', $item['id']) }}" enctype="multipart/form-data" style="display: inline;">
                                                @csrf
                                                <input type="file" name="upload_desain" id="design{{ $item['id'] }}" style="display: none;" accept=".jpeg,.png,.jpg,.pdf,.ai,.psd" onchange="this.form.submit()">
                                                <label for="design{{ $item['id'] }}" class="btn-small btn-upload">Upload</label>
                                            </form>
                                        @endif
                                        
                                        <form method="POST" action="{{ route('keranjang.remove', $item['id']) }}" style="display: inline;" onsubmit="return confirm('Hapus item ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn-small btn-delete">Hapus</button>
                                        </form>
                                    </div>
                                </div>
                                
                                <div class="item-price-section">
                                    <div class="item-price">Rp {{ number_format($item['total_harga'] ?? 0, 0, ',', '.') }}</div>
                                    <div class="quantity-control" data-item-id="{{ $item['id'] }}" data-unit-price="{{ ($item['total_harga'] ?? 0) / max(1, $item['quantity'] ?? 1) }}">
                                        <button type="button" class="btn-decrease" {{ ($item['quantity'] ?? 1) <= 1 ? 'disabled' : '' }}>−</button>
                                        <input type="text" class="quantity-input" value="{{ $item['quantity'] ?? 1 }}" readonly>
                                        <button type="button" class="btn-increase">+</button>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                
                <div class="col-lg-4">
                    <div class="summary-card">
                        <h6 class="fw-semibold mb-3">Ringkasan Belanja</h6>
                        
                        <div id="summary-items">
                            <!-- Dynamic content -->
                        </div>
                        
                        <div class="summary-row">
                            <span>Biaya Desain</span>
                            <span>Rp {{ number_format($summary['biaya_desain'] ?? 0, 0, ',', '.') }}</span>
                        </div>
                        
                        <div class="summary-row total">
                            <span>Total</span>
                            <span id="grand-total">Rp 0</span>
                        </div>
                        
                        <a href="{{ route('checkout') }}" class="btn-checkout" id="checkout-btn" style="text-decoration: none; display: block; text-align: center; pointer-events: none; opacity: 0.6;">
                            Checkout (<span id="selected-count">0</span> item)
                        </a>
                        
                        <form method="POST" action="{{ route('keranjang.clear') }}" onsubmit="return confirm('Kosongkan keranjang?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-clear">Kosongkan Keranjang</button>
                        </form>
                    </div>
                </div>
            </div>
        @else
            <div class="empty-cart">
                <img src="{{ asset('images/empty-cart.png') }}" alt="Keranjang Kosong">
                <h4>Keranjang Masih Kosong</h4>
                <p>Yuk mulai belanja dan temukan produk favoritmu!</p>
                <a href="{{ route('welcome') }}" class="btn-shop">Mulai Belanja</a>
            </div>
        @endif
    </div>
</div>

@if(isset($keranjangItems) && count($keranjangItems) > 0)
<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectAllCheckbox = document.getElementById('selectAll');
    const itemCheckboxes = document.querySelectorAll('.item-checkbox');
    const quantityControls = document.querySelectorAll('.quantity-control');
    const biayaDesain = {{ $summary['biaya_desain'] ?? 0 }};

    updateCartSummary();
    
    // Checkbox handlers
    itemCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateCartSummary);
    });
    
    selectAllCheckbox.addEventListener('change', function() {
        itemCheckboxes.forEach(checkbox => {
            checkbox.checked = selectAllCheckbox.checked;
        });
        updateCartSummary();
    });

    // Quantity controls
    quantityControls.forEach(control => {
        const decreaseBtn = control.querySelector('.btn-decrease');
        const increaseBtn = control.querySelector('.btn-increase');
        const quantityInput = control.querySelector('.quantity-input');
        const itemId = control.dataset.itemId;
        
        decreaseBtn.addEventListener('click', () => {
            const currentQuantity = parseInt(quantityInput.value) || 1;
            if (currentQuantity > 1) updateQuantity(itemId, currentQuantity - 1);
        });
        
        increaseBtn.addEventListener('click', () => {
            const currentQuantity = parseInt(quantityInput.value) || 1;
            updateQuantity(itemId, currentQuantity + 1);
        });
    });

    function updateQuantity(itemId, newQuantity) {
        fetch(`/keranjang/${itemId}/quantity`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ quantity: newQuantity })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const quantityInput = document.querySelector(`.quantity-control[data-item-id="${itemId}"] .quantity-input`);
                quantityInput.value = newQuantity;
                
                const unitPrice = parseFloat(document.querySelector(`.quantity-control[data-item-id="${itemId}"]`).dataset.unitPrice);
                const newTotalPrice = unitPrice * newQuantity;
                const priceElement = document.querySelector(`[data-item-id="${itemId}"] .item-price`);
                priceElement.textContent = `Rp ${newTotalPrice.toLocaleString('id-ID')}`;
                
                const control = document.querySelector(`.quantity-control[data-item-id="${itemId}"]`);
                control.querySelector('.btn-decrease').disabled = newQuantity <= 1;
                
                updateCartSummary();
            }
        })
        .catch(error => console.error('Error:', error));
    }

    function updateCartSummary() {
        let totalItems = 0;
        let subtotal = 0;
        const summaryHtml = [];
        
        itemCheckboxes.forEach(checkbox => {
            if (checkbox.checked) {
                const cartItem = checkbox.closest('.cart-item');
                const itemName = cartItem.querySelector('.item-name').textContent;
                const quantityInput = cartItem.querySelector('.quantity-input');
                const priceElement = cartItem.querySelector('.item-price');
                
                const quantity = parseInt(quantityInput.value) || 0;
                const price = parseFloat(priceElement.textContent.replace(/[^\d]/g, '')) || 0;
                
                totalItems += quantity;
                subtotal += price;
                
                summaryHtml.push(`
                    <div class="summary-row">
                        <span>${itemName} (${quantity}x)</span>
                        <span>Rp ${price.toLocaleString('id-ID')}</span>
                    </div>
                `);
            }
        });
        
        document.getElementById('summary-items').innerHTML = summaryHtml.join('');
        
        const grandTotal = subtotal + (totalItems > 0 ? biayaDesain : 0);
        document.getElementById('grand-total').textContent = `Rp ${grandTotal.toLocaleString('id-ID')}`;
        document.getElementById('selected-count').textContent = totalItems;
        document.getElementById('total-items').textContent = itemCheckboxes.length;
        
        const checkoutBtn = document.getElementById('checkout-btn');
        if (totalItems === 0) {
            checkoutBtn.style.pointerEvents = 'none';
            checkoutBtn.style.opacity = '0.6';
        } else {
            checkoutBtn.style.pointerEvents = 'auto';
            checkoutBtn.style.opacity = '1';
        }
        
        const checkedCount = Array.from(itemCheckboxes).filter(cb => cb.checked).length;
        selectAllCheckbox.checked = checkedCount === itemCheckboxes.length && itemCheckboxes.length > 0;
        selectAllCheckbox.indeterminate = checkedCount > 0 && checkedCount < itemCheckboxes.length;
    }
});
</script>
@endif
@endsection