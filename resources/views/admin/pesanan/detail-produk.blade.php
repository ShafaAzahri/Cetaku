@extends('admin.layout.admin')

@section('title', 'Detail Produk')

@section('styles')
<style>
    .product-image {
        max-height: 300px;
        width: auto;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        object-fit: contain;
    }
    
    .design-image {
        max-height: 200px;
        width: auto;
        border-radius: 8px;
        border: 1px solid #dee2e6;
        object-fit: contain;
    }
    
    .img-container {
        background-color: #f8f9fa;
        padding: 15px;
        border-radius: 8px;
        text-align: center;
        margin-bottom: 20px;
    }
    
    .detail-label {
        font-weight: 600;
        color: #495057;
    }
    
    .detail-value {
        background-color: #f8f9fa;
        padding: 8px 12px;
        border-radius: 4px;
        margin-bottom: 15px;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.pesanan.index') }}">Pesanan</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.pesanan.show', $pesanan->id ?? 0) }}">Pesanan #{{ $pesanan->id ?? 0 }}</a></li>
            <li class="breadcrumb-item active" aria-current="page">Detail Produk</li>
        </ol>
    </nav>

    <div class="card shadow-sm">
        <div class="card-header">
            <h5 class="m-0">Detail Produk: {{ $produk['nama'] }}</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <!-- Kolom Kiri - Gambar Produk -->
                <div class="col-md-4">
                    <div class="img-container">
                        <img src="{{ $produk['gambar_url'] }}" class="product-image" alt="{{ $produk['nama'] }}">
                    </div>
                    
                    <div class="card mb-3">
                        <div class="card-header bg-light">
                            <h6 class="mb-0">Informasi Produk</h6>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <p class="detail-label">Nama Produk</p>
                                <div class="detail-value">{{ $produk['nama'] }}</div>
                            </div>
                            
                            <div class="mb-3">
                                <p class="detail-label">Jenis</p>
                                <div class="detail-value">{{ $produk['jenis'] }}</div>
                            </div>
                            
                            <div class="mb-3">
                                <p class="detail-label">Bahan</p>
                                <div class="detail-value">{{ $produk['bahan'] }}</div>
                            </div>
                            
                            <div class="mb-3">
                                <p class="detail-label">Ukuran</p>
                                <div class="detail-value">{{ $produk['ukuran'] }}</div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Kolom Kanan - Detail Pesanan -->
                <div class="col-md-8">
                    <div class="card mb-4">
                        <div class="card-header bg-light">
                            <h6 class="mb-0">Detail Pemesanan</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <p class="detail-label">Jumlah</p>
                                    <div class="detail-value">{{ $produk['jumlah'] }} unit</div>
                                </div>
                                <div class="col-md-6">
                                    <p class="detail-label">Harga Satuan</p>
                                    <div class="detail-value">Rp {{ number_format($produk['harga'], 0, ',', '.') }}</div>
                                </div>
                                <div class="col-md-6">
                                    <p class="detail-label">Subtotal</p>
                                    <div class="detail-value">Rp {{ number_format($produk['subtotal'], 0, ',', '.') }}</div>
                                </div>
                                <div class="col-md-6">
                                    <p class="detail-label">Tipe Desain</p>
                                    <div class="detail-value">
                                        @if($produk['tipe_desain'] == 'sendiri')
                                            <span class="badge bg-primary">Desain Sendiri</span>
                                        @else
                                            <span class="badge bg-success">Jasa Desain</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mt-3">
                                <p class="detail-label">Catatan</p>
                                <div class="detail-value">{{ $produk['catatan'] }}</div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Desain Customer & Final -->
                    <div class="row">
                        @if($produk['desain_customer_url'])
                        <div class="col-md-6">
                            <div class="card mb-3">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0">Desain Pelanggan</h6>
                                </div>
                                <div class="card-body text-center">
                                    <img src="{{ $produk['desain_customer_url'] }}" class="design-image" alt="Desain Pelanggan">
                                </div>
                            </div>
                        </div>
                        @endif
                        
                        @if($produk['desain_final_url'])
                        <div class="col-md-6">
                            <div class="card mb-3">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0">Desain Final</h6>
                                </div>
                                <div class="card-body text-center">
                                    <img src="{{ $produk['desain_final_url'] }}" class="design-image" alt="Desain Final">
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                    
                    <!-- Form Upload Desain -->
                    <div class="card">
                        <div class="card-header bg-light">
                            <h6 class="mb-0">Upload Desain</h6>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('admin.pesanan.upload', $pesanan->id ?? 0) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" name="produk_id" value="{{ $produk['id'] }}">
                                
                                <div class="form-group mb-3">
                                    <label for="tipe" class="form-label">Tipe Desain</label>
                                    <select class="form-select" id="tipe" name="tipe" required>
                                        <option value="customer">Desain Pelanggan</option>
                                        <option value="final">Desain Final</option>
                                    </select>
                                </div>
                                
                                <div class="form-group mb-3">
                                    <label for="desain" class="form-label">File Desain</label>
                                    <input type="file" class="form-control" id="desain" name="desain" required accept="image/*">
                                    <div class="form-text">Maksimal ukuran file 2MB. Format yang didukung: JPG, PNG, GIF.</div>
                                </div>
                                
                                <div class="form-group mb-3">
                                    <label for="catatan" class="form-label">Catatan</label>
                                    <textarea class="form-control" id="catatan" name="catatan" rows="2"></textarea>
                                </div>
                                
                                <div class="text-end">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-upload me-1"></i> Upload Desain
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-footer">
            <div class="d-flex justify-content-between">
                <a href="{{ route('admin.pesanan.show', $pesanan->id ?? 0) }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-1"></i> Kembali ke Detail Pesanan
                </a>
                
                <a href="{{ route('admin.pesanan.index') }}" class="btn btn-info">
                    <i class="fas fa-list me-1"></i> Daftar Pesanan
                </a>
            </div>
        </div>
    </div>
</div>
@endsection