<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Chapter;
use App\Models\Course;
use App\Models\CourseProgress;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create 3 users (or get existing)
        $users = [];
        for ($i = 1; $i <= 3; $i++) {
            $users[$i] = User::firstOrCreate(
                ['email' => "peserta{$i}@example.com"],
                [
                    'name' => "User {$i}",
                    'password' => Hash::make('password'),
                ]
            );
        }

        // Create 1 category (or get existing)
        $category = Category::firstOrCreate(
            ['slug' => 'programming'],
            [
                'name' => 'Programming',
            ]
        );

        // Course titles for 11 courses
        $courseTitles = [
            'Laravel Fundamentals',
            'React.js Mastery',
            'Vue.js Essentials',
            'Node.js Development',
            'Python for Beginners',
            'JavaScript Advanced',
            'TypeScript Deep Dive',
            'PHP Best Practices',
            'SQL Database Design',
            'API Development',
            'Web Security Basics',
        ];

        // Create 11 courses with 2 chapters and 5 lessons each
        $allCourses = [];
        foreach ($courseTitles as $index => $title) {
            $randomId = $index + 1;
            $course = Course::create([
                'category_id' => $category->id,
                'title' => $title,
                'slug' => strtolower(str_replace(' ', '-', $title)),
                'description' => "Learn the basics of {$title}",
                'price' => 99.99,
                'level' => 'beginner',
                'thumbnail_url' => "https://picsum.photos/400/200?random={$randomId}",
                'status' => 'published',
                'total_duration' => 6000, // 100 minutes in seconds
                'total_lessons' => 10,
                'avg_rating' => 4.5,
            ]);
            $allCourses[] = $course;

            // Create 2 chapters with 5 lessons each (total 10 lessons)
            for ($chapterNum = 1; $chapterNum <= 2; $chapterNum++) {
                $chapter = Chapter::create([
                    'course_id' => $course->id,
                    'title' => "Chapter {$chapterNum}: Getting Started",
                    'sort_order' => $chapterNum,
                ]);

                for ($lessonNum = 1; $lessonNum <= 5; $lessonNum++) {
                    $lessonCounter = (($chapterNum - 1) * 5) + $lessonNum;
                    Lesson::create([
                        'chapter_id' => $chapter->id,
                        'title' => "Lesson {$lessonCounter}: Introduction",
                        'type' => 'video',
                        'slug' => strtolower(str_replace(' ', '-', $title)) . "-lesson-{$lessonCounter}",
                        'content_url' => 'https://example.com/video.mp4',
                        'content_text' => 'Lesson content goes here...',
                        'duration_seconds' => 600, // 10 minutes
                        'is_free_preview' => false,
                        'sort_order' => $lessonNum,
                    ]);
                }
            }
        }

        // User 3 orders 6 courses (courses 1-6)
        $orderedCourses = array_slice($allCourses, 0, 6);

        // Calculate total amount
        $totalAmount = count($orderedCourses) * 99.99;

        // Create 1 order for user ID 3
        $order = Order::create([
            'user_id' => $users[3]->id,
            'invoice_number' => 'INV-' . time(),
            'subtotal' => $totalAmount,
            'discount_amount' => 0.00,
            'total_amount' => $totalAmount,
            'status' => 'paid',
            'payment_method' => 'credit_card',
            'payment_proof' => null,
            'paid_at' => now(),
        ]);

        // Create order items and enrollments
        // Course 1-5: Chapter 1 completed (5 lessons true), Chapter 2 not completed (5 lessons false)
        for ($i = 0; $i < 5; $i++) {
            $this->createEnrollment(
                $users[3],
                $orderedCourses[$i],
                $order,
                5, // completed_lesson_count (first 5 lessons)
                false // is_completed
            );
        }

        // Course 6: 100% completed (all 10 lessons, is_completed true)
        $this->createEnrollment(
            $users[3],
            $orderedCourses[5],
            $order,
            10, // completed_lesson_count
            true // is_completed
        );

        $this->command->info('Demo data seeded successfully!');
        $this->command->info('- Created 3 users');
        $this->command->info('- Created 1 category');
        $this->command->info('- Created 11 courses with 2 chapters and 10 lessons each');
        $this->command->info('- Created 1 order with 6 order items for user 3');
        $this->command->info('- Created 6 enrollments:');
        $this->command->info('  * 5 courses: 5 lessons completed (true), 5 lessons not completed (false), is_completed: false');
        $this->command->info('  * 1 course: All 10 lessons completed (is_completed: true, 100% complete)');
    }

    /**
     * Create enrollment with order item and course progress
     */
    private function createEnrollment($user, $course, $order, $completedLessonCount, $isCompleted)
    {
        // Create order item
        OrderItem::create([
            'order_id' => $order->id,
            'course_id' => $course->id,
            'class_id' => null,
            'price_at_purchase' => 99.99,
            'item_name' => $course->title,
        ]);

        // Get all lessons for this course
        $lessons = Lesson::whereHas('chapter', function ($query) use ($course) {
            $query->where('course_id', $course->id);
        })->orderBy('sort_order')->get();

        // Create enrollment
        $enrollment = Enrollment::create([
            'user_id' => $user->id,
            'course_id' => $course->id,
            'class_id' => null,
            'order_id' => $order->id,
            'enrolled_at' => now(),
            'expires_at' => now()->addYear(),
            'is_completed' => $isCompleted,
            'completed_at' => $isCompleted ? now() : null,
            'certificate_code' => $isCompleted ? 'CERT-' . strtoupper(uniqid()) : null,
            'certificate_url' => $isCompleted ? 'https://example.com/certificate.pdf' : null,
            'completed_lesson_count' => $completedLessonCount,
            'last_accessed_lesson_id' => $lessons[$completedLessonCount - 1]->id ?? $lessons[0]->id,
        ]);

        // Create course progress entries
        foreach ($lessons as $index => $lesson) {
            $isLessonCompleted = $index < $completedLessonCount;
            CourseProgress::create([
                'enrollment_id' => $enrollment->id,
                'lesson_id' => $lesson->id,
                'is_completed' => $isLessonCompleted,
                'last_watched_at' => $isLessonCompleted ? now()->subHours($completedLessonCount - $index) : null,
            ]);
        }
    }
}
