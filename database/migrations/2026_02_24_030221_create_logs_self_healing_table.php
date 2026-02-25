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
        Schema::create('logs_self_healing', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->enum('order_type', ['course', 'class']);
            $table->string('invoice_number')->index(); // Tambahkan index untuk pencarian cepat

            // Detail teknis untuk debugging
            $table->string('status_before')->nullable();
            $table->string('status_after')->nullable();
            $table->text('message');
            $table->json('payload')->nullable(); // Menyimpan data mentah jika diperlukan

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('logs_self_healing');
    }
};
