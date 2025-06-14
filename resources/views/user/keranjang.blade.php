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
    
    .status-toko {
        background: #d1ecf1;
        color: #0c5460;
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
        cursor: pointer;
    }
    
    .btn-checkout:hover {
        background: #3651d4;
    }
    
    .btn-checkout:disabled {
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
<div class="cart-container">
    <div class="container">
        @include('user.components.alert')
        
        @if(isset($keranjangItems) && count($keranjangItems) > 0)
        <div class="row gx-5">
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
                        <input class="form-check-input item-checkbox" 
                               type="checkbox" 
                               name="selected_items[]" 
                               value="{{ $item['id'] }}"
                               data-item-data="{{ json_encode($item) }}" />

                        @if(isset($item['item']['gambar']) && $item['item']['gambar'])
                            <img src="{{ asset('storage/' . $item['item']['gambar']) }}" 
                                 class="item-image" 
                                 alt="{{ $item['item']['nama_item'] ?? 'Produk' }}">
                        @else
                            <img src="{{ asset('images/products/default.png') }}" 
                                 class="item-image" 
                                 alt="{{ $item['item']['nama_item'] ?? 'Produk' }}">
                        @endif

                        <div class="item-details">
                            <div class="item-name">{{ $item['item']['nama_item'] ?? 'Nama Produk' }}</div>
                            <div class="item-specs">
                                {{ $item['bahan']['nama_bahan'] ?? '-' }} • 
                                {{ $item['ukuran']['size'] ?? '-' }} • 
                                {{ $item['jenis']['kategori'] ?? '-' }}
                            </div>
                            <div class="mb-2">
                                @if($item['tipe_desain'] === 'dibuatkan')
                                    <span class="item-status status-dibuatkan">Dibuatkan</span>
                                @elseif(isset($item['upload_desain']) && $item['upload_desain'])
                                    <span class="item-status status-uploaded">Desain Sendiri</span>
                                @else
                                    <span class="item-status status-pending">Perlu Upload</span>
                                @endif
                            </div>
                            <div class="item-actions">
                                @if($item['tipe_desain'] === 'sendiri')
                                    @if(isset($item['upload_desain']) && $item['upload_desain'])
                                        <form method="POST" 
                                              action="{{ route('keranjang.upload-design', $item['id']) }}" 
                                              enctype="multipart/form-data" 
                                              style="display: inline;">
                                            @csrf
                                            <input type="file" 
                                                   name="upload_desain" 
                                                   id="design{{ $item['id'] }}" 
                                                   style="display: none;" 
                                                   accept=".jpeg,.png,.jpg,.pdf,.ai,.psd" 
                                                   onchange="this.form.submit()" />
                                            <label for="design{{ $item['id'] }}" class="btn-small btn-upload">Ganti</label>
                                        </form>
                                    @else
                                        <form method="POST" 
                                              action="{{ route('keranjang.upload-design', $item['id']) }}" 
                                              enctype="multipart/form-data" 
                                              style="display: inline;">
                                            @csrf
                                            <input type="file" 
                                                   name="upload_desain" 
                                                   id="design{{ $item['id'] }}" 
                                                   style="display: none;" 
                                                   accept=".jpeg,.png,.jpg,.pdf,.ai,.psd" 
                                                   onchange="this.form.submit()" />
                                            <label for="design{{ $item['id'] }}" class="btn-small btn-upload">Upload</label>
                                        </form>
                                    @endif
                                @endif
                                <form method="POST" 
                                      action="{{ route('keranjang.remove', $item['id']) }}" 
                                      style="display: inline;" 
                                      onsubmit="return confirm('Hapus item ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-small btn-delete">Hapus</button>
                                </form>
                            </div>
                        </div>

                        <div class="item-price-section">
                            <div class="item-price">Rp {{ number_format($item['total_harga'] ?? 0, 0, ',', '.') }}</div>
                            <div class="quantity-control" 
                                 data-item-id="{{ $item['id'] }}" 
                                 data-unit-price="{{ ($item['total_harga'] ?? 0) / max(1, $item['jumlah'] ?? 1) }}">
                                <button type="button" 
                                        class="btn-decrease" 
                                        {{ ($item['jumlah'] ?? 1) <= 1 ? 'disabled' : '' }}>−</button>
                                <input type="text" 
                                       class="quantity-input" 
                                       value="{{ $item['jumlah'] ?? 1 }}" 
                                       readonly />
                                <button type="button" class="btn-increase">+</button>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <div class="col-lg-4">
                <div class="summary-card" role="region" aria-label="Ringkasan belanja">
                    <h5 class="fw-semibold mb-5">Ringkasan Belanja</h5>
                    <div id="summary-items" aria-live="polite">
                        <!-- Dynamic content -->
                    </div>
                    <div class="summary-row" id="biaya-desain-row" style="display: none;">
                        <span>Biaya Desain</span>
                        <span id="biaya-desain-amount">Rp 0</span>
                    </div>
                    <div class="summary-row total">
                        <span>Total</span>
                        <span id="grand-total">Rp 0</span>
                    </div>
                    <button type="button" 
                            class="btn-checkout" 
                            id="checkout-btn" 
                            onclick="proceedToCheckout()" 
                            disabled
                            aria-disabled="true" 
                            aria-live="polite"
                            >
                        Checkout (<span id="selected-count">0</span> item)
                    </button>
                    <button type="button" 
                            class="btn-clear" 
                            onclick="clearCart()"
                            aria-label="Kosongkan keranjang">
                        Kosongkan Keranjang
                    </button>
                </div>
            </div>
        </div>
        @else
        <div class="empty-cart" role="alert" aria-live="polite">
            <img src="{{ asset('images/empty-cart.png') }}" alt="Keranjang Kosong" />
            <h4>Keranjang Masih Kosong</h4>
            <p>Yuk mulai belanja dan temukan produk favoritmu!</p>
            <a href="{{ route('welcome') }}" class="btn-shop" role="button">Mulai Belanja</a>
        </div>
        @endif
    </div>
</div>

@if(isset($keranjangItems) && count($keranjangItems) > 0)
<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectAllCheckbox = document.getElementById('selectAll');
    const itemCheckboxes = document.querySelectorAll('input[name="selected_items[]"]');
    const quantityControls = document.querySelectorAll('.quantity-control');
    const biayaDesainPerItem = {{ $summary['biaya_desain'] ?? 0 }};
    const checkoutBtn = document.getElementById('checkout-btn');

    updateCartSummary();
    
    // Checkbox handlers
    itemCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            updateCartSummary();
        });
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
            const currentJumlah = parseInt(quantityInput.value) || 1;
            if (currentJumlah > 1) updateJumlah(itemId, currentJumlah - 1);
        });
        
        increaseBtn.addEventListener('click', () => {
            const currentJumlah = parseInt(quantityInput.value) || 1;
            updateJumlah(itemId, currentJumlah + 1);
        });
    });

    function updateJumlah(itemId, newJumlah) {
        fetch(`/keranjang/${itemId}/quantity`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ jumlah: newJumlah })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const quantityInput = document.querySelector(`.quantity-control[data-item-id="${itemId}"] .quantity-input`);
                quantityInput.value = newJumlah;
                
                const unitPrice = parseFloat(document.querySelector(`.quantity-control[data-item-id="${itemId}"]`).dataset.unitPrice);
                const newTotalPrice = unitPrice * newJumlah;
                const priceElement = document.querySelector(`[data-item-id="${itemId}"] .item-price`);
                priceElement.textContent = `Rp ${newTotalPrice.toLocaleString('id-ID')}`;
                
                const control = document.querySelector(`.quantity-control[data-item-id="${itemId}"]`);
                control.querySelector('.btn-decrease').disabled = newJumlah <= 1;
                
                // Update data item untuk checkout
                const checkbox = document.querySelector(`input[name="selected_items[]"][value="${itemId}"]`);
                if (checkbox) {
                    const itemData = JSON.parse(checkbox.dataset.itemData);
                    itemData.jumlah = newJumlah;
                    itemData.total_harga = newTotalPrice;
                    checkbox.dataset.itemData = JSON.stringify(itemData);
                }

                updateCartSummary();
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat mengupdate jumlah');
        });
    }

    function updateCartSummary() {
        let totalSelectedItems = 0;
        let subtotal = 0;
        let countDibuatkan = 0;
        const summaryHtml = [];
        const uniqueDesignKeys = [];

        const checkedBoxes = document.querySelectorAll('input[name="selected_items[]"]:checked');

        checkedBoxes.forEach(checkbox => {
            const cartItem = checkbox.closest('.cart-item');
            const itemName = cartItem.querySelector('.item-name').textContent;
            const quantityInput = cartItem.querySelector('.quantity-input');
            const priceElement = cartItem.querySelector('.item-price');

            const jumlah = parseInt(quantityInput.value) || 0;
            const price = parseFloat(priceElement.textContent.replace(/[^\d]/g, '')) || 0;

            totalSelectedItems += jumlah;
            subtotal += price;

            // Cek tipe_desain dibuatkan
            const statusElement = cartItem.querySelector('.item-status');
            if (statusElement && statusElement.classList.contains('status-dibuatkan')) {
                const itemId = cartItem.dataset.itemId;
                const key = `${itemId}-dibuatkan`;
                if (!uniqueDesignKeys.includes(key)) {
                    countDibuatkan++;
                    uniqueDesignKeys.push(key);
                }
            }

            summaryHtml.push(`
                <div class="summary-row">
                    <span>${itemName} (${jumlah}x)</span>
                    <span>Rp ${price.toLocaleString('id-ID')}</span>
                </div>
            `);
        });

        document.getElementById('summary-items').innerHTML = summaryHtml.join('');

        const totalBiayaDesain = countDibuatkan * biayaDesainPerItem;
        const biayaDesainRow = document.getElementById('biaya-desain-row');
        const biayaDesainAmount = document.getElementById('biaya-desain-amount');

        if (totalBiayaDesain > 0) {
            biayaDesainRow.style.display = 'flex';
            biayaDesainAmount.textContent = `Rp ${totalBiayaDesain.toLocaleString('id-ID')}`;
        } else {
            biayaDesainRow.style.display = 'none';
        }

        const grandTotal = subtotal + totalBiayaDesain;
        document.getElementById('grand-total').textContent = `Rp ${grandTotal.toLocaleString('id-ID')}`;
        document.getElementById('selected-count').textContent = totalSelectedItems;
        document.getElementById('total-items').textContent = itemCheckboxes.length;

        // Update checkout button state
        checkoutBtn.disabled = totalSelectedItems === 0;
        checkoutBtn.setAttribute('aria-disabled', totalSelectedItems === 0);

        // Update select all checkbox state
        const checkedCount = checkedBoxes.length;
        selectAllCheckbox.checked = checkedCount === itemCheckboxes.length && itemCheckboxes.length > 0;
        selectAllCheckbox.indeterminate = checkedCount > 0 && checkedCount < itemCheckboxes.length;
    }

    window.proceedToCheckout = function() {
        const checkedBoxes = document.querySelectorAll('input[name="selected_items[]"]:checked');
        
        if (checkedBoxes.length === 0) {
            alert('Pilih minimal satu item untuk checkout');
            return;
        }

        // Validasi item perlu upload desain
        let hasUnuploadedDesign = false;
        let unuploadedItems = [];
        
        checkedBoxes.forEach(checkbox => {
            const cartItem = checkbox.closest('.cart-item');
            const statusElement = cartItem.querySelector('.item-status');
            const itemName = cartItem.querySelector('.item-name').textContent;

            if (statusElement && statusElement.classList.contains('status-pending')) {
                hasUnuploadedDesign = true;
                unuploadedItems.push(itemName);
            }
        });

        if (hasUnuploadedDesign) {
            alert(`Ada item yang masih perlu upload desain:\n- ${unuploadedItems.join('\n- ')}\n\nSilakan lengkapi terlebih dahulu.`);
            return;
        }

        // Buat form dan submit
        const checkoutForm = document.createElement('form');
        checkoutForm.method = 'POST';
        checkoutForm.action = '{{ route("checkout.terpilih") }}';
        
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = document.querySelector('meta[name="csrf-token"]').content;
        checkoutForm.appendChild(csrfToken);

        checkedBoxes.forEach(checkbox => {
            const hiddenInput = document.createElement('input');
            hiddenInput.type = 'hidden';
            hiddenInput.name = 'selected_items[]';
            hiddenInput.value = checkbox.value;
            checkoutForm.appendChild(hiddenInput);
        });

        document.body.appendChild(checkoutForm);
        checkoutForm.submit();
    };

    window.clearCart = function() {
        if (confirm('Kosongkan keranjang?')) {
            const clearForm = document.createElement('form');
            clearForm.method = 'POST';
            clearForm.action = '{{ route("keranjang.clear") }}';
            
            const csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = '_token';
            csrfToken.value = document.querySelector('meta[name="csrf-token"]').content;
            clearForm.appendChild(csrfToken);
            
            const methodInput = document.createElement('input');
            methodInput.type = 'hidden';
            methodInput.name = '_method';
            methodInput.value = 'DELETE';
            clearForm.appendChild(methodInput);
            
            document.body.appendChild(clearForm);
            clearForm.submit();
        }
    };
});
</script>
@endif
@endsection

