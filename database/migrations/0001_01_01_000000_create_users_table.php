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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('nama')->nullable();
            $table->string('password')->nullable();
            $table->string('email')->nullable();
            $table->string('api_token', 80)->nullable()->unique();
            $table->timestamp('token_expires_at')->nullable();
            $table->foreignId('role_id')->nullable()->constrained('roles');
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('last_login_at')->nullable();
            $table->string('last_login_ip', 45)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};