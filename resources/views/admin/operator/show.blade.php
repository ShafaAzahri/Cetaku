@extends('admin.layout.admin')

@section('title', 'Detail Operator')

@section('styles')
<style>
    .operator-card {
        background-color: #fff;
        border-radius: 8px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        margin-bottom: 20px;
    }
    .operator-header {
        padding: 20px;
        border-bottom: 1px solid #eee;
    }
    .operator-content {
        padding: 20px;
    }
    .operator-avatar {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        background-color: #e0e7ff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 28px;
        color: #4f46e5;
        margin-right: 20px;
    }
    .info-row {
        display: flex;
        margin-bottom: 15px;
    }
    .info-label {
        width: 40%;
        color: #6c757d;
        font-weight: 500;
    }
    .info-value {
        width: 60%;
        font-weight: 400;
    }
    .status-badge {
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 12px;
        font-weight: 500;
    }
    .status-aktif { background-color: #d1fae5; color: #065f46; }
    .status-tidak_aktif { background-color: #fee2e2; color: #991b1b; }
    .assignment-card {
        background-color: #f9fafb;
        border-radius: 8px;
        padding: 15px;
        margin-top: 15px;
    }
    .table th {
        background-color: #f9fafb;
        font-size: 14px;
        font-weight: 600;
    }
    .nav-tabs .nav-link {
        color: #6c757d;
        font-weight: 500;
    }
    .nav-tabs .nav-link.active {
        color: #4f46e5;
        font-weight: 600;
    }
    .process-badge {
        padding: 3px 6px;
        border-radius: 3px;
        font-size: 11px;
    }
    .process-Mulai { background-color: #dbeafe; color: #1e40af; }
    .process-Selesai { background-color: #d1fae5; color: #065f46; }
    .process-Dikerjakan { background-color: #fef3c7; color: #92400e; }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">Detail Operator</h4>
        <a href="{{ route('admin.operators.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-arrow-left me-1"></i> Kembali
        </a>
    </div>

    <!-- Flash Messages -->
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <div class="row">
        <!-- Informasi Operator -->
        <div class="col-md-4">
            <div class="operator-card">
                <div class="operator-header d-flex align-items-center">
                    <div class="operator-avatar">
                        <i class="fas fa-user"></i>
                    </div>
                    <div>
                        <h5 class="mb-1">{{ $operator['nama'] }}</h5>
                        <p class="mb-0 text-muted">{{ $operator['posisi'] }}</p>
                    </div>
                </div>
                <div class="operator-content">
                    <div class="info-row">
                        <div class="info-label">ID</div>
                        <div class="info-value">{{ $operator['id'] }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Status</div>
                        <div class="info-value">
                            <span class="status-badge status-{{ $operator['status'] }}">
                                {{ $operator['status'] == 'aktif' ? 'Aktif' : 'Tidak Aktif' }}
                            </span>
                        </div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Kontak</div>
                        <div class="info-value">{{ $operator['kontak'] }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Tanggal Bergabung</div>
                        <div class="info-value">
                            {{ isset($operator['created_at']) ? \Carbon\Carbon::parse($operator['created_at'])->format('d M Y') : '-' }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Detail dan Pekerjaan Saat Ini -->
        <div class="col-md-8">
            <div class="operator-card">
                <div class="operator-header">
                    <ul class="nav nav-tabs border-0" id="operatorTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="current-tab" data-bs-toggle="tab" data-bs-target="#current" type="button" role="tab">Pekerjaan Saat Ini</button>
                        </li>
                    </ul>
                </div>
                <div class="operator-content">
                    <div class="tab-content" id="operatorTabsContent">
                        <!-- Tab Pekerjaan Saat Ini -->
                        <div class="tab-pane fade show active" id="current" role="tabpanel" aria-labelledby="current-tab">
                            @if(isset($operator['current_assignment']) && $operator['current_assignment'])
                                @php
                                    $assignment = $operator['current_assignment'];
                                    $detailPesanan = $assignment['detailPesanan'] ?? null;
                                    $custom = $detailPesanan['custom'] ?? null;
                                    $mesin = $assignment['mesin'] ?? null;
                                @endphp
                                <div class="assignment-card">
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <h6 class="mb-3">Informasi Pesanan</h6>
                                            <div class="info-row">
                                                <div class="info-label">ID Pesanan</div>
                                                <div class="info-value">
                                                    #{{ data_get($operator, 'current_assignment.detail_pesanan.pesanan.id', '-') }}
                                                </div>
                                            </div>
                                            <div class="info-row">
                                                <div class="info-label">Produk</div>
                                                <div class="info-value">
                                                    {{ data_get($operator, 'current_assignment.detail_pesanan.custom.item.nama_item', '-') }}
                                                </div>
                                            </div>
                                            <div class="info-row">
                                                <div class="info-label">Bahan</div>
                                                <div class="info-value">
                                                    {{ data_get($operator, 'current_assignment.detail_pesanan.custom.bahan.nama_bahan', '-') }}
                                                </div>
                                            </div>
                                            <div class="info-row">
                                                <div class="info-label">Ukuran</div>
                                                <div class="info-value">
                                                    {{ data_get($operator, 'current_assignment.detail_pesanan.custom.ukuran.size', '-') }}
                                                </div>
                                            </div>
                                            <div class="info-row">
                                                <div class="info-label">Jenis</div>
                                                <div class="info-value">
                                                    {{ data_get($operator, 'current_assignment.detail_pesanan.custom.jenis.kategori', '-') }}
                                                </div>
                                            </div>
                                            <div class="info-row">
                                                <div class="info-label">Jumlah</div>
                                                <div class="info-value">
                                                    {{ data_get($operator, 'current_assignment.detail_pesanan.jumlah', '-') }} pcs
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <h6 class="mb-3">Informasi Proses</h6>
                                            <div class="info-row">
                                                <div class="info-label">Status</div>
                                                <div class="info-value">
                                                    <span class="process-badge process-{{ $assignment['status_proses'] }}">
                                                        {{ $assignment['status_proses'] }}
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="info-row">
                                                <div class="info-label">Mesin</div>
                                                <div class="info-value">{{ $mesin['nama_mesin'] ?? '-' }}</div>
                                            </div>
                                            <div class="info-row">
                                                <div class="info-label">Mulai</div>
                                                <div class="info-value">
                                                    {{ isset($assignment['waktu_mulai']) ? \Carbon\Carbon::parse($assignment['waktu_mulai'])->format('d M Y, H:i') : '-' }}
                                                </div>
                                            </div>
                                            <div class="info-row">
                                                <div class="info-label">Catatan</div>
                                                <div class="info-value">{{ $assignment['catatan'] ?? '-' }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="text-center py-5">
                                    <i class="fas fa-briefcase fa-3x mb-3 text-muted"></i>
                                    <h5 class="text-muted">Tidak ada pekerjaan saat ini</h5>
                                    <p>Operator ini tidak sedang menangani pekerjaan apapun.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
@endsection
