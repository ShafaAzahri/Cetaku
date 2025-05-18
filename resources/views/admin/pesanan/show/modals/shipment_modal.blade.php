<!-- Modal Konfirmasi Pengiriman -->
<div class="modal fade" id="shipmentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Konfirmasi Pengiriman</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.pesanan.confirm-shipment', $pesanan['id']) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="ekspedisi_id" class="form-label">Pilih Ekspedisi</label>
                        <select name="ekspedisi_id" id="ekspedisi_id" class="form-select" required>
                            <option value="">-- Pilih Ekspedisi --</option>
                            <!-- Ekspedisi options -->
                            <option value="1">JNE (Regular)</option>
                            <option value="2">J&T Express (Regular)</option>
                            <option value="3">SiCepat (Regular)</option>
                            <option value="4">AnterAja (Same Day)</option>
                            <option value="5">Pos Indonesia (Regular)</option>
                        </select>
                        @error('ekspedisi_id') <div class="text-danger mt-1">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-3">
                        <label for="nomor_resi" class="form-label">Nomor Resi (Opsional)</label>
                        <input type="text" class="form-control" id="nomor_resi" name="nomor_resi">
                        @error('nomor_resi') <div class="text-danger mt-1">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-3">
                        <label for="shipment_note" class="form-label">Catatan (Opsional)</label>
                        <textarea name="catatan" id="shipment_note" rows="3" class="form-control"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Konfirmasi Pengiriman</button>
                </div>
            </form>
        </div>
    </div>
</div>