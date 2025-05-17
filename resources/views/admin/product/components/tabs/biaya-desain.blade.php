<div>
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="mb-0">Daftar Biaya Desain</h5>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addBiayaDesainModal">
            <i class="fas fa-plus me-1"></i> Tambah Biaya Desain
        </button>
    </div>

    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Biaya</th>
                    <th>Deskripsi</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($biaya_desains ?? [] as $key => $biaya)
                <tr>
                    <td>{{ $key + 1 }}</td>
                    <td>Rp {{ number_format($biaya['biaya'], 0, ',', '.') }}</td>
                    <td>{{ $biaya['deskripsi'] ?? '-' }}</td>
                    <td>
                        <button class="btn btn-info btn-action" title="Edit"
                                data-bs-toggle="modal" data-bs-target="#editBiayaDesainModal"
                                data-id="{{ $biaya['id'] }}"
                                data-biaya="{{ $biaya['biaya'] }}"
                                data-deskripsi="{{ $biaya['deskripsi'] }}">
                            <i class="fas fa-edit"></i>
                        </button>
                        <form action="{{ route('admin.biaya-desains.destroy', $biaya['id']) }}" method="POST" class="d-inline delete-form" data-entity-type="biaya-desain">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-action" title="Hapus"
                                    onclick="return confirm('Apakah Anda yakin ingin menghapus biaya desain ini?')">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="4" class="text-center">Tidak ada data biaya desain</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
