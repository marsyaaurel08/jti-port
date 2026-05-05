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
        Schema::create('reconciliation_logs', function (Blueprint $table) {
            $table->id();

            $table->foreignId('order_id')->constrained()->cascadeOnDelete();

            $table->integer('attempt_number');
            $table->enum('trigger_type', [
                'scheduler',
                'user_action',
                'payment_proof',
                'manual'
            ]);

            $table->enum('result', [
                'paid',
                'not_found',
                'pending',
                'failed'
            ]);

            $table->text('response_message')->nullable();

            $table->timestamp('checked_at');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reconciliation_logs');
    }
};
