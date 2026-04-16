<?php

use App\Models\Order;
use App\Models\User;
use App\Services\BNIService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

beforeEach(function () {
    $this->user = User::factory()->create([
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'phone_number' => '081234567890',
    ]);

    $this->order = Order::factory()->for($this->user)->create([
        'invoice_number' => 'INV-' . time(),
        'total_amount' => 150000,
        'status' => 'pending',
    ]);

    $this->service = app(BNIService::class);
});

describe('BNIService - Create Virtual Account', function () {

    it('creates virtual account successfully and updates order', function () {
        Http::fake([
            '*' => Http::response([
                'status' => '000',
                'data' => [
                    'virtual_account' => '9881123456789012',
                    'trx_id' => $this->order->bni_transaction_id ?? 'ORD' . time() . '001',
                ]
            ], 200),
        ]);

        $result = $this->service->createVirtualAccount($this->order);

        $this->order->refresh();

        expect($result)
            ->toHaveKey('va_number')
            ->and($result['va_number'])->toBe('9881123456789012')
            ->and($this->order->bni_transaction_id)->not->toBeNull()
            ->and($this->order->bni_va_number)->toBe('9881123456789012')
            ->and($this->order->bni_va_expired_at)->not->toBeNull();
    });

    it('throws exception when BNI response is not successful', function () {
        Http::fake([
            '*' => Http::response([
                'status' => '001',
                'message' => 'Invalid parameter'
            ], 200),
        ]);

        expect(fn() => $this->service->createVirtualAccount($this->order))
            ->toThrow(Exception::class);
    });

    it('logs error when creation fails', function () {
        Log::shouldReceive('error')->once()->with(
            'Failed to create BNI Virtual Account',
            Mockery::on(fn($data) => isset($data['order_id']) && $data['order_id'] === $this->order->id)
        );

        Http::fake([
            '*' => Http::response(['status' => '001'], 200),
        ]);

        try {
            $this->service->createVirtualAccount($this->order);
        } catch (Exception $e) {
            // expected
        }
    });

    it('generates correct transaction id format', function () {
        // Kita test melalui createVirtualAccount (private method)
        Http::fake(['*' => Http::response(['status' => '000', 'data' => ['virtual_account' => '988123456789']])]);

        $this->service->createVirtualAccount($this->order);

        $this->order->refresh();

        expect($this->order->bni_transaction_id)
            ->toStartWith('ORD')
            ->and(strlen($this->order->bni_transaction_id))->toBeGreaterThan(15);
    });
});

describe('BNIService - Get Payment Status', function () {

    it('retrieves payment status successfully', function () {
        $this->order->update(['bni_transaction_id' => 'ORD16822903451000001']);

        Http::fake([
            '*' => Http::response([
                'status' => '000',
                'data' => [
                    'paymentStatus' => '1',
                    'trxAmount' => '150000',
                    'paymentTime' => now()->toIso8601String(),
                ]
            ], 200),
        ]);

        $result = $this->service->getPaymentStatus($this->order);

        expect($result['status'])->toBe('paid')
            ->and($result['payment_amount'])->toBe(150000);
    });

    it('throws exception if bni_transaction_id is missing', function () {
        $orderWithoutTrx = Order::factory()->create(['bni_transaction_id' => null]);

        expect(fn() => $this->service->getPaymentStatus($orderWithoutTrx))
            ->toThrow(Exception::class, 'BNI Transaction ID not found');
    });
});

describe('BNIService - Webhook Handling', function () {

    it('handles successful APPROVED webhook and updates order to paid', function () {
        $this->order->update(['bni_transaction_id' => 'ORD123456']);

        $payload = [
            'trxId' => 'ORD123456',
            'trxAmount' => 150000,
            'paymentStatus' => 'APPROVED',
        ];

        $result = $this->service->handlePaymentWebhook($payload);

        $this->order->refresh();

        expect($result)->toBeTrue()
            ->and($this->order->status)->toBe('paid')
            ->and($this->order->paid_at)->not->toBeNull();
    });

    it('returns false when order not found for webhook', function () {
        $payload = [
            'trxId' => 'ORD_NOT_EXIST',
            'paymentStatus' => 'APPROVED',
        ];

        $result = $this->service->handlePaymentWebhook($payload);

        expect($result)->toBeFalse();
    });

    it('verifies webhook payload has required fields', function () {
        $validPayload = [
            'trxId' => 'ORD123',
            'trxAmount' => 100000,
            'paymentStatus' => '1',
        ];

        expect($this->service->verifyWebhookPayload($validPayload))->toBeTrue();

        $invalidPayload = ['trxAmount' => 100000];
        expect($this->service->verifyWebhookPayload($invalidPayload))->toBeFalse();
    });
});

describe('BNIService - Inactive Virtual Account', function () {

    it('inactivates VA successfully', function () {
        $this->order->update([
            'bni_transaction_id' => 'ORD123456',
            'bni_va_number' => '9881123456789012',
        ]);

        Http::fake([
            '*' => Http::response(['status' => '000'], 200),
        ]);

        $result = $this->service->inactiveVirtualAccount($this->order);

        expect($result)->toBeTrue();
    });

    it('returns false if transaction id or va number is missing', function () {
        $orderIncomplete = Order::factory()->create([
            'bni_transaction_id' => null,
            'bni_va_number' => null,
        ]);

        $result = $this->service->inactiveVirtualAccount($orderIncomplete);

        expect($result)->toBeFalse();
    });
});

describe('BNIService - Helper & Edge Cases', function () {

    it('maps BNI payment status correctly', function () {
        // Kita test melalui getPaymentStatus dengan berbagai status
        $this->order->update(['bni_transaction_id' => 'TEST']);

        Http::fake([
            '*' => Http::response([
                'status' => '000',
                'data' => ['paymentStatus' => '1']
            ]),
        ]);

        $result = $this->service->getPaymentStatus($this->order);
        expect($result['status'])->toBe('paid');
    });

    it('handles large amount correctly', function () {
        $bigOrder = Order::factory()->create(['total_amount' => 999999999]);

        Http::fake(['*' => Http::response(['status' => '000', 'data' => ['virtual_account' => '988999999999']])]);

        $result = $this->service->createVirtualAccount($bigOrder);

        expect((int) $result['amount'])->toBe(999999999);
    });
});
