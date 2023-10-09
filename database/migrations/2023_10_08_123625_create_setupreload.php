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
        Schema::create('setupreloads', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->foreignId('fileexcel_id')->nullable();
            $table->foreignId('group_id')->nullable();
            $table->foreignId('statuscall_id');
            $table->integer('status')->default(0);
            $table->foreignId('created_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('setupreloads');
    }
};