# BNI Integration Implementation Guide untuk Course Checkout

## Daftar Isi

1. [Overview](#overview)
2. [Fitur yang Diimplementasikan](#fitur-yang-diimplementasikan)
3. [Setup dan Instalasi](#setup-dan-instalasi)
4. [Konfigurasi](#konfigurasi)
5. [Struktur Kode](#struktur-kode)
6. [API Endpoints](#api-endpoints)
7. [Testing](#testing)
8. [Troubleshooting](#troubleshooting)

---

## Overview

Integrasi BNI untuk checkout course menggunakan **BNI Ecollection API** (Virtual Account) yang memungkinkan:

- Pembuatan Virtual Account unik untuk setiap transaksi
- Automatic payment verification saat customer mentransfer ke VA
- Real-time payment status tracking
- Webhook-based payment confirmation

**Product yang digunakan**: BNI Ecollection (Virtual Account)
**Package**: `bni-api/bni-php` v2.3.4

---

## Fitur yang Diimplementasikan

### ✅ Fitur Core

- [x] Student Course Checkout dengan form lengkap
- [x] Integrasi BNI Ecollection API untuk Virtual Account
- [x] Create Billing (VA creation)
- [x] Inquiry Billing (Status check)
- [x] Inactive Billing (Cancel VA)
- [x] Payment Webhook Handler
- [x] Invoice Number Generation
- [x] Promo Code Support
- [x] Multiple Transfer Method Instructions (BNI, BCA, Mandiri, BRI)

### ✅ Database

- [x] Migration untuk BNI fields di Order table
- [x] BNI transaction ID storage
- [x] BNI VA number storage
- [x] BNI VA expiration tracking

### ✅ API Endpoints

- [x] POST `/api/payments/bni-callback` - Webhook untuk payment confirmation
- [x] POST `/api/payments/status` - Check payment status (auth required)
- [x] GET `/api/payments/order/{order}` - Get order payment details (auth required)

### ✅ Testing

- [x] BNI Service unit tests
- [x] Payment Callback Controller tests
- [x] Student Course Checkout component tests

---

## Setup dan Instalasi

### 1. Install Package

```bash
composer require bni-api/bni-php
```

### 2. Generate Migration

```bash
php artisan migrate
```

Ini akan menambahkan 3 kolom baru ke tabel `orders`:

- `bni_transaction_id` (string, nullable)
- `bni_va_number` (string, nullable)
- `bni_va_expired_at` (datetime, nullable)

### 3. Environment Variables

Tambahkan ke `.env`:

```env
# BNI API Configuration
BNI_ENVIRONMENT=sandbox          # atau 'prod' untuk production
BNI_CLIENT_ID=your_client_id
BNI_CLIENT_SECRET=your_client_secret
BNI_API_KEY=your_api_key          # Optional
BNI_API_SECRET=your_api_secret    # Optional

# BNI API Endpoints (optional, defaults sudah disediakan)
# BNI_SNAP_BASE_URL=https://app.sandbox.midtrans.com/snap/v1
# BNI_OGP_BASE_URL=https://sandbox.bni.co.id/api/v1
# BNI_BNIDIRECT_BASE_URL=https://sandbox.bni.co.id/api/v1

# Webhook URL (optional, defaults ke APP_URL/api/payments/bni-callback)
# BNI_WEBHOOK_URL=https://yourdomain.com/api/payments/bni-callback
```

### 4. Registrasikan BNI Service (jika diperlukan)

Jika Anda ingin menggunakan singleton instance, tambahkan di `bootstrap/providers.php`:

```php
\App\Services\BNIService::class,
```

---

## Konfigurasi

### File: `config/bni.php`

```php
return [
    'environment' => env('BNI_ENVIRONMENT', 'sandbox'),
    'client_id' => env('BNI_CLIENT_ID'),
    'client_secret' => env('BNI_CLIENT_SECRET'),
    'api_key' => env('BNI_API_KEY', ''),
    'api_secret' => env('BNI_API_SECRET', ''),
    'snap_base_url' => env('BNI_SNAP_BASE_URL', 'https://app.sandbox.midtrans.com/snap/v1'),
    'ogp_base_url' => env('BNI_OGP_BASE_URL', 'https://sandbox.bni.co.id/api/v1'),
    'bnidirect_base_url' => env('BNI_BNIDIRECT_BASE_URL', 'https://sandbox.bni.co.id/api/v1'),
    'webhook_url' => env('BNI_WEBHOOK_URL', env('APP_URL').'/api/payments/bni-callback'),
];
```

---

## Struktur Kode

### 1. Service Layer: `app/Services/BNIService.php`

**Tanggung jawab:**

- Menghandle semua komunikasi dengan BNI API
- Membuat Virtual Account baru
- Verifikasi payment status
- Handle webhook dari BNI

**Public Methods:**

```php
// Create Virtual Account
createVirtualAccount(Order $order): array

// Get Payment Status
getPaymentStatus(Order $order): array

// Verify Webhook Payload
verifyWebhookPayload(array $payload): bool

// Handle Payment Webhook
handlePaymentWebhook(array $payload): bool

// Inactive Virtual Account
inactiveVirtualAccount(Order $order): bool
```

### 2. Controller: `app/Http/Controllers/PaymentCallbackController.php`

**Endpoints:**

- `POST /api/payments/bni-callback` - Webhook handler (no auth)
- `POST /api/payments/status` - Check payment status (auth required)
- `GET /api/payments/order/{order}` - Get order details (auth required)

### 3. Livewire Component: `app/Livewire/StudentCourseCheckout.php`

**Features:**

- Course selection dan price display
- User information form
- Promo code management
- Multiple payment method selection
- Integration dengan BNI Service
- Order creation flow

**Key Properties:**

```php
public string $fullName;           // Required
public string $email;              // Required, email format
public string $phoneNumber;        // Required, ID phone format
public string $promoCode;          // Optional
public float $discountAmount;      // Calculated
public string $transferMethod;     // bni_mobile, bca_mobile, dll
public bool $showVAInstructions;   // Show VA details modal
public array $vaData;              // BNI VA response data
```

**Key Methods:**

```php
processPayment()           // Create order dan generate VA
applyPromoCode()          // Apply discount dari promo code
removePromoCode()         // Remove applied promo
getTotalAmount(): float   // Return total after discount
getTransferInstructions() // Get transfer steps untuk selected method
```

### 4. Model: `app/Models/Order.php`

**New Properties:**

```php
protected $fillable = [
    // ... existing fields ...
    'bni_transaction_id',    // BNI Ecollection transaction ID
    'bni_va_number',         // Virtual Account number
    'bni_va_expired_at',     // VA expiration datetime
];

protected function casts(): array
{
    return [
        // ... existing casts ...
        'bni_va_expired_at' => 'datetime',
    ];
}
```

---

## API Endpoints

### 1. BNI Webhook Callback

**Endpoint:** `POST /api/payments/bni-callback`

**Authentication:** None (CSRF exempt for webhooks)

**Request Body:**

```json
{
    "trxId": "ORD1680000001000001",
    "trxAmount": "100000",
    "paymentStatus": "APPROVED",
    "paymentTime": "2024-04-07T10:30:00+07:00"
}
```

**Response (Success):**

```json
{
    "status": "success",
    "message": "Payment confirmed"
}
```

**Response (Error):**

```json
{
    "status": "error",
    "message": "Invalid webhook payload"
}
```

### 2. Check Payment Status

**Endpoint:** `POST /api/payments/status`

**Authentication:** Required (Bearer token)

**Request Body:**

```json
{
    "bni_transaction_id": "ORD1680000001000001"
}
```

**Response:**

```json
{
    "status": "success",
    "data": {
        "order_id": 1,
        "payment_status": "paid",
        "amount": 100000,
        "paid_at": "2024-04-07T10:30:00+07:00"
    }
}
```

### 3. Get Order Payment Details

**Endpoint:** `GET /api/payments/order/{orderId}`

**Authentication:** Required (Bearer token)

**Response:**

```json
{
    "status": "success",
    "data": {
        "order_id": 1,
        "invoice_number": "INV-20240407103000-1",
        "status": "pending",
        "amount": 100000,
        "payment_method": "bni_mobile",
        "bni_va_number": "9000000001000001",
        "bni_va_expired_at": "2024-04-08T10:30:00+07:00",
        "paid_at": null,
        "created_at": "2024-04-07T10:30:00+07:00"
    }
}
```

---

## Testing

### Jalankan Tests

```bash
# Run all payment tests
php artisan test tests/Feature/Payment

# Run specific test
php artisan test tests/Feature/Payment/BNIServiceTest.php

# Run with filter
php artisan test --filter=handle_bni_callback

# Generate coverage report
php artisan test --coverage
```

### Test Files

1. **BNIServiceTest.php** - Test BNI service methods
    - `it_can_create_virtual_account_for_order`
    - `it_can_verify_webhook_payload`
    - `it_can_handle_payment_webhook`
    - `it_can_get_payment_status`
    - `it_can_inactive_virtual_account`

2. **PaymentCallbackControllerTest.php** - Test API endpoints
    - `it_can_handle_bni_callback_webhook`
    - `it_rejects_invalid_callback_payload`
    - `it_can_check_payment_status`
    - `it_requires_auth_to_check_payment_status`
    - `it_can_get_order_payment_details`

3. **StudentCourseCheckoutTest.php** - Test checkout flow
    - `it_displays_course_checkout_page`
    - `authenticated_user_can_prefill_personal_info`
    - `user_can_apply_promo_code`
    - `user_can_process_payment`
    - `it_validates_required_fields`

---

## Troubleshooting

### 1. "Unable to create Virtual Account"

**Penyebab:**

- BNI credentials belum dikonfigurasi dengan benar
- Environment set ke 'prod' tapi credentials adalah sandbox

**Solusi:**

```bash
# Verify environment
cat .env | grep BNI_

# Check dengan sandbox first
# Update .env:
BNI_ENVIRONMENT=sandbox
BNI_CLIENT_ID=your_sandbox_id
BNI_CLIENT_SECRET=your_sandbox_secret

# Clear config cache
php artisan config:clear
```

### 2. "Webhook not being received"

**Penyebab:**

- Webhook URL tidak terdaftar di BNI dashboard
- Firewall memblokir request dari BNI

**Solusi:**

```bash
# Check webhook URL di config
php artisan tinker
>>> config('bni.webhook_url')

# Pastikan URL publicly accessible
# Test dengan curl:
curl -X POST https://yourdomain.com/api/payments/bni-callback \
  -H "Content-Type: application/json" \
  -d '{"trxId":"TEST","trxAmount":"100000","paymentStatus":"APPROVED"}'

# Harus return JSON dengan status "success" atau "error"
```

### 3. "Invalid phone number validation"

**Penyebab:**

- Phone number format tidak sesuai dengan rule `phone:ID,mobile`

**Solusi:**

```php
// Format yang valid:
// - 081234567890 (tanpa +62 atau 0)
// - Divalidasi sebagai mobile Indonesia

// Update form:
<input
  type="tel"
  wire:model="phoneNumber"
  placeholder="812xxxxxx"
  pattern="^[0-9]{10,12}$"
/>
```

### 4. "Order tidak tercreate setelah processPayment"

**Penyebab:**

- Validation error pada form fields
- BNI API error saat membuat VA
- Database transaction failure

**Solusi:**

```php
// Check logs
tail -f storage/logs/laravel.log

// Gunakan tinker untuk debug
php artisan tinker

// Create order manually
>>> \App\Models\Order::create([
...     'user_id' => 1,
...     'invoice_number' => 'INV-TEST-001',
...     'subtotal' => 100000,
...     'total_amount' => 100000,
...     'status' => 'pending',
... ]);
```

### 5. CSRF Token Mismatch pada Webhook

**Error:** "CSRF token mismatch" saat BNI mengirim webhook

**Solusi:**
Sudah dihandle otomatis di routes:

```php
Route::post('/bni-callback', [...])
    ->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);
```

Jika masih error, clear routes cache:

```bash
php artisan route:clear
php artisan config:clear
php artisan cache:clear
```

---

## Queue Job untuk Payment Processing (Optional)

Untuk production, sebaiknya handle webhook di background job:

```php
// Create Job
php artisan make:job ProcessPaymentCallback --queued

// File: app/Jobs/ProcessPaymentCallback.php
class ProcessPaymentCallback implements ShouldQueue
{
    public function __construct(private array $payload) {}

    public function handle(BNIService $bniService)
    {
        $bniService->handlePaymentWebhook($this->payload);
    }
}

// Di controller
public function bniCallback(Request $request)
{
    ProcessPaymentCallback::dispatch($request->all());

    return response()->json(['status' => 'success'], 200);
}
```

---

## Checklist Deployment ke Production

- [ ] Update `.env` dengan credentials production BNI
- [ ] Set `BNI_ENVIRONMENT=prod`
- [ ] Update `BNI_WEBHOOK_URL` ke production URL
- [ ] Testkan webhook dari BNI dashboard
- [ ] Setup monitoring untuk logs
- [ ] Backup database sebelum migration
- [ ] Run full test suite
- [ ] Update BNI settings untuk webhook URL
- [ ] Test payment flow end-to-end
- [ ] Setup alert untuk payment failures

---

## Support & Reference

- **BNI API Docs:** https://digitalservices.bni.co.id/documentation/public/en
- **Package GitHub:** https://github.com/bni-api/bni-php-sdk
- **BNI Digital Services:** https://digitalservices.bni.co.id
