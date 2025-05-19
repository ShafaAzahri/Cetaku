@extends('superadmin.layout.superadmin')

@section('title', 'Manajemen Admin')

@section('styles')
<style>
    .admin-card {
        background-color: #fff;
        border-radius: 8px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        margin-bottom: 20px;
    }
    
    .admin-card .card-header {
        padding: 15px 20px;
        border-bottom: 1px solid #eee;
    }
    
    .admin-card .card-title {
        font-size: 18px;
        font-weight: 600;
        margin-bottom: 0;
    }
    
    .admin-table th {
        font-weight: 600;
        color: #4b5563;
    }
    
    .admin-table td {
        vertical-align: middle;
    }
    
    .admin-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        object-fit: cover;
    }
    
    .btn-action {
        width: 32px;
        height: 32px;
        border-radius: 6px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        margin-right: 5px;
        transition: all 0.2s;
    }
    
    .btn-action:hover {
        transform: translateY(-3px);
    }
    
    .search-box {
        position: relative;
        margin-bottom: 20px;
    }
    
    .search-box .search-icon {
        position: absolute;
        left: 10px;
        top: 10px;
        color: #6c757d;
    }
    
    .search-box .form-control {
        padding-left: 35px;
        border-radius: 8px;
    }
    
    .filter-box {
        display: flex;
        gap: 10px;
        margin-bottom: 20px;
    }
    
    .status-badge {
        padding: 3px 8px;
        border-radius: 12px;
        font-size: 12px;
        font-weight: 500;
    }
    
    .status-badge.active {
        background-color: #d1fae5;
        color: #065f46;
    }
    
    .status-badge.inactive {
        background-color: #fee2e2;
        color: #991b1b;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">Manajemen Admin</h4>
        <a href="{{ route('superadmin.admin.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i> Tambah Admin
        </a>
    </div>
    
    <!-- Alert Messages -->
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
    
    <div class="admin-card">
        <div class="card-header">
            <form action="{{ route('superadmin.admin.index') }}" method="GET">
                <div class="row g-3 align-items-center">
                    <div class="col-md-8">
                        <div class="search-box">
                            <i class="fas fa-search search-icon"></i>
                            <input type="text" class="form-control" name="search" placeholder="Cari Admin..." value="{{ $search ?? '' }}">
                        </div>
                    </div>
                    <div class="col-md-4 text-md-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search me-1"></i> Cari
                        </button>
                        <a href="{{ route('superadmin.admin.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-redo me-1"></i> Reset
                        </a>
                    </div>
                </div>
            </form>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table admin-table">
                    <thead>
                        <tr>
                            <th width="5%">#</th>
                            <th width="15%">Avatar</th>
                            <th width="20%">Nama</th>
                            <th width="25%">Email</th>
                            <th width="15%">Terakhir Login</th>
                            <th width="20%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($admins as $key => $admin)
                        <tr>
                            <td>{{ ($pagination['current_page'] - 1) * $pagination['per_page'] + $key + 1 }}</td>
                            <td>
                                <img src="https://ui-avatars.com/api/?name={{ urlencode($admin['nama']) }}&background=4361ee&color=fff" class="admin-avatar" alt="{{ $admin['nama'] }}">
                            </td>
                            <td>{{ $admin['nama'] }}</td>
                            <td>{{ $admin['email'] }}</td>
                            <td>
                                @if(isset($admin['last_login_at']))
                                    {{ \Carbon\Carbon::parse($admin['last_login_at'])->format('d M Y, H:i') }}
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('superadmin.admin.show', $admin['id']) }}" class="btn btn-info btn-action" title="Detail">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('superadmin.admin.edit', $admin['id']) }}" class="btn btn-warning btn-action" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button type="button" class="btn btn-primary btn-action" title="Reset Password" onclick="resetPassword({{ $admin['id'] }}, '{{ $admin['nama'] }}')">
                                    <i class="fas fa-key"></i>
                                </button>
                                <button type="button" class="btn btn-danger btn-action" title="Hapus" onclick="confirmDelete({{ $admin['id'] }}, '{{ $admin['nama'] }}')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-3">Tidak ada data admin</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            @if(isset($pagination) && $pagination['total_pages'] > 1)
            <div class="d-flex justify-content-center mt-4">
                <nav aria-label="Page navigation">
                    <ul class="pagination">
                        <li class="page-item {{ $pagination['current_page'] == 1 ? 'disabled' : '' }}">
                            <a class="page-link" href="{{ route('superadmin.admin.index', ['page' => 1, 'search' => $search ?? '']) }}" aria-label="First">
                                <span aria-hidden="true">&laquo;&laquo;</span>
                            </a>
                        </li>
                        <li class="page-item {{ $pagination['current_page'] == 1 ? 'disabled' : '' }}">
                            <a class="page-link" href="{{ route('superadmin.admin.index', ['page' => $pagination['current_page'] - 1, 'search' => $search ?? '']) }}" aria-label="Previous">
                                <span aria-hidden="true">&laquo;</span>
                            </a>
                        </li>
                        
                        @for($i = max(1, $pagination['current_page'] - 2); $i <= min($pagination['current_page'] + 2, $pagination['total_pages']); $i++)
                            <li class="page-item {{ $i == $pagination['current_page'] ? 'active' : '' }}">
                                <a class="page-link" href="{{ route('superadmin.admin.index', ['page' => $i, 'search' => $search ?? '']) }}">{{ $i }}</a>
                            </li>
                        @endfor
                        
                        <li class="page-item {{ $pagination['current_page'] == $pagination['total_pages'] ? 'disabled' : '' }}">
                            <a class="page-link" href="{{ route('superadmin.admin.index', ['page' => $pagination['current_page'] + 1, 'search' => $search ?? '']) }}" aria-label="Next">
                                <span aria-hidden="true">&raquo;</span>
                            </a>
                        </li>
                        <li class="page-item {{ $pagination['current_page'] == $pagination['total_pages'] ? 'disabled' : '' }}">
                            <a class="page-link" href="{{ route('superadmin.admin.index', ['page' => $pagination['total_pages'], 'search' => $search ?? '']) }}" aria-label="Last">
                                <span aria-hidden="true">&raquo;&raquo;</span>
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
            @endif
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
                <p>Apakah Anda yakin ingin menghapus admin <strong id="delete-admin-name"></strong>?</p>
                <p class="text-danger">Tindakan ini tidak dapat dibatalkan.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <form id="delete-form" method="POST">
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
                <p>Apakah Anda yakin ingin mereset password untuk <strong id="reset-admin-name"></strong>?</p>
                <p>Password baru akan ditampilkan setelah reset berhasil.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <form id="reset-password-form" method="POST">
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
                <p>Password untuk admin <strong id="admin-name-with-new-password"></strong> telah direset.</p>
                <div class="alert alert-info">
                    <p class="mb-0">Password baru: <strong id="new-password"></strong></p>
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
    function confirmDelete(id, name) {
        document.getElementById('delete-admin-name').textContent = name;
        document.getElementById('delete-form').action = "{{ route('superadmin.admin.index') }}/" + id;
        new bootstrap.Modal(document.getElementById('deleteModal')).show();
    }
    
    function resetPassword(id, name) {
        document.getElementById('reset-admin-name').textContent = name;
        document.getElementById('reset-password-form').action = "{{ route('superadmin.admin.index') }}/" + id + "/reset-password";
        new bootstrap.Modal(document.getElementById('resetPasswordModal')).show();
    }
    
    document.addEventListener('DOMContentLoaded', function() {
        // Tampilkan password baru jika ada dari session
        @if(session('new_password'))
            document.getElementById('admin-name-with-new-password').textContent = "{{ session('admin_name') ?? 'Admin' }}";
            document.getElementById('new-password').textContent = "{{ session('new_password') }}";
            new bootstrap.Modal(document.getElementById('newPasswordModal')).show();
        @endif
    });
</script>
@endsection