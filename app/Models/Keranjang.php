<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Keranjang extends Model
{
    /**
     * Nama tabel yang terkait dengan model.
     *
     * @var string
     */
    protected $table = 'keranjang';

    /**
     * Atribut yang dapat diisi secara massal.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'item_id',
        'ukuran_id',
        'bahan_id',
        'jenis_id',
        'quantity',
        'upload_desain',
        'harga_satuan',
        'total_harga',
    ];

    /**
     * Atribut yang harus dikonversi ke tipe data khusus.
     *
     * @var array
     */
    protected $casts = [
        'quantity' => 'integer',
        'harga_satuan' => 'decimal:2',
        'total_harga' => 'decimal:2',
    ];

    /**
     * Relasi ke User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi ke Item
     */
    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    /**
     * Relasi ke Ukuran
     */
    public function ukuran()
    {
        return $this->belongsTo(Ukuran::class);
    }

    /**
     * Relasi ke Bahan
     */
    public function bahan()
    {
        return $this->belongsTo(Bahan::class);
    }

    /**
     * Relasi ke Jenis
     */
    public function jenis()
    {
        return $this->belongsTo(Jenis::class);
    }

    /**
     * Hitung harga satuan berdasarkan item + biaya tambahan
     */
    public function calculateHargaSatuan()
    {
        $hargaDasar = $this->item->harga_dasar ?? 0;
        $biayaUkuran = $this->ukuran->biaya_tambahan ?? 0;
        $biayaBahan = $this->bahan->biaya_tambahan ?? 0;
        $biayaJenis = $this->jenis->biaya_tambahan ?? 0;

        return $hargaDasar + $biayaUkuran + $biayaBahan + $biayaJenis;
    }

    /**
     * Hitung total harga berdasarkan quantity
     */
    public function calculateTotalHarga()
    {
        return $this->calculateHargaSatuan() * $this->quantity;
    }

    /**
     * Update harga otomatis sebelum save
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($keranjang) {
            if ($keranjang->isDirty(['item_id', 'ukuran_id', 'bahan_id', 'jenis_id', 'quantity'])) {
                $keranjang->harga_satuan = $keranjang->calculateHargaSatuan();
                $keranjang->total_harga = $keranjang->calculateTotalHarga();
            }
        });
    }
}