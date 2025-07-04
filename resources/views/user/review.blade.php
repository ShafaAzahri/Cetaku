@extends('user.layouts.app')

@section('custom-css')
<style>
    .rating-stars input[type="radio"] {
        display: none;
    }
    .rating-stars label {
        font-size: 2rem;
        color: #ccc;
        cursor: pointer;
        transition: color 0.2s;
    }
    .rating-stars input:checked ~ label,
    .rating-stars label:hover,
    .rating-stars label:hover ~ label {
        color: #f5b301;
    }
</style>
@endsection

@section('content')
<div class="container py-5">
    <h2 class="mb-4">Beri Ulasan</h2>
    <form action="{{ route('review.submit', $detail->id) }}" method="POST">
        @csrf
        <div class="mb-4">
            <label class="form-label">Rating</label>
            <div class="rating-stars d-flex flex-row-reverse justify-content-start">
                @for($i = 5; $i >= 1; $i--)
                    <input type="radio" id="star{{ $i }}" name="rating" value="{{ $i }}" {{ old('rating') == $i ? 'checked' : '' }}>
                    <label for="star{{ $i }}">&#9733;</label>
                @endfor
            </div>
            @error('rating')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-4">
            <label class="form-label">Komentar</label>
            <textarea name="komentar" class="form-control" rows="4" required>{{ old('komentar') }}</textarea>
            @error('komentar')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn btn-primary">Kirim Ulasan</button>
        <a href="{{ route('user.pesanan') }}" class="btn btn-secondary">Kembali</a>
    </form>
</div>
@endsection
