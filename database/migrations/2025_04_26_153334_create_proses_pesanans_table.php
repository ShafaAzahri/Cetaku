<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('proses_pesanans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('detail_pesanan_id')->nullable()->constrained('detail_pesanans');
            $table->foreignId('mesin_id')->nullable()->constrained('mesins');
            $table->foreignId('operator_id')->nullable()->constrained('users');
            $table->timestamp('waktu_mulai')->nullable();
            $table->timestamp('waktu_selesai')->nullable();
            $table->string('status_proses', 100)->nullable();
            $table->text('catatan')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('proses_pesanans');
    }
};