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
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'kategori',
        'biaya_tambahan'
    ];
    
    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'biaya_tambahan' => 'decimal:2',
    ];
    
    /**
     * Get the items for the jenis.
     */
    public function items()
    {
        return $this->belongsToMany(Item::class, 'item_jenis');
    }
}