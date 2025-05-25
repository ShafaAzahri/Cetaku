@extends('superadmin.layout.superadmin')

@section('content')
<style>
    table {
        width: 100%;
        border-collapse: collapse;
    }

    table th,
    table td {
        border: 1px solid #ddd;
        /* Vertical borders */
        padding: 8px;
        text-align: left;
    }

    table th {
        background-color: #f4f4f4;
        /* Optional: Background color for header */
    }
</style>

<h2>Laporan Penjualan</h2>

<!-- Filter Form -->
<form action="{{ route('superadmin.laporan.index') }}" method="GET" class="mb-3">
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

        <!-- export button -->
        <div class="col-md-2">
            <a href="{{ route('superadmin.laporan.export.pdf', ['start_date' => $startDate, 'end_date' => $endDate]) }}" class="btn btn-danger w-100 mb-2">Export PDF</a>
        </div>
        <div class="col-md-2">
            <a href="{{ route('superadmin.laporan.export.excel', ['start_date' => $startDate, 'end_date' => $endDate]) }}" class="btn btn-success w-100">Export Excel</a>
        </div>
    </div>
</form>

<!-- Sales Data Table -->
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
            <td>{{ \Carbon\Carbon::parse($sale['tanggal_dipesan'] ?? '')->format('d/m/Y') ?: 'N/A' }}</td>
            <td>{{ $sale['status'] ?? 'N/A' }}</td>
            <td>{{ number_format($sale['total_harga'] ?? 0, 2) }}</td>
        </tr>
        <tr>
            <td colspan="3" class="text-center"><strong>Total Penjualan:</strong></td>
            <td><strong>{{ number_format($totalPrice, 2) }}</strong></td>
        </tr>
        @endforeach
    </tbody>
</table>

<!-- Total Price -->
@else
<p>Tidak ada data penjualan.</p>
@endif
@endsection