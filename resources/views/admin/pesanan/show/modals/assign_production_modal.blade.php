<!-- Modal Tugaskan Produksi -->
<div class="modal fade" id="assignProductionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tugaskan Produksi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.pesanan.assign-production', $pesanan['id']) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="detail_pesanan_id" class="form-label">Pilih Produk</label>
                        <select name="detail_pesanan_id" id="detail_pesanan_id" class="form-select" required>
                            @foreach($pesanan['detail_pesanans'] ?? [] as $detail)
                            <option value="{{ $detail['id'] }}">
                                {{ $detail['custom']['item']['nama_item'] ?? 'Produk' }} 
                                ({{ $detail['custom']['bahan']['nama_bahan'] ?? '-' }}, {{ $detail['custom']['ukuran']['size'] ?? '-' }})
                            </option>
                            @endforeach
                        </select>
                        @error('detail_pesanan_id') <div class="text-danger mt-1">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-3">
                        <label for="mesin_id" class="form-label">Pilih Mesin</label>
                        <select name="mesin_id" id="mesin_id" class="form-select" required>
                            <option value="">-- Pilih Mesin --</option>
                            @foreach($mesinList ?? [] as $mesin)
                            <option value="{{ $mesin['id'] }}">{{ $mesin['nama_mesin'] }} ({{ $mesin['tipe_mesin'] }})</option>
                            @endforeach
                        </select>
                        @error('mesin_id') <div class="text-danger mt-1">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-3">
                        <label for="operator_id" class="form-label">Pilih Operator</label>
                        <select name="operator_id" id="operator_id" class="form-select" required>
                            <option value="">-- Pilih Operator --</option>
                            @foreach($operatorList ?? [] as $operator)
                            <option value="{{ $operator['id'] }}">
                                {{ $operator['nama'] }} ({{ $operator['posisi'] }})
                            </option>
                            @endforeach
                        </select>
                        @error('operator_id') <div class="text-danger mt-1">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-3">
                        <label for="production_note" class="form-label">Catatan Produksi (Opsional)</label>
                        <textarea name="catatan" id="production_note" rows="3" class="form-control"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Tugaskan</button>
                </div>
            </form>
        </div>
    </div>
</div>