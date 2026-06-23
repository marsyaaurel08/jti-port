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
        Schema::table('user_promos', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropForeign(['promo_id']);
            $table->dropUnique(['user_id', 'promo_id']);
            
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('promo_id')->references('id')->on('promos')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_promos', function (Blueprint $table) {
            $table->unique(['user_id', 'promo_id']);
        });
    }
};
