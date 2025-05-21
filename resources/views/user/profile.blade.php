@extends('user.layouts.app')

@section('title', 'Profile')

@section('content')
    <!-- Breadcrumb -->
    <div class="bg-light py-3 fw-medium">
        <div class="container">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="/" class="text-decoration-none text-secondary">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Profile</li>
                </ol>
            </nav>
        </div>
    </div>

    <!-- Alert -->
    <div class="container mt-3">
        @include('user.components.alert')
    </div>

    <!-- Konten utama profile -->
    <div class="container my-4">
        <div class="card shadow-sm rounded-3 p-4">
            <!-- Profile Header -->
            <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap">
                <div class="d-flex align-items-center mb-3 mb-md-0">
                    <img src="/images/profile.png" alt="Profile Picture" class="rounded-circle me-4" style="width: 70px; height: 70px; object-fit: cover;">
                    <div>
                        <div class="fw-bold">{{ $profile['nama'] ?? '-' }}</div>
                        <div class="text-muted small">{{ $profile['email'] ?? '-' }}</div>
                    </div>
                </div>
                <button class="btn btn-primary fw-bold px-4" disabled>Simpan Perubahan</button>
            </div>

            <!-- Main Content -->
            <div class="row g-4">
                <!-- Account Information Section -->
                <div class="col-md-6">
                    <div class="fw-medium mb-3">Account Information</div>
                    <div class="mb-3">
                        <label class="form-label small text-muted mb-1">Display name</label>
                        <input type="text" class="form-control" value="{{ $profile['nama'] ?? '-' }}" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small text-muted mb-1">Email</label>
                        <input type="email" class="form-control" value="{{ $profile['email'] ?? '-' }}" readonly>
                    </div>
                </div>
                <!-- Password Reset Section -->
                <div class="col-md-6">
                    <h2 class="h6 fw-medium mb-3">Reset Password</h2>
                    <form method="POST" action="{{ route('profile.update-password') }}">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label small text-muted mb-1">Password Lama</label>
                            <input type="password" class="form-control" name="old_password" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small text-muted mb-1">Password Baru</label>
                            <input type="password" class="form-control" name="new_password" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small text-muted mb-1">Konfirmasi Password Baru</label>
                            <input type="password" class="form-control" name="new_password_confirmation" required>
                        </div>
                        <button type="submit" class="btn btn-primary px-4 fw-bold w-100">Update Password</button>
                    </form>
                </div>
            </div>

            <!-- Address Section -->
            <div class="border rounded-3 p-4 mt-4 bg-white">
                <div class="fw-medium mb-4">Alamat Anda</div>
                <div>
                    @forelse ($addresses as $address)
                        <div class="mb-3 pb-3 border-bottom">
                            <span class="badge bg-secondary me-2">{{ $address['type'] ?? '-' }}</span>
                            <span class="fw-bold me-2">{{ $address['type'] ?? '-' }}</span>
                            <span class="text-muted">{{ $address['phone'] ?? '-' }}</span>
                            <div class="d-inline float-end">
                                <a href="#" class="text-primary me-2" style="text-decoration:none;" tabindex="-1">Ubah</a>
                                <a href="#" class="text-danger" style="text-decoration:none;" tabindex="-1">Hapus</a>
                            </div>
                            <div class="text-muted small mt-1">
                                {{ $address['address'] ?? '-' }},
                                {{ $address['kecamatan'] ?? '' }},
                                {{ $address['kota'] ?? '' }},
                                {{ $address['provinsi'] ?? '' }},
                                {{ $address['kode_pos'] ?? '' }}
                            </div>
                        </div>
                    @empty
                        <div class="text-muted">Belum ada alamat.</div>
                    @endforelse
                </div>
                <button class="btn btn-outline-secondary w-100 mt-3" disabled>
                    + Tambah Alamat
                </button>
            </div>

            <!-- Logout Button -->
            <div class="mt-4 text-end">
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-danger fw-bold px-4">
                        Logout
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection
