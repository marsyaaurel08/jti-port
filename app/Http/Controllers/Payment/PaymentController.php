<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Models\Class as ClassModel;
use App\Models\Course;
use App\Models\CourseClass;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Promo;
use App\Models\User;
use App\Models\UserPromo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    /**
     * Create order for load testing without Midtrans integration
     * API endpoint: POST /api/payment/load-test/order
     *
     * Request body:
     * {
     *   "class_id": 1,
     *   "user_id": 1 (optional - if not provided, uses Auth::id()),
     *   "status": "pending" (optional - default 'pending', can be 'paid'),
     *   "promo_id": 1 (optional - apply promo if provided)
     * }
     */
    public function checkoutClass(Request $request)
    {
        $request->validate([
            'class_id' => 'required|exists:classes,id',
            'user_id' => 'nullable|exists:users,id',
            'status' => 'nullable|in:pending,paid',
            'promo_id' => 'nullable|exists:promos,id',
        ]);

        DB::beginTransaction();

        try {
            // Determine user ID
            $userId = $request->input('user_id') ?? Auth::id();

            if (! $userId) {
                DB::rollBack();

                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated. Please provide user_id in request or authenticate first.',
                ], 401);
            }

            // Get class and validate
            $class = CourseClass::findOrFail($request->input('class_id'));

            // Re-validasi Promo if provided
            $promoId = $request->input('promo_id');
            $discountAmount = 0;

            if ($promoId) {
                $promo = Promo::find($promoId);

                if (! $promo || ! $promo->is_active) {
                    throw new \Exception('Promo that is used is no longer valid.');
                }

                if ($promo->max_usage !== null && $promo->userPromos()->count() >= $promo->max_usage) {
                    throw new \Exception('Promo quota has been exhausted.');
                }

                if (
                    UserPromo::where('promo_id', $promo->id)
                    ->where('user_id', $userId)
                    ->count() >= $promo->max_usage_per_user
                ) {
                    throw new \Exception('You have reached the promo usage limit.');
                }

                // Calculate discount
                $subtotal = (float) $class->price;
                $discount = $subtotal * ($promo->value / 100);
                $discountAmount = min($discount, $subtotal);
            }

            $subtotal = (float) $class->price;
            $totalAmount = $subtotal - $discountAmount;

            // Create order
            $order = Order::create([
                'user_id' => $userId,
                'invoice_number' => $this->generateInvoiceNumber($userId),
                'subtotal' => $subtotal,
                'discount_amount' => $discountAmount,
                'total_amount' => $totalAmount,
                'status' => $request->input('status') ?? 'pending',
                'payment_gateway' => 'midtrans', // Mark as load test
                'payment_method' => 'bni_va', // Mark as load test
                'promo_id' => $promoId,
            ]);

            // Create user promo record if promo applied
            if ($promoId) {
                UserPromo::create([
                    'user_id' => $userId,
                    'promo_id' => $promoId,
                    'order_id' => $order->id,
                    'discount_amount' => $discountAmount,
                ]);
            }

            // Create order item
            OrderItem::create([
                'order_id' => $order->id,
                'course_id' => $class->course_id,
                'class_id' => $class->id,
                'price_at_purchase' => $totalAmount,
                'item_name' => $class->title,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Order created successfully for load testing.',
                'data' => [
                    'order' => $order->fresh('items'),
                    'class' => $class,
                ],
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Failed to create order: ' . $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Generate invoice number for order
     */
    private function generateInvoiceNumber(int $userId): string
    {
        $micro = sprintf('%06d', (int)((microtime(true) - floor(microtime(true))) * 1000000));

        return 'INV-LOADTEST-' .
            date('YmdHis') .
            $micro .
            '-' .
            $userId;
    }

    /**
     * Create course order for load testing without Midtrans integration
     * API endpoint: POST /api/payment/load-test/course-order
     *
     * Request body:
     * {
     *   "course_id": 1,
     *   "user_id": 1 (optional - if not provided, uses Auth::id()),
     *   "status": "pending" (optional - default 'pending', can be 'paid'),
     *   "promo_id": 1 (optional - apply promo if provided)
     * }
     */
    public function checkoutCourse(Request $request)
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id',
            'user_id' => 'nullable|exists:users,id',
            'status' => 'nullable|in:pending,paid',
            'promo_id' => 'nullable|exists:promos,id',
        ]);

        DB::beginTransaction();

        try {
            // Determine user ID
            $userId = $request->input('user_id') ?? Auth::id();

            if (! $userId) {
                DB::rollBack();

                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated. Please provide user_id in request or authenticate first.',
                ], 401);
            }

            // Get course and validate
            $course = Course::findOrFail($request->input('course_id'));

            // Re-validasi Promo if provided
            $promoId = $request->input('promo_id');
            $discountAmount = 0;

            if ($promoId) {
                $promo = Promo::find($promoId);

                if (! $promo || ! $promo->is_active) {
                    throw new \Exception('Promo that is used is no longer valid.');
                }

                if ($promo->max_usage !== null && $promo->userPromos()->count() >= $promo->max_usage) {
                    throw new \Exception('Promo quota has been exhausted.');
                }

                if (
                    UserPromo::where('promo_id', $promo->id)
                    ->where('user_id', $userId)
                    ->count() >= $promo->max_usage_per_user
                ) {
                    throw new \Exception('You have reached the promo usage limit.');
                }

                // Validate promo applies to this course
                if (! $promo->for_all_products && ! $promo->products()->where('course_id', $course->id)->exists()) {
                    throw new \Exception('Promo does not apply to this course.');
                }

                // Calculate discount
                $subtotal = (float) $course->price;
                if ($promo->type === 'percentage') {
                    $discount = $subtotal * ($promo->value / 100);
                } else {
                    $discount = $promo->value;
                }
                $discountAmount = min($discount, $subtotal);
            }

            $subtotal = (float) $course->price;
            $totalAmount = $subtotal - $discountAmount;

            // Create order
            $order = Order::create([
                'user_id' => $userId,
                'invoice_number' => $this->generateInvoiceNumber($userId),
                'subtotal' => $subtotal,
                'discount_amount' => $discountAmount,
                'total_amount' => $totalAmount,
                'status' => $request->input('status') ?? 'pending',
                'payment_gateway' => 'midtrans', // Mark as load test
                'payment_method' => 'bni_va', // Mark as load test
                'promo_id' => $promoId,
            ]);

            // Create user promo record if promo applied
            if ($promoId) {
                UserPromo::create([
                    'user_id' => $userId,
                    'promo_id' => $promoId,
                    'order_id' => $order->id,
                    'discount_amount' => $discountAmount,
                ]);
            }

            // Create order item
            OrderItem::create([
                'order_id' => $order->id,
                'course_id' => $course->id,
                'price_at_purchase' => $totalAmount,
                'item_name' => $course->title,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Course order created successfully for load testing.',
                'data' => [
                    'order' => $order->fresh('items'),
                    'course' => $course,
                ],
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Failed to create course order: ' . $e->getMessage(),
            ], 400);
        }
    }
}
