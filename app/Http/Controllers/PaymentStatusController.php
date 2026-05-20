<?php

namespace App\Http\Controllers;

use App\Services\Payment\MidtransService;
use Illuminate\Http\JsonResponse;

class PaymentStatusController extends Controller
{
    public function check(string $externalId): JsonResponse
    {
        $service = new MidtransService;
        $status = $service->checkTransactionStatus($externalId);

        // Kembalikan hanya data yang dibutuhkan frontend
        return response()->json([
            'is_paid' => $status['is_paid'],
            'is_cancelled' => $status['is_cancelled'],
        ]);
    }
}
