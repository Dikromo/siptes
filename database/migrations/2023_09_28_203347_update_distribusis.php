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
            $table->string('remarksadmin')->nullable();
            $table->string('statusbank')->nullable();
            $table->timestamp('statusbank_date')->nullable();
            $table->string('remarksbank')->nullable();
            $table->string('temp_limit')->nullable();
            $table->string('disburse_limit')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('distribusis', function (Blueprint $table) {
            //
            $table->dropColumn('remarksadmin');
            $table->dropColumn('statusbank');
            $table->dropColumn('statusbank_date');
            $table->dropColumn('remarksbank');
            $table->dropColumn('temp_limit');
            $table->dropColumn('disburse_limit');
        });
    }
};
