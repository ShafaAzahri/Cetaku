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
                <div class="info-value">
                    {{ \Carbon\Carbon::parse($pesanan['tanggal_dipesan'])->format('d M Y, H:i') }}
                </div>
            </div>
            <div class="info-row">
                <div class="info-label">Metode Pengambilan</div>
                <div class="info-value">
                    {{ $pesanan['metode_pengambilan'] == 'antar' ? 'Dikirim' : 'Ambil di Tempat' }}
                </div>
            </div>
            <div class="info-row">
                <div class="info-label">Estimasi Selesai</div>
                <div class="info-value">{{ $pesanan['estimasi_waktu'] ?? '5 jam' }}</div>
            </div>
        </div>

        <div class="col-md-6">
            @if ($pesanan['metode_pengambilan'] == 'antar')
                <div class="info-row">
                    <div class="info-label">Ekspedisi</div>
                    <div class="info-value">{{ $pesanan['ekspedisi']['nama_ekspedisi'] ?? '-' }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Layanan</div>
                    <div class="info-value">{{ $pesanan['ekspedisi']['layanan'] ?? '-' }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Biaya Pengiriman</div>
                    <div class="info-value">
                        Rp {{ number_format($pesanan['ekspedisi']['ongkos_kirim'] ?? 0, 0, ',', '.') }}
                    </div>
                </div>
            @endif
            <div class="info-row">
                <div class="info-label">Admin</div>
                <div class="info-value">{{ $pesanan['admin']['nama'] ?? 'Belum ditentukan' }}</div>
            </div>
        </div>
    </div>

    {{-- Upload resi jika status "Sedang Dikirim" --}}
    @if ($pesanan['status'] === 'Sedang Dikirim')
        <h4 class="mt-4">Upload Resi Pengiriman</h4>

        @foreach ($pesanan['detail_pesanans'] as $detail)
            <div class="mb-3 p-3 border rounded">
                <p><strong>Produk:</strong> {{ $detail['custom']['item']['nama_item'] ?? 'Produk' }}</p>


                @if ($detail['resi_pesanan'])
                    <p>
                        <strong>Resi:</strong>
                        <a href="{{ asset('storage/' . $detail['resi_pesanan']) }}" target="_blank">Lihat Resi</a>
                    </p>
                @else
                    <form action="{{ route('admin.pesanan.upload-resi', $pesanan['id']) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="detail_pesanan_id" value="{{ $detail['id'] }}">
                        <div class="mb-2">
                            <input type="file" name="resi" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-primary btn-sm">Upload Resi</button>
                    </form>
                @endif
            </div>
        @endforeach
    @endif
</div>