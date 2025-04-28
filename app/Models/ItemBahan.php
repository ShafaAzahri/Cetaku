<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ItemBahan extends Model
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
        'item_id',
        'bahan_id'
    ];
}   