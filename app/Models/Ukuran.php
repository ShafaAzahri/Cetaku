<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ukuran extends Model
{
    /**
     * Nama tabel yang terkait dengan model.
     *
     * @var string
     */
    protected $table = 'ukurans';
    
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
        'size',
        'biaya_tambahan'  // ganti dari 'faktor_harga'
    ];
    
    protected $casts = [
        'biaya_tambahan' => 'decimal:2',  // ganti dari 'faktor_harga'
    ];
    
    /**
     * The items that belong to the ukuran.
     */
    public function items()
    {
        return $this->belongsToMany(Item::class, 'item_ukurans');
    }
    
    /**
     * Get the customs for the ukuran.
     */
    public function customs()
    {
        return $this->hasMany(Custom::class);
    }
}