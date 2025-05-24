<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;

class pesanan extends Controller
{
    public function index()
    {
        return view('user.pesanan');
    }
    public function indexk()
    {
        return view('user.keranjang');
    }

    public function allproduk()
    {
        return view('user.produk-all');
    }
    public function detailproduk()
    {
        return view('user.product-detail');
    }
}
