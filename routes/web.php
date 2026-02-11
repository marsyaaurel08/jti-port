<?php

use App\Http\Controllers\Auth\SocialiteController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\ClassesController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\EnrollmentController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\UserController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
// use Illuminate\Foundation\Http\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// --- Public Routes ---
Route::get('/', [HomeController::class, 'index'])->name('home');

// Login Routes
Route::redirect('/login', '/admin/login')->name('login');
Route::redirect('/register', '/admin/register')->name('register');
Route::redirect('/logout', '/admin/logout')->name('logout');

// Email Verification Routes
Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');

Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();

    return redirect('/');
})->middleware(['auth', 'signed', 'throttle:6,1'])->name('verification.verify');

Route::post('/email/resend', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();

    return back()->with('message', 'Verification link sent!');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');

// Grup Rute Kursus & Kelas
Route::prefix('courses')->name('courses.')->group(function () {
    Route::get('/', [CourseController::class, 'index'])->name('index');
    Route::get('/{slug}', [CourseController::class, 'show'])->name('show');
});

Route::prefix('classes')->name('classes.')->group(function () {
    Route::get('/', [ClassesController::class, 'index'])->name('index');
    Route::get('/{slug}', [ClassesController::class, 'show'])->name('show');
});

// Keranjang Publik (Opsional)
Route::post('/cart/add/{course}', [CartController::class, 'store'])->name('cart.add');

// --- User Dashboard Routes (Authenticated) ---
Route::middleware(['auth', 'verified'])->prefix('student')->name('student.')->group(function () {
    // Profil & Keamanan
    Route::get('/', [UserController::class, 'index'])->name('index');
    Route::get('/profile', [UserController::class, 'index'])->name('profile');
    Route::put('/profile/update', [UserController::class, 'updateProfile'])->name('profile.update');
    Route::patch('/profile/avatar', [UserController::class, 'updateAvatar'])->name('profile.update.avatar');
    Route::put('/password/update', [UserController::class, 'updatePassword'])->name('password.update');

    // Enrollment
    Route::get('/enrolled', [EnrollmentController::class, 'index'])->name('enrolled');

    Route::get('/my-courses', [StudentController::class, 'myCourses'])
        ->name('my-courses');
    Route::get('/courses/{courseSlug}/lessons/{lessonSlug}', [StudentController::class, 'lesson'])
        ->name('lesson');

    Route::get('/my-classes', [StudentController::class, 'myClasses'])
        ->name('my-classes');
    Route::get('/classes/{classSlug}/lessons/{lessonSlug}', [StudentController::class, 'lesson'])
        ->name('classLesson');

    // Keranjang & Checkout (Disesuaikan dengan Sidebar & Cart Page)
    Route::get('/cart', [CartController::class, 'index'])->name('cart');
    Route::post('/cart/add/{course}', [CartController::class, 'store'])->name('cart.add');
    Route::delete('/cart/remove/{id}', [CartController::class, 'removeFromCart'])->name('cart.remove');

    // Transaksi
    Route::get('/transactions', [TransactionController::class, 'index'])->name('transactions');
});

// --- Social Login Routes ---
Route::get('/auth/{provider}/redirect', [SocialiteController::class, 'redirect'])
    ->name('socialite.redirect');

Route::get('/auth/{provider}/callback', [SocialiteController::class, 'callback'])
    ->name('socialite.callback');

Route::delete('/auth/{provider}/unlink', [SocialiteController::class, 'unlink'])
    ->middleware('auth')
    ->name('socialite.unlink');
// tes student course route
// Route::middleware(['auth'])->prefix('student')->name('student.')->group(function () {
//     Route::get('/my-courses', [StudentController::class, 'myCourses'])
//         ->name('my-courses');
//     Route::get('/courses/{courseSlug}/lessons/{lessonSlug}', [StudentController::class, 'lesson'])
//         ->name('lesson');
// });
