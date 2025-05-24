@extends('user.layouts.app')

@section('title', 'Keranjang')

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
            <h4 class="mb-0">Keranjang Anda ({{ $summary['total_items'] }} item)</h4>
        </div>
        
        <!-- Select All Form -->
        <form method="GET" action="{{ route('keranjang') }}">
            <div class="cart-header">
                <div class="form-check select-all">
                    <input class="form-check-input" type="checkbox" id="selectAll" name="select_all" 
                           {{ request('select_all') ? 'checked' : '' }}
                           onchange="this.form.submit()">
                    <label class="form-check-label" for="selectAll">
                        Pilih Semua
                    </label>
                </div>
            </div>
        </form>
        
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
                                                       {{ $item['selected'] ?? false ? 'checked' : '' }}>
                                                <label class="form-check-label" for="item{{ $item['id'] }}"></label>
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <img src="{{ $item['item']['gambar'] ?? asset('images/default-product.png') }}" 
                                                 class="item-img" alt="{{ $item['item']['nama'] }}">
                                        </div>
                                        <div class="col">
                                            <div class="item-details">
                                                <h5>{{ $item['item']['nama'] }}</h5>
                                                <p>Bahan: {{ $item['bahan']['nama'] ?? '-' }}</p>
                                                <p>Ukuran: {{ $item['ukuran']['nama'] ?? '-' }}</p>
                                                <p>Jenis: {{ $item['jenis']['nama'] ?? '-' }}</p>
                                                <p>
                                                    Upload Desain: 
                                                    @if($item['upload_desain'])
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
                                                <div class="item-price">Rp {{ number_format($item['harga_total'], 0, ',', '.') }}</div>
                                                <div class="quantity-control">
                                                    <form method="POST" action="{{ route('keranjang.update-quantity', $item['id']) }}" 
                                                          style="display: inline;">
                                                        @csrf
                                                        @method('PUT')
                                                        <input type="hidden" name="quantity" value="{{ max(1, $item['quantity'] - 1) }}">
                                                        <button type="submit" {{ $item['quantity'] <= 1 ? 'disabled' : '' }}>-</button>
                                                    </form>
                                                    <input type="text" value="{{ $item['quantity'] }}" readonly>
                                                    <form method="POST" action="{{ route('keranjang.update-quantity', $item['id']) }}" 
                                                          style="display: inline;">
                                                        @csrf
                                                        @method('PUT')
                                                        <input type="hidden" name="quantity" value="{{ $item['quantity'] + 1 }}">
                                                        <button type="submit">+</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endforeach
                @else
                    @foreach($keranjangItems as $item)
                        <div class="cart-item mb-3">
                            <div class="row g-3">
                                <div class="col-auto">
                                    <div class="form-check pt-4">
                                        <input class="form-check-input" type="checkbox" 
                                               id="item{{ $item['id'] }}" 
                                               {{ $item['selected'] ?? false ? 'checked' : '' }}>
                                        <label class="form-check-label" for="item{{ $item['id'] }}"></label>
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <img src="{{ $item['item']['gambar'] ?? asset('images/default-product.png') }}" 
                                         class="item-img" alt="{{ $item['item']['nama'] }}">
                                </div>
                                <div class="col">
                                    <div class="item-details">
                                        <h5>{{ $item['item']['nama'] }}</h5>
                                        <p>Bahan: {{ $item['bahan']['nama'] ?? '-' }}</p>
                                        <p>Ukuran: {{ $item['ukuran']['nama'] ?? '-' }}</p>
                                        <p>Jenis: {{ $item['jenis']['nama'] ?? '-' }}</p>
                                        <p>
                                            Upload Desain: 
                                            @if($item['upload_desain'])
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
                                        <div class="item-price">Rp {{ number_format($item['harga_total'], 0, ',', '.') }}</div>
                                        <div class="quantity-control">
                                            <form method="POST" action="{{ route('keranjang.update-quantity', $item['id']) }}" 
                                                  style="display: inline;">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" name="quantity" value="{{ max(1, $item['quantity'] - 1) }}">
                                                <button type="submit" {{ $item['quantity'] <= 1 ? 'disabled' : '' }}>-</button>
                                            </form>
                                            <input type="text" value="{{ $item['quantity'] }}" readonly>
                                            <form method="POST" action="{{ route('keranjang.update-quantity', $item['id']) }}" 
                                                  style="display: inline;">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" name="quantity" value="{{ $item['quantity'] + 1 }}">
                                                <button type="submit">+</button>
                                            </form>
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
                    
                    <div class="summary-item">
                        <span>Subtotal ({{ $summary['total_items'] }} produk)</span>
                        <span>Rp {{ number_format($summary['total_harga'], 0, ',', '.') }}</span>
                    </div>
                    
                    <div class="summary-item">
                        <span>Biaya Desain</span>
                        <span>Rp {{ number_format($summary['biaya_desain'] ?? 0, 0, ',', '.') }}</span>
                    </div>
                    
                    <div class="summary-item summary-total">
                        <span>Total</span>
                        <span>Rp {{ number_format(($summary['total_harga'] + ($summary['biaya_desain'] ?? 0)), 0, ',', '.') }}</span>
                    </div>
                    
                    <a href="#" class="checkout-btn">CHECKOUT SEKARANG</a>
                    
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
@endsection