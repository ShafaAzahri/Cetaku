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
        Schema::create('detail_pesanans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pesanan_id')->nullable()->constrained('pesanans');
            $table->foreignId('custom_id')->nullable()->constrained('customs');
            $table->integer('jumlah')->nullable();
            $table->string('upload_desain')->nullable();
            $table->decimal('total_harga', 10, 2)->nullable();
            $table->enum('tipe_desain', ['sendiri', 'dibuatkan'])->nullable();
            $table->decimal('biaya_jasa', 10, 2)->nullable();
            $table->integer('rating')->nullable();
            $table->text('komentar')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->string('desain_revisi', 100)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_pesanans');
    }
};