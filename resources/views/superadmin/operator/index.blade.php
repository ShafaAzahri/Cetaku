@extends('superadmin.layout.superadmin')

@section('content')
    <div class="container-fluid px-3">
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

        {{-- Filter --}}
        <form method="GET" action="{{ route('superadmin.operator.index') }}" class="mb-3">
            <div class="row">
                <div class="col-md-4">
                    <input type="text" name="search" class="form-control" placeholder="Cari nama / kontak..." value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <select name="status" class="form-control">
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
                <div class="col-md-2">
                    <button class="btn btn-primary w-100">Filter</button>
                </div>
            </div>
        </form>

        {{-- Tabel --}}
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama</th>
                        <th>Posisi</th>
                        <th>Kontak</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($operators as $key => $operator)
                        <tr>
                            <td>{{ ($pagination['current_page'] ?? 1 - 1) * 10 + $key + 1 }}</td>
                            <td>{{ is_array($operator) ? $operator['nama'] : $operator->nama }}</td>
                            <td>{{ is_array($operator) ? $operator['posisi'] : $operator->posisi }}</td>
                            <td>{{ is_array($operator) ? $operator['kontak'] : $operator->kontak }}</td>
                            <td>
                                @php
        $status = is_array($operator) ? $operator['status'] : $operator->status;
                                @endphp
                                <span class="badge {{ $status === 'aktif' ? 'bg-success' : 'bg-secondary' }}">
                                    {{ ucfirst(str_replace('_', ' ', $status)) }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('superadmin.operator.show', is_array($operator) ? $operator['id'] : $operator->id) }}"
                                    class="btn btn-info btn-sm"
                                    title="Detail">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('superadmin.operator.edit', is_array($operator) ? $operator['id'] : $operator->id) }}"
                                    class="btn btn-warning btn-sm"
                                    title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('superadmin.operator.destroy', is_array($operator) ? $operator['id'] : $operator->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus operator ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-danger btn-sm"
                                        title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">Tidak ada data operator.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
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