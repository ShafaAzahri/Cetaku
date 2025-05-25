@extends('superadmin.layout.superadmin')

@section('content')
<div class="container mt-4">
    <h2>Edit Operator</h2>

    {{-- Alert --}}
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @elseif(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    {{-- Form Edit Operator --}}
    <form action="{{ route('superadmin.operator.update', $operator['id']) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="form-group">
            <label for="nama">Nama</label>
            <input type="text" class="form-control" id="nama" name="nama" value="{{ old('nama', $operator['nama'] ?? '') }}" required>
            @error('nama')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="posisi">Posisi</label>
            <select class="form-control" id="posisi" name="posisi" required>
                <option value="admin" {{ ($operator['posisi'] ?? '') == 'admin' ? 'selected' : '' }}>Admin</option>
                <option value="operator" {{ ($operator['posisi'] ?? '') == 'operator' ? 'selected' : '' }}>Operator</option>
            </select>
            @error('posisi')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="kontak">Kontak</label>
            <input type="text" class="form-control" id="kontak" name="kontak" value="{{ old('kontak', $operator['kontak'] ?? '') }}" required>
            @error('kontak')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="status">Status</label>
            <select class="form-control" id="status" name="status" required>
                <option value="aktif" {{ ($operator['status'] ?? '') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                <option value="tidak_aktif" {{ ($operator['status'] ?? '') == 'tidak_aktif' ? 'selected' : '' }}>Tidak Aktif</option>
            </select>
            @error('status')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group mt-1">
            <button type="submit" class="btn btn-primary">Update Operator</button>
            <a href="{{ route('superadmin.operator.index') }}" class="btn btn-secondary">Kembali</a>
        </div>
    </form>
</div>
@endsection
