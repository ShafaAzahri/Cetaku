@extends('admin.layout.admin')

@section('title', 'Dashboard')

@section('content')
<!-- Row 1: Welcome & Stats -->
<div class="row mb-4">
    <div class="col-md-5">
        <div class="card h-100 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h5 class="card-title">Selamat Datang, {{ session('user')['nama'] }}!</h5>
                        <p class="card-text text-muted">Anda login sebagai Administrator</p>
                        <a href="{{ route('admin.product-manager') }}" class="btn btn-primary mt-2">
                            <i class="fas fa-boxes me-1"></i> Kelola Produk
                        </a>
                    </div>
                    <div class="d-flex align-items-center">
                        <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                            <i class="fas fa-user-tie text-white"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-7">
        <div class="card h-100 shadow-sm">
            <div class="card-body">
                <div class="row row-cols-1 row-cols-md-4 g-3">
                    <div class="col">
                        <div class="card border-0 bg-light h-100">
                            <div class="card-body text-center d-flex flex-column justify-content-center">
                                <h3 class="text-primary mb-2">{{ $pesananBulanIni }}</h3>
                                <p class="card-text mb-0">Pesanan</p>
                                <small class="text-muted">Bulan Ini</small>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card border-0 bg-light h-100">
                            <div class="card-body text-center d-flex flex-column justify-content-center">
                                <h3 class="text-success mb-2">{{ $pesananSelesaiBulanIni }}</h3>
                                <p class="card-text mb-0">Selesai</p>
                                <small class="text-muted">Bulan Ini</small>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card border-0 bg-light h-100">
                            <div class="card-body text-center d-flex flex-column justify-content-center">
                                <h3 class="text-warning mb-2">{{ $pesananBerjalan }}</h3>
                                <p class="card-text mb-0">Berjalan</p>
                                <small class="text-muted">Saat Ini</small>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card border-0 bg-light h-100">
                            <div class="card-body text-center d-flex flex-column justify-content-center">
                                <h3 class="text-danger mb-2">{{ $pesananDibatalkan }}</h3>
                                <p class="card-text mb-0">Dibatalkan</p>
                                <small class="text-muted">Bulan Ini</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Total Penjualan -->
<div class="row">
    <div class="col-md-3 mb-0">
        <div class="card shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center bg-white">
                <h5 class="m-0">Total Penjualan</h5>
                <!-- <div>
                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" id="salesMonthDropdown" data-bs-toggle="dropdown">
                        {{ \Carbon\Carbon::createFromFormat('Y-m', $selectedMonth)->format('F Y') }}
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="salesMonthDropdown">
                        @foreach($months as $month)
                            <li>
                                <a class="dropdown-item" href="{{ route('admin.dashboard', ['month' => $month->year.'-'.$month->month]) }}">
                                    {{ \Carbon\Carbon::createFromFormat('Y-m', $month->year.'-'.$month->month)->format('F Y') }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div> -->
            </div>
            <div class="card-body">
                <h1 class="text-primary mb-3">Rp {{ number_format($totalPenjualan, 0, ',', '.') }}</h1>
                <small class="text-muted">
                    Total pemasukan di bulan
                    {{ \Carbon\Carbon::createFromFormat('Y-m', $selectedMonth)->format('F Y') }}
                </small>
            </div>
        </div>
    </div>
</div>

<!-- Statistik dan Grafik -->
<div class="row">
    <!-- Statistik Penjualan -->
    <div class="col-md-12 mb-4">
        <div class="card h-100 shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center bg-white">
                <h5 class="m-0">Statistik Penjualan</h5>
                <div>
                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" id="salesMonthDropdown" data-bs-toggle="dropdown">
                        {{ \Carbon\Carbon::createFromFormat('Y-m', $selectedMonth)->format('F Y') }}
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="salesMonthDropdown">
                        @foreach($months as $month)
                            <li>
                                <a class="dropdown-item" href="{{ route('admin.dashboard', ['month' => $month->year.'-'.$month->month]) }}">
                                    {{ \Carbon\Carbon::createFromFormat('Y-m', $month->year.'-'.$month->month)->format('F Y') }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
            <div class="card-body">
                <p class="mb-2">{{ $pesananBulanIni }} Pesanan</p>
                <div style="height: 300px;">
                    <canvas id="salesChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Penjualan
    <div class="col-md-6 mb-4">
        <div class="card h-100 shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center bg-white">
                <h5 class="m-0">Total Penjualan</h5>
                <div>
                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" id="totalMonthDropdown" data-bs-toggle="dropdown">
                        {{ \Carbon\Carbon::createFromFormat('Y-m', $selectedMonth)->format('F Y') }}
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="totalMonthDropdown">
                        @foreach($months as $month)
                            <li>
                                <a class="dropdown-item" href="{{ route('admin.dashboard', ['month' => $month->year.'-'.$month->month]) }}">
                                    {{ \Carbon\Carbon::createFromFormat('Y-m', $month->year.'-'.$month->month)->format('F Y') }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
            <div class="card-body text-center">
                <h3 class="text-primary mb-3">{{ number_format($totalPenjualan, 0, ',', '.') }} IDR</h3>
                <p class="mb-3">{{ $pesananBulanIni }} Pesanan</p>
                <div style="height: 200px; max-width: 200px; margin: 0 auto;">
                    <canvas id="doughnutChart"></canvas>
                </div>
            </div>
        </div>
    </div> -->
</div>

<!-- Pesanan Terbaru dan Riwayat Pesanan -->
<div class="row">
    <!-- Pesanan Terbaru -->
    <div class="col-md-12 mb-0">
        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <h5 class="card-title mb-0">Pesanan Terbaru</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="text-center">ID</th>
                                <th class="text-center">Pelanggan</th>
                                <th class="text-center">Status</th>
                                <th class="text-center">Total</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pesananTerbaru as $pesanan)
                            <tr>
                                <td class="text-center">#{{ $pesanan->pesanan_id }}</td>
                                <td class="text-center">{{ $pesanan->pelanggan }}</td>
                                <td class="text-center">
                                    <span class="badge 
                                    @if($pesanan->status == 'Pemesanan') bg-secondary
                                    @elseif($pesanan->status == 'Dikonfirmasi') bg-info
                                    @elseif($pesanan->status == 'Sedang Diproses') bg-warning
                                    @elseif($pesanan->status == 'Menunggu Pengambilan') bg-warning
                                    @elseif($pesanan->status == 'Sedang Dikirim') bg-primary
                                    @elseif($pesanan->status == 'Selesai') bg-success
                                    @elseif($pesanan->status == 'Dibatalkan') bg-danger
                                    @endif">{{ $pesanan->status }}</span>
                                </td>
                                <td class="text-center">Rp {{ number_format($pesanan->total_harga, 0, ',', '.') }}</td>
                                <td class="text-center">
                                    <a href="{{ route('admin.pesanan.show', $pesanan->pesanan_id) }}" class="btn btn-sm btn-outline-primary">Lihat</a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer bg-white text-center">
                <a href="{{ route('admin.pesanan.index') }}" class="text-decoration-none">Lihat Semua Pesanan</a>
            </div>
        </div>
    </div>


    <!-- Riwayat Pesanan -->
    <div class="col-md-12 mb-0">
        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <h5 class="card-title mb-0">Riwayat Pesanan</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="text-center">No</th>
                                <th class="text-center">Tanggal</th>
                                <th class="text-center">Pesanan ID</th>
                                <th class="text-center">Status</th>
                                <th class="text-center">Info</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($riwayatPesanan as $key => $pesanan)
                            <tr>
                                <td class="text-center">{{ $key + 1 }}</td>
                                <td class="text-center">{{ $pesanan->created_at->format('Y-m-d') }}</td>
                                <td class="text-center">{{ str_pad($pesanan->id, 4, '0', STR_PAD_LEFT) }}</td>
                                <td class="text-center">
                                    <span class="badge 
                                        @if($pesanan->status == 'Selesai') bg-success
                                        @elseif($pesanan->status == 'Dibatalkan') bg-danger
                                        @endif">{{ $pesanan->status }}</span>
                                </td>
                                <td class="text-center">
                                <a href="{{ route('admin.pesanan.show', $pesanan->id) }}" class="btn btn-sm btn-outline-primary">Lihat Riwayat</a></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>  <!-- Ensure Chart.js is included -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Sales Chart
        const salesCtx = document.getElementById('salesChart');
        const pesananPerTanggal = @json($pesananPerTanggal); // Data dari PHP per tanggal
        const daysInMonth = {{ $jumlahHari }}; // Dihitung dari PHP

        // Buat array tanggal: [1, 2, ..., 31]
        const labels = Array.from({ length: daysInMonth }, (_, i) => i + 1);

        // Pastikan semua tanggal punya nilai, kalau tidak isi 0
        const data = labels.map(tgl => pesananPerTanggal[tgl] ?? 0);

        if (salesCtx) {
            const salesChart = new Chart(salesCtx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Pesanan',
                        data: data,
                        borderColor: '#007bff',
                        tension: 0.1,
                        fill: true,
                        backgroundColor: 'rgba(0, 123, 255, 0.2)',
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            min: 0,
                            max: 10,
                            ticks: {
                                stepSize: 1
                            },
                            // grid: {
                            //     display: false
                            // },
                            title: {
                                display: true,
                                text: 'Jumlah Pesanan'
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            },
                            title: {
                                display: true,
                                text: 'Tanggal'
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        }
                    }
                }
            });
        }

        // // Doughnut Chart
        // const doughnutCtx = document.getElementById('doughnutChart');
        // if (doughnutCtx) {
        //     const doughnutChart = new Chart(doughnutCtx, {
        //         type: 'doughnut',
        //         data: {
        //             labels: ['Pemesanan', 'Selesai'],
        //             datasets: [{
        //                 data: [{{ $pesananSelesaiBulanIni }}, {{ $pesananBulanIni - $pesananSelesaiBulanIni }}],
        //                 backgroundColor: [
        //                     '#007bff', // Pemesanan color
        //                     '#28a745'  // Selesai color
        //                 ]
        //             }]
        //         },
        //         options: {
        //             responsive: true,
        //             maintainAspectRatio: false,
        //             plugins: {
        //                 legend: {
        //                     position: 'bottom'
        //                 }
        //             }
        //         }
        //     });
        // }
    });
</script>
@endsection
