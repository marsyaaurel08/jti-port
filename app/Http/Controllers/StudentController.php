<?php

namespace App\Http\Controllers;

use App\Contract\CourseStudentContract;
use App\Models\{Course, Lesson, Chapter, Enrollment, CourseProgress};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class StudentController extends Controller
{

    protected CourseStudentContract $courseStudentService;

    public function __construct(CourseStudentContract $courseStudentService)
    {
        $this->courseStudentService = $courseStudentService;
    }

    public function myCourses(Request $request)
    {
        try {
            $user = Auth::user();
            $filter = $request->get('filter', 'all');
            // For demonstration, using a fixed user ID of 3
            $courses = $this->courseStudentService->getStudentCourses(3, $filter);
            return view('pages.user.mycourse', compact('courses'));
        } catch (\Exception $e) {
            Log::error('Failed to load student courses: ' . $e->getMessage());
            return back()->with('error', 'Gagal memuat kursus. Silakan coba lagi.');
        }
    }

    public function lesson(Request $request, string $courseSlug, string $lessonSlug)
    {
        try {
            $user = Auth::user();

            // Find the course by slug
            $course = Course::where('slug', $courseSlug)
                ->where('status', 'published')
                ->firstOrFail();

            // Check if user is enrolled in the course
            $enrollment = Enrollment::where('user_id', $user->id)
                ->where('course_id', $course->id)
                ->first();

            if (!$enrollment) {
                return redirect()->route('courses.show', $courseSlug)
                    ->with('error', 'Anda harus mendaftar kursus terlebih dahulu.');
            }

            // Find the lesson by slug
            $lesson = Lesson::with('chapter')
                ->where('slug', $lessonSlug)
                ->whereHas('chapter', function ($query) use ($course) {
                    $query->where('course_id', $course->id);
                })
                ->firstOrFail();

            // Get all chapters with lessons for the sidebar
            $chapters = Chapter::with(['lessons' => function ($query) use ($enrollment) {
                $query->orderBy('sort_order');
            }])
                ->where('course_id', $course->id)
                ->orderBy('sort_order')
                ->get();

            // Get completed lesson IDs for the user
            $completedLessonIds = CourseProgress::where('enrollment_id', $enrollment->id)
                ->where('is_completed', true)
                ->pluck('lesson_id')
                ->toArray();

            // Calculate progress percentage
            $totalLessons = $course->total_lessons;
            $completedCount = count($completedLessonIds);
            $progress = $totalLessons > 0 ? round(($completedCount / $totalLessons) * 100) : 0;

            // Find previous and next lessons
            $allLessons = collect();
            foreach ($chapters as $chapter) {
                $allLessons = $allLessons->concat($chapter->lessons);
            }

            $currentLessonIndex = $allLessons->search(function ($item) use ($lesson) {
                return $item->id === $lesson->id;
            });

            $previousLesson = $currentLessonIndex > 0 ? $allLessons[$currentLessonIndex - 1] : null;
            $nextLesson = $currentLessonIndex < $allLessons->count() - 1 ? $allLessons[$currentLessonIndex + 1] : null;

            // Update last accessed lesson
            $enrollment->update([
                'last_accessed_lesson_id' => $lesson->id
            ]);

            return view('pages.user.lesson', compact(
                'course',
                'lesson',
                'chapters',
                'completedLessonIds',
                'progress',
                'previousLesson',
                'nextLesson',
                'user'
            ));
        } catch (\Exception $e) {
            Log::error('Failed to load lesson: ' . $e->getMessage());
            return back()->with('error', 'Gagal memuat materi. Silakan coba lagi.');
        }
    }
}
