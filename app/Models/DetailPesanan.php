<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetailPesanan extends Model
{
    /**
     * Nama tabel yang terkait dengan model.
     *
     * @var string
     */
    protected $table = 'detail_pesanans';

    /**
     * Menonaktifkan timestamps otomatis.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * Atribut yang dapat diisi secara massal.
     *
     * @var array
     */
    protected $fillable = [
        'pesanan_id',
        'custom_id',
        'jumlah',
        'upload_desain',
        'total_harga',
        'tipe_desain',
        'biaya_jasa',
        'rating',
        'komentar',
        'reviewed_at',
        'desain_revisi'
    ];

    /**
     * Atribut yang harus dikonversi ke tipe data khusus.
     *
     * @var array
     */
    protected $casts = [
        'total_harga' => 'decimal:2',
        'biaya_jasa' => 'decimal:2',
        'reviewed_at' => 'datetime',
    ];

    /**
     * Dapatkan pesanan terkait dengan detail pesanan.
     */
    public function pesanan()
    {
        return $this->belongsTo(Pesanan::class);
    }

    /**
     * Dapatkan custom terkait dengan detail pesanan.
     */
    public function custom()
    {
        return $this->belongsTo(Custom::class);
    }

    /**
     * Dapatkan proses pesanan terkait dengan detail pesanan.
     */
    public function prosesPesanan()
    {
        return $this->hasOne(ProsesPesanan::class);
    }
}