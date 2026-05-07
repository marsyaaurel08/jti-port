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
        Schema::create('payment_webhooks', function (Blueprint $table) {
            $table->id();
            $table->string('payment_gateway'); // 'bni' atau 'midtrans'
            $table->string('order_invoice_number')->nullable(); // ID Order / Invoice Anda
            $table->string('external_id')->nullable(); // ID Transaksi dari Midtrans/BNI
            $table->json('payload'); // Simpan seluruh data JSON asli dari gateway di sini
            $table->enum('status', ['pending', 'processed', 'failed'])->default('pending');
            $table->text('error_message')->nullable(); // Jika gagal diproses oleh sistem Anda
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_webhooks');
    }
};
