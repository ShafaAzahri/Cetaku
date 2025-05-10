<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pembayaran extends Model
{
    /**
     * Nama tabel yang terkait dengan model.
     *
     * @var string
     */
    protected $table = 'pembayarans';

    /**
     * Atribut yang dapat diisi secara massal.
     *
     * @var array
     */
    protected $fillable = [
        'pesanan_id',
        'metode',
        'status',
        'bukti_bayar',
        'tanggal_bayar'
    ];

    /**
     * Atribut yang harus dikonversi ke tipe data khusus.
     *
     * @var array
     */
    protected $casts = [
        'tanggal_bayar' => 'datetime',
    ];

    /**
     * Dapatkan pesanan terkait dengan pembayaran.
     */
    public function pesanan()
    {
        return $this->belongsTo(Pesanan::class);
    }
}