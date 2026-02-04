<?php

namespace Database\Seeders;

use App\Models\CourseClass;
use Illuminate\Database\Seeder;

/**
 * Run the database seeds.
 */
class CourseClassSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        CourseClass::factory()->count(10)->create();
    }
}
