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

            // Rename kolom lama ke baru
            $table->renameColumn('bni_transaction_id', 'external_id');
            $table->renameColumn('bni_va_number', 'va_number');
            $table->renameColumn('bni_va_expired_at', 'va_expired_at');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {

            // rollback rename
            $table->renameColumn('external_id', 'bni_transaction_id');
            $table->renameColumn('va_number', 'bni_va_number');
            $table->renameColumn('va_expired_at', 'bni_va_expired_at');
        });

        Schema::table('orders', function (Blueprint $table) {

            // balik ke string
            $table->string('payment_method')
                ->nullable()
                ->change();
        });
    }
};
