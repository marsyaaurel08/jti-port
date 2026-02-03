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
        Schema::create('enrollments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->foreignId('course_id')->nullable()->constrained();
            $table->foreignId('class_id')->nullable()->constrained();
            $table->foreignId('order_id')->nullable()->constrained();
            $table->dateTime('enrolled_at');
            $table->dateTime('expires_at')->nullable();
            $table->boolean('is_completed')->default(false);
            $table->dateTime('completed_at')->nullable();
            $table->string('certificate_code')->nullable();
            $table->string('certificate_url')->nullable();
            $table->integer('completed_lesson_count')->default(0);
            $table->foreignId('last_accessed_lesson_id')->nullable()->constrained('lessons');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('enrollments');
    }
};
