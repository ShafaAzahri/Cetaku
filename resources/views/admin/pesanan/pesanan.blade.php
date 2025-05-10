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
    .btn-aksi {
        font-size: 0.85rem;
        padding: 0.25rem 0.5rem;
    }
    .action-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 30px;
        height: 30px;
        border-radius: 50%;
        margin-right: 5px;
        cursor: pointer;
        transition: background-color 0.2s;
    }
    .action-icon:hover {
        background-color: rgba(0, 0, 0, 0.05);
    }
    .action-icon.check {
        color: #4CAF50;
    }
    .action-icon.print {
        color: #2196F3;
    }
    .pagination {
        justify-content: center;
        margin-top: 20px;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">List Pesanan</h3>
                    <div class="card-tools">
                        <div class="input-group input-group-sm" style="width: 250px;">
                            <input type="text" name="table_search" class="form-control float-right" placeholder="Cari ID Pesanan atau Pelanggan...">
                            <div class="input-group-append">
                                <button type="submit" class="btn btn-default">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /.card-header -->
                <div class="card-body table-responsive p-0">
                    <table class="table table-hover text-nowrap">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Tanggal</th>
                                <th>Pesanan ID</th>
                                <th>Pelanggan</th>
                                <th>Status</th>
                                <th>Metode</th>
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
                                <td>Ambil di Tempat</td>
                                <td>Rp 250.000</td>
                                <td>
                                    <div class="d-flex">
                                        <span class="action-icon check" title="Konfirmasi Pesanan" onclick="konfirmasiPesanan('0001')">
                                            <i class="fas fa-check"></i>
                                        </span>
                                        <span class="action-icon print" title="Cetak Invoice" onclick="cetakInvoice('0001')">
                                            <i class="fas fa-print"></i>
                                        </span>
                                        <div class="dropdown">
                                            <span class="action-icon" data-toggle="dropdown">
                                                <i class="fas fa-ellipsis-v"></i>
                                            </span>
                                            <div class="dropdown-menu dropdown-menu-right">
                                                <a class="dropdown-item" href="#" onclick="ubahStatus('0001', 'proses')">
                                                    <i class="fas fa-sync fa-fw mr-1"></i> Proses Pesanan
                                                </a>
                                                <a class="dropdown-item" href="#" onclick="uploadDesain('0001')">
                                                    <i class="fas fa-upload fa-fw mr-1"></i> Upload Desain
                                                </a>
                                                <a class="dropdown-item text-danger" href="#" onclick="batalkanPesanan('0001')">
                                                    <i class="fas fa-times fa-fw mr-1"></i> Batalkan Pesanan
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-info" onclick="lihatDetail('0001')">
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
                                <td>Dikirim</td>
                                <td>Rp 450.000</td>
                                <td>
                                    <div class="d-flex">
                                        <span class="action-icon check" title="Selesaikan Pesanan" onclick="selesaikanPesanan('0002')">
                                            <i class="fas fa-check-double"></i>
                                        </span>
                                        <span class="action-icon print" title="Cetak Invoice" onclick="cetakInvoice('0002')">
                                            <i class="fas fa-print"></i>
                                        </span>
                                        <div class="dropdown">
                                            <span class="action-icon" data-toggle="dropdown">
                                                <i class="fas fa-ellipsis-v"></i>
                                            </span>
                                            <div class="dropdown-menu dropdown-menu-right">
                                                <a class="dropdown-item" href="#" onclick="ubahStatus('0002', 'kirim')">
                                                    <i class="fas fa-truck fa-fw mr-1"></i> Siap Dikirim
                                                </a>
                                                <a class="dropdown-item" href="#" onclick="uploadDesain('0002')">
                                                    <i class="fas fa-upload fa-fw mr-1"></i> Upload Hasil
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-info" onclick="lihatDetail('0002')">
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
                                <td>Ambil di Tempat</td>
                                <td>Rp 175.000</td>
                                <td>
                                    <div class="d-flex">
                                        <span class="action-icon check" title="Konfirmasi Pengambilan" onclick="konfirmasiPengambilan('0003')">
                                            <i class="fas fa-handshake"></i>
                                        </span>
                                        <span class="action-icon print" title="Cetak Invoice" onclick="cetakInvoice('0003')">
                                            <i class="fas fa-print"></i>
                                        </span>
                                        <div class="dropdown">
                                            <span class="action-icon" data-toggle="dropdown">
                                                <i class="fas fa-ellipsis-v"></i>
                                            </span>
                                            <div class="dropdown-menu dropdown-menu-right">
                                                <a class="dropdown-item" href="#" onclick="kirimNotifikasi('0003')">
                                                    <i class="fas fa-bell fa-fw mr-1"></i> Kirim Notifikasi
                                                </a>
                                                <a class="dropdown-item" href="#" onclick="selesaikanPesanan('0003')">
                                                    <i class="fas fa-check-double fa-fw mr-1"></i> Selesaikan Pesanan
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-info" onclick="lihatDetail('0003')">
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
                                <td>Dikirim</td>
                                <td>Rp 320.000</td>
                                <td>
                                    <div class="d-flex">
                                        <span class="action-icon check" title="Konfirmasi Pengiriman" onclick="konfirmasiPengiriman('0004')">
                                            <i class="fas fa-truck-loading"></i>
                                        </span>
                                        <span class="action-icon print" title="Cetak Invoice" onclick="cetakInvoice('0004')">
                                            <i class="fas fa-print"></i>
                                        </span>
                                        <div class="dropdown">
                                            <span class="action-icon" data-toggle="dropdown">
                                                <i class="fas fa-ellipsis-v"></i>
                                            </span>
                                            <div class="dropdown-menu dropdown-menu-right">
                                                <a class="dropdown-item" href="#" onclick="updateTracking('0004')">
                                                    <i class="fas fa-map-marker-alt fa-fw mr-1"></i> Update Tracking
                                                </a>
                                                <a class="dropdown-item" href="#" onclick="selesaikanPesanan('0004')">
                                                    <i class="fas fa-check-double fa-fw mr-1"></i> Selesaikan Pesanan
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-info" onclick="lihatDetail('0004')">
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
                                <td>Ambil di Tempat</td>
                                <td>Rp 180.000</td>
                                <td>
                                    <div class="d-flex">
                                        <span class="action-icon print" title="Cetak Invoice" onclick="cetakInvoice('0005')">
                                            <i class="fas fa-print"></i>
                                        </span>
                                        <div class="dropdown">
                                            <span class="action-icon" data-toggle="dropdown">
                                                <i class="fas fa-ellipsis-v"></i>
                                            </span>
                                            <div class="dropdown-menu dropdown-menu-right">
                                                <a class="dropdown-item" href="#" onclick="lihatRiwayat('0005')">
                                                    <i class="fas fa-history fa-fw mr-1"></i> Lihat Riwayat
                                                </a>
                                                <a class="dropdown-item" href="#" onclick="kontakPelanggan('0005')">
                                                    <i class="fas fa-envelope fa-fw mr-1"></i> Kontak Pelanggan
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-info" onclick="lihatDetail('0005')">
                                        <i class="fas fa-eye"></i> Lihat Pesanan
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <!-- /.card-body -->
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
            <!-- /.card -->
        </div>
    </div>
</div>

<!-- Modal Detail Pesanan -->
<div class="modal fade" id="detailPesananModal" tabindex="-1" aria-labelledby="detailPesananModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="detailPesananModalLabel">Detail Pesanan #0001</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>ID Pemesanan</label>
                            <input type="text" class="form-control" value="0001" readonly>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>ID Pelanggan</label>
                            <input type="text" class="form-control" value="A1" readonly>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label>Produk</label>
                    <div class="input-group">
                        <input type="text" class="form-control" value="Kaos Lengan Panjang" readonly>
                        <div class="input-group-append">
                            <button class="btn btn-outline-secondary" type="button" data-toggle="modal" data-target="#detailProdukModal">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label>Alamat</label>
                    <input type="text" class="form-control" value="Bandungan" readonly>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Metode Pengambilan</label>
                            <input type="text" class="form-control" value="Ambil di Tempat" readonly>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Jumlah</label>
                            <input type="text" class="form-control" value="5" readonly>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label>Total Harga</label>
                    <input type="text" class="form-control" value="Rp 250.000" readonly>
                </div>
                <div class="form-group">
                    <label>Estimasi Selesai</label>
                    <input type="text" class="form-control" value="2025-04-10" readonly>
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
            </div>
        </div>
    </div>
</div>

<!-- Modal Detail Produk -->
<div class="modal fade" id="detailProdukModal" tabindex="-1" aria-labelledby="detailProdukModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="detailProdukModalLabel">Detail Produk</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>ID Detail Pesanan</label>
                    <input type="text" class="form-control" value="1" readonly>
                </div>
                <div class="form-group">
                    <label>Jenis Produk</label>
                    <input type="text" class="form-control" value="Lengan Panjang" readonly>
                </div>
                <div class="form-group">
                    <label>Bahan Produk</label>
                    <input type="text" class="form-control" value="Katun" readonly>
                </div>
                <div class="form-group">
                    <label>Ukuran</label>
                    <input type="text" class="form-control" value="XL" readonly>
                </div>
                <div class="form-group">
                    <label>Catatan</label>
                    <textarea class="form-control" rows="3" readonly>-</textarea>
                </div>
                <div class="form-group">
                    <label>Desain Produk</label>
                    <div class="text-center p-3 bg-light mb-3" style="border: 1px dashed #ccc;">
                        <img src="https://via.placeholder.com/400x200" alt="Desain Produk" class="img-fluid">
                    </div>
                </div>
                <div class="form-group">
                    <label>Desain Revisi</label>
                    <div class="input-group">
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" id="inputDesainRevisi">
                            <label class="custom-file-label" for="inputDesainRevisi">Pilih file</label>
                        </div>
                        <div class="input-group-append">
                            <button class="btn btn-outline-secondary" type="button">Upload</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" id="simpanDetailProdukBtn">Simpan Detail Produk</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Fungsi untuk menampilkan modal detail pesanan
    function lihatDetail(id) {
        $('#detailPesananModal').modal('show');
        // Di sini nantinya akan fetch data dari API/DB
        console.log("Lihat detail pesanan ID: " + id);
    }
    
    // Fungsi untuk konfirmasi pesanan
    function konfirmasiPesanan(id) {
        alert("Konfirmasi pesanan ID: " + id);
        // Di sini nantinya akan update status di API/DB
    }
    
    // Fungsi untuk cetak invoice
    function cetakInvoice(id) {
        alert("Cetak invoice pesanan ID: " + id);
        // Di sini nantinya akan buka halaman print atau generate PDF
    }
    
    // Fungsi untuk ubah status
    function ubahStatus(id, status) {
        alert("Ubah status pesanan ID: " + id + " menjadi: " + status);
        // Di sini nantinya akan update status di API/DB
    }
    
    // Fungsi untuk upload desain
    function uploadDesain(id) {
        alert("Upload desain untuk pesanan ID: " + id);
        // Di sini nantinya akan buka form upload atau modal
    }
    
    // Fungsi untuk batalkan pesanan
    function batalkanPesanan(id) {
        if (confirm("Apakah Anda yakin ingin membatalkan pesanan ID: " + id + "?")) {
            alert("Pesanan ID: " + id + " telah dibatalkan");
            // Di sini nantinya akan update status di API/DB
        }
    }
    
    // Fungsi untuk selesaikan pesanan
    function selesaikanPesanan(id) {
        if (confirm("Apakah Anda yakin ingin menyelesaikan pesanan ID: " + id + "?")) {
            alert("Pesanan ID: " + id + " telah diselesaikan");
            // Di sini nantinya akan update status di API/DB
        }
    }
    
    // Fungsi untuk konfirmasi pengambilan
    function konfirmasiPengambilan(id) {
        if (confirm("Konfirmasi pengambilan pesanan ID: " + id + "?")) {
            alert("Pengambilan pesanan ID: " + id + " telah dikonfirmasi");
            // Di sini nantinya akan update status di API/DB
        }
    }
    
    // Fungsi untuk update tracking
    function updateTracking(id) {
        alert("Update tracking untuk pesanan ID: " + id);
        // Di sini nantinya akan buka form tracking atau modal
    }
    
    // Fungsi untuk konfirmasi pengiriman
    function konfirmasiPengiriman(id) {
        if (confirm("Konfirmasi pengiriman pesanan ID: " + id + "?")) {
            alert("Pengiriman pesanan ID: " + id + " telah dikonfirmasi");
            // Di sini nantinya akan update status di API/DB
        }
    }
    
    // Fungsi untuk lihat riwayat
    function lihatRiwayat(id) {
        alert("Lihat riwayat pesanan ID: " + id);
        // Di sini nantinya akan buka halaman riwayat atau modal
    }
    
    // Fungsi untuk kontak pelanggan
    function kontakPelanggan(id) {
        alert("Kontak pelanggan untuk pesanan ID: " + id);
        // Di sini nantinya akan buka form kontak atau modal
    }
    
    // Fungsi untuk kirim notifikasi
    function kirimNotifikasi(id) {
        alert("Kirim notifikasi untuk pesanan ID: " + id);
        // Di sini nantinya akan buka form notifikasi atau modal
    }
    
    // Script untuk button di modal
    $(document).ready(function() {
        $('#prosesPrintBtn').click(function() {
            alert("Memproses print pesanan");
            $('#detailPesananModal').modal('hide');
        });
        
        $('#batalkanPesananBtn').click(function() {
            if (confirm("Apakah Anda yakin ingin membatalkan pesanan ini?")) {
                alert("Pesanan telah dibatalkan");
                $('#detailPesananModal').modal('hide');
            }
        });
        
        $('#simpanDetailProdukBtn').click(function() {
            alert("Detail produk berhasil disimpan");
            $('#detailProdukModal').modal('hide');
        });
        
        // Script untuk file input
        $(".custom-file-input").on("change", function() {
            var fileName = $(this).val().split("\\").pop();
            $(this).siblings(".custom-file-label").addClass("selected").html(fileName);
        });
    });
</script>
@endsection