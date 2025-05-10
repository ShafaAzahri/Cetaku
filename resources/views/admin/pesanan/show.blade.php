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
        background-color: #FFC107;
        color: #000;
    }
    .status-proses {
        background-color: #00BCD4;
        color: #fff;
    }
    .status-pengambilan {
        background-color: #673AB7;
        color: #fff;
    }
    .status-dikirim {
        background-color: #2196F3;
        color: #fff;
    }
    .status-selesai {
        background-color: #4CAF50;
        color: #fff;
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
                <form action="{{ route('admin.pesanan.update-status', ['id' => $pesanan['id']]) }}" method="POST" class="d-inline">
                    @csrf
                    <div class="form-group" style="max-width: 300px; display: inline-block; margin-right: 10px;">
                        <select class="form-control" name="status">
                            <option value="Pemesanan" {{ $pesanan['status'] == 'Pemesanan' ? 'selected' : '' }}>Pemesanan</option>
                            <option value="Sedang Diproses" {{ $pesanan['status'] == 'Sedang Diproses' ? 'selected' : '' }}>Sedang Diproses</option>
                            <option value="Menunggu Pengambilan" {{ $pesanan['status'] == 'Menunggu Pengambilan' ? 'selected' : '' }}>Menunggu Pengambilan</option>
                            <option value="Sedang Dikirim" {{ $pesanan['status'] == 'Sedang Dikirim' ? 'selected' : '' }}>Sedang Dikirim</option>
                            <option value="Selesai" {{ $pesanan['status'] == 'Selesai' ? 'selected' : '' }}>Selesai</option>
                            <option value="Dibatalkan" {{ $pesanan['status'] == 'Dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-success action-button">
                        <i class="fas fa-check"></i> Update Status
                    </button>
                </form>

                <a href="{{ route('admin.pesanan.print', ['id' => $pesanan['id']]) }}" class="btn btn-primary action-button" target="_blank">
                    <i class="fas fa-print"></i> Proses Print
                </a>

                @if($pesanan['status'] != 'Selesai' && $pesanan['status'] != 'Dibatalkan')
                <form action="{{ route('admin.pesanan.cancel', ['id' => $pesanan['id']]) }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-danger action-button" onclick="return confirm('PERHATIAN! Apakah Anda yakin ingin MEMBATALKAN pesanan ini?')">
                        <i class="fas fa-times"></i> Batalkan Pesanan
                    </button>
                </form>
                @endif
            </div>
        </div>
    </div>
</div>

@if(isset($_GET['fokus_produk']))
<script>
    // Scroll ke produk tertentu jika ada parameter fokus_produk
    document.addEventListener('DOMContentLoaded', function() {
        const produkName = "{{ htmlspecialchars(request()->get('fokus_produk', '')) }}";
        const rows = document.querySelectorAll('.product-table tbody tr');
        
        for (let i = 0; i < rows.length; i++) {
            const produkText = rows[i].querySelector('td:first-child').textContent;
            if (produkText.includes(produkName)) {
                rows[i].scrollIntoView({ behavior: 'smooth', block: 'center' });
                rows[i].classList.add('bg-light');
                setTimeout(() => {
                    rows[i].classList.remove('bg-light');
                }, 3000);
                break;
            }
        }
    });
</script>
@endif
@endsection