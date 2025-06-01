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

    <form method="POST" action="{{ route('superadmin.operator.update', $operator['id']) }}">
      @csrf
      @method('PUT')

      <div class="mb-3">
        <label for="nama" class="form-label">Nama</label>
        <input type="text" name="nama" class="form-control" id="nama" value="{{ old('nama', $operator['nama']) }}" required>
      </div>

      <div class="mb-3">
        <label for="posisi" class="form-label">Posisi</label>
        <input type="text" name="posisi" class="form-control" id="posisi" value="{{ old('posisi', $operator['posisi']) }}" required>
      </div>

      <div class="mb-3">
        <label for="kontak" class="form-label">Kontak</label>
        <input type="text" name="kontak" class="form-control" id="kontak" value="{{ old('kontak', $operator['kontak']) }}">
      </div>

      <div class="mb-3">
        <label for="status" class="form-label">Status</label>
        <select name="status" id="status" class="form-control" required>
          <option value="aktif" {{ old('status', $operator['status']) === 'aktif' ? 'selected' : '' }}>Aktif</option>
          <option value="tidak_aktif" {{ old('status', $operator['status']) === 'tidak_aktif' ? 'selected' : '' }}>Tidak Aktif</option>
        </select>
      </div>

      <div class="mt-4">
        <a href="{{ route('superadmin.operator.index') }}" class="btn btn-secondary">Batal</a>
        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
      </div>
    </form>
  </div>
@endsection
