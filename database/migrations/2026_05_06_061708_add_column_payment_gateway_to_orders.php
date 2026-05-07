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
        Schema::table('orders', function (Blueprint $table) {
            $table->enum('payment_gateway', ['bni', 'midtrans', 'manual'])
                ->nullable();
            $table->string('snap_token')->nullable()->after('payment_gateway');
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('payment_gateway');
            $table->dropColumn('snap_token');
            $table->dropSoftDeletes();
        });
    }
};
