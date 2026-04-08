# BNI Credentials - Panduan Pengisian

## 📍 Lokasi Credentials di BNI Dashboard

Anda sudah benar! Credentials yang diberikan BNI terbagi menjadi:

### 1️⃣ **Kredensial OAuth** (di BNI Dashboard)

```
Applications > [Nama Aplikasi Anda] > Credentials > OAuth Credentials
```

Anda akan melihat:

- **Client ID** → Copy ke `BNI_CLIENT_ID`
- **Client Secret** → Copy ke `BNI_CLIENT_SECRET`

### 2️⃣ **Kunci API** (di BNI Dashboard)

```
Applications > [Nama Aplikasi Anda] > Credentials > API Keys
```

Anda akan melihat:

- **API Key** → Copy ke `BNI_API_KEY`
- **API Secret** → Copy ke `BNI_API_SECRET`

---

## 🔧 Cara Mengisi `.env`

### Step 1: Buka BNI Dashboard

- Login ke https://digitalservices.bni.co.id
- Navigasi ke **Applications** → **[Nama Aplikasi Anda]**

### Step 2: Copy OAuth Credentials

Lihat menu **Credentials** > **OAuth Credentials**:

```
📋 OAuth Credentials
├── Client ID:     xxxxx-xxxxx-xxxxx-xxxxx-xxxxx
└── Client Secret: aaaa-bbbb-cccc-dddd-eeee
```

Buka file `.env` di project Anda:

```env
# .env

BNI_ENVIRONMENT=sandbox

# OAuth Credentials (Kredensial OAuth)
BNI_CLIENT_ID=xxxxx-xxxxx-xxxxx-xxxxx-xxxxx        ← Paste Client ID di sini
BNI_CLIENT_SECRET=aaaa-bbbb-cccc-dddd-eeee         ← Paste Client Secret di sini

# API Credentials (Kunci API)
BNI_API_KEY=
BNI_API_SECRET=
```

### Step 3: Copy API Key Credentials

Di menu yang sama, lihat **Credentials** > **API Keys**:

```
📋 API Keys
├── API Key:    11111111-2222-3333-4444-555555555555
└── API Secret: zzzz-yyyy-xxxx-wwww-vvvv
```

Lanjutkan mengisi `.env`:

```env
# API Credentials (Kunci API)
BNI_API_KEY=11111111-2222-3333-4444-555555555555    ← Paste API Key di sini
BNI_API_SECRET=zzzz-yyyy-xxxx-wwww-vvvv            ← Paste API Secret di sini
```

### Step 4: Simpan & Test

Setelah mengisi semua credentials:

```bash
# Clear config cache (penting!)
php artisan config:clear

# Test koneksi dengan tinker
php artisan tinker
>>> config('bni.client_id')
>>> config('bni.api_key')
# Harusnya menunjukkan value yang Anda masukkan
```

---

## 📝 Contoh File `.env` yang Sudah Lengkap

```env
APP_NAME=jtiport
APP_ENV=local
APP_KEY=base64:k9u8ye4ZZjCjcw97acgyrRQIYihIqPpH3J4X1rsxygI=
APP_DEBUG=true
APP_URL=http://localhost

# ... konfigurasi lainnya ...

# BNI Ecollection API Configuration
BNI_ENVIRONMENT=sandbox

# OAuth Credentials (dari BNI Dashboard > Credentials > OAuth Credentials)
BNI_CLIENT_ID=8-000
BNI_CLIENT_SECRET=Y2xpZW50X3NlY3JldF8xMjM0NTY3ODkw

# API Credentials (dari BNI Dashboard > Credentials > API Keys)
BNI_API_KEY=api_key_1234567890abcdef
BNI_API_SECRET=api_secret_abcdefghijklmnop

# Webhook URL untuk callback dari BNI
BNI_WEBHOOK_URL=http://localhost/api/payments/bni-callback
```

---

## ⚠️ Penting: Environment Sandbox vs Production

### Development / Testing (Sandbox)

```env
BNI_ENVIRONMENT=sandbox
BNI_CLIENT_ID=your_sandbox_client_id
BNI_CLIENT_SECRET=your_sandbox_secret
BNI_API_KEY=your_sandbox_api_key
BNI_API_SECRET=your_sandbox_api_secret
```

### Production

```env
BNI_ENVIRONMENT=prod
BNI_CLIENT_ID=your_production_client_id
BNI_CLIENT_SECRET=your_production_secret
BNI_API_KEY=your_production_api_key
BNI_API_SECRET=your_production_api_secret
```

---

## 🧪 Test Credentials

Setelah mengisi `.env`, test dengan command:

```bash
# Clear cache dan test
php artisan config:clear

# Akses tinker
php artisan tinker

# Cek apakah credentials terbaca
>>> config('bni.environment')
=> "sandbox"

>>> config('bni.client_id')
=> "8-000"        ✓ Harus menampilkan value Anda

>>> config('bni.api_key')
=> "api_key_..."  ✓ Harus menampilkan value Anda

# Exit tinker
>>> exit
```

Jika semua menampilkan value yang Anda masukkan, maka credentials sudah benar! ✅

---

## 🔐 Keamanan (Tips Penting)

### ❌ JANGAN:

- Commit file `.env` ke Git
- Share credentials di forum/chat publik
- Hardcode credentials di dalam code

### ✅ LAKUKAN:

- Simpan credentials di `.env` lokal saja
- Gunakan `.env.production` untuk production
- Rotate credentials secara berkala
- Use environment file yang aman di production (server env vars)

---

## 📞 Jika Ada Masalah

### Error: "Client credentials not found"

```bash
php artisan config:clear
php artisan cache:clear
# Pastikan .env benar syntax-nya (tidak ada spasi sebelum/sesudah `=`)
```

### Error: "Invalid credentials"

- Double-check bahwa Anda copy dari aplikasi yang **sama** di BNI dashboard
- Cek apakah aplikasi sudah ter-subscribe ke Ecollection product
- Coba regenerate API Key / Client Secret di BNI dashboard

### Error: "CORS atau HTTPS"

- Jika testing di localhost, gunakan `BNI_ENVIRONMENT=sandbox`
- Untuk production, pastikan domain menggunakan HTTPS
- Register webhook URL di BNI dashboard sesuai domain yang digunakan

---

## 🚀 Next Steps

Setelah credentials siap:

1. **Run migration**

    ```bash
    php artisan migrate
    ```

2. **Test checkout flow**

    ```
    http://localhost:8000/student/course/checkout/nama-kursus
    ```

3. **Monit logs**

    ```bash
    php artisan pail
    ```

4. **Run tests**
    ```bash
    php artisan test tests/Feature/Payment/
    ```

Good luck! 🎉
