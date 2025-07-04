@extends('user.layouts.app')
@section('title', 'Checkout')

@push('meta')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('custom-css')
    <style>
        .checkout-container {
            padding: 20px 0;
            background: #f8f9fa;
        }

        .checkout-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
            padding: 20px;
        }

        .section-title {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 15px;
            color: #333;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .delivery-options {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-bottom: 20px;
        }

        .delivery-option {
            border: 2px solid #e5e5e5;
            border-radius: 12px;
            padding: 20px;
            cursor: pointer;
            transition: all 0.3s;
            text-align: center;
            position: relative;
        }

        .delivery-option:hover,
        .delivery-option.active {
            border-color: #4361ee;
            background: #f8f9ff;
        }

        .delivery-option .icon {
            font-size: 32px;
            margin-bottom: 10px;
            display: block;
        }

        .delivery-option .title {
            font-weight: 600;
            font-size: 16px;
            margin-bottom: 5px;
            color: #333;
        }

        .delivery-option .desc {
            font-size: 14px;
            color: #666;
            margin: 0;
        }

        .delivery-option input[type="radio"] {
            position: absolute;
            top: 10px;
            right: 10px;
        }

        .card-item {
            border: 1px solid #e5e5e5;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 10px;
            cursor: pointer;
            transition: all 0.3s;
        }

        .card-item:hover,
        .card-item.selected {
            border-color: #4361ee;
            background: #f8f9ff;
        }

        .card-item input[type="radio"] {
            display: none;
        }

        .address-label {
            font-weight: 600;
            color: #4361ee;
            font-size: 12px;
            text-transform: uppercase;
            margin-bottom: 5px;
        }

        .address-name {
            font-weight: 600;
            margin-bottom: 3px;
        }

        .address-detail {
            color: #666;
            font-size: 14px;
            line-height: 1.4;
        }

        .ekspedisi-option {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .ekspedisi-info {
            flex: 1;
        }

        .ekspedisi-name {
            font-weight: 600;
            margin-bottom: 3px;
        }

        .ekspedisi-service {
            color: #666;
            font-size: 14px;
        }

        .ekspedisi-price {
            text-align: right;
        }

        .ekspedisi-cost {
            font-weight: 600;
            color: #4361ee;
        }

        .ekspedisi-estimate {
            font-size: 12px;
            color: #666;
        }

        .store-info {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 15px;
        }

        .store-info-item {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            margin-bottom: 15px;
        }

        .store-info-item:last-child {
            margin-bottom: 0;
        }

        .store-info-icon {
            color: #4361ee;
            font-size: 18px;
            margin-top: 2px;
        }

        .store-info-content h6 {
            margin: 0 0 3px 0;
            font-weight: 600;
            color: #333;
        }

        .store-info-content p {
            margin: 0;
            color: #666;
            font-size: 14px;
            line-height: 1.4;
        }

        .payment-option {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .payment-icon {
            font-size: 24px;
            color: #4361ee;
        }

        .order-item {
            display: flex;
            gap: 15px;
            padding: 15px 0;
            border-bottom: 1px solid #f0f0f0;
        }

        .order-item:last-child {
            border-bottom: none;
        }

        .order-item-image {
            width: 60px;
            height: 60px;
            border-radius: 8px;
            object-fit: cover;
        }

        .order-item-details {
            flex: 1;
        }

        .order-item-name {
            font-weight: 600;
            margin-bottom: 5px;
            font-size: 14px;
        }

        .order-item-specs {
            color: #666;
            font-size: 12px;
            line-height: 1.3;
        }

        .order-item-price {
            text-align: right;
        }

        .order-item-quantity {
            font-size: 12px;
            color: #666;
        }

        .order-item-total {
            font-weight: 600;
            color: #4361ee;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
        }

        .summary-row.total {
            border-top: 1px solid #e5e5e5;
            padding-top: 15px;
            margin-top: 10px;
            font-weight: 600;
            font-size: 16px;
        }

        .btn-add-address {
            border: 2px dashed #4361ee;
            background: transparent;
            color: #4361ee;
            padding: 15px;
            border-radius: 8px;
            width: 100%;
            font-weight: 600;
            transition: all 0.3s;
        }

        .btn-add-address:hover {
            background: #f8f9ff;
        }

        .checkout-actions {
            background: white;
            border-top: 1px solid #e5e5e5;
            padding: 20px;
            position: sticky;
            bottom: 0;
            margin: 0 -15px -15px -15px;
            border-radius: 0 0 12px 12px;
        }

        .btn-checkout {
            background: #4361ee;
            color: white;
            border: none;
            padding: 15px 30px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 16px;
            width: 100%;
            transition: all 0.3s;
        }

        .btn-checkout:hover {
            background: #3651d4;
        }

        .btn-checkout:disabled {
            background: #ccc;
            cursor: not-allowed;
        }

        @media (max-width: 768px) {
            .delivery-options {
                grid-template-columns: 1fr;
            }

            .checkout-actions {
                position: fixed;
                bottom: 0;
                left: 0;
                right: 0;
                margin: 0;
                border-radius: 0;
                box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.1);
            }

            .checkout-container {
                padding-bottom: 100px;
            }
        }
    </style>
@endsection

@section('content')
    <div class="bg-light py-3">
        <div class="container">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="/" class="text-decoration-none">Home</a></li>
                    <li class="breadcrumb-item"><a href="/keranjang" class="text-decoration-none">Keranjang</a></li>
                    <li class="breadcrumb-item active">Checkout</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="checkout-container">
        <div class="container">
            <div class="row">
                <div class="col-lg-8">
                    <!-- Delivery Options -->
                    <div class="checkout-card">
                        <h5 class="section-title"><i class="fas fa-truck"></i>Metode Pengiriman</h5>
                        <div class="delivery-options">
                            <div class="delivery-option active" onclick="pilihMetodePengiriman('antar', this)">
                                <input type="radio" name="delivery_method" value="antar" checked>
                                <span class="icon">🚗</span>
                                <div class="title">Pesan Antar</div>
                                <p class="desc">Estimasi: 2-3 hari<br>+ Ongkir</p>
                            </div>
                            <div class="delivery-option" onclick="pilihMetodePengiriman('ambil', this)">
                                <input type="radio" name="delivery_method" value="ambil">
                                <span class="icon">🏪</span>
                                <div class="title">Ambil Sendiri</div>
                                <p class="desc">Gratis ongkir</p>
                            </div>
                        </div>
                    </div>

                    <!-- Address Section -->
                    <div class="checkout-card" id="address-section">
                        <h5 class="section-title"><i class="fas fa-map-marker-alt"></i>Alamat Pengiriman</h5>
                        @foreach($addresses as $i => $address)
                            <div class="card-item address-card{{ $i === 0 ? ' selected' : '' }}" onclick="pilihAlamat(this)">
                                <input type="radio" name="selected_address" value="{{ $address['id'] }}" {{ $i === 0 ? 'checked' : '' }}>
                                <div class="address-label">{{ strtoupper($address['label']) }}</div>
                                <div class="address-name">{{ $user_name }}</div>
                                <div class="address-detail">
                                    {{ $address['alamat_lengkap'] }}<br>
                                    {{ $address['kelurahan'] }}, {{ $address['kecamatan'] }}, {{ $address['kota'] }},
                                    {{ $address['provinsi'] }} {{ $address['kode_pos'] }}<br>
                                    +{{ $address['nomor_hp'] }}
                                </div>
                            </div>
                        @endforeach
                        <button class="btn-add-address"><i class="fas fa-plus me-2"></i>Tambah Alamat Baru</button>
                    </div>

                    <!-- Shipping Section -->
                    <div class="checkout-card" id="shipping-section">
                        <h5 class="section-title"><i class="fas fa-shipping-fast"></i>Pilihan Ekspedisi</h5>
                        @if(isset($expeditions) && count($expeditions) > 0)
                            @foreach($expeditions as $index => $layanan)
                                <div class="card-item ekspedisi-option{{ $index === 0 ? ' selected' : '' }}"
                                    data-cost="{{ $layanan['cost'] ?? 0 }}" onclick="pilihEkspedisi(this)">
                                    <input type="radio" name="selected_ekspedisi"
                                        value="{{ $layanan['code'] . '-' . ($layanan['service'] ?? '') }}" {{ $index === 0 ? 'checked' : '' }}>
                                    <div class="ekspedisi-info">
                                        <div class="ekspedisi-name">{{ $layanan['name'] ?? '-' }}</div>
                                        <div class="ekspedisi-service">{{ $layanan['description'] ?? '-' }}</div>
                                    </div>
                                    <div class="ekspedisi-price">
                                        <div class="ekspedisi-cost">Rp {{ number_format($layanan['cost'] ?? 0, 0, ',', '.') }}</div>
                                        <div class="ekspedisi-estimate">{{ $layanan['etd'] ?? '-' }}</div>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="ekspedisi-option" data-cost="0">
                                <div class="ekspedisi-info">
                                    <div class="ekspedisi-name">Tidak ada ekspedisi ditemukan.</div>
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Store Section -->
                    <div class="checkout-card" id="store-section" style="display: none;">
                        <h5 class="section-title"><i class="fas fa-store"></i>Informasi Toko</h5>
                        <div class="store-info">
                            <div class="store-info-item">
                                <i class="fas fa-map-marker-alt store-info-icon"></i>
                                <div class="store-info-content">
                                    <h6>Alamat Toko</h6>
                                    <p>
                                        {{ $tokoInfo->alamat_lengkap }},
                                        {{ $tokoInfo->kecamatan }},
                                        {{ $tokoInfo->kota }},
                                        {{ $tokoInfo->provinsi }},
                                        {{ $tokoInfo->kode_pos }}
                                    </p>
                                </div>
                            </div>
                            <div class="store-info-item">
                                <i class="fas fa-clock store-info-icon"></i>
                                <div class="store-info-content">
                                    <h6>Jam Operasional</h6>
                                    <p>Senin - Jumat: 08:00 - 17:00<br>Sabtu: 08:00 - 15:00<br>Minggu: Tutup</p>
                                </div>
                            </div>
                            <div class="store-info-item">
                                <i class="fas fa-phone store-info-icon"></i>
                                <div class="store-info-content">
                                    <h6>Kontak</h6>
                                    <p>{{ $tokoInfo->nomor_telepon ?? 'Kontak belum tersedia' }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label"><strong>Catatan Pengambilan</strong> <span
                                    class="text-muted">(Opsional)</span></label>
                            <textarea class="form-control" rows="3"
                                placeholder="Contoh: Mohon SMS jika pesanan sudah siap diambil"></textarea>
                            <small class="text-muted">Kami akan menghubungi Anda ketika pesanan siap diambil</small>
                        </div>
                    </div>

                    <!-- Payment Method -->
                    <div class="checkout-card">
                        <h5 class="section-title"><i class="fas fa-credit-card"></i>Metode Pembayaran</h5>
                        <!-- COD - Hidden by default when antar is selected -->
                        <div class="card-item payment-option" id="cod-option" onclick="pilihPembayaran('cod', this)"
                            style="display: none;">
                            <input type="radio" name="payment_method" value="cod">
                            <i class="fas fa-money-bill-wave payment-icon"></i>
                            <div>
                                <div class="fw-semibold">COD (Bayar di Tempat)</div>
                                <small class="text-muted">Bayar saat pesanan diterima</small>
                            </div>
                        </div>
                        <!-- QRIS - Always visible -->
                        <div class="card-item payment-option selected" id="qris-option"
                            onclick="pilihPembayaran('qris', this)">
                            <input type="radio" name="payment_method" value="qris" checked>
                            <i class="fas fa-qrcode payment-icon"></i>
                            <div>
                                <div class="fw-semibold">QRIS</div>
                                <small class="text-muted">Scan QR Code untuk pembayaran</small>
                            </div>
                        </div>
                    </div>

                    <!-- Order Notes -->
                    <div class="checkout-card">
                        <h5 class="section-title"><i class="fas fa-sticky-note"></i>Catatan Pesanan</h5>
                        <textarea class="form-control" rows="3"
                            placeholder="Catatan khusus untuk pesanan Anda (opsional)"></textarea>
                    </div>
                </div>

                <!-- Order Summary -->
                <div class="col-lg-4">
                    <div class="checkout-card">
                        <h5 class="section-title"><i class="fas fa-receipt"></i>Ringkasan Pesanan</h5>

                        @if(isset($produkTerpilih) && count($produkTerpilih) > 0)
                            @php
                                $subtotal = array_sum(array_map(fn($p) => ($p['harga_satuan'] ?? 0) * ($p['jumlah'] ?? 1), $produkTerpilih));
                                $biayaDesainFinal = $biaya_desain ?? 0;
                                $defaultOngkir = ($expeditions[0]['cost'] ?? 0);
                            @endphp

                            @foreach($produkTerpilih as $produk)
                                <div class="order-item">
                                    <img src="{{ isset($produk['item']['gambar']) ? asset('storage/' . $produk['item']['gambar']) : asset('images/products/default.png') }}"
                                        class="order-item-image" alt="{{ $produk['item']['nama_item'] ?? 'Produk' }}">
                                    <div class="order-item-details">
                                        <div class="order-item-name">{{ $produk['item']['nama_item'] ?? '-' }}</div>
                                        <div class="order-item-specs">
                                            {{ $produk['bahan']['nama_bahan'] ?? '-' }}, Ukuran:
                                            {{ $produk['ukuran']['size'] ?? '-' }}<br>
                                            Jenis: {{ $produk['jenis']['kategori'] ?? '-' }}<br>
                                            <span
                                                class="badge badge-{{ ($produk['tipe_desain'] ?? 'sendiri') === 'dibuatkan' ? 'info' : 'secondary' }}">
                                                {{ ($produk['tipe_desain'] ?? 'sendiri') === 'dibuatkan' ? 'Desain Toko' : 'Desain Sendiri' }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="order-item-price">
                                        <div class="order-item-quantity">{{ $produk['jumlah'] ?? 1 }}x</div>
                                        <div class="order-item-total">Rp
                                            {{ number_format(($produk['harga_satuan'] ?? 0) * ($produk['jumlah'] ?? 1), 0, ',', '.') }}
                                        </div>
                                    </div>
                                </div>
                            @endforeach

                            <hr>

                            <div class="order-summary">
                                <div class="summary-row">
                                    <span>Subtotal ({{ count($produkTerpilih) }} item)</span>
                                    <span id="subtotal-display">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                                </div>
                                @if($biayaDesainFinal > 0)
                                    <div class="summary-row">
                                        <span>Biaya Desain</span>
                                        <span id="design-cost-display">Rp {{ number_format($biayaDesainFinal, 0, ',', '.') }}</span>
                                    </div>
                                @endif
                                <div class="summary-row" id="shipping-cost-row">
                                    <span>Ongkos Kirim</span>
                                    <span id="shipping-cost-display">Rp {{ number_format($defaultOngkir, 0, ',', '.') }}</span>
                                </div>
                                <div class="summary-row total">
                                    <span><strong>Total</strong></span>
                                    <span id="total-display"><strong>Rp
                                            {{ number_format($subtotal + $biayaDesainFinal + $defaultOngkir, 0, ',', '.') }}</strong></span>
                                </div>
                            </div>
                        @else
                            <p>Tidak ada produk yang dipilih.</p>
                        @endif

                        <div class="checkout-actions">
                            <button class="btn-checkout" type="button" onclick="prosesCheckout()">
                                <i class="fas fa-shopping-bag me-2"></i>Buat Pesanan
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Global variables
        var subtotal = {{ $subtotal ?? 0 }};
        var biayaDesain = {{ $biayaDesainFinal ?? 0 }};
        var defaultOngkir = {{ $defaultOngkir ?? 0 }};
        var currentOngkir = defaultOngkir;
        var currentMethod = 'antar';

        // Utility functions
        function formatRupiah(amount) {
            return 'Rp ' + amount.toLocaleString('id-ID');
        }

        function updateTotal() {
            var ongkir = (currentMethod === 'ambil') ? 0 : currentOngkir;
            var total = subtotal + biayaDesain + ongkir;

            document.getElementById('shipping-cost-display').textContent = formatRupiah(ongkir);
            document.getElementById('total-display').innerHTML = '<strong>' + formatRupiah(total) + '</strong>';
            document.getElementById('shipping-cost-row').style.display = (currentMethod === 'ambil') ? 'none' : 'flex';
        }

        // Selection functions
        function pilihMetodePengiriman(method, element) {
            // Update UI
            document.querySelectorAll('.delivery-option').forEach(el => el.classList.remove('active'));
            element.classList.add('active');
            element.querySelector('input[type="radio"]').checked = true;

            currentMethod = method;

            // Show/hide sections
            document.getElementById('address-section').style.display = method === 'antar' ? 'block' : 'none';
            document.getElementById('shipping-section').style.display = method === 'antar' ? 'block' : 'none';
            document.getElementById('store-section').style.display = method === 'ambil' ? 'block' : 'none';

            // Payment method logic
            var codOption = document.getElementById('cod-option');
            var qrisOption = document.getElementById('qris-option');

            if (method === 'antar') {
                // Pesan Antar: Only QRIS
                codOption.style.display = 'none';
                qrisOption.style.display = 'block';
                qrisOption.classList.add('selected');
                qrisOption.querySelector('input').checked = true;
            } else {
                // Ambil Sendiri: Both COD and QRIS
                codOption.style.display = 'block';
                qrisOption.style.display = 'block';
                // Default to COD for pickup
                qrisOption.classList.remove('selected');
                codOption.classList.add('selected');
                codOption.querySelector('input').checked = true;
                qrisOption.querySelector('input').checked = false;
            }

            updateTotal();
        }

        function pilihAlamat(element) {
            document.querySelectorAll('.address-card').forEach(el => el.classList.remove('selected'));
            element.classList.add('selected');
            element.querySelector('input[type="radio"]').checked = true;
        }

        function pilihEkspedisi(element) {
            document.querySelectorAll('.ekspedisi-option').forEach(el => el.classList.remove('selected'));
            element.classList.add('selected');
            element.querySelector('input[type="radio"]').checked = true;

            currentOngkir = parseInt(element.getAttribute('data-cost')) || 0;
            updateTotal();
        }

        function pilihPembayaran(method, element) {
            document.querySelectorAll('.payment-option').forEach(el => el.classList.remove('selected'));
            element.classList.add('selected');
            element.querySelector('input[type="radio"]').checked = true;
        }

        function prosesCheckout() {
            // Validation
            if (currentMethod === 'antar') {
                if (!document.querySelector('input[name="selected_address"]:checked')) {
                    alert('Silakan pilih alamat pengiriman terlebih dahulu.');
                    return;
                }
                if (!document.querySelector('input[name="selected_ekspedisi"]:checked')) {
                    alert('Silakan pilih ekspedisi terlebih dahulu.');
                    return;
                }
            }

            var paymentMethod = document.querySelector('input[name="payment_method"]:checked');
            if (!paymentMethod) {
                alert('Silakan pilih metode pembayaran terlebih dahulu.');
                return;
            }

            // Disable button
            var btn = document.querySelector('.btn-checkout');
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Memproses...';

            // Prepare data
            var ekspedisiData = null;
            if (currentMethod === 'antar') {
                var selectedEkspedisi = document.querySelector('input[name="selected_ekspedisi"]:checked');
                var selectedEkspedisiElement = selectedEkspedisi.closest('.ekspedisi-option');

                if (selectedEkspedisi && selectedEkspedisiElement) {
                    var ekspedisiValue = selectedEkspedisi.value.split('-');
                    ekspedisiData = {
                        code: ekspedisiValue[0] || '',
                        service: ekspedisiValue[1] || '',
                        nama: selectedEkspedisiElement.querySelector('.ekspedisi-name').textContent,
                        description: selectedEkspedisiElement.querySelector('.ekspedisi-service').textContent,
                        cost: parseInt(selectedEkspedisiElement.getAttribute('data-cost')) || 0,
                        etd: selectedEkspedisiElement.querySelector('.ekspedisi-estimate').textContent
                    };
                }
            }

            var data = {
                alamat_id: document.querySelector('input[name="selected_address"]:checked')?.value || null,
                selected_items: @json(array_column($produkTerpilih ?? [], 'id')),
                ongkir: (currentMethod === 'antar') ? currentOngkir : 0,
                payment_method: paymentMethod.value,
                delivery_method: currentMethod,
                ekspedisi: ekspedisiData
            };

            // Send to server
            fetch('{{ route("checkout.payment") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify(data)
            })
                .then(response => response.json())
                .then(result => {
                    if (result.success) {
                        if (result.payment_method === 'qris' && result.snap_token && window.snap) {
                            window.snap.pay(result.snap_token, {
                                onSuccess: () => {
                                    alert('Pembayaran berhasil! Pesanan Anda sedang diproses.');
                                    window.location.href = '/keranjang';
                                },
                                onPending: () => {
                                    alert('Pembayaran sedang diproses.');
                                    window.location.href = '/keranjang';
                                },
                                onError: () => {
                                    alert('Pembayaran gagal. Silakan coba lagi.');
                                    resetButton();
                                },
                                onClose: () => {
                                    alert('Pembayaran dibatalkan.');
                                    resetButton();
                                }
                            });
                        } else {
                            alert('Pesanan berhasil dibuat!');
                            window.location.href = '/keranjang';
                        }
                    } else {
                        alert(result.message || 'Terjadi kesalahan saat memproses pesanan.');
                        resetButton();
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan: ' + error.message);
                    resetButton();
                });
        }

        function resetButton() {
            var btn = document.querySelector('.btn-checkout');
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-shopping-bag me-2"></i>Buat Pesanan';
        }

        // Load Midtrans Snap
        @if(config('midtrans.client_key'))
            var snapScript = document.createElement('script');
            snapScript.src = 'https://app.sandbox.midtrans.com/snap/snap.js';
            snapScript.setAttribute('data-client-key', '{{ config("midtrans.client_key") }}');
            document.head.appendChild(snapScript);
        @endif

        // Initialize
        updateTotal();
    </script>
@endsection