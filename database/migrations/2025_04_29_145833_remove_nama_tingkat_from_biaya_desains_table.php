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
        Schema::table('biaya_desains', function (Blueprint $table) {
            $table->dropColumn('nama_tingkat');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('biaya_desains', function (Blueprint $table) {
            $table->string('nama_tingkat')->nullable()->after('id');
        });
    }
};