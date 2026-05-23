<?php

use App\Http\Controllers\ClassesController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\Payment\PaymentController;
use App\Http\Controllers\Payment\WebhookController;
use App\Livewire\StudentClassCheckout;
use App\Livewire\StudentCourseCheckout;
use Illuminate\Support\Facades\Route;

Route::prefix('webhook')->name('webhook.')->group(function () {
    // Midtrans mengirim POST ke URL ini saat status transaksi berubah.
    // Set URL ini di Midtrans Dashboard → Settings → Configuration → Payment Notification URL
    Route::post('/midtrans', [WebhookController::class, 'midtrans'])->name('midtrans');

    // BNI mengirim POST ke URL ini saat VA dibayar.
    // Set URL ini di BNI Developer Portal sebagai callback URL.
    Route::post('/bni', [WebhookController::class, 'bni'])->name('bni');
});

Route::middleware('auth:sanctum')->group(function () {
    // Checkout - Terpisah untuk Course dan Class
    // Memuat halaman checkout Livewire
    Route::get('/course/checkout/{slug}', StudentCourseCheckout::class)->name('checkout.course');
    Route::get('/class/checkout/{slug}', StudentClassCheckout::class)->name('checkout.class');

    // Endpoint untuk trigger self-healing manual (hanya untuk testing, tidak dipakai di production)
    // Params di body: { "course_id": "<course_id>", user_id": "<user_id>", status": "pending" }
    Route::post('/testing/course/checkout', [PaymentController::class, 'checkoutCourse'])->name('checkoutCourse');
    // Params di body: { "class_id": "<class_id>", user_id": "<user_id>", status": "pending" }
    Route::post('/testing/class/checkout', [PaymentController::class, 'checkoutClass'])->name('checkoutClass');
});

// Route untuk trigger self-healing (load testing saja, tidak dipakai di production)
// Endpoint ini bisa dipanggil secara manual untuk simulasi
// Params di body: { force: boolean }
// Jika force=true, maka akan memproses semua order yang masih pending tanpa cek waktu created_at
Route::post('/self-healing', [WebhookController::class, 'selfHealing'])->name('self-healing');
