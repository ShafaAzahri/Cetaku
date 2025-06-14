<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pembayaran extends Model
{
    use HasFactory;

    protected $fillable = [
        'pesanan_id', 'midtrans_order_id', 'midtrans_transaction_id', 'midtrans_payment_type', 'midtrans_transaction_status', 'midtrans_fraud_status', 
        'metode', 'status', 'bukti_bayar', 'tanggal_bayar', 'expired_at', 'keranjang_snapshot', 'transaction_id', 'snap_token', 'payment_url', 
        'midtrans_response'
    ];

    // Relasi ke pesanan
    public function pesanan()
    {
        return $this->belongsTo(Pesanan::class);
    }
}
