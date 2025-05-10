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
                <h5 class="modal-title" id="detailPesananModalLabel">Detail Pesanan #<span id="pesanan-id">0001</span></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>ID Pemesanan</label>
                            <input type="text" class="form-control" id="detail-pesanan-id" value="0001" readonly>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>ID Pelanggan</label>
                            <input type="text" class="form-control" id="detail-pelanggan-id" value="A1" readonly>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label>Produk</label>
                    <div class="input-group">
                        <input type="text" class="form-control clickable" id="detail-produk" value="Kaos Lengan Panjang" readonly onclick="lihatDetailProduk()">
                        <div class="input-group-append">
                            <button class="btn btn-outline-info" type="button" onclick="lihatDetailProduk()">
                                <i class="fas fa-eye"></i> Lihat Produk
                            </button>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label>Alamat</label>
                    <input type="text" class="form-control" id="detail-alamat" value="Bandungan" readonly>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Metode Pengambilan</label>
                            <input type="text" class="form-control" id="detail-metode" value="Ambil di Tempat" readonly>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Jumlah</label>
                            <input type="text" class="form-control" id="detail-jumlah" value="5" readonly>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label>Total Harga</label>
                    <input type="text" class="form-control" id="detail-total" value="Rp 250.000" readonly>
                </div>
                <div class="form-group">
                    <label>Estimasi Selesai</label>
                    <input type="text" class="form-control" id="detail-estimasi" value="2025-04-10" readonly>
                </div>
                <div class="form-group">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" id="jasaEdit" disabled checked>
                        <label class="custom-control-label" for="jasaEdit">Dengan Jasa Edit</label>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="prosesPrintBtn">Proses Print</button>
                <button type="button" class="btn btn-danger" id="batalkanPesananBtn">Batalkan Pesanan</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
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
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>ID Produk</label>
                            <input type="text" class="form-control" id="produk-id" value="1" readonly>
                        </div>
                        <div class="form-group">
                            <label>Nama Produk</label>
                            <input type="text" class="form-control" id="produk-detail-nama" value="Kaos Lengan Panjang" readonly>
                        </div>
                        <div class="form-group">
                            <label>Jenis Produk</label>
                            <input type="text" class="form-control" id="produk-jenis" value="Lengan Panjang" readonly>
                        </div>
                        <div class="form-group">
                            <label>Bahan</label>
                            <input type="text" class="form-control" id="produk-bahan" value="Katun" readonly>
                        </div>
                        <div class="form-group">
                            <label>Ukuran</label>
                            <input type="text" class="form-control" id="produk-ukuran" value="XL" readonly>
                        </div>
                        <div class="form-group">
                            <label>Harga Dasar</label>
                            <input type="text" class="form-control" id="produk-harga" value="Rp 50.000" readonly>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Gambar Produk</label>
                            <div class="text-center p-3 bg-light" style="border: 1px solid #ddd; border-radius: 5px;">
                                <img src="https://via.placeholder.com/300x300" alt="Gambar Produk" class="img-fluid" id="produk-gambar">
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Catatan</label>
                            <textarea class="form-control" id="produk-catatan" rows="4" readonly>Tidak ada catatan khusus.</textarea>
                        </div>
                    </div>
                </div>
                
                <div class="mt-4">
                    <h5>Detail Desain</h5>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Desain Pelanggan</label>
                                <div class="text-center p-3 bg-light" style="border: 1px solid #ddd; border-radius: 5px;">
                                    <img src="https://via.placeholder.com/300x150" alt="Desain Pelanggan" class="img-fluid" id="desain-pelanggan">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Desain Final</label>
                                <div class="text-center p-3 bg-light" style="border: 1px solid #ddd; border-radius: 5px;">
                                    <img src="https://via.placeholder.com/300x150" alt="Desain Final" class="img-fluid" id="desain-final">
                                </div>
                                <div class="mt-2">
                                    <button type="button" class="btn btn-sm btn-primary btn-block" onclick="uploadDesainFinal()">
                                        <i class="fas fa-upload"></i> Upload Desain Final
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-primary" onclick="goToProductPage()">Lihat di Katalog</button>
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
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="uploadDesainForm">
                    <div class="form-group">
                        <label for="desainFile">Pilih File Desain</label>
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" id="desainFile" accept="image/*">
                            <label class="custom-file-label" for="desainFile">Pilih file...</label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="desainPreview">Preview</label>
                        <div class="text-center p-3 bg-light" id="previewContainer" style="border: 1px solid #ddd; border-radius: 5px; min-height: 150px; display: none;">
                            <img id="desainPreview" class="img-fluid" alt="Preview">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="catatanDesain">Catatan (Opsional)</label>
                        <textarea class="form-control" id="catatanDesain" rows="3"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
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
        $('#pesanan-id').text(id);
        $('#detail-pesanan-id').val(id);
        
        // Jika ini implementasi yang lengkap, Anda akan melakukan AJAX request
        // untuk mendapatkan data pesanan berdasarkan ID
        // Untuk contoh ini, kita gunakan data dummy
        
        switch(id) {
            case '0001':
                $('#detail-pelanggan-id').val('A1');
                $('#detail-produk').val('Kaos Lengan Panjang');
                $('#detail-alamat').val('Bandungan');
                $('#detail-metode').val('Ambil di Tempat');
                $('#detail-jumlah').val('5');
                $('#detail-total').val('Rp 250.000');
                $('#detail-estimasi').val('2025-04-10');
                $('#jasaEdit').prop('checked', true);
                break;
            case '0002':
                $('#detail-pelanggan-id').val('B2');
                $('#detail-produk').val('Hoodie Premium');
                $('#detail-alamat').val('Semarang');
                $('#detail-metode').val('Dikirim');
                $('#detail-jumlah').val('3');
                $('#detail-total').val('Rp 450.000');
                $('#detail-estimasi').val('2025-04-12');
                $('#jasaEdit').prop('checked', true);
                break;
            case '0003':
                $('#detail-pelanggan-id').val('C3');
                $('#detail-produk').val('Jersey Custom');
                $('#detail-alamat').val('Yogyakarta');
                $('#detail-metode').val('Ambil di Tempat');
                $('#detail-jumlah').val('7');
                $('#detail-total').val('Rp 175.000');
                $('#detail-estimasi').val('2025-04-08');
                $('#jasaEdit').prop('checked', false);
                break;
            case '0004':
                $('#detail-pelanggan-id').val('D4');
                $('#detail-produk').val('Topi Sablon');
                $('#detail-alamat').val('Jakarta');
                $('#detail-metode').val('Dikirim');
                $('#detail-jumlah').val('10');
                $('#detail-total').val('Rp 320.000');
                $('#detail-estimasi').val('2025-04-15');
                $('#jasaEdit').prop('checked', true);
                break;
            case '0005':
                $('#detail-pelanggan-id').val('E5');
                $('#detail-produk').val('Kaos Polo');
                $('#detail-alamat').val('Surabaya');
                $('#detail-metode').val('Ambil di Tempat');
                $('#detail-jumlah').val('4');
                $('#detail-total').val('Rp 180.000');
                $('#detail-estimasi').val('2025-04-07');
                $('#jasaEdit').prop('checked', false);
                break;
        }
        
        // Tampilkan modal
        $('#detailPesananModal').modal('show');
    }
    
    // Fungsi untuk melihat detail produk (dari halaman list)
    function lihatProduk(nama, id) {
        activeProdukId = id;
        
        // Update detail produk
        $('#produk-nama').text(nama);
        $('#produk-id').val(id);
        $('#produk-detail-nama').val(nama);
        
        // Set data produk berdasarkan ID
        switch(id) {
            case 1:
                $('#produk-jenis').val('Lengan Panjang');
                $('#produk-bahan').val('Katun');
                $('#produk-ukuran').val('XL');
                $('#produk-harga').val('Rp 50.000');
                $('#produk-catatan').val('Tidak ada catatan khusus.');
                break;
            case 2:
                $('#produk-jenis').val('Hoodie');
                $('#produk-bahan').val('Cotton Fleece');
                $('#produk-ukuran').val('L');
                $('#produk-harga').val('Rp 150.000');
                $('#produk-catatan').val('Bahan premium, double layer');
                break;
            case 3:
                $('#produk-jenis').val('Jersey');
                $('#produk-bahan').val('Dry Fit');
                $('#produk-ukuran').val('M');
                $('#produk-harga').val('Rp 25.000');
                $('#produk-catatan').val('Jersey tim olahraga');
                break;
            case 4:
                $('#produk-jenis').val('Topi');
                $('#produk-bahan').val('Canvas');
                $('#produk-ukuran').val('All Size');
                $('#produk-harga').val('Rp 32.000');
                $('#produk-catatan').val('Topi snapback dengan sablon custom');
                break;
            case 5:
                $('#produk-jenis').val('Polo');
                $('#produk-bahan').val('Lacoste');
                $('#produk-ukuran').val('L');
                $('#produk-harga').val('Rp 45.000');
                $('#produk-catatan').val('Kaos polo untuk formal/semi-formal');
                break;
        }
        
        // Update gambar produk (dalam implementasi nyata, URL gambar akan dinamis)
        $('#produk-gambar').attr('src', 'https://via.placeholder.com/300x300?text=Produk+' + id);
        $('#desain-pelanggan').attr('src', 'https://via.placeholder.com/300x150?text=Desain+Pelanggan+' + id);
        $('#desain-final').attr('src', 'https://via.placeholder.com/300x150?text=Desain+Final+' + id);
        
        // Tampilkan modal
        $('#detailProdukModal').modal('show');
    }
    
    // Fungsi untuk melihat detail produk (dari modal detail pesanan)
    function lihatDetailProduk() {
        const produkNama = $('#detail-produk').val();
        let produkId;
        
        // Tentukan ID produk berdasarkan nama
        switch(produkNama) {
            case 'Kaos Lengan Panjang':
                produkId = 1;
                break;
            case 'Hoodie Premium':
                produkId = 2;
                break;
            case 'Jersey Custom':
                produkId = 3;
                break;
            case 'Topi Sablon':
                produkId = 4;
                break;
            case 'Kaos Polo':
                produkId = 5;
                break;
            default:
                produkId = 1;
        }
        
        // Bisa menutup modal pesanan jika perlu
        // $('#detailPesananModal').modal('hide');
        
        // Tampilkan detail produk
        lihatProduk(produkNama, produkId);
    }
    
    // Fungsi untuk proses pesanan
    function prosesOrder(id) {
        if (confirm("Apakah Anda yakin ingin memproses pesanan #" + id + "?")) {
            // Simulasi proses pesanan (dalam implementasi nyata akan menggunakan AJAX)
            alert("Pesanan #" + id + " sedang diproses!");
            
            // Update status di UI (implementasi sederhana)
            const statusCell = $("#pesananTable tr").filter(function() {
                return $(this).find("td:eq(2)").text() === id;
            }).find("td:eq(4)").find("span");
            
            // Ubah badge status
            statusCell.removeClass("status-pemesanan").addClass("status-proses");
            statusCell.text("Sedang Diproses");
            
            // Dalam implementasi nyata, Anda akan melakukan POST request
            // $.post('/admin/pesanan/' + id + '/status', { status: 'proses' }, function(response) {
            //    console.log('Status berhasil diubah:', response);
            // });
        }
    }
    
    // Fungsi untuk selesaikan pesanan
    function selesaikanOrder(id) {
        if (confirm("Apakah Anda yakin ingin menyelesaikan pesanan #" + id + "?")) {
            // Simulasi selesaikan pesanan
            alert("Pesanan #" + id + " telah diselesaikan!");
            
            // Update status di UI
            const statusCell = $("#pesananTable tr").filter(function() {
                return $(this).find("td:eq(2)").text() === id;
            }).find("td:eq(4)").find("span");
            
            // Ubah badge status
            statusCell.removeClass().addClass("status-badge status-selesai");
            statusCell.text("Selesai");
        }
    }
    
    // Fungsi untuk konfirmasi pengambilan
    function konfirmasiPengambilan(id) {
        if (confirm("Konfirmasi pengambilan pesanan #" + id + "?")) {
            // Simulasi konfirmasi pengambilan
            alert("Pengambilan pesanan #" + id + " telah dikonfirmasi!");
            
            // Update status di UI
            const statusCell = $("#pesananTable tr").filter(function() {
                return $(this).find("td:eq(2)").text() === id;
            }).find("td:eq(4)").find("span");
            
            // Ubah badge status
            statusCell.removeClass().addClass("status-badge status-selesai");
            statusCell.text("Selesai");
        }
    }
    
    // Fungsi untuk konfirmasi pengiriman
    function konfirmasiPengiriman(id) {
        if (confirm("Konfirmasi pengiriman pesanan #" + id + "?")) {
            // Simulasi konfirmasi pengiriman
            alert("Pengiriman pesanan #" + id + " telah dikonfirmasi!");
            
            // Update status di UI
            const statusCell = $("#pesananTable tr").filter(function() {
                return $(this).find("td:eq(2)").text() === id;
            }).find("td:eq(4)").find("span");
            
            // Ubah badge status
            statusCell.removeClass().addClass("status-badge status-selesai");
            statusCell.text("Selesai");
        }
    }
    
    // Fungsi untuk batalkan pesanan
    function batalkanOrder(id) {
        if (confirm("PERHATIAN! Apakah Anda yakin ingin MEMBATALKAN pesanan #" + id + "?\nTindakan ini tidak dapat dibatalkan!")) {
            // Simulasi batalkan pesanan
            alert("Pesanan #" + id + " telah dibatalkan!");
            
            // Update status di UI atau hapus baris dari tabel
            const row = $("#pesananTable tr").filter(function() {
                return $(this).find("td:eq(2)").text() === id;
            });
            
            // Opsi 1: Ubah status
            const statusCell = row.find("td:eq(4)").find("span");
            statusCell.removeClass().addClass("status-badge").css({
                backgroundColor: "#FFCDD2", 
                color: "#C62828"
            });
            statusCell.text("Dibatalkan");
            
            // Opsi 2: Hapus baris (Nonaktifkan jika ingin menyimpan record dibatalkan)
            // row.fadeOut(400, function() {
            //     $(this).remove();
            // });
        }
    }
    
    // Fungsi untuk mencetak invoice
    function printInvoice(id) {
        // Dalam implementasi nyata, akan membuka jendela baru dengan halaman cetak
        window.open('/admin/pesanan/' + id + '/print', '_blank');
    }
    
    // Fungsi untuk upload desain
    function uploadDesain(id) {
        // Reset form
        $('#uploadDesainForm')[0].reset();
        $('#previewContainer').hide();
        
        // Set ID pesanan aktif
        activePesananId = id;
        
        // Tampilkan modal
        $('#uploadDesainModal').modal('show');
    }
    
    // Fungsi untuk upload desain final
    function uploadDesainFinal() {
        // Persiapkan modal upload yang sama
        $('#uploadDesainForm')[0].reset();
        $('#previewContainer').hide();
        $('#uploadDesainModalLabel').text('Upload Desain Final');
        
        // Tampilkan modal
        $('#uploadDesainModal').modal('show');
    }
    
    // Fungsi untuk update tracking
    function updateTracking(id) {
        // Implementasi sederhana untuk demo
        const trackingNumber = prompt("Masukkan nomor resi untuk pesanan #" + id + ":");
        if (trackingNumber) {
            alert("Nomor resi " + trackingNumber + " telah ditambahkan untuk pesanan #" + id);
        }
    }
    
    // Fungsi untuk kirim notifikasi
    function kirimNotifikasi(id) {
        // Implementasi sederhana untuk demo
        const pesan = prompt("Masukkan pesan notifikasi untuk pelanggan pesanan #" + id + ":");
        if (pesan) {
            alert("Notifikasi telah dikirim ke pelanggan pesanan #" + id);
        }
    }
    
    // Fungsi untuk lihat riwayat
    function lihatRiwayat(id) {
        // Dalam implementasi nyata, akan mengarahkan ke halaman riwayat
        alert("Membuka riwayat pesanan #" + id);
        window.location.href = '/admin/pesanan/' + id + '/history';
    }
    
    // Fungsi untuk pergi ke halaman produk di katalog
    function goToProductPage() {
        // Dalam implementasi nyata, akan mengarahkan ke halaman detail produk
        if (activeProdukId) {
            alert("Membuka halaman produk ID #" + activeProdukId + " di katalog");
            window.location.href = '/admin/product-manager/product/' + activeProdukId;
        }
    }
    
    // Fungsi pencarian
    function searchTable() {
        const searchText = $('#searchInput').val().toLowerCase();
        $("#pesananTable tbody tr").filter(function() {
            const pesananId = $(this).find("td:eq(2)").text().toLowerCase();
            const pelanggan = $(this).find("td:eq(3)").text().toLowerCase();
            const produk = $(this).find("td:eq(5)").text().toLowerCase();
            
            // Mencari berdasarkan ID pesanan, nama pelanggan, atau produk
            const matchesSearch = pesananId.includes(searchText) || 
                                  pelanggan.includes(searchText) || 
                                  produk.includes(searchText);
            
            $(this).toggle(matchesSearch);
        });
    }
    
    // Filter berdasarkan status
    function filterByStatus(status) {
        if (status === 'all') {
            // Tampilkan semua
            $("#pesananTable tbody tr").show();
        } else {
            $("#pesananTable tbody tr").filter(function() {
                const rowStatus = $(this).find("td:eq(4)").text().toLowerCase();
                return rowStatus.includes(status.toLowerCase());
            }).show();
            
            $("#pesananTable tbody tr").filter(function() {
                const rowStatus = $(this).find("td:eq(4)").text().toLowerCase();
                return !rowStatus.includes(status.toLowerCase());
            }).hide();
        }
    }
    
    // Event listeners
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
                
                // Update label dengan nama file
                $(this).next('.custom-file-label').html(file.name);
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
            
            // Jika ini adalah upload desain final, update gambar di modal produk
            if ($('#uploadDesainModalLabel').text() === 'Upload Desain Final') {
                // Preview file sebagai desain final
                const reader = new FileReader();
                reader.onload = function(e) {
                    $('#desain-final').attr('src', e.target.result);
                }
                reader.readAsDataURL(file);
            }
        });
        
        // Tombol proses print di modal detail pesanan
        $('#prosesPrintBtn').click(function() {
            if (activePesananId) {
                if (confirm("Proses print untuk pesanan #" + activePesananId + "?")) {
                    alert("Pesanan #" + activePesananId + " sedang diproses untuk dicetak!");
                    $('#detailPesananModal').modal('hide');
                    
                    // Update status di UI
                    const statusCell = $("#pesananTable tr").filter(function() {
                        return $(this).find("td:eq(2)").text() === activePesananId;
                    }).find("td:eq(4)").find("span");
                    
                    // Ubah badge status
                    statusCell.removeClass().addClass("status-badge status-proses");
                    statusCell.text("Sedang Diproses");
                }
            }
        });
        
        // Tombol batalkan pesanan di modal detail pesanan
        $('#batalkanPesananBtn').click(function() {
            if (activePesananId) {
                batalkanOrder(activePesananId);
                $('#detailPesananModal').modal('hide');
            }
        });
        
        // Pencarian saat tekan tombol search
        $('#searchBtn').click(function() {
            searchTable();
        });
        
        // Pencarian saat ketik
        $('#searchInput').keyup(function() {
            searchTable();
        });
        
        // Filter berdasarkan status
        $('.btn-group .btn').click(function() {
            $('.btn-group .btn').removeClass('active');
            $(this).addClass('active');
            
            const status = $(this).data('status');
            filterByStatus(status);
        });
    });
</script>
@endsection 