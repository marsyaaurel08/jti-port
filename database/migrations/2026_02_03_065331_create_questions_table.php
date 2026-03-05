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
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quiz_id')->constrained()->onDelete('cascade');
            $table->enum('type', [
                'single_choice',
                'multiple_choice',
                'boolean',
                'essay',
                'matching',
                'drag_drop_sorting',
                'drag_drop_zones',
                'fill_in_blank'
            ]);
            $table->text('question_text');
            $table->json('question_config')->nullable(); // Options, media, settings (NO answers)
            $table->json('answer_key')->nullable(); // Answer key only
            $table->text('explanation')->nullable(); // Shown after answering
            $table->text('hint')->nullable(); // Optional hint
            $table->integer('score_weight')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('questions');
    }
};
