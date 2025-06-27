@extends('user.layouts.app')

@section('title', 'Notifikasi Pesanan')

@section('content')
<div class="container my-4">
    <h2>Notifikasi Pesanan</h2>

    @forelse ($pesanans as $pesanan)
        <div class="card mb-3">
            <div class="card-body">
                <strong>Pesanan #{{ $pesanan->kode }}</strong><br>
                Status: <span class="badge bg-info">{{ $pesanan->status }}</span><br>
                <small>{{ $pesanan->created_at->diffForHumans() }}</small>
            </div>
        </div>
    @empty
        <div class="alert alert-info">Tidak ada notifikasi pesanan.</div>
    @endforelse
</div>
@endsection
