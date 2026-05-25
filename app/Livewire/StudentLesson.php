<?php

namespace App\Livewire;

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

#[Layout('layouts.app', ['showFooter' => false])]

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

    public $nextAccessibleLessonId = null;

    public $allLessonsCompleted = false;

    public $allLessons = [];

    #[Computed]
    public function user()
    {
        return Auth::user();
    }

    #[Computed]
    public function videoEmbedUrl(): ?string
    {
        if (!$this->lesson || !$this->lesson->content_url) {
            return null;
        }

        $url = $this->lesson->content_url;

        // Convert YouTube URL to embed format
        if (preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/|youtube\.com\/embed\/)([a-zA-Z0-9_-]+)/', $url, $matches)) {
            return 'https://www.youtube.com/embed/' . $matches[1];
        }

        return $url;
    }

    public function mount($courseSlug, $lessonSlug): void
    {
        $this->courseSlug = $courseSlug;
        $this->lessonSlug = $lessonSlug;
        $this->loadLesson();
    }

    public function loadLesson(): void
    {
        // Log::info('loadLesson called', [
        //     'courseSlug' => $this->courseSlug,
        //     'lessonSlug' => $this->lessonSlug,
        // ]);

        try {
            $user = Auth::user();

            // Find the course by slug
            $course = Course::withTrashed()
                ->where('slug', $this->courseSlug)
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

            // Find the lesson by slug - OPTIMIZED: Use join instead of whereHas
            $this->lesson = Lesson::join('chapters', 'lessons.chapter_id', '=', 'chapters.id')
                ->where('lessons.slug', $this->lessonSlug)
                ->where('chapters.course_id', $course->id)
                ->select('lessons.*')
                ->firstOrFail();

            // Load chapter relationship
            $this->lesson->load('chapter');

            // Load quiz if lesson type is quiz
            if ($this->lesson->type === 'quiz') {
                $this->lesson->load('quiz');
            }

            // Get all chapters with lessons for the sidebar - OPTIMIZED: Select only needed fields
            $chapters = Chapter::with(['lessons' => function ($query) {
                $query->select('id', 'chapter_id', 'title', 'slug', 'sort_order')
                    ->orderBy('sort_order');
            }])
                ->select('id', 'course_id', 'title', 'sort_order')
                ->where('course_id', $course->id)
                ->orderBy('sort_order')
                ->get();

            // Build chapters array and collect all lessons for navigation
            $this->allLessons = [];
            $this->chapters = [];

            foreach ($chapters as $chapter) {
                $chapterData = [
                    'id' => $chapter->id,
                    'title' => $chapter->title,
                    'sort_order' => $chapter->sort_order,
                    'lessons' => $chapter->lessons->toArray(),
                ];
                $this->chapters[] = $chapterData;

                // Collect lessons for navigation
                foreach ($chapter->lessons as $lesson) {
                    $this->allLessons[] = $lesson;
                }
            }

            // Get completed lesson IDs for the user - OPTIMIZED: Using composite index
            $this->completedLessonIds = CourseProgress::where('enrollment_id', $this->enrollment->id)
                ->where('is_completed', true)
                ->pluck('lesson_id')
                ->toArray();

            // Convert to array for faster lookups
            $completedIdsSet = array_flip($this->completedLessonIds);

            // Calculate progress percentage
            $totalLessons = $course->total_lessons;
            $completedCount = count($this->completedLessonIds);
            $this->progress = $totalLessons > 0 ? round(($completedCount / $totalLessons) * 100) : 0;

            // Check if current lesson is completed - OPTIMIZED: Use isset instead of in_array
            $this->isLessonCompleted = isset($completedIdsSet[$this->lesson->id]);

            // Find previous and next lessons - OPTIMIZED: Use array search instead of collection
            $currentLessonIndex = -1;
            foreach ($this->allLessons as $index => $lesson) {
                if ($lesson->id === $this->lesson->id) {
                    $currentLessonIndex = $index;
                    break;
                }
            }

            if ($currentLessonIndex > 0) {
                $this->previousLesson = $this->allLessons[$currentLessonIndex - 1]->toArray();
            }

            if ($currentLessonIndex >= 0 && $currentLessonIndex < count($this->allLessons) - 1) {
                $this->nextLesson = $this->allLessons[$currentLessonIndex + 1]->toArray();
            }

            // Pre-compute lesson accessibility for blade view - OPTIMIZATION
            $this->allLessonsCompleted = $completedCount >= count($this->allLessons);

            // Find the first uncompleted lesson
            $this->nextAccessibleLessonId = null;
            if (!$this->allLessonsCompleted) {
                foreach ($this->allLessons as $lesson) {
                    if (!isset($completedIdsSet[$lesson->id])) {
                        $this->nextAccessibleLessonId = $lesson->id;
                        break;
                    }
                }
            }

            // Log::info('Lesson navigation determined', [
            //     'current_lesson_id' => $this->lesson->id,
            //     'current_lesson_slug' => $this->lesson->slug,
            //     'has_previous' => $this->previousLesson !== null,
            //     'has_next' => $this->nextLesson !== null,
            //     'is_lesson_completed' => $this->isLessonCompleted,
            //     'next_lesson_slug' => $this->nextLesson['slug'] ?? null,
            // ]);

            // Auto-mark last lesson as completed when there's no next lesson
            if ($this->nextLesson === null && !$this->isLessonCompleted) {
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

                // Recalculate progress percentage
                $completedCount = count($this->completedLessonIds) + 1;
                $this->progress = $totalLessons > 0 ? round(($completedCount / $totalLessons) * 100) : 0;

                // Update enrollment completed lesson count and completion status
                $updateData = [
                    'completed_lesson_count' => $completedCount,
                ];

                if ($totalLessons > 0 && $completedCount >= $totalLessons && !$this->enrollment->is_completed) {
                    $updateData['is_completed'] = true;
                    $updateData['completed_at'] = now();
                }

                $this->enrollment->update($updateData);
            }

            // Update last accessed lesson
            $this->enrollment->update([
                'last_accessed_lesson_id' => $this->lesson->id,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to load lesson: ' . $e->getMessage());
            session()->flash('error', 'Gagal memuat materi. Silakan coba lagi.');
        }
    }


    public function markAsCompletedAndNext(): void
    {
        Log::info('markAsCompletedAndNext called', [
            'lesson_id' => $this->lesson->id,
            'has_next_lesson' => $this->nextLesson !== null,
            'next_lesson_slug' => $this->nextLesson['slug'] ?? null,
            'is_lesson_completed' => $this->isLessonCompleted,
        ]);

        try {
            $isNewCompletion = false;

            // Mark current lesson as completed
            $existingProgress = CourseProgress::where('enrollment_id', $this->enrollment->id)
                ->where('lesson_id', $this->lesson->id)
                ->first();

            if ($existingProgress) {
                if (!$existingProgress->is_completed) {
                    $existingProgress->update(['is_completed' => true]);
                    $isNewCompletion = true;
                }
            } else {
                CourseProgress::create([
                    'enrollment_id' => $this->enrollment->id,
                    'lesson_id' => $this->lesson->id,
                    'is_completed' => true,
                ]);
                $isNewCompletion = true;
            }

            if ($isNewCompletion) {
                $completedCount = CourseProgress::where('enrollment_id', $this->enrollment->id)
                    ->where('is_completed', true)
                    ->count();

                $totalLessons = $this->course['total_lessons'] ?? 0;

                $updateData = [
                    'completed_lesson_count' => $completedCount,
                ];

                if ($totalLessons > 0 && $completedCount >= $totalLessons && !$this->enrollment->is_completed) {
                    $updateData['is_completed'] = true;
                    $updateData['completed_at'] = now();
                }

                $this->enrollment->update($updateData);
            }

            // Log::info('Lesson marked as completed', ['lesson_id' => $this->lesson->id]);

            // Navigate to next lesson
            if ($this->nextLesson) {
                Log::info('Redirecting to next lesson', ['slug' => $this->nextLesson['slug']]);
                $this->redirect(route('student.lesson', [$this->courseSlug, $this->nextLesson['slug']]));
            } else {
                Log::warning('No next lesson found', ['lesson_id' => $this->lesson->id]);
            }
        } catch (\Exception $e) {
            Log::error('Failed to mark lesson as completed and navigate: ' . $e->getMessage());
            session()->flash('error', 'Gagal menandai pelajaran. Silakan coba lagi.');
        }
    }

    public function render()
    {
        return view('livewire.student-lesson');
    }
}
