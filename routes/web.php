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
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// --- Public Routes ---
Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/user', function () {
    return redirect('/');
});
/**
 * Penting: Mendefinisikan rute 'login' agar Middleware Auth tidak error.
 * Diarahkan ke halaman login Filament User Panel.
 */
Route::get('/login', function () {
    return redirect()->route('filament.user.auth.login');
})->name('login');

/**
 * Opsional: Jika Anda ingin menggunakan registrasi bawaan Filament
 */
Route::get('/register', function () {
    return redirect()->route('filament.user.auth.register');
})->name('register');

// Logout Route - Menggunakan logout bawaan filament atau manual
Route::post('/logout', function () {
    auth()->logout();

    return redirect('/');
})->name('logout');

// --- Email Verification Routes ---
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

// --- Kursus & Kelas (Katalog Publik) ---
Route::prefix('courses')->name('courses.')->group(function () {
    Route::get('/', [CourseController::class, 'index'])->name('index');
    Route::get('/{slug}', [CourseController::class, 'show'])->name('show');
});

Route::prefix('classes')->name('classes.')->group(function () {
    Route::get('/', [ClassesController::class, 'index'])->name('index');
    Route::get('/{courseClass}', [ClassesController::class, 'show'])->name('show');
});

// Keranjang Publik
Route::post('/cart/add/{course}', [CartController::class, 'store'])->name('cart.add');

// --- User Dashboard Routes (Authenticated) ---
// Menggunakan middleware 'auth' yang sekarang akan me-redirect ke filament.user.auth.login jika belum login
Route::middleware(['auth', 'verified'])->prefix('student')->name('student.')->group(function () {

    // Profil & Keamanan
    Route::get('/', [UserController::class, 'index'])->name('index');
    Route::get('/profile', [UserController::class, 'index'])->name('profile');
    Route::put('/profile/update', [UserController::class, 'updateProfile'])->name('profile.update');
    Route::patch('/profile/avatar', [UserController::class, 'updateAvatar'])->name('profile.update.avatar');
    Route::put('/password/update', [UserController::class, 'updatePassword'])->name('password.update');

    // Enrollment & Belajar
    Route::get('/enrolled', [EnrollmentController::class, 'index'])->name('enrolled');
    Route::get('/my-courses', [StudentController::class, 'myCourses'])->name('my-courses');
    Route::get('/my-classes', [StudentController::class, 'myClasses'])->name('my-classes');

    // Materi Kursus & Kelas
    Route::get('/courses/{courseSlug}/lessons/{lessonSlug}', [StudentController::class, 'lesson'])->name('lesson');
    Route::get('/classes/{classSlug}/lessons/{lessonSlug}', [StudentController::class, 'lesson'])->name('classLesson');

    // Keranjang & Checkout
    Route::get('/cart', [CartController::class, 'index'])->name('cart');
    Route::delete('/cart/remove/{id}', [CartController::class, 'removeFromCart'])->name('cart.remove');

    // Transaksi
    Route::get('/transactions', [TransactionController::class, 'index'])->name('transactions');
});

// --- Social Login Routes ---
Route::get('/auth/{provider}/redirect', [SocialiteController::class, 'redirect'])->name('socialite.redirect');
Route::get('/auth/{provider}/callback', [SocialiteController::class, 'callback'])->name('socialite.callback');
Route::delete('/auth/{provider}/unlink', [SocialiteController::class, 'unlink'])->middleware('auth')->name('socialite.unlink');
