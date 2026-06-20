<?php

namespace App\Http\Controllers;

use App\Models\CourseProgress;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Course;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class EnrollmentController extends Controller
{
    public function index()
    {
        $enrolled = Enrollment::where('user_id', Auth::id())
            ->with('course.category')
            ->latest()
            ->paginate(6);

        return view('pages.user.enrolled', compact('enrolled'));
    }

    /**
     * Membuat order dan course progress untuk enrollment
     */
    public static function createEnrollmentOrder(Enrollment $enrollment, int $courseId): void
    {
        // Membuat transaksi otomatis oleh admin
        $order = Order::create([
            'user_id' => $enrollment->user_id,
            'invoice_number' => 'INV-ADM-' . now()->format('Ymd') . strtoupper(Str::random(5)),
            'subtotal' => 0,
            'total_amount' => 0,
            'status' => 'paid',
            'paid_at' => now(),
        ]);

        // Asosiasikan Order ke Enrollment
        $enrollment->update(['order_id' => $order->id]);

        $course = Course::find($courseId);
        
        OrderItem::create([
            'order_id' => $order->id,
            'course_id' => $courseId,
            'class_id' => $enrollment->class_id,
            'price_at_purchase' => 0,
            'item_name' => $course ? $course->title : 'Course Enrollment',
        ]);

        // Ambil semua ID lesson dari course
        $lessonIds = Lesson::whereHas('chapter', function ($query) use ($courseId) {
            $query->where('course_id', $courseId);
        })->pluck('id');

        // Persiapkan data untuk bulk insert
        $progressData = $lessonIds->map(function ($lessonId) use ($enrollment) {
            return [
                'enrollment_id' => $enrollment->id,
                'lesson_id' => $lessonId,
                'is_completed' => false,
                'last_watched_at' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        })->toArray();

        // Simpan ke tabel course_progress
        if (! empty($progressData)) {
            CourseProgress::insert($progressData);
        }
    }
}
