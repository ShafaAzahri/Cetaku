<div>
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="mb-0">Daftar Ukuran</h5>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUkuranModal">
            <i class="fas fa-plus me-1"></i> Tambah Ukuran
        </button>
    </div>

    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Size</th>
                    <th>Faktor Harga</th>
                    <th>Item Terkait</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($ukurans ?? [] as $key => $ukuran)
                <tr>
                    <td>{{ $key + 1 }}</td>
                    <td>{{ $ukuran['size'] }}</td>
                    <td>Rp. {{ number_format($ukuran['biaya_tambahan'], 0, ',', '.') }}</td>
                    <td>
                        @if(isset($ukuran['items']) && count($ukuran['items']) > 0)
                            @php
                                $itemNames = collect($ukuran['items'])->pluck('nama_item')->take(3);
                                $extraCount = count($ukuran['items']) - 3;
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
                                data-bs-toggle="modal" data-bs-target="#editUkuranModal"
                                data-id="{{ $ukuran['id'] }}"
                                data-size="{{ $ukuran['size'] }}"
                                data-faktor="{{ $ukuran['biaya_tambahan'] }}">
                            <i class="fas fa-edit"></i>
                        </button>
                        <form action="{{ route('admin.ukurans.destroy', $ukuran['id']) }}" method="POST" class="d-inline delete-form" data-entity-type="ukuran">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-action" title="Hapus"
                                    onclick="return confirm('Apakah Anda yakin ingin menghapus ukuran ini? Semua hubungan dengan item akan dihapus juga.')">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="text-center">Tidak ada data ukuran</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
