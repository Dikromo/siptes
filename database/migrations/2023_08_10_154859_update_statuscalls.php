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
        Schema::table('statuscalls', function (Blueprint $table) {
            //
            $table->integer('produk_id')->default(0);
            $table->integer('parentstatus_id')->default(0);
            $table->integer('cabang_id')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('statuscalls', function (Blueprint $table) {
            //
            $table->dropColumn('produk_id');
            $table->dropColumn('parentstatus_id');
            $table->dropColumn('cabang_id');
        });
    }
};
