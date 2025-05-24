@extends('user.layouts.app')

@section('title', 'Detail Produk - ' . $item['nama_item'])

@section('custom-css')
<style>
    .product-section { padding: 30px 0; }
    .product-image { border-radius: 8px; max-height: 400px; object-fit: contain; }
    .product-title { color: #333; font-weight: 600; margin-bottom: 15px; }
    .product-price { color: #4361ee; font-weight: 700; font-size: 1.8rem; margin-bottom: 20px; }
    
    .form-select, .form-control { border-radius: 6px; padding: 10px 15px; margin-bottom: 15px; }
    .form-select:focus, .form-control:focus { border-color: #4361ee; box-shadow: 0 0 0 0.2rem rgba(67, 97, 238, 0.25); }
    
    .design-option { 
        border: 2px solid #e5e5e5; 
        border-radius: 8px; 
        padding: 20px; 
        margin-bottom: 15px; 
        cursor: pointer; 
        transition: all 0.3s;
        position: relative;
    }
    .design-option input[type="radio"] { display: none; }
    .design-option input[type="radio"]:checked + .design-content { border-color: #4361ee; background-color: #f8f9ff; }
    .design-content { 
        border: 2px solid transparent; 
        border-radius: 6px; 
        padding: 15px; 
        transition: all 0.3s;
    }
    .design-option h6 { margin-bottom: 5px; font-weight: 600; }
    .design-option p { margin: 0; color: #666; font-size: 14px; }
    .design-price { color: #28a745; font-weight: 500; }
    .design-extra { color: #ffc107; font-weight: 500; }
    
    .upload-area { 
        border: 2px dashed #ddd; 
        border-radius: 8px; 
        padding: 30px; 
        text-align: center; 
        margin-top: 15px;
        background-color: #f9f9f9;
    }
    
    .quantity-control { 
        display: flex; 
        align-items: center; 
        border: 1px solid #ddd; 
        border-radius: 6px; 
        overflow: hidden; 
        width: fit-content; 
    }
    .quantity-control button { 
        border: none; 
        background: #f8f9fa; 
        width: 40px; 
        height: 40px; 
        font-size: 18px; 
        cursor: pointer; 
    }
    .quantity-control button:hover { background: #e9ecef; }
    .quantity-control input { width: 60px; text-align: center; border: none; height: 40px; }
    
    .btn-add-cart { 
        background-color: #4361ee; 
        border-color: #4361ee; 
        padding: 12px 30px; 
        font-weight: 600; 
        border-radius: 6px; 
    }
    .btn-add-cart:hover { background-color: #3651d4; }
    
    .price-info { 
        background: #f8f9fa; 
        border-radius: 8px; 
        padding: 15px; 
        margin-top: 20px;
        font-size: 14px;
    }
    .price-info strong { color: #4361ee; }
</style>
@endsection

@section('content')
<!-- Breadcrumb -->
<div class="bg-light py-3">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="/" class="text-decoration-none">Home</a></li>
                <li class="breadcrumb-item"><a href="#" onclick="history.back()" class="text-decoration-none">Produk</a></li>
                <li class="breadcrumb-item active">{{ $item['nama_item'] }}</li>
            </ol>
        </nav>
    </div>
</div>

<!-- Product Detail -->
<div class="product-section">
    <div class="container">
        @if(!$user)
        <!-- Login Alert -->
        <div class="alert alert-info">
            <i class="fas fa-info-circle me-2"></i>
            <strong>Info:</strong> Silakan <a href="{{ route('login') }}" class="alert-link">login</a> untuk menambahkan produk ke keranjang.
        </div>
        @endif

        <form action="{{ route('keranjang.add') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="item_id" value="{{ $item['id'] }}">
            
            <div class="row">
                <!-- Product Image -->
                <div class="col-lg-6 mb-4">
                    <div class="text-center">
                        <img src="{{ isset($item['gambar']) && $item['gambar'] ? asset('storage/' . $item['gambar']) : asset('images/products/default.png') }}" alt="{{ $item['nama_item'] }}" class="img-fluid product-image">
                    </div>
                </div>

                <!-- Product Details -->
                <div class="col-lg-6">
                    <h1 class="product-title">{{ $item['nama_item'] }}</h1>
                    <div class="product-price">Rp {{ number_format($item['harga_dasar'], 0, ',', '.') }}</div>
                    
                    @if($item['deskripsi'])
                    <p class="text-muted mb-4">{{ $item['deskripsi'] }}</p>
                    @endif

                    <!-- Selections -->
                    @if(count($ukurans) > 0)
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Ukuran <span class="text-danger">*</span></label>
                        <select class="form-select" name="ukuran_id" required onchange="updateTotalPrice()">
                            <option value="">Pilih Ukuran</option>
                            @foreach($ukurans as $ukuran)
                                <option value="{{ $ukuran['id'] }}" data-price="{{ $ukuran['biaya_tambahan'] }}">
                                    {{ $ukuran['size'] }} 
                                    @if($ukuran['biaya_tambahan'] > 0) 
                                        (+Rp {{ number_format($ukuran['biaya_tambahan'], 0, ',', '.') }}) 
                                    @endif
                                </option>
                            @endforeach
                        </select>
                    </div>
                    @endif

                    @if(count($bahans) > 0)
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Bahan <span class="text-danger">*</span></label>
                        <select class="form-select" name="bahan_id" required onchange="updateTotalPrice()">
                            <option value="">Pilih Bahan</option>
                            @foreach($bahans as $bahan)
                                <option value="{{ $bahan['id'] }}" data-price="{{ $bahan['biaya_tambahan'] }}">
                                    {{ $bahan['nama_bahan'] }} 
                                    @if($bahan['biaya_tambahan'] > 0) 
                                        (+Rp {{ number_format($bahan['biaya_tambahan'], 0, ',', '.') }}) 
                                    @endif
                                </option>
                            @endforeach
                        </select>
                    </div>
                    @endif

                    @if(count($jenis) > 0)
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Jenis <span class="text-danger">*</span></label>
                        <select class="form-select" name="jenis_id" required onchange="updateTotalPrice()">
                            <option value="">Pilih Jenis</option>
                            @foreach($jenis as $jenis_item)
                                <option value="{{ $jenis_item['id'] }}" data-price="{{ $jenis_item['biaya_tambahan'] }}">
                                    {{ $jenis_item['kategori'] }} 
                                    @if($jenis_item['biaya_tambahan'] > 0) 
                                        (+Rp {{ number_format($jenis_item['biaya_tambahan'], 0, ',', '.') }}) 
                                    @endif
                                </option>
                            @endforeach
                        </select>
                    </div>
                    @endif

                    <!-- Design Options -->
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Pilih Jenis Desain <span class="text-danger">*</span></label>
                        
                        <!-- Upload Sendiri -->
                        <div class="design-option">
                            <input type="radio" name="tipe_desain" value="sendiri" id="designSendiri" checked onchange="updateDesignUI()">
                            <label for="designSendiri" class="design-content w-100">
                                <h6>🎨 Upload Desain Sendiri</h6>
                                <p>Upload file desain Anda sendiri</p>
                                <div class="design-price">Gratis</div>
                            </label>
                        </div>
                        
                        <!-- Desain Toko -->
                        <div class="design-option">
                            <input type="radio" name="tipe_desain" value="toko" id="designToko" onchange="updateDesignUI()">
                            <label for="designToko" class="design-content w-100">
                                <h6>🏪 Minta Dibuatkan Desain</h6>
                                <p>Tim desainer kami akan membuatkan desain untuk Anda</p>
                                <div class="design-extra">+Rp {{ number_format($biaya_desain, 0, ',', '.') }}</div>
                            </label>
                        </div>
                    </div>

                    <!-- Upload Area -->
                    <div id="uploadArea" class="mb-4">
                        <label class="form-label fw-semibold">Upload Desain</label>
                        <div class="upload-area">
                            <i class="fas fa-cloud-upload-alt fa-2x text-muted mb-2"></i>
                            <p class="mb-2">Pilih file desain Anda</p>
                            <input type="file" name="upload_desain" class="form-control" accept=".jpg,.jpeg,.png,.pdf">
                            <small class="text-muted">Format: JPG, PNG, PDF (Max 5MB)</small>
                        </div>
                    </div>

                    <!-- Quantity -->
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Jumlah</label>
                        <div class="quantity-control">
                            <button type="button" onclick="changeQuantity(-1)">-</button>
                            <input type="number" name="quantity" value="1" min="1" max="100" readonly>
                            <button type="button" onclick="changeQuantity(1)">+</button>
                        </div>
                    </div>

                    <!-- Price Info -->
                    <div class="price-info">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Harga Produk:</span>
                            <span>Rp {{ number_format($item['harga_dasar'], 0, ',', '.') }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Biaya Desain:</span>
                            <span id="designCostDisplay">Rp 0</span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between">
                            <strong>Total Harga:</strong>
                            <strong id="totalPrice">Rp {{ number_format($item['harga_dasar'], 0, ',', '.') }}</strong>
                        </div>
                    </div>

                    <!-- Add to Cart Button -->
                    <div class="mt-4">
                        @if($user)
                            <button type="submit" class="btn btn-primary btn-add-cart w-100">
                                <i class="fas fa-shopping-cart me-2"></i>Tambah ke Keranjang
                            </button>
                        @else
                            <a href="{{ route('login') }}" class="btn btn-primary w-100">
                                <i class="fas fa-sign-in-alt me-2"></i>Login untuk Pesan
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
// Function to change the quantity
function changeQuantity(delta) {
    const input = document.querySelector('input[name="quantity"]');
    const currentValue = parseInt(input.value) || 1;
    const newValue = currentValue + delta;

    if (newValue >= 1 && newValue <= 100) {
        input.value = newValue;
        updateTotalPrice();  // Update price when quantity changes
    }
}

// Update design UI based on selected design type
function updateDesignUI() {
    const uploadArea = document.getElementById('uploadArea');
    const designCostDisplay = document.getElementById('designCostDisplay');
    const designType = document.querySelector('input[name="tipe_desain"]:checked').value;
    
    if (designType === 'sendiri') {
        uploadArea.style.display = 'block';
        designCostDisplay.textContent = 'Rp 0';
    } else {
        uploadArea.style.display = 'none';
        designCostDisplay.textContent = 'Rp {{ number_format($biaya_desain, 0, ',', '.') }}';
    }

    updateTotalPrice(); // Update total price immediately after design selection
}

// Update the total price based on selections
function updateTotalPrice() {
    let basePrice = {{ $item['harga_dasar'] }};
    let totalPrice = basePrice;

    const quantity = parseInt(document.querySelector('input[name="quantity"]').value) || 1;

    // Calculate additional costs
    document.querySelectorAll('select').forEach(select => {
        const price = parseInt(select.selectedOptions[0].getAttribute('data-price')) || 0;
        totalPrice += price;
    });

    // Add design cost if applicable
    const designType = document.querySelector('input[name="tipe_desain"]:checked').value;
    if (designType === 'toko') {
        totalPrice += {{ $biaya_desain }};
    }

    // Multiply by quantity
    totalPrice *= quantity;

    // Update price display
    document.getElementById('totalPrice').textContent = `Rp ${totalPrice.toLocaleString('id-ID')}`;
}

// Initialize UI on page load
document.addEventListener('DOMContentLoaded', function() {
    updateDesignUI();
    updateTotalPrice();
});
</script>
@endsection