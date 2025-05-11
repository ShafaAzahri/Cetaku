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
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row mb-2">
        <div class="col-md-6">
            <h1 class="m-0 text-dark">Detail Pesanan #{{ $pesanan['id'] }}</h1>
        </div>
        <div class="col-md-6 text-md-right">
            <a href="{{ route('admin.pesanan.index') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> Kembali ke Daftar
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

    <div class="card">
        <div class="card-body">
            <div class="row detail-section">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label font-weight-bold">ID Pemesanan</label>
                        <input type="text" class="form-control" value="{{ $pesanan['id'] }}" readonly>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label font-weight-bold">ID Pelanggan</label>
                        <input type="text" class="form-control" value="{{ $pesanan['pelanggan_id'] }}" readonly>
                    </div>
                </div>

                <div class="col-12">
                    <div class="form-group">
                        <label class="form-label font-weight-bold">Alamat</label>
                        <input type="text" class="form-control" value="{{ $pesanan['alamat'] }}" readonly>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label font-weight-bold">Metode Pengambilan</label>
                        <input type="text" class="form-control" value="{{ $pesanan['metode'] }}" readonly>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label font-weight-bold">Estimasi Selesai</label>
                        <input type="text" class="form-control" value="{{ $pesanan['estimasi_selesai'] }}" readonly>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Detail Produk</h5>
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
                                <tr id="produk-{{ $item['id'] }}">
                                    <td>{{ $item['nama'] }}</td>
                                    <td>{{ $item['bahan'] }}</td>
                                    <td>{{ $item['ukuran'] }}</td>
                                    <td>{{ $item['jumlah'] }}</td>
                                    <td>Rp {{ number_format($item['harga_satuan'], 0, ',', '.') }}</td>
                                    <td>Rp {{ number_format($item['subtotal'], 0, ',', '.') }}</td>
                                    <td>
                                        <a href="{{ route('admin.pesanan.detail-produk', ['id' => $pesanan['id'], 'produk_id' => $item['id']]) }}" class="btn btn-info btn-sm">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <th colspan="5" class="text-right">Total</th>
                                    <th>Rp {{ number_format($pesanan['total'], 0, ',', '.') }}</th>
                                    <th></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            <div class="row mt-4">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="font-weight-bold">Jasa Tambahan</label>
                        <div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="jasaEdit" disabled {{ $pesanan['dengan_jasa_edit'] ? 'checked' : '' }}>
                                <label class="form-check-label" for="jasaEdit">
                                    Dengan Jasa Edit
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="font-weight-bold">Status Pesanan</label>
                        <div>
                            @php
                                $statusClass = '';
                                switch($pesanan['status']) {
                                    case 'Pemesanan':
                                        $statusClass = 'status-pemesanan';
                                        break;
                                    case 'Dikonfirmasi':
                                        $statusClass = 'status-dikonfirmasi';
                                        break;
                                    case 'Sedang Diproses':
                                        $statusClass = 'status-proses';
                                        break;
                                    case 'Menunggu Pengambilan':
                                        $statusClass = 'status-pengambilan';
                                        break;
                                    case 'Sedang Dikirim':
                                        $statusClass = 'status-dikirim';
                                        break;
                                    case 'Selesai':
                                        $statusClass = 'status-selesai';
                                        break;
                                    case 'Dibatalkan':
                                        $statusClass = 'status-dibatalkan';
                                        break;
                                }
                            @endphp
                            <span class="status-badge {{ $statusClass }}">{{ $pesanan['status'] }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-group mt-3">
                <label class="font-weight-bold">Catatan</label>
                <textarea class="form-control" rows="3" readonly>{{ $pesanan['catatan'] }}</textarea>
            </div>

            <div class="mt-4">
                <form action="{{ route('admin.pesanan.update-status', ['id' => $pesanan['id']]) }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="status" class="form-label">Update Status</label>
                                <select class="form-select" name="status">
                                    @foreach($statusList ?? [] as $status => $label)
                                        <option value="{{ $status }}" {{ $pesanan['status'] == $status ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="catatan" class="form-label">Catatan Status (Opsional)</label>
                                <input type="text" class="form-control" name="catatan" placeholder="Tambahkan catatan untuk perubahan status">
                            </div>
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="submit" class="btn btn-success w-100">
                                <i class="fas fa-check"></i> Update Status
                            </button>
                        </div>
                    </div>
                </form>

                <div class="mt-4">
                    @if($pesanan['status'] == 'Pemesanan')
                    <a href="{{ route('admin.pesanan.konfirmasi', $pesanan['id']) }}" class="btn btn-primary action-button">
                        <i class="fas fa-check"></i> Konfirmasi Pesanan
                    </a>
                    @endif

                    @if($pesanan['status'] == 'Dikonfirmasi' || $pesanan['status'] == 'Sedang Diproses')
                    <a href="{{ route('admin.pesanan.proses', $pesanan['id']) }}" class="btn btn-primary action-button">
                        <i class="fas fa-print"></i> Proses Cetak
                    </a>
                    @endif

                    @if($pesanan['status'] == 'Menunggu Pengambilan')
                    <form action="{{ route('admin.pesanan.confirm-pickup', ['id' => $pesanan['id']]) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-success action-button" onclick="return confirm('PERHATIAN! Apakah Anda yakin pesanan ini telah diambil oleh pelanggan?')">
                            <i class="fas fa-handshake"></i> Konfirmasi Pengambilan
                        </button>
                    </form>
                    @endif

                    @if($pesanan['status'] == 'Sedang Diproses' && $pesanan['metode'] == 'Dikirim')
                    <a href="{{ route('admin.pesanan.kirim', $pesanan['id']) }}" class="btn btn-info action-button">
                        <i class="fas fa-truck"></i> Konfirmasi Pengiriman
                    </a>
                    @endif

                    @if($pesanan['status'] == 'Sedang Dikirim')
                    <form action="{{ route('admin.pesanan.confirm-delivery', ['id' => $pesanan['id']]) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-success action-button" onclick="return confirm('PERHATIAN! Apakah Anda yakin pesanan ini telah diterima oleh pelanggan?')">
                            <i class="fas fa-box"></i> Konfirmasi Penerimaan
                        </button>
                    </form>
                    @endif

                    @if($pesanan['status'] != 'Selesai' && $pesanan['status'] != 'Dibatalkan')
                    <form action="{{ route('admin.pesanan.cancel', ['id' => $pesanan['id']]) }}" method="POST" class="d-inline">
                        @csrf
                        <input type="hidden" name="alasan" value="Dibatalkan oleh admin">
                        <button type="submit" class="btn btn-danger action-button" onclick="return confirm('PERHATIAN! Apakah Anda yakin ingin MEMBATALKAN pesanan ini?')">
                            <i class="fas fa-times"></i> Batalkan Pesanan
                        </button>
                    </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection