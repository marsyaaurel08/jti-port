<?php

namespace Database\Seeders;

use App\Models\CourseProgress;
use Illuminate\Database\Seeder;

/**
 * Run the database seeds.
 */
class CourseProgressSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        CourseProgress::factory()->count(25)->create();
    }
}
