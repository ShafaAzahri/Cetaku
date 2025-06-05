<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TokoInfo extends Model
{
    protected $table = 'toko_info';
    protected $fillable = [
        'nama', 'alamat_lengkap', 'kecamatan', 'kota', 'provinsi', 'kode_pos', 'nomor_telepon', 'email', 'logo', 'is_active'
    ];
}