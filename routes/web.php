<?php

use App\Http\Controllers\Auth\SocialiteController;
use App\Http\Controllers\{CartController, ClassesController, CourseController, EnrollmentController, HomeController, PaymentController, StudentController};
use App\Http\Controllers\Payment\WebhookController;
use App\Livewire\{StudentCart, StudentLesson, StudentOrders, StudentClassCheckout, StudentClassLesson, StudentCourseCheckout, StudentProfile, StudentTransactions};
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Support\Facades\Route;

// --- Public Routes ---
Route::get('/', [HomeController::class, 'index'])->name('home');

// --- Kursus & Kelas (Katalog Publik) ---
Route::prefix('courses')->name('courses.')->group(function () {
    Route::get('/', [CourseController::class, 'index'])->name('index');
    Route::get('/{slug}', [CourseController::class, 'show'])->name('show');
});

Route::prefix('classes')->name('classes.')->group(function () {
    Route::get('/', [ClassesController::class, 'index'])->name('index');
    Route::get('/{slug}', [ClassesController::class, 'show'])->name('show');
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

    //Quiz Attempt
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
    Route::get('/finish',   fn () => redirect()->route('student.orders'))->name('finish');
    Route::get('/unfinish', fn () => redirect()->route('student.orders'))->name('unfinish');
    Route::get('/error',    fn () => redirect()->route('student.orders'))->name('error');
});
