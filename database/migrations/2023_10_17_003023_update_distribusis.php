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
            //
            $table->string('namaktp')->nullable();
            $table->string('email')->nullable();
            $table->string('masakerja')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('distribusis', function (Blueprint $table) {
            //
            $table->dropColumn('namaktp');
            $table->dropColumn('email');
            $table->dropColumn('masakerja');
        });
    }
};
