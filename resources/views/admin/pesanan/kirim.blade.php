@extends('admin.layout.admin')

@section('title', 'Proses Cetak Pesanan')

@section('styles')
<style>
    .card-header {
        background-color: #f8f9fa;
    }
    .detail-section {
        background-color: #f8f9fa;
        border-radius: 5px;
        padding: 20px;
        margin-bottom: 20px;
    }
    .product-item {
        border: 1px solid #dee2e6;
        border-radius: 5px;
        padding: 15px;
        margin-bottom: 15px;
    }
    .product-item:hover {
        background-color: #f8f9fa;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row mb-2">
        <div class="col-md-6">
            <h1 class="h3 mb-0 text-dark">Proses Cetak - Pesanan #{{ $pesanan->id }}</h1>
        </div>
        <div class="col-md-6 text-md-right">
            <a href="{{ route('admin.pesanan.show', $pesanan->id) }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> Kembali ke Detail Pesanan
            </a>
        </div>
    </div>

    <div class="card shadow">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Pilih Operator & Mesin</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.pesanan.process-print', $pesanan->id) }}" method="POST">
                @csrf
                
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="operator_id" class="form-label font-weight-bold">Pilih Operator</label>
                            <select class="form-select @error('operator_id') is-invalid @enderror" id="operator_id" name="operator_id" required>
                                <option value="">-- Pilih Operator --</option>
                                @foreach($operators as $operator)
                                <option value="{{ $operator->id }}">{{ $operator->nama }} ({{ $operator->posisi }})</option>
                                @endforeach
                            </select>
                            @error('operator_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="mesin_id" class="form-label font-weight-bold">Pilih Mesin</label>
                            <select class="form-select @error('mesin_id') is-invalid @enderror" id="mesin_id" name="mesin_id" required>
                                <option value="">-- Pilih Mesin --</option>
                                @foreach($mesins as $mesin)
                                <option value="{{ $mesin->id }}">{{ $mesin->nama_mesin }} ({{ $mesin->tipe_mesin }})</option>
                                @endforeach
                            </select>
                            @error('mesin_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                
                <div class="form-group mb-4">
                    <label class="form-label font-weight-bold">Pilih Produk yang Akan Diproses</label>
                    
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="radio" name="detail_pesanan_id" id="all_products" value="" checked>
                        <label class="form-check-label" for="all_products">
                            <strong>Semua Produk</strong> - Proses semua produk dalam pesanan ini
                        </label>
                    </div>
                    
                    <div class="mt-3">
                        <p><strong>Atau pilih produk spesifik:</strong></p>
                        
                        @foreach($pesanan->detailPesanans as $detail)
                        <div class="product-item">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="detail_pesanan_id" id="product_{{ $detail->id }}" value="{{ $detail->id }}">
                                <label class="form-check-label" for="product_{{ $detail->id }}">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <strong>{{ $detail->custom->item->nama_item ?? 'Produk tidak diketahui' }}</strong>
                                            <span class="text-muted ms-2">({{ $detail->jumlah }} unit)</span>
                                        </div>
                                        <div>
                                            @if($detail->prosesPesanan)
                                                <span class="badge bg-warning text-dark">Sudah Ada Proses</span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="mt-2">
                                        <small class="text-muted">
                                            Bahan: {{ $detail->custom->bahan->nama_bahan ?? 'Unknown' }} | 
                                            Ukuran: {{ $detail->custom->ukuran->size ?? 'Unknown' }} | 
                                            Jenis: {{ $detail->custom->jenis->kategori ?? 'Unknown' }}
                                        </small>
                                    </div>
                                </label>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="catatan" class="form-label font-weight-bold">Catatan Proses (Opsional)</label>
                    <textarea class="form-control" id="catatan" name="catatan" rows="3" placeholder="Tambahkan catatan untuk proses produksi"></textarea>
                </div>
                
                <div class="alert alert-info mt-4">
                    <i class="fas fa-info-circle me-2"></i> Proses cetak akan ditugaskan kepada operator yang dipilih dan status pesanan akan berubah menjadi "Sedang Diproses".
                </div>
                
                <div class="mt-4 text-center">
                    <a href="{{ route('admin.pesanan.show', $pesanan->id) }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Batal
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-print"></i> Mulai Proses Cetak
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection