@extends('admin.layout.admin')

@section('title', 'Tambah Item')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Tambah Item Baru</h5>
                    <a href="{{ route('admin.product-manager') }}" class="btn btn-sm btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i> Kembali
                    </a>
                </div>
                <div class="card-body">
                    @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    <form action="{{ route('admin.items.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="mb-3">
                            <label for="nama_item" class="form-label">Nama Item</label>
                            <input type="text" class="form-control" id="nama_item" name="nama_item" value="{{ old('nama_item') }}" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="deskripsi" class="form-label">Deskripsi</label>
                            <textarea class="form-control" id="deskripsi" name="deskripsi" rows="3">{{ old('deskripsi') }}</textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label for="harga_dasar" class="form-label">Harga Dasar</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" class="form-control" id="harga_dasar" name="harga_dasar" value="{{ old('harga_dasar') }}" required>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="gambar" class="form-label">Gambar</label>
                            <input type="file" class="form-control" id="gambar" name="gambar">
                        </div>
                        
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Bahan</label>
                                    <div class="card">
                                        <div class="card-body p-2" style="max-height: 150px; overflow-y: auto;">
                                            @forelse($bahans as $bahan)
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="bahan{{ $bahan['id'] }}" 
                                                       name="bahans[]" value="{{ $bahan['id'] }}" 
                                                       {{ in_array($bahan['id'], old('bahans', [])) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="bahan{{ $bahan['id'] }}">
                                                    {{ $bahan['nama_bahan'] }}
                                                </label>
                                            </div>
                                            @empty
                                            <p class="text-center text-muted">Tidak ada data bahan</p>
                                            @endforelse
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Ukuran</label>
                                    <div class="card">
                                        <div class="card-body p-2" style="max-height: 150px; overflow-y: auto;">
                                            @forelse($ukurans as $ukuran)
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="ukuran{{ $ukuran['id'] }}" 
                                                       name="ukurans[]" value="{{ $ukuran['id'] }}" 
                                                       {{ in_array($ukuran['id'], old('ukurans', [])) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="ukuran{{ $ukuran['id'] }}">
                                                    {{ $ukuran['size'] }}
                                                </label>
                                            </div>
                                            @empty
                                            <p class="text-center text-muted">Tidak ada data ukuran</p>
                                            @endforelse
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Jenis</label>
                                    <div class="card">
                                        <div class="card-body p-2" style="max-height: 150px; overflow-y: auto;">
                                            @forelse($jenis as $j)
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="jenis{{ $j['id'] }}" 
                                                       name="jenis[]" value="{{ $j['id'] }}" 
                                                       {{ in_array($j['id'], old('jenis', [])) ? 'checked' : '' }}>
                                                       <label class="form-check-label" for="jenis{{ $j['id'] }}">
                                                    {{ $j['kategori'] }}
                                                </label>
                                            </div>
                                            @empty
                                            <p class="text-center text-muted">Tidak ada data jenis</p>
                                            @endforelse
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-end">
                            <button type="reset" class="btn btn-secondary me-2">Reset</button>
                            <button type="submit" class="btn btn-primary">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection