@extends('user.layouts.app')

@section('title', 'Pembayaran QRIS')

@section('content')
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">
                        <i class="fas fa-qrcode me-2"></i>
                        Pembayaran QRIS
                    </h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="alert alert-info">
                                <h6><strong>Detail Pembayaran</strong></h6>
                                <hr>
                                <p class="mb-1"><strong>Order ID:</strong> {{ $order_id }}</p>
                                <p class="mb-1"><strong>Pesanan ID:</strong> #{{ $pesanan_id }}</p>
                                <p class="mb-1"><strong>Total Pembayaran:</strong> 
                                    <span class="fw-bold text-success">Rp {{ number_format($total_amount, 0, ',', '.') }}</span>
                                </p>
                                <p class="mb-0"><strong>Batas Waktu:</strong> 
                                    <span id="countdown" class="fw-bold text-danger"></span>
                                </p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="alert alert-warning">
                                <h6><strong>Petunjuk Pembayaran</strong></h6>
                                <hr>
                                <ol class="mb-0 small">
                                    <li>Klik tombol "Bayar Sekarang"</li>
                                    <li>Pilih metode pembayaran QRIS</li>
                                    <li>Scan QR Code dengan aplikasi mobile banking</li>
                                    <li>Konfirmasi pembayaran di aplikasi Anda</li>
                                </ol>
                            </div>
                        </div>
                    </div>
                    
                    <div class="text-center mt-4">
                        <button id="pay-button" class="btn btn-primary btn-lg px-5">
                            <i class="fas fa-credit-card me-2"></i>
                            Bayar Sekarang
                        </button>
                        
                        <div class="mt-3">
                            <button id="check-status" class="btn btn-outline-secondary">
                                <i class="fas fa-sync-alt me-2"></i>
                                Cek Status Pembayaran
                            </button>
                        </div>
                        
                        <div class="mt-3">
                            <a href="{{ route('pesanan') }}" class="btn btn-outline-dark">
                                <i class="fas fa-arrow-left me-2"></i>
                                Kembali ke Pesanan
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Loading Modal -->
<div class="modal fade" id="loadingModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-2 mb-0">Memproses pembayaran...</p>
            </div>
        </div>
    </div>
</div>

<script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.client_key') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const snapToken = '{{ $snap_token }}';
    const orderId = '{{ $order_id }}';
    const pesananId = '{{ $pesanan_id }}';
    const expiresAt = new Date('{{ $expires_at }}');
    
    // Countdown timer
    function updateCountdown() {
        const now = new Date();
        const timeLeft = expiresAt - now;
        
        if (timeLeft <= 0) {
            document.getElementById('countdown').textContent = 'EXPIRED';
            document.getElementById('pay-button').disabled = true;
            document.getElementById('pay-button').innerHTML = '<i class="fas fa-times me-2"></i>Pembayaran Expired';
            document.getElementById('pay-button').classList.remove('btn-primary');
            document.getElementById('pay-button').classList.add('btn-danger');
            return;
        }
        
        const hours = Math.floor(timeLeft / 3600000);
        const minutes = Math.floor((timeLeft % 3600000) / 60000);
        const seconds = Math.floor((timeLeft % 60000) / 1000);
        
        document.getElementById('countdown').textContent = 
            `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
    }
    
    updateCountdown();
    const countdownInterval = setInterval(updateCountdown, 1000);
    
    // Payment button
    document.getElementById('pay-button').onclick = function() {
        const loadingModal = new bootstrap.Modal(document.getElementById('loadingModal'));
        
        snap.pay(snapToken, {
            onSuccess: function(result) {
                loadingModal.hide();
                console.log('Payment Success:', result);
                
                Swal.fire({
                    icon: 'success',
                    title: 'Pembayaran Berhasil!',
                    text: 'Terima kasih, pembayaran Anda telah berhasil diproses.',
                    confirmButtonText: 'Lihat Pesanan'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = '{{ route("pesanan") }}';
                    }
                });
            },
            onPending: function(result) {
                loadingModal.hide();
                console.log('Payment Pending:', result);
                
                Swal.fire({
                    icon: 'info',
                    title: 'Pembayaran Pending',
                    text: 'Pembayaran Anda sedang diproses. Silakan tunggu konfirmasi.',
                    confirmButtonText: 'OK'
                });
            },
            onError: function(result) {
                loadingModal.hide();
                console.log('Payment Error:', result);
                
                Swal.fire({
                    icon: 'error',
                    title: 'Pembayaran Gagal',
                    text: 'Terjadi kesalahan saat memproses pembayaran. Silakan coba lagi.',
                    confirmButtonText: 'OK'
                });
            },
            onClose: function() {
                loadingModal.hide();
                console.log('Payment popup closed');
            }
        });
        
        loadingModal.show();
    };
    
    // Check status button
    document.getElementById('check-status').onclick = function() {
        const button = this;
        const originalText = button.innerHTML;
        
        button.disabled = true;
        button.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Mengecek...';
        
        fetch(`/api/payment/status?pesanan_id=${pesananId}`, {
            headers: {
                'Authorization': 'Bearer {{ session("api_token") }}',
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            button.disabled = false;
            button.innerHTML = originalText;
            
            if (data.status === 'success') {
                const paymentStatus = data.data.payment_status;
                const pesananStatus = data.data.pesanan_status;
                
                if (paymentStatus === 'Lunas') {
                    clearInterval(countdownInterval);
                    Swal.fire({
                        icon: 'success',
                        title: 'Pembayaran Lunas!',
                        text: 'Pembayaran Anda telah berhasil dikonfirmasi.',
                        confirmButtonText: 'Lihat Pesanan'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = '{{ route("pesanan") }}';
                        }
                    });
                } else {
                    Swal.fire({
                        icon: 'info',
                        title: 'Status Pembayaran',
                        text: `Status: ${paymentStatus}`,
                        confirmButtonText: 'OK'
                    });
                }
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: data.message || 'Gagal mengecek status pembayaran',
                    confirmButtonText: 'OK'
                });
            }
        })
        .catch(error => {
            button.disabled = false;
            button.innerHTML = originalText;
            console.error('Error:', error);
            
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Terjadi kesalahan saat mengecek status',
                confirmButtonText: 'OK'
            });
        });
    };
});
</script>

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endsection
