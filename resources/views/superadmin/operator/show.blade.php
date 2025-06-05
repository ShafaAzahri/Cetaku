@extends('superadmin.layout.superadmin')

@section('content')
  <div class="container mt-4">
    <h2>Detail Operator</h2>

    {{-- Alert --}}
    @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @elseif(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="card">
    <div class="card-body">
      <h4 class="card-title">{{ $operator['nama'] }}</h4>

      <div class="mb-2">
      <strong>Posisi:</strong> {{ ucfirst($operator['posisi']) }}
      </div>

      <div class="mb-2">
      <strong>Kontak:</strong> {{ $operator['kontak'] ?? '-' }}
      </div>

      <div class="mb-2">
      <strong>Status:</strong>
      <span class="badge {{ $operator['status'] === 'aktif' ? 'bg-success' : 'bg-secondary' }}">
        {{ ucfirst(str_replace('_', ' ', $operator['status'])) }}
      </span>
      </div>

      <div class="mt-4">
      <a href="{{ route('superadmin.operator.index') }}" class="btn btn-secondary">Kembali</a>
      <a href="{{ route('superadmin.operator.edit', $operator['id']) }}" class="btn btn-warning">Edit</a>
      <form action="{{ route('superadmin.operator.destroy', $operator['id']) }}" method="POST" class="d-inline"
        onsubmit="return confirm('Yakin ingin menghapus operator ini?')">
        @csrf
        @method('DELETE')
        <button class="btn btn-danger">Hapus</button>
      </form>
      </div>
    </div>
    </div>
  </div>
@endsection