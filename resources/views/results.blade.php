@extends('user.layouts.app')

@section('title', 'Hasil Pencarian - CETAKU')

@section('content')
<!-- Breadcrumb -->
<div class="bg-light py-3 fw-medium">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="/" class="text-decoration-none text-secondary">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">Hasil Pencarian</li>
            </ol>
        </nav>
    </div>
</div>

<!-- Products Section -->
<section class="products-section py-5">
    <div class="container">
        <h2 class="section-title text-center mb-4">Hasil pencarian: "{{ $query }}"</h2>

        <div class="row g-4">
            @forelse ($results as $item)
                <div class="col-6 col-md-4 col-lg-3">
                    <div class="product-card h-100">
                        <div class="card border-0 shadow-sm h-100">
                            <img src="{{ isset($item->gambar) && $item->gambar ? asset('storage/' . $item->gambar) : asset('images/products/default.png') }}"
                                alt="{{ $item->nama_item }}" class="img-fluid product-image">

                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title">{{ $item->nama_item }}</h5>
                                <p class="card-text small text-muted">{{ Str::limit($item->deskripsi, 50) }}</p>
                                <div class="mt-auto d-flex justify-content-between align-items-center">
                                    <span class="fw-bold text-primary">Rp {{ number_format($item->harga_dasar, 0, ',', '.') }}</span>
                                    <a href="{{ route('product.detail', $item->id) }}" class="btn btn-sm btn-outline-primary">Lihat Detail</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12 text-center">
                    <p class="text-muted">Produk tidak ditemukan.</p>
                </div>
            @endforelse
        </div>



<!-- Style agar seragam -->
<style>
    .product-card {
        transition: transform 0.3s;
        height: 100%;
    }

    .product-card:hover {
        transform: translateY(-5px);
    }

    .product-image {
        height: 200px;
        object-fit: cover;
        width: 100%;
    }

    .section-title {
        position: relative;
        display: inline-block;
        padding-bottom: 10px;
        margin-bottom: 30px;
    }

    .section-title::after {
        content: '';
        position: absolute;
        width: 80px;
        height: 3px;
        background-color: #4361ee;
        bottom: 0;
        left: 50%;
        transform: translateX(-50%);
    }

    @media (max-width: 576px) {
        .product-image {
            height: 150px;
        }
    }
</style>
@endsection
