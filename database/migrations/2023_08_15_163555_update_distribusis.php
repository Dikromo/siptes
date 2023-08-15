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
            $table->string('limit')->nullable();
            $table->string('loan_apply')->nullable();
            $table->string('mob')->nullable();
            $table->string('bank_penerbit')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('distribusis', function (Blueprint $table) {
            //
            $table->dropColumn('limit');
            $table->dropColumn('loan_apply');
            $table->dropColumn('mob');
            $table->dropColumn('bank_penerbit');
        });
    }
};
