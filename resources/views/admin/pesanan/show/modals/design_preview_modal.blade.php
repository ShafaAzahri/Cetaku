<!-- Design Preview Modals -->
@foreach($pesanan['detail_pesanans'] ?? [] as $index => $detail)
    @if(($detail['upload_desain'] ?? false) || ($detail['desain_revisi'] ?? false))
    <!-- Modal Preview Desain untuk Produk {{ $index }} -->
    <div class="modal fade" id="designModal{{ $index }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Preview Desain - {{ $detail['custom']['item']['nama_item'] ?? 'Produk' }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <!-- Tabs untuk berbagai jenis desain -->
                    <ul class="nav nav-tabs mb-3" id="designTabs{{ $index }}" role="tablist">
                        @if(($detail['upload_desain'] ?? false) && ($detail['tipe_desain'] ?? '') == 'sendiri')
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="user-tab-{{ $index }}" data-bs-toggle="tab" data-bs-target="#user-design-{{ $index }}" type="button" role="tab" aria-controls="user-design-{{ $index }}" aria-selected="true">Desain Pelanggan</button>
                        </li>
                        @endif
                        
                        @if(($detail['upload_desain'] ?? false) && ($detail['tipe_desain'] ?? '') == 'dibuatkan')
                        <li class="nav-item" role="presentation">
                            <button class="nav-link {{ ($detail['tipe_desain'] ?? '') == 'dibuatkan' ? 'active' : '' }}" id="shop-tab-{{ $index }}" data-bs-toggle="tab" data-bs-target="#shop-design-{{ $index }}" type="button" role="tab" aria-controls="shop-design-{{ $index }}" aria-selected="{{ ($detail['tipe_desain'] ?? '') == 'dibuatkan' ? 'true' : 'false' }}">Desain Toko</button>
                        </li>
                        @endif
                        
                        @if($detail['desain_revisi'] ?? false)
                        <li class="nav-item" role="presentation">
                            <button class="nav-link {{ !($detail['upload_desain'] ?? false) ? 'active' : '' }}" id="revision-tab-{{ $index }}" data-bs-toggle="tab" data-bs-target="#revision-design-{{ $index }}" type="button" role="tab" aria-controls="revision-design-{{ $index }}" aria-selected="{{ !($detail['upload_desain'] ?? false) ? 'true' : 'false' }}">Revisi Desain</button>
                        </li>
                        @endif
                    </ul>
                    
                    <!-- Tab content -->
                    <div class="tab-content" id="designTabsContent{{ $index }}">
                        @if(($detail['upload_desain'] ?? false) && ($detail['tipe_desain'] ?? '') == 'sendiri')
                        <div class="tab-pane fade show active" id="user-design-{{ $index }}" role="tabpanel" aria-labelledby="user-tab-{{ $index }}">
                            <img src="{{ asset('storage/' . $detail['upload_desain']) }}" alt="Desain Pelanggan" class="img-fluid" style="max-height: 70vh;">
                            <div class="mt-3">
                                <a href="{{ asset('storage/' . $detail['upload_desain']) }}" download class="btn btn-primary">
                                    <i class="fas fa-download me-1"></i> Download
                                </a>
                            </div>
                        </div>
                        @endif
                        
                        @if(($detail['upload_desain'] ?? false) && ($detail['tipe_desain'] ?? '') == 'dibuatkan')
                        <div class="tab-pane fade {{ ($detail['tipe_desain'] ?? '') == 'dibuatkan' ? 'show active' : '' }}" id="shop-design-{{ $index }}" role="tabpanel" aria-labelledby="shop-tab-{{ $index }}">
                            <img src="{{ asset('storage/' . $detail['upload_desain']) }}" alt="Desain Toko" class="img-fluid" style="max-height: 70vh;">
                            <div class="mt-3">
                                <a href="{{ asset('storage/' . $detail['upload_desain']) }}" download class="btn btn-primary">
                                    <i class="fas fa-download me-1"></i> Download
                                </a>
                            </div>
                        </div>
                        @endif
                        
                        @if($detail['desain_revisi'] ?? false)
                        <div class="tab-pane fade {{ !($detail['upload_desain'] ?? false) ? 'show active' : '' }}" id="revision-design-{{ $index }}" role="tabpanel" aria-labelledby="revision-tab-{{ $index }}">
                            <img src="{{ asset('storage/' . $detail['desain_revisi']) }}" alt="Revisi Desain" class="img-fluid" style="max-height: 70vh;">
                            <div class="mt-3">
                                <a href="{{ asset('storage/' . $detail['desain_revisi']) }}" download class="btn btn-primary">
                                    <i class="fas fa-download me-1"></i> Download
                                </a>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
    @endif
@endforeach