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
        Schema::create('logs_self_healing', function (Blueprint $table) {
            $table->id();

            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->string('invoice_number');               // denormalized untuk query cepat
            $table->string('payment_gateway');              // 'bni' | 'midtrans'

            $table->unsignedTinyInteger('attempt');         // nomor percobaan ke-N
            $table->enum('result', [
                'paid',         // gateway konfirmasi sudah bayar
                'pending',      // gateway konfirmasi belum bayar
                'error',        // panggilan API gagal / timeout
                'skipped',      // order sudah final saat dicek
            ]);

            $table->text('error_message')->nullable();      // isi jika result = error
            $table->json('raw_response')->nullable();       // raw response dari gateway

            $table->timestamps();

            $table->index(['invoice_number', 'attempt']);
            $table->index(['result', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('logs_self_healing');
    }
};
