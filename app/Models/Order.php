<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Model Order
 *
 * Model ini merepresentasikan sebuah pesanan (order) yang dibuat oleh user.
 * Digunakan untuk menyimpan informasi transaksi, pembayaran, dan status order.
 *
 * @property int $id
 * @property int $user_id
 * @property string $invoice_number
 * @property float $subtotal
 * @property float $discount_amount
 * @property float $total_amount
 * @property string $status
 * @property string|null $payment_method
 * @property string|null $payment_proof
 * @property \Carbon\Carbon|null $paid_at
 * @property string|null $bni_transaction_id
 * @property string|null $bni_va_number
 * @property \Carbon\Carbon|null $bni_va_expired_at
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 *
 * @property-read \App\Models\User $user
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\OrderItem> $orderItems
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Enrollment> $enrollments
 *
 * @package App\Models
 */
class Order extends Model
{
    use HasFactory;

    /**
     * Kolom yang boleh diisi secara massal (mass assignable).
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'invoice_number',
        'subtotal',
        'discount_amount',
        'total_amount',
        'status',
        'payment_method',
        'payment_proof',
        'paid_at',
        'bni_transaction_id',
        'bni_va_number',
        'bni_va_expired_at',
    ];

    /**
     * Casting attribute untuk kolom tertentu.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'subtotal'      => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'total_amount'  => 'decimal:2',
            'paid_at'       => 'datetime',
            'bni_va_expired_at' => 'datetime',
        ];
    }

    /**
     * Relasi ke model User (pemilik order).
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi ke OrderItem (detail barang/jasa dalam order).
     *
     * Satu order dapat memiliki banyak item.
     *
     * @return HasMany
     */
    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Relasi ke Enrollment (pendaftaran kursus/materi setelah pembayaran berhasil).
     *
     * Satu order dapat menghasilkan banyak enrollment.
     *
     * @return HasMany
     */
    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }

    public function getVaData(): array
    {
        return [
            'va_number' => $this->bni_va_number,
            'bank_code' => '009',
            'bank_name' => 'BNI',
            'account_holder' => $this->user->name,
            'amount' => $this->total_amount,
            'expired_at' => $this->bni_va_expired_at->toIso8601String(),
            'description' => "Transfer untuk Invoice {$this->invoice_number}",
            'status' => $this->status,
            'is_fallback_va' => $this->is_fallback_va,
            'response_code' => 'FALLBACK',
            'response_message' => 'Menggunakan Virtual Account Backup - Verifikasi Manual Diperlukan',
        ];
    }
}
