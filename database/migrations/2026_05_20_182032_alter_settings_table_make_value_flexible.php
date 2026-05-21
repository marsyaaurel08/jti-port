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
        Schema::table('settings', function (Blueprint $table) {
            // Hapus enum lama
            $table->dropColumn('value');
        });

        Schema::table('settings', function (Blueprint $table) {
            // Tambahan struktur baru
            $table->string('group')->default('general')->after('id');
            $table->longText('value')->nullable()->after('key');
            $table->string('type')->default('text')->after('value');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn([
                'group',
                'type',
                'value',
            ]);
        });

        Schema::table('settings', function (Blueprint $table) {
            $table->enum('value', ['midtrans', 'bni'])
                ->default('midtrans')
                ->after('key');
        });
    }
};
