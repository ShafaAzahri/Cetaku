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
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'nama_bahan',
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
     * The items that belong to the bahan.
     */
    public function items()
    {
        return $this->belongsToMany(Item::class, 'item_bahans');
    }
}