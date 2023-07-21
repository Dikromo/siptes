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
        Schema::table('distribusis', function (Blueprint $table) {
            $table->after('deskripsi', function (Blueprint $table) {
                $table->string('tipeproses')->nullable();
                $table->string('nik')->nullable();
                $table->string('dob')->nullable();
                $table->string('perusahaan')->nullable();
                $table->string('jabatan')->nullable();
                $table->string('jmoasli')->nullable();
            });
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('distribusis', function (Blueprint $table) {
            $table->dropColumn('tipeproses');
            $table->dropColumn('nik');
            $table->dropColumn('dob');
            $table->dropColumn('perusahaan');
            $table->dropColumn('jabatan');
            $table->dropColumn('jmoasli');
        });
    }
};
