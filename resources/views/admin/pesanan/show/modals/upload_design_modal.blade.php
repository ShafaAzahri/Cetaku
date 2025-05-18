<!-- Modal Upload Desain -->
<div class="modal fade" id="uploadDesignModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Upload Desain</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.pesanan.upload-desain', $pesanan['id']) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="detail_pesanan_id" class="form-label">Pilih Produk</label>
                        <select name="detail_pesanan_id" id="detail_pesanan_id" class="form-select" required>
                            @foreach($pesanan['detail_pesanans'] ?? [] as $detail)
                                @if(!isset($detail['proses_pesanan']))
                                <option value="{{ $detail['id'] }}">
                                    {{ $detail['custom']['item']['nama_item'] ?? 'Produk' }} 
                                    ({{ $detail['custom']['bahan']['nama_bahan'] ?? '-' }}, {{ $detail['custom']['ukuran']['size'] ?? '-' }})
                                </option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="desain" class="form-label">File Desain</label>
                        <input type="file" class="form-control" id="desain" name="desain" accept="image/jpeg,image/png,image/jpg,application/pdf" required>
                        <div class="form-text">Format yang didukung: JPG, PNG, PDF. Ukuran maksimum: 10MB</div>
                        @error('desain') <div class="text-danger mt-1">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-3">
                        <label for="tipe" class="form-label">Tipe Desain</label>
                        <select name="tipe" id="tipe" class="form-select" required>
                            <option value="desain_toko">Desain dari Toko</option>
                            <option value="revisi">Revisi Desain</option>
                        </select>
                        @error('tipe') <div class="text-danger mt-1">{{ $message }}</div> @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Upload</button>
                </div>
            </form>
        </div>
    </div>
</div>