@extends('admin.layout.admin')

@section('title', 'Daftar Mesin')

@section('styles')
<style>
    .status-badge {
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 12px;
        font-weight: 500;
    }
    .status-aktif { background-color: #d1fae5; color: #065f46; }
    .status-digunakan { background-color: #dbeafe; color: #1e40af; }
    .status-maintenance { background-color: #fef3c7; color: #92400e; }
    
    .table th {
        background-color: #f9fafb;
        font-size: 14px;
        font-weight: 600;
    }
    
    .action-btn {
        width: 32px;
        height: 32px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: #f1f5f9;
        border-radius: 4px;
        border: none;
        cursor: pointer;
        color: #475569;
        transition: all 0.2s;
    }
    
    .action-btn:hover {
        background: #e2e8f0;
    }
    
    .action-btn.info { color: #3b82f6; }
    .action-btn.edit { color: #10b981; }
    
    .usage-badge {
        padding: 3px 6px;
        border-radius: 3px;
        font-size: 11px;
        background-color: #dbeafe;
        color: #1e40af;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">Daftar Mesin</h4>
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
    
    <div class="card mb-4">
        <div class="card-body">
            <form action="{{ route('admin.mesins.index') }}" method="GET" class="row g-3">
                <!-- Filters -->
                <div class="col-md-4">
                    <label for="search" class="form-label">Cari Mesin</label>
                    <input type="text" class="form-control" id="search" name="search" placeholder="Nama mesin..." value="{{ $search ?? '' }}">
                </div>
                <div class="col-md-3">
                    <label for="tipe" class="form-label">Tipe Mesin</label>
                    <input type="text" class="form-control" id="tipe" name="tipe" placeholder="Tipe mesin..." value="{{ $tipe ?? '' }}">
                </div>
                <div class="col-md-2">
                    <label for="status" class="form-label">Status</label>
                    <select name="status" id="status" class="form-select">
                        <option value="">Semua Status</option>
                        <option value="aktif" {{ ($status ?? '') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="digunakan" {{ ($status ?? '') == 'digunakan' ? 'selected' : '' }}>Digunakan</option>
                        <option value="maintenance" {{ ($status ?? '') == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="fas fa-search me-1"></i> Cari
                    </button>
                    <a href="{{ route('admin.mesins.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-redo me-1"></i> Reset
                    </a>
                </div>
            </form>
        </div>
    </div>
    
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nama Mesin</th>
                            <th>Tipe</th>
                            <th>Status</th>
                            <th>Penggunaan Saat Ini</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($mesins as $mesin)
                        <tr>
                            <td>{{ $mesin['id'] }}</td>
                            <td>{{ $mesin['nama_mesin'] }}</td>
                            <td>{{ $mesin['tipe_mesin'] }}</td>
                            <td>
                                <span class="status-badge status-{{ $mesin['status'] }}">
                                    {{ ucfirst($mesin['status']) }}
                                </span>
                            </td>
                            <td>
                                @if(isset($mesin['current_usage']) && $mesin['current_usage'])
                                    <span class="usage-badge">
                                        {{ $mesin['current_usage']['detailPesanan']['custom']['item']['nama_item'] ?? 'Item' }}
                                        ({{ $mesin['current_usage']['operator']['nama'] ?? 'Operator' }})
                                    </span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex gap-1">
                                    <a href="{{ route('admin.mesins.show', $mesin['id']) }}" class="action-btn info" title="Detail">
                                        <i class="fas fa-info-circle"></i>
                                    </a>
                                    
                                    <button type="button" class="action-btn edit" title="Ubah Status" 
                                            data-bs-toggle="modal" data-bs-target="#statusModal{{ $mesin['id'] }}">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-4">Tidak ada data mesin</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Status Modals -->
@foreach($mesins as $mesin)tus -->
<div class="modal fade" id="statusModal{{ $mesin['id'] }}" tabindex="-1" aria-hidden="true">
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
                        <label for="status{{ $mesin['id'] }}" class="form-label">Status</label>
                        <select name="status" id="status{{ $mesin['id'] }}" class="form-select" required>
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
@endforeach
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Script untuk menghandle perubahan status
        const statusSelects = document.querySelectorAll('[id^="status"]');
        statusSelects.forEach(select => {
            select.addEventListener('change', function() {
                const modalId = this.id.replace('status', '');
                const submitBtn = this.closest('form').querySelector('button[type="submit"]');
                const warningDiv = this.closest('.modal-body').querySelector('.alert-warning');
                
                if ((this.value !== 'digunakan') && warningDiv) {
                    submitBtn.disabled = true;
                } else {
                    submitBtn.disabled = false;
                }
            });
        });
    });
</script>
@endsection