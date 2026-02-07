<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory(10)->create();

        User::factory()->create([
            'name' => 'admin',
            'email' => 'admin@admin.com',
        ]);

        $this->call([
            CategorySeeder::class,
            CourseSeeder::class,
            CourseClassSeeder::class,
            ChapterSeeder::class,
            LessonSeeder::class,
            OrderSeeder::class,
            OrderItemSeeder::class,
            EnrollmentSeeder::class,
            QuizSeeder::class,
            QuestionSeeder::class,
            CourseProgressSeeder::class,
            ReviewSeeder::class,

        ]);
    }
}
