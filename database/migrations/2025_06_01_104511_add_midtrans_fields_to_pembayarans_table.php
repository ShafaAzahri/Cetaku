<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('pembayarans', function (Blueprint $table) {
            // Tambahkan field yang belum ada
            if (!Schema::hasColumn('pembayarans', 'expires_at')) {
                $table->timestamp('expires_at')->nullable()->after('tanggal_bayar');
            }
            
            if (!Schema::hasColumn('pembayarans', 'snap_token')) {
                $table->string('snap_token')->nullable()->after('expires_at');
            }
            
            // Update enum metode jika belum ada QRIS
            $table->enum('metode', ['COD', 'QRIS'])->change();
            
            // Update enum status jika belum ada Dibatalkan
            $table->enum('status', ['Pending', 'Lunas', 'Dibatalkan'])->change();
        });
    }

    public function down()
    {
        Schema::table('pembayarans', function (Blueprint $table) {
            $table->dropColumn(['expires_at', 'snap_token']);
        });
    }
};
