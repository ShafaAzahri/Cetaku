<div>
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="mb-0">Daftar Jenis</h5>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addJenisModal">
            <i class="fas fa-plus me-1"></i> Tambah Jenis
        </button>
    </div>

    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Kategori</th>
                    <th>Biaya Tambahan</th>
                    <th>Item Terkait</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($jenis_list ?? [] as $key => $jenis)
                <tr>
                    <td>{{ $key + 1 }}</td>
                    <td>{{ $jenis['kategori'] }}</td>
                    <td>Rp {{ number_format($jenis['biaya_tambahan'], 0, ',', '.') }}</td>
                    <td>
                        @if(isset($jenis['items']) && count($jenis['items']) > 0)
                            @php
                                $itemNames = collect($jenis['items'])->pluck('nama_item')->take(3);
                                $extraCount = count($jenis['items']) - 3;
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
                            data-bs-toggle="modal" 
                            data-bs-target="#editJenisModal"
                            data-id="{{ $jenis['id'] }}"
                            data-kategori="{{ $jenis['kategori'] }}"
                            data-biaya="{{ $jenis['biaya_tambahan'] }}">
                        <i class="fas fa-edit"></i>
                    </button>
                        <form action="{{ route('admin.jenis.destroy', $jenis['id']) }}" method="POST" class="d-inline delete-form" data-entity-type="jenis">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-action" title="Hapus"
                                    onclick="return confirm('Apakah Anda yakin ingin menghapus jenis ini? Semua hubungan dengan item akan dihapus juga.')">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="text-center">Tidak ada data jenis</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
