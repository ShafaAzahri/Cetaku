<?php
// test_midtrans.php

require_once '../vendor/autoload.php';

use Midtrans\Config;

// Set konfigurasi
Config::$serverKey = 'SB-Mid-server-ENGNOvmSgF89Mn1dX9Yi9DF3';
Config::$isProduction = false;

echo "Midtrans SDK berhasil diinstall!\n";
echo "Server Key: " . Config::$serverKey . "\n";
echo "Production Mode: " . (Config::$isProduction ? 'Yes' : 'No') . "\n";
