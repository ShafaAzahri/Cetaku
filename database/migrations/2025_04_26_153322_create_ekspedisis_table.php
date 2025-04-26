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
        Schema::create('ekspedisis', function (Blueprint $table) {
            $table->id();
            $table->string('nama_ekspedisi')->nullable();
            $table->string('layanan')->nullable();
            $table->string('estimasi')->nullable();
            $table->decimal('ongkos_kirim', 10, 2)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ekspedisis');
    }
};