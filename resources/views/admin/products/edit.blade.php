@extends('admin.layout.admin')

@section('title', 'Edit Item')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Edit Item</h5>
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

                    <form action="{{ route('admin.items.update', $item['id']) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-3">
                            <label for="nama_item" class="form-label">Nama Item</label>
                            <input type="text" class="form-control" id="nama_item" name="nama_item" 
                                   value="{{ old('nama_item', $item['nama_item']) }}" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="deskripsi" class="form-label">Deskripsi</label>
                            <textarea class="form-control" id="deskripsi" name="deskripsi" rows="3">{{ old('deskripsi', $item['deskripsi']) }}</textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label for="harga_dasar" class="form-label">Harga Dasar</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" class="form-control" id="harga_dasar" name="harga_dasar" 
                                       value="{{ old('harga_dasar', $item['harga_dasar']) }}" required>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="gambar" class="form-label">Gambar</label>
                            <input type="file" class="form-control" id="gambar" name="gambar">
                            
                            @if(isset($item['gambar']) && $item['gambar'])
                            <div class="mt-2">
                                <label>Gambar Saat Ini:</label>
                                <div>
                                    <img src="{{ asset('storage/'.$item['gambar']) }}" alt="{{ $item['nama_item'] }}" 
                                         class="img-thumbnail" style="max-height: 100px">
                                </div>
                                <small class="text-muted">Upload gambar baru untuk menggantinya</small>
                            </div>
                            @endif
                        </div>
                        
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Bahan</label>
                                    <div class="card">
                                        <div class="card-body p-2" style="max-height: 150px; overflow-y: auto;">
                                            @forelse($bahans as $bahan)
                                            <div class="form-check">
                                                @php
                                                    $selectedBahans = old('bahans', collect($item['bahans'] ?? [])->pluck('id')->toArray());
                                                @endphp
                                                <input class="form-check-input" type="checkbox" id="bahan{{ $bahan['id'] }}" 
                                                       name="bahans[]" value="{{ $bahan['id'] }}" 
                                                       {{ in_array($bahan['id'], $selectedBahans) ? 'checked' : '' }}>
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
                                                @php
                                                    $selectedUkurans = old('ukurans', collect($item['ukurans'] ?? [])->pluck('id')->toArray());
                                                @endphp
                                                <input class="form-check-input" type="checkbox" id="ukuran{{ $ukuran['id'] }}" 
                                                       name="ukurans[]" value="{{ $ukuran['id'] }}" 
                                                       {{ in_array($ukuran['id'], $selectedUkurans) ? 'checked' : '' }}>
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
                                                @php
                                                    $selectedJenis = old('jenis', collect($item['jenis'] ?? [])->pluck('id')->toArray());
                                                @endphp
                                                <input class="form-check-input" type="checkbox" id="jenis{{ $j['id'] }}" 
                                                       name="jenis[]" value="{{ $j['id'] }}" 
                                                       {{ in_array($j['id'], $selectedJenis) ? 'checked' : '' }}>
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
                            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection