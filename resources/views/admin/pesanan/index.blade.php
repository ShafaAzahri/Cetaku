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
        <div class="card-header">
        <div class="row">
            <div class="col-md-4">
                <div class="input-group search-box">
                    <input type="text" class="form-control" placeholder="Cari ID Pesanan atau Pelanggan..." id="searchInput" value="{{ request('search') }}">
                    <div class="input-group-append">
                        <button class="btn btn-outline-secondary" type="button" id="searchBtn">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <select class="form-control" id="perpage-select">
                        <option value="10" {{ $perPage == 10 ? 'selected' : '' }}>10 per halaman</option>
                        <option value="25" {{ $perPage == 25 ? 'selected' : '' }}>25 per halaman</option>
                        <option value="50" {{ $perPage == 50 ? 'selected' : '' }}>50 per halaman</option>
                        <option value="100" {{ $perPage == 100 ? 'selected' : '' }}>100 per halaman</option>
                    </select>
                </div>
            </div>
            <div class="col-md-4 text-right">
                <div class="btn-group">
                    <button type="button" class="btn btn-default filter-btn" data-status="all">Semua</button>
                    <button type="button" class="btn btn-warning filter-btn" data-status="Pemesanan">Pemesanan</button>
                    <button type="button" class="btn btn-info filter-btn" data-status="Sedang Diproses">Proses</button>
                    <button type="button" class="btn btn-primary filter-btn" data-status="Menunggu Pengambilan">Pengambilan</button>
                    <button type="button" class="btn btn-success filter-btn" data-status="Selesai">Selesai</button>
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
                    @forelse($pesanan as $key => $p)
                    <tr class="pesanan-row" data-status="{{ $p['status'] }}">
                        <td>{{ $key + 1 }}</td>
                        <td>{{ $p['tanggal'] }}</td>
                        <td>{{ $p['id'] }}</td>
                        <td>{{ $p['pelanggan'] }}</td>
                        <td>
                            <span class="status-badge status-{{ strtolower(str_replace(' ', '-', $p['status'])) }}">
                                {{ $p['status'] }}
                            </span>
                        </td>
                        <td>{{ $p['produk'] }}</td>
                        <td>Rp {{ number_format($p['total'], 0, ',', '.') }}</td>
                        <td>
                            <div class="btn-group">
                                @if($p['status'] == 'Pemesanan')
                                    <button type="button" class="btn btn-sm btn-success proses-btn" data-id="{{ $p['id'] }}">
                                        <i class="fas fa-check"></i>
                                    </button>
                                @elseif($p['status'] == 'Sedang Diproses')
                                    <button type="button" class="btn btn-sm btn-primary selesai-btn" data-id="{{ $p['id'] }}">
                                        <i class="fas fa-check-double"></i>
                                    </button>
                                @elseif($p['status'] == 'Menunggu Pengambilan')
                                    <button type="button" class="btn btn-sm btn-success ambil-btn" data-id="{{ $p['id'] }}">
                                        <i class="fas fa-handshake"></i>
                                    </button>
                                @elseif($p['status'] == 'Sedang Dikirim')
                                    <button type="button" class="btn btn-sm btn-info tracking-btn" data-id="{{ $p['id'] }}">
                                        <i class="fas fa-truck"></i>
                                    </button>
                                @endif
                                <button type="button" class="btn btn-sm btn-info print-btn" data-id="{{ $p['id'] }}">
                                    <i class="fas fa-print"></i>
                                </button>
                                @if($p['status'] != 'Selesai' && $p['status'] != 'Dibatalkan')
                                    <button type="button" class="btn btn-sm btn-danger batal-btn" data-id="{{ $p['id'] }}">
                                        <i class="fas fa-times"></i>
                                    </button>
                                @endif
                            </div>
                        </td>
                        <td>
                            <a href="{{ route('admin.pesanan.show', $p['id']) }}" class="btn btn-sm btn-primary">
                                <i class="fas fa-eye"></i> Lihat Pesanan
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center">Tidak ada data pesanan</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer clearfix">
            @if(isset($pagination) && $pagination['last_page'] > 1)
            <div class="float-right">
                <nav aria-label="Page navigation">
                    <ul class="pagination pagination-sm">
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
            
            <div class="float-left text-muted">
                Menampilkan {{ $pesanan->count() }} dari {{ $pagination['total'] }} pesanan
            </div>
            @endif
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
            <form id="updateStatusForm">
                <div class="modal-body">
                    <input type="hidden" id="pesanan_id" name="pesanan_id">
                    <div class="form-group">
                        <label class="form-label fw-bold">Status Baru</label>
                        <select class="form-select" id="new-status" name="status">
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
@endsection

@section('scripts')
<script>
    // Data global untuk pesanan yang sedang aktif
    let activePesananId = null;

    // Fungsi untuk membuka modal status
    function openUpdateStatusModal(id, status) {
        activePesananId = id;
        $('#pesanan_id').val(id);
        $('#new-status').val(status);
        $('#updateStatusModal').modal('show');
    }

    $(document).ready(function() {
        // Event handler untuk tombol proses
        $('.proses-btn').click(function() {
            let id = $(this).data('id');
            openUpdateStatusModal(id, 'Sedang Diproses');
        });
        
        // Event handler untuk tombol selesai
        $('.selesai-btn').click(function() {
            let id = $(this).data('id');
            openUpdateStatusModal(id, 'Selesai');
        });
        
        // Event handler untuk tombol konfirmasi pengambilan
        $('.ambil-btn').click(function() {
            let id = $(this).data('id');
            openUpdateStatusModal(id, 'Selesai');
        });
        
        // Event handler untuk tombol tracking
        $('.tracking-btn').click(function() {
            let id = $(this).data('id');
            alert("Update tracking untuk pesanan #" + id);
        });
        
        // Event handler untuk tombol print
        $('.print-btn').click(function() {
            let id = $(this).data('id');
            window.open("{{ url('admin/pesanan') }}/" + id + "/print", "_blank");
        });
        
        // Event handler untuk tombol batal
        $('.batal-btn').click(function() {
            let id = $(this).data('id');
            if (confirm("Apakah Anda yakin ingin membatalkan pesanan #" + id + "?")) {
                openUpdateStatusModal(id, 'Dibatalkan');
            }
        });
        
        // Filter pesanan berdasarkan status
        $('.filter-btn').click(function() {
            const status = $(this).data('status');
            
            $('.filter-btn').removeClass('active');
            $(this).addClass('active');
            
            if (status === 'all') {
                $('.pesanan-row').show();
            } else {
                $('.pesanan-row').hide();
                $('.pesanan-row[data-status="' + status + '"]').show();
            }
        });
        
        // Search function
        $('#searchBtn').click(function() {
            const searchValue = $('#searchInput').val().toLowerCase();
            
            if (searchValue.length > 0) {
                $('.pesanan-row').hide();
                $('.pesanan-row').each(function() {
                    const id = $(this).find('td:eq(2)').text().toLowerCase();
                    const customer = $(this).find('td:eq(3)').text().toLowerCase();
                    
                    if (id.includes(searchValue) || customer.includes(searchValue)) {
                        $(this).show();
                    }
                });
            } else {
                $('.pesanan-row').show();
            }
        });
        
        // Handle Enter key in search input
        $('#searchInput').keypress(function(e) {
            if (e.which === 13) {
                $('#searchBtn').click();
            }
        });
        
        // Form submission untuk update status
        $('#updateStatusForm').submit(function(e) {
            e.preventDefault();
            
            const id = $('#pesanan_id').val();
            const status = $('#new-status').val();
            const catatan = $('#status-catatan').val();
            
            $.ajax({
                url: "{{ url('admin/pesanan') }}/" + id + "/status",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    status: status,
                    catatan: catatan
                },
                success: function(response) {
                    if (response.success) {
                        $('#updateStatusModal').modal('hide');
                        alert(response.message);
                        window.location.reload();
                    } else {
                        alert("Error: " + response.message);
                    }
                },
                error: function(xhr) {
                    alert("Terjadi kesalahan saat mengubah status: " + xhr.responseText);
                }
            });
        });
    });

    // Tambahkan event listener untuk perpage-select
$('#perpage-select').change(function() {
    const perpage = $(this).val();
    const currentUrl = new URL(window.location.href);
    currentUrl.searchParams.set('perpage', perpage);
    currentUrl.searchParams.set('page', 1); // Reset ke halaman pertama
    window.location.href = currentUrl.toString();
});

// Modifikasi event listener untuk filter-btn
$('.filter-btn').click(function() {
    const status = $(this).data('status');
    
    $('.filter-btn').removeClass('active');
    $(this).addClass('active');
    
    const currentUrl = new URL(window.location.href);
    currentUrl.searchParams.set('status', status);
    currentUrl.searchParams.set('page', 1); // Reset ke halaman pertama
    window.location.href = currentUrl.toString();
});

// Modifikasi event listener untuk searchBtn
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

// Handle Enter key in search input
$('#searchInput').keypress(function(e) {
    if (e.which === 13) {
        $('#searchBtn').click();
    }
});
</script>
@endsection