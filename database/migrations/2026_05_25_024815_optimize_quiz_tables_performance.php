<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Performance optimization: Add indexes to quiz-related tables for faster queries
     */
    public function up(): void
    {
        // Add composite index for quiz_answers table
        // This optimizes queries that fetch answers by attempt and question
        Schema::table('quiz_answers', function (Blueprint $table) {
            $table->index(['quiz_attempt_id', 'question_id'], 'idx_quiz_attempt_question');
            $table->index('quiz_attempt_id', 'idx_quiz_attempt_id');
        });

        // Add composite index for quiz_attempts table
        // This optimizes queries that find active attempts by user and quiz
        Schema::table('quiz_attempts', function (Blueprint $table) {
            $table->index(['user_id', 'quiz_id', 'finished_at'], 'idx_user_quiz_finished');
            $table->index(['user_id', 'quiz_id'], 'idx_user_quiz');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove indexes from quiz_answers table
        Schema::table('quiz_answers', function (Blueprint $table) {
            $table->dropIndex('idx_quiz_attempt_question');
            $table->dropIndex('idx_quiz_attempt_id');
        });

        // Remove indexes from quiz_attempts table
        Schema::table('quiz_attempts', function (Blueprint $table) {
            $table->dropIndex('idx_user_quiz_finished');
            $table->dropIndex('idx_user_quiz');
        });
    }
};
