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
        Schema::table('questions', function (Blueprint $table) {
            $table->enum('type', [
                'single_choice',
                'multiple_choice',
                'boolean',
                'essay',
                'matching',
                'drag_drop_sorting',
                'drag_drop_zones',
                'fill_in_blank',
                'drop_down'
            ])->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('questions', function (Blueprint $table) {
            $table->enum('type', [
                'single_choice',
                'multiple_choice',
                'boolean',
                'essay',
                'matching',
                'drag_drop_sorting',
                'drag_drop_zones',
                'fill_in_blank'
            ])->change();
        });
    }
};
