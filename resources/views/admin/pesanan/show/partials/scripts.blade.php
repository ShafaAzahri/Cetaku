<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Show modal on validation errors
        @if ($errors->has('detail_pesanan_id') || $errors->has('mesin_id') || $errors->has('operator_id'))
            var assignModal = document.getElementById('assignProductionModal');
            if (assignModal) {
                var modal = new bootstrap.Modal(assignModal);
                modal.show();
            }
        @endif
        
        @if ($errors->has('proses_pesanan_id'))
            var completeModal = document.getElementById('completeProductionModal');
            if (completeModal) {
                var modal = new bootstrap.Modal(completeModal);
                modal.show();
            }
        @endif
        
        @if ($errors->has('ekspedisi_id') || $errors->has('nomor_resi'))
            var shipmentModal = document.getElementById('shipmentModal');
            if (shipmentModal) {
                var modal = new bootstrap.Modal(shipmentModal);
                modal.show();
            }
        @endif
        
        @if ($errors->has('desain') || $errors->has('tipe'))
            var uploadModal = document.getElementById('uploadDesignModal');
            if (uploadModal) {
                var modal = new bootstrap.Modal(uploadModal);
                modal.show();
            }
        @endif
    });
</script>