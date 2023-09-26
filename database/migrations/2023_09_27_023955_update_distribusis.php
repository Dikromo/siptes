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
            $table->string('statusadmin')->nullable();
            $table->timestamp('statusadmin_date')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('distribusis', function (Blueprint $table) {
            //
            $table->dropColumn('statusadmin');
            $table->dropColumn('statusadmin_date');
        });
    }
};
