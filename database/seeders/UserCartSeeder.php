<?php

namespace Database\Seeders;

use App\Models\UserCart;
use Illuminate\Database\Seeder;

/**
 * Run the database seeds.
 */
class UserCartSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        UserCart::factory()->count(5)->create();
    }
}
