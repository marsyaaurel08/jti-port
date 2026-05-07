<?php

namespace App\Livewire;

use App\Models\Chapter;
use App\Models\Course;
use App\Models\CourseClass;
use App\Models\CourseProgress;
use App\Models\Enrollment;
use App\Models\Lesson;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app', ['showFooter' => false])]
class StudentClassLesson extends Component
{
    public $classSlug = '';
    public $instructor = null;
    public $zoomLink = null;
    public $lessonSlug = '';
    public $course = null;
    public $class = null;
    public $chapters = [];
    public $lesson = null;
    public $lessons = [];
    public $completedLessonIds = [];
    public $progress = 0;
    public $previousLesson = null;
    public $nextLesson = null;
    public $enrollment = null;
    public $isLessonCompleted = false;

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

        if (preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/|youtube\.com\/embed\/)([a-zA-Z0-9_-]+)/', $url, $matches)) {
            return 'https://www.youtube.com/embed/' . $matches[1];
        }

        return $url;
    }

    public function mount($classSlug, $lessonSlug): void
    {
        $this->classSlug = $classSlug;
        $this->lessonSlug = $lessonSlug;
        $this->loadLesson();
    }

    public function loadLesson(): void
    {
        try {
            $user = Auth::user();

            // 1. Cari kelas (CourseClass) berdasarkan slug
            $class = CourseClass::where('slug', $this->classSlug)
                ->where('is_active', true)
                ->firstOrFail();

            $this->class = $class->toArray();
            $this->instructor = $class->instructor ? $class->instructor->only(['id', 'name', 'email']) : null;
            $this->zoomLink = $class->meeting_link;

            // 2. Ambil course dari relasi kelas
            $course = $class->course;
            if (!$course || $course->status !== 'published') {
                throw new \Exception('Course not found or not published');
            }
            $this->course = $course->toArray();

            // 3. Cek enrollment (dengan order_id sebagai pembeda)
            $this->enrollment = Enrollment::where('user_id', $user->id)
                ->where('course_id', $course->id)
                ->where('class_id', $class->id)
                ->whereHas('order', function ($query) {
                    $query->whereIn('status', ['paid', 'completed']);
                })
                ->latest('id')
                ->first();

            if (!$this->enrollment) {
                session()->flash('error', 'Anda tidak memiliki akses ke kelas ini.');
                redirect()->route('courses.show', $course->slug);
                return;
            }

            // 4. Cari lesson (sama seperti sebelumnya)
            $this->lesson = Lesson::with('chapter')
                ->where('slug', $this->lessonSlug)
                ->whereHas('chapter', function ($query) use ($course) {
                    $query->where('course_id', $course->id);
                })
                ->firstOrFail();

            if ($this->lesson->type === 'quiz') {
                $this->lesson->load('quiz');
            }

            // 5. Ambil chapters & lessons untuk sidebar
            $chapters = Chapter::with(['lessons' => function ($query) {
                $query->orderBy('sort_order');
            }])
                ->where('course_id', $course->id)
                ->orderBy('sort_order')
                ->get();

            $this->chapters = $chapters->map(fn($chapter) => [
                'id' => $chapter->id,
                'title' => $chapter->title,
                'sort_order' => $chapter->sort_order,
                'lessons' => $chapter->lessons->toArray(),
            ])->toArray();

            // 6. Progress
            $this->completedLessonIds = CourseProgress::where('enrollment_id', $this->enrollment->id)
                ->where('is_completed', true)
                ->pluck('lesson_id')
                ->toArray();

            $totalLessons = $course->total_lessons;
            $completedCount = count($this->completedLessonIds);
            $this->progress = $totalLessons > 0 ? round(($completedCount / $totalLessons) * 100) : 0;

            $this->isLessonCompleted = in_array($this->lesson->id, $this->completedLessonIds);

            // 7. Previous & next lesson (dari allLessons)
            $allLessons = collect();
            foreach ($chapters as $chapter) {
                $allLessons = $allLessons->concat($chapter->lessons);
            }
            $currentLessonIndex = $allLessons->search(fn($item) => $item->id === $this->lesson->id);
            $this->previousLesson = $currentLessonIndex > 0 ? $allLessons[$currentLessonIndex - 1]->toArray() : null;
            $this->nextLesson = $currentLessonIndex < $allLessons->count() - 1 ? $allLessons[$currentLessonIndex + 1]->toArray() : null;

            // 8. Auto-mark last lesson
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
                $completedCount = count($this->completedLessonIds) + 1;
                $this->progress = $totalLessons > 0 ? round(($completedCount / $totalLessons) * 100) : 0;
            }

            // 9. Update last accessed lesson
            $this->enrollment->update([
                'last_accessed_lesson_id' => $this->lesson->id,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to load class lesson: ' . $e->getMessage());
            session()->flash('error', 'Gagal memuat materi kelas. Silakan coba lagi.');
        }
    }


    public function markAsCompletedAndNext(): void
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

            if ($this->nextLesson) {
                $this->redirect(route('student.classLesson', [
                    'classSlug' => $this->classSlug,
                    'lessonSlug' => $this->nextLesson['slug']
                ]));
            } else {
                session()->flash('info', 'Selamat! Anda telah menyelesaikan semua pelajaran di kelas ini.');
                $this->redirect(route('courses.show', $this->classSlug));
            }
        } catch (\Exception $e) {
            Log::error('Failed to mark lesson as completed and navigate: ' . $e->getMessage());
            session()->flash('error', 'Gagal menandai pelajaran. Silakan coba lagi.');
        }
    }

    public function render()
    {
        return view('livewire.student-class-lesson');
    }
}
