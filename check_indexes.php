<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$tables = DB::select('SHOW TABLES');
$tableKey = 'Tables_in_' . env('DB_DATABASE');

foreach ($tables as $table) {
    $tableName = $table->$tableKey;
    echo "Indexes for $tableName:\n";
    $indexes = DB::select("SHOW INDEX FROM $tableName");
    foreach ($indexes as $index) {
        echo "- " . $index->Key_name . " (" . $index->Column_name . ")\n";
    }
    echo "--------------------------\n";
}
