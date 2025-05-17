<div>
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="mb-0">Daftar Bahan</h5>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addBahanModal">
            <i class="fas fa-plus me-1"></i> Tambah Bahan
        </button>
    </div>

    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Bahan</th>
                    <th>Biaya Tambahan</th>
                    <th>Item Terkait</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($bahans ?? [] as $key => $bahan)
                <tr>
                    <td>{{ $key + 1 }}</td>
                    <td>{{ $bahan['nama_bahan'] }}</td>
                    <td>Rp {{ number_format($bahan['biaya_tambahan'], 0, ',', '.') }}</td>
                    <td>
                        @if(isset($bahan['items']) && count($bahan['items']) > 0)
                            @php
                                $itemNames = collect($bahan['items'])->pluck('nama_item')->take(3);
                                $extraCount = count($bahan['items']) - 3;
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
                                data-bs-toggle="modal" data-bs-target="#editBahanModal"
                                data-id="{{ $bahan['id'] }}"
                                data-nama="{{ $bahan['nama_bahan'] }}"
                                data-biaya="{{ $bahan['biaya_tambahan'] }}">
                            <i class="fas fa-edit"></i>
                        </button>
                        <form action="{{ route('admin.bahans.destroy', $bahan['id']) }}" method="POST" class="d-inline delete-form" data-entity-type="bahan">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-action" title="Hapus"
                                    onclick="return confirm('Apakah Anda yakin ingin menghapus bahan ini? Semua hubungan dengan item akan dihapus juga.')">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="text-center">Tidak ada data bahan</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
