<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\CourseClass;
use App\Models\User;
use Illuminate\Database\Seeder;

class CourseClassSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create 5 instructor users
        $instructors = [];
        for ($i = 1; $i <= 5; $i++) {
            $instructors[$i] = User::firstOrCreate(
                ['email' => "instructor{$i}@example.com"],
                [
                    'name' => "Instructor {$i}",
                    'password' => bcrypt('password'),
                    'role' => 'pengajar',
                    'email_verified_at' => now(),
                ]
            );
            $instructors[$i]->syncRoles(['pengajar']);
        }

        // Get first 10 published courses
        $courses = Course::where('status', 'published')->limit(10)->get();

        if ($courses->isEmpty()) {
            $this->command->warn('No published courses found. Please run DemoSeeder first.');

            return;
        }

        // Create 1 class per course (total 10 classes)
        foreach ($courses as $index => $course) {
            $instructorKey = ($index % 5) + 1;

            $startDate = now()->addDays(rand(1, 30));
            $endDate = (clone $startDate)->addDays(rand(30, 90));
            $randomId = $index + 1;

            CourseClass::create([
                'course_id' => $course->id,
                'category_id' => $course->category_id,
                'instructor_id' => $instructors[$instructorKey]->id,
                'thumbnail_url' => "https://picsum.photos/400/200?random={$randomId}",
                'description' => "Class description for {$course->title}",
                'level' => $course->level,
                'slug' => strtolower(str_replace(' ', '-', $course->title)),
                'title' => $course->title,
                'price' => $course->price,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'max_capacity' => rand(20, 50),
                'current_enrolled' => rand(5, 30),
                'meeting_link' => 'https://meet.google.com/abc-defg-hij',
                'is_active' => true,
            ]);
        }

        $totalClasses = CourseClass::count();

        $this->command->info('CourseClass seeded successfully!');
        $this->command->info("- Created {$totalClasses} classes (1 per course)");
        $this->command->info('- All classes are active');
        $this->command->info('- Created 5 instructor users');
    }
}
