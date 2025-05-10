@extends('admin.layout.admin')

@section('title', 'List Pesanan')

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
    .clickable {
        cursor: pointer;
        transition: all 0.2s;
    }
    .clickable:hover {
        background-color: rgba(0,123,255,0.1);
    }
    .search-box {
        max-width: 300px;
        margin-bottom: 20px;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row mb-2">
        <div class="col">
            <h1 class="m-0 text-dark">List Pesanan</h1>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-md-6">
                    <div class="input-group search-box">
                        <input type="text" class="form-control" placeholder="Cari ID Pesanan atau Pelanggan..." id="searchInput">
                        <div class="input-group-append">
                            <button class="btn btn-outline-secondary" type="button" id="searchBtn">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 text-right">
                    <div class="btn-group">
                        <button type="button" class="btn btn-default" data-status="all">Semua</button>
                        <button type="button" class="btn btn-warning" data-status="Pemesanan">Pemesanan</button>
                        <button type="button" class="btn btn-info" data-status="Proses">Proses</button>
                        <button type="button" class="btn btn-primary" data-status="Pengambilan">Pengambilan</button>
                        <button type="button" class="btn btn-success" data-status="Selesai">Selesai</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body table-responsive p-0">
            <table class="table table-hover text-nowrap" id="pesananTable">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Tanggal</th>
                        <th>Pesanan ID</th>
                        <th>Pelanggan</th>
                        <th>Status</th>
                        <th>Produk</th>
                        <th>Total Harga</th>
                        <th>Aksi</th>
                        <th>Info</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>01</td>
                        <td>2025-04-05</td>
                        <td>0001</td>
                        <td>Ahmad Fauzi</td>
                        <td><span class="status-badge status-pemesanan">Pemesanan</span></td>
                        <td><a href="javascript:void(0)" class="text-primary" onclick="lihatProduk('Kaos Lengan Panjang', 1)">Kaos Lengan Panjang</a></td>
                        <td>Rp 250.000</td>
                        <td>
                            <div class="btn-group">
                                <button type="button" class="btn btn-sm btn-success" onclick="prosesOrder('0001')">
                                    <i class="fas fa-check"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-info" onclick="printInvoice('0001')">
                                    <i class="fas fa-print"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-danger" onclick="batalkanOrder('0001')">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </td>
                        <td>
                            <button class="btn btn-sm btn-primary" onclick="lihatDetail('0001')">
                                <i class="fas fa-eye"></i> Lihat Pesanan
                            </button>
                        </td>
                    </tr>
                    <tr>
                        <td>02</td>
                        <td>2025-04-05</td>
                        <td>0002</td>
                        <td>Budi Santoso</td>
                        <td><span class="status-badge status-proses">Sedang Diproses</span></td>
                        <td><a href="javascript:void(0)" class="text-primary" onclick="lihatProduk('Hoodie Premium', 2)">Hoodie Premium</a></td>
                        <td>Rp 450.000</td>
                        <td>
                            <div class="btn-group">
                                <button type="button" class="btn btn-sm btn-success" onclick="selesaikanOrder('0002')">
                                    <i class="fas fa-check-double"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-info" onclick="printInvoice('0002')">
                                    <i class="fas fa-print"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-warning" onclick="uploadDesain('0002')">
                                    <i class="fas fa-upload"></i>
                                </button>
                            </div>
                        </td>
                        <td>
                            <button class="btn btn-sm btn-primary" onclick="lihatDetail('0002')">
                                <i class="fas fa-eye"></i> Lihat Pesanan
                            </button>
                        </td>
                    </tr>
                    <tr>
                        <td>03</td>
                        <td>2025-04-05</td>
                        <td>0003</td>
                        <td>Citra Dewi</td>
                        <td><span class="status-badge status-pengambilan">Menunggu Pengambilan</span></td>
                        <td><a href="javascript:void(0)" class="text-primary" onclick="lihatProduk('Jersey Custom', 3)">Jersey Custom</a></td>
                        <td>Rp 175.000</td>
                        <td>
                            <div class="btn-group">
                                <button type="button" class="btn btn-sm btn-success" onclick="konfirmasiPengambilan('0003')">
                                    <i class="fas fa-handshake"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-info" onclick="printInvoice('0003')">
                                    <i class="fas fa-print"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-primary" onclick="kirimNotifikasi('0003')">
                                    <i class="fas fa-bell"></i>
                                </button>
                            </div>
                        </td>
                        <td>
                            <button class="btn btn-sm btn-primary" onclick="lihatDetail('0003')">
                                <i class="fas fa-eye"></i> Lihat Pesanan
                            </button>
                        </td>
                    </tr>
                    <tr>
                        <td>04</td>
                        <td>2025-04-05</td>
                        <td>0004</td>
                        <td>Deni Purnama</td>
                        <td><span class="status-badge status-dikirim">Sedang Dikirim</span></td>
                        <td><a href="javascript:void(0)" class="text-primary" onclick="lihatProduk('Topi Sablon', 4)">Topi Sablon</a></td>
                        <td>Rp 320.000</td>
                        <td>
                            <div class="btn-group">
                                <button type="button" class="btn btn-sm btn-success" onclick="konfirmasiPengiriman('0004')">
                                    <i class="fas fa-truck-loading"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-info" onclick="printInvoice('0004')">
                                    <i class="fas fa-print"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-warning" onclick="updateTracking('0004')">
                                    <i class="fas fa-map-marker-alt"></i>
                                </button>
                            </div>
                        </td>
                        <td>
                            <button class="btn btn-sm btn-primary" onclick="lihatDetail('0004')">
                                <i class="fas fa-eye"></i> Lihat Pesanan
                            </button>
                        </td>
                    </tr>
                    <tr>
                        <td>05</td>
                        <td>2025-04-05</td>
                        <td>0005</td>
                        <td>Eko Sulistyo</td>
                        <td><span class="status-badge status-selesai">Selesai</span></td>
                        <td><a href="javascript:void(0)" class="text-primary" onclick="lihatProduk('Kaos Polo', 5)">Kaos Polo</a></td>
                        <td>Rp 180.000</td>
                        <td>
                            <div class="btn-group">
                                <button type="button" class="btn btn-sm btn-info" onclick="printInvoice('0005')">
                                    <i class="fas fa-print"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-secondary" onclick="lihatRiwayat('0005')">
                                    <i class="fas fa-history"></i>
                                </button>
                            </div>
                        </td>
                        <td>
                            <button class="btn btn-sm btn-primary" onclick="lihatDetail('0005')">
                                <i class="fas fa-eye"></i> Lihat Pesanan
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="card-footer clearfix">
            <ul class="pagination pagination-sm m-0 float-right">
                <li class="page-item"><a class="page-link" href="#">&laquo;</a></li>
                <li class="page-item active"><a class="page-link" href="#">1</a></li>
                <li class="page-item"><a class="page-link" href="#">2</a></li>
                <li class="page-item"><a class="page-link" href="#">3</a></li>
                <li class="page-item"><a class="page-link" href="#">&raquo;</a></li>
            </ul>
        </div>
    </div>
</div>

<!-- Modal Detail Pesanan -->
<div class="modal fade" id="detailPesananModal" tabindex="-1" aria-labelledby="detailPesananModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="detailPesananModalLabel">Detail Pesanan #0001</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label fw-bold">ID Pemesanan</label>
                            <input type="text" class="form-control" id="detail-pesanan-id" value="0001" readonly>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label fw-bold">ID Pelanggan</label>
                            <input type="text" class="form-control" id="detail-pelanggan-id" value="A1" readonly>
                        </div>
                    </div>
                </div>
                
                <div class="form-group mb-3">
                    <label class="form-label fw-bold">Alamat</label>
                    <input type="text" class="form-control" id="detail-alamat" value="Bandungan" readonly>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label fw-bold">Metode Pengambilan</label>
                            <input type="text" class="form-control" id="detail-metode" value="Ambil di Tempat" readonly>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label fw-bold">Estimasi Selesai</label>
                            <input type="text" class="form-control" id="detail-estimasi" value="2025-04-10" readonly>
                        </div>
                    </div>
                </div>
                
                <div class="card mb-3">
                    <div class="card-header bg-light">
                        <h6 class="mb-0">Detail Produk</h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0" id="detail-produk-table">
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
                                    <tr data-id="1">
                                        <td>Kaos Lengan Panjang</td>
                                        <td>Katun</td>
                                        <td>XL</td>
                                        <td>3</td>
                                        <td>Rp 50.000</td>
                                        <td>Rp 150.000</td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-info" onclick="lihatDetailProduk(1)">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    <tr data-id="2">
                                        <td>Stiker</td>
                                        <td>Vinyl</td>
                                        <td>10x10 cm</td>
                                        <td>5</td>
                                        <td>Rp 20.000</td>
                                        <td>Rp 100.000</td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-info" onclick="lihatDetailProduk(2)">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    <tr data-id="3">
                                        <td>Topi Sablon</td>
                                        <td>Canvas</td>
                                        <td>All Size</td>
                                        <td>1</td>
                                        <td>Rp 35.000</td>
                                        <td>Rp 35.000</td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-info" onclick="lihatDetailProduk(3)">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                                <tfoot class="table-light">
                                    <tr>
                                        <th colspan="5" class="text-end">Total</th>
                                        <th id="detail-total-harga">Rp 285.000</th>
                                        <th></th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="form-group mb-0">
                            <label class="form-label fw-bold">Jasa Tambahan</label>
                            <div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="jasaEdit" disabled checked>
                                    <label class="form-check-label" for="jasaEdit">
                                        Dengan Jasa Edit
                                    </label>
                                </div>
                                <!-- Jika ada jasa tambahan lain bisa ditambahkan di sini -->
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group mb-0">
                            <label class="form-label fw-bold">Status Pesanan</label>
                            <div>
                                <span class="badge rounded-pill bg-warning text-dark px-3 py-2 fs-6">Pemesanan</span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="card mb-0">
                    <div class="card-header bg-light">
                        <h6 class="mb-0">Catatan</h6>
                    </div>
                    <div class="card-body">
                        <textarea class="form-control" id="detail-catatan" rows="2" readonly>Tolong dikirim secepatnya.</textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="prosesPrintBtn">
                    <i class="fas fa-print me-1"></i> Proses Print
                </button>
                <button type="button" class="btn btn-success" id="updateStatusBtn">
                    <i class="fas fa-check me-1"></i> Update Status
                </button>
                <button type="button" class="btn btn-danger" id="batalkanPesananBtn">
                    <i class="fas fa-times me-1"></i> Batalkan Pesanan
                </button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Detail Produk -->
<div class="modal fade" id="detailProdukModal" tabindex="-1" aria-labelledby="detailProdukModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="detailProdukModalLabel">Detail Produk: <span id="produk-nama">Kaos Lengan Panjang</span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label class="form-label fw-bold">ID Produk</label>
                            <input type="text" class="form-control" id="produk-id" value="1" readonly>
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label fw-bold">Nama Produk</label>
                            <input type="text" class="form-control" id="produk-detail-nama" value="Kaos Lengan Panjang" readonly>
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label fw-bold">Jenis Produk</label>
                            <input type="text" class="form-control" id="produk-jenis" value="Lengan Panjang" readonly>
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label fw-bold">Bahan</label>
                            <input type="text" class="form-control" id="produk-bahan" value="Katun" readonly>
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label fw-bold">Ukuran</label>
                            <input type="text" class="form-control" id="produk-ukuran" value="XL" readonly>
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label fw-bold">Harga Dasar</label>
                            <input type="text" class="form-control" id="produk-harga" value="Rp 50.000" readonly>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label class="form-label fw-bold">Gambar Produk</label>
                            <div class="text-center p-3 bg-light" style="border: 1px solid #ddd; border-radius: 5px;">
                                <img src="https://via.placeholder.com/300x300" alt="Gambar Produk" class="img-fluid" id="produk-gambar">
                            </div>
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label fw-bold">Catatan</label>
                            <textarea class="form-control" id="produk-catatan" rows="3" readonly>Tidak ada catatan khusus.</textarea>
                        </div>
                    </div>
                </div>
                
                <div class="mt-4">
                    <h5>Detail Desain</h5>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label class="form-label fw-bold">Desain Pelanggan</label>
                                <div class="text-center p-3 bg-light" style="border: 1px solid #ddd; border-radius: 5px;">
                                    <img src="https://via.placeholder.com/300x150" alt="Desain Pelanggan" class="img-fluid" id="desain-pelanggan">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label class="form-label fw-bold">Desain Final</label>
                                <div class="text-center p-3 bg-light" style="border: 1px solid #ddd; border-radius: 5px;">
                                    <img src="https://via.placeholder.com/300x150" alt="Desain Final" class="img-fluid" id="desain-final">
                                </div>
                                <div class="mt-2">
                                    <button type="button" class="btn btn-sm btn-primary btn-block" onclick="uploadDesainFinal()">
                                        <i class="fas fa-upload me-1"></i> Upload Desain Final
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-primary" onclick="goToProductPage()">Lihat di Katalog</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Update Status -->
<div class="modal fade" id="updateStatusModal" tabindex="-1" aria-labelledby="updateStatusModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="updateStatusModalLabel">Update Status Pesanan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label fw-bold">Status Baru</label>
                    <select class="form-select" id="new-status">
                        <option value="Pemesanan">Pemesanan</option>
                        <option value="Sedang Diproses">Sedang Diproses</option>
                        <option value="Menunggu Pengambilan">Menunggu Pengambilan</option>
                        <option value="Sedang Dikirim">Sedang Dikirim</option>
                        <option value="Selesai">Selesai</option>
                        <option value="Dibatalkan">Dibatalkan</option>
                    </select>
                </div>
                <div class="form-group mt-3">
                    <label class="form-label fw-bold">Catatan (Opsional)</label>
                    <textarea class="form-control" id="status-catatan" rows="3" placeholder="Tambahkan catatan untuk perubahan status"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="saveStatusBtn">Simpan Perubahan</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Upload Desain -->
<div class="modal fade" id="uploadDesainModal" tabindex="-1" aria-labelledby="uploadDesainModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="uploadDesainModalLabel">Upload Desain</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="uploadDesainForm">
                    <div class="form-group mb-3">
                        <label for="desainFile" class="form-label fw-bold">Pilih File Desain</label>
                        <div class="input-group">
                            <input type="file" class="form-control" id="desainFile" accept="image/*">
                            <label class="input-group-text" for="desainFile">Browse</label>
                        </div>
                    </div>
                    <div class="form-group mb-3">
                        <label for="desainPreview" class="form-label fw-bold">Preview</label>
                        <div class="text-center p-3 bg-light" id="previewContainer" style="border: 1px solid #ddd; border-radius: 5px; min-height: 150px; display: none;">
                            <img id="desainPreview" class="img-fluid" alt="Preview">
                        </div>
                    </div>
                    <div class="form-group mb-3">
                        <label for="catatanDesain" class="form-label fw-bold">Catatan (Opsional)</label>
                        <textarea class="form-control" id="catatanDesain" rows="3"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="submitDesain">Upload</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Data global untuk pesanan yang sedang aktif
let activePesananId = null;
let activeProdukId = null;

// Fungsi untuk melihat detail pesanan
function lihatDetail(id) {
    activePesananId = id;
    
    // Update detail UI
    $('#detailPesananModalLabel').text('Detail Pesanan #' + id);
    $('#detail-pesanan-id').val(id);
    
    // Di sini dalam implementasi lengkap, Anda akan melakukan AJAX request
    // untuk mendapatkan data pesanan berdasarkan ID
    // Untuk contoh ini, kita gunakan data statis
    
    // Tampilkan modal
    $('#detailPesananModal').modal('show');
}

// Fungsi untuk melihat detail produk
function lihatDetailProduk(id) {
    activeProdukId = id;
    
    // Atur judul modal dan ID produk
    $('#produk-id').val(id);
    
    // Set data produk berdasarkan ID
    switch(id) {
        case 1:
            $('#produk-nama').text('Kaos Lengan Panjang');
            $('#produk-detail-nama').val('Kaos Lengan Panjang');
            $('#produk-jenis').val('Lengan Panjang');
            $('#produk-bahan').val('Katun');
            $('#produk-ukuran').val('XL');
            $('#produk-harga').val('Rp 50.000');
            $('#produk-catatan').val('Tidak ada catatan khusus.');
            $('#produk-gambar').attr('src', 'https://via.placeholder.com/300x300?text=Kaos+Lengan+Panjang');
            $('#desain-pelanggan').attr('src', 'https://via.placeholder.com/300x150?text=Desain+Pelanggan+1');
            $('#desain-final').attr('src', 'https://via.placeholder.com/300x150?text=Desain+Final+1');
            break;
        case 2:
            $('#produk-nama').text('Stiker');
            $('#produk-detail-nama').val('Stiker');
            $('#produk-jenis').val('Stiker');
            $('#produk-bahan').val('Vinyl');
            $('#produk-ukuran').val('10x10 cm');
            $('#produk-harga').val('Rp 20.000');
            $('#produk-catatan').val('Stiker cutting untuk outdoor.');
            $('#produk-gambar').attr('src', 'https://via.placeholder.com/300x300?text=Stiker');
            $('#desain-pelanggan').attr('src', 'https://via.placeholder.com/300x150?text=Desain+Pelanggan+2');
            $('#desain-final').attr('src', 'https://via.placeholder.com/300x150?text=Desain+Final+2');
            break;
        case 3:
            $('#produk-nama').text('Topi Sablon');
            $('#produk-detail-nama').val('Topi Sablon');
            $('#produk-jenis').val('Topi');
            $('#produk-bahan').val('Canvas');
            $('#produk-ukuran').val('All Size');
            $('#produk-harga').val('Rp 35.000');
            $('#produk-catatan').val('Topi snapback dengan sablon custom');
            $('#produk-gambar').attr('src', 'https://via.placeholder.com/300x300?text=Topi+Sablon');
            $('#desain-pelanggan').attr('src', 'https://via.placeholder.com/300x150?text=Desain+Pelanggan+3');
            $('#desain-final').attr('src', 'https://via.placeholder.com/300x150?text=Desain+Final+3');
            break;
    }
    
    // Tampilkan modal
    $('#detailProdukModal').modal('show');
}

// Fungsi untuk membuka modal update status
function openUpdateStatusModal() {
    $('#updateStatusModal').modal('show');
}

// Fungsi untuk upload desain final
function uploadDesainFinal() {
    // Reset form upload
    $('#uploadDesainForm')[0].reset();
    $('#previewContainer').hide();
    
    // Tampilkan modal upload
    $('#uploadDesainModal').modal('show');
}

// Fungsi untuk pergi ke halaman produk di katalog
function goToProductPage() {
    if (activeProdukId) {
        alert("Membuka halaman produk ID #" + activeProdukId + " di katalog");
        // window.location.href = '/admin/product-manager/product/' + activeProdukId;
    }
}

// Event listeners saat dokumen selesai dimuat
$(document).ready(function() {
    // Preview upload file
    $('#desainFile').change(function() {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                $('#desainPreview').attr('src', e.target.result);
                $('#previewContainer').show();
            }
            reader.readAsDataURL(file);
        }
    });
    
    // Submit upload desain
    $('#submitDesain').click(function() {
        const file = $('#desainFile')[0].files[0];
        if (!file) {
            alert("Silakan pilih file desain terlebih dahulu!");
            return;
        }
        
        // Simulasi upload (dalam implementasi nyata akan menggunakan AJAX FormData)
        alert("Desain berhasil diupload!");
        $('#uploadDesainModal').modal('hide');
        
        // Update gambar desain final di modal produk
        const reader = new FileReader();
        reader.onload = function(e) {
            $('#desain-final').attr('src', e.target.result);
        }
        reader.readAsDataURL(file);
    });
    
    // Tombol proses print di modal detail pesanan
    $('#prosesPrintBtn').click(function() {
        if (activePesananId) {
            if (confirm("Proses print untuk pesanan #" + activePesananId + "?")) {
                alert("Pesanan #" + activePesananId + " sedang diproses untuk dicetak!");
                $('#detailPesananModal').modal('hide');
                
                // Update status di UI tabel pesanan
                // Implementasi sesuai kebutuhan
            }
        }
    });
    
    // Tombol update status
    $('#updateStatusBtn').click(function() {
        openUpdateStatusModal();
    });
    
    // Tombol simpan status baru
    $('#saveStatusBtn').click(function() {
        const newStatus = $('#new-status').val();
        const statusNote = $('#status-catatan').val();
        
        if (activePesananId && newStatus) {
            alert("Status pesanan #" + activePesananId + " diubah menjadi: " + newStatus);
            $('#updateStatusModal').modal('hide');
            
            // Update badge status di modal detail pesanan
            const badgeClass = getBadgeClassForStatus(newStatus);
            const badgeHtml = `<span class="badge ${badgeClass} py-2 px-3">${newStatus}</span>`;
            $('#detail-status-badge').html(badgeHtml);
            
            // Update status di UI tabel pesanan
            // Implementasi sesuai kebutuhan
        }
    });
    
    // Tombol batalkan pesanan
    $('#batalkanPesananBtn').click(function() {
        if (activePesananId) {
            if (confirm("PERHATIAN! Apakah Anda yakin ingin MEMBATALKAN pesanan #" + activePesananId + "?\nTindakan ini tidak dapat dibatalkan!")) {
                alert("Pesanan #" + activePesananId + " telah dibatalkan!");
                $('#detailPesananModal').modal('hide');
                
                // Update status di UI tabel pesanan
                // Implementasi sesuai kebutuhan
            }
        }
    });
});

// Fungsi helper untuk mendapatkan kelas badge berdasarkan status
function getBadgeClassForStatus(status) {
    switch(status) {
        case 'Pemesanan':
            return 'bg-warning text-dark';
        case 'Sedang Diproses':
            return 'bg-info text-dark';
        case 'Menunggu Pengambilan':
            return 'bg-primary';
        case 'Sedang Dikirim':
            return 'bg-info';
        case 'Selesai':
            return 'bg-success';
        case 'Dibatalkan':
            return 'bg-danger';
        default:
            return 'bg-secondary';
    }
}
</script>
@endsection 