<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kategori extends Model
{
    protected $fillable = [
        'nama_kategori',
        'deskripsi',
        'gambar'
    ];
    
    /**
     * The items that belong to the kategori.
     */
    public function items()
    {
        return $this->belongsToMany(Item::class, 'kategori_items');
    }
}