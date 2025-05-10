<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Custom extends Model
{
    /**
     * Atribut yang dapat diisi secara massal.
     *
     * @var array
     */
    protected $fillable = [
        'item_id',
        'ukuran_id',
        'bahan_id',
        'jenis_id',
        'harga'
    ];
    
    /**
     * Disable timestamps.
     *
     * @var bool
     */
    public $timestamps = false;
    
    /**
     * Atribut yang harus dikonversi ke tipe data khusus.
     *
     * @var array
     */
    protected $casts = [
        'harga' => 'decimal:2',
    ];
    
    /**
     * Dapatkan item terkait dengan custom.
     */
    public function item()
    {
        return $this->belongsTo(Item::class);
    }
    
    /**
     * Dapatkan ukuran terkait dengan custom.
     */
    public function ukuran()
    {
        return $this->belongsTo(Ukuran::class);
    }
    
    /**
     * Dapatkan bahan terkait dengan custom.
     */
    public function bahan()
    {
        return $this->belongsTo(Bahan::class);
    }
    
    /**
     * Dapatkan jenis terkait dengan custom.
     */
    public function jenis()
    {
        return $this->belongsTo(Jenis::class);
    }
    
    /**
     * Dapatkan detail pesanan terkait dengan custom.
     */
    public function detailPesanans()
    {
        return $this->hasMany(DetailPesanan::class);
    }
}