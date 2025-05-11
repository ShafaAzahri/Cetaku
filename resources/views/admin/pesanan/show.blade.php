@extends('admin.layout.admin')

@section('title', 'Detail Pesanan')

@section('styles')
<style>
    .status-badge {
        display: inline-block;
        padding: 0.4em 0.8em;
        font-size: 0.9rem;
        font-weight: 500;
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
    .detail-section {
        background-color: #f8f9fa;
        border-radius: 5px;
        padding: 20px;
        margin-bottom: 20px;
    }
    .product-table th, .product-table td {
        vertical-align: middle;
    }
    .action-button {
        margin-right: 10px;
    }
    .info-card {
        border: 1px solid #dee2e6;
        border-radius: 8px;
        padding: 15px;
        margin-bottom: 15px;
    }
    .info-card h6 {
        color: #495057;
        font-weight: 600;
        margin-bottom: 10px;
        border-bottom: 1px solid #dee2e6;
        padding-bottom: 5px;
    }
    .status-trail {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 10px 0;
        border-bottom: 1px solid #dee2e6;
        margin-bottom: 15px;
    }
    .status-point {
        display: flex;
        align-items: center;
        flex-direction: column;
        position: relative;
    }
    .status-icon {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 5px;
    }
    .status-active {
        background-color: #28a745;
        color: white;
    }
    .status-inactive {
        background-color: #e9ecef;
        color: #adb5bd;
    }
    .status-current {
        background-color: #007bff;
        color: white;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-md-6">
            <h1 class="h3 mb-0 text-gray-800">Detail Pesanan #{{ $pesanan['id'] }}</h1>
        </div>
        <div class="col-md-6 text-md-end">
            <a href="{{ route('admin.pesanan.index') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left me-1"></i> Kembali ke Daftar
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

    <!-- Status Trail -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-white">
            <h6 class="mb-0">Status Pesanan</h6>
        </div>
        <div class="card-body">
            <div class="status-trail">
                @php
                    $statuses = ['Pemesanan', 'Dikonfirmasi', 'Sedang Diproses', 'Menunggu Pengambilan', 'Sedang Dikirim', 'Selesai'];
                    $currentStatus = $pesanan['status'];
                    $currentIndex = array_search($currentStatus, $statuses);
                @endphp
                
                @foreach($statuses as $index => $status)
                    @if($pesanan['metode'] == 'Ambil di Tempat' && $status == 'Sedang Dikirim')
                        @continue
                    @endif
                    
                    <div class="status-point">
                        <div class="status-icon {{ $index < $currentIndex ? 'status-active' : ($index == $currentIndex ? 'status-current' : 'status-inactive') }}">
                            @if($index < $currentIndex)
                                <i class="fas fa-check"></i>
                            @elseif($index == $currentIndex)
                                <i class="fas fa-clock"></i>
                            @else
                                {{ $index + 1 }}
                            @endif
                        </div>
                        <small class="text-muted">{{ $status }}</small>
                    </div>
                @endforeach
            </div>
            
            <div class="text-center">
                <h5>Status Saat Ini: <span class="status-badge status-{{ strtolower(str_replace(' ', '-', $pesanan['status'])) }}">{{ $pesanan['status'] }}</span></h5>
            </div>
        </div>
    </div>

    <!-- Informasi Pesanan -->
    <div class="row">
        <div class="col-md-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3 bg-white">
                    <h6 class="mb-0">Informasi Pesanan</h6>
                </div>
                <div class="card-body">
                    <div class="detail-section">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label class="form-label font-weight-bold">ID Pemesanan</label>
                                    <input type="text" class="form-control" value="#{{ $pesanan['id'] }}" readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label class="form-label font-weight-bold">ID Pelanggan</label>
                                    <input type="text" class="form-control" value="#{{ $pesanan['pelanggan_id'] }}" readonly>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group mb-3">
                            <label class="form-label font-weight-bold">Alamat</label>
                            <input type="text" class="form-control" value="{{ $pesanan['alamat'] }}" readonly>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label class="form-label font-weight-bold">Metode Pengambilan</label>
                                    <input type="text" class="form-control" value="{{ $pesanan['metode'] }}" readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label class="form-label font-weight-bold">Estimasi Selesai</label>
                                    <input type="text" class="form-control" value="{{ $pesanan['estimasi_selesai'] }}" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Detail Produk -->
            <div class="card shadow mb-4">
                <div class="card-header py-3 bg-white">
                    <h6 class="mb-0">Detail Produk</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover product-table">
                            <thead class="table-light">
                                <tr>
                                    <th>Produk</th>
                                    <th>Bahan</th>
                                    <th>Ukuran</th>
                                    <th>Jumlah</th>
                                    <th>Harga Satuan</th>
                                    <th>Subtotal</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($pesanan['produk_items'] as $item)
                                <tr>
                                    <td>{{ $item['nama'] }}</td>
                                    <td>{{ $item['bahan'] }}</td>
                                    <td>{{ $item['ukuran'] }}</td>
                                    <td>{{ $item['jumlah'] }}</td>
                                    <td>Rp {{ number_format($item['harga_satuan'], 0, ',', '.') }}</td>
                                    <td>Rp {{ number_format($item['subtotal'], 0, ',', '.') }}</td>
                                    <td>
                                        <a href="{{ route('admin.pesanan.detail-produk', ['id' => $pesanan['id'], 'produk_id' => $item['id']]) }}" 
                                           class="btn btn-info btn-sm" title="Lihat Detail">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <th colspan="5" class="text-end">Total</th>
                                    <th>Rp {{ number_format($pesanan['total'], 0, ',', '.') }}</th>
                                    <th></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Sidebar Kanan -->
        <div class="col-md-4">
            <!-- Informasi Pelanggan -->
            <div class="info-card">
                <h6>Informasi Pelanggan</h6>
                <p class="mb-1"><strong>Nama:</strong> {{ $pesanan['pelanggan'] }}</p>
                <p class="mb-1"><strong>ID:</strong> #{{ $pesanan['pelanggan_id'] }}</p>
                <p class="mb-0"><strong>Tanggal:</strong> {{ $pesanan['tanggal'] }}</p>
            </div>
            
            <!-- Info Tambahan -->
            <div class="info-card">
                <h6>Informasi Tambahan</h6>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" disabled {{ $pesanan['dengan_jasa_edit'] ? 'checked' : '' }}>
                    <label class="form-check-label">
                        Dengan Jasa Edit
                    </label>
                </div>
            </div>
            
            <!-- Catatan -->
            <div class="info-card">
                <h6>Catatan</h6>
                <p class="mb-0">{{ $pesanan['catatan'] }}</p>
            </div>
            
            <!-- Update Status -->
            <div class="card shadow mb-4">
                <div class="card-header py-3 bg-white">
                    <h6 class="mb-0">Update Status</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.pesanan.update-status', $pesanan['id']) }}" method="POST">
                        @csrf
                        <div class="form-group mb-3">
                            <label for="status" class="form-label">Status Baru</label>
                            <select class="form-select" name="status" required>
                                @foreach($statusList as $key => $label)
                                    <option value="{{ $key }}" {{ $pesanan['status'] == $key ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="form-group mb-3">
                            <label for="catatan" class="form-label">Catatan (Opsional)</label>
                            <textarea class="form-control" name="catatan" rows="3" placeholder="Tambahkan catatan untuk perubahan status..."></textarea>
                        </div>
                        
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-save me-1"></i> Update Status
                        </button>
                    </form>
                </div>
            </div>
            
            <!-- Aksi Pesanan -->
            <div class="card shadow mb-4">
                <div class="card-header py-3 bg-white">
                    <h6 class="mb-0">Aksi Pesanan</h6>
                </div>
                <div class="card-body">
                    @if($pesanan['status'] == 'Pemesanan')
                        <a href="{{ route('admin.pesanan.konfirmasi', $pesanan['id']) }}" class="btn btn-success w-100 mb-2">
                            <i class="fas fa-check me-1"></i> Konfirmasi Pesanan
                        </a>
                    @endif
                    
                    @if($pesanan['status'] == 'Dikonfirmasi' || $pesanan['status'] == 'Sedang Diproses')
                        <a href="{{ route('admin.pesanan.proses', $pesanan['id']) }}" class="btn btn-primary w-100 mb-2">
                            <i class="fas fa-print me-1"></i> Proses Cetak
                        </a>
                    @endif
                    
                    @if($pesanan['status'] == 'Menunggu Pengambilan' && $pesanan['metode'] == 'Ambil di Tempat')
                        <form action="{{ route('admin.pesanan.confirm-pickup', $pesanan['id']) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-success w-100 mb-2" onclick="return confirm('Konfirmasi pengambilan pesanan?')">
                                <i class="fas fa-handshake me-1"></i> Konfirmasi Pengambilan
                            </button>
                        </form>
                    @endif
                    
                    @if($pesanan['status'] == 'Sedang Diproses' && $pesanan['metode'] == 'Dikirim')
                        <a href="{{ route('admin.pesanan.kirim', $pesanan['id']) }}" class="btn btn-info w-100 mb-2">
                            <i class="fas fa-truck me-1"></i> Konfirmasi Pengiriman
                        </a>
                    @endif
                    
                    @if($pesanan['status'] == 'Sedang Dikirim')
                        <form action="{{ route('admin.pesanan.confirm-delivery', $pesanan['id']) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-success w-100 mb-2" onclick="return confirm('Konfirmasi penerimaan pesanan?')">
                                <i class="fas fa-box me-1"></i> Konfirmasi Penerimaan
                            </button>
                        </form>
                    @endif
                    
                    @if(in_array($pesanan['status'], ['Pemesanan', 'Dikonfirmasi', 'Sedang Diproses', 'Menunggu Pengambilan', 'Sedang Dikirim']))
                        <hr>
                        <form action="{{ route('admin.pesanan.cancel', $pesanan['id']) }}" method="POST">
                            @csrf
                            <input type="hidden" name="alasan" value="Dibatalkan oleh admin">
                            <button type="submit" class="btn btn-danger w-100" onclick="return confirm('PERHATIAN! Apakah Anda yakin ingin MEMBATALKAN pesanan ini?')">
                                <i class="fas fa-times me-1"></i> Batalkan Pesanan
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Auto-submit status update form
        $('select[name="status"]').change(function() {
            var status = $(this).val();
            var currentStatus = '{{ $pesanan["status"] }}';
            
            // Prevent redundant updates
            if(status === currentStatus) {
                $(this).val(currentStatus);
                return;
            }
            
            // Warning for critical status changes
            if(status === 'Dibatalkan') {
                if(!confirm('Anda yakin ingin membatalkan pesanan ini?')) {
                    $(this).val(currentStatus);
                    return;
                }
            }
        });
        
        // Status point animation on load
        $('.status-point .status-icon').each(function(index) {
            $(this).css('animation-delay', index * 0.1 + 's');
        });
    });
</script>
@endsection