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
        Schema::create('mutasi_lists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mutasi_id');
            $table->string('jenis')->nullable();
            $table->string('deskripsi')->nullable();
            $table->string('deskripsi2')->nullable();
            $table->string('deskripsi3')->nullable();
            $table->string('jumlah')->nullable();
            $table->timestamp('tanggal')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mutasi_lists');
    }
};
