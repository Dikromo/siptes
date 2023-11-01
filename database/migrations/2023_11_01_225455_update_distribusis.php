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
            $table->string('d_parentuser_id')->nullable();
            $table->string('d_sm_id')->nullable();
            $table->string('d_um_id')->nullable();
            $table->string('d_cabang_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('distribusis', function (Blueprint $table) {
            //
            $table->dropColumn('d_parentuser_id');
            $table->dropColumn('d_sm_id');
            $table->dropColumn('d_um_id');
            $table->dropColumn('d_cabang_id');
        });
    }
};
