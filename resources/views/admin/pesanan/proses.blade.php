@extends('admin.layout.admin')

@section('title', 'Proses Cetak Pesanan')

@section('styles')
<style>
    .process-form {
        max-width: 600px;
        margin: 0 auto;
    }
    .operator-card, .machine-card {
        cursor: pointer;
        transition: all 0.3s ease;
        position: relative;
    }
    .operator-card:hover, .machine-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    .operator-card.selected, .machine-card.selected {
        border-color: #007bff;
        background-color: #f8f9fa;
    }
    .operator-card.selected::after, .machine-card.selected::after {
        content: '\f00c';
        font-family: 'Font Awesome 5 Free';
        font-weight: 900;
        position: absolute;
        top: 10px;
        right: 10px;
        width: 20px;
        height: 20px;
        background-color: #007bff;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
    }
    .product-selector {
        max-height: 400px;
        overflow-y: auto;
        border: 1px solid #dee2e6;
        border-radius: 8px;
        padding: 15px;
        margin-bottom: 20px;
    }
    .product-item {
        padding: 12px;
        border: 1px solid #dee2e6;
        border-radius: 6px;
        margin-bottom: 10px;
        cursor: pointer;
        transition: all 0.3s ease;
    }
    .product-item:hover {
        background-color: #f8f9fa;
    }
    .product-item.selected {
        border-color: #007bff;
        background-color: #e7f3fe;
    }
    .radio-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 15px;
        margin-bottom: 20px;
    }
    .process-alert {
        background-color: #e3f2fd;
        border-color: #1976d2;
        color: #0d47a1;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-print me-2"></i>
                        Proses Cetak - Pesanan #{{ $pesanan['id'] }}
                    </h5>
                </div>
                
                <div class="card-body">
                    <div class="text-end mb-4">
                        <a href="{{ route('admin.pesanan.show', $pesanan['id']) }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left me-1"></i> Kembali ke Detail
                        </a>
                    </div>
                    
                    <form action="{{ route('admin.pesanan.process-print', $pesanan['id']) }}" method="POST" id="processForm">
                        @csrf
                        
                        <!-- Operator Selection -->
                        <div class="mb-4">
                            <h6 class="text-uppercase text-muted fw-bold mb-3">Pilih Operator</h6>
                            <div class="radio-grid">
                                @foreach($operators as $operator)
                                <div class="operator-card card border" onclick="selectOperator({{ $operator['id'] }})">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center">
                                            <input type="radio" name="operator_id" value="{{ $operator['id'] }}" id="op_{{ $operator['id'] }}" class="form-check-input me-2" required>
                                            <div>
                                                <h6 class="mb-0">{{ $operator['nama'] }}</h6>
                                                <small class="text-muted">{{ $operator['posisi'] }}</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        
                        <!-- Machine Selection -->
                        <div class="mb-4">
                            <h6 class="text-uppercase text-muted fw-bold mb-3">Pilih Mesin</h6>
                            <div class="radio-grid">
                                @foreach($mesins as $mesin)
                                <div class="machine-card card border" onclick="selectMachine({{ $mesin['id'] }})">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center">
                                            <input type="radio" name="mesin_id" value="{{ $mesin['id'] }}" id="mc_{{ $mesin['id'] }}" class="form-check-input me-2" required>
                                            <div>
                                                <h6 class="mb-0">{{ $mesin['nama_mesin'] }}</h6>
                                                <small class="text-muted">{{ $mesin['tipe_mesin'] }}</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        
                        <!-- Product Selection -->
                        <div class="mb-4">
                            <h6 class="text-uppercase text-muted fw-bold mb-3">Pilih Produk</h6>
                            
                            <div class="product-item selected" onclick="selectAllProducts()">
                                <div class="d-flex align-items-center">
                                    <input type="radio" name="detail_pesanan_id" value="" id="all_products" class="form-check-input me-2" checked>
                                    <div>
                                        <h6 class="mb-0">Semua Produk</h6>
                                        <small class="text-muted">Proses semua produk dalam pesanan ini</small>
                                    </div>
                                </div>
                            </div>
                            
                            <hr class="my-3">
                            
                            <div class="product-selector">
                                @foreach($pesanan['detailPesanans'] as $detail)
                                <div class="product-item" onclick="selectProduct({{ $detail['id'] }})">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="d-flex align-items-center">
                                            <input type="radio" name="detail_pesanan_id" value="{{ $detail['id'] }}" id="prod_{{ $detail['id'] }}" class="form-check-input me-2">
                                            <div>
                                                <h6 class="mb-0">{{ $detail['custom']['item']['nama_item'] ?? 'Produk' }}</h6>
                                                <small class="text-muted">
                                                    {{ $detail['jumlah'] }} unit - 
                                                    {{ $detail['custom']['bahan']['nama_bahan'] ?? 'Bahan' }} - 
                                                    {{ $detail['custom']['ukuran']['size'] ?? 'Ukuran' }}
                                                </small>
                                            </div>
                                        </div>
                                        @if(isset($detail['prosesPesanan']))
                                        <span class="badge bg-warning">Sudah Ada Proses</span>
                                        @endif
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        
                        <!-- Notes -->
                        <div class="mb-4">
                            <label for="catatan" class="form-label fw-bold">Catatan Proses (Opsional)</label>
                            <textarea class="form-control" id="catatan" name="catatan" rows="3" 
                                      placeholder="Masukkan catatan khusus untuk proses produksi..."></textarea>
                        </div>
                        
                        <!-- Information Alert -->
                        <div class="alert process-alert">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Informasi:</strong> Setelah proses dimulai, status pesanan akan berubah menjadi "Sedang Diproses" dan operator akan menerima tugas produksi.
                        </div>
                        
                        <!-- Action Buttons -->
                        <div class="d-flex justify-content-center gap-3">
                            <a href="{{ route('admin.pesanan.show', $pesanan['id']) }}" class="btn btn-secondary">
                                <i class="fas fa-times me-1"></i> Batal
                            </a>
                            <button type="submit" class="btn btn-primary" id="submitBtn">
                                <i class="fas fa-paper-plane me-1"></i> Mulai Proses
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function selectOperator(id) {
        $('.operator-card').removeClass('selected');
        document.getElementById('op_' + id).checked = true;
        document.getElementById('op_' + id).closest('.operator-card').classList.add('selected');
    }
    
    function selectMachine(id) {
        $('.machine-card').removeClass('selected');
        document.getElementById('mc_' + id).checked = true;
        document.getElementById('mc_' + id).closest('.machine-card').classList.add('selected');
    }
    
    function selectProduct(id) {
        $('.product-item').removeClass('selected');
        document.getElementById('prod_' + id).checked = true;
        document.getElementById('prod_' + id).closest('.product-item').classList.add('selected');
    }
    
    function selectAllProducts() {
        $('.product-item').removeClass('selected');
        document.getElementById('all_products').checked = true;
        document.getElementById('all_products').closest('.product-item').classList.add('selected');
    }
    
    $(document).ready(function() {
        // Form validation
        $('#processForm').on('submit', function(e) {
            if (!$('input[name="operator_id"]:checked').length) {
                e.preventDefault();
                alert('Silakan pilih operator terlebih dahulu');
                return false;
            }
            
            if (!$('input[name="mesin_id"]:checked').length) {
                e.preventDefault();
                alert('Silakan pilih mesin terlebih dahulu');
                return false;
            }
            
            var operatorName = $('input[name="operator_id"]:checked').closest('.operator-card').find('h6').text();
            var machineName = $('input[name="mesin_id"]:checked').closest('.machine-card').find('h6').text();
            
            if (!confirm('Apakah Anda yakin ingin memulai proses dengan Operator: ' + operatorName + ' dan Mesin: ' + machineName + '?')) {
                e.preventDefault();
                return false;
            }
        });
        
        // Initialize first selection
        selectAllProducts();
    });
</script>
@endsection
