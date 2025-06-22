@extends('superadmin.layout.superadmin')

@section('title', 'Daftar Operator')

@section('styles')
<style>
    .operator-card {
    background-color: #fff;
    border-radius: 8px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    margin-bottom: 20px;
    }

    .operator-card .card-header {
    padding: 15px 20px;
    border-bottom: 1px solid #eee;
    }

    .operator-table th {
    font-weight: 600;
    font-size: 16px;
    color: #4b5563;
    }

    .operator-table td {
    font-weight: normal;
    vertical-align: middle;
    }

    .operator-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    object-fit: cover;
    }
    
    .btn-action {
    border: none;
    color: #fff;
    margin-right: 5px;
    }

    .btn-view {
    background-color: #17a2b8;
    }

    .btn-edit {
    background-color: #ffc107;
    }

    .btn-password {
    background-color: #6f42c1;
    }

    .btn-delete {
    background-color: #dc3545;
    }
</style>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="mb-0">Daftar Operator</h4>
            <a href="{{ route('superadmin.operator.create') }}" class="btn btn-success">
                <i class="fas fa-plus me-1"></i> Tambah Operator
            </a>
        </div>

        {{-- Alert --}}
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @elseif(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <div class="operator-card">
            <div class="card-header">
                {{-- Filter --}}
                <form method="GET" action="{{ route('superadmin.operator.index') }}" class="mb-3">
                    <div class="row">
                        <div class="col-md-7">
                            <input type="text" name="search" class="form-control" placeholder="Cari nama / kontak..." value="{{ request('search') }}">
                        </div>
                        <div class="col-md-3">
                            <select name="status" class="form-control" onchange="this.form.submit()">
                                <option value="">Semua Status</option>
                                <option value="aktif" {{ request('status') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                                <option value="tidak_aktif" {{ request('status') == 'tidak_aktif' ? 'selected' : '' }}>Tidak Aktif</option>
                            </select>
                        </div>
                        <!--
                        <div class="col-md-3">
                            <select name="posisi" class="form-control">
                                <option value="">Semua Posisi</option>
                                <option value="admin" {{ request('posisi') == 'admin' ? 'selected' : '' }}>Admin</option>
                                <option value="operator" {{ request('posisi') == 'operator' ? 'selected' : '' }}>Operator</option>
                                {{-- Tambahkan posisi lainnya sesuai kebutuhan --}}
                            </select>
                        </div>
                        -->
                        <div class="col-md-2 text-end">
                            <button class="btn btn-primary w-50">Filter</button>
                            <a href="{{ route('superadmin.operator.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-redo me-1"></i> Reset
                            </a>
                        </div>
                    </div>
                </form>
            </div>

            {{-- Tabel --}}
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table operator-table">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Avatar</th>
                                <th>Nama</th>
                                <th>Posisi</th>
                                <th>Kontak</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>

                        @forelse($operators as $key => $operator)
                            @php
                                $nama = is_array($operator) ? $operator['nama'] : $operator->nama;
                                $initial = collect(explode(' ', $nama))->map(fn($n) => strtoupper($n[0]))->implode('');
                                $posisi = is_array($operator) ? $operator['posisi'] : $operator->posisi;
                                $kontak = is_array($operator) ? $operator['kontak'] : $operator->kontak;
                                $status = is_array($operator) ? $operator['status'] : $operator->status;
                                $id = is_array($operator) ? $operator['id'] : $operator->id;
                            @endphp
                            <tr>
                                <td>{{ $loop->iteration }}</td>

                                {{-- Avatar --}}
                                <td>
                                <img src="https://ui-avatars.com/api/?name={{ urlencode($nama) }}&background=4361ee&color=fff"
                                    class="operator-avatar" alt="{{ $nama }}">
                                </td>

                                {{-- Nama --}}
                                <td>{{ $nama }}</td>

                                {{-- Posisi --}}
                                <td>{{ $posisi }}</td>

                                {{-- Kontak --}}
                                <td>{{ $kontak }}</td>

                                {{-- Status --}}
                                <td>
                                    <span class="badge {{ $status === 'aktif' ? 'bg-success' : 'bg-secondary' }}">
                                        {{ ucfirst(str_replace('_', ' ', $status)) }}
                                    </span>
                                </td>

                                {{-- Aksi --}}
                                <td>
                                    <a href="{{ route('superadmin.operator.show', $id) }}"
                                    class="btn btn-info btn-sm"
                                    title="Detail">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('superadmin.operator.edit', $id) }}"
                                    class="btn btn-warning btn-sm"
                                    title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('superadmin.operator.destroy', $id) }}" method="POST" class="d-inline"
                                        onsubmit="return confirm('Yakin ingin menghapus operator ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-danger btn-sm" title="Hapus">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">Tidak ada data operator.</td>
                            </tr>
                        @endforelse

                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Paginasi --}}
        @if(isset($pagination) && isset($pagination['last_page']) && $pagination['last_page'] > 1)
            <nav>
                <ul class="pagination justify-content-center">
                    @for($i = 1; $i <= $pagination['last_page']; $i++)
                        <li class="page-item {{ $pagination['current_page'] == $i ? 'active' : '' }}">
                            <a class="page-link" href="{{ request()->fullUrlWithQuery(['page' => $i]) }}">{{ $i }}</a>
                        </li>
                    @endfor
                </ul>
            </nav>
        @endif
    </div>
@endsection