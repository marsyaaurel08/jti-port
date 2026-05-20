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
        Schema::create('user_promos', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained('users')
                ->onDelete('cascade');

            $table->foreignId('promo_id')
                ->constrained('promos')
                ->onDelete('cascade');

            $table->foreignId('order_id')
                ->nullable()
                ->constrained('orders')
                ->onDelete('set null');

            $table->decimal('discount_amount', 15, 2);           // Jumlah diskon yang didapat

            $table->timestamps();
            $table->softDeletes();

            // Penting! Mencegah user pakai promo yang sama 2x
            $table->unique(['user_id', 'promo_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_promos');
    }
};
