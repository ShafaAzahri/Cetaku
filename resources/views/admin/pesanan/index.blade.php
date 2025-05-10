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
        transition: all 0.2s;
    }
    .clickable:hover {
        background-color: rgba(0,123,255,0.1);
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

    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-white">
            <div class="row align-items-center">
                <div class="col-md-6 mb-2 mb-md-0">
                    <div class="input-group search-box">
                        <input type="text" class="form-control" placeholder="Cari ID Pesanan atau Pelanggan..." id="searchInput" value="{{ request('search') }}">
                        <button class="btn btn-outline-secondary" type="button" id="searchBtn">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
                <div class="col-md-3 mb-2 mb-md-0">
                    <div class="form-group mb-0">
                    <select class="form-select" id="perpage-select">
                        @php $perPage = $perPage ?? 10; @endphp
                        <option value="10" {{ $perPage == 10 ? 'selected' : '' }}>10 per halaman</option>
                        <option value="25" {{ $perPage == 25 ? 'selected' : '' }}>25 per halaman</option>
                        <option value="50" {{ $perPage == 50 ? 'selected' : '' }}>50 per halaman</option>
                        <option value="100" {{ $perPage == 100 ? 'selected' : '' }}>100 per halaman</option>
                    </select>
                    </div>
                </div>
                <div class="col-md-3 text-end">
                    <div class="btn-group">
                        <button type="button" class="btn btn-sm btn-outline-secondary filter-btn {{ request('status') == 'all' || !request('status') ? 'active' : '' }}" data-status="all">Semua</button>
                        <button type="button" class="btn btn-sm btn-outline-warning filter-btn {{ request('status') == 'Pemesanan' ? 'active' : '' }}" data-status="Pemesanan">Baru</button>
                        <button type="button" class="btn btn-sm btn-outline-info filter-btn {{ request('status') == 'Sedang Diproses' ? 'active' : '' }}" data-status="Sedang Diproses">Proses</button>
                        <button type="button" class="btn btn-sm btn-outline-success filter-btn {{ request('status') == 'Selesai' ? 'active' : '' }}" data-status="Selesai">Selesai</button>
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
                        <tr class="clickable pesanan-row" data-status="{{ $p['status'] }}" data-id="{{ $p['id'] }}">
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
                                    <button type="button" class="btn btn-sm btn-success action-btn process-btn" data-id="{{ $p['id'] }}" title="Konfirmasi">
                                        <i class="fas fa-check"></i>
                                    </button>
                                    @endif
                                    
                                    @if($p['status'] == 'Dikonfirmasi' || $p['status'] == 'Sedang Diproses')
                                    <button type="button" class="btn btn-sm btn-primary action-btn print-btn" data-id="{{ $p['id'] }}" title="Proses Print">
                                        <i class="fas fa-print"></i>
                                    </button>
                                    @endif
                                    
                                    @if($p['status'] == 'Menunggu Pengambilan')
                                    <button type="button" class="btn btn-sm btn-success action-btn pickup-btn" data-id="{{ $p['id'] }}" title="Konfirmasi Pengambilan">
                                        <i class="fas fa-handshake"></i>
                                    </button>
                                    @endif
                                    
                                    @if($p['status'] == 'Sedang Diproses' && $p['metode'] == 'Dikirim')
                                    <button type="button" class="btn btn-sm btn-info action-btn ship-btn" data-id="{{ $p['id'] }}" title="Konfirmasi Pengiriman">
                                        <i class="fas fa-truck"></i>
                                    </button>
                                    @endif
                                    
                                    @if($p['status'] == 'Sedang Dikirim')
                                    <button type="button" class="btn btn-sm btn-success action-btn delivery-btn" data-id="{{ $p['id'] }}" title="Konfirmasi Penerimaan">
                                        <i class="fas fa-box-check"></i>
                                    </button>
                                    @endif
                                    
                                    <a href="{{ route('admin.pesanan.print', $p['id']) }}" class="btn btn-sm btn-secondary action-btn" target="_blank" title="Cetak Invoice">
                                        <i class="fas fa-file-invoice"></i>
                                    </a>
                                    
                                    @if($p['status'] != 'Selesai' && $p['status'] != 'Dibatalkan')
                                    <button type="button" class="btn btn-sm btn-danger action-btn cancel-btn" data-id="{{ $p['id'] }}" title="Batalkan Pesanan">
                                        <i class="fas fa-times"></i>
                                    </button>
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
                    Menampilkan {{ $pesanan->count() }} dari {{ $pagination['total'] }} pesanan
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
                                <a class="page-link" href="{{ url()->current() }}?page={{ $pagination['current_page']-1 }}&perpage={{ $perPage }}&status={{ request('status', 'all') }}&search={{ request('search', '') }}" rel="prev">&laquo;</a>
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
                                    <a class="page-link" href="{{ url()->current() }}?page={{ $i }}&perpage={{ $perPage }}&status={{ request('status', 'all') }}&search={{ request('search', '') }}">{{ $i }}</a>
                                </li>
                            @endif
                        @endfor

                        {{-- Next Page Link --}}
                        @if($pagination['current_page'] < $pagination['last_page'])
                            <li class="page-item">
                                <a class="page-link" href="{{ url()->current() }}?page={{ $pagination['current_page']+1 }}&perpage={{ $perPage }}&status={{ request('status', 'all') }}&search={{ request('search', '') }}" rel="next">&raquo;</a>
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

<!-- Modal Update Status -->
<div class="modal fade" id="updateStatusModal" tabindex="-1" aria-labelledby="updateStatusModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="updateStatusModalLabel">Update Status Pesanan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="updateStatusForm">
                <div class="modal-body">
                    <input type="hidden" id="pesanan_id" name="pesanan_id">
                    <div class="form-group">
                        <label class="form-label fw-bold">Status Baru</label>
                        <select class="form-select" id="new-status" name="status">
                            <option value="Pemesanan">Pemesanan</option>
                            <option value="Dikonfirmasi">Dikonfirmasi</option>
                            <option value="Sedang Diproses">Sedang Diproses</option>
                            <option value="Menunggu Pengambilan">Menunggu Pengambilan</option>
                            <option value="Sedang Dikirim">Sedang Dikirim</option>
                            <option value="Selesai">Selesai</option>
                            <option value="Dibatalkan">Dibatalkan</option>
                        </select>
                    </div>
                    <div class="form-group mt-3">
                        <label class="form-label fw-bold">Catatan (Opsional)</label>
                        <textarea class="form-control" id="status-catatan" name="catatan" rows="3" placeholder="Tambahkan catatan untuk perubahan status"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" id="saveStatusBtn">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Proses Print -->
<div class="modal fade" id="printProcessModal" tabindex="-1" aria-labelledby="printProcessModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="printProcessModalLabel">Proses Cetak Pesanan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="printProcessForm" action="" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group mb-3">
                        <label class="form-label fw-bold">Pilih Operator</label>
                        <select class="form-select" id="operator-id" name="operator_id" required>
                            <option value="">-- Pilih Operator --</option>
                            @foreach($operators ?? [] as $operator)
                            <option value="{{ $operator['id'] }}">{{ $operator['nama'] }} ({{ $operator['posisi'] }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group mb-3">
                        <label class="form-label fw-bold">Pilih Mesin</label>
                        <select class="form-select" id="mesin-id" name="mesin_id" required>
                            <option value="">-- Pilih Mesin --</option>
                            @foreach($mesins ?? [] as $mesin)
                            <option value="{{ $mesin['id'] }}">{{ $mesin['nama_mesin'] }} ({{ $mesin['tipe_mesin'] }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group mb-3">
                        <label class="form-label fw-bold">Pilih Produk</label>
                        <select class="form-select" id="detail-pesanan-id" name="detail_pesanan_id">
                            <option value="">-- Semua Produk --</option>
                            <!-- Options akan diisi secara dinamis dengan JavaScript -->
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label fw-bold">Catatan (Opsional)</label>
                        <textarea class="form-control" id="print-catatan" name="catatan" rows="3" placeholder="Tambahkan catatan untuk proses cetak"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Mulai Proses</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Konfirmasi Pengiriman -->
<div class="modal fade" id="shipmentModal" tabindex="-1" aria-labelledby="shipmentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="shipmentModalLabel">Konfirmasi Pengiriman</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="shipmentForm" action="" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group mb-3">
                        <label class="form-label fw-bold">Nomor Resi</label>
                        <input type="text" class="form-control" id="no-resi" name="no_resi" placeholder="Masukkan nomor resi pengiriman">
                    </div>
                    <div class="form-group">
                        <label class="form-label fw-bold">Catatan Pengiriman (Opsional)</label>
                        <textarea class="form-control" id="shipment-catatan" name="catatan" rows="3" placeholder="Tambahkan catatan pengiriman"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Konfirmasi Pengiriman</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Konfirmasi Pembatalan -->
<div class="modal fade" id="cancelModal" tabindex="-1" aria-labelledby="cancelModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="cancelModalLabel">Konfirmasi Pembatalan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Anda yakin ingin membatalkan pesanan <strong id="cancel-order-id"></strong>?</p>
                <p class="text-danger"><strong>Perhatian:</strong> Tindakan ini tidak dapat dibatalkan.</p>
                <div class="form-group">
                    <label class="form-label fw-bold">Alasan Pembatalan</label>
                    <textarea class="form-control" id="cancel-reason" rows="3" placeholder="Masukkan alasan pembatalan"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tidak</button>
                <button type="button" class="btn btn-danger" id="confirmCancelBtn">Ya, Batalkan Pesanan</button>
            </div>
        </div>
    </div>
</div>
@endsection
@section('scripts')
<script>
    // Data global untuk pesanan yang sedang aktif
    let activePesananId = null;
    let activePesananData = null;

    /// Fungsi untuk memuat detail pesanan
    function loadOrderDetails(orderId) {
        const apiToken = "{{ session('api_token') }}";
        return fetch(`{{ url('admin/api/pesanan') }}/${orderId}`, {
            headers: {
                "Authorization": "Bearer " + apiToken,
                "Accept": "application/json"
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                return data.data;
            } else {
                throw new Error(data.message || 'Gagal memuat detail pesanan');
            }
        });
    }

    // Fungsi-fungsi modal
    function openUpdateStatusModal(id, status) {
        activePesananId = id;
        $('#pesanan_id').val(id);
        $('#new-status').val(status);
        $('#updateStatusModal').modal('show');
    }

    async function openPrintProcessModal(id) {
        try {
            activePesananId = id;
            // Ambil detail pesanan untuk mendapatkan produk-produknya
            const orderDetails = await loadOrderDetails(id);
            activePesananData = orderDetails;
            
            // Kosongkan pilihan produk terlebih dahulu
            $('#detail-pesanan-id').empty();
            $('#detail-pesanan-id').append('<option value="">-- Semua Produk --</option>');
            
            // Isi pilihan produk berdasarkan detail pesanan
            if (orderDetails.detail_pesanans && orderDetails.detail_pesanans.length > 0) {
                orderDetails.detail_pesanans.forEach(detail => {
                    const item = detail.custom?.item;
                    if (item) {
                        $('#detail-pesanan-id').append(`<option value="${detail.id}">${item.nama_item}</option>`);
                    }
                });
            }
            
            // Set action form
            $('#printProcessForm').attr('action', `{{ url('admin/pesanan') }}/${id}/process-print`);
            
            $('#printProcessModal').modal('show');
        } catch (error) {
            console.error('Error:', error);
            alert('Gagal memuat detail pesanan: ' + error.message);
        }
    }

    function openShipmentModal(id) {
        activePesananId = id;
        $('#shipmentForm').attr('action', `{{ url('admin/pesanan') }}/${id}/confirm-shipment`);
        $('#shipmentModal').modal('show');
    }

    function openCancelModal(id) {
        activePesananId = id;
        $('#cancel-order-id').text('#' + id);
        $('#cancelModal').modal('show');
    }

    $(document).ready(function() {
        // Klik baris untuk melihat detail
        $('.pesanan-row').click(function(e) {
            // Kecuali jika yang diklik adalah tombol
            if (!$(e.target).closest('button, a').length) {
                const id = $(this).data('id');
                window.location.href = `{{ url('admin/pesanan') }}/${id}`;
            }
        });
        
        // Event handler untuk tombol-tombol
        $('.process-btn').click(function(e) {
            e.stopPropagation();
            let id = $(this).data('id');
            openUpdateStatusModal(id, 'Dikonfirmasi');
        });
        
        $('.print-btn').click(function(e) {
            e.stopPropagation();
            let id = $(this).data('id');
            openPrintProcessModal(id);
        });
        
        $('.pickup-btn').click(function(e) {
            e.stopPropagation();
            let id = $(this).data('id');
            
            if (confirm("Konfirmasi bahwa pesanan #" + id + " telah diambil oleh pelanggan?")) {
                $.ajax({
                    url: `{{ url('admin/pesanan') }}/${id}/confirm-pickup`,
                    type: "POST",
                    data: {
                        "_token": "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        if (response.success) {
                            alert("Pengambilan pesanan berhasil dikonfirmasi");
                            window.location.reload();
                        } else {
                            alert("Error: " + response.message);
                        }
                    },
                    error: function(xhr) {
                        alert("Terjadi kesalahan saat konfirmasi pengambilan: " + xhr.responseText);
                    }
                });
            }
        });
        
        $('.ship-btn').click(function(e) {
            e.stopPropagation();
            let id = $(this).data('id');
            openShipmentModal(id);
        });
        
        $('.delivery-btn').click(function(e) {
            e.stopPropagation();
            let id = $(this).data('id');
            
            if (confirm("Konfirmasi bahwa pesanan #" + id + " telah diterima oleh pelanggan?")) {
                $.ajax({
                    url: `{{ url('admin/pesanan') }}/${id}/confirm-received`,
                    type: "POST",
                    data: {
                        "_token": "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        if (response.success) {
                            alert("Penerimaan pesanan berhasil dikonfirmasi");
                            window.location.reload();
                        } else {
                            alert("Error: " + response.message);
                        }
                    },
                    error: function(xhr) {
                        alert("Terjadi kesalahan saat konfirmasi penerimaan: " + xhr.responseText);
                    }
                });
            }
        });
        
        $('.cancel-btn').click(function(e) {
            e.stopPropagation();
            let id = $(this).data('id');
            openCancelModal(id);
        });
        
        // Filter dan pencarian
        $('.filter-btn').click(function() {
            const status = $(this).data('status');
            
            $('.filter-btn').removeClass('active');
            $(this).addClass('active');
            
            const currentUrl = new URL(window.location.href);
            currentUrl.searchParams.set('status', status);
            currentUrl.searchParams.set('page', 1); // Reset ke halaman pertama
            window.location.href = currentUrl.toString();
        });
        
        $('#perpage-select').change(function() {
            const perpage = $(this).val();
            const currentUrl = new URL(window.location.href);
            currentUrl.searchParams.set('perpage', perpage);
            currentUrl.searchParams.set('page', 1); // Reset ke halaman pertama
            window.location.href = currentUrl.toString();
        });
        
        $('#searchBtn').click(function() {
            const searchValue = $('#searchInput').val();
            
            const currentUrl = new URL(window.location.href);
            if (searchValue.length > 0) {
                currentUrl.searchParams.set('search', searchValue);
            } else {
                currentUrl.searchParams.delete('search');
            }
            currentUrl.searchParams.set('page', 1); // Reset ke halaman pertama
            window.location.href = currentUrl.toString();
        });
        
        $('#searchInput').keypress(function(e) {
            if (e.which === 13) {
                $('#searchBtn').click();
            }
        });
        
        // Form submission dan konfirmasi
        $('#updateStatusForm').submit(function(e) {
            e.preventDefault();
            
            const id = $('#pesanan_id').val();
            const status = $('#new-status').val();
            const catatan = $('#status-catatan').val();
            
            $.ajax({
                url: `{{ url('admin/pesanan') }}/${id}/status`,
                type: "POST",
                data: {
                    "_token": "{{ csrf_token() }}",
                    "status": status,
                    "catatan": catatan
                },
                success: function(response) {
                    if (response.success) {
                        $('#updateStatusModal').modal('hide');
                        
                        // Update badge di tampilan
                        const row = $(`.pesanan-row[data-id="${id}"]`);
                        const statusCell = row.find('td:nth-child(4)');
                        statusCell.html(`<span class="status-badge ${response.badgeClass}">${status}</span>`);
                        row.attr('data-status', status);
                        
                        // Perbarui tampilan jika diperlukan (tambahkan atau hapus action button)
                        setTimeout(function() {
                            window.location.reload();
                        }, 1000);
                    } else {
                        alert("Error: " + response.message);
                    }
                },
                error: function(xhr) {
                    alert("Terjadi kesalahan saat mengubah status: " + xhr.responseText);
                }
            });
        });
        
        $('#confirmCancelBtn').click(function() {
            const id = activePesananId;
            const alasan = $('#cancel-reason').val();
            
            $.ajax({
                url: `{{ url('admin/pesanan') }}/${id}/status`,
                type: "POST",
                data: {
                    "_token": "{{ csrf_token() }}",
                    "status": "Dibatalkan",
                    "catatan": "Dibatalkan dengan alasan: " + alasan
                },
                success: function(response) {
                    if (response.success) {
                        $('#cancelModal').modal('hide');
                        alert("Pesanan #" + id + " berhasil dibatalkan");
                        window.location.reload();
                    } else {
                        alert("Error: " + response.message);
                    }
                },
                error: function(xhr) {
                    alert("Terjadi kesalahan saat membatalkan pesanan: " + xhr.responseText);
                }
            });
        });
        
        // Inisialisasi modal dan highlight dari session
        let modalInfo = {
            type: "{{ session('modal') }}",
            pesananId: "{{ session('pesanan_id') }}",
            status: "{{ session('status') }}",
            highlightId: "{{ session('highlight_id') }}"
        };
        
        // Tampilkan modal jika ada info dari session
        if (modalInfo.type === 'update-status') {
            openUpdateStatusModal(modalInfo.pesananId, modalInfo.status);
        } else if (modalInfo.type === 'print-process') {
            openPrintProcessModal(modalInfo.pesananId);
        } else if (modalInfo.type === 'shipment') {
            openShipmentModal(modalInfo.pesananId);
        }
        
        // Highlight pesanan jika ada ID highlight
        if (modalInfo.highlightId) {
            const highlightRow = $(`tr[data-id='${modalInfo.highlightId}']`);
            if (highlightRow.length) {
                highlightRow.addClass('bg-light-blue');
                setTimeout(() => {
                    highlightRow.removeClass('bg-light-blue');
                }, 3000);
                
                // Scroll ke row tersebut
                $('html, body').animate({
                    scrollTop: highlightRow.offset().top - 100
                }, 500);
            }
        }
    });
</script>
@endsection