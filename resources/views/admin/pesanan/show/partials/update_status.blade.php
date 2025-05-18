<div class="detail-card">
    <h5>Update Status</h5>
    <form action="{{ route('admin.pesanan.update-status', $pesanan['id']) }}" method="POST" class="status-form">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label for="status" class="form-label">Status Baru</label>
            <select name="status" id="status" class="form-select">
                @foreach($statusOptions as $statusOption)
                <option value="{{ $statusOption }}" {{ $pesanan['status'] == $statusOption ? 'selected' : '' }}>
                    {{ $statusOption }}
                </option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label for="catatan" class="form-label">Catatan (Opsional)</label>
            <textarea name="catatan" id="catatan" rows="3" class="form-control" placeholder="Tambahkan catatan untuk perubahan status..."></textarea>
        </div>
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save me-1"></i> Update Status
        </button>
    </form>
</div>