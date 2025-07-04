<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DetailPesanan;

class ReviewController extends Controller
{
    public function form($id)
    {
        $detail = DetailPesanan::findOrFail($id);
        return view('user.review', compact('detail'));
    }

    public function submit(Request $request, $id)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'komentar' => 'required|string|max:500',
        ]);

        $detail = DetailPesanan::findOrFail($id);
        $detail->rating = $request->rating;
        $detail->komentar = $request->komentar;
        $detail->reviewed_at = now();
        $detail->save();

        return redirect()->route('user.pesanan')->with('success', 'Ulasan berhasil dikirim.');
    }
}
