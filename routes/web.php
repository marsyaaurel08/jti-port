<?php

use App\Http\Controllers\Auth\SocialiteController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

// Grup Rute Kursus
Route::prefix('courses')->name('courses.')->group(function () {
    // Halaman Katalog (Tampilkan Semua)
    Route::get('/', [CourseController::class, 'index'])->name('index');

    // Halaman Detail Kursus
    Route::get('/{slug}', [CourseController::class, 'show'])->name('show');
});
// Grup Rute Kelas
Route::prefix('classes')->name('classes.')->group(function () {
    // Halaman Katalog (Tampilkan Semua)
    Route::get('/', [ClassesController::class, 'index'])->name('index');

    // Halaman Detail Kelas
    Route::get('/{slug}', [ClassesController::class, 'show'])->name('show');
});

// Social Login Routes
Route::get('/auth/{provider}/redirect', [SocialiteController::class, 'redirect'])
    ->name('socialite.redirect');

Route::get('/auth/{provider}/callback', [SocialiteController::class, 'callback'])
    ->name('socialite.callback');

Route::delete('/auth/{provider}/unlink', [SocialiteController::class, 'unlink'])
    ->middleware('auth')
    ->name('socialite.unlink');
