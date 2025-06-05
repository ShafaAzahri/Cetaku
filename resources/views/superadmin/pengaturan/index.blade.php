@extends('superadmin.layout.superadmin')

@section('content')
<div class="container-fluid" style="font-size: 18px;">
    <h2>Pengaturan Toko</h2>

    <!-- Display success message if available -->
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <!-- Form to Edit Toko Info -->
    <form action="{{ route('superadmin.pengaturan.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <table class="table">
            <tr>
                <th>Nama</th>
                <td><input type="text" name="nama" class="form-control fs-7" value="{{ old('nama', $tokoInfo->nama) }}" required></td>
            </tr>
            <tr>
                <th>Alamat</th>
                <td><input type="text" name="alamat_lengkap" class="form-control fs-7" value="{{ old('alamat_lengkap', $tokoInfo->alamat_lengkap) }}" required></td>
            </tr>
            <tr>
                <th>Kecamatan</th>
                <td><input type="text" name="kecamatan" class="form-control fs-7" value="{{ old('kecamatan', $tokoInfo->kecamatan) }}" required></td>
            </tr>
            <tr>
                <th>Kota</th>
                <td><input type="text" name="kota" class="form-control fs-7" value="{{ old('kota', $tokoInfo->kota) }}" required></td>
            </tr>
            <tr>
                <th>Provinsi</th>
                <td><input type="text" name="provinsi" class="form-control fs-7" value="{{ old('provinsi', $tokoInfo->provinsi) }}" required></td>
            </tr>
            <tr>
                <th>Kode Pos</th>
                <td><input type="text" name="kode_pos" class="form-control fs-7" value="{{ old('kode_pos', $tokoInfo->kode_pos) }}" required></td>
            </tr>
            <tr>
                <th>Nomor Telepon</th>
                <td><input type="text" name="nomor_telepon" class="form-control fs-7" value="{{ old('nomor_telepon', $tokoInfo->nomor_telepon) }}" required></td>
            </tr>
            <tr>
                <th>Email</th>
                <td><input type="email" name="email" class="form-control fs-7" value="{{ old('email', $tokoInfo->email) }}" required></td>
            </tr>
            <tr>
                <th>Logo</th>
                <td><input type="file" name="logo" class="form-control fs-7"></td>
            </tr>
            <tr>
                <th>Status</th>
                <td>
                    <select name="is_active" class="form-control fs-7">
                        <option value="1" {{ $tokoInfo->is_active ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ !$tokoInfo->is_active ? 'selected' : '' }}>Inactive</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td colspan="2"><button type="submit" class="btn btn-primary fs-7">Update Toko Info</button></td>
            </tr>
        </table>
    </form>
</div>
@endsection
