<div class="detail-card">
    <h5>Detail Produk</h5>
    
    @forelse($pesanan['detail_pesanans'] ?? [] as $index => $detail)
    <div class="produk-item">
        <div class="produk-header" data-bs-toggle="collapse" data-bs-target="#collapse{{ $index }}" aria-expanded="{{ $index === 0 ? 'true' : 'false' }}" aria-controls="collapse{{ $index }}">
            <div class="produk-title">
                <i class="fas {{ $detail['custom']['item']['nama_item'] === 'Jaket' ? 'fa-vest' : 'fa-tshirt' }}"></i>
                <strong>{{ $detail['custom']['item']['nama_item'] ?? 'Produk' }}</strong>
                
                <!-- Indikator Status Produksi -->
                @if(isset($detail['proses_pesanan']))
                    <span class="badge bg-info ms-2">
                        <i class="fas fa-cogs me-1"></i>Produksi Ditugaskan
                    </span>
                @else
                    <span class="badge bg-warning ms-2">
                        <i class="fas fa-clock me-1"></i>Menunggu Penugasan
                    </span>
                @endif
            </div>
            <div class="d-flex align-items-center">
                <span class="me-3">{{ $detail['jumlah'] ?? 1 }} pcs</span>
                <i class="fas fa-chevron-down chevron"></i>
            </div>
        </div>
        
        <div class="collapse {{ $index === 0 ? 'show' : '' }}" id="collapse{{ $index }}">
            <div class="produk-content">
                <div class="row">
                    <div class="col-md-6">
                        <div class="info-row">
                            <div class="info-label">Bahan</div>
                            <div class="info-value">{{ $detail['custom']['bahan']['nama_bahan'] ?? 'Cotton Combed 24s' }}</div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">Ukuran</div>
                            <div class="info-value">{{ $detail['custom']['ukuran']['size'] ?? 'M' }}</div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">Jenis</div>
                            <div class="info-value">{{ $detail['custom']['jenis']['kategori'] ?? 'Lengan Pendek' }}</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-row">
                            <div class="info-label">Tipe Desain</div>
                            <div class="info-value">{{ $detail['tipe_desain'] == 'sendiri' ? 'Upload Sendiri' : 'Dibuatkan' }}</div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">Biaya Desain</div>
                            <div class="info-value">
                                @if($detail['tipe_desain'] == 'sendiri')
                                    Rp 0
                                @else
                                    Rp {{ number_format($detail['biaya_jasa'] ?? $default_biaya_desain ?? 20000, 0, ',', '.') }}
                                @endif
                            </div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">Harga Satuan</div>
                            <div class="info-value">Rp {{ number_format($detail['custom']['harga'] ?? 80000, 0, ',', '.') }}</div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">Total</div>
                            <div class="info-value">Rp {{ number_format($detail['total_harga'] ?? 160000, 0, ',', '.') }}</div>
                        </div>
                    </div>
                </div>
                
                <!-- Bagian Desain -->
                @if($detail['upload_desain'] ?? false || $detail['desain_revisi'] ?? false)
                <div class="mt-3 pt-3 border-top">
                    <div class="row">
                        <!-- Desain dari User -->
                        @if(($detail['upload_desain'] ?? false) && ($detail['tipe_desain'] ?? '') == 'sendiri')
                        <div class="col-md-6 mb-3">
                            <h6 class="mb-2">Desain dari Pelanggan:</h6>
                            <div class="design-preview">
                                <img src="{{ asset('storage/' . $detail['upload_desain']) }}" 
                                    alt="Desain Pelanggan" 
                                    class="img-thumbnail cursor-pointer" 
                                    style="max-height: 200px;"
                                    data-bs-toggle="modal" 
                                    data-bs-target="#designModal{{ $index }}">
                                <div class="mt-2">
                                    <a href="{{ asset('storage/' . $detail['upload_desain']) }}" download class="btn btn-sm btn-outline-secondary">
                                        <i class="fas fa-download"></i> Download
                                    </a>
                                </div>
                            </div>
                        </div>
                        @endif
                        
                        <!-- Desain dari Admin/Toko -->
                        @if(($detail['upload_desain'] ?? false) && ($detail['tipe_desain'] ?? '') == 'dibuatkan')
                        <div class="col-md-6 mb-3">
                            <h6 class="mb-2">Desain dari Toko:</h6>
                            <div class="design-preview">
                                <img src="{{ asset('storage/' . $detail['upload_desain']) }}" 
                                    alt="Desain Toko" 
                                    class="img-thumbnail cursor-pointer" 
                                    style="max-height: 200px;"
                                    data-bs-toggle="modal" 
                                    data-bs-target="#designModal{{ $index }}">
                                <div class="mt-2">
                                    <a href="{{ asset('storage/' . $detail['upload_desain']) }}" download class="btn btn-sm btn-outline-secondary">
                                        <i class="fas fa-download"></i> Download
                                    </a>
                                </div>
                            </div>
                        </div>
                        @endif
                        
                        <!-- Revisi Desain -->
                        @if($detail['desain_revisi'] ?? false)
                        <div class="col-md-6 mb-3">
                            <h6 class="mb-2">Revisi Desain:</h6>
                            <div class="design-preview">
                                <img src="{{ asset('storage/' . $detail['desain_revisi']) }}" 
                                    alt="Revisi Desain" 
                                    class="img-thumbnail cursor-pointer" 
                                    style="max-height: 200px;"
                                    data-bs-toggle="modal" 
                                    data-bs-target="#designModal{{ $index }}">
                                <div class="mt-2">
                                    <a href="{{ asset('storage/' . $detail['desain_revisi']) }}" download class="btn btn-sm btn-outline-secondary">
                                        <i class="fas fa-download"></i> Download
                                    </a>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
                @endif
                
                @if(isset($detail['proses_pesanan']))
                <div class="mt-3 pt-3 border-top">
                    <div class="mb-2 fw-bold">Informasi Produksi:</div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="info-row">
                                <div class="info-label">Operator</div>
                                <div class="info-value">{{ $detail['proses_pesanan']['operator']['nama'] ?? 'Ahmad Rizky' }}</div>
                            </div>
                            <div class="info-row">
                                <div class="info-label">Mesin</div>
                                <div class="info-value">{{ $detail['proses_pesanan']['mesin']['nama_mesin'] ?? 'Mesin Cetak A' }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-row">
                                <div class="info-label">Waktu Mulai</div>
                                <div class="info-value">
                                    @if(isset($detail['proses_pesanan']['waktu_mulai']))
                                        {{ \Carbon\Carbon::parse($detail['proses_pesanan']['waktu_mulai'])->format('d M Y, H:i') }}
                                    @else
                                        11 May 2025, 00:57
                                    @endif
                                </div>
                            </div>
                            <div class="info-row">
                                <div class="info-label">Status Proses</div>
                                <div class="info-value">{{ $detail['proses_pesanan']['status_proses'] ?? 'Ditugaskan' }}</div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
    @empty
    <div class="alert alert-info">Tidak ada detail produk</div>
    @endforelse
    
    <!-- Total -->
    <div class="total-section">
        @php
            $subTotal = 0;
            $totalBiayaDesain = 0;
            
            foreach ($pesanan['detail_pesanans'] ?? [] as $detail) {
                $hargaProduk = $detail['custom']['harga'] ?? 0;
                $jumlah = $detail['jumlah'] ?? 1;
                $subTotal += $hargaProduk * $jumlah;
                
                // Hitung biaya desain
                if(($detail['tipe_desain'] ?? '') == 'dibuatkan') {
                    $totalBiayaDesain += $detail['biaya_jasa'] ?? ($biayaDesain ?? 20000);
                }
            }
            
            $ongkir = $pesanan['ekspedisi']['ongkos_kirim'] ?? 0;
            $grandTotal = $subTotal + $totalBiayaDesain + $ongkir;
        @endphp
        
        <div class="subtotal">Subtotal Produk: Rp {{ number_format($subTotal, 0, ',', '.') }}</div>
        <div class="biaya-desain">Biaya Desain: Rp {{ number_format($totalBiayaDesain, 0, ',', '.') }}</div>
        @if(($pesanan['metode_pengambilan'] ?? '') == 'antar')
        <div class="ongkir">Ongkos Kirim: Rp {{ number_format($ongkir, 0, ',', '.') }}</div>
        @endif
        <div class="total">Total: Rp {{ number_format($grandTotal, 0, ',', '.') }}</div>
    </div>
</div>