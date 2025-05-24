@extends('user.layouts.app')

@section('title')
Detail Produk - {{ $item['nama_item'] }}
@endsection

@section('custom-css')
<style>
    .product-image {
        border-radius: 8px;
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
    
    .product-title {
        color: #333;
        font-weight: 600;
    }
    
    .product-price {
        color: #4361ee;
        font-weight: 700;
        font-size: 1.5rem;
    }
    
    .rating-stars {
        color: #ffc107;
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
        background: #f8f9fa;
        width: 40px;
        height: 40px;
        font-size: 18px;
        font-weight: bold;
        transition: background-color 0.2s;
    }
    
    .quantity-control button:hover {
        background: #e9ecef;
    }
    
    .quantity-control input {
        width: 60px;
        text-align: center;
        border: none;
        border-left: 1px solid #e0e0e0;
        border-right: 1px solid #e0e0e0;
        font-weight: 500;
        height: 40px;
    }
    
    .btn-add-cart {
        background-color: #4361ee;
        border-color: #4361ee;
        padding: 12px 30px;
        font-weight: 600;
        font-size: 16px;
    }
    
    .btn-add-cart:hover {
        background-color: #3651d4;
        border-color: #3651d4;
    }
    
    .product-meta {
        background-color: #f8f9fa;
        border-radius: 8px;
        padding: 20px;
    }
    
    .breadcrumb {
        background-color: #f8f9fa;
        padding: 15px 0;
    }
    
    .breadcrumb-item a {
        color: #6c757d;
        text-decoration: none;
    }
    
    .breadcrumb-item a:hover {
        color: #4361ee;
    }
    
    .breadcrumb-item.active {
        color: #333;
    }
    
    .product-section {
        padding: 40px 0;
    }
    
    .form-select, .form-control {
        border-radius: 6px;
        border: 1px solid #e0e0e0;
        padding: 10px 15px;
    }
    
    .form-select:focus, .form-control:focus {
        border-color: #4361ee;
        box-shadow: 0 0 0 0.2rem rgba(67, 97, 238, 0.25);
    }
    
    .price-breakdown {
        background-color: #f8f9fa;
        border-radius: 6px;
        padding: 15px;
        margin-top: 20px;
    }
    
    .upload-area {
        border: 2px dashed #ddd;
        border-radius: 8px;
        padding: 20px;
        text-align: center;
        margin-top: 20px;
        transition: border-color 0.3s;
    }
    
    .upload-area:hover {
        border-color: #4361ee;
    }
    
    .upload-area.dragover {
        border-color: #4361ee;
        background-color: #f0f4ff;
    }
</style>
@endsection

@section('content')
<!-- Breadcrumb -->
<div class="breadcrumb">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="/">Home</a></li>
                <li class="breadcrumb-item"><a href="#" onclick="history.back()">Produk</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ $item['nama_item'] }}</li>
            </ol>
        </nav>
    </div>
</div>

<!-- Product Detail Section -->
<div class="product-section">
    <div class="container">
        <div class="row">
            <!-- Product Image -->
            <div class="col-lg-6 mb-4">
                <div class="text-center">
                    @if(isset($item['gambar']) && $item['gambar'])
                        <img src="{{ asset('storage/' . $item['gambar']) }}" 
                             alt="{{ $item['nama_item'] }}" 
                             class="img-fluid product-image"
                             style="max-height: 500px; object-fit: contain;">
                    @else
                        <img src="{{ asset('images/products/default.png') }}" 
                             alt="{{ $item['nama_item'] }}" 
                             class="img-fluid product-image"
                             style="max-height: 500px; object-fit: contain;">
                    @endif
                </div>
            </div>

            <!-- Product Details -->
            <div class="col-lg-6">
                <h1 class="product-title mb-3">{{ $item['nama_item'] }}</h1>
                <div class="product-price mb-3" id="currentPrice">
                    Rp {{ number_format($item['harga_dasar'], 0, ',', '.') }}
                </div>

                <!-- Rating (Static for now) -->
                <div class="d-flex align-items-center mb-3">
                    <div class="rating-stars me-2">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                    </div>
                    <span class="text-muted">(4.8/5 - 24 Reviews)</span>
                </div>

                <!-- Description -->
                @if($item['deskripsi'])
                <p class="text-muted mb-4">{{ $item['deskripsi'] }}</p>
                @endif

                <!-- Size Selection -->
                @if(count($ukurans) > 0)
                <div class="mb-3">
                    <label class="form-label fw-semibold">Ukuran</label>
                    <select class="form-select" id="ukuranSelect" required>
                        <option value="">Pilih Ukuran</option>
                        @foreach($ukurans as $key => $ukuran)
                            <option value="{{ $ukuran['id'] }}" 
                                    data-price="{{ $ukuran['biaya_tambahan'] }}"
                                    {{ $key === 0 ? 'selected' : '' }}>
                                {{ $ukuran['size'] }} 
                                @if($ukuran['biaya_tambahan'] > 0)
                                    (+Rp {{ number_format($ukuran['biaya_tambahan'], 0, ',', '.') }})
                                @endif
                            </option>
                        @endforeach
                    </select>
                </div>
                @endif

                <!-- Material Selection -->
                @if(count($bahans) > 0)
                <div class="mb-3">
                    <label class="form-label fw-semibold">Bahan</label>
                    <select class="form-select" id="bahanSelect" required>
                        <option value="">Pilih Bahan</option>
                        @foreach($bahans as $key => $bahan)
                            <option value="{{ $bahan['id'] }}" 
                                    data-price="{{ $bahan['biaya_tambahan'] }}"
                                    {{ $key === 0 ? 'selected' : '' }}>
                                {{ $bahan['nama_bahan'] }} 
                                @if($bahan['biaya_tambahan'] > 0)
                                    (+Rp {{ number_format($bahan['biaya_tambahan'], 0, ',', '.') }})
                                @endif
                            </option>
                        @endforeach
                    </select>
                </div>
                @endif

                <!-- Type Selection -->
                @if(count($jenis) > 0)
                <div class="mb-4">
                    <label class="form-label fw-semibold">Jenis</label>
                    <select class="form-select" id="jenisSelect" required>
                        <option value="">Pilih Jenis</option>
                        @foreach($jenis as $key => $jenis_item)
                            <option value="{{ $jenis_item['id'] }}" 
                                    data-price="{{ $jenis_item['biaya_tambahan'] }}"
                                    {{ $key === 0 ? 'selected' : '' }}>
                                {{ $jenis_item['kategori'] }} 
                                @if($jenis_item['biaya_tambahan'] > 0)
                                    (+Rp {{ number_format($jenis_item['biaya_tambahan'], 0, ',', '.') }})
                                @endif
                            </option>
                        @endforeach
                    </select>
                </div>
                @endif

                <!-- Upload Design Area -->
                <div class="mb-4">
                    <label class="form-label fw-semibold">Upload Desain (Opsional)</label>
                    <div class="upload-area" id="uploadArea">
                        <i class="fas fa-cloud-upload-alt fa-2x text-muted mb-2"></i>
                        <p class="mb-2">Klik untuk upload atau drag & drop file desain</p>
                        <small class="text-muted">Format: JPG, PNG, PDF (Max 5MB)</small>
                        <input type="file" id="designFile" accept=".jpg,.jpeg,.png,.pdf" style="display: none;">
                    </div>
                    <div id="uploadStatus" class="mt-2" style="display: none;"></div>
                </div>

                <!-- Price Breakdown -->
                <div class="price-breakdown">
                    <h6 class="fw-semibold mb-2">Rincian Harga:</h6>
                    <div class="d-flex justify-content-between">
                        <span>Harga Dasar:</span>
                        <span id="basePrice">Rp {{ number_format($item['harga_dasar'], 0, ',', '.') }}</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span>Biaya Ukuran:</span>
                        <span id="ukuranPrice">Rp 0</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span>Biaya Bahan:</span>
                        <span id="bahanPrice">Rp 0</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span>Biaya Jenis:</span>
                        <span id="jenisPrice">Rp 0</span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between fw-bold">
                        <span>Total:</span>
                        <span id="totalPrice">Rp {{ number_format($item['harga_dasar'], 0, ',', '.') }}</span>
                    </div>
                </div>

                <!-- Quantity and Add to Cart -->
                <div class="d-flex align-items-center gap-3 mb-4 mt-4">
                    <div class="quantity-control">
                        <button type="button" class="btn-decrease">-</button>
                        <input type="text" value="1" readonly class="quantity-input">
                        <button type="button" class="btn-increase">+</button>
                    </div>
                    <button class="btn btn-primary btn-add-cart flex-grow-1">
                        <i class="fas fa-shopping-cart me-2"></i>
                        Tambah ke Keranjang
                    </button>
                </div>

                <!-- Product Meta -->
                <div class="product-meta">
                    <div class="row">
                        <div class="col-sm-6 mb-2">
                            <strong>SKU:</strong> 
                            <span class="text-muted">{{ strtoupper(substr($item['nama_item'], 0, 3)) }}-{{ str_pad($item['id'], 3, '0', STR_PAD_LEFT) }}</span>
                        </div>
                        <div class="col-sm-6 mb-2">
                            <strong>Estimasi:</strong> 
                            <span class="text-muted">1-3 Hari Kerja</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Login Modal -->
<div class="modal fade" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="loginModalLabel">Silakan Login</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <i class="fas fa-user-circle fa-3x text-primary mb-3"></i>
                <p class="text-muted mb-4">Untuk menambahkan produk ke keranjang, Anda harus login terlebih dahulu.</p>
            </div>
            <div class="modal-footer justify-content-center">
                <a href="{{ route('login') }}" class="btn btn-primary px-4">Login</a>
                <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">Batal</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Data produk
    const basePrice = {{ $item['harga_dasar'] }};
    const isLoggedIn = {{ $user ? 'true' : 'false' }};
    let uploadedFile = null;
    
    // Quantity controls
    let quantity = 1;
    const quantityInput = document.querySelector('.quantity-input');
    const decreaseBtn = document.querySelector('.btn-decrease');
    const increaseBtn = document.querySelector('.btn-increase');

    decreaseBtn.addEventListener('click', function() {
        if (quantity > 1) {
            quantity--;
            quantityInput.value = quantity;
        }
    });

    increaseBtn.addEventListener('click', function() {
        quantity++;
        quantityInput.value = quantity;
    });

    // Price calculation
    function updatePrice() {
        const ukuranSelect = document.getElementById('ukuranSelect');
        const bahanSelect = document.getElementById('bahanSelect');
        const jenisSelect = document.getElementById('jenisSelect');
        
        let ukuranPrice = 0;
        let bahanPrice = 0;
        let jenisPrice = 0;
        
        if (ukuranSelect && ukuranSelect.selectedOptions[0]) {
            ukuranPrice = parseInt(ukuranSelect.selectedOptions[0].dataset.price) || 0;
        }
        
        if (bahanSelect && bahanSelect.selectedOptions[0]) {
            bahanPrice = parseInt(bahanSelect.selectedOptions[0].dataset.price) || 0;
        }
        
        if (jenisSelect && jenisSelect.selectedOptions[0]) {
            jenisPrice = parseInt(jenisSelect.selectedOptions[0].dataset.price) || 0;
        }
        
        const totalPrice = basePrice + ukuranPrice + bahanPrice + jenisPrice;
        
        // Update display
        document.getElementById('ukuranPrice').textContent = 'Rp ' + ukuranPrice.toLocaleString('id-ID');
        document.getElementById('bahanPrice').textContent = 'Rp ' + bahanPrice.toLocaleString('id-ID');
        document.getElementById('jenisPrice').textContent = 'Rp ' + jenisPrice.toLocaleString('id-ID');
        document.getElementById('totalPrice').textContent = 'Rp ' + totalPrice.toLocaleString('id-ID');
        document.getElementById('currentPrice').textContent = 'Rp ' + totalPrice.toLocaleString('id-ID');
    }

    // Add event listeners to select elements
    ['ukuranSelect', 'bahanSelect', 'jenisSelect'].forEach(id => {
        const element = document.getElementById(id);
        if (element) {
            element.addEventListener('change', updatePrice);
        }
    });

    // File upload handling
    const uploadArea = document.getElementById('uploadArea');
    const fileInput = document.getElementById('designFile');
    const uploadStatus = document.getElementById('uploadStatus');

    uploadArea.addEventListener('click', () => fileInput.click());

    uploadArea.addEventListener('dragover', (e) => {
        e.preventDefault();
        uploadArea.classList.add('dragover');
    });

    uploadArea.addEventListener('dragleave', () => {
        uploadArea.classList.remove('dragover');
    });

    uploadArea.addEventListener('drop', (e) => {
        e.preventDefault();
        uploadArea.classList.remove('dragover');
        const files = e.dataTransfer.files;
        if (files.length > 0) {
            handleFileUpload(files[0]);
        }
    });

    fileInput.addEventListener('change', (e) => {
        if (e.target.files.length > 0) {
            handleFileUpload(e.target.files[0]);
        }
    });

    function handleFileUpload(file) {
        // Validate file
        const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'application/pdf'];
        const maxSize = 5 * 1024 * 1024; // 5MB

        if (!allowedTypes.includes(file.type)) {
            uploadStatus.innerHTML = '<small class="text-danger">Format file tidak didukung. Gunakan JPG, PNG, atau PDF.</small>';
            uploadStatus.style.display = 'block';
            return;
        }

        if (file.size > maxSize) {
            uploadStatus.innerHTML = '<small class="text-danger">Ukuran file terlalu besar. Maksimal 5MB.</small>';
            uploadStatus.style.display = 'block';
            return;
        }

        // Store file temporarily (not uploaded to server yet)
        uploadedFile = file;
        uploadStatus.innerHTML = `<small class="text-success"><i class="fas fa-check"></i> File "${file.name}" siap diupload.</small>`;
        uploadStatus.style.display = 'block';
    }

    // Add to cart functionality
    document.querySelector('.btn-add-cart').addEventListener('click', function() {
        if (!isLoggedIn) {
            const loginModal = new bootstrap.Modal(document.getElementById('loginModal'));
            loginModal.show();
            return;
        }

        // Get selected options
        const ukuranSelect = document.getElementById('ukuranSelect');
        const bahanSelect = document.getElementById('bahanSelect');
        const jenisSelect = document.getElementById('jenisSelect');

        // Validate selections
        if (!ukuranSelect.value || !bahanSelect.value || !jenisSelect.value) {
            alert('Mohon pilih ukuran, bahan, dan jenis terlebih dahulu.');
            return;
        }

        // Prepare data
        const cartData = {
            item_id: {{ $item['id'] }},
            ukuran_id: ukuranSelect.value,
            bahan_id: bahanSelect.value,
            jenis_id: jenisSelect.value,
            quantity: quantity,
            upload_file: uploadedFile
        };

        // Show success message for now
        const ukuranText = ukuranSelect.selectedOptions[0].text;
        const bahanText = bahanSelect.selectedOptions[0].text;
        const jenisText = jenisSelect.selectedOptions[0].text;
        
        alert(`Berhasil menambahkan ${quantity} produk ke keranjang!\n\nDetail:\n- Ukuran: ${ukuranText}\n- Bahan: ${bahanText}\n- Jenis: ${jenisText}${uploadedFile ? '\n- Desain: ' + uploadedFile.name : ''}`);
    });

    // Initialize price calculation
    updatePrice();
});
</script>
@endsection