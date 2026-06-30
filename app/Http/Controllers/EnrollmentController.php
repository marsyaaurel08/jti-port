<?php

namespace App\Http\Controllers;

use App\Models\CourseProgress;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Course;
use Illuminate\Http\Request;
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
     * Cek status enrollment user untuk course atau class tertentu.
     */
    public function checkEnrollment(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'course_id' => 'nullable|exists:courses,id',
            'class_id' => 'nullable|exists:classes,id',
        ]);

        if (!$request->course_id && !$request->class_id) {
            return response()->json([
                'success' => false,
                'message' => 'Harap sertakan course_id atau class_id.'
            ], 400);
        }

        $query = Enrollment::where('user_id', $request->user_id);

        if ($request->course_id) {
            $query->where('course_id', $request->course_id);
        }

        if ($request->class_id) {
            $query->where('class_id', $request->class_id);
        }

        $enrollment = $query->with(['course', 'class', 'order'])->first();

        if ($enrollment) {
            $order = $enrollment->order;
            $processingTime = null;
            if ($order && $enrollment->enrolled_at && $order->created_at) {
                $processingTime = $order->created_at->diffInMilliseconds($enrollment->enrolled_at) / 1000;
            }

            return response()->json([
                'success' => true,
                'is_enrolled' => true,
                'status' => $enrollment->status,
                'data' => [
                    'enrollment_id' => $enrollment->id,
                    'enrolled_at' => $enrollment->enrolled_at,
                    'expires_at' => $enrollment->expires_at,
                    'is_completed' => $enrollment->is_completed,
                    'order_created_at' => $order ? $order->created_at : null,
                    'processing_time_seconds' => $processingTime,
                ]
            ]);
        }

        return response()->json([
            'success' => true,
            'is_enrolled' => false,
            'message' => 'User belum terdaftar di produk ini.'
        ]);
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
