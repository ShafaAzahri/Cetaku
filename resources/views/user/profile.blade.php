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
<div class="container my-4" style="max-width: 900px;">
    <div class="card shadow-sm rounded-3 p-4">

        <!-- Bagian Profil & Form -->
        <div class="d-flex justify-content-center">
            <form action="{{ route('user.profile.update') }}" method="POST" class="w-100" style="max-width: 1300px;">
                @csrf
                <!-- Header Profil -->
                <div class="text-center mb-3">
                    <div class="fw-bold fs-5">{{ $profile['nama'] ?? '-' }}</div>
                    <div class="text-muted">{{ $profile['email'] ?? '-' }}</div>
                </div>

                <!-- Account Information -->
                <div class="mb-4">
                    <div class="fw-bold mb-3">Account Information</div>
                    <div class="mb-3">
                        <label class="form-label small text-muted mb-1">Display name</label>
                        <input type="text" class="form-control" name="nama" value="{{ $profile['nama'] ?? '' }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small text-muted mb-1">Email</label>
                        <input type="email" class="form-control" name="email" value="{{ $profile['email'] ?? '' }}" required>
                    </div>

                    <!-- Tombol Simpan -->
                <div class="text-center mt-3">
                    <button type="submit" class="btn btn-primary fw-bold w-40">Simpan</button>
                </div>
                </div>

                <!-- Reset Password -->
                <div class="mb-3">
                    <h2 class="h6 fw-medium mb-2 text-center">Reset Password</h2>
                    <div class="text-center">
                        <button type="button" 
                                class="btn btn-outline-primary btn-sm fw-bold" 
                                style="width: 200px;"
                                data-bs-toggle="modal" 
                                data-bs-target="#ubahPasswordModal">
                            Ubah Password
                        </button>
                    </div>
                </div>

                
            </form>
        </div>

                
                        <div class="border rounded-3 p-4 mt-4 bg-white">
                <div class="fw-medium mb-4">Alamat Anda</div>
                <div>
                    @forelse ($addresses as $address)
                        <div class="mb-3 pb-3 border-bottom">
                            <span class="badge bg-secondary me-2">{{ $address['label'] ?? $address['type'] ?? '-' }}</span>
                            <span class="fw-bold me-2">{{ $profile['nama'] ?? '-' }}</span>
                            <span class="text-muted">{{ $address['nomor_hp'] ?? $address['phone'] ?? '-' }}</span>
                            <div class="d-inline float-end">
                                <a href="#" class="text-primary me-2" data-bs-toggle="modal" data-bs-target="#editAlamatModal{{ $address['id'] }}" style="text-decoration:none;" tabindex="-1">Ubah</a>
                                <form action="{{ route('Alamat.delete', $address['id']) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus alamat ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-link text-danger p-0 m-0 align-baseline">Hapus</button>
                                </form>
                            </div>
                            <div class="text-muted small mt-1">
                                {{ $address['provinsi'] ?? '-' }},
                                {{ $address['kota'] ?? '-' }},
                                {{ $address['kecamatan'] ?? '-' }},
                                {{ $address['kelurahan'] ?? '-' }},
                                {{ $address['kode_pos'] ?? '-' }}
                            </div>
                        </div>

                        <!-- Modal Edit Alamat (DI DALAM FORELSE) -->
                        <div class="modal fade" id="editAlamatModal{{ $address['id'] }}" tabindex="-1" aria-labelledby="editAlamatLabel{{ $address['id'] }}" aria-hidden="true">
                            <div class="modal-dialog">
                                <form method="POST" action="{{ route('Alamat.update', $address['id']) }}">
                                    @csrf
                                    @method('PUT')
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="editAlamatLabel{{ $address['id'] }}">Edit Alamat</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                                        </div>
                                        <div class="modal-body">
                                            {{-- <div class="mb-3">
                                                <label class="form-label">Full Name</label>
                                                <input type="text" name="full_name" class="form-control" value="{{ $profile['nama'] ?? '' }}" required>
                                            </div> --}}
                                            <div class="mb-3">
                                                <label class="form-label">Nomor HP</label>
                                                <input type="text" name="nomor_hp" class="form-control" value="{{ $address['nomor_hp'] ?? '' }}" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Alamat Lengkap</label>
                                                <textarea name="alamat_lengkap" class="form-control" required>{{ $address['alamat_lengkap'] ?? '' }}</textarea>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Kelurahan</label>
                                                <input type="text" name="kelurahan" class="form-control" value="{{ $address['kelurahan'] ?? '' }}" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Kecamatan</label>
                                                <input type="text" name="kecamatan" class="form-control" value="{{ $address['kecamatan'] ?? '' }}" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Kota</label>
                                                <input type="text" name="kota" class="form-control" value="{{ $address['kota'] ?? '' }}" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Provinsi</label>
                                                <input type="text" name="provinsi" class="form-control" value="{{ $address['provinsi'] ?? '' }}" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Kode Pos</label>
                                                <input type="text" name="kode_pos" class="form-control" value="{{ $address['kode_pos'] ?? '' }}" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Tipe</label>
                                                <select name="type" class="form-select" required>
                                                    <option value="Utama" {{ ($address['type'] ?? '') == 'Utama' ? 'selected' : '' }}>Utama</option>
                                                    <option value="Kantor" {{ ($address['type'] ?? '') == 'Kantor' ? 'selected' : '' }}>Kantor</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @empty
                        <div class="text-muted">Belum ada alamat.</div>
                    @endforelse
                </div>

                <!-- Tambah Alamat -->
                <button class="btn btn-outline-secondary w-100 mt-3" data-bs-toggle="modal" data-bs-target="#tambahAlamatModal">
                    + Tambah Alamat
                </button>
            </div>

                       <!-- Modal Tambah Alamat -->
            <div class="modal fade" id="tambahAlamatModal" tabindex="-1" aria-labelledby="tambahAlamatModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <form method="POST" action="{{ route('Alamat.store') }}">
                        @csrf
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="tambahAlamatModalLabel">Tambah Alamat Baru</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                            </div>
                            <div class="modal-body">
                                <button type="button" class="btn btn-outline-primary w-100 mb-3" id="btn-get-location" onclick="getLocation()">
                                    <i class="fas fa-map-marker-alt me-2"></i>Gunakan Lokasi Saat Ini
                                </button>
                                <div class="mb-3">
                                    <label class="form-label">Nomor HP</label>
                                    <input type="text" name="nomor_hp" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Alamat Lengkap</label>
                                    <textarea name="alamat_lengkap" id="alamat_lengkap" class="form-control" required></textarea>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Kelurahan</label>
                                    <input type="text" name="kelurahan" id="kelurahan" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Kecamatan</label>
                                    <input type="text" name="kecamatan" id="kecamatan" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Kota</label>
                                    <input type="text" name="kota" id="kota" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Provinsi</label>
                                    <input type="text" name="provinsi" id="provinsi" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Kode Pos</label>
                                    <input type="text" name="kode_pos" id="kode_pos" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Tipe</label>
                                    <select name="label" class="form-select" required>
                                        <option value="Utama">Utama</option>
                                        <option value="Kantor">Kantor</option>
                                    </select>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                <button type="submit" class="btn btn-primary">Simpan Alamat</button>
                            </div>
                        </div>
                    </form>
                </div>
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

    <!-- Modal Ubah Password -->
    <div class="modal fade" id="ubahPasswordModal" tabindex="-1" aria-labelledby="ubahPasswordModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form method="POST" action="{{ route('profile.updatePassword') }}">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="ubahPasswordModalLabel">Ubah Password</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Password Lama</label>
                        <input type="password" name="old_password" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password Baru</label>
                        <input type="password" name="new_password" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Konfirmasi Password Baru</label>
                        <input type="password" name="new_password_confirmation" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Update Password</button>
                </div>
            </div>
        </form>
    </div>
    </div>

    <script>
        function getLocation() {
            const btn = document.getElementById('btn-get-location');
            const originalText = btn.innerHTML;
            
            if (navigator.geolocation) {
                btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Mencari lokasi...';
                btn.disabled = true;
                
                navigator.geolocation.getCurrentPosition(showPosition, showError, {
                    enableHighAccuracy: true,
                    timeout: 10000,
                    maximumAge: 0
                });
            } else {
                alert("Geolocation tidak didukung oleh browser Anda.");
            }
            
            function showPosition(position) {
                const lat = position.coords.latitude;
                const lon = position.coords.longitude;
                
                // Panggil Nominatim API untuk Reverse Geocoding
                fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lon}&addressdetails=1`, {
                    headers: {
                        'Accept-Language': 'id-ID,id;q=0.9,en-US;q=0.8,en;q=0.7'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data && data.address) {
                        const addr = data.address;
                        
                        // Set nilai ke form
                        document.getElementById('kelurahan').value = addr.village || addr.suburb || addr.neighbourhood || '';
                        document.getElementById('kecamatan').value = addr.city_district || addr.county || addr.suburb || '';
                        document.getElementById('kota').value = addr.city || addr.town || addr.municipality || '';
                        document.getElementById('provinsi').value = addr.state || addr.province || '';
                        document.getElementById('kode_pos').value = addr.postcode || '';
                        
                        // Set alamat lengkap
                        let road = addr.road ? addr.road + ', ' : '';
                        document.getElementById('alamat_lengkap').value = road + (addr.village || addr.suburb || '');
                    } else {
                        alert("Gagal menemukan detail alamat dari koordinat.");
                    }
                    
                    // Kembalikan tombol
                    btn.innerHTML = originalText;
                    btn.disabled = false;
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert("Terjadi kesalahan saat menghubungi layanan peta.");
                    btn.innerHTML = originalText;
                    btn.disabled = false;
                });
            }
            
            function showError(error) {
                let msg = "Terjadi kesalahan.";
                switch(error.code) {
                    case error.PERMISSION_DENIED:
                        msg = "Permintaan akses lokasi ditolak oleh pengguna.";
                        break;
                    case error.POSITION_UNAVAILABLE:
                        msg = "Informasi lokasi tidak tersedia.";
                        break;
                    case error.TIMEOUT:
                        msg = "Waktu permintaan lokasi habis.";
                        break;
                }
                alert(msg);
                btn.innerHTML = originalText;
                btn.disabled = false;
            }
        }
    </script>
@endsection