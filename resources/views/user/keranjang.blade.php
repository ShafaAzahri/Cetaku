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
        cursor: pointer;
    }
    .quantity-control button:hover {
        background: #e0e0e0;
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
        text-decoration: none;
        font-size: 12px;
    }
    .upload-btn:hover {
        background-color: #e5e5e5;
        color: #333;
        text-decoration: none;
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
        text-decoration: none;
        display: inline-block;
        text-align: center;
    }
    .checkout-btn:hover {
        background-color: #3651d4;
        color: white;
        text-decoration: none;
    }
    .select-all {
        margin-right: 10px;
    }
    .empty-cart {
        text-align: center;
        padding: 60px 20px;
        color: #666;
    }
    .empty-cart img {
        max-width: 200px;
        opacity: 0.7;
        margin-bottom: 20px;
    }
    .empty-cart h4 {
        margin-bottom: 10px;
        color: #333;
    }
    .empty-cart p {
        margin-bottom: 20px;
    }
    .btn-primary {
        background-color: #4361ee;
        border-color: #4361ee;
        padding: 10px 30px;
    }
    .btn-primary:hover {
        background-color: #3651d4;
        border-color: #3651d4;
    }
</style>
@endsection

@section('content')
<!-- Breadcrumb -->
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
    <!-- Alert -->
    @include('user.components.alert')
    
    @if(isset($keranjangItems) && count($keranjangItems) > 0)
        <!-- Cart Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="mb-0">Keranjang Anda ({{ $summary['total_items'] ?? 0 }} item)</h4>
        </div>
        
        <!-- Select All Header -->
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
                @if(isset($groupedItems) && count($groupedItems) > 0)
                    @foreach($groupedItems as $kategori => $items)
                        <div class="mb-4">
                            <div class="cart-group-title">Kategori: {{ $kategori }}</div>
                            
                            @foreach($items as $item)
                                <div class="cart-item">
                                    <div class="row g-3">
                                        <div class="col-auto">
                                            <div class="form-check pt-4">
                                                <input class="form-check-input" type="checkbox" 
                                                       id="item{{ $item['id'] }}" 
                                                       name="selected_items[]" 
                                                       value="{{ $item['id'] }}">
                                                <label class="form-check-label" for="item{{ $item['id'] }}"></label>
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            @if(isset($item['item']['gambar']) && $item['item']['gambar'])
                                                <img src="{{ asset('storage/' . $item['item']['gambar']) }}" 
                                                     class="item-img" alt="{{ $item['item']['nama_item'] ?? 'Produk' }}">
                                            @else
                                                <img src="{{ asset('images/products/default.png') }}" 
                                                     class="item-img" alt="{{ $item['item']['nama_item'] ?? 'Produk' }}">
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
                                                        <form method="POST" action="{{ route('keranjang.upload-design', $item['id']) }}" 
                                                              enctype="multipart/form-data" style="display: inline;">
                                                            @csrf
                                                            <input type="file" name="upload_desain" id="design{{ $item['id'] }}" 
                                                                   style="display: none;" accept=".jpeg,.png,.jpg,.pdf,.ai,.psd"
                                                                   onchange="this.form.submit()">
                                                            <label for="design{{ $item['id'] }}" class="upload-btn">
                                                                <i class="fas fa-edit me-1"></i> Ganti Desain
                                                            </label>
                                                        </form>
                                                    @else
                                                        <span class="upload-status status-pending">Belum diupload</span>
                                                        <form method="POST" action="{{ route('keranjang.upload-design', $item['id']) }}" 
                                                              enctype="multipart/form-data" style="display: inline;">
                                                            @csrf
                                                            <input type="file" name="upload_desain" id="design{{ $item['id'] }}" 
                                                                   style="display: none;" accept=".jpeg,.png,.jpg,.pdf,.ai,.psd"
                                                                   onchange="this.form.submit()">
                                                            <label for="design{{ $item['id'] }}" class="upload-btn">
                                                                <i class="fas fa-upload me-1"></i> Upload Desain
                                                            </label>
                                                        </form>
                                                    @endif
                                                </p>
                                                <div class="d-block mt-1">
                                                    <form method="POST" action="{{ route('keranjang.remove', $item['id']) }}" 
                                                          style="display: inline;"
                                                          onsubmit="return confirm('Yakin ingin menghapus item ini?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn p-0 item-action">
                                                            <i class="fas fa-trash-alt me-1"></i> Hapus
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="d-flex flex-column align-items-end h-100 justify-content-between">
                                                <div class="item-price">Rp {{ number_format($item['total_harga'] ?? 0, 0, ',', '.') }}</div>
                                                <div class="quantity-control" 
                                                     data-item-id="{{ $item['id'] }}"
                                                     data-base-price="{{ ($item['total_harga'] ?? 0) / max(1, $item['quantity'] ?? 1) }}">
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
                    @endforeach
                @else
                    <!-- Tampilkan items tanpa grouping -->
                    @foreach($keranjangItems as $item)
                        <div class="cart-item mb-3">
                            <div class="row g-3">
                                <div class="col-auto">
                                    <div class="form-check pt-4">
                                        <input class="form-check-input" type="checkbox" 
                                               id="item{{ $item['id'] }}" 
                                               name="selected_items[]" 
                                               value="{{ $item['id'] }}">
                                        <label class="form-check-label" for="item{{ $item['id'] }}"></label>
                                    </div>
                                </div>
                                <div class="col-auto">
                                    @if(isset($item['item']['gambar']) && $item['item']['gambar'])
                                        <img src="{{ asset('storage/' . $item['item']['gambar']) }}" 
                                             class="item-img" alt="{{ $item['item']['nama_item'] ?? 'Produk' }}">
                                    @else
                                        <img src="{{ asset('images/products/default.png') }}" 
                                             class="item-img" alt="{{ $item['item']['nama_item'] ?? 'Produk' }}">
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
                                                <form method="POST" action="{{ route('keranjang.upload-design', $item['id']) }}" 
                                                      enctype="multipart/form-data" style="display: inline;">
                                                    @csrf
                                                    <input type="file" name="upload_desain" id="design{{ $item['id'] }}" 
                                                           style="display: none;" accept=".jpeg,.png,.jpg,.pdf,.ai,.psd"
                                                           onchange="this.form.submit()">
                                                    <label for="design{{ $item['id'] }}" class="upload-btn">
                                                        <i class="fas fa-edit me-1"></i> Ganti Desain
                                                    </label>
                                                </form>
                                            @else
                                                <span class="upload-status status-pending">Belum diupload</span>
                                                <form method="POST" action="{{ route('keranjang.upload-design', $item['id']) }}" 
                                                      enctype="multipart/form-data" style="display: inline;">
                                                    @csrf
                                                    <input type="file" name="upload_desain" id="design{{ $item['id'] }}" 
                                                           style="display: none;" accept=".jpeg,.png,.jpg,.pdf,.ai,.psd"
                                                           onchange="this.form.submit()">
                                                    <label for="design{{ $item['id'] }}" class="upload-btn">
                                                        <i class="fas fa-upload me-1"></i> Upload Desain
                                                    </label>
                                                </form>
                                            @endif
                                        </p>
                                        <div class="d-block mt-1">
                                            <form method="POST" action="{{ route('keranjang.remove', $item['id']) }}" 
                                                  style="display: inline;"
                                                  onsubmit="return confirm('Yakin ingin menghapus item ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn p-0 item-action">
                                                    <i class="fas fa-trash-alt me-1"></i> Hapus
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="d-flex flex-column align-items-end h-100 justify-content-between">
                                        <div class="item-price">Rp {{ number_format($item['total_harga'] ?? 0, 0, ',', '.') }}</div>
                                        <div class="quantity-control" 
                                             data-item-id="{{ $item['id'] }}"
                                             data-base-price="{{ ($item['total_harga'] ?? 0) / max(1, $item['quantity'] ?? 1) }}">
                                            <button type="button" class="btn-decrease" {{ ($item['quantity'] ?? 1) <= 1 ? 'disabled' : '' }}>-</button>
                                            <input type="text" class="quantity-input" value="{{ $item['quantity'] ?? 1 }}" readonly>
                                            <button type="button" class="btn-increase">+</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
            
            <!-- Order Summary -->
            <div class="col-lg-4">
                <div class="summary-card">
                    <h5>Ringkasan Pesanan</h5>
                    
                    <!-- Dynamic Summary Items -->
                    <div id="summary-items">
                        <!-- Items akan diisi oleh JavaScript -->
                    </div>
                    
                    <div class="summary-item">
                        <span>Biaya Desain</span>
                        <span id="design-cost">Rp {{ number_format($summary['biaya_desain'] ?? 0, 0, ',', '.') }}</span>
                    </div>
                    
                    <div class="summary-item summary-total">
                        <span>Total</span>
                        <span id="grand-total">Rp 0</span>
                    </div>
                    
                    <a href="#" class="checkout-btn" id="checkout-btn">PILIH ITEM UNTUK CHECKOUT</a>
                    
                    <form method="POST" action="{{ route('keranjang.clear') }}" class="mt-3"
                        onsubmit="return confirm('Yakin ingin mengosongkan keranjang?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger w-100">
                            <i class="fas fa-trash me-1"></i> Kosongkan Keranjang
                        </button>
                    </form>
                </div>
            </div>
        </div>
    @else
        <!-- Empty Cart State -->
        <div class="empty-cart">
            <img src="{{ asset('images/empty-cart.png') }}" alt="Keranjang Kosong">
            <h4>Keranjang Anda kosong</h4>
            <p class="text-muted">Anda belum menambahkan produk apapun ke keranjang</p>
            <a href="{{ route('welcome') }}" class="btn btn-primary px-4">Belanja Sekarang</a>
        </div>
    @endif
</div>

@if(isset($keranjangItems) && count($keranjangItems) > 0)
<!-- JavaScript for Cart functionality -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectAllCheckbox = document.getElementById('selectAll');
    const itemCheckboxes = document.querySelectorAll('input[name="selected_items[]"]');
    
    // Initialize summary on page load
    updateCartSummary();
    
    // Individual checkbox change
    itemCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const allChecked = Array.from(itemCheckboxes).every(cb => cb.checked);
            const someChecked = Array.from(itemCheckboxes).some(cb => cb.checked);
            
            if (selectAllCheckbox) {
                selectAllCheckbox.checked = allChecked;
                selectAllCheckbox.indeterminate = someChecked && !allChecked;
            }
            
            updateCartSummary();
        });
    });
    
    // Select all functionality
    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function() {
            itemCheckboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
            updateCartSummary();
        });
    }

    // Real-time quantity update functionality
    const quantityControls = document.querySelectorAll('.quantity-control');
    
    quantityControls.forEach(control => {
        const decreaseBtn = control.querySelector('.btn-decrease');
        const increaseBtn = control.querySelector('.btn-increase');
        const quantityInput = control.querySelector('.quantity-input');
        const itemRow = control.closest('.cart-item');
        const itemPriceElement = itemRow.querySelector('.item-price');
        const itemId = control.dataset.itemId;
        const basePrice = parseFloat(control.dataset.basePrice || 0);
        
        function updatePriceDisplay(quantity) {
            const totalPrice = basePrice * quantity;
            itemPriceElement.textContent = 'Rp ' + totalPrice.toLocaleString('id-ID');
            updateCartSummary();
        }
        
        function updateQuantity(newQuantity, callback) {
            if (newQuantity < 1) return;
            
            decreaseBtn.disabled = true;
            increaseBtn.disabled = true;
            
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            
            fetch(`/keranjang/${itemId}/quantity`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    quantity: newQuantity
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    quantityInput.value = newQuantity;
                    updatePriceDisplay(newQuantity);
                    updateCartSummary();
                    if (callback) callback();
                } else {
                    alert('Gagal mengupdate quantity: ' + (data.message || 'Terjadi kesalahan'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Terjadi kesalahan saat mengupdate quantity');
            })
            .finally(() => {
                decreaseBtn.disabled = newQuantity <= 1;
                increaseBtn.disabled = false;
            });
        }
        
        if (decreaseBtn) {
            decreaseBtn.addEventListener('click', function(e) {
                e.preventDefault();
                const currentQuantity = parseInt(quantityInput.value) || 1;
                if (currentQuantity > 1) {
                    updateQuantity(currentQuantity - 1);
                }
            });
        }
        
        if (increaseBtn) {
            increaseBtn.addEventListener('click', function(e) {
                e.preventDefault();
                const currentQuantity = parseInt(quantityInput.value) || 1;
                updateQuantity(currentQuantity + 1);
            });
        }
    });
    
    // Function to update cart summary dengan detail per kategori
    function updateCartSummary() {
        let totalItems = 0;
        let grandTotal = 0;
        let checkedItemsCount = 0;
        let categoryTotals = {};
        
        // Ambil biaya desain original
        const originalDesignCost = {{ $summary['biaya_desain'] ?? 0 }};
        
        // Hitung per kategori yang dicentang
        document.querySelectorAll('.cart-item').forEach(item => {
            const checkbox = item.querySelector('input[name="selected_items[]"]');
            const quantityInput = item.querySelector('.quantity-input');
            const priceText = item.querySelector('.item-price').textContent;
            const itemName = item.querySelector('.item-details h5').textContent.trim();
            
            if (checkbox && checkbox.checked && quantityInput && priceText) {
                const quantity = parseInt(quantityInput.value) || 0;
                const priceOnly = priceText.replace(/[^\d]/g, '');
                const price = parseFloat(priceOnly) || 0;
                
                totalItems += quantity;
                grandTotal += price;
                checkedItemsCount++;
                
                // Group by product name
                if (categoryTotals[itemName]) {
                    categoryTotals[itemName].quantity += quantity;
                    categoryTotals[itemName].total += price;
                } else {
                    categoryTotals[itemName] = {
                        quantity: quantity,
                        total: price
                    };
                }
            }
        });
        
        // Update summary items
        const summaryItemsContainer = document.getElementById('summary-items');
        summaryItemsContainer.innerHTML = '';
        
        if (checkedItemsCount > 0) {
            // Tampilkan detail per kategori
            for (const [productName, data] of Object.entries(categoryTotals)) {
                const summaryItem = document.createElement('div');
                summaryItem.className = 'summary-item';
                summaryItem.innerHTML = `
                    <span>${productName} (${data.quantity} pcs)</span>
                    <span>Rp ${data.total.toLocaleString('id-ID')}</span>
                `;
                summaryItemsContainer.appendChild(summaryItem);
            }
            
            // Tampilkan subtotal jika ada lebih dari 1 kategori
            if (Object.keys(categoryTotals).length > 1) {
                const subtotalItem = document.createElement('div');
                subtotalItem.className = 'summary-item';
                subtotalItem.style.borderTop = '1px solid #e5e5e5';
                subtotalItem.style.paddingTop = '10px';
                subtotalItem.style.marginTop = '10px';
                subtotalItem.style.fontWeight = '500';
                subtotalItem.innerHTML = `
                    <span>Subtotal (${totalItems} produk)</span>
                    <span>Rp ${grandTotal.toLocaleString('id-ID')}</span>
                `;
                summaryItemsContainer.appendChild(subtotalItem);
            }
        } else {
            // Tampilkan pesan jika tidak ada item yang dipilih
            const emptyItem = document.createElement('div');
            emptyItem.className = 'summary-item';
            emptyItem.style.color = '#666';
            emptyItem.style.fontStyle = 'italic';
            emptyItem.innerHTML = `
                <span>Belum ada item yang dipilih</span>
                <span>Rp 0</span>
            `;
            summaryItemsContainer.appendChild(emptyItem);
        }
        
        // Update biaya desain
        const designCostElement = document.getElementById('design-cost');
        if (designCostElement) {
            const displayDesignCost = checkedItemsCount > 0 ? originalDesignCost : 0;
            designCostElement.textContent = 'Rp ' + displayDesignCost.toLocaleString('id-ID');
        }
        
        // Update grand total
        const grandTotalElement = document.getElementById('grand-total');
        if (grandTotalElement) {
            const displayDesignCost = checkedItemsCount > 0 ? originalDesignCost : 0;
            const finalTotal = grandTotal + displayDesignCost;
            grandTotalElement.textContent = 'Rp ' + finalTotal.toLocaleString('id-ID');
        }
        
        // Update header
        const cartHeaderCount = document.querySelector('h4');
        const allItems = document.querySelectorAll('.cart-item').length;
        if (cartHeaderCount) {
            cartHeaderCount.textContent = `Keranjang Anda (${allItems} item)`;
        }
        
        // Update checkout button
        const checkoutBtn = document.getElementById('checkout-btn');
        if (checkoutBtn) {
            if (checkedItemsCount > 0) {
                checkoutBtn.style.opacity = '1';
                checkoutBtn.style.pointerEvents = 'auto';
                checkoutBtn.style.backgroundColor = '#4361ee';
                checkoutBtn.textContent = `CHECKOUT SEKARANG (${checkedItemsCount} item)`;
            } else {
                checkoutBtn.style.opacity = '0.5';
                checkoutBtn.style.pointerEvents = 'none';
                checkoutBtn.style.backgroundColor = '#6c757d';
                checkoutBtn.textContent = 'PILIH ITEM UNTUK CHECKOUT';
            }
        }
        
        console.log('Category Totals:', categoryTotals);
        console.log(`Grand Total: ${grandTotal}, Items: ${totalItems}`);
    }
});
</script>
@endif
@endsection