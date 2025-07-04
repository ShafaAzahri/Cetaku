<?php

// App\Models\TokoInfo.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TokoInfo extends Model
{
    protected $table = 'toko_info';

    protected $fillable = [
        'nama',
        'alamat_lengkap',
        'kecamatan',
        'kota',
        'provinsi',
        'kode_pos',
        'nomor_telepon',
        'email',
        'logo',
        'is_active'
    ];

    /**
     * Ambil satu toko yang aktif.
     *
     * @return TokoInfo|null
     */
    public static function getActiveToko()
    {
        return self::where('is_active', true)->first();
    }
}
