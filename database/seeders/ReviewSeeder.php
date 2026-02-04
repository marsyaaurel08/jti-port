<?php

namespace Database\Seeders;

use App\Models\Review;
use Illuminate\Database\Seeder;

/**
 * Run the database seeds.
 */
class ReviewSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Review::factory()->count(10)->create();
    }
}
