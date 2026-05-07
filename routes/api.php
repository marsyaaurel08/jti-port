<?php

use App\Http\Controllers\Payment\WebhookController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('webhook')->name('webhook.')->group(function () {
    // Midtrans mengirim POST ke URL ini saat status transaksi berubah.
    // Set URL ini di Midtrans Dashboard → Settings → Configuration → Payment Notification URL
    Route::post('/midtrans', [WebhookController::class, 'midtrans'])->name('midtrans');

    // BNI mengirim POST ke URL ini saat VA dibayar.
    // Set URL ini di BNI Developer Portal sebagai callback URL.
    Route::post('/bni', [WebhookController::class, 'bni'])->name('bni');
});
