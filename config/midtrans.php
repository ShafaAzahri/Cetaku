<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Konfigurasi Midtrans
    |--------------------------------------------------------------------------
    |
    | File ini mengambil kredensial Midtrans dari file .env Anda.
    | Menggunakan file config seperti ini adalah praktik terbaik karena
    | memungkinkan caching konfigurasi untuk performa yang lebih baik.
    |
    */

    'server_key' => env('MIDTRANS_SERVER_KEY'),
    
    'client_key' => env('MIDTRANS_CLIENT_KEY'),
    
    'is_production' => env('MIDTRANS_IS_PRODUCTION', false),

];