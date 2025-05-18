<div class="detail-card">
    <h5>Informasi Pesanan</h5>
    
    <div class="row">
        <div class="col-md-6">
            <div class="info-row">
                <div class="info-label">ID Pemesanan</div>
                <div class="info-value">#{{ $pesanan['id'] }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Tanggal Pemesanan</div>
                <div class="info-value">{{ \Carbon\Carbon::parse($pesanan['tanggal_dipesan'])->format('d M Y, H:i') }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Metode Pengambilan</div>
                <div class="info-value">{{ $pesanan['metode_pengambilan'] == 'antar' ? 'Dikirim' : 'Ambil di Tempat' }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Estimasi Selesai</div>
                <div class="info-value">{{ $pesanan['estimasi_waktu'] ?? '5 jam' }}</div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="info-row">
                <div class="info-label">Ekspedisi</div>
                <div class="info-value">{{ $pesanan['ekspedisi']['nama_ekspedisi'] ?? 'JNE' }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Layanan</div>
                <div class="info-value">{{ $pesanan['ekspedisi']['layanan'] ?? 'Regular' }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Biaya Pengiriman</div>
                <div class="info-value">Rp {{ number_format($pesanan['ekspedisi']['ongkos_kirim'] ?? 15000, 0, ',', '.') }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Admin</div>
                <div class="info-value">{{ $pesanan['admin']['nama'] ?? 'Belum ditentukan' }}</div>
            </div>
        </div>
    </div>
</div>