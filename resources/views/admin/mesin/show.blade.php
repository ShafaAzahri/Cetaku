@extends('admin.layout.admin')

@section('title', 'Detail Mesin')

@section('styles')
<style>
    .mesin-card {
        background-color: #fff;
        border-radius: 8px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        margin-bottom: 20px;
    }
    
    .mesin-header {
        padding: 20px;
        border-bottom: 1px solid #eee;
    }
    
    .mesin-content {
        padding: 20px;
    }
    
    .mesin-icon {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        background-color: #e0f2fe;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 28px;
        color: #0284c7;
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
    .status-digunakan { background-color: #dbeafe; color: #1e40af; }
    .status-maintenance { background-color: #fef3c7; color: #92400e; }
    
    .usage-card {
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
        color: #0284c7;
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
        <h4 class="mb-0">Detail Mesin</h4>
        <a href="{{ route('admin.mesins.index') }}" class="btn btn-outline-secondary btn-sm">
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
        <!-- Informasi Mesin -->
        <div class="col-md-4">
            <div class="mesin-card">
                <div class="mesin-header d-flex align-items-center">
                    <div class="mesin-icon">
                        <i class="fas fa-print"></i>
                    </div>
                    <div>
                        <h5 class="mb-1">{{ $mesin['nama_mesin'] }}</h5>
                        <p class="mb-0 text-muted">{{ $mesin['tipe_mesin'] }}</p>
                    </div>
                </div>
                <div class="mesin-content">
                    <div class="info-row">
                        <div class="info-label">ID</div>
                        <div class="info-value">{{ $mesin['id'] }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Status</div>
                        <div class="info-value">
                            <span class="status-badge status-{{ $mesin['status'] }}">
                                {{ ucfirst($mesin['status']) }}
                            </span>
                        </div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Tanggal Terdaftar</div>
                        <div class="info-value">
                            {{ isset($mesin['created_at']) ? \Carbon\Carbon::parse($mesin['created_at'])->format('d M Y') : '-' }}
                        </div>
                    </div>
                    
                    <div class="d-grid gap-2 mt-4">
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#statusModal">
                            <i class="fas fa-edit me-1"></i> Ubah Status
                        </button>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Detail dan Riwayat -->
        <div class="col-md-8">
            <div class="mesin-card">
                <div class="mesin-header">
                    <ul class="nav nav-tabs border-0" id="mesinTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="current-tab" data-bs-toggle="tab" data-bs-target="#current" type="button" role="tab">Penggunaan Saat Ini</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="history-tab" data-bs-toggle="tab" data-bs-target="#history" type="button" role="tab">Riwayat Penggunaan</button>
                        </li>
                    </ul>
                </div>
                <div class="mesin-content">
                    <div class="tab-content" id="mesinTabsContent">
                        <!-- Tab Penggunaan Saat Ini -->
                        <div class="tab-pane fade show active" id="current" role="tabpanel" aria-labelledby="current-tab">
                            @if(isset($mesin['current_usage']) && $mesin['current_usage'])
                                @php
                                    $usage = $mesin['current_usage'];
                                    $detailPesanan = $usage['detailPesanan'] ?? null;
                                    $custom = $detailPesanan['custom'] ?? null;
                                    $operator = $usage['operator'] ?? null;
                                @endphp
                                
                                <div class="usage-card">
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <h6 class="mb-3">Informasi Pesanan</h6>
                                            <div class="info-row">
                                                <div class="info-label">Nomor Pesanan</div>
                                                <div class="info-value">#{{ $detailPesanan['pesanan_id'] ?? '-' }}</div>
                                            </div>
                                            <div class="info-row">
                                                <div class="info-label">Produk</div>
                                                <div class="info-value">{{ $custom['item']['nama_item'] ?? '-' }}</div>
                                            </div>
                                            <div class="info-row">
                                                <div class="info-label">Bahan</div>
                                                <div class="info-value">{{ $custom['bahan']['nama_bahan'] ?? '-' }}</div>
                                            </div>
                                            <div class="info-row">
                                                <div class="info-label">Ukuran</div>
                                                <div class="info-value">{{ $custom['ukuran']['size'] ?? '-' }}</div>
                                            </div>
                                            <div class="info-row">
                                                <div class="info-label">Jumlah</div>
                                                <div class="info-value">{{ $detailPesanan['jumlah'] ?? '-' }} pcs</div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <h6 class="mb-3">Informasi Proses</h6>
                                            <div class="info-row">
                                                <div class="info-label">Status</div>
                                                <div class="info-value">
                                                    <span class="process-badge process-{{ $usage['status_proses'] }}">
                                                        {{ $usage['status_proses'] }}
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="info-row">
                                                <div class="info-label">Operator</div>
                                                <div class="info-value">
                                                    <a href="{{ route('admin.operators.show', $operator['id'] ?? 0) }}">
                                                        {{ $operator['nama'] ?? '-' }}
                                                    </a>
                                                </div>
                                            </div>
                                            <div class="info-row">
                                                <div class="info-label">Mulai</div>
                                                <div class="info-value">
                                                    {{ isset($usage['waktu_mulai']) ? \Carbon\Carbon::parse($usage['waktu_mulai'])->format('d M Y, H:i') : '-' }}
                                                </div>
                                            </div>
                                            <div class="info-row">
                                                <div class="info-label">Catatan</div>
                                                <div class="info-value">{{ $usage['catatan'] ?? '-' }}</div>
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
                                    <i class="fas fa-print fa-3x mb-3 text-muted"></i>
                                    <h5 class="text-muted">Tidak ada penggunaan saat ini</h5>
                                    <p>Mesin ini tidak sedang digunakan.</p>
                                </div>
                            @endif
                        </div>
                        
                        <!-- Tab Riwayat Penggunaan -->
                        <div class="tab-pane fade" id="history" role="tabpanel" aria-labelledby="history-tab">
                            @if(isset($usage_history['data']) && count($usage_history['data']) > 0)
                                <div class="table-responsive">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>Tanggal</th>
                                                <th>Pesanan</th>
                                                <th>Produk</th>
                                                <th>Operator</th>
                                                <th>Durasi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($usage_history['data'] as $item)
                                            <tr>
                                                <td>{{ isset($item['waktu_selesai']) ? \Carbon\Carbon::parse($item['waktu_selesai'])->format('d M Y, H:i') : '-' }}</td>
                                                <td>#{{ $item['detailPesanan']['pesanan_id'] ?? '-' }}</td>
                                                <td>
                                                    {{ $item['detailPesanan']['custom']['item']['nama_item'] ?? 'Item' }}
                                                    ({{ $item['detailPesanan']['custom']['ukuran']['size'] ?? '-' }})
                                                </td>
                                                <td>
                                                    <a href="{{ route('admin.operators.show', $item['operator']['id'] ?? 0) }}">
                                                        {{ $item['operator']['nama'] ?? '-' }}
                                                    </a>
                                                </td>
                                                <td>{{ $item['durasi_penggunaan'] ?? '-' }}</td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                
                                @if(isset($usage_history['last_page']) && $usage_history['last_page'] > 1)
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
                                    <h5 class="text-muted">Belum ada riwayat penggunaan</h5>
                                    <p>Mesin ini belum pernah digunakan sebelumnya.</p>
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
                <h5 class="modal-title">Ubah Status Mesin</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.mesins.update-status', $mesin['id']) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <p>Mesin: <strong>{{ $mesin['nama_mesin'] }}</strong></p>
                    <div class="mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select name="status" id="status" class="form-select" required>
                            <option value="aktif" {{ $mesin['status'] == 'aktif' ? 'selected' : '' }}>Aktif</option>
                            <option value="digunakan" {{ $mesin['status'] == 'digunakan' ? 'selected' : '' }}>Digunakan</option>
                            <option value="maintenance" {{ $mesin['status'] == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                        </select>
                    </div>
                    
                    @if(isset($mesin['current_usage']) && $mesin['current_usage'])
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Mesin ini sedang digunakan. Anda tidak dapat mengubah status selama sedang digunakan.
                    </div>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" {{ isset($mesin['current_usage']) && $mesin['current_usage'] ? 'disabled' : '' }}>Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Update Status Proses Penggunaan -->
@if(isset($mesin['current_usage']) && $mesin['current_usage'])
<div class="modal fade" id="updateStatusModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Update Status Proses</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.proses-produksi.update-status', $mesin['current_usage']['id']) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <p>Proses ID: <strong>#{{ $mesin['current_usage']['id'] }}</strong></p>
                    <p>Pesanan: <strong>#{{ $mesin['current_usage']['detailPesanan']['pesanan_id'] ?? '-' }}</strong></p>
                    <p>Produk: <strong>{{ $mesin['current_usage']['detailPesanan']['custom']['item']['nama_item'] ?? 'Item' }}</strong></p>
                    
                    <div class="mb-3">
                        <label for="status_proses" class="form-label">Status Proses</label>
                        <select name="status_proses" id="status_proses" class="form-select" required>
                            <option value="Mulai" {{ $mesin['current_usage']['status_proses'] == 'Mulai' ? 'selected' : '' }}>Mulai</option>
                            <option value="Sedang Dikerjakan" {{ $mesin['current_usage']['status_proses'] == 'Sedang Dikerjakan' ? 'selected' : '' }}>Sedang Dikerjakan</option>
                            <option value="Pause" {{ $mesin['current_usage']['status_proses'] == 'Pause' ? 'selected' : '' }}>Pause</option>
                            <option value="Selesai" {{ $mesin['current_usage']['status_proses'] == 'Selesai' ? 'selected' : '' }}>Selesai</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="catatan" class="form-label">Catatan (Opsional)</label>
                        <textarea name="catatan" id="catatan" rows="3" class="form-control">{{ $mesin['current_usage']['catatan'] }}</textarea>
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
        // Script untuk menghandle perubahan status mesin
        const statusSelect = document.getElementById('status');
        if (statusSelect) {
            statusSelect.addEventListener('change', function() {
                const submitBtn = this.closest('form').querySelector('button[type="submit"]');
                const warningDiv = this.closest('.modal-body').querySelector('.alert-warning');
                
                if ((this.value !== 'digunakan') && warningDiv) {
                    submitBtn.disabled = true;
                } else {
                    submitBtn.disabled = false;
                }
            });
        }
    });
</script>
@endsection