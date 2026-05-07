<?php

namespace App\Services\Payment;

use App\Services\Payment\Contracts\PaymentServiceInterface;
use App\Services\Payment\Exceptions\PaymentException;



class PaymentFactory
{
    /**
     * Membuat instance Payment Service berdasarkan nama gateway.
     *
     * @param  string  $gateway  Nama gateway: 'bni', 'midtrans', 'manual'
     * @return PaymentServiceInterface
     *
     * @throws PaymentException
     */
    public static function make(string $gateway): PaymentServiceInterface
    {
        return match ($gateway) {
            'midtrans' => app(MidtransService::class),
            'bni'      => app(BNIService::class),
            default    => throw new PaymentException(
                message:     "Payment gateway [{$gateway}] tidak didukung.",
                gatewayName: $gateway,
            ),
        };
    }
}
