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
        Schema::create('promos', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();                    // KODEPROMO123
            $table->string('name');                              // Nama Promo
            $table->text('description')->nullable();

            $table->enum('type', ['percentage', 'fixed'])->default('percentage');
            $table->decimal('value', 15, 2);                     // 10 = 10%, 50000 = Rp50.000

            $table->decimal('min_order', 15, 2)->default(0);     // Minimal belanja
            $table->integer('max_usage')->nullable();            // Kuota total promo (null = unlimited)
            $table->integer('max_usage_per_user')->default(1);   // Default 1 kali per user

            $table->dateTime('start_date');
            $table->dateTime('end_date');

            $table->boolean('is_active')->default(true);
            $table->boolean('for_all_products')->default(true);  // true = semua produk

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('promos');
    }
};
