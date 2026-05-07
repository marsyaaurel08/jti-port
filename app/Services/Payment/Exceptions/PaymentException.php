<?php

namespace App\Services\Payment\Exceptions;

use RuntimeException;

class PaymentException extends RuntimeException
{
    public function __construct(
        string $message = '',
        private readonly string $gatewayName = '',
        private readonly mixed $rawResponse = null,
        int $code = 0,
        ?\Throwable $previous = null,
    ) {
        parent::__construct($message, $code, $previous);
    }

    public function getGatewayName(): string
    {
        return $this->gatewayName;
    }

    public function getRawResponse(): mixed
    {
        return $this->rawResponse;
    }
}
