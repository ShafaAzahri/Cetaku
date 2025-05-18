<div class="detail-card">
    <h5>Aksi Pesanan</h5>
    
    <!-- Tombol ini mungkin tidak ada atau tersembunyi -->
    @if($pesanan['status'] == 'Dikonfirmasi')
        <!-- Cek apakah masih ada produk yang belum ditugaskan -->
        @php
            $unassignedExists = false;
            foreach($pesanan['detail_pesanans'] ?? [] as $detail) {
                if(!isset($detail['proses_pesanan'])) {
                    $unassignedExists = true;
                    break;
                }
            }
        @endphp
        
        @if($unassignedExists)
        <button type="button" class="action-btn btn-primary w-100 mb-2" data-bs-toggle="modal" data-bs-target="#assignProductionModal">
            <i class="fas fa-tasks me-1"></i> Tugaskan Produksi
        </button>
        @endif
    @endif
    <!-- Selesaikan Produksi -->
    @if($pesanan['status'] == 'Sedang Diproses')
    <button type="button" class="action-btn btn-complete" data-bs-toggle="modal" data-bs-target="#completeProductionModal">
        <i class="fas fa-check-double"></i> Selesaikan Produksi
    </button>
    @endif
    
    <!-- Konfirmasi Pengiriman -->
    @if($pesanan['status'] == 'Sedang Diproses' && ($pesanan['metode_pengambilan'] ?? '') == 'antar')
    <button type="button" class="action-btn btn-ship" data-bs-toggle="modal" data-bs-target="#shipmentModal">
        <i class="fas fa-truck"></i> Konfirmasi Pengiriman
    </button>
    @endif
    
    <!-- Upload Desain -->
    @if(in_array($pesanan['status'], ['Dikonfirmasi', 'Sedang Diproses']))
    <button type="button" class="action-btn btn-upload" data-bs-toggle="modal" data-bs-target="#uploadDesignModal">
        <i class="fas fa-upload"></i> Upload Desain
    </button>
    @endif
    
    <!-- Batalkan Pesanan -->
    @if(!in_array($pesanan['status'], ['Selesai', 'Dibatalkan']))
    <form action="{{ route('admin.pesanan.cancel', $pesanan['id']) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin membatalkan pesanan ini?')">
        @csrf
        <button type="submit" class="action-btn btn-cancel">
            <i class="fas fa-times-circle"></i> Batalkan Pesanan
        </button>
    </form>
    @endif
</div>