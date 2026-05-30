# Implementasi Pending Order Modal

## Deskripsi

Modal informatif yang muncul ketika user memiliki pesanan pending untuk produk yang sama, memberikan pilihan untuk membatalkan pesanan lama atau menutup modal.

## File yang Telah Dibuat/Diupdate

### 1. Modal Component

**File:** `resources/views/components/classes/pending-order-modal.blade.php`

- Modal informatif dengan Alpine.js
- Menampilkan detail order pending (ID, nominal, metode pembayaran, waktu pesan)
- 2 tombol: "Batalkan" (tutup modal) dan "Lanjutkan Checkout" (batalkan order lama & redirect)
- Loading state saat memproses

### 2. Halaman Show

**File:** `resources/views/pages/classes/show.blade.php`

- Menambahkan component modal di bagian akhir
- Modal hanya render jika ada `$pendingOrder`

### 3. Mobile Checkout Bar

**File:** `resources/views/components/classes/mobile-checkout-bar.blade.php` & `resources/views/components/course/mobile-checkout-bar.blade.php`

- Sudah menerapkan `$is_user_enrolled` logic dari `purchase-card.blade.php`
- Tombol dinamis: "Lanjutkan Belajar" (jika enrolled) atau "Checkout Sekarang" (jika belum)

## Implementasi Backend yang Dibutuhkan

### 1. Update ClassController (show method)

```php
public function show($slug)
{
    $class = Class::where('slug', $slug)->firstOrFail();
    $is_user_enrolled = auth()->check()
        ? auth()->user()->enrolledClasses()->where('class_id', $class->id)->exists()
        : false;

    // Cek pending order
    $pendingOrder = null;
    if (auth()->check()) {
        $pendingOrder = Order::where('user_id', auth()->id())
            ->where('class_id', $class->id)
            ->where('status', 'pending')
            ->latest()
            ->first();
    }

    return view('pages.classes.show', [
        'class' => $class,
        'is_user_enrolled' => $is_user_enrolled,
        'pendingOrder' => $pendingOrder,
    ]);
}
```

### 2. Buat Route Baru (routes/web.php)

```php
Route::middleware('auth')->group(function () {
    Route::post('/student/cancel-pending-order', [StudentController::class, 'cancelPendingOrder'])
        ->name('student.cancel-pending-order');
});
```

### 3. Implementasi Controller Method

```php
public function cancelPendingOrder(Request $request)
{
    $validated = $request->validate([
        'order_id' => 'required|integer',
        'class_slug' => 'required|string',
    ]);

    // Cari order
    $order = Order::findOrFail($validated['order_id']);

    // Verifikasi ownership & status
    if ($order->user_id !== auth()->id() || $order->status !== 'pending') {
        return response()->json([
            'success' => false,
            'message' => 'Pesanan tidak valid.',
        ], 403);
    }

    // Batalkan order
    $order->update(['status' => 'cancelled']);

    // Activity log (jika ada)
    activity('Order')
        ->causedBy(auth()->user())
        ->performedOn($order)
        ->withProperties(['reason' => 'User membatalkan untuk order baru'])
        ->log('cancelled');

    // Redirect ke checkout
    return response()->json([
        'success' => true,
        'redirect_url' => route('student.checkout.class', $validated['class_slug']),
    ]);
}
```

## Data Structure

Modal mengharapkan `$pendingOrder` dengan attributes:

- `id` - Order ID
- `order_number` - Nomor pesanan
- `total_amount` - Jumlah pembayaran
- `payment_method` - Metode pembayaran (bni_transfer, midtrans, dll)
- `status` - Status pesanan
- `created_at` - Waktu pemesanan

## Flow User

1. User membuka halaman kelas
2. Jika sudah authenticated dan ada pending order untuk kelas ini → Modal muncul otomatis
3. User dapat:
    - **Batalkan**: Modal tertutup, user bisa explore halaman lagi
    - **Lanjutkan Checkout**: Order lama dibatalkan, redirect ke checkout baru

## Styling & UX Considerations

✅ **Informatif** - Menampilkan semua detail order dengan jelas
✅ **Responsive** - Bekerja di mobile dan desktop
✅ **Loading State** - Menampilkan spinner saat memproses
✅ **Accessible** - Dapat ditutup dengan ESC atau klik backdrop
✅ **Alert Info** - Memberitahu bahwa order lama akan dibatalkan

## Catatan

- Modal hanya render jika ada pending order
- Route name `student.cancel-pending-order` harus disesuaikan dengan routing Anda
- Pastikan CSRF token tersedia di view
