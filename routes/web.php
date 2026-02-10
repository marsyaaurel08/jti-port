<?php

use App\Http\Controllers\Auth\SocialiteController;
use App\Http\Controllers\{CartController, ClassesController, CourseController, DashboardController, EnrollmentController, HomeController, TransactionController, UserController};
use App\Http\Controllers\StudentController;
use Illuminate\Support\Facades\Route;

// --- Public Routes ---
Route::get('/', [HomeController::class, 'index'])->name('home');

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
Route::middleware(['auth', 'verified'])->prefix('user')->name('user.')->group(function () {

    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('index');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Kursus & Belajar
    Route::get('/enrolled-courses', [EnrollmentController::class, 'index'])->name('enrolled');

    // Keranjang & Checkout (Disesuaikan dengan Sidebar & Cart Page)
    Route::get('/cart', [CartController::class, 'index'])->name('cart');
    Route::post('/cart/add/{course}', [CartController::class, 'store'])->name('cart.add');
    Route::delete('/cart/remove/{id}', [CartController::class, 'removeFromCart'])->name('cart.remove');

    // Transaksi
    Route::get('/transactions', [TransactionController::class, 'index'])->name('transactions');

    // Profil & Keamanan
    Route::get('/profile', [UserController::class, 'index'])->name('profile');
    Route::put('/profile/update', [UserController::class, 'updateProfile'])->name('profile.update');
    Route::patch('/profile/avatar', [UserController::class, 'updateAvatar'])->name('profile.update.avatar');
    Route::put('/password/update', [UserController::class, 'updatePassword'])->name('password.update');
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
Route::get('/student/my-courses', [StudentController::class, 'myCourses'])
    ->name('student.my-courses');
