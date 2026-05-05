# 🧠 🎯 Tujuan Self-Healing

> **Memperbaiki status order yang masih `pending` padahal user sudah bayar, tanpa bergantung pada webhook atau user action**

---

# 🔁 🔥 FLOW UTAMA (END-TO-END)

## 1. Order dibuat

```text
User checkout
↓
Sistem create VA (BNI)
↓
Order.status = pending
```

---

## 2. Sistem menunggu webhook (jalur normal)

```text
User bayar
↓
BNI kirim webhook
↓
Order → paid ✅
```

👉 ini **best case (tanpa self-healing)**

---

## 3. Masalah terjadi (trigger self-healing)

Kasus kamu:

```text
User bayar
↓
Webhook tidak masuk ❌
↓
Order tetap pending ❌
```

---

# 🧠 4. DETEKSI (Candidate Detection)

Self-healing TIDAK langsung jalan.

👉 sistem dulu harus menentukan:

> “order mana yang mencurigakan?”

---

## ✅ Kriteria kandidat

```text
status = pending
is_fallback_va = false
tidak ada webhook
umur order > X menit (misal 10 menit)
```

---

## Visual:

```text
[Order Pending]
      ↓
[No Webhook]
      ↓
[Age > 10 min]
      ↓
👉 Candidate for Self-Healing
```

---

# ⚙️ 5. TRIGGER (Scheduler jalan)

Scheduler jalan tiap menit:

```text
Cron / Laravel Scheduler
↓
Ambil candidate
↓
Masukkan ke proses self-healing
```

---

# 🔍 6. RECONCILIATION (Inti Self-Healing)

Ini bagian mahal (kena biaya 300/hit):

```text
Sistem hit API BNI (inquiryBilling)
↓
Cek status pembayaran
```

---

## Kemungkinan hasil:

---

### ✅ A. SUDAH BAYAR

```text
BNI → PAID
↓
Order.status → paid
↓
Selesai ✅
```

---

### ⏳ B. BELUM BAYAR

```text
BNI → PENDING
↓
Schedule ulang (retry)
```

---

### ❌ C. ERROR

```text
API gagal / timeout
↓
Retry nanti
```

---

# 🔁 7. RETRY STRATEGY (SELF-HEALING CORE)

Ini yang bikin sistemmu efisien.

---

## Contoh jadwal:

```text
Attempt 1 → 5 menit
Attempt 2 → 30 menit
Attempt 3 → 2 jam
Attempt 4 → 8 jam
```

👉 disebut:

> **exponential / phase-based retry**

---

## Kenapa penting?

* hemat biaya 💰
* tetap robust 💪

---

# 🧩 8. TRIGGER TAMBAHAN (BIAR LEBIH CEPAT RECOVER)

Self-healing tidak hanya dari scheduler.

---

## 🔹 A. User upload bukti

```text
User upload proof
↓
Langsung trigger check
```

---

## 🔹 B. User buka halaman

```text
User buka dashboard
↓
Trigger check
```

---

👉 ini disebut:

> **event-assisted self-healing**

---

# 🛑 9. STOP CONDITION (WAJIB)

Tanpa ini → infinite loop & boros biaya

---

## Stop jika:

```text
status = paid
atau
sudah retry max (misal 5x)
atau
expired
atau
fallback VA
```

---

# 🔥 FLOW FINAL (VERSI RINGKAS)

```text
User bayar
↓
Webhook?
 ├── YA → selesai ✅
 └── TIDAK
        ↓
   Tunggu X menit
        ↓
   Candidate detection
        ↓
   Scheduler trigger
        ↓
   Reconciliation (cek BNI)
        ↓
   ├── PAID → update ✅
   ├── PENDING → retry ⏳
   └── ERROR → retry ⚠️
```

---

# 🧠 Insight penting (ini inti desain kamu)

Self-healing itu:

> ❌ bukan ngecek terus-menerus
> ✅ tapi **ngecek hanya saat perlu**

---

# 📌 Versi 1 kalimat (buat skripsi)

> “Self-healing dilakukan dengan mendeteksi order yang tidak menerima webhook dalam periode tertentu, kemudian melakukan rekonsiliasi status pembayaran ke sistem eksternal secara berkala hingga mencapai konsistensi.”

---

# 🚀 Kalau mau lanjut

Aku bisa bantu:

* bikin **sequence diagram (step-by-step request flow)**
* bikin **diagram MAPE-K khusus sistem kamu**
* atau bantu tulis **BAB 3 (flow + algoritma lengkap)**

Karena sekarang kamu sudah punya:

> **flow self-healing yang sudah production-grade** 🔥
