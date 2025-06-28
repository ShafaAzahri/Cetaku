<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ekspedisi extends Model
{
    // Nonaktifkan timestamps untuk model ini
    public $timestamps = false;

    protected $fillable = [
        'pesanan_id',
        'nama_ekspedisi',
        'layanan',
        'estimasi',
        'ongkos_kirim',
        'berat'
    ];

    /**
     * Relasi dengan pesanan
     */
    public function pesanan()
    {
        return $this->belongsTo(Pesanan::class, 'pesanan_id');
    }
}
