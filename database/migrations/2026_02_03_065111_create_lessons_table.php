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
        Schema::create('lessons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('chapter_id')->constrained();
            $table->string('title');
            $table->enum('type', ['video', 'text', 'quiz', 'assignment']);
            $table->string('slug');
            $table->text('content_url')->nullable();
            $table->longText('content_text')->nullable();
            $table->integer('duration_seconds')->default(0);
            $table->boolean('is_free_preview')->default(false);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lessons');
    }
};
