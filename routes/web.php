<?php

use App\Http\Controllers\Auth\SocialiteController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\ClassesController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\EnrollmentController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PaymentStatusController;
use App\Http\Controllers\StudentController;
use App\Livewire\Pages\ClassShow;
use App\Livewire\Pages\CourseShow;
use App\Livewire\StudentCart;
use App\Livewire\StudentClassCheckout;
use App\Livewire\StudentClassLesson;
use App\Livewire\StudentCourseCheckout;
use App\Livewire\StudentLesson;
use App\Livewire\StudentOrders;
use App\Livewire\StudentProfile;
use App\Livewire\StudentTransactions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// --- Public Routes ---
Route::get('/', [HomeController::class, 'index'])->name('home');

// --- Kursus & Kelas (Katalog Publik) ---
Route::prefix('courses')->name('courses.')->group(function () {
    Route::get('/', [CourseController::class, 'index'])->name('index');
    // Use Livewire CourseShow component for course detail page
    Route::get('/{slug}', CourseShow::class)->name('show');
});

Route::prefix('classes')->name('classes.')->group(function () {
    Route::get('/', [ClassesController::class, 'index'])->name('index');
    Route::get('/{slug}', ClassShow::class)->name('show');
});

Route::middleware(['auth'])->prefix('/user')->name('user.')->group(function () {
    Route::get('/', function () {
        // Cek manual apakah sudah verified
        if (! auth()->user()?->hasVerifiedEmail()) {
            // Redirect ke halaman prompt Filament (bukan Laravel default)
            return redirect()->route('filament.user.auth.email-verification.prompt'); // sesuaikan panel ID kamu
            // atau filament()->getPanel('user')->getEmailVerificationPromptUrl()
        }

        return redirect()->route('home');
    })->name('index');
});

// --- User Dashboard Routes (Authenticated) ---
// Menggunakan middleware 'auth' yang sekarang akan me-redirect ke filament.user.auth.login jika belum login
Route::middleware(['auth', 'verified', 'non-admin'])->prefix('student')->name('student.')->group(function () {
    // Profil & Keamanan
    Route::get('/', function () {
        return redirect()->route('student.profile');
    })->name('index');
    Route::get('/profile', StudentProfile::class)->name('profile');

    // Enrollment & Belajar
    // Route::get('/enrolled', [EnrollmentController::class, 'index'])->name('enrolled');
    Route::get('/my-courses', [StudentController::class, 'myCourses'])->name('my-courses');
    Route::get('/my-classes', [StudentController::class, 'myClasses'])->name('my-classes');

    // Materi Kursus & Kelas
    Route::get('/courses/{courseSlug}/lessons/{lessonSlug}', StudentLesson::class)->name('lesson');
    Route::get('/classes/{classSlug}/lessons/{lessonSlug}', StudentClassLesson::class)->name('classLesson');

    // Keranjang & Checkout
    Route::get('/cart', StudentCart::class)->name('cart');
    Route::delete('/cart/remove/{id}', [CartController::class, 'removeFromCart'])->name('cart.remove');

    // Keranjang (Authenticated Only)
    Route::post('/cart/add/{course}', [CartController::class, 'storeCourse'])->name('cart.add');
    Route::post('/cart/add-class/{courseClass}', [CartController::class, 'storeClass'])->name('cart.add-class');

    // Checkout - Terpisah untuk Course dan Class
    Route::get('/course/checkout/{slug}', StudentCourseCheckout::class)->name('checkout.course');
    Route::get('/class/checkout/{slug}', StudentClassCheckout::class)->name('checkout.class');

    // Orders
    Route::get('/orders', StudentOrders::class)->name('orders');

    // Transaksi
    Route::get('/transactions', StudentTransactions::class)->name('transactions');

    // Quiz Attempt
    Route::get('/quiz/attempt/{id}', function ($id) {
        return view('pages.quiz-attempt-page', ['quizId' => $id]);
    })->name('quiz.attempt');

    // Quiz Result
    // Route::get('/quiz/attempt/{attempt}/result', [QuizAttemptController::class, 'showResult'])->name('quiz.attempt.result');
});

// --- Social Login Routes ---
Route::get('/auth/{provider}/redirect', [SocialiteController::class, 'redirect'])->name('socialite.redirect');
Route::get('/auth/{provider}/callback', [SocialiteController::class, 'callback'])->name('socialite.callback');
Route::delete('/auth/{provider}/unlink', [SocialiteController::class, 'unlink'])->middleware('auth')->name('socialite.unlink');

// ============================================================
// Midtrans Finish / Error / Unfinish (Redirect dari Snap)
// ============================================================

Route::prefix('payment')->name('payment.')->group(function () {
    Route::get('/finish', fn() => redirect()->route('student.orders'))->name('finish');
    Route::get('/unfinish', fn() => redirect()->route('student.orders'))->name('unfinish');
    Route::get('/error', fn() => redirect()->route('student.orders'))->name('error');
});

Route::get('/payment/check-status/{externalId}', [PaymentStatusController::class, 'check'])
    ->middleware('auth')
    ->name('payment.check-status');

// routes/web.php (pakai middleware web agar bisa akses session login)
Route::middleware('auth')->post('/api/tokens/create', function (Request $request) {
    $token = $request->user()->createToken('k6-load-tester');
    return response()->json(['token' => $token->plainTextToken]);
});
