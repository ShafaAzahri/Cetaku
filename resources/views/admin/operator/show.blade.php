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
                    
                    <div class="d-grid gap-2 mt-4">
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#statusModal">
                            <i class="fas fa-edit me-1"></i> Ubah Status
                        </button>
                    </div>
                    
                    @if(isset($summary) && count($summary) > 0)
                    <hr class="my-4">
                    <h6 class="mb-3">Ringkasan Pekerjaan</h6>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Total Diselesaikan:</span>
                        <span class="fw-bold">{{ $summary['total_completed'] ?? 0 }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Bulan Ini:</span>
                        <span class="fw-bold">{{ $summary['this_month'] ?? 0 }}</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span>Minggu Ini:</span>
                        <span class="fw-bold">{{ $summary['this_week'] ?? 0 }}</span>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        
        <!-- Detail dan Riwayat -->
        <div class="col-md-8">
            <div class="operator-card">
                <div class="operator-header">
                    <ul class="nav nav-tabs border-0" id="operatorTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="current-tab" data-bs-toggle="tab" data-bs-target="#current" type="button" role="tab">Pekerjaan Saat Ini</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="history-tab" data-bs-toggle="tab" data-bs-target="#history" type="button" role="tab">Riwayat Pekerjaan</button>
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
                                    
                                    <div class="mt-3 pt-3 border-top">
                                        <div class="d-flex justify-content-between">
                                            <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#updateStatusModal">
                                                <i class="fas fa-edit me-1"></i> Update Status
                                            </button>
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
                        
                        <!-- Tab Riwayat Pekerjaan -->
                        <div class="tab-pane fade" id="history" role="tabpanel" aria-labelledby="history-tab">
                            @if(isset($history['data']) && count($history['data']) > 0)
                                <div class="table-responsive">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>Tanggal</th>
                                                <th>Pesanan</th>
                                                <th>Produk</th>
                                                <th>Mesin</th>
                                                <th>Durasi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($history['data'] as $item)
                                            <tr>
                                                <td>{{ isset($item['waktu_selesai']) ? \Carbon\Carbon::parse($item['waktu_selesai'])->format('d M Y, H:i') : '-' }}</td>
                                                <td>#{{ $item['detailPesanan']['pesanan_id'] ?? '-' }}</td>
                                                <td>
                                                    {{ $item['detailPesanan']['custom']['item']['nama_item'] ?? 'Item' }}
                                                    ({{ $item['detailPesanan']['custom']['ukuran']['size'] ?? '-' }})
                                                </td>
                                                <td>{{ $item['mesin']['nama_mesin'] ?? '-' }}</td>
                                                <td>{{ $item['durasi_pengerjaan'] ?? '-' }}</td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                
                                @if(isset($history['last_page']) && $history['last_page'] > 1)
                                <div class="d-flex justify-content-center mt-4">
                                    <nav>
                                        <ul class="pagination">
                                            {{-- Pagination logic here --}}
                                        </ul>
                                    </nav>
                                </div>
                                @endif
                            @else
                                <div class="text-center py-5">
                                    <i class="fas fa-history fa-3x mb-3 text-muted"></i>
                                    <h5 class="text-muted">Belum ada riwayat pekerjaan</h5>
                                    <p>Operator ini belum menyelesaikan pekerjaan apapun.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Ubah Status -->
<div class="modal fade" id="statusModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Ubah Status Operator</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.operators.update-status', $operator['id']) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <p>Operator: <strong>{{ $operator['nama'] }}</strong></p>
                    <div class="mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select name="status" id="status" class="form-select" required>
                            <option value="aktif" {{ $operator['status'] == 'aktif' ? 'selected' : '' }}>Aktif</option>
                            <option value="tidak_aktif" {{ $operator['status'] == 'tidak_aktif' ? 'selected' : '' }}>Tidak Aktif</option>
                        </select>
                    </div>
                    
                    @if(isset($operator['current_assignment']) && $operator['current_assignment'])
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Operator ini sedang mengerjakan pesanan. Anda tidak dapat mengubah status menjadi tidak aktif.
                    </div>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" {{ isset($operator['current_assignment']) && $operator['current_assignment'] ? 'disabled' : '' }}>Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Update Status Pekerjaan -->
@if(isset($operator['current_assignment']) && $operator['current_assignment'])
<div class="modal fade" id="updateStatusModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Update Status Pekerjaan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.proses-produksi.update-status', $operator['current_assignment']['id']) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <p>Proses ID: <strong>#{{ $operator['current_assignment']['id'] }}</strong></p>
                    <p>Pesanan: <strong>#{{ $operator['current_assignment']['detailPesanan']['pesanan_id'] ?? '-' }}</strong></p>
                    <p>Produk: <strong>{{ $operator['current_assignment']['detailPesanan']['custom']['item']['nama_item'] ?? 'Item' }}</strong></p>
                    
                    <div class="mb-3">
                        <label for="status_proses" class="form-label">Status Proses</label>
                        <select name="status_proses" id="status_proses" class="form-select" required>
                            <option value="Mulai" {{ $operator['current_assignment']['status_proses'] == 'Mulai' ? 'selected' : '' }}>Mulai</option>
                            <option value="Sedang Dikerjakan" {{ $operator['current_assignment']['status_proses'] == 'Sedang Dikerjakan' ? 'selected' : '' }}>Sedang Dikerjakan</option>
                            <option value="Pause" {{ $operator['current_assignment']['status_proses'] == 'Pause' ? 'selected' : '' }}>Pause</option>
                            <option value="Selesai" {{ $operator['current_assignment']['status_proses'] == 'Selesai' ? 'selected' : '' }}>Selesai</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="catatan" class="form-label">Catatan (Opsional)</label>
                        <textarea name="catatan" id="catatan" rows="3" class="form-control">{{ $operator['current_assignment']['catatan'] }}</textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Script untuk menghandle perubahan status operator
        const statusSelect = document.getElementById('status');
        if (statusSelect) {
            statusSelect.addEventListener('change', function() {
                const submitBtn = this.closest('form').querySelector('button[type="submit"]');
                const warningDiv = this.closest('.modal-body').querySelector('.alert-warning');
                
                if (this.value === 'tidak_aktif' && warningDiv) {
                    submitBtn.disabled = true;
                } else {
                    submitBtn.disabled = false;
                }
            });
        }
    });
</script>
@endsection