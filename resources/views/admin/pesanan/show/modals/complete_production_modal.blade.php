<!-- Modal Selesaikan Produksi -->
<div class="modal fade" id="completeProductionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Selesaikan Produksi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.pesanan.complete-production', $pesanan['id']) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="proses_pesanan_id" class="form-label">Pilih Proses Produksi</label>
                        <select name="proses_pesanan_id" id="proses_pesanan_id" class="form-select" required>
                            @foreach($pesanan['detail_pesanans'] ?? [] as $detail)
                                @if(isset($detail['proses_pesanan']) && ($detail['proses_pesanan']['status_proses'] ?? '') !== 'Selesai')
                                <option value="{{ $detail['proses_pesanan']['id'] ?? '' }}">
                                    {{ $detail['custom']['item']['nama_item'] ?? 'Produk' }} 
                                    ({{ $detail['proses_pesanan']['operator']['nama'] ?? 'Operator' }})
                                </option>
                                @endif
                            @endforeach
                        </select>
                        @error('proses_pesanan_id') <div class="text-danger mt-1">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-3">
                        <label for="complete_note" class="form-label">Catatan (Opsional)</label>
                        <textarea name="catatan" id="complete_note" rows="3" class="form-control"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">Selesaikan</button>
                </div>
            </form>
        </div>
    </div>
</div>