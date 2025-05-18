<div class="detail-card">
    <h5>Informasi Pelanggan</h5>
    <div class="info-row">
        <div class="info-label">Nama</div>
        <div class="info-value">{{ $pesanan['user']['nama'] ?? 'Iswanti' }}</div>
    </div>
    <div class="info-row">
        <div class="info-label">ID</div>
        <div class="info-value">#{{ $pesanan['user']['id'] ?? '2' }}</div>
    </div>
    <div class="info-row">
        <div class="info-label">Email</div>
        <div class="info-value">{{ $pesanan['user']['email'] ?? 'iswanti@gmail.com' }}</div>
    </div>
    <div class="info-row">
        <div class="info-label">Tanggal</div>
        <div class="info-value">{{ \Carbon\Carbon::parse($pesanan['tanggal_dipesan'])->format('d M Y') }}</div>
    </div>
</div>