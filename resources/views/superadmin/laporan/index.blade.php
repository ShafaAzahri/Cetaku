@extends('superadmin.layout.superadmin')

@section('content')
<style>
    /* Main table styling */
    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
    }

    table th,
    table td {
        padding: 10px;
        text-align: left;
        vertical-align: middle;
        border: 1px solid #ddd;
        font-size: 16px;
    }

    table th {
        background-color: #f4f4f4;
        font-weight: bold;
        color: #555;
    }

    table tr:nth-child(even) {
        background-color: #f9f9f9;
    }

    table tr:hover {
        background-color: #f1f1f1;
    }

    /* Form styling */
    .form-control {
        border-radius: 4px;
        font-size: 16px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }

    .btn {
        border-radius: 4px;
        padding: 6px;
        font-size: 16px;
        font-weight: normal;
        transition: background-color 0.3s ease;
    }

    .btn-primary {
        background-color: #007bff;
        border-color: #007bff;
    }

    .btn-primary:hover {
        background-color: #0056b3;
        border-color: #004085;
    }

    .btn-success {
        background-color: #28a745;
        border-color: #28a745;
    }

    .btn-success:hover {
        background-color: #218838;
        border-color: #1e7e34;
    }

    /* Heading styles */
    h2,
    h3 {
        color: #333;
        /* font-size: 19px; */
        font-weight: 600;
    }

    .container {
        max-width: 100%;
        margin: 0 auto;
    }

    .row {
        margin-bottom: 20px;
    }

    /* Add some margin to the filter section */
    .filter-section {
        margin-bottom: 30px;
    }
</style>

<div class="container">
    <h2>Laporan Penjualan</h2>

    <!-- Filter Form -->
    <form action="{{ route('superadmin.laporan.index') }}" method="GET" class="mb-3 filter-section">
        <div class="row">
            <!-- Start Date -->
            <div class="col-md-3">
                <input type="date" name="start_date" class="form-control" value="{{ old('start_date', $startDate) }}">
            </div>

            <!-- End Date -->
            <div class="col-md-3">
                <input type="date" name="end_date" class="form-control" value="{{ old('end_date', $endDate) }}">
            </div>

            <!-- Filter Button -->
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">Filter</button>
            </div>

            <!-- Export Excel Button -->
            <div class="col-md-2">
                <a href="{{ route('superadmin.laporan.export') }}" class="btn btn-success w-100 mb-3">Export to Excel</a>
            </div>
        </div>
    </form>

    <!-- Sales Data Table -->
    <h3>Laporan Penjualan</h3>

    @if(!empty($salesData) && count($salesData) > 0)
    <table class="table">
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal Pesanan</th>
                <th>Status</th>
                <th>Total Harga</th>
            </tr>
        </thead>
        <tbody>
            @foreach($salesData as $key => $sale)
            <tr>
                <td>{{ $key + 1 }}</td>
                <td>{{ \Carbon\Carbon::parse($sale['created_at'])->format('d/m/Y') }}</td>
                <td>{{ $sale['status'] }}</td>
                <td>{{ number_format($sale['total_harga'], 2) }}</td>
            </tr>
            @endforeach
            <tr>
                <td colspan="3" class="text-end"><strong>Total Penjualan:</strong></td>
                <td><strong>{{ number_format($totalPrice, 2) }}</strong></td>
            </tr>
        </tbody>
    </table>
    @else
    <p>Tidak ada data penjualan.</p>
    @endif
    <br>
    <!-- Top Selling Products Table -->
    <h3>Produk Unggulan (Top Selling Items)</h3>

    @if(!empty($topItems))
    <table class="table">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Produk</th>
                <th>Total Terjual</th>
                <th>Total Pendapatan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($topItems as $key => $item)
            <tr>
                <td>{{ $key + 1 }}</td>
                <td>{{ $item['nama_item'] }}</td>
                <td>{{ $item['total_terjual'] }}</td>
                <td>{{ number_format($item['total_pendapatan'], 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @else
    <p>Tidak ada produk unggulan.</p>
    @endif
</div>

@endsection