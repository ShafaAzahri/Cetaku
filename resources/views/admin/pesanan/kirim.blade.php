@extends('admin.layout.admin')

@section('title', 'Konfirmasi Pengiriman')

@section('styles')
<style>
    .shipping-form {
        max-width: 600px;
        margin: 40px auto;
    }
    .shipping-info {
        background-color: #f8f9fa;
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 30px;
    }
    .info-item {
        display: flex;
        justify-content: space-between;
        padding: 10px 0;
        border-bottom: 1px solid #dee2e6;
    }
    .info-item:last-child {
        border-bottom: none;
    }
    .shipping-icon {
        font-size: 3rem;
        color: #17a2b8;
        margin-bottom: 20px;
    }
    .courier-selector {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 15px;
        margin: 15px 0;
    }
    .courier-option {
        border: 1px solid #dee2e6;
        border-radius: 8px;
        padding: 15px;
        text-align: center;
        cursor: pointer;
        transition: all 0.3s ease;
    }
    .courier-option:hover {
        border-color: #17a2b8;
        background-color: #f8f9fa;
    }
    .courier-option.selected {
        border-color: #17a2b8;
        background-color: #e7f3fe;
    }
    .shipping-alert {
        background-color: #e8f5e9;
        border-color: #4caf50;
        color: #2e7d32;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card shadow shipping-form">
                <div class="card-header text-center bg-info text-white">
                    <i class="fas fa-shipping-fast shipping-icon d-block"></i>
                    <h4 class="mb-0">Konfirmasi Pengiriman</h4>
                </div>
                
                <div class="card-body">
                    <div class="text-center mb-4">
                        <h5>Pesanan #{{ $pesanan->id }}</h5>
                        <p class="text-muted">Akan dikirim kepada {{ $pesanan->user->nama ?? 'Pelanggan' }}</p>
                    </div>
                    
                    <div class="shipping-info">
                        <h6 class="text-uppercase text-muted fw-bold mb-3">Informasi Pengiriman</h6>
                        
                        <div class="info-item">
                            <span class="text-muted">Ekspedisi:</span>
                            <span class="fw-bold">{{ $pesanan->ekspedisi->nama_ekspedisi ?? 'Belum dipilih' }}</span>
                        </div>
                        
                        <div class="info-item">
                            <span class="text-muted">Layanan:</span>
                            <span class="fw-bold">{{ $pesanan->ekspedisi->layanan ?? '-' }}</span>
                        </div>
                        
                        <div class="info-item">
                            <span class="text-muted">Estimasi:</span>
                            <span class="fw-bold">{{ $pesanan->ekspedisi->estimasi ?? '-' }}</span>
                        </div>
                        
                        <div class="info-item">
                            <span class="text-muted">Ongkos Kirim:</span>
                            <span class="fw-bold">Rp {{ number_format($pesanan->ekspedisi->ongkos_kirim ?? 0, 0, ',', '.') }}</span>
                        </div>
                        
                        <div class="info-item">
                            <span class="text-muted">Alamat Tujuan:</span>
                            <div class="text-end fw-bold" style="max-width: 200px;">
                                {{ $pesanan->user->alamats->first()->alamat_lengkap ?? 'Alamat tidak tersedia' }}
                            </div>
                        </div>
                    </div>
                    
                    <form action="{{ route('admin.pesanan.proses-kirim', $pesanan->id) }}" method="POST" id="shippingForm">
                        @csrf
                        
                        <div class="mb-4">
                            <label for="no_resi" class="form-label fw-bold">Nomor Resi</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-barcode"></i></span>
                                <input type="text" class="form-control" id="no_resi" name="no_resi" 
                                       placeholder="Masukkan nomor resi pengiriman" required>
                            </div>
                            <small class="form-text text-muted">Nomor resi akan dikirim ke pelanggan via email/SMS</small>
                        </div>
                        
                        <div class="mb-4">
                            <label for="tanggal_kirim" class="form-label fw-bold">Tanggal Pengiriman</label>
                            <input type="date" class="form-control" id="tanggal_kirim" name="tanggal_kirim" 
                                   value="{{ date('Y-m-d') }}" required>
                        </div>
                        
                        <div class="mb-4">
                            <label for="catatan" class="form-label fw-bold">Catatan Pengiriman (Opsional)</label>
                            <textarea class="form-control" id="catatan" name="catatan" rows="3" 
                                      placeholder="Masukkan catatan khusus untuk pengiriman..."></textarea>
                        </div>
                        
                        <!-- Checklist -->
                        <div class="mb-4">
                            <h6 class="fw-bold mb-3">Checklist Pengiriman</h6>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" id="check1" required>
                                <label class="form-check-label" for="check1">
                                    Produk sudah dikemas dengan baik
                                </label>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" id="check2" required>
                                <label class="form-check-label" for="check2">
                                    Label pengiriman sudah terpasang
                                </label>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" id="check3" required>
                                <label class="form-check-label" for="check3">
                                    Invoice sudah disertakan
                                </label>
                            </div>
                        </div>
                        
                        <!-- Success Alert -->
                        <div class="alert shipping-alert">
                            <i class="fas fa-check-circle me-2"></i>
                            <strong>Perhatian:</strong> Setelah mengkonfirmasi pengiriman, status pesanan akan berubah menjadi "Sedang Dikirim".
                        </div>
                        
                        <!-- Action Buttons -->
                        <div class="d-flex justify-content-center gap-3">
                            <a href="{{ route('admin.pesanan.show', $pesanan->id) }}" class="btn btn-secondary">
                                <i class="fas fa-times me-1"></i> Batal
                            </a>
                            <button type="submit" class="btn btn-info">
                                <i class="fas fa-truck me-1"></i> Konfirmasi Pengiriman
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
        // Auto-format nomor resi
        $('#no_resi').on('input', function() {
            this.value = this.value.toUpperCase();
        });
        
        // Form validation
        $('#shippingForm').on('submit', function(e) {
            var allChecked = true;
            $('.form-check-input[required]').each(function() {
                if (!$(this).is(':checked')) {
                    allChecked = false;
                }
            });
            
            if (!allChecked) {
                e.preventDefault();
                alert('Harap centang semua checklist sebelum mengirim');
                return false;
            }
            
            if (!$('#no_resi').val()) {
                e.preventDefault();
                alert('Silakan masukkan nomor resi pengiriman');
                return false;
            }
            
            var confirmText = 'Apakah Anda yakin ingin mengkonfirmasi pengiriman untuk pesanan #{{ $pesanan->id }}?\n\n';
            confirmText += 'Nomor Resi: ' + $('#no_resi').val() + '\n';
            confirmText += 'Tanggal Kirim: ' + $('#tanggal_kirim').val();
            
            if (!confirm(confirmText)) {
                e.preventDefault();
                return false;
            }
        });
        
        // Highlight required fields
        $('.form-check-input[required]').on('change', function() {
            if ($(this).is(':checked')) {
                $(this).closest('.form-check').addClass('text-success');
            } else {
                $(this).closest('.form-check').removeClass('text-success');
            }
        });
    });
</script>
@endsection