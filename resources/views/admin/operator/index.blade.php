@extends('admin.layout.admin')

@section('title', 'Daftar Operator')

@section('styles')
<style>
    .status-badge {
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 12px;
        font-weight: 500;
    }
    .status-aktif { background-color: #d1fae5; color: #065f46; }
    .status-tidak_aktif { background-color: #fee2e2; color: #991b1b; }
    
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
    
    .assignment-badge {
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
        <h4 class="mb-0">Daftar Operator</h4>
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
            <form action="{{ route('admin.operators.index') }}" method="GET" class="row g-3">
                <!-- Filters -->
                <div class="col-md-4">
                    <label for="search" class="form-label">Cari Operator</label>
                    <input type="text" class="form-control" id="search" name="search" placeholder="Nama operator..." value="{{ $search ?? '' }}">
                </div>
                <div class="col-md-3">
                    <label for="posisi" class="form-label">Posisi</label>
                    <input type="text" class="form-control" id="posisi" name="posisi" placeholder="Posisi operator..." value="{{ $posisi ?? '' }}">
                </div>
                <div class="col-md-2">
                    <label for="status" class="form-label">Status</label>
                    <select name="status" id="status" class="form-select">
                        <option value="">Semua Status</option>
                        <option value="aktif" {{ ($status ?? '') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="tidak_aktif" {{ ($status ?? '') == 'tidak_aktif' ? 'selected' : '' }}>Tidak Aktif</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="fas fa-search me-1"></i> Cari
                    </button>
                    <a href="{{ route('admin.operators.index') }}" class="btn btn-outline-secondary">
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
                            <th>Nama</th>
                            <th>Posisi</th>
                            <th>Kontak</th>
                            <th>Status</th>
                            <th>Pekerjaan Saat Ini</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($operators as $operator)
                        <tr>
                            <td>{{ $operator['id'] }}</td>
                            <td>{{ $operator['nama'] }}</td>
                            <td>{{ $operator['posisi'] }}</td>
                            <td>{{ $operator['kontak'] }}</td>
                            <td>
                                <span class="status-badge status-{{ $operator['status'] }}">
                                    {{ $operator['status'] == 'aktif' ? 'Aktif' : 'Tidak Aktif' }}
                                </span>
                            </td>
                            <td>
                                @if(isset($operator['current_assignment']) && $operator['current_assignment'])
                                    <span class="assignment-badge">
                                        {{ $operator['current_assignment']['detailPesanan']['custom']['item']['nama_item'] ?? 'Item' }}
                                        (Pesanan #{{ $operator['current_assignment']['detailPesanan']['pesanan_id'] ?? '-' }})
                                    </span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex gap-1">
                                    <a href="{{ route('admin.operators.show', $operator['id']) }}" class="action-btn info" title="Detail">
                                        <i class="fas fa-info-circle"></i>
                                    </a>
                                    
                                    <button type="button" class="action-btn edit" title="Ubah Status" 
                                            data-bs-toggle="modal" data-bs-target="#statusModal{{ $operator['id'] }}">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-4">Tidak ada data operator</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Status Modals -->
@foreach($operators as $operator)
<div class="modal fade" id="statusModal{{ $operator['id'] }}" tabindex="-1" aria-hidden="true">
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
                        <label for="status{{ $operator['id'] }}" class="form-label">Status</label>
                        <select name="status" id="status{{ $operator['id'] }}" class="form-select" required>
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
                
                if (this.value === 'tidak_aktif' && warningDiv) {
                    submitBtn.disabled = true;
                } else {
                    submitBtn.disabled = false;
                }
            });
        });
    });
</script>
@endsection