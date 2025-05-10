<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProsesPesanan extends Model
{
    /**
     * Nama tabel yang terkait dengan model.
     *
     * @var string
     */
    protected $table = 'proses_pesanans';

    /**
     * Atribut yang dapat diisi secara massal.
     *
     * @var array
     */
    protected $fillable = [
        'detail_pesanan_id',
        'mesin_id',
        'operator_id',
        'waktu_mulai',
        'waktu_selesai',
        'status_proses',
        'catatan'
    ];

    /**
     * Atribut yang harus dikonversi ke tipe data khusus.
     *
     * @var array
     */
    protected $casts = [
        'waktu_mulai' => 'datetime',
        'waktu_selesai' => 'datetime',
    ];

    /**
     * Dapatkan detail pesanan terkait dengan proses.
     */
    public function detailPesanan()
    {
        return $this->belongsTo(DetailPesanan::class);
    }

    /**
     * Dapatkan mesin terkait dengan proses.
     */
    public function mesin()
    {
        return $this->belongsTo(Mesin::class);
    }

    /**
     * Dapatkan operator terkait dengan proses.
     */
    public function operator()
    {
        return $this->belongsTo(Operator::class);
    }
}