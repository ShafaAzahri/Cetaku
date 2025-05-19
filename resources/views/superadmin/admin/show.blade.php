@extends('superadmin.layout.superadmin')

@section('title', 'Detail Admin')

@section('styles')
<style>
    .admin-detail-card {
        background-color: #fff;
        border-radius: 8px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        margin-bottom: 20px;
    }
    
    .admin-detail-card .card-header {
        padding: 15px 20px;
        border-bottom: 1px solid #eee;
    }
    
    .admin-detail-card .card-title {
        font-size: 18px;
        font-weight: 600;
        margin-bottom: 0;
    }
    
    .admin-detail-card .card-body {
        padding: 20px;
    }
    
    .admin-avatar-large {
        width: 100px;
        height: 100px;
        border-radius: 50%;
        object-fit: cover;
        margin-right: 20px;
    }
    
    .admin-name {
        font-size: 24px;
        font-weight: 600;
        margin-bottom: 5px;
    }
    
    .admin-email {
        color: #6c757d;
        margin-bottom: 0;
    }
    
    .admin-info-item {
        margin-bottom: 15px;
    }
    
    .admin-info-label {
        font-weight: 500;
        color: #4b5563;
        margin-bottom: 5px;
    }
    
    .admin-info-value {
        color: #111827;
    }
    
    .action-btn {
        margin-right: 10px;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">Detail Admin</h4>
        <a href="{{ route('superadmin.admin.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i> Kembali
        </a>
    </div>
    
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
    
    <div class="admin-detail-card">
        <div class="card-header">
            <h5 class="card-title">Informasi Admin</h5>
        </div>
        <div class="card-body">
            <div class="d-flex align-items-center mb-4">
                <img src="https://ui-avatars.com/api/?name={{ urlencode($admin['nama']) }}&background=4361ee&color=fff&size=100" class="admin-avatar-large" alt="{{ $admin['nama'] }}">
                <div>
                    <h5 class="admin-name">{{ $admin['nama'] }}</h5>
                    <p class="admin-email">{{ $admin['email'] }}</p>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="admin-info-item">
                        <div class="admin-info-label">Tanggal Pendaftaran</div>
                        <div class="admin-info-value">
                            @if(isset($admin['created_at']))
                                {{ \Carbon\Carbon::parse($admin['created_at'])->format('d M Y, H:i') }}
                            @else
                                -
                            @endif
                        </div>
                    </div>
                    
                    <div class="admin-info-item">
                        <div class="admin-info-label">Tanggal Update</div>
                        <div class="admin-info-value">
                            @if(isset($admin['updated_at']))
                                {{ \Carbon\Carbon::parse($admin['updated_at'])->format('d M Y, H:i') }}
                            @else
                                -
                            @endif
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="admin-info-item">
                        <div class="admin-info-label">Terakhir Login</div>
                        <div class="admin-info-value">
                            @if(isset($admin['last_login_at']))
                                {{ \Carbon\Carbon::parse($admin['last_login_at'])->format('d M Y, H:i') }}
                            @else
                                -
                            @endif
                        </div>
                    </div>
                    
                    <div class="admin-info-item">
                        <div class="admin-info-label">IP Terakhir Login</div>
                        <div class="admin-info-value">{{ $admin['last_login_ip'] ?? '-' }}</div>
                    </div>
                </div>
            </div>
            
            <div class="mt-4 d-flex">
                <a href="{{ route('superadmin.admin.edit', $admin['id']) }}" class="btn btn-warning action-btn">
                    <i class="fas fa-edit me-1"></i> Edit
                </a>
                <button type="button" class="btn btn-primary action-btn" onclick="resetPassword()">
                    <i class="fas fa-key me-1"></i> Reset Password
                </button>
                <button type="button" class="btn btn-danger action-btn" onclick="confirmDelete()">
                    <i class="fas fa-trash me-1"></i> Hapus
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Konfirmasi Hapus -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Konfirmasi Hapus</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin menghapus admin <strong>{{ $admin['nama'] }}</strong>?</p>
                <p class="text-danger">Tindakan ini tidak dapat dibatalkan.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <form action="{{ route('superadmin.admin.destroy', $admin['id']) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Hapus</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Reset Password -->
<div class="modal fade" id="resetPasswordModal" tabindex="-1" aria-labelledby="resetPasswordModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="resetPasswordModalLabel">Reset Password</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin mereset password untuk <strong>{{ $admin['nama'] }}</strong>?</p>
                <p>Password baru akan ditampilkan setelah reset berhasil.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <form action="{{ route('superadmin.admin.reset-password', $admin['id']) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-primary">Reset Password</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Password Baru -->
<div class="modal fade" id="newPasswordModal" tabindex="-1" aria-labelledby="newPasswordModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="newPasswordModalLabel">Password Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Password untuk admin <strong>{{ $admin['nama'] }}</strong> telah direset.</p>
                <div class="alert alert-info">
                    <p class="mb-0">Password baru: <strong id="new-password">{{ session('new_password') }}</strong></p>
                </div>
                <p class="text-danger">Pastikan Anda menyimpan password ini di tempat yang aman, karena ini hanya akan ditampilkan sekali.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Mengerti</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function confirmDelete() {
        new bootstrap.Modal(document.getElementById('deleteModal')).show();
    }
    
    function resetPassword() {
        new bootstrap.Modal(document.getElementById('resetPasswordModal')).show();
    }
    
    document.addEventListener('DOMContentLoaded', function() {
        // Tampilkan password baru jika ada dari session
        @if(session('new_password'))
            new bootstrap.Modal(document.getElementById('newPasswordModal')).show();
        @endif
    });
</script>
@endsection