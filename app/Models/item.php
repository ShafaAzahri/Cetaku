<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    /**
     * Atribut yang dapat diisi secara massal.
     *
     * @var array
     */
    protected $fillable = [
        'nama_item',
        'deskripsi',
        'harga_dasar',
        'gambar'
    ];
    
    /**
     * Indicates if the model should be timestamped.
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
        'harga_dasar' => 'decimal:2',
    ];
    
    /**
     * Get the bahans for the item.
     */
    public function bahans()
    {
        return $this->belongsToMany(Bahan::class, 'item_bahans');
    }
    
    /**
     * Get the ukurans for the item.
     */
    public function ukurans()
    {
        return $this->belongsToMany(Ukuran::class, 'item_ukurans');
    }
    
    /**
     * Get the jenis for the item.
     */
    public function jenis()
    {
        return $this->belongsToMany(Jenis::class, 'item_jenis');
    }
    
    /**
     * Get the customs for the item.
     */
    public function customs()
    {
        return $this->hasMany(Custom::class);
    }
    public function kategoris()
    {
        return $this->belongsToMany(Kategori::class, 'kategori_items');
    }
    /**
     * Relasi untuk detail pesanan melalui customs
     */
    public function detailPesanans()
    {
        return $this->hasManyThrough(
            DetailPesanan::class,
            Custom::class,
            'item_id',     // Foreign key di customs table
            'custom_id',   // Foreign key di detail_pesanans table
            'id',          // Local key di items table
            'id'           // Local key di customs table
        );
    }
}