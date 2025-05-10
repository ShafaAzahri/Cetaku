<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ekspedisi extends Model
{
    /**
     * Nama tabel yang terkait dengan model.
     *
     * @var string
     */
    protected $table = 'ekspedisis';

    /**
     * Atribut yang dapat diisi secara massal.
     *
     * @var array
     */
    protected $fillable = [
        'nama_ekspedisi',
        'layanan',
        'estimasi',
        'ongkos_kirim',
        'berat'
    ];

    /**
     * Atribut yang harus dikonversi ke tipe data khusus.
     *
     * @var array
     */
    protected $casts = [
        'ongkos_kirim' => 'decimal:2',
    ];

    /**
     * Dapatkan pesanan terkait dengan ekspedisi.
     */
    public function pesanans()
    {
        return $this->hasMany(Pesanan::class);
    }
}