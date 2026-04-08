# BNI Integration - Environment Setup Guide

## .env Example untuk Development (Sandbox)

```env
# BNI Ecollection Configuration - SANDBOX
BNI_ENVIRONMENT=sandbox
BNI_CLIENT_ID=8-000        # Format: PREFIX-CLIENTID (contoh: 8-000)
BNI_CLIENT_SECRET=your_sandbox_secret_key_here
BNI_API_KEY=
BNI_API_SECRET=

# Default endpoints (jangan diubah kecuali khusus)
BNI_SNAP_BASE_URL=https://app.sandbox.midtrans.com/snap/v1
BNI_OGP_BASE_URL=https://sandbox.bni.co.id/api/v1
BNI_BNIDIRECT_BASE_URL=https://sandbox.bni.co.id/api/v1

# Webhook callback URL
BNI_WEBHOOK_URL=http://127.0.0.1:8000/api/payments/bni-callback
```

## .env Example untuk Production

```env
# BNI Ecollection Configuration - PRODUCTION
BNI_ENVIRONMENT=prod
BNI_CLIENT_ID=your_production_client_id
BNI_CLIENT_SECRET=your_production_secret_key
BNI_API_KEY=
BNI_API_SECRET=

# Production endpoints
BNI_SNAP_BASE_URL=https://app.midtrans.com/snap/v1
BNI_OGP_BASE_URL=https://api.bni.co.id/api/v1
BNI_BNIDIRECT_BASE_URL=https://api.bni.co.id/api/v1

# Webhook callback URL (must be HTTPS)
BNI_WEBHOOK_URL=https://yourdomain.com/api/payments/bni-callback
```

## Cara Mendapatkan Credentials

### 1. Daftar di BNI Digital Services

- Buka https://digitalservices.bni.co.id
- Register atau login dengan akun BNI Anda
- Lengkapi data perusahaan

### 2. Buat Aplikasi

- Navigasi ke "Applications" / "Aplikasi"
- Klik "Create New Application"
- Isi detail aplikasi:
    - Application Name: "JTI Port Payment System"
    - Description: "Virtual Account payment gateway untuk checkout kursus"
    - Callback URL: `https://yourdomain.com/api/payments/bni-callback`

### 3. Subscribe ke Ecollection Product

- Di dashboard aplikasi, pilih "Products"
- Cari "Ecollection" / "Virtual Account"
- Klik "Subscribe"
- Setujui terms & conditions

### 4. Ambil Credentials

- Buka Applications > [Nama Aplikasi]
- Tab "Credentials" / "API Keys"
- Copy:
    - **Client ID**: Format `PREFIX-CLIENTID` (contoh: `8-000`)
    - **Client Secret**: Secret key untuk authentication

### 5. Setup Webhook di Dashboard

- Di aplikasi BNI, buka "Webhooks" / "Callback Settings"
- Tambah webhook URL:
    ```
    https://yourdomain.com/api/payments/bni-callback
    ```
- Pilih events:
    - ✓ Payment Received (Pembayaran Diterima)
    - ✓ Payment Expired (Pembayaran Kadaluarsa)

## Testing di Sandbox Environment

### 1. Setup Local Environment

```bash
# Clone project
git clone <your-repo>
cd jti-port

# Install dependencies
composer install
npm install

# Setup .env dengan sandbox credentials
cp .env.example .env
# Edit .env dengan sandbox BNI credentials

# Generate app key
php artisan key:generate

# Run migrations
php artisan migrate

# Start dev server
php artisan serve
# Atau
composer run dev
```

### 2. Test Virtual Account Creation

```bash
# Akses checkout page
http://127.0.0.1:8000/student/course/checkout/nama-kursus

# Fill form:
- Nama Lengkap: Test User
- Email: test@example.com
- Nomor HP: 081234567890 (format: tanpa +62)
- Pilih metode transfer: BNI Mobile Banking

# Klik "Bayar sekarang"
# Sistem akan membuat VA di BNI sandbox
```

### 3. Verify dengan Tinker

```bash
php artisan tinker

# Cek order terbaru
>>> \App\Models\Order::latest()->first();

# Cek BNI fields
>>> $order = \App\Models\Order::latest()->first();
>>> $order->bni_va_number;
>>> $order->bni_transaction_id;
>>> $order->bni_va_expired_at;
```

### 4. Test Webhook Locally

```bash
# Gunakan tools seperti Postman atau curl

# Test successful payment callback
curl -X POST http://127.0.0.1:8000/api/payments/bni-callback \
  -H "Content-Type: application/json" \
  -d '{
    "trxId": "ORD1680000001000001",
    "trxAmount": "100000",
    "paymentStatus": "APPROVED",
    "paymentTime": "2024-04-07T10:30:00+07:00"
  }'

# Response:
# {"status":"success","message":"Payment confirmed"}
```

### 5. Monitor Logs

```bash
# Real-time log monitoring
php artisan pail

# Atau check file
tail -f storage/logs/laravel.log
```

## Sandbox Test Data

### Virtual Account Numbers (Format)

- BNI generates format: `9000[bank][customer][invoice]`
- Tidak perlu dipaksa, BNI sandbox akan auto-generate

### Payment Status Values

- `APPROVED` atau `1` - Payment diterima ✓
- `PENDING` atau `2` - Menunggu pembayaran
- `FAILED` atau `3` - Pembayaran gagal
- `EXPIRED` atau `4` - VA kadaluarsa

### Test Amounts

- Sandbox menerima amount apapun (tidak ada minimum)
- Gunakan nominal sesuai course price di database

## Troubleshooting Setup

### Error: "Invalid Client ID Format"

**Fix:** Client ID harus format `PREFIX-ID`, contoh `8-000`

### Error: "Credentials not found in config"

```bash
# Clear cache
php artisan config:clear
php artisan cache:clear

# Verify .env variables
php artisan tinker
>>> config('bni.client_id')
>>> config('bni.client_secret')
```

### Error: "Callback URL tidak match"

- Pastikan URL di `.env` sama dengan di BNI dashboard
- Testing locally: gunakan ngrok untuk expose localhost
    ```bash
    ngrok http 8000
    # Update BNI_WEBHOOK_URL dan dashboard dengan ngrok URL
    ```

### Error: "Certificate verificati failed"

```bash
# Untuk development only:
# Edit app/Services/BNIService.php temporarily:
// 'verify' => false,  // JANGAN di production!
```

## Deployment ke Production

### Pre-deployment Checklist

- [ ] Request production credentials dari BNI
- [ ] Setup HTTPS certificate di production server
- [ ] Configure webhook URL di BNI production dashboard
- [ ] Update `.env` dengan production credentials
- [ ] Test payment flow sekali di production sandbox
- [ ] Setup error monitoring (Sentry, etc)
- [ ] Configure auto-retry untuk failed payments

### Production Deployment Steps

```bash
# 1. Setup environment
cp .env.production .env

# 2. Run migrations (jika ada table changes)
php artisan migrate --force

# 3. Clear caches
php artisan config:clear
php artisan cache:clear
php artisan route:clear

# 4. Test webhook berjalan
curl -X POST https://{your-domain}/api/payments/bni-callback \
  -H "Content-Type: application/json" \
  -d '{"trxId":"TEST","trxAmount":"100000","paymentStatus":"APPROVED"}'

# 5. Monitor logs setelah go-live
tail -f /path/to/storage/logs/laravel.log
```

---

## Support Contact

- **BNI Support:** apisupport@bni.co.id
- **BNI Docs:** https://digitalservices.bni.co.id/documentation
- **Project Docs:** `/docs/BNI_INTEGRATION_IMPLEMENTATION.md`
