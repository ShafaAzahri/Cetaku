<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bahan extends Model
{
    /**
     * Indicates if the model should be timestamped.
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
        'nama_bahan',
        'biaya_tambahan'
    ];
    
    /**
     * Atribut yang harus dikonversi ke tipe data khusus.
     *
     * @var array
     */
    protected $casts = [
        'biaya_tambahan' => 'decimal:2',
    ];
    
    /**
     * The items that belong to the bahan.
     */
    public function items()
    {
        return $this->belongsToMany(Item::class, 'item_bahans');
    }
    
    /**
     * Get the customs for the bahan.
     */
    public function customs()
    {
        return $this->hasMany(Custom::class);
    }
}