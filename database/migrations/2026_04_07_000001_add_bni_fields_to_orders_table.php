<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Add BNI Virtual Account fields
            $table->string('bni_transaction_id')->nullable()->after('payment_method')->comment('BNI Ecollection Transaction ID');
            $table->string('bni_va_number')->nullable()->after('bni_transaction_id')->comment('BNI Virtual Account Number');
            $table->dateTime('bni_va_expired_at')->nullable()->after('bni_va_number')->comment('BNI VA Expiration Time');

            // Add indexes for faster queries
            $table->index('bni_transaction_id');
            $table->index('bni_va_number');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex(['bni_transaction_id']);
            $table->dropIndex(['bni_va_number']);
            $table->dropColumn(['bni_transaction_id', 'bni_va_number', 'bni_va_expired_at']);
        });
    }
};
