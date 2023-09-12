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
        Schema::table('fileexcels', function (Blueprint $table) {
            //
            $table->string('prioritas')->nullable();
            $table->timestamp('prioritas_date')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('fileexcels', function (Blueprint $table) {
            //
            $table->dropColumn('prioritas');
            $table->dropColumn('prioritas_date');
        });
    }
};
