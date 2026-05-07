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
            $table->string('gateway'); // 'bni' atau 'midtrans'
            $table->string('action'); // misal: 'scheduled_inquiry', 'manual_sync_by_admin'
            $table->json('request_payload')->nullable(); // Data yang dikirim saat inquiry
            $table->json('response_payload')->nullable(); // Respon dari BNI/Midtrans
            $table->enum('status', ['success', 'failed', 'unresolved']); // success: sinkron, unresolved: API error/timeout
            $table->text('notes')->nullable(); // Catatan tambahan, misal: "Max retry reached"
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
