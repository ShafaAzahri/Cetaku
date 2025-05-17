<div>
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="mb-0">Daftar Kategori</h5>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addKategoriModal">
            <i class="fas fa-plus me-1"></i> Tambah Kategori
        </button>
    </div>

    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Gambar</th>
                    <th>Nama Kategori</th>
                    <th>Deskripsi</th>
                    <th>Item Terkait</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($kategoris ?? [] as $key => $kategori)
                <tr>
                    <td>{{ $key + 1 }}</td>
                    <td>
                        @if(isset($kategori['gambar']) && $kategori['gambar'])
                            <img src="{{ asset('storage/' . $kategori['gambar']) }}" class="img-product-thumbnail" alt="{{ $kategori['nama_kategori'] }}">
                        @else
                            <img src="{{ asset('images/no-image.png') }}" class="img-product-thumbnail" alt="No Image">
                        @endif
                    </td>
                    <td>{{ $kategori['nama_kategori'] }}</td>
                    <td>{{ $kategori['deskripsi'] ?? '-' }}</td>
                    <td>
                        @if(isset($kategori['items']) && count($kategori['items']) > 0)
                            @php
                                $itemNames = collect($kategori['items'])->pluck('nama_item')->take(3);
                                $extraCount = count($kategori['items']) - 3;
                            @endphp
                            {{ $itemNames->join(', ') }}
                            @if($extraCount > 0)
                                <span class="text-muted">+{{ $extraCount }} lainnya</span>
                            @endif
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </td>
                    <td>
                        <button class="btn btn-info btn-action" title="Edit"
                                data-bs-toggle="modal" data-bs-target="#editKategoriModal"
                                data-id="{{ $kategori['id'] }}"
                                data-nama="{{ $kategori['nama_kategori'] }}"
                                data-deskripsi="{{ $kategori['deskripsi'] }}">
                            <i class="fas fa-edit"></i>
                        </button>
                        <form action="{{ route('admin.kategoris.destroy', $kategori['id']) }}" method="POST" class="d-inline delete-form" data-entity-type="kategori">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-action" title="Hapus">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center">Tidak ada data kategori</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
