<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::rename('user_charts', 'user_carts');
    }

    public function down(): void
    {
        Schema::rename('user_carts', 'user_charts');
    }
};
