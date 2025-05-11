<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTimestampsToProsesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('proses_pesanans', function (Blueprint $table) {
            // Tambahkan kolom timestamps jika belum ada
            if (!Schema::hasColumn('proses_pesanans', 'created_at')) {
                $table->timestamp('created_at')->nullable();
            }
            
            if (!Schema::hasColumn('proses_pesanans', 'updated_at')) {
                $table->timestamp('updated_at')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('proses_pesanans', function (Blueprint $table) {
            $table->dropColumn(['created_at', 'updated_at']);
        });
    }
}