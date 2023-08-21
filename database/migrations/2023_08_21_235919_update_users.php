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
        Schema::table('users', function (Blueprint $table) {
            //
            $table->after('parentuser_id', function (Blueprint $table) {
                $table->string('sm_id')->nullable();
                $table->string('um_id')->nullable();
                $table->string('flag_hadir')->nullable();
            });
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            //
            $table->dropColumn('sm_id');
            $table->dropColumn('um_id');
            $table->dropColumn('flag_hadir');
        });
    }
};
