<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Laporan Penjualan</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid black;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <h2>Laporan Penjualan</h2>
    <p>Periode: {{ $startDate }} - {{ $endDate }}</p>

    <table>
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
                    <td>{{ \Carbon\Carbon::parse($sale['tanggal_dipesan'])->format('d/m/Y') }}</td>
                    <td>{{ $sale['status'] }}</td>
                    <td>{{ number_format($sale['total_harga'], 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <p><strong>Total Penjualan: {{ number_format($totalPrice, 2) }}</strong></p>
</body>
</html>
