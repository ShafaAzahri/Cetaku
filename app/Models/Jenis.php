<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Jenis extends Model
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
        'kategori',
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
     * The items that belong to the jenis.
     */
    public function items()
    {
        return $this->belongsToMany(Item::class, 'item_jenis');
    }
    
    /**
     * Get the customs for the jenis.
     */
    public function customs()
    {
        return $this->hasMany(Custom::class);
    }
}