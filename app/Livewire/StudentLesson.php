<?php

namespace App\Livewire;

use App\Contract\CourseStudentContract;
use App\Models\Chapter;
use App\Models\Course;
use App\Models\CourseProgress;
use App\Models\Enrollment;
use App\Models\Lesson;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]

class StudentLesson extends Component
{
    public $courseSlug = '';

    public $lessonSlug = '';

    public $course = null;

    public $lesson = null;

    public $chapters = [];

    public $completedLessonIds = [];

    public $progress = 0;

    public $previousLesson = null;

    public $nextLesson = null;

    public $enrollment = null;

    public $isLessonCompleted = false;

    protected CourseStudentContract $courseStudentService;

    public function __construct(CourseStudentContract $courseStudentService)
    {
        $this->courseStudentService = $courseStudentService;
    }

    #[Computed]
    public function user()
    {
        return Auth::user();
    }

    public function mount($courseSlug, $lessonSlug): void
    {
        $this->courseSlug = $courseSlug;
        $this->lessonSlug = $lessonSlug;
        $this->loadLesson();
    }

    public function loadLesson(): void
    {
        try {
            $user = Auth::user();

            // Find the course by slug
            $course = Course::where('slug', $this->courseSlug)
                ->where('status', 'published')
                ->firstOrFail();

            $this->course = $course->toArray();

            // Check if user is enrolled in the course
            $this->enrollment = Enrollment::where('user_id', $user->id)
                ->where('course_id', $course->id)
                ->first();

            if (! $this->enrollment) {
                session()->flash('error', 'Anda harus mendaftar kursus terlebih dahulu.');
                redirect()->route('courses.show', $this->courseSlug);

                return;
            }

            // Find the lesson by slug
            $this->lesson = Lesson::with('chapter')
                ->where('slug', $this->lessonSlug)
                ->whereHas('chapter', function ($query) use ($course) {
                    $query->where('course_id', $course->id);
                })
                ->firstOrFail();

            // Get all chapters with lessons for the sidebar
            $chapters = Chapter::with(['lessons' => function ($query) {
                $query->orderBy('sort_order');
            }])
                ->where('course_id', $course->id)
                ->orderBy('sort_order')
                ->get();

            $this->chapters = $chapters->map(fn ($chapter) => [
                'id' => $chapter->id,
                'title' => $chapter->title,
                'sort_order' => $chapter->sort_order,
                'lessons' => $chapter->lessons->toArray(),
            ])->toArray();

            // Get completed lesson IDs for the user
            $this->completedLessonIds = CourseProgress::where('enrollment_id', $this->enrollment->id)
                ->where('is_completed', true)
                ->pluck('lesson_id')
                ->toArray();

            // Calculate progress percentage
            $totalLessons = $course->total_lessons;
            $completedCount = count($this->completedLessonIds);
            $this->progress = $totalLessons > 0 ? round(($completedCount / $totalLessons) * 100) : 0;

            // Check if current lesson is completed
            $this->isLessonCompleted = in_array($this->lesson->id, $this->completedLessonIds);

            // Find previous and next lessons
            $allLessons = collect();
            foreach ($chapters as $chapter) {
                $allLessons = $allLessons->concat($chapter->lessons);
            }

            $currentLessonIndex = $allLessons->search(function ($item) {
                return $item->id === $this->lesson->id;
            });

            $this->previousLesson = $currentLessonIndex > 0 ? $allLessons[$currentLessonIndex - 1]->toArray() : null;
            $this->nextLesson = $currentLessonIndex < $allLessons->count() - 1 ? $allLessons[$currentLessonIndex + 1]->toArray() : null;

            // Update last accessed lesson
            $this->enrollment->update([
                'last_accessed_lesson_id' => $this->lesson->id,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to load lesson: '.$e->getMessage());
            session()->flash('error', 'Gagal memuat materi. Silakan coba lagi.');
        }
    }

    public function markAsCompleted(): void
    {
        try {
            $existingProgress = CourseProgress::where('enrollment_id', $this->enrollment->id)
                ->where('lesson_id', $this->lesson->id)
                ->first();

            if ($existingProgress) {
                $existingProgress->update(['is_completed' => true]);
            } else {
                CourseProgress::create([
                    'enrollment_id' => $this->enrollment->id,
                    'lesson_id' => $this->lesson->id,
                    'is_completed' => true,
                ]);
            }

            $this->isLessonCompleted = true;

            // Reload to update progress
            $this->loadLesson();

            session()->flash('success', 'Pelajaran berhasil ditandai sebagai selesai!');
        } catch (\Exception $e) {
            Log::error('Failed to mark lesson as completed: '.$e->getMessage());
            session()->flash('error', 'Gagal menandai pelajaran. Silakan coba lagi.');
        }
    }

    public function markAsIncomplete(): void
    {
        try {
            CourseProgress::where('enrollment_id', $this->enrollment->id)
                ->where('lesson_id', $this->lesson->id)
                ->update(['is_completed' => false]);

            $this->isLessonCompleted = false;
            $this->loadLesson();

            session()->flash('success', 'Pelajaran ditandai sebagai belum selesai.');
        } catch (\Exception $e) {
            Log::error('Failed to mark lesson as incomplete: '.$e->getMessage());
            session()->flash('error', 'Gagal mengubah status pelajaran. Silakan coba lagi.');
        }
    }

    public function render()
    {
        return view('livewire.student-lesson');
    }
}
