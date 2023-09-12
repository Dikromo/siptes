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
        Schema::table('jmos', function (Blueprint $table) {
            //
            $table->string('lastIuranDate_saldo')->nullable()->after('lastIuranDate');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('jmos', function (Blueprint $table) {
            //
            $table->dropColumn('lastIuranDate_saldo');
        });
    }
};
