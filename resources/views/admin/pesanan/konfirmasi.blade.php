@extends('admin.layout.admin')

@section('title', 'Konfirmasi Pesanan')

@section('styles')
<style>
    .confirmation-card {
        max-width: 600px;
        margin: 40px auto;
    }
    .order-info {
        background-color: #f8f9fa;
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 20px;
    }
    .info-row {
        display: flex;
        justify-content: space-between;
        padding: 10px 0;
        border-bottom: 1px solid #dee2e6;
    }
    .info-row:last-child {
        border-bottom: none;
    }
    .info-label {
        color: #6c757d;
        font-weight: 500;
    }
    .info-value {
        color: #495057;
        font-weight: 600;
    }
    .confirmation-icon {
        font-size: 4rem;
        color: #ffc107;
        margin-bottom: 20px;
    }
    .action-buttons {
        display: flex;
        gap: 10px;
        justify-content: center;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card shadow confirmation-card">
                <div class="card-header text-center bg-warning text-white">
                    <i class="fas fa-exclamation-triangle confirmation-icon d-block"></i>
                    <h4 class="mb-0">Konfirmasi Pesanan</h4>
                </div>
                
                <div class="card-body">
                    <div class="text-center mb-4">
                        <h5>Pesanan #{{ $pesanan->id }}</h5>
                        <p class="text-muted">Anda akan mengkonfirmasi pesanan ini</p>
                    </div>
                    
                    <div class="order-info">
                        <div class="info-row">
                            <span class="info-label">Tanggal Pesan:</span>
                            <span class="info-value">{{ $pesanan->tanggal_dipesan->format('d M Y H:i') }}</span>
                        </div>
                        
                        <div class="info-row">
                            <span class="info-label">Pelanggan:</span>
                            <span class="info-value">{{ $pesanan->user->nama ?? 'Unknown' }}</span>
                        </div>
                        
                        <div class="info-row">
                            <span class="info-label">Metode Pengambilan:</span>
                            <span class="info-value">
                                {{ $pesanan->metode_pengambilan == 'ambil' ? 'Ambil di Tempat' : 'Dikirim' }}
                            </span>
                        </div>
                        
                        @if($pesanan->metode_pengambilan == 'antar' && $pesanan->ekspedisi)
                        <div class="info-row">
                            <span class="info-label">Ekspedisi:</span>
                            <span class="info-value">{{ $pesanan->ekspedisi->nama_ekspedisi }} - {{ $pesanan->ekspedisi->layanan }}</span>
                        </div>
                        @endif
                        
                        <div class="info-row">
                            <span class="info-label">Total Harga:</span>
                            <span class="info-value">Rp {{ number_format($pesanan->detailPesanans->sum('total_harga'), 0, ',', '.') }}</span>
                        </div>
                    </div>
                    
                    <form action="{{ route('admin.pesanan.proses-konfirmasi', $pesanan->id) }}" method="POST">
                        @csrf
                        
                        <div class="form-group mb-4">
                            <label for="catatan" class="form-label">Catatan Konfirmasi (Opsional)</label>
                            <textarea class="form-control" id="catatan" name="catatan" rows="3" 
                                      placeholder="Masukkan catatan untuk pelanggan (jika ada)..."></textarea>
                        </div>
                        
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Perhatian:</strong> Dengan mengkonfirmasi pesanan ini, Anda bertanggung jawab untuk proses selanjutnya.
                        </div>
                        
                        <div class="action-buttons">
                            <a href="{{ route('admin.pesanan.show', $pesanan->id) }}" class="btn btn-secondary">
                                <i class="fas fa-times me-1"></i> Batal
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-check me-1"></i> Konfirmasi Pesanan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Add animation to confirmation card
        $('.confirmation-card').css({
            'opacity': '0',
            'transform': 'translateY(20px)'
        }).animate({
            'opacity': '1',
            'transform': 'translateY(0)'
        }, 300);
        
        // Form validation
        $('form').on('submit', function(e) {
            var confirmed = confirm('Apakah Anda yakin ingin mengkonfirmasi pesanan ini?');
            if (!confirmed) {
                e.preventDefault();
            }
        });
    });
</script>
@endsection