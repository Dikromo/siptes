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
            $table->string('foto')->nullable()->after('nama');
            $table->string('lastiuran')->nullable();
            $table->string('jmltenagakerja')->nullable();
            $table->string('saldo')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('jmos', function (Blueprint $table) {
            //
            $table->dropColumn('foto');
            $table->dropColumn('lastiuran');
            $table->dropColumn('jmltenagakerja');
            $table->dropColumn('saldo');
        });
    }
};
