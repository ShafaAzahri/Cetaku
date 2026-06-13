@extends('superadmin.layout.superadmin')

@section('content')
<style>
    .table th, .table td {
        font-size: 16px;
        vertical-align: middle;
    }

    .btn {
        font-size: 16px;
    }

    h2, h3 {
        color: #333;
        font-weight: 600;
    }

    .filter-section {
        margin-bottom: 20px;
    }

    .card {
        border: 1px solid #ddd;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        border-radius: 6px;
    }

    .card-header {
        background-color: #f8f9fa;
        border-bottom: 1px solid #ddd;
        padding: 16px;
    }

    .card-body {
        padding: 20px;
    }

    .table-hover tbody tr:hover {
        background-color: #f1f1f1;
    }
    
    .show-more-info {
        color: #6c757d;
        font-style: italic;
        font-size: 14px;
    }
</style>


<div class="container-fluid px-2">
    <div class="card mb-4">
        <div class="card-header">
            <h2 class="mb-0">Laporan Penjualan</h2>
        </div>
        <div class="card-body">

            <!-- 1. Produk Unggulan -->
            <h3>Produk Unggulan (Top Selling Items)</h3>
            @if(!empty($topItems) && count($topItems) > 0)
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="">
                            <tr>
                                <th>No</th>
                                <th>Nama Produk</th>
                                <th>Total Terjual</th>
                                <th>Total Pendapatan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach(collect($topItems)->take(5) as $key => $item)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td>{{ $item['nama_item'] }}</td>
                                    <td>{{ $item['total_terjual'] }}</td>
                                    <td>Rp {{ number_format($item['total_pendapatan'], 2, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @if(collect($topItems)->count() > 5)
                    <p class="show-more-info">Menampilkan 5 dari {{ collect($topItems)->count() }} produk unggulan</p>
                @endif
            @else
                <p class="text-muted">Tidak ada produk unggulan.</p>
            @endif


            <hr class="my-4">

            <!-- 2. Filter Tanggal -->
            <form action="{{ route('superadmin.laporan.index') }}" method="GET" class="mb-3 filter-section filter-auto-submit">
                <div class="row">
                    <div class="col-md-3">
                        <input type="date" name="start_date" class="form-control auto-submit"
                            value="{{ old('start_date', isset($startDate) ? $startDate : '') }}">
                    </div>
                    <div class="col-md-3">
                        <input type="date" name="end_date" class="form-control auto-submit"
                            value="{{ old('end_date', isset($endDate) ? $endDate : '') }}">
                    </div>
                    <div class="col-md-2 d-grid">
                        <a href="{{ route('superadmin.laporan.export', ['start_date' => $startDate, 'end_date' => $endDate]) }}" class="btn btn-success">Export ke Excel</a>

                    </div>
                </div>
            </form>

            @if(!empty($startDate) && !empty($endDate))
                <p><strong>Periode:</strong> {{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }}</p>
            @endif

            <!-- 3. Laporan Penjualan -->
            <h3 class="mt-4">Laporan Penjualan</h3>
            @if(!empty($salesData) && count($salesData) > 0)
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>No</th>
                                <th>Tanggal Pesanan</th>
                                <th>Status</th>
                                <th>Total Harga</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach(collect($salesData)->take(5) as $key => $sale)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td>{{ \Carbon\Carbon::parse($sale['created_at'])->format('d/m/Y') }}</td>
                                    <td>{{ $sale['status'] }}</td>
                                    <td>Rp {{ number_format($sale['total_harga'], 2, ',', '.') }}</td>
                                </tr>
                            @endforeach
                            <tr>
                                <td colspan="3" class="text-end"><strong>Total Penjualan:</strong></td>
                                <td><strong>Rp {{ number_format($totalPrice, 2, ',', '.') }}</strong></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                @if(collect($salesData)->count() > 5)
                    <p class="show-more-info">Menampilkan 5 dari {{ collect($salesData)->count() }} data penjualan</p>
                @endif
            @else
                <p class="text-muted">Tidak ada data penjualan.</p>
            @endif

            <!-- 4. Laporan Rincian -->
            <h3 class="mt-5">Laporan Rincian</h3>
            @if(!empty($detailRincian) && count($detailRincian) > 0)
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>No</th>
                                <th>ID Pesanan</th>
                                <th>Tanggal Pesanan</th>
                                <th>Nama Pemesan</th>
                                <th>Nama Produk</th>
                                <th>Harga Satuan</th>
                                <th>Jumlah</th>
                                <th>Biaya Desain</th>
                                <th>Biaya Ongkir</th>
                                <th>Total Harga</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $groupedByPesanan = collect($detailRincian)->groupBy('pesanan_id');
                                $limitedGrouped = $groupedByPesanan->take(5); // Ambil 5 pesanan pertama
                                $no = 1;
                                $grandTotal = 0;
                            @endphp

                            @foreach($limitedGrouped as $pesananId => $items)
                                @php
                                    $rowspan = $items->count();
                                    $first = $items->first();
                                    $subtotal = 0;
                                    $ongkir = $first->ongkos_kirim ?? 0;
                                @endphp

                                @foreach($items as $index => $item)
                                    <tr>
                                        @if($index === 0)
                                            <td rowspan="{{ $rowspan }}">{{ $no++ }}</td>
                                            <td rowspan="{{ $rowspan }}">#{{ $pesananId }}</td>
                                            <td rowspan="{{ $rowspan }}">{{ \Carbon\Carbon::parse($item->tanggal_pesanan)->format('d/m/Y') }}</td>
                                            <td rowspan="{{ $rowspan }}">{{ $item->nama_pemesan }}</td>
                                        @endif
                                        <td>{{ $item->nama_item }}</td>
                                        <td>Rp {{ number_format($item->harga_satuan, 2, ',', '.') }}</td>
                                        <td>{{ $item->jumlah }}</td>
                                        <td>
                                            @if ($item->biaya_jasa > 0)
                                                Rp {{ number_format($item->biaya_jasa, 2, ',', '.') }}
                                            @else
                                                -
                                            @endif
                                        </td>
                                        @if($index === 0)
                                            <td rowspan="{{ $rowspan }}">
                                                @if($ongkir > 0)
                                                    Rp {{ number_format($ongkir, 2, ',', '.') }}
                                                @else
                                                    -
                                                @endif
                                            </td>
                                        @endif
                                        <td>Rp {{ number_format($item->total, 2, ',', '.') }}</td>
                                    </tr>
                                    @php
                                        $subtotal += $item->total;
                                    @endphp
                                @endforeach

                                <tr class="table-secondary">
                                    <td colspan="9" class="text-end"><strong>Sub total:</strong></td>
                                    <td><strong>Rp {{ number_format($subtotal, 2, ',', '.') }}</strong></td>
                                </tr>

                                @php $grandTotal += $subtotal; @endphp
                            @endforeach

                            <tr class="table-info">
                                <td colspan="9" class="text-end"><strong>Total Keseluruhan:</strong></td>
                                <td><strong>Rp {{ number_format($grandTotal, 2, ',', '.') }}</strong></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                @if($groupedByPesanan->count() > 5)
                    <p class="show-more-info">Menampilkan 5 dari {{ $groupedByPesanan->count() }} pesanan</p>
                @endif
            @else
                <p class="text-muted">Tidak ada data rincian penjualan.</p>
            @endif

        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const form = document.querySelector('form.filter-auto-submit');
        const startDateInput = form.querySelector('input[name="start_date"]');
        const endDateInput = form.querySelector('input[name="end_date"]');

        function autoSubmitIfBothDatesFilled() {
            if (startDateInput.value && endDateInput.value) {
                form.submit();
            }
        }

        startDateInput.addEventListener('change', autoSubmitIfBothDatesFilled);
        endDateInput.addEventListener('change', autoSubmitIfBothDatesFilled);
    });
</script>
@endpush

@endsection