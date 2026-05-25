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
        Schema::table('lessons', function (Blueprint $table) {
            // Add index on slug for fast lesson lookup
            $table->index('slug', 'idx_lessons_slug');

            // Add composite index on chapter_id and sort_order for ordering lessons within chapters
            $table->index(['chapter_id', 'sort_order'], 'idx_lessons_chapter_sort');
        });

        Schema::table('chapters', function (Blueprint $table) {
            // Add composite index on course_id and sort_order for ordering chapters within courses
            $table->index(['course_id', 'sort_order'], 'idx_chapters_course_sort');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lessons', function (Blueprint $table) {
            $table->dropIndex('idx_lessons_slug');
            $table->dropIndex('idx_lessons_chapter_sort');
        });

        Schema::table('chapters', function (Blueprint $table) {
            $table->dropIndex('idx_chapters_course_sort');
        });
    }
};
