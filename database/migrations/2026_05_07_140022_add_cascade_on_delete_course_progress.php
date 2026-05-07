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
        Schema::table('course_progress', function (Blueprint $table) {

            // hapus foreign key lama
            $table->dropForeign(['enrollment_id']);

            // buat ulang dengan cascade delete
            $table->foreign('enrollment_id')
                ->references('id')
                ->on('enrollments')
                ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('course_progress', function (Blueprint $table) {

            // hapus foreign key cascade
            $table->dropForeign(['enrollment_id']);

            // kembalikan seperti semula
            $table->foreign('enrollment_id')
                ->references('id')
                ->on('enrollments');
        });
    }
};
