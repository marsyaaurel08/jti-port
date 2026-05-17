<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'invoice_number',
        'external_id',       // Bisa digunakan untuk trx_id dari Midtrans / ref ID BNI
        'snap_token',        // (BARU) Wajib untuk Midtrans Snap
        'payment_gateway',   // (BARU) Isi: 'bni', 'midtrans', 'manual'
        'payment_method',    // Isi: 'bca_va', 'qris', 'gopay', 'bni_va'
        'va_number',
        'va_expired_at',
        'is_fallback_va',
        'subtotal',
        'discount_amount',
        'total_amount',
        'status',            // Sebaiknya distandarisasi (misal: 'pending', 'paid', 'cancelled')
        'payment_proof',
        'paid_at',
        'last_inquiry_at',
        'inquiry_count',
    ];

    protected function casts(): array
    {
        return [
            'subtotal' => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'total_amount' => 'decimal:2',
            'paid_at' => 'datetime',
            'va_expired_at' => 'datetime',
            'last_inquiry_at' => 'datetime',
            'deleted_at' => 'datetime',
            'is_fallback_va' => 'boolean',
            'inquiry_count' => 'integer',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | RELATIONSHIPS
    |--------------------------------------------------------------------------
    */

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }

    /*
    |--------------------------------------------------------------------------
    | HELPER METHODS
    |--------------------------------------------------------------------------
    */

    // public function getVaData(): ?array
    // {
    //     // Midtrans mungkin memakai QRIS/GoPay yang tidak punya VA
    //     if (!$this->va_number) {
    //         return null;
    //     }

    //     return [
    //         'va_number'      => $this->va_number,
    //         'bank_code'      => $this->getBankCode(),
    //         'bank_name'      => $this->getBankName(),
    //         'account_holder' => $this->user->name,
    //         'amount'         => $this->total_amount,
    //         'expired_at'     => optional($this->va_expired_at)?->toIso8601String(),
    //         'description'    => "Transfer untuk Invoice {$this->invoice_number}",
    //         'status'         => $this->status,
    //         'is_fallback_va' => $this->is_fallback_va,
    //         'response_code'  => $this->is_fallback_va ? 'FALLBACK' : null,
    //         'response_message' => $this->is_fallback_va
    //             ? 'Menggunakan Virtual Account Backup - Verifikasi Manual Diperlukan'
    //             : null,
    //     ];
    // }

    /**
     * Relasi ke log self-healing.
     * Digunakan oleh SelfHealingService untuk menghitung attempt
     * dan menentukan apakah order sudah due untuk di-cek ulang.
     */
    public function selfHealingLogs(): HasMany
    {
        return $this->hasMany(LogSelfHealing::class);
    }

    /**
     * Relasi ke webhook yang masuk dari payment gateway.
     * Digunakan oleh SelfHealingService sebagai sinyal bahwa
     * webhook sudah pernah masuk — sehingga order tidak perlu di-heal.
     */
    public function paymentWebhooks(): HasMany
    {
        return $this->hasMany(PaymentWebhook::class, 'order_invoice_number', 'invoice_number');
    }

    /**
     * Helper: kembalikan data VA untuk ditampilkan di UI.
     * Dipanggil di Livewire component setelah createTransaction() BNI.
     *
     * @return array{va_number: string|null, va_expired_at: string|null, total_amount: string}
     */
    public function getVaData(): array
    {
        return [
            'va_number' => $this->va_number,
            'va_expired_at' => $this->va_expired_at?->format('d M Y H:i'),
            'total_amount' => number_format($this->total_amount, 0, ',', '.'),
        ];
    }

    public function getBankCode(): ?string
    {
        // Sesuaikan dengan gateway atau method yang spesifik
        if ($this->payment_gateway === 'bni') {
            return '009';
        }

        // Jika menggunakan Midtrans, kodenya bergantung pada payment_method
        return match ($this->payment_method) {
            'bca_va' => '014',
            'mandiri_va' => '008',
            'bni_va' => '009',
            'bri_va' => '002',
            default => null,
        };
    }

    public function getBankName(): ?string
    {
        if ($this->payment_gateway === 'bni') {
            return 'BNI';
        }

        if ($this->payment_gateway === 'manual') {
            return 'Manual Verifikasi';
        }

        // Midtrans bisa menangani banyak channel
        if ($this->payment_gateway === 'midtrans') {
            return match ($this->payment_method) {
                'bca_va' => 'BCA',
                'mandiri_va' => 'Mandiri',
                'bni_va' => 'BNI',
                'bri_va' => 'BRI',
                'qris' => 'QRIS',
                'gopay' => 'GoPay',
                default => 'Midtrans (Lainnya)',
            };
        }

        return null;
    }
}
