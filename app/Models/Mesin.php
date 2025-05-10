<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mesin extends Model
{
    /**
     * Nama tabel yang terkait dengan model.
     *
     * @var string
     */
    protected $table = 'mesins';

    /**
     * Atribut yang dapat diisi secara massal.
     *
     * @var array
     */
    protected $fillable = [
        'nama_mesin',
        'tipe_mesin',
        'status'
    ];

    /**
     * Dapatkan proses pesanan terkait dengan mesin.
     */
    public function prosesPesanans()
    {
        return $this->hasMany(ProsesPesanan::class);
    }
}