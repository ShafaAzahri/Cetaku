<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BiayaDesain extends Model
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
        'nama_tingkat',
        'deskripsi',
        'biaya'
    ];
    
    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'biaya' => 'decimal:2',
    ];
}