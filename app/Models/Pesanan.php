<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pesanan extends Model
{
    /**
     * Nama tabel yang terkait dengan model.
     *
     * @var string
     */
    protected $table = 'pesanans';

    /**
     * Atribut yang dapat diisi secara massal.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'admin_id',
        'ekspedisi_id',
        'status',
        'metode_pengambilan',
        'waktu_pengambilan',
        'estimasi_waktu',
        'tanggal_dipesan'
    ];

    /**
     * Atribut yang harus dikonversi ke tipe data khusus.
     *
     * @var array
     */
    protected $casts = [
        'waktu_pengambilan' => 'datetime',
        'created_at' => 'datetime',
        'tanggal_dipesan' => 'datetime',
    ];

    /**
     * Dapatkan user (pelanggan) terkait dengan pesanan.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Dapatkan admin terkait dengan pesanan.
     */
    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    /**
     * Dapatkan ekspedisi terkait dengan pesanan.
     */
    public function ekspedisi()
    {
        return $this->belongsTo(Ekspedisi::class);
    }

    /**
     * Dapatkan detail pesanan terkait dengan pesanan.
     */
    public function detailPesanans()
    {
        return $this->hasMany(DetailPesanan::class);
    }

    /**
     * Dapatkan pembayaran terkait dengan pesanan.
     */
    public function pembayaran()
    {
        return $this->hasOne(Pembayaran::class);
    }
}