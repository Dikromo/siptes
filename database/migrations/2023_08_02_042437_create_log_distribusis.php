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
        Schema::create('log_distribusis', function (Blueprint $table) {
            $table->id();
            $table->string('tipe')->nullable();
            $table->string('kode')->nullable();
            $table->string('nama_sales')->nullable();
            $table->string('provider')->nullable();
            $table->string('deskripsi')->nullable();
            $table->string('total')->nullable();
            $table->foreignId('user_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('log_distribusis');
    }
};
