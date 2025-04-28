<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Custom extends Model
{
    protected $fillable = [
        'item_id',
        'ukuran_id',
        'bahan_id',
        'jenis_id',
        'harga'
    ];
    
    public $timestamps = false;
    
    public function item()
    {
        return $this->belongsTo(Item::class);
    }
    
    public function ukuran()
    {
        return $this->belongsTo(Ukuran::class);
    }
    
    public function bahan()
    {
        return $this->belongsTo(Bahan::class);
    }
    
    public function jenis()
    {
        return $this->belongsTo(Jenis::class);
    }
}