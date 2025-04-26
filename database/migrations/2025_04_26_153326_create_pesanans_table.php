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
        Schema::create('pesanans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users');
            $table->foreignId('admin_id')->nullable()->constrained('users');
            $table->foreignId('ekspedisi_id')->nullable()->constrained('ekspedisis');
            $table->string('status', 50)->nullable();
            $table->enum('metode_pengambilan', ['antar', 'ambil'])->nullable();
            $table->timestamp('waktu_pengambilan')->nullable();
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->integer('estimasi_waktu')->nullable();
            $table->datetime('tanggal_dipesan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pesanans');
    }
};