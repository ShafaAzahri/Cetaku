@extends('admin.layout.admin')

@section('title', 'Daftar Pesanan')

@section('styles')
<style>
    .status-badge {
        font-size: 0.85rem;
        padding: 0.35em 0.65em;
        border-radius: 4px;
    }
    .status-pemesanan {
        background-color: #FFE0B2;
        color: #E65100;
    }
    .status-dikonfirmasi {
        background-color: #B3E5FC;
        color: #0277BD;
    }
    .status-proses {
        background-color: #FFF9C4;
        color: #F57F17;
    }
    .status-pengambilan {
        background-color: #FFD180;
        color: #EF6C00;
    }
    .status-dikirim {
        background-color: #B3E5FC;
        color: #0277BD;
    }
    .status-selesai {
        background-color: #C8E6C9;
        color: #2E7D32;
    }
    .status-dibatalkan {
        background-color: #FFCDD2;
        color: #C62828;
    }
    .clickable {
        cursor: pointer;
    }
    .search-box {
        max-width: 300px;
        margin-bottom: 20px;
    }
    .filter-btn.active {
        box-shadow: 0 0 0 3px rgba(0,123,255,0.25);
    }
    .action-btn {
        width: 32px;
        height: 32px;
        padding: 0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 4px;
        margin-right: 5px;
    }
    .filter-section {
        background-color: #f8f9fa;
        padding: 20px;
        border-radius: 8px;
        margin-bottom: 20px;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col">
            <h1 class="h3 mb-0 text-gray-800">Daftar Pesanan</h1>
        </div>
        <div class="col-auto">
            <a href="{{ route('admin.dashboard') }}" class="btn btn-sm btn-secondary">
                <i class="fas fa-arrow-left me-1"></i> Kembali ke Dashboard
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <!-- Filter Section -->
    <div class="filter-section">
        <form action="{{ route('admin.pesanan.index') }}" method="GET">
            <div class="row g-3">
                <div class="col-md-3">
                    <label for="search" class="form-label">Cari</label>
                    <input type="text" class="form-control" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="ID Pesanan atau Pelanggan...">
                </div>
                <div class="col-md-2">
                    <label for="status" class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="">Semua Status</option>
                        <option value="Pemesanan" {{ ($filters['status'] ?? '') == 'Pemesanan' ? 'selected' : '' }}>Pemesanan</option>
                        <option value="Dikonfirmasi" {{ ($filters['status'] ?? '') == 'Dikonfirmasi' ? 'selected' : '' }}>Dikonfirmasi</option>
                        <option value="Sedang Diproses" {{ ($filters['status'] ?? '') == 'Sedang Diproses' ? 'selected' : '' }}>Sedang Diproses</option>
                        <option value="Menunggu Pengambilan" {{ ($filters['status'] ?? '') == 'Menunggu Pengambilan' ? 'selected' : '' }}>Menunggu Pengambilan</option>
                        <option value="Sedang Dikirim" {{ ($filters['status'] ?? '') == 'Sedang Dikirim' ? 'selected' : '' }}>Sedang Dikirim</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="start_date" class="form-label">Dari Tanggal</label>
                    <input type="date" class="form-control" name="start_date" value="{{ $filters['start_date'] ?? '' }}">
                </div>
                <div class="col-md-2">
                    <label for="end_date" class="form-label">Sampai Tanggal</label>
                    <input type="date" class="form-control" name="end_date" value="{{ $filters['end_date'] ?? '' }}">
                </div>
                <div class="col-md-2">
                    <label for="perpage" class="form-label">Tampilkan</label>
                    <select name="perpage" class="form-select">
                        <option value="10" {{ $perPage == 10 ? 'selected' : '' }}>10 per halaman</option>
                        <option value="25" {{ $perPage == 25 ? 'selected' : '' }}>25 per halaman</option>
                        <option value="50" {{ $perPage == 50 ? 'selected' : '' }}>50 per halaman</option>
                    </select>
                </div>
                <div class="col-md-1">
                    <label class="form-label">&nbsp;</label>
                    <button type="submit" class="btn btn-primary d-block w-100">
                        <i class="fas fa-search"></i> Cari
                    </button>
                </div>
            </div>
        </form>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-white">
            <div class="row align-items-center">
                <div class="col">
                    <h6 class="m-0 font-weight-bold text-primary">Pesanan Aktif</h6>
                </div>
                <div class="col-auto">
                    <div class="btn-group">
                        <a href="{{ route('admin.pesanan.index', ['status' => 'all', 'perpage' => $perPage]) }}" 
                           class="btn btn-sm btn-outline-secondary {{ ($filters['status'] ?? 'all') == 'all' ? 'active' : '' }}">
                            Semua
                        </a>
                        <a href="{{ route('admin.pesanan.index', ['status' => 'Pemesanan', 'perpage' => $perPage]) }}" 
                           class="btn btn-sm btn-outline-warning {{ ($filters['status'] ?? '') == 'Pemesanan' ? 'active' : '' }}">
                            Baru
                        </a>
                        <a href="{{ route('admin.pesanan.index', ['status' => 'Sedang Diproses', 'perpage' => $perPage]) }}" 
                           class="btn btn-sm btn-outline-info {{ ($filters['status'] ?? '') == 'Sedang Diproses' ? 'active' : '' }}">
                            Proses
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover text-nowrap mb-0" id="pesananTable">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3">ID</th>
                            <th>Tanggal</th>
                            <th>Pelanggan</th>
                            <th>Status</th>
                            <th>Metode</th>
                            <th>Produk</th>
                            <th>Total</th>
                            <th class="text-center">Tindakan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pesanan as $p)
                        <tr>
                            <td class="ps-3">#{{ $p['id'] }}</td>
                            <td>{{ $p['tanggal'] }}</td>
                            <td>{{ $p['pelanggan'] }}</td>
                            <td>
                                @php
                                    $statusClass = '';
                                    switch($p['status']) {
                                        case 'Pemesanan':
                                            $statusClass = 'status-pemesanan'; break;
                                        case 'Dikonfirmasi':
                                            $statusClass = 'status-dikonfirmasi'; break;
                                        case 'Sedang Diproses':
                                            $statusClass = 'status-proses'; break;
                                        case 'Menunggu Pengambilan':
                                            $statusClass = 'status-pengambilan'; break;
                                        case 'Sedang Dikirim':
                                            $statusClass = 'status-dikirim'; break;
                                        case 'Selesai':
                                            $statusClass = 'status-selesai'; break;
                                        case 'Dibatalkan':
                                            $statusClass = 'status-dibatalkan'; break;
                                        default:
                                            $statusClass = ''; break;
                                    }
                                @endphp
                                <span class="status-badge {{ $statusClass }}">{{ $p['status'] }}</span>
                            </td>
                            <td>{{ $p['metode'] }}</td>
                            <td>{{ $p['produk'] }}</td>
                            <td>Rp {{ number_format($p['total'], 0, ',', '.') }}</td>
                            <td class="text-center">
                                <div class="btn-group">
                                    <a href="{{ route('admin.pesanan.show', $p['id']) }}" class="btn btn-sm btn-info action-btn" title="Lihat Detail">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    
                                    @if($p['status'] == 'Pemesanan')
                                    <a href="{{ route('admin.pesanan.konfirmasi', $p['id']) }}" class="btn btn-sm btn-success action-btn" title="Konfirmasi">
                                        <i class="fas fa-check"></i>
                                    </a>
                                    @endif
                                    
                                    @if($p['status'] == 'Dikonfirmasi' || $p['status'] == 'Sedang Diproses')
                                    <a href="{{ route('admin.pesanan.proses', $p['id']) }}" class="btn btn-sm btn-primary action-btn" title="Proses Print">
                                        <i class="fas fa-print"></i>
                                    </a>
                                    @endif
                                    
                                    @if($p['status'] == 'Menunggu Pengambilan' && $p['metode'] == 'Ambil di Tempat')
                                    <form action="{{ route('admin.pesanan.confirm-pickup', $p['id']) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success action-btn" title="Konfirmasi Pengambilan" onclick="return confirm('Konfirmasi pengambilan pesanan?')">
                                            <i class="fas fa-handshake"></i>
                                        </button>
                                    </form>
                                    @endif
                                    
                                    @if($p['status'] == 'Sedang Diproses' && $p['metode'] == 'Dikirim')
                                    <a href="{{ route('admin.pesanan.kirim', $p['id']) }}" class="btn btn-sm btn-info action-btn" title="Konfirmasi Pengiriman">
                                        <i class="fas fa-truck"></i>
                                    </a>
                                    @endif
                                    
                                    @if($p['status'] == 'Sedang Dikirim')
                                    <form action="{{ route('admin.pesanan.confirm-delivery', $p['id']) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success action-btn" title="Konfirmasi Penerimaan" onclick="return confirm('Konfirmasi penerimaan pesanan?')">
                                            <i class="fas fa-box"></i>
                                        </button>
                                    </form>
                                    @endif
                                    
                                    @if(in_array($p['status'], ['Pemesanan', 'Dikonfirmasi', 'Sedang Diproses', 'Menunggu Pengambilan', 'Sedang Dikirim']))
                                    <form action="{{ route('admin.pesanan.cancel', $p['id']) }}" method="POST" class="d-inline">
                                        @csrf
                                        <input type="hidden" name="alasan" value="Dibatalkan oleh admin">
                                        <button type="submit" class="btn btn-sm btn-danger action-btn" title="Batalkan Pesanan" onclick="return confirm('PERHATIAN! Apakah Anda yakin ingin MEMBATALKAN pesanan?')">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-4">Tidak ada data pesanan</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        
        @if(isset($pagination) && $pagination['last_page'] > 1)
        <div class="card-footer bg-white">
            <div class="d-flex justify-content-between align-items-center">
                <div class="text-muted">
                    Menampilkan {{ count($pesanan) }} dari {{ $pagination['total'] }} pesanan
                </div>
                
                <nav aria-label="Page navigation">
                    <ul class="pagination pagination-sm mb-0">
                        {{-- Previous Page Link --}}
                        @if($pagination['current_page'] == 1)
                            <li class="page-item disabled">
                                <span class="page-link">&laquo;</span>
                            </li>
                        @else
                            <li class="page-item">
                                <a class="page-link" href="{{ url()->current() }}?page={{ $pagination['current_page']-1 }}&perpage={{ $perPage }}&status={{ $filters['status'] ?? '' }}&search={{ $filters['search'] ?? '' }}" rel="prev">&laquo;</a>
                            </li>
                        @endif

                        {{-- Pagination Elements --}}
                        @for($i = 1; $i <= $pagination['last_page']; $i++)
                            @if($i == $pagination['current_page'])
                                <li class="page-item active">
                                    <span class="page-link">{{ $i }}</span>
                                </li>
                            @else
                                <li class="page-item">
                                    <a class="page-link" href="{{ url()->current() }}?page={{ $i }}&perpage={{ $perPage }}&status={{ $filters['status'] ?? '' }}&search={{ $filters['search'] ?? '' }}">{{ $i }}</a>
                                </li>
                            @endif
                        @endfor

                        {{-- Next Page Link --}}
                        @if($pagination['current_page'] < $pagination['last_page'])
                            <li class="page-item">
                                <a class="page-link" href="{{ url()->current() }}?page={{ $pagination['current_page']+1 }}&perpage={{ $perPage }}&status={{ $filters['status'] ?? '' }}&search={{ $filters['search'] ?? '' }}" rel="next">&raquo;</a>
                            </li>
                        @else
                            <li class="page-item disabled">
                                <span class="page-link">&raquo;</span>
                            </li>
                        @endif
                    </ul>
                </nav>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Auto-submit form on select change for perpage
        $('select[name="perpage"]').change(function() {
            $(this).closest('form').submit();
        });
        
        // Highlight active filter
        $('.filter-btn').click(function() {
            $('.filter-btn').removeClass('active');
            $(this).addClass('active');
        });
    });
</script>
@endsection