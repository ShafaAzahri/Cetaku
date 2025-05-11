@extends('admin.layout.admin')

@section('title', 'Manajemen Pesanan')

@section('styles')
<style>
    .status-badge {
        padding: 5px 10px;
        border-radius: 4px;
        font-size: 12px;
        font-weight: 500;
    }
    .status-pemesanan { background-color: #e2e8f0; color: #1a202c; }
    .status-dikonfirmasi { background-color: #90cdf4; color: #2c5282; }
    .status-diproses { background-color: #fbd38d; color: #744210; }
    .status-menunggu { background-color: #fbd38d; color: #744210; }
    .status-dikirim { background-color: #90cdf4; color: #2c5282; }
    .status-selesai { background-color: #9ae6b4; color: #22543d; }
    .status-dibatalkan { background-color: #feb2b2; color: #822727; }
    
    .pesanan-filter {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-bottom: 20px;
    }
    
    .action-btn {
        width: 32px;
        height: 32px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: #f1f5f9;
        border-radius: 4px;
        border: none;
        cursor: pointer;
        color: #475569;
        transition: all 0.2s;
    }
    
    .action-btn:hover {
        background: #e2e8f0;
    }
    
    .action-btn.info { color: #3b82f6; }
    .action-btn.print { color: #10b981; }
    .action-btn.ship { color: #6366f1; }
    .action-btn.cancel { color: #ef4444; }
    
    .status-summary {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 10px;
        margin-bottom: 20px;
    }
    
    .status-card {
        padding: 15px;
        border-radius: 8px;
        background: white;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }
    
    .status-card h3 {
        font-size: 24px;
        margin: 0;
    }
    
    .status-card p {
        color: #64748b;
        margin: 5px 0 0;
    }
</style>
@endsection

@section('content')   
    <!-- Flash Messages -->
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
    
    <div class="card mb-4">
        <div class="card-body">
            <form action="{{ route('admin.pesanan.index') }}" method="GET" class="row g-3">
                <!-- Filters -->
                <div class="col-md-3">
                    <label for="search" class="form-label">Cari</label>
                    <input type="text" class="form-control" id="search" name="search" placeholder="ID Pesanan atau Pelanggan..." value="{{ $search ?? '' }}">
                </div>
                <div class="col-md-2">
                    <label for="status" class="form-label">Status</label>
                    <select name="status" id="status" class="form-select">
                        <option value="Semua Status" {{ ($status ?? '') == 'Semua Status' ? 'selected' : '' }}>Semua Status</option>
                        @foreach($statusOptions as $statusOption)
                        <option value="{{ $statusOption }}" {{ ($status ?? '') == $statusOption ? 'selected' : '' }}>{{ $statusOption }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="dari_tanggal" class="form-label">Dari Tanggal</label>
                    <input type="date" class="form-control" id="dari_tanggal" name="dari_tanggal" value="{{ $dariTanggal ?? '' }}">
                </div>
                <div class="col-md-2">
                    <label for="sampai_tanggal" class="form-label">Sampai Tanggal</label>
                    <input type="date" class="form-control" id="sampai_tanggal" name="sampai_tanggal" value="{{ $sampaiTanggal ?? '' }}">
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search me-1"></i> Cari
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Tanggal</th>
                            <th>Pelanggan</th>
                            <th>Status</th>
                            <th>Metode</th>
                            <th>Produk</th>
                            <th>Total</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pesanans['data'] ?? [] as $pesanan)
                        <tr>
                            <td>#{{ $pesanan['id'] }}</td>
                            <td>{{ \Carbon\Carbon::parse($pesanan['tanggal_dipesan'])->format('Y-m-d') }}</td>
                            <td>{{ $pesanan['user']['nama'] ?? 'Tidak ada data' }}</td>
                            <td>
                                <span class="status-badge status-{{ strtolower(str_replace(' ', '-', $pesanan['status'])) }}">
                                    {{ $pesanan['status'] }}
                                </span>
                            </td>
                            <td>{{ $pesanan['metode_pengambilan'] == 'antar' ? 'Dikirim' : 'Ambil di Tempat' }}</td>
                            <td>
                                @if(isset($pesanan['detail_pesanans']) && count($pesanan['detail_pesanans']) > 0)
                                    {{ $pesanan['detail_pesanans'][0]['custom']['item']['nama_item'] ?? 'Produk tidak diketahui' }}
                                    @if(count($pesanan['detail_pesanans']) > 1)
                                        <span class="text-muted">(+{{ count($pesanan['detail_pesanans'])-1 }} lainnya)</span>
                                    @endif
                                @else
                                    Produk tidak diketahui
                                @endif
                            </td>
                            <td>
                                @php
                                    $total = 0;
                                    if(isset($pesanan['detail_pesanans'])) {
                                        foreach($pesanan['detail_pesanans'] as $detail) {
                                            $total += $detail['total_harga'] ?? 0;
                                        }
                                    }
                                @endphp
                                Rp {{ number_format($total, 0, ',', '.') }}
                            </td>
                            <td>
                                <div class="d-flex gap-1">
                                    <a href="{{ route('admin.pesanan.show', $pesanan['id']) }}" class="action-btn info" title="Detail">
                                        <i class="fas fa-info-circle"></i>
                                    </a>
                                    
                                    @if($pesanan['status'] == 'Sedang Dikirim')
                                    <a href="#" class="action-btn ship" title="Info Pengiriman" data-bs-toggle="modal" data-bs-target="#trackingModal{{ $pesanan['id'] }}">
                                        <i class="fas fa-truck"></i>
                                    </a>
                                    @endif
                                    
                                    @if(!in_array($pesanan['status'], ['Selesai', 'Dibatalkan']))
                                    <form action="{{ route('admin.pesanan.cancel', $pesanan['id']) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin membatalkan pesanan ini?')">
                                        @csrf
                                        <button type="submit" class="action-btn cancel" title="Batalkan">
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
            
            <!-- Pagination -->
            @if(isset($pesanans['links']))
            <div class="d-flex justify-content-end mt-3">
                <nav>
                    <ul class="pagination">
                        @foreach($pesanans['links'] as $link)
                            <li class="page-item {{ $link['active'] ? 'active' : '' }} {{ $link['url'] === null ? 'disabled' : '' }}">
                                <a class="page-link" href="{{ $link['url'] ?? '#' }}">
                                    {!! $link['label'] !!}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </nav>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Tracking Modal -->
@foreach($pesanans['data'] ?? [] as $pesanan)
    @if($pesanan['status'] == 'Sedang Dikirim' && isset($pesanan['ekspedisi']))
    <div class="modal fade" id="trackingModal{{ $pesanan['id'] }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Info Pengiriman #{{ $pesanan['id'] }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Ekspedisi</label>
                        <p>{{ $pesanan['ekspedisi']['nama_ekspedisi'] ?? 'Tidak ada data' }}</p>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Layanan</label>
                        <p>{{ $pesanan['ekspedisi']['layanan'] ?? 'Regular' }}</p>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Estimasi</label>
                        <p>{{ $pesanan['ekspedisi']['estimasi'] ?? '3-5 hari' }}</p>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Ongkir</label>
                        <p>Rp {{ number_format($pesanan['ekspedisi']['ongkos_kirim'] ?? 0, 0, ',', '.') }}</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
    @endif
@endforeach
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Hapus filter
        const clearFilters = document.getElementById('clearFilters');
        if(clearFilters) {
            clearFilters.addEventListener('click', function() {
                window.location.href = "{{ route('admin.pesanan.index') }}";
            });
        }
    });
</script>
@endsection