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

    @if ($pesanan['status'] === 'Sedang Dikirim')
        <h4 class="mt-4">Upload Resi & Bukti Pengiriman</h4>

        @if (!empty($pesanan['resi_pesanan']))
            <p>
                <strong>Resi:</strong>
                <span>{{ $pesanan['resi_pesanan'] }}</span>
            </p>
        @endif

        @if (!empty($pesanan['bukti_pengiriman']))
            <p>
                <strong>Bukti Pengiriman:</strong><br>
                <a href="{{ asset('storage/' . $pesanan['bukti_pengiriman']) }}" target="_blank">
                    <img src="{{ asset('storage/' . $pesanan['bukti_pengiriman']) }}" alt="Bukti" class="img-fluid mt-2" style="max-width: 200px;">
                </a>
            </p>
        @endif

        {{-- Form Upload Resi --}}
        <form action="{{ route('admin.pesanan.upload-resi', $pesanan['id']) }}" method="POST" class="mt-3 mb-3" onsubmit="return konfirmasiResi()">
            @csrf
            <div class="mb-2">
                <label for="resi_pesanan">No Resi:</label>
                <input type="text" name="resi_pesanan" id="resi_pesanan" class="form-control" value="{{ old('resi_pesanan', $pesanan['resi_pesanan'] ?? '') }}" required>
            </div>
            <button type="submit" class="btn btn-primary btn-sm">Upload Resi</button>
        </form>

        {{-- Form Upload Bukti --}}
        <form action="{{ route('admin.pesanan.upload-bukti', $pesanan['id']) }}" method="POST" enctype="multipart/form-data" onsubmit="return konfirmasiBukti()">
            @csrf
            <div class="mb-2">
                <label for="bukti_pengiriman">Upload Bukti Pengiriman (gambar):</label>
                <input type="file" name="bukti_pengiriman" id="bukti_pengiriman" class="form-control" accept="image/*" required>
            </div>
            <button type="submit" class="btn btn-success btn-sm">Upload Bukti</button>
        </form>
    @elseif ($pesanan['status'] === 'Selesai')
        <h4 class="mt-4">Informasi Pengiriman</h4>

        @if (!empty($pesanan['resi_pesanan']))
            <p>
                <strong>Resi:</strong>
                <span>{{ $pesanan['resi_pesanan'] }}</span>
            </p>
        @endif

        @if (!empty($pesanan['bukti_pengiriman']))
            <p>
                <strong>Bukti Pengiriman:</strong><br>
                <a href="{{ asset('storage/' . $pesanan['bukti_pengiriman']) }}" target="_blank">
                    <img src="{{ asset('storage/' . $pesanan['bukti_pengiriman']) }}" alt="Bukti" class="img-fluid mt-2" style="max-width: 200px;">
                </a>
            </p>
        @endif
    @endif
</div>

@push('scripts')
<script>
    function konfirmasiResi() {
        const resi = document.getElementById('resi_pesanan').value.trim();
        if (!resi) {
            alert("Nomor resi tidak boleh kosong!");
            return false;
        }
        return confirm("Apakah Anda yakin nomor resi sudah benar?");
    }

    function konfirmasiBukti() {
        const fileInput = document.getElementById('bukti_pengiriman');
        if (!fileInput.files.length) {
            alert("Anda belum memilih file gambar!");
            return false;
        }
        return confirm("Apakah Anda yakin gambar bukti pengiriman sudah benar?");
    }
</script>
@endpush
