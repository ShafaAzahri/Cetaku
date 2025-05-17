<div>
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="mb-0">Daftar Item</h5>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addItemModal">
            <i class="fas fa-plus me-1"></i> Tambah Item
        </button>
    </div>

    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Gambar</th>
                    <th>Nama Item</th>
                    <th>Deskripsi</th>
                    <th>Harga Dasar</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($items as $key => $item)
                <tr>
                    <td>{{ $key + 1 }}</td>
                    <td>
                        @if(isset($item['gambar']) && $item['gambar'])
                            <img src="{{ asset('storage/' . $item['gambar']) }}" class="img-product-thumbnail" alt="{{ $item['nama_item'] }}">
                        @else
                            <img src="{{ asset('images/no-image.png') }}" class="img-product-thumbnail" alt="No Image">
                        @endif
                    </td>
                    <td>{{ $item['nama_item'] }}</td>
                    <td>{{ $item['deskripsi'] ?? '-' }}</td>
                    <td>Rp {{ number_format($item['harga_dasar'], 0, ',', '.') }}</td>
                    <td>
                        <button class="btn btn-info btn-action" title="Edit"
                                data-bs-toggle="modal" data-bs-target="#editItemModal"
                                data-id="{{ $item['id'] }}"
                                data-nama="{{ $item['nama_item'] }}"
                                data-deskripsi="{{ $item['deskripsi'] }}"
                                data-harga="{{ $item['harga_dasar'] }}"
                                data-gambar="{{ $item['gambar'] ?? '' }}">
                            <i class="fas fa-edit"></i>
                        </button>
                        <form action="{{ route('admin.items.destroy', $item['id']) }}" method="POST" class="d-inline delete-form" data-entity-type="items">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-action" title="Hapus"
                                    onclick="return confirm('Apakah Anda yakin ingin menghapus item ini? Semua hubungan dengan bahan, ukuran, dan jenis akan dihapus juga.')">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center">Tidak ada data item</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
