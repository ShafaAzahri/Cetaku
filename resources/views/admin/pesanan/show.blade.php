@extends('admin.layout.admin')

@section('title', 'Detail Pesanan #' . $pesanan['id'])

@section('styles')
<style>
    /* Halaman keseluruhan */
    .content-wrapper {
        background-color: #f8f9fa;
    }
    
    /* Card style */
    .detail-card {
        background: white;
        border-radius: 5px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        padding: 20px;
        margin-bottom: 20px;
    }
    
    .detail-card h5 {
        font-weight: 600;
        margin-bottom: 20px;
        padding-bottom: 10px;
        border-bottom: 1px solid #eee;
    }
    
    /* Progress Track / Status Bar */
    .progress-track {
        display: flex;
        list-style: none;
        padding: 0;
        margin: 20px 0;
    }
    
    .progress-track li {
        flex: 1;
        position: relative;
        text-align: center;
    }
    
    .progress-track .step {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background-color: #dee2e6;
        color: white;
        line-height: 40px;
        margin: 0 auto 8px;
        position: relative;
        z-index: 2;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .progress-track .step-label {
        font-size: 12px;
        color: #6c757d;
    }
    
    .progress-track li.active .step {
        background-color: #007bff;
    }
    
    .progress-track li.completed .step {
        background-color: #28a745;
    }
    
    .progress-track li.current .step {
        background-color: #007bff;
    }
    
    /* Information row style */
    .info-row {
        display: flex;
        margin-bottom: 15px;
    }
    
    .info-label {
        width: 40%;
        color: #6c757d;
    }
    
    .info-value {
        width: 60%;
        font-weight: 500;
    }
    
    /* Produk collapse styling */
    .produk-item {
        padding: 10px 15px;
        border-radius: 5px;
        border: 1px solid #eee;
        margin-bottom: 10px;
        cursor: pointer;
    }
    
    .produk-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .produk-header .produk-title {
        display: flex;
        align-items: center;
    }
    
    .produk-header .produk-title i {
        margin-right: 10px;
    }
    
    .produk-header .chevron {
        transition: transform 0.3s;
    }
    
    .produk-header[aria-expanded="true"] .chevron {
        transform: rotate(180deg);
    }
    
    .produk-content {
        padding-top: 15px;
        margin-top: 15px;
        border-top: 1px solid #eee;
    }
    
    /* Total section */
    .total-section {
        text-align: right;
        margin-top: 15px;
        padding-top: 15px;
        border-top: 1px solid #eee;
    }
    
    .total-section .subtotal, 
    .total-section .ongkir {
        color: #6c757d;
        margin-bottom: 5px;
    }
    
    .total-section .total {
        font-weight: 600;
        font-size: 1.1rem;
    }
    
    /* Action buttons */
    .action-btn {
        display: block;
        width: 100%;
        padding: 12px;
        margin-bottom: 10px;
        border-radius: 5px;
        text-align: center;
        font-weight: 500;
        transition: all 0.2s;
    }
    
    .action-btn.btn-complete {
        background-color: #00BCD4;
        color: white;
        border: none;
    }
    
    .action-btn.btn-ship {
        background-color: #4361ee;
        color: white;
        border: none;
    }
    
    .action-btn.btn-upload {
        background-color: white;
        color: #4361ee;
        border: 1px solid #4361ee;
    }
    
    .action-btn.btn-cancel {
        background-color: #ef4444;
        color: white;
        border: none;
    }
    
    .action-btn i {
        margin-right: 5px;
    }
    
    /* Update Status Form */
    .status-form select,
    .status-form textarea {
        margin-bottom: 15px;
    }
    
    .status-form button {
        width: 100%;
        padding: 12px;
    }
    
    /* Design preview styling */
    .design-preview {
        position: relative;
        margin-bottom: 15px;
    }

    .design-preview img {
        cursor: pointer;
        transition: all 0.2s;
        max-width: 100%;
        border: 1px solid #dee2e6;
    }

    .design-preview img:hover {
        transform: scale(1.03);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }

    .cursor-pointer {
        cursor: pointer;
    }

    /* Styling untuk tab */
    .nav-tabs .nav-link {
        padding: 8px 15px;
        border-radius: 0;
        font-weight: 500;
    }

    .nav-tabs .nav-link.active {
        background-color: #f8f9fa;
        border-bottom-color: #f8f9fa;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4>Detail Pesanan #{{ $pesanan['id'] }}</h4>
        <a href="{{ route('admin.pesanan.index') }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left me-1"></i> Kembali ke Daftar
        </a>
    </div>
    
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
    
    <!-- Status Pesanan -->
    <div class="detail-card">
        <h5>Status Pesanan</h5>
        <ul class="progress-track">
            <li class="{{ in_array($pesanan['status'], ['Pemesanan', 'Dikonfirmasi', 'Sedang Diproses', 'Menunggu Pengambilan', 'Sedang Dikirim', 'Selesai']) ? 'completed' : '' }}
                     {{ $pesanan['status'] == 'Pemesanan' ? 'current' : '' }}">
                <div class="step"><i class="fas fa-shopping-cart"></i></div>
                <div class="step-label">Pemesanan</div>
            </li>
            <li class="{{ in_array($pesanan['status'], ['Dikonfirmasi', 'Sedang Diproses', 'Menunggu Pengambilan', 'Sedang Dikirim', 'Selesai']) ? 'completed' : '' }}
                     {{ $pesanan['status'] == 'Dikonfirmasi' ? 'current' : '' }}">
                <div class="step"><i class="fas fa-clipboard-check"></i></div>
                <div class="step-label">Dikonfirmasi</div>
            </li>
            <li class="{{ in_array($pesanan['status'], ['Sedang Diproses', 'Menunggu Pengambilan', 'Sedang Dikirim', 'Selesai']) ? 'completed' : '' }}
                     {{ $pesanan['status'] == 'Sedang Diproses' ? 'current' : '' }}">
                <div class="step"><i class="fas fa-cogs"></i></div>
                <div class="step-label">Sedang Diproses</div>
            </li>
            <li class="{{ in_array($pesanan['status'], ['Menunggu Pengambilan', 'Sedang Dikirim', 'Selesai']) ? 'completed' : '' }}
                     {{ $pesanan['status'] == 'Menunggu Pengambilan' || $pesanan['status'] == 'Sedang Dikirim' ? 'current' : '' }}">
                <div class="step"><i class="{{ $pesanan['metode_pengambilan'] == 'antar' ? 'fas fa-truck' : 'fas fa-hourglass-half' }}"></i></div>
                <div class="step-label">{{ $pesanan['metode_pengambilan'] == 'antar' ? 'Pengiriman' : 'Menunggu Pengambilan' }}</div>
            </li>
            <li class="{{ $pesanan['status'] == 'Selesai' ? 'completed' : '' }}
                     {{ $pesanan['status'] == 'Selesai' ? 'current' : '' }}">
                <div class="step"><i class="fas fa-check"></i></div>
                <div class="step-label">Selesai</div>
            </li>
        </ul>
        
        <div class="text-center mt-2">
            <span>Status Saat Ini: <strong>{{ $pesanan['status'] }}</strong></span>
        </div>
    </div>
    
    <div class="row">
        <!-- Kolom Kiri: Informasi Pesanan dan Produk -->
        <div class="col-md-8">
            <!-- Informasi Pesanan -->
            <div class="detail-card">
                <h5>Informasi Pesanan</h5>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="info-row">
                            <div class="info-label">ID Pemesanan</div>
                            <div class="info-value">#{{ $pesanan['id'] }}</div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">Tanggal Pemesanan</div>
                            <div class="info-value">{{ \Carbon\Carbon::parse($pesanan['tanggal_dipesan'])->format('d M Y, H:i') }}</div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">Metode Pengambilan</div>
                            <div class="info-value">{{ $pesanan['metode_pengambilan'] == 'antar' ? 'Dikirim' : 'Ambil di Tempat' }}</div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">Estimasi Selesai</div>
                            <div class="info-value">{{ $pesanan['estimasi_waktu'] ?? '5 jam' }}</div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="info-row">
                            <div class="info-label">Ekspedisi</div>
                            <div class="info-value">{{ $pesanan['ekspedisi']['nama_ekspedisi'] ?? 'JNE' }}</div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">Layanan</div>
                            <div class="info-value">{{ $pesanan['ekspedisi']['layanan'] ?? 'Regular' }}</div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">Biaya Pengiriman</div>
                            <div class="info-value">Rp {{ number_format($pesanan['ekspedisi']['ongkos_kirim'] ?? 15000, 0, ',', '.') }}</div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">Admin</div>
                            <div class="info-value">{{ $pesanan['admin']['nama'] ?? 'Belum ditentukan' }}</div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Detail Produk -->
            <div class="detail-card">
                <h5>Detail Produk</h5>
                
                @forelse($pesanan['detail_pesanans'] ?? [] as $index => $detail)
                <div class="produk-item">
                    <div class="produk-header" data-bs-toggle="collapse" data-bs-target="#collapse{{ $index }}" aria-expanded="{{ $index === 0 ? 'true' : 'false' }}" aria-controls="collapse{{ $index }}">
                        <div class="produk-title">
                            <i class="fas {{ $detail['custom']['item']['nama_item'] === 'Jaket' ? 'fa-vest' : 'fa-tshirt' }}"></i>
                            <strong>{{ $detail['custom']['item']['nama_item'] ?? 'Produk' }}</strong>
                            
                            <!-- Indikator Status Produksi -->
                            @if(isset($detail['proses_pesanan']))
                                <span class="badge bg-info ms-2">
                                    <i class="fas fa-cogs me-1"></i>Produksi Ditugaskan
                                </span>
                            @else
                                <span class="badge bg-warning ms-2">
                                    <i class="fas fa-clock me-1"></i>Menunggu Penugasan
                                </span>
                            @endif
                        </div>
                        <div class="d-flex align-items-center">
                            <span class="me-3">{{ $detail['jumlah'] ?? 1 }} pcs</span>
                            <i class="fas fa-chevron-down chevron"></i>
                        </div>
                    </div>
                    
                    <div class="collapse {{ $index === 0 ? 'show' : '' }}" id="collapse{{ $index }}">
                        <div class="produk-content">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="info-row">
                                        <div class="info-label">Bahan</div>
                                        <div class="info-value">{{ $detail['custom']['bahan']['nama_bahan'] ?? 'Cotton Combed 24s' }}</div>
                                    </div>
                                    <div class="info-row">
                                        <div class="info-label">Ukuran</div>
                                        <div class="info-value">{{ $detail['custom']['ukuran']['size'] ?? 'M' }}</div>
                                    </div>
                                    <div class="info-row">
                                        <div class="info-label">Jenis</div>
                                        <div class="info-value">{{ $detail['custom']['jenis']['kategori'] ?? 'Lengan Pendek' }}</div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="info-row">
                                        <div class="info-label">Tipe Desain</div>
                                        <div class="info-value">{{ $detail['tipe_desain'] == 'sendiri' ? 'Upload Sendiri' : 'Dibuatkan' }}</div>
                                    </div>
                                    <div class="info-row">
                                        <div class="info-label">Biaya Desain</div>
                                        <div class="info-value">
                                            @if($detail['tipe_desain'] == 'sendiri')
                                                Rp 0
                                            @else
                                                Rp {{ number_format($detail['biaya_jasa'] ?? $default_biaya_desain ?? 20000, 0, ',', '.') }}
                                            @endif
                                        </div>
                                    </div>
                                    <div class="info-row">
                                        <div class="info-label">Harga Satuan</div>
                                        <div class="info-value">Rp {{ number_format($detail['custom']['harga'] ?? 80000, 0, ',', '.') }}</div>
                                    </div>
                                    <div class="info-row">
                                        <div class="info-label">Total</div>
                                        <div class="info-value">Rp {{ number_format($detail['total_harga'] ?? 160000, 0, ',', '.') }}</div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Bagian Desain -->
                            @if($detail['upload_desain'] || $detail['desain_revisi'])
                            <div class="mt-3 pt-3 border-top">
                                <div class="row">
                                    <!-- Desain dari User -->
                                    @if($detail['upload_desain'] && $detail['tipe_desain'] == 'sendiri')
                                    <div class="col-md-6 mb-3">
                                        <h6 class="mb-2">Desain dari Pelanggan:</h6>
                                        <div class="design-preview">
                                            <img src="{{ asset('storage/' . $detail['upload_desain']) }}" 
                                                alt="Desain Pelanggan" 
                                                class="img-thumbnail cursor-pointer" 
                                                style="max-height: 200px;"
                                                data-bs-toggle="modal" 
                                                data-bs-target="#designModal{{ $index }}">
                                            <div class="mt-2">
                                                <a href="{{ asset('storage/' . $detail['upload_desain']) }}" download class="btn btn-sm btn-outline-secondary">
                                                    <i class="fas fa-download"></i> Download
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                    @endif
                                    
                                    <!-- Desain dari Admin/Toko -->
                                    @if($detail['upload_desain'] && $detail['tipe_desain'] == 'dibuatkan')
                                    <div class="col-md-6 mb-3">
                                        <h6 class="mb-2">Desain dari Toko:</h6>
                                        <div class="design-preview">
                                            <img src="{{ asset('storage/' . $detail['upload_desain']) }}" 
                                                alt="Desain Toko" 
                                                class="img-thumbnail cursor-pointer" 
                                                style="max-height: 200px;"
                                                data-bs-toggle="modal" 
                                                data-bs-target="#designModal{{ $index }}">
                                            <div class="mt-2">
                                                <a href="{{ asset('storage/' . $detail['upload_desain']) }}" download class="btn btn-sm btn-outline-secondary">
                                                    <i class="fas fa-download"></i> Download
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                    @endif
                                    
                                    <!-- Revisi Desain -->
                                    @if($detail['desain_revisi'])
                                    <div class="col-md-6 mb-3">
                                        <h6 class="mb-2">Revisi Desain:</h6>
                                        <div class="design-preview">
                                            <img src="{{ asset('storage/' . $detail['desain_revisi']) }}" 
                                                alt="Revisi Desain" 
                                                class="img-thumbnail cursor-pointer" 
                                                style="max-height: 200px;"
                                                data-bs-toggle="modal" 
                                                data-bs-target="#designModal{{ $index }}">
                                            <div class="mt-2">
                                                <a href="{{ asset('storage/' . $detail['desain_revisi']) }}" download class="btn btn-sm btn-outline-secondary">
                                                    <i class="fas fa-download"></i> Download
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                    @endif
                                </div>
                            </div>
                            @endif
                            
                            @if(isset($detail['proses_pesanan']))
                            <div class="mt-3 pt-3 border-top">
                                <div class="mb-2 fw-bold">Informasi Produksi:</div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="info-row">
                                            <div class="info-label">Operator</div>
                                            <div class="info-value">{{ $detail['proses_pesanan']['operator']['nama'] ?? 'Ahmad Rizky' }}</div>
                                        </div>
                                        <div class="info-row">
                                            <div class="info-label">Mesin</div>
                                            <div class="info-value">{{ $detail['proses_pesanan']['mesin']['nama_mesin'] ?? 'Mesin Cetak A' }}</div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="info-row">
                                            <div class="info-label">Waktu Mulai</div>
                                            <div class="info-value">
                                                @if(isset($detail['proses_pesanan']['waktu_mulai']))
                                                    {{ \Carbon\Carbon::parse($detail['proses_pesanan']['waktu_mulai'])->format('d M Y, H:i') }}
                                                @else
                                                    11 May 2025, 00:57
                                                @endif
                                            </div>
                                        </div>
                                        <div class="info-row">
                                            <div class="info-label">Status Proses</div>
                                            <div class="info-value">{{ $detail['proses_pesanan']['status_proses'] ?? 'Ditugaskan' }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
                @empty
                <div class="alert alert-info">Tidak ada detail produk</div>
                @endforelse
                
                <!-- Total -->
                <div class="total-section">
                    @php
                        $subTotal = 0;
                        $totalBiayaDesain = 0;
                        
                        foreach ($pesanan['detail_pesanans'] ?? [] as $detail) {
                            $hargaProduk = $detail['custom']['harga'] ?? 0;
                            $jumlah = $detail['jumlah'] ?? 1;
                            $subTotal += $hargaProduk * $jumlah;
                            
                            // Hitung biaya desain
                            if($detail['tipe_desain'] == 'dibuatkan') {
                                $totalBiayaDesain += $biayaDesain;
                            }
                        }
                        
                        $ongkir = $pesanan['ekspedisi']['ongkos_kirim'] ?? 0;
                        $grandTotal = $subTotal + $totalBiayaDesain + $ongkir;
                    @endphp
                    
                    <div class="subtotal">Subtotal Produk: Rp {{ number_format($subTotal, 0, ',', '.') }}</div>
                    <div class="biaya-desain">Biaya Desain: Rp {{ number_format($totalBiayaDesain, 0, ',', '.') }}</div>
                    @if($pesanan['metode_pengambilan'] == 'antar')
                    <div class="ongkir">Ongkos Kirim: Rp {{ number_format($ongkir, 0, ',', '.') }}</div>
                    @endif
                    <div class="total">Total: Rp {{ number_format($grandTotal, 0, ',', '.') }}</div>
                </div>
            </div>
        </div>
        
        <!-- Kolom Kanan: Informasi Pelanggan dan Aksi -->
        <div class="col-md-4">
            <!-- Informasi Pelanggan -->
            <div class="detail-card">
                <h5>Informasi Pelanggan</h5>
                <div class="info-row">
                    <div class="info-label">Nama</div>
                    <div class="info-value">{{ $pesanan['user']['nama'] ?? 'Iswanti' }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">ID</div>
                    <div class="info-value">#{{ $pesanan['user']['id'] ?? '2' }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Email</div>
                    <div class="info-value">{{ $pesanan['user']['email'] ?? 'iswanti@gmail.com' }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Tanggal</div>
                    <div class="info-value">{{ \Carbon\Carbon::parse($pesanan['tanggal_dipesan'])->format('d M Y') }}</div>
                </div>
            </div>
            
            <!-- Catatan -->
            <div class="detail-card">
                <h5>Catatan</h5>
                <p>{{ $pesanan['catatan'] ?? 'Tidak ada catatan' }}</p>
            </div>
            
            <!-- Update Status -->
            <div class="detail-card">
                <h5>Update Status</h5>
                <form action="{{ route('admin.pesanan.update-status', $pesanan['id']) }}" method="POST" class="status-form">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label for="status" class="form-label">Status Baru</label>
                        <select name="status" id="status" class="form-select">
                            @foreach($statusOptions ?? ['Pemesanan', 'Dikonfirmasi', 'Sedang Diproses', 'Menunggu Pengambilan', 'Sedang Dikirim', 'Selesai', 'Dibatalkan'] as $statusOption)
                            <option value="{{ $statusOption }}" {{ $pesanan['status'] == $statusOption ? 'selected' : '' }}>
                                {{ $statusOption }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="catatan" class="form-label">Catatan (Opsional)</label>
                        <textarea name="catatan" id="catatan" rows="3" class="form-control" placeholder="Tambahkan catatan untuk perubahan status..."></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> Update Status
                    </button>
                </form>
            </div>
            
            <!-- Aksi Pesanan -->
            <div class="detail-card">
                <h5>Aksi Pesanan</h5>
                
                <!-- Tombol ini mungkin tidak ada atau tersembunyi -->
                @if($pesanan['status'] == 'Dikonfirmasi')
                    <!-- Cek apakah masih ada produk yang belum ditugaskan -->
                    @php
                        $unassignedExists = false;
                        foreach($pesanan['detail_pesanans'] ?? [] as $detail) {
                            if(!isset($detail['proses_pesanan'])) {
                                $unassignedExists = true;
                                break;
                            }
                        }
                    @endphp
                    
                    @if($unassignedExists)
                    <button type="button" class="action-btn btn-primary w-100 mb-2" data-bs-toggle="modal" data-bs-target="#assignProductionModal">
                        <i class="fas fa-tasks me-1"></i> Tugaskan Produksi
                    </button>
                    @endif
                @endif
                <!-- Selesaikan Produksi -->
                @if($pesanan['status'] == 'Sedang Diproses')
                <button type="button" class="action-btn btn-complete" data-bs-toggle="modal" data-bs-target="#completeProductionModal">
                    <i class="fas fa-check-double"></i> Selesaikan Produksi
                </button>
                @endif
                
                <!-- Konfirmasi Pengiriman -->
                @if($pesanan['status'] == 'Sedang Diproses' && $pesanan['metode_pengambilan'] == 'antar')
                <button type="button" class="action-btn btn-ship" data-bs-toggle="modal" data-bs-target="#shipmentModal">
                    <i class="fas fa-truck"></i> Konfirmasi Pengiriman
                </button>
                @endif
                
                <!-- Upload Desain -->
                @if(in_array($pesanan['status'], ['Dikonfirmasi', 'Sedang Diproses']))
                <button type="button" class="action-btn btn-upload" data-bs-toggle="modal" data-bs-target="#uploadDesignModal">
                    <i class="fas fa-upload"></i> Upload Desain
                </button>
                @endif
                
                <!-- Batalkan Pesanan -->
                @if(!in_array($pesanan['status'], ['Selesai', 'Dibatalkan']))
                <form action="{{ route('admin.pesanan.cancel', $pesanan['id']) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin membatalkan pesanan ini?')">
                    @csrf
                    <button type="submit" class="action-btn btn-cancel">
                        <i class="fas fa-times-circle"></i> Batalkan Pesanan
                    </button>
                </form>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Design Preview Modals -->
@foreach($pesanan['detail_pesanans'] ?? [] as $index => $detail)
    @if($detail['upload_desain'] || $detail['desain_revisi'])
    <!-- Modal Preview Desain untuk Produk {{ $index }} -->
    <div class="modal fade" id="designModal{{ $index }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Preview Desain - {{ $detail['custom']['item']['nama_item'] ?? 'Produk' }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <!-- Tabs untuk berbagai jenis desain -->
                    <ul class="nav nav-tabs mb-3" id="designTabs{{ $index }}" role="tablist">
                        @if($detail['upload_desain'] && $detail['tipe_desain'] == 'sendiri')
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="user-tab-{{ $index }}" data-bs-toggle="tab" data-bs-target="#user-design-{{ $index }}" type="button" role="tab" aria-controls="user-design-{{ $index }}" aria-selected="true">Desain Pelanggan</button>
                        </li>
                        @endif
                        
                        @if($detail['upload_desain'] && $detail['tipe_desain'] == 'dibuatkan')
                        <li class="nav-item" role="presentation">
                            <button class="nav-link {{ $detail['tipe_desain'] == 'dibuatkan' ? 'active' : '' }}" id="shop-tab-{{ $index }}" data-bs-toggle="tab" data-bs-target="#shop-design-{{ $index }}" type="button" role="tab" aria-controls="shop-design-{{ $index }}" aria-selected="{{ $detail['tipe_desain'] == 'dibuatkan' ? 'true' : 'false' }}">Desain Toko</button>
                        </li>
                        @endif
                        
                        @if($detail['desain_revisi'])
                        <li class="nav-item" role="presentation">
                            <button class="nav-link {{ !$detail['upload_desain'] ? 'active' : '' }}" id="revision-tab-{{ $index }}" data-bs-toggle="tab" data-bs-target="#revision-design-{{ $index }}" type="button" role="tab" aria-controls="revision-design-{{ $index }}" aria-selected="{{ !$detail['upload_desain'] ? 'true' : 'false' }}">Revisi Desain</button>
                        </li>
                        @endif
                    </ul>
                    
                    <!-- Tab content -->
                    <div class="tab-content" id="designTabsContent{{ $index }}">
                        @if($detail['upload_desain'] && $detail['tipe_desain'] == 'sendiri')
                        <div class="tab-pane fade show active" id="user-design-{{ $index }}" role="tabpanel" aria-labelledby="user-tab-{{ $index }}">
                            <img src="{{ asset('storage/' . $detail['upload_desain']) }}" alt="Desain Pelanggan" class="img-fluid" style="max-height: 70vh;">
                            <div class="mt-3">
                                <a href="{{ asset('storage/' . $detail['upload_desain']) }}" download class="btn btn-primary">
                                    <i class="fas fa-download me-1"></i> Download
                                </a>
                            </div>
                        </div>
                        @endif
                        
                        @if($detail['upload_desain'] && $detail['tipe_desain'] == 'dibuatkan')
                        <div class="tab-pane fade {{ $detail['tipe_desain'] == 'dibuatkan' ? 'show active' : '' }}" id="shop-design-{{ $index }}" role="tabpanel" aria-labelledby="shop-tab-{{ $index }}">
                            <img src="{{ asset('storage/' . $detail['upload_desain']) }}" alt="Desain Toko" class="img-fluid" style="max-height: 70vh;">
                            <div class="mt-3">
                                <a href="{{ asset('storage/' . $detail['upload_desain']) }}" download class="btn btn-primary">
                                    <i class="fas fa-download me-1"></i> Download
                                </a>
                            </div>
                        </div>
                        @endif
                        
                        @if($detail['desain_revisi'])
                        <div class="tab-pane fade {{ !$detail['upload_desain'] ? 'show active' : '' }}" id="revision-design-{{ $index }}" role="tabpanel" aria-labelledby="revision-tab-{{ $index }}">
                            <img src="{{ asset('storage/' . $detail['desain_revisi']) }}" alt="Revisi Desain" class="img-fluid" style="max-height: 70vh;">
                            <div class="mt-3">
                                <a href="{{ asset('storage/' . $detail['desain_revisi']) }}" download class="btn btn-primary">
                                    <i class="fas fa-download me-1"></i> Download
                                </a>
                            </div>
                        </div>
                        @endif
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

<!-- Other Modals -->
@include('admin.pesanan.modals')
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Show modal on validation errors
        @if ($errors->has('detail_pesanan_id') || $errors->has('mesin_id') || $errors->has('operator_id'))
            var assignModal = document.getElementById('assignProductionModal');
            if (assignModal) {
                var modal = new bootstrap.Modal(assignModal);
                modal.show();
            }
        @endif
        
        @if ($errors->has('proses_pesanan_id'))
            var completeModal = document.getElementById('completeProductionModal');
            if (completeModal) {
                var modal = new bootstrap.Modal(completeModal);
                modal.show();
            }
        @endif
        
        @if ($errors->has('ekspedisi_id') || $errors->has('nomor_resi'))
            var shipmentModal = document.getElementById('shipmentModal');
            if (shipmentModal) {
                var modal = new bootstrap.Modal(shipmentModal);
                modal.show();
            }
        @endif
        
        @if ($errors->has('desain') || $errors->has('tipe'))
            var uploadModal = document.getElementById('uploadDesignModal');
            if (uploadModal) {
                var modal = new bootstrap.Modal(uploadModal);
                modal.show();
            }
        @endif
    });
</script>
@endsection