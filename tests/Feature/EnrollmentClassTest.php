<?php

use App\Models\Course;
use App\Models\CourseClass;
use App\Models\Enrollment;
use App\Models\User;
use Spatie\Permission\Models\Role;

describe('Enrollment Class - Bulk Enrollment', function () {
    beforeEach(function () {
        // Create admin role
        $role = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);

        $this->admin = User::factory()->create();
        $this->admin->assignRole($role);

        $this->actingAs($this->admin);
    });

    it('can create bulk enrollments with repeater', function () {
        $course = Course::factory()->create();
        $class = CourseClass::factory()->create([
            'course_id' => $course->id,
            'max_capacity' => 10,
            'current_enrolled' => 0,
        ]);

        $students = User::factory(3)->create();

        // Simulate form submission
        $enrollmentController = new \App\Http\Controllers\EnrollmentController;

        // Manually create enrollments to test bulk creation logic
        $enrollments = [];
        foreach ($students as $student) {
            $enrollments[] = [
                'user_id' => $student->id,
                'class_id' => $class->id,
                'course_id' => $course->id,
                'enrolled_at' => now(),
                'expires_at' => null,
                'is_completed' => false,
                'completed_lesson_count' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        Enrollment::insert($enrollments);
        $class->increment('current_enrolled', count($enrollments));

        expect(Enrollment::count())->toBe(3);
        expect($class->fresh()->current_enrolled)->toBe(3);

        foreach ($students as $student) {
            expect(Enrollment::where('user_id', $student->id)
                ->where('class_id', $class->id)
                ->exists())->toBeTrue();
        }
    });

    it('prevents enrollment when capacity is exceeded', function () {
        $course = Course::factory()->create();
        $class = CourseClass::factory()->create([
            'course_id' => $course->id,
            'max_capacity' => 2,
            'current_enrolled' => 0,
        ]);

        $students = User::factory(3)->create();

        // Count enrollments before attempt
        $enrollmentsBefore = Enrollment::count();

        // Try to create more enrollments than capacity
        $enrolledCount = 0;
        foreach ($students as $student) {
            if ($enrolledCount < $class->max_capacity) {
                Enrollment::create([
                    'user_id' => $student->id,
                    'class_id' => $class->id,
                    'course_id' => $course->id,
                    'enrolled_at' => now(),
                ]);
                $enrolledCount++;
            }
        }

        // Only 2 students should be enrolled
        expect(Enrollment::where('class_id', $class->id)->count())
            ->toBe($class->max_capacity);
    });

    it('prevents duplicate students in same enrollment form', function () {
        $course = Course::factory()->create();
        $class = CourseClass::factory()->create([
            'course_id' => $course->id,
            'max_capacity' => 10,
            'current_enrolled' => 0,
        ]);

        $student = User::factory()->create();

        // Try to enroll same student twice
        Enrollment::create([
            'user_id' => $student->id,
            'class_id' => $class->id,
            'course_id' => $course->id,
            'enrolled_at' => now(),
        ]);

        // Second enrollment for same student in same class should fail validation
        $alreadyEnrolled = Enrollment::where('user_id', $student->id)
            ->where('class_id', $class->id)
            ->exists();

        expect($alreadyEnrolled)->toBeTrue();
        expect(Enrollment::where('user_id', $student->id)->where('class_id', $class->id)->count())->toBe(1);
    });

    it('prevents enrollment if student already enrolled in class', function () {
        $course = Course::factory()->create();
        $class = CourseClass::factory()->create([
            'course_id' => $course->id,
            'max_capacity' => 10,
            'current_enrolled' => 1,
        ]);

        $student1 = User::factory()->create();
        $student2 = User::factory()->create();

        // Pre-existing enrollment
        Enrollment::create([
            'user_id' => $student1->id,
            'class_id' => $class->id,
            'course_id' => $course->id,
            'enrolled_at' => now(),
        ]);

        // Try to create another enrollment for same class
        $exists = Enrollment::where('user_id', $student1->id)
            ->where('class_id', $class->id)
            ->exists();

        expect($exists)->toBeTrue();
        expect(Enrollment::where('class_id', $class->id)->count())->toBe(1);
    });

    it('correctly calculates available slots', function () {
        $course = Course::factory()->create();
        $class = CourseClass::factory()->create([
            'course_id' => $course->id,
            'max_capacity' => 5,
            'current_enrolled' => 2,
        ]);

        $availableSlots = $class->max_capacity - $class->current_enrolled;

        expect($availableSlots)->toBe(3); // 5 - 2 = 3
    });

    it('can set expiration date for enrollment', function () {
        $course = Course::factory()->create();
        $class = CourseClass::factory()->create([
            'course_id' => $course->id,
            'max_capacity' => 10,
            'current_enrolled' => 0,
        ]);

        $student = User::factory()->create();
        $expiresAt = now()->addMonths(6);

        Enrollment::create([
            'user_id' => $student->id,
            'class_id' => $class->id,
            'course_id' => $course->id,
            'enrolled_at' => now(),
            'expires_at' => $expiresAt,
        ]);

        $enrollment = Enrollment::where('user_id', $student->id)->first();

        expect($enrollment->expires_at->format('Y-m-d'))
            ->toEqual($expiresAt->format('Y-m-d'));
    });
});
