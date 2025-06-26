@extends('admin.layout.admin')

@section('title', 'Detail Pesanan #' . $pesanan['id'])

@section('styles')
    @include('admin.pesanan.show.partials.styles')
@endsection

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4>Detail Pesanan #{{ $pesanan['id'] }}</h4>
        <a href="{{ route('admin.pesanan.index') }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left me-1"></i> Kembali ke Daftar
        </a>
    </div>

    @include('admin.pesanan.show.partials.alerts')
    @include('admin.pesanan.show.partials.status_timeline', ['pesanan' => $pesanan])

    <div class="row">
        <!-- Kolom Kiri -->
        <div class="col-md-8">
            @include('admin.pesanan.show.partials.pesanan_info', ['pesanan' => $pesanan])
            @include('admin.pesanan.show.partials.detail_produk', ['pesanan' => $pesanan])
        </div>

        <!-- Kolom Kanan -->
        <div class="col-md-4">
            @include('admin.pesanan.show.partials.pelanggan_info', ['pesanan' => $pesanan])
            @include('admin.pesanan.show.partials.catatan', ['pesanan' => $pesanan])
            @include('admin.pesanan.show.partials.update_status', [
                'pesanan' => $pesanan,
                'statusOptions' => $statusOptions ?? ['Pemesanan', 'Dikonfirmasi', 'Sedang Diproses', 'Menunggu Pengambilan', 'Sedang Dikirim', 'Selesai', 'Dibatalkan']
            ])
            @include('admin.pesanan.show.partials.aksi_pesanan', ['pesanan' => $pesanan])
        </div>
    </div>
</div>

{{-- Modals --}}
@include('admin.pesanan.show.modals.design_preview_modal', ['pesanan' => $pesanan])
@include('admin.pesanan.show.modals.assign_production_modal', ['pesanan' => $pesanan, 'mesinList' => $mesinList ?? [], 'operatorList' => $operatorList ?? []])
@include('admin.pesanan.show.modals.complete_production_modal', ['pesanan' => $pesanan])
@include('admin.pesanan.show.modals.shipment_modal', ['pesanan' => $pesanan])
@include('admin.pesanan.show.modals.upload_design_modal', ['pesanan' => $pesanan])
@endsection

@section('scripts')
    @include('admin.pesanan.show.partials.scripts')

    <script>
        function copyResi() {
            const resiInput = document.getElementById("resi_pesanan") || document.getElementById("resiText");
            resiInput.select();
            resiInput.setSelectionRange(0, 99999);
            document.execCommand("copy");
            alert("Nomor Resi disalin: " + resiInput.value);
        }

        function updateFileLabel() {
            const input = document.getElementById('bukti_pengiriman');
            const label = document.getElementById('labelBukti');
            if (input.files.length > 0) {
                label.textContent = 'Upload Bukti Pengiriman: ' + input.files[0].name;
            } else {
                label.textContent = 'Upload Bukti Pengiriman: No file chosen';
            }
        }

        function konfirmasiSimpan() {
            const resiInput = document.getElementById('resi_pesanan');
            if (!resiInput.value.trim()) {
                alert("Nomor resi belum diisi!");
                return false;
            }
            return confirm("Pastikan nomor resi dan bukti pengiriman sudah benar. Lanjutkan?");
        }
    </script>
@endsection
