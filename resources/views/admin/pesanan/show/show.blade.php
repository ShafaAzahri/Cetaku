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
    
    <!-- Status Timeline -->
    @include('admin.pesanan.show.partials.status_timeline', ['pesanan' => $pesanan])
    
    <div class="row">
        <!-- Kolom Kiri: Informasi Pesanan dan Produk -->
        <div class="col-md-8">
            <!-- Informasi Pesanan -->
            @include('admin.pesanan.show.partials.pesanan_info', ['pesanan' => $pesanan])
            
            <!-- Detail Produk -->
            @include('admin.pesanan.show.partials.detail_produk', ['pesanan' => $pesanan])
        </div>
        
        <!-- Kolom Kanan: Informasi Pelanggan dan Aksi -->
        <div class="col-md-4">
            <!-- Informasi Pelanggan -->
            @include('admin.pesanan.show.partials.pelanggan_info', ['pesanan' => $pesanan])
            
            <!-- Catatan -->
            @include('admin.pesanan.show.partials.catatan', ['pesanan' => $pesanan])
            
            <!-- Update Status -->
            @include('admin.pesanan.show.partials.update_status', [
                'pesanan' => $pesanan,
                'statusOptions' => $statusOptions ?? ['Pemesanan', 'Dikonfirmasi', 'Sedang Diproses', 'Menunggu Pengambilan', 'Sedang Dikirim', 'Selesai', 'Dibatalkan']
            ])
            
            <!-- Aksi Pesanan -->
            @include('admin.pesanan.show.partials.aksi_pesanan', ['pesanan' => $pesanan])
        </div>
    </div>
</div>

<!-- Modals -->
@include('admin.pesanan.show.modals.design_preview_modal', ['pesanan' => $pesanan])
@include('admin.pesanan.show.modals.assign_production_modal', [
    'pesanan' => $pesanan,
    'mesinList' => $mesinList ?? [],
    'operatorList' => $operatorList ?? []
])
@include('admin.pesanan.show.modals.complete_production_modal', ['pesanan' => $pesanan])
@include('admin.pesanan.show.modals.shipment_modal', ['pesanan' => $pesanan])
@include('admin.pesanan.show.modals.upload_design_modal', ['pesanan' => $pesanan])
@endsection

@section('scripts')
    @include('admin.pesanan.show.partials.scripts')
@endsection