<div class="detail-card">
    <h5>Status Pesanan</h5>
    <ul class="progress-track">
        <li class="{{ in_array($pesanan['status'], ['Pemesanan', 'Dikonfirmasi', 'Sedang Diproses', 'Menunggu Pengambilan', 'Sedang Dikirim', 'Selesai']) ? 'completed' : '' }}
                 {{ $pesanan['status'] == 'Pemesanan' ? 'current' : '' }}">
            <div class="step"><i class="fas fa-shopping-cart"></i></div>
            <div class="step-label">Pemesanan</div>
        </li>
        <li class="{{ in_array($pesanan['status'], ['Dikonfirmasi', 'Sedang Diproses', 'Menunggu Pengambilan', 'Sedang Dikirim', 'Selesai']) ? 'completed' : '' }}
                 {{ $pesanan['status'] == 'Dikonfirmasi' ? 'current' : '' }}">
            <div class="step"><i class="fas fa-clipboard-check"></i></div>
            <div class="step-label">Dikonfirmasi</div>
        </li>
        <li class="{{ in_array($pesanan['status'], ['Sedang Diproses', 'Menunggu Pengambilan', 'Sedang Dikirim', 'Selesai']) ? 'completed' : '' }}
                 {{ $pesanan['status'] == 'Sedang Diproses' ? 'current' : '' }}">
            <div class="step"><i class="fas fa-cogs"></i></div>
            <div class="step-label">Sedang Diproses</div>
        </li>
        <li class="{{ in_array($pesanan['status'], ['Menunggu Pengambilan', 'Sedang Dikirim', 'Selesai']) ? 'completed' : '' }}
                 {{ $pesanan['status'] == 'Menunggu Pengambilan' || $pesanan['status'] == 'Sedang Dikirim' ? 'current' : '' }}">
            <div class="step"><i class="{{ $pesanan['metode_pengambilan'] == 'antar' ? 'fas fa-truck' : 'fas fa-hourglass-half' }}"></i></div>
            <div class="step-label">{{ $pesanan['metode_pengambilan'] == 'antar' ? 'Pengiriman' : 'Menunggu Pengambilan' }}</div>
        </li>
        <li class="{{ $pesanan['status'] == 'Selesai' ? 'completed' : '' }}
                 {{ $pesanan['status'] == 'Selesai' ? 'current' : '' }}">
            <div class="step"><i class="fas fa-check"></i></div>
            <div class="step-label">Selesai</div>
        </li>
    </ul>
    
    <div class="text-center mt-2">
        <span>Status Saat Ini: <strong>{{ $pesanan['status'] }}</strong></span>
    </div>
</div>