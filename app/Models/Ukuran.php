<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ukuran extends Model
{
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;
    
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'ukurans';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'size',
        'faktor_harga'
    ];
    
    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'faktor_harga' => 'decimal:2',
    ];
    
    /**
     * The items that belong to the ukuran.
     */
    public function items()
    {
        return $this->belongsToMany(Item::class, 'item_ukurans');
    }
}