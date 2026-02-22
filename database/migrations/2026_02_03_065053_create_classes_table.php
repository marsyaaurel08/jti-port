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
        Schema::create('classes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained();
            $table->foreignId('category_id')->constrained();
            $table->foreignId('instructor_id')->constrained('users');
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('thumbnail_url')->nullable();
            $table->text('description')->nullable();
            $table->enum('level', ['beginner', 'intermediate', 'advanced']);
            $table->decimal('price', 12, 2)->default(0);
            $table->dateTime('start_date');
            $table->dateTime('end_date');
            $table->integer('max_capacity');
            $table->integer('current_enrolled')->default(0);
            $table->text('meeting_link')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('classes');
    }
};
