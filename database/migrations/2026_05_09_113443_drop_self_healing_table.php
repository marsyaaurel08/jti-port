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
        Schema::dropIfExists('logs_self_healing');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('logs_self_healing', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('order_id')->nullable();
            $table->string('order_type')->nullable();
            $table->string('invoice_number')->nullable();
            $table->string('status_before')->nullable();
            $table->string('status_after')->nullable();
            $table->text('message')->nullable();
            $table->json('payload')->nullable();
            $table->timestamps();

            // Index untuk pencarian cepat berdasarkan order_id dan order_type
            $table->index(['order_id', 'order_type']);
        });
    }
};
