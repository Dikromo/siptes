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
            $table->string('refferal')->nullable();
            $table->string('salescode')->nullable();
            $table->timestamp('join_date')->nullable();
            $table->timestamp('resign_date')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            //
            $table->dropColumn('refferal');
            $table->dropColumn('salescode');
            $table->dropColumn('join_date');
            $table->dropColumn('resign_date');
        });
    }
};
