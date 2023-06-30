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
        Schema::create('jmos', function (Blueprint $table) {
            $table->id();
            $table->string('email')->unique();
            $table->string('password');
            $table->string('nama');
            $table->string('perusahaan');
            $table->string('statusPeserta');
            $table->string('segmenPeserta');
            $table->string('lastUpah');
            $table->string('lastIuranDate');
            $table->string('pensiunanDate');
            $table->string('masaIuranjp');
            $table->string('kepesertaanDate');
            $table->string('masaIuranjkp');
            $table->string('jkm');
            $table->string('jkk');
            $table->string('jht');
            $table->string('jp');
            $table->string('jkp');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jmos');
    }
};
