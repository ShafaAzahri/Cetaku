<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'nama_item',
        'deskripsi',
        'jenis_id',
        'harga_dasar'
    ];
    
    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'harga_dasar' => 'decimal:2',
    ];
    
    /**
     * Get the jenis that owns the item.
     */
    public function jenis()
    {
        return $this->belongsTo(Jenis::class);
    }
    
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
}