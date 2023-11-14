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
        Schema::create('submitions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('distribusis_id');
            $table->string('tipeproses')->nullable();
            $table->string('nik')->nullable();
            $table->string('namaktp')->nullable();
            $table->string('email')->nullable();
            $table->string('dob')->nullable();
            $table->string('perusahaan')->nullable();
            $table->string('nokantor')->nullable();
            $table->string('kota')->nullable();
            $table->string('zipcode')->nullable();
            $table->string('domisili')->nullable();
            $table->string('masakerja')->nullable();
            $table->string('jabatan')->nullable();
            $table->string('jmoasli')->nullable();
            $table->string('limit')->nullable();
            $table->string('loan_apply')->nullable();
            $table->string('mob')->nullable();
            $table->string('bank_penerbit')->nullable();
            $table->string('statusadmin')->nullable();
            $table->timestamp('statusadmin_date')->nullable();
            $table->string('remarksadmin')->nullable();
            $table->string('statusbank')->nullable();
            $table->timestamp('statusbank_date')->nullable();
            $table->string('remarksbank')->nullable();
            $table->string('temp_limit')->nullable();
            $table->string('disburse_limit')->nullable();
            $table->foreignId('created_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('submitions');
    }
};
