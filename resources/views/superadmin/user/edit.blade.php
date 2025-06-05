@extends('superadmin.layout.superadmin')

@section('title', 'Edit User')

@section('styles')
<style>
    .user-form-card {
        background-color: #fff;
        border-radius: 8px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        margin-bottom: 20px;
    }
    
    .user-form-card .card-header {
        padding: 15px 20px;
        border-bottom: 1px solid #eee;
    }
    
    .user-form-card .card-title {
        font-size: 18px;
        font-weight: 600;
        margin-bottom: 0;
    }
    
    .user-form-card .card-body {
        padding: 20px;
    }
    
    .form-label {
        font-weight: 500;
        color: #4b5563;
    }
    
    .required-field::after {
        content: " *";
        color: #ef4444;
    }
</style>
@endsection

@section('content')
  <div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h4 class="mb-0">Edit User</h4>
      <a href="{{ route('superadmin.user.index') }}" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-1"></i> Kembali
      </a>
    </div>

    @if($errors->any())
    <div class="alert alert-danger">
      <ul class="mb-0">
        @foreach($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
    @endif

    <div class="user-form-card">
      <div class="card-header">
        <h5 class="card-title">Form Edit User</h5>
      </div>
      <div class="card-body">
      <form action="{{ route('superadmin.user.update', $user['id']) }}" method="POST">
          @csrf
          @method('PUT')

          <div class="mb-3">
            <label for="nama" class="form-label required-field">Nama Lengkap</label>
            <input type="text" class="form-control @error('nama') is-invalid @enderror" id="nama" name="nama" value="{{ old('nama', $user['nama']) }}" required>
            @error('nama')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          <div class="mb-3">
            <label for="email" class="form-label required-field">Email</label>
            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $user['email']) }}" required>
            @error('email')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          <div class="mb-3">
            <label for="password" class="form-label">Password Baru</label>
            <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password">
            @error('password')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            <div class="form-text">Kosongkan jika tidak ingin mengubah password. Minimal 8 karakter jika diisi.</div>
          </div>

          <div class="mb-3 d-flex justify-content-end">
            <button type="reset" class="btn btn-outline-secondary me-2">Reset</button>
            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
          </div>
        </form>
      </div>
    </div>
  </div>
@endsection
