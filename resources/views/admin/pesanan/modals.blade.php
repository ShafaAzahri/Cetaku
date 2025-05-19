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
                                {{ $operator['nama'] }} ({{ $operator['posisi'] }}) - 
                                {{ $operator['status'] == 'aktif' ? 'Aktif' : 'Tidak Aktif' }}
                            </option>
                            @endforeach
                        </select>
                        <div class="form-text">Operator yang dipilih dengan status tidak aktif akan diubah menjadi aktif saat ditugaskan.</div>
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
                            <option value="{{ $detail['id'] }}">
                                {{ $detail['custom']['item']['nama_item'] ?? 'Produk' }} 
                                ({{ $detail['custom']['bahan']['nama_bahan'] ?? '-' }}, {{ $detail['custom']['ukuran']['size'] ?? '-' }})
                            </option>
                            @endforeach
                        </select>
                        @error('detail_pesanan_id') <div class="text-danger mt-1">{{ $message }}</div> @enderror
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