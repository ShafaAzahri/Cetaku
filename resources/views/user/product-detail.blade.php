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

    .design-option { border: 2px solid #e5e5e5; border-radius: 8px; padding: 20px; margin-bottom: 15px; cursor: pointer; transition: all 0.3s; position: relative; }
    .design-option input[type="radio"] { display: none; }
    .design-option input[type="radio"]:checked + .design-content { border-color: #4361ee; background-color: #f8f9ff; }
    .design-content { border: 2px solid transparent; border-radius: 6px; padding: 15px; transition: all 0.3s; }
    .design-option h6 { margin-bottom: 5px; font-weight: 600; }
    .design-option p { margin: 0; color: #666; font-size: 14px; }
    .design-price { color: #28a745; font-weight: 500; }
    .design-extra { color: #ffc107; font-weight: 500; }

    .upload-area { border: 2px dashed #ddd; border-radius: 8px; padding: 30px; text-align: center; margin-top: 15px; background-color: #f9f9f9; transition: all 0.3s; }
    .upload-area.dragover { border-color: #4361ee; background-color: #f0f4ff; }

    .quantity-control { display: flex; align-items: center; border: 1px solid #ddd; border-radius: 6px; overflow: hidden; }
    .quantity-control button { border: none; background: #f8f9fa; width: 40px; height: 40px; font-size: 18px; cursor: pointer; transition: background 0.2s; }
    .quantity-control button:hover { background: #e9ecef; }
    .quantity-control button:disabled { background: #f8f9fa; color: #ccc; cursor: not-allowed; }
    .quantity-control input { width: 60px; text-align: center; border: none; height: 40px; }

    .btn-add-cart { background-color: #4361ee; border-color: #4361ee; padding: 12px 30px; font-weight: 600; border-radius: 6px; transition: all 0.3s; }
    .btn-add-cart:hover { background-color: #3651d4; transform: translateY(-1px); }
    .btn-add-cart:disabled { background-color: #ccc; border-color: #ccc; cursor: not-allowed; }

    .price-info { background: #f8f9fa; border-radius: 8px; padding: 15px; margin-top: 20px; font-size: 14px; border-left: 4px solid #4361ee; }
    .price-info strong { color: #4361ee; }

    .loading-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(255, 255, 255, 0.8); display: none; justify-content: center; align-items: center; z-index: 9999; }
    .spinner-border { width: 3rem; height: 3rem; }

    .validation-error { color: #dc3545; font-size: 0.875rem; margin-top: 0.25rem; }

    .form-group { margin-bottom: 1rem; }
    .form-group.has-error .form-select, .form-group.has-error .form-control { border-color: #dc3545; }
</style>
@endsection

@section('content')
<!-- Loading Overlay -->
<div class="loading-overlay" id="loadingOverlay">
    <div class="spinner-border text-primary" role="status">
        <span class="visually-hidden">Loading...</span>
    </div>
</div>

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
        <div class="alert alert-info">
            <i class="fas fa-info-circle me-2"></i>
            <strong>Info:</strong> Silakan <a href="{{ route('login') }}" class="alert-link">login</a> untuk menambahkan produk ke keranjang.
        </div>
        @endif

        <!-- Alert Component - Consistent with Profile Page -->
        @include('user.components.alert')

        <form id="addToCartForm" action="{{ route('keranjang.add') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="item_id" value="{{ $item['id'] }}">
            <input type="hidden" name="harga_satuan" id="hargaSatuanInput" value="{{ $item['harga_dasar'] }}">
            <input type="hidden" name="total_harga" id="totalHargaInput" value="{{ $item['harga_dasar'] }}">

            <div class="row">
                <!-- Product Image -->
                <div class="col-lg-6 mb-4 text-center">
                    <img src="{{ isset($item['gambar']) && $item['gambar'] ? asset('storage/' . $item['gambar']) : asset('images/products/default.png') }}" alt="{{ $item['nama_item'] }}" class="img-fluid product-image">
                </div>

                <!-- Product Details -->
                <div class="col-lg-6">
                    <h1 class="product-title">{{ $item['nama_item'] }}</h1>
                    <div class="product-price">Rp {{ number_format($item['harga_dasar'], 0, ',', '.') }}</div>

                    @if($item['deskripsi'])
                    <p class="text-muted mb-4">{{ $item['deskripsi'] }}</p>
                    @endif

                    <!-- Ukuran Selection -->
                    @if(count($ukurans) > 0)
                    <div class="form-group">
                        <label class="form-label fw-semibold">Ukuran <span class="text-danger">*</span></label>
                        <select class="form-select" name="ukuran_id" id="ukuranSelect" required>
                            <option value="">Pilih Ukuran</option>
                            @foreach($ukurans as $ukuran)
                            <option value="{{ $ukuran['id'] }}" data-price="{{ $ukuran['biaya_tambahan'] }}">
                                {{ $ukuran['size'] }} @if($ukuran['biaya_tambahan'] > 0) (+Rp {{ number_format($ukuran['biaya_tambahan'], 0, ',', '.') }}) @endif
                            </option>
                            @endforeach
                        </select>
                        <div class="validation-error" id="ukuranError"></div>
                    </div>
                    @endif

                    <!-- Bahan Selection -->
                    @if(count($bahans) > 0)
                    <div class="form-group">
                        <label class="form-label fw-semibold">Bahan <span class="text-danger">*</span></label>
                        <select class="form-select" name="bahan_id" id="bahanSelect" required>
                            <option value="">Pilih Bahan</option>
                            @foreach($bahans as $bahan)
                            <option value="{{ $bahan['id'] }}" data-price="{{ $bahan['biaya_tambahan'] }}">
                                {{ $bahan['nama_bahan'] }} @if($bahan['biaya_tambahan'] > 0) (+Rp {{ number_format($bahan['biaya_tambahan'], 0, ',', '.') }}) @endif
                            </option>
                            @endforeach
                        </select>
                        <div class="validation-error" id="bahanError"></div>
                    </div>
                    @endif

                    <!-- Jenis Selection -->
                    @if(count($jenis) > 0)
                    <div class="form-group">
                        <label class="form-label fw-semibold">Jenis <span class="text-danger">*</span></label>
                        <select class="form-select" name="jenis_id" id="jenisSelect" required>
                            <option value="">Pilih Jenis</option>
                            @foreach($jenis as $jenis_item)
                            <option value="{{ $jenis_item['id'] }}" data-price="{{ $jenis_item['biaya_tambahan'] }}">
                                {{ $jenis_item['kategori'] }} @if($jenis_item['biaya_tambahan'] > 0) (+Rp {{ number_format($jenis_item['biaya_tambahan'], 0, ',', '.') }}) @endif
                            </option>
                            @endforeach
                        </select>
                        <div class="validation-error" id="jenisError"></div>
                    </div>
                    @endif

                    <!-- Design Options -->
                    <div class="form-group">
                        <label class="form-label fw-semibold">Pilih Jenis Desain <span class="text-danger">*</span></label>
                        <div class="design-option">
                            <input type="radio" name="tipe_desain" value="sendiri" id="designSendiri" checked>
                            <label for="designSendiri" class="design-content w-100">
                                <h6>🎨 Upload Desain Sendiri</h6>
                                <p>Upload file desain Anda sendiri</p>
                                <div class="design-price">Gratis</div>
                            </label>
                        </div>
                        <div class="design-option">
                            <input type="radio" name="tipe_desain" value="dibuatkan" id="designDibuatkan">
                            <label for="designDibuatkan" class="design-content w-100">
                                <h6>🏪 Minta Dibuatkan Desain</h6>
                                <p>Tim desainer kami akan membuatkan desain untuk Anda</p>
                                <div class="design-extra">+Rp {{ number_format($biaya_desain, 0, ',', '.') }}</div>
                            </label>
                        </div>
                        <div class="validation-error" id="tipeDesainError"></div>
                    </div>

                    <!-- Upload Area -->
                    <div id="uploadArea" class="form-group">
                        <label class="form-label fw-semibold">Upload Desain <span class="text-danger">*</span></label>
                        <div class="upload-area" id="uploadDropArea">
                            <i class="fas fa-cloud-upload-alt fa-2x text-muted mb-2"></i>
                            <p class="mb-2">Pilih file desain Anda atau drag & drop di sini</p>
                            <input type="file" name="upload_desain" id="uploadDesainInput" class="form-control" accept=".jpg,.jpeg,.png,.pdf,.ai,.psd" style="display: none;">
                            <button type="button" class="btn btn-outline-primary btn-sm" onclick="document.getElementById('uploadDesainInput').click()">
                                <i class="fas fa-folder-open me-1"></i>Pilih File
                            </button>
                            <div id="selectedFileName" class="mt-2 text-success" style="display: none;"></div>
                            <small class="text-muted d-block mt-2">Format: JPG, PNG, PDF, AI, PSD (Max 10MB)</small>
                        </div>
                        <div class="validation-error" id="uploadDesainError"></div>
                    </div>

                    <!-- Quantity -->
                    <div class="form-group">
                        <label class="form-label fw-semibold">Jumlah</label>
                        <div class="quantity-control">
                            <button type="button" id="decreaseBtn" onclick="changeQuantity(-1)">-</button>
                            <input type="number" name="jumlah" id="jumlahInput" value="1" min="1" max="100" readonly>
                            <button type="button" id="increaseBtn" onclick="changeQuantity(1)">+</button>
                        </div>
                    </div>

                    <!-- Price Info -->
                    <div class="price-info">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Harga Produk:</span>
                            <span id="hargaProdukDisplay">Rp {{ number_format($item['harga_dasar'], 0, ',', '.') }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Biaya Tambahan:</span>
                            <span id="biayaTambahanDisplay">Rp 0</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Biaya Desain:</span>
                            <span id="designCostDisplay">Rp 0</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Harga Satuan:</span>
                            <span id="hargaSatuanDisplay">Rp {{ number_format($item['harga_dasar'], 0, ',', '.') }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Jumlah:</span>
                            <span id="jumlahDisplay">1</span>
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
                            <button type="submit" id="addToCartBtn" class="btn btn-primary btn-add-cart w-100">
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
// Constants
const BASE_PRICE = {{ $item['harga_dasar'] }};
const DESIGN_COST = {{ $biaya_desain }};
let currentPrices = { base: BASE_PRICE, ukuran: 0, bahan: 0, jenis: 0, design: 0 };

// DOM Elements
const form = document.getElementById('addToCartForm');
const loadingOverlay = document.getElementById('loadingOverlay');
const jumlahInput = document.getElementById('jumlahInput');
const addToCartBtn = document.getElementById('addToCartBtn');

// Initialize page
document.addEventListener('DOMContentLoaded', () => {
    initializeEventListeners();
    updateDesignUI();
    updateAllDisplays();
    setupFormValidation();
});

// Event Listeners
function initializeEventListeners() {
    ['ukuranSelect', 'bahanSelect', 'jenisSelect'].forEach(id => {
        const element = document.getElementById(id);
        if (element) element.addEventListener('change', updateAllDisplays);
    });
    document.querySelectorAll('input[name="tipe_desain"]').forEach(radio => {
        radio.addEventListener('change', () => { updateDesignUI(); updateAllDisplays(); });
    });
    const uploadInput = document.getElementById('uploadDesainInput');
    const uploadArea = document.getElementById('uploadDropArea');
    if (uploadInput && uploadArea) {
        uploadInput.addEventListener('change', handleFileSelect);
        uploadArea.addEventListener('dragover', handleDragOver);
        uploadArea.addEventListener('dragleave', handleDragLeave);
        uploadArea.addEventListener('drop', handleFileDrop);
    }
    if (form) form.addEventListener('submit', handleFormSubmit);
}

// Quantity control
function changeQuantity(delta) {
    const currentValue = parseInt(jumlahInput.value) || 1;
    const newValue = currentValue + delta;
    if (newValue >= 1 && newValue <= 100) {
        jumlahInput.value = newValue;
        updateAllDisplays();
        document.getElementById('decreaseBtn').disabled = newValue <= 1;
        document.getElementById('increaseBtn').disabled = newValue >= 100;
    }
}

// Update design UI
function updateDesignUI() {
    const uploadArea = document.getElementById('uploadArea');
    const designType = document.querySelector('input[name="tipe_desain"]:checked');
    if (designType && uploadArea) {
        if (designType.value === 'sendiri') {
            uploadArea.style.display = 'block';
            currentPrices.design = 0;
        } else {
            uploadArea.style.display = 'none';
            currentPrices.design = DESIGN_COST;
            const uploadInput = document.getElementById('uploadDesainInput');
            if (uploadInput) {
                uploadInput.value = '';
                hideFileName();
            }
        }
    }
}

// Update all price displays
function updateAllDisplays() {
    calculatePrices();
    updatePriceDisplays();
    updateHiddenInputs();
}

// Calculate all prices
function calculatePrices() {
    currentPrices.ukuran = 0;
    currentPrices.bahan = 0;
    currentPrices.jenis = 0;
    ['ukuranSelect', 'bahanSelect', 'jenisSelect'].forEach(id => {
        const element = document.getElementById(id);
        if (element && element.selectedOptions[0]) {
            const price = parseInt(element.selectedOptions[0].getAttribute('data-price')) || 0;
            const type = id.replace('Select', '');
            currentPrices[type] = price;
        }
    });
}

// Update price displays
function updatePriceDisplays() {
    const jumlah = parseInt(jumlahInput.value) || 1;
    const biayaTambahan = currentPrices.ukuran + currentPrices.bahan + currentPrices.jenis;
    const hargaSatuan = currentPrices.base + biayaTambahan;
    const totalHarga = (hargaSatuan + currentPrices.design) * jumlah;
    document.getElementById('hargaProdukDisplay').textContent = formatCurrency(currentPrices.base);
    document.getElementById('biayaTambahanDisplay').textContent = formatCurrency(biayaTambahan);
    document.getElementById('designCostDisplay').textContent = formatCurrency(currentPrices.design);
    document.getElementById('hargaSatuanDisplay').textContent = formatCurrency(hargaSatuan);
    document.getElementById('jumlahDisplay').textContent = jumlah;
    document.getElementById('totalPrice').textContent = formatCurrency(totalHarga);
}

// Update hidden inputs
function updateHiddenInputs() {
    const jumlah = parseInt(jumlahInput.value) || 1;
    const biayaTambahan = currentPrices.ukuran + currentPrices.bahan + currentPrices.jenis;
    const hargaSatuan = currentPrices.base + biayaTambahan;
    const totalHarga = (hargaSatuan + currentPrices.design) * jumlah;
    document.getElementById('hargaSatuanInput').value = hargaSatuan;
    document.getElementById('totalHargaInput').value = totalHarga;
}

// Format currency
function formatCurrency(amount) {
    return 'Rp ' + parseInt(amount).toLocaleString('id-ID');
}

// File handling
function handleFileSelect(e) {
    const file = e.target.files[0];
    if (file) {
        showFileName(file.name);
        clearError('uploadDesainError');
    }
}

function handleDragOver(e) {
    e.preventDefault();
    e.currentTarget.classList.add('dragover');
}

function handleDragLeave(e) {
    e.preventDefault();
    e.currentTarget.classList.remove('dragover');
}

function handleFileDrop(e) {
    e.preventDefault();
    e.currentTarget.classList.remove('dragover');
    const files = e.dataTransfer.files;
    if (files.length > 0) {
        const uploadInput = document.getElementById('uploadDesainInput');
        uploadInput.files = files;
        showFileName(files[0].name);
        clearError('uploadDesainError');
    }
}

function showFileName(fileName) {
    const fileNameDisplay = document.getElementById('selectedFileName');
    if (fileNameDisplay) {
        fileNameDisplay.innerHTML = `<i class="fas fa-file me-1"></i>${fileName}`;
        fileNameDisplay.style.display = 'block';
    }
}

function hideFileName() {
    const fileNameDisplay = document.getElementById('selectedFileName');
    if (fileNameDisplay) {
        fileNameDisplay.style.display = 'none';
    }
}

// Form validation
function setupFormValidation() {
    const requiredSelects = ['ukuranSelect', 'bahanSelect', 'jenisSelect'];
    requiredSelects.forEach(id => {
        const element = document.getElementById(id);
        if (element) {
            element.addEventListener('change', () => validateField(id));
            element.addEventListener('blur', () => validateField(id));
        }
    });
}

function validateField(fieldId) {
    const field = document.getElementById(fieldId);
    const errorId = fieldId.replace('Select', 'Error');
    if (field && !field.value) {
        showError(errorId, 'Field ini wajib dipilih');
        return false;
    } else {
        clearError(errorId);
        return true;
    }
}

function validateForm() {
    let isValid = true;
    const requiredSelects = ['ukuranSelect', 'bahanSelect', 'jenisSelect'];
    requiredSelects.forEach(id => {
        if (!validateField(id)) isValid = false;
    });
    
    const designType = document.querySelector('input[name="tipe_desain"]:checked');
    if (!designType) {
        showError('tipeDesainError', 'Pilih jenis desain');
        isValid = false;
    } else {
        clearError('tipeDesainError');
        if (designType.value === 'sendiri') {
            const uploadInput = document.getElementById('uploadDesainInput');
            if (!uploadInput.files || uploadInput.files.length === 0) {
                showError('uploadDesainError', 'File desain wajib diupload untuk desain sendiri');
                isValid = false;
            } else {
                clearError('uploadDesainError');
            }
        }
    }
    return isValid;
}

function showError(errorId, message) {
    const errorElement = document.getElementById(errorId);
    if (errorElement) {
        errorElement.textContent = message;
        const formGroup = errorElement.closest('.form-group');
        if (formGroup) formGroup.classList.add('has-error');
    }
}

function clearError(errorId) {
    const errorElement = document.getElementById(errorId);
    if (errorElement) {
        errorElement.textContent = '';
        const formGroup = errorElement.closest('.form-group');
        if (formGroup) formGroup.classList.remove('has-error');
    }
}

// Form submission - Updated to use standard form submission for Laravel session alerts
async function handleFormSubmit(e) {
    e.preventDefault();
    if (!validateForm()) {
        const firstError = document.querySelector('.validation-error:not(:empty)');
        if (firstError) firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
        return;
    }
    showLoading(true);
    addToCartBtn.disabled = true;
    
    // Submit form normally to let Laravel handle the response with session alerts
    form.submit();
}

function showLoading(show) {
    if (loadingOverlay) loadingOverlay.style.display = show ? 'flex' : 'none';
}
</script>
@endsection