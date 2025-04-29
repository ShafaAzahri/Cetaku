@extends('admin.layout.admin')

@section('title', 'Kelola Produk')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/product.css') }}">
@endsection

@section('content')
<div class="container-fluid">
    <!-- Alert Messages -->
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
        <div class="card-header bg-white">
            <h5 class="mb-0">Kelola Produk</h5>
        </div>
        
        <div class="card-body">
            <!-- Nav tabs -->
            <ul class="nav nav-tabs" id="productManagerTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="items-tab" data-bs-toggle="tab" data-bs-target="#items" type="button" role="tab" aria-controls="items" aria-selected="true">
                        <i class="fas fa-box me-1"></i> Produk
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="bahans-tab" data-bs-toggle="tab" data-bs-target="#bahans" type="button" role="tab" aria-controls="bahans" aria-selected="false">
                        <i class="fas fa-layer-group me-1"></i> Bahan
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="ukurans-tab" data-bs-toggle="tab" data-bs-target="#ukurans" type="button" role="tab" aria-controls="ukurans" aria-selected="false">
                        <i class="fas fa-ruler-combined me-1"></i> Ukuran
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="jenis-tab" data-bs-toggle="tab" data-bs-target="#jenis" type="button" role="tab" aria-controls="jenis" aria-selected="false">
                        <i class="fas fa-tags me-1"></i> Jenis
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="biaya-desain-tab" data-bs-toggle="tab" data-bs-target="#biaya-desain" type="button" role="tab" aria-controls="biaya-desain" aria-selected="false">
                        <i class="fas fa-paint-brush me-1"></i> Biaya Desain
                    </button>
                </li>
            </ul>
            
            <!-- Tab panes -->
            <div class="tab-content">
                <!-- Items Tab -->
                <div class="tab-pane active" id="items" role="tabpanel" aria-labelledby="items-tab">
                    <button type="button" class="btn btn-primary add-button" data-bs-toggle="modal" data-bs-target="#addItemModal">
                        <i class="fas fa-plus"></i> Tambah Produk Baru
                    </button>
                    
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama Produk</th>
                                    <th>Deskripsi</th>
                                    <th>Harga Dasar</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($items as $index => $item)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $item->nama_item }}</td>
                                        <td>{{ Str::limit($item->deskripsi, 50) }}</td>
                                        <td>Rp {{ number_format($item->harga_dasar, 0, ',', '.') }}</td>
                                        <td>
                                            <div class="item-actions">
                                                <button type="button" class="btn btn-sm btn-info edit-item-btn" 
                                                        data-id="{{ $item->id }}" 
                                                        data-nama="{{ $item->nama_item }}"
                                                        data-deskripsi="{{ $item->deskripsi }}"
                                                        data-harga="{{ $item->harga_dasar }}">
                                                    <i class="fas fa-edit"></i> Edit
                                                </button>
                                                <form action="{{ route('admin.items.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus produk ini?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger">
                                                        <i class="fas fa-trash"></i> Hapus
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center">Tidak ada produk</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <!-- Bahans Tab -->
                <div class="tab-pane" id="bahans" role="tabpanel" aria-labelledby="bahans-tab">
                    <button type="button" class="btn btn-primary add-button" data-bs-toggle="modal" data-bs-target="#addBahanModal">
                        <i class="fas fa-plus"></i> Tambah Bahan Baru
                    </button>
                    
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Item Produk</th>
                                    <th>Nama Bahan</th>
                                    <th>Biaya Tambahan</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($bahans as $index => $bahan)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>
                                            @if($bahan->items->count() > 0)
                                                {{ $bahan->items->first()->nama_item }}
                                            @else
                                                <span class="text-muted">Tidak terkait</span>
                                            @endif
                                        </td>
                                        <td>{{ $bahan->nama_bahan }}</td>
                                        <td>Rp {{ number_format($bahan->biaya_tambahan, 0, ',', '.') }}</td>
                                        <td>
                                            <div class="bahan-actions">
                                                <button type="button" class="btn btn-sm btn-info edit-bahan-btn" 
                                                        data-id="{{ $bahan->id }}" 
                                                        data-nama="{{ $bahan->nama_bahan }}"
                                                        data-biaya="{{ $bahan->biaya_tambahan }}"
                                                        data-item="{{ $bahan->items->count() > 0 ? $bahan->items->first()->id : '' }}">
                                                    <i class="fas fa-edit"></i> Edit
                                                </button>
                                                <form action="{{ route('admin.bahans.destroy', $bahan->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus bahan ini?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger">
                                                        <i class="fas fa-trash"></i> Hapus
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center">Tidak ada bahan</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <!-- Ukurans Tab -->
                <div class="tab-pane" id="ukurans" role="tabpanel" aria-labelledby="ukurans-tab">
                    <button type="button" class="btn btn-primary add-button" data-bs-toggle="modal" data-bs-target="#addUkuranModal">
                        <i class="fas fa-plus"></i> Tambah Ukuran Baru
                    </button>
                    
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Item Produk</th>
                                    <th>Ukuran</th>
                                    <th>Faktor Harga</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($ukurans as $index => $ukuran)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>
                                            @if($ukuran->items->count() > 0)
                                                {{ $ukuran->items->first()->nama_item }}
                                            @else
                                                <span class="text-muted">Tidak terkait</span>
                                            @endif
                                        </td>
                                        <td>{{ $ukuran->size }}</td>
                                        <td>x{{ number_format($ukuran->faktor_harga, 2) }}</td>
                                        <td>
                                            <div class="ukuran-actions">
                                                <button type="button" class="btn btn-sm btn-info edit-ukuran-btn" 
                                                        data-id="{{ $ukuran->id }}" 
                                                        data-size="{{ $ukuran->size }}"
                                                        data-faktor="{{ $ukuran->faktor_harga }}"
                                                        data-item="{{ $ukuran->items->count() > 0 ? $ukuran->items->first()->id : '' }}">
                                                    <i class="fas fa-edit"></i> Edit
                                                </button>
                                                <form action="{{ route('admin.ukurans.destroy', $ukuran->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus ukuran ini?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger">
                                                        <i class="fas fa-trash"></i> Hapus
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center">Tidak ada ukuran</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <!-- Jenis Tab -->
                <div class="tab-pane" id="jenis" role="tabpanel" aria-labelledby="jenis-tab">
                    <button type="button" class="btn btn-primary add-button" data-bs-toggle="modal" data-bs-target="#addJenisModal">
                        <i class="fas fa-plus"></i> Tambah Jenis Baru
                    </button>
                    
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Item Produk</th>
                                    <th>Kategori</th>
                                    <th>Biaya Tambahan</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($jenis as $index => $j)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>
                                            @if($j->items->count() > 0)
                                                {{ $j->items->first()->nama_item }}
                                            @else
                                                <span class="text-muted">Tidak terkait</span>
                                            @endif
                                        </td>
                                        <td>{{ $j->kategori }}</td>
                                        <td>Rp {{ number_format($j->biaya_tambahan, 0, ',', '.') }}</td>
                                        <td>
                                            <div class="jenis-actions">
                                                <button type="button" class="btn btn-sm btn-info edit-jenis-btn" 
                                                        data-id="{{ $j->id }}" 
                                                        data-kategori="{{ $j->kategori }}"
                                                        data-biaya="{{ $j->biaya_tambahan }}"
                                                        data-item="{{ $j->items->count() > 0 ? $j->items->first()->id : '' }}">
                                                    <i class="fas fa-edit"></i> Edit
                                                </button>
                                                <form action="{{ route('admin.jenis.destroy', $j->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus jenis ini?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger">
                                                        <i class="fas fa-trash"></i> Hapus
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center">Tidak ada jenis</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <!-- Biaya Desain Tab -->
                <div class="tab-pane" id="biaya-desain" role="tabpanel" aria-labelledby="biaya-desain-tab">
                    <button type="button" class="btn btn-primary add-button" data-bs-toggle="modal" data-bs-target="#addBiayaDesainModal">
                        <i class="fas fa-plus"></i> Tambah Biaya Desain Baru
                    </button>
                    
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Biaya</th>
                                    <th>Deskripsi</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($biayaDesains as $index => $biayaDesain)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>Rp {{ number_format($biayaDesain->biaya, 0, ',', '.') }}</td>
                                        <td>{{ Str::limit($biayaDesain->deskripsi, 50) }}</td>
                                        <td>
                                            <div class="desain-actions">
                                                <button type="button" class="btn btn-sm btn-info edit-biaya-desain-btn" 
                                                        data-id="{{ $biayaDesain->id }}" 
                                                        data-deskripsi="{{ $biayaDesain->deskripsi }}"
                                                        data-biaya="{{ $biayaDesain->biaya }}">
                                                    <i class="fas fa-edit"></i> Edit
                                                </button>
                                                <form action="{{ route('admin.biaya-desain.destroy', $biayaDesain->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus biaya desain ini?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger">
                                                        <i class="fas fa-trash"></i> Hapus
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center">Tidak ada biaya desain</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modals -->
@include('admin.product-manager.modals.item-modals')
@include('admin.product-manager.modals.bahan-modals')
@include('admin.product-manager.modals.ukuran-modals')
@include('admin.product-manager.modals.jenis-modals')
@include('admin.product-manager.modals.biaya-desain-modals')
@endsection

@section('scripts')
<script>
    var baseUrl = "{{ url('/') }}";
</script>
<script src="{{ asset('js/product.js') }}"></script>
@endsection