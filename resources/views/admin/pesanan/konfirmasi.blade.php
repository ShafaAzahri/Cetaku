@extends('admin.layout.admin')

@section('title', 'Konfirmasi Pesanan')

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
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row mb-2">
        <div class="col-md-6">
            <h1 class="h3 mb-0 text-dark">Konfirmasi Pesanan #{{ $pesanan->id }}</h1>
        </div>
        <div class="col-md-6 text-md-right">
            <a href="{{ route('admin.pesanan.index') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> Kembali ke Daftar
            </a>
        </div>
    </div>

    <div class="card shadow">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Detail Pesanan</h6>
        </div>
        <div class="card-body">
            <div class="row detail-section">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label font-weight-bold">ID Pemesanan</label>
                        <input type="text" class="form-control" value="{{ $pesanan->id }}" readonly>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label font-weight-bold">Tanggal Pesan</label>
                        <input type="text" class="form-control" value="{{ $pesanan->tanggal_dipesan->format('Y-m-d H:i') }}" readonly>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label font-weight-bold">Pelanggan</label>
                        <input type="text" class="form-control" value="{{ $pesanan->user->nama ?? 'Unknown' }}" readonly>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label font-weight-bold">Metode Pengambilan</label>
                        <input type="text" class="form-control" value="{{ $pesanan->metode_pengambilan == 'ambil' ? 'Ambil di Tempat' : 'Dikirim' }}" readonly>
                    </div>
                </div>
            </div>

            <form action="{{ route('admin.pesanan.proses-konfirmasi', $pesanan->id) }}" method="POST">
                @csrf
                
                <div class="form-group">
                    <label for="catatan" class="form-label font-weight-bold">Catatan Konfirmasi (Opsional)</label>
                    <textarea class="form-control" id="catatan" name="catatan" rows="3" placeholder="Tambahkan catatan untuk konfirmasi pesanan ini."></textarea>
                </div>
                
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i> Dengan mengonfirmasi pesanan ini, Anda bertanggung jawab untuk mengelola proses produksi pesanan.
                </div>
                
                <div class="mt-4 text-center">
                    <a href="{{ route('admin.pesanan.show', $pesanan->id) }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Batal
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-check"></i> Konfirmasi Pesanan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection