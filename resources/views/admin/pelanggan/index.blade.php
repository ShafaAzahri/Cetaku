@extends('admin.layout.admin')

@section('title', 'Manajemen Pesanan')

@section('content')
<style>
    .list-group-item {
        border-bottom: 1px solid #ddd;
        /* Menambahkan garis bawah pada setiap item */
        padding: 15px 20px;
        /* Optional: Padding agar lebih rapi */
    }

    .list-group-item:last-child {
        border-bottom: none;
        /* Menghilangkan border pada item terakhir */

        .list-group-item {
            border-bottom: 1px solid #ddd;
            padding: 12px 20px;
            /* Memberikan ruang antara elemen */
            margin-bottom: 8px;
            /* Memberikan jarak antar elemen */
        }

        .list-group-item .badge {
            font-size: 12px;
            /* Mengubah ukuran font pada badge */
        }

        .pagination {
            margin-top: 20px;
            justify-content: center;
        }

        .pagination .page-link {
            padding: 8px 16px;
        }

    }
</style>
<div class="container mt-4">
    <h3>List Pelanggan</h3>
    <!-- Form Pencarian -->
    <form method="GET" action="{{ route('admin.pelanggan.index') }}" class="mb-4">
        <input type="text" name="search" class="form-control" value="{{ old('search', $search) }}" placeholder="Masukkan ID Pesanan dan ID Customer untuk mencari detail info yang dicari">
    </form>

    <!-- List Group for Customers -->
    <div class="list-group">
        @foreach($pelanggan as $user)
        <div class="list-group-item d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
                <span class="font-weight-bold mr-3">{{ $user->nama }}</span>
                <span class="text-muted">| ID Pesanan Pelanggan {{ $user->id }}</span>
            </div>
            <div class="d-flex align-items-center">
                <!-- Status Badge (Change according to your logic) -->
                <span class="badge bg-warning mr-3">Pemesanan</span>
                <!-- Show Pesanan Status -->
                @foreach($user->pesanan as $pesanan)
                <!-- <span class="badge bg-info">{{ $pesanan->tanggal_dipesan }}</span> -->
                @endforeach
                <div class="d-flex">
                    <i class="fas fa-comments mr-3"></i> <!-- Chat Icon -->
                    <i class="fas fa-ellipsis-h"></i> <!-- More Options Icon -->
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Pagination (Optional) -->
    <div class="pagination justify-content-center mt-4">
        <ul class="pagination">
            <li class="page-item"><a class="page-link" href="#">A</a></li>
            <li class="page-item"><a class="page-link" href="#">B</a></li>
            <li class="page-item"><a class="page-link" href="#">C</a></li>
            <li class="page-item"><a class="page-link" href="#">...</a></li>
        </ul>
    </div>
</div>

@endsection