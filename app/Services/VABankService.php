<?php

namespace App\Services;

use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class VABankService
{
    /**
     * Generate BNI Virtual Account
     */
    public function generateBNIVA(Order $order): array
    {
        // Mock VA Generation - In production, integrate with BNI API
        $vaNumber = $this->generateVANumber($order->id);
        $expiredAt = Carbon::now()->addHours(24);

        // Store VA info to order (you might want to add this to Order model as fields)
        // For now, returning the data to display

        return [
            'va_number' => $vaNumber,
            'bank_code' => '009',
            'bank_name' => 'BNI',
            'account_holder' => $order->user->name,
            'amount' => $order->total_amount,
            'expired_at' => $expiredAt,
            'description' => 'Transfer untuk Invoice '.$order->invoice_number,
            'status' => 'pending',
        ];
    }

    /**
     * Generate unique VA number
     * Format: [Company Code][Customer Code][Invoice Number]
     */
    private function generateVANumber(int $orderId): string
    {
        // Mock format: 9000 (BNI) + Customer Code (5 digits) + Order ID (5 digits)
        $customerId = Auth::id() ?? 1;
        $customerCode = str_pad($customerId, 5, '0', STR_PAD_LEFT);
        $invoiceCode = str_pad($orderId, 5, '0', STR_PAD_LEFT);

        return "9000{$customerCode}{$invoiceCode}";
    }

    /**
     * Verify payment from BNI
     */
    public function verifyPayment(Order $order): bool
    {
        // In production, verify with BNI API
        // For now, just return true for testing
        return true;
    }

    /**
     * Get payment instructions for BNI VA
     */
    public function getBNIInstructions(array $vaData): array
    {
        return [
            'mobile_banking' => [
                'title' => 'BNI Mobile Banking',
                'steps' => [
                    '1. Buka aplikasi BNI Mobile Banking',
                    '2. Pilih menu "Transfer"',
                    '3. Pilih "Virtual Account"',
                    '4. Masukkan nomor VA: '.$vaData['va_number'],
                    '5. Pastikan nominal sesuai: Rp'.number_format($vaData['amount'], 0, ',', '.'),
                    '6. Konfirmasi dan selesaikan transaksi',
                ],
            ],
            'atm' => [
                'title' => 'ATM BNI',
                'steps' => [
                    '1. Masukkan kartu ATM BNI',
                    '2. Pilih menu "Transaksi Lain"',
                    '3. Pilih "Transfer"',
                    '4. Pilih "Ke Virtual Account"',
                    '5. Masukkan nomor VA: '.$vaData['va_number'],
                    '6. Pastikan nominal sesuai: Rp'.number_format($vaData['amount'], 0, ',', '.'),
                    '7. Konfirmasi dan ambil bukti transaksi',
                ],
            ],
            'cross_bank' => [
                'title' => 'Transfer Antar Bank',
                'steps' => [
                    '1. Login ke aplikasi/internet banking bank Anda',
                    '2. Pilih "Transfer Antar Bank"',
                    '3. Kode bank BNI: 009',
                    '4. Nomor rekening (VA): '.$vaData['va_number'],
                    '5. Atas nama: '.$vaData['account_holder'],
                    '6. Nominal: Rp'.number_format($vaData['amount'], 0, ',', '.'),
                    '7. Konfirmasi dan selesaikan',
                ],
            ],
            'indomaret' => [
                'title' => 'Bayar di Indomaret',
                'steps' => [
                    '1. Datang ke toko Indomaret terdekat',
                    '2. Katakan ingin membayar BNI Virtual Account',
                    '3. Berikan nomor VA: '.$vaData['va_number'],
                    '4. Sebutkan jumlah: Rp'.number_format($vaData['amount'], 0, ',', '.'),
                    '5. Serahkan uang dan simpan bukti pembayaran',
                ],
            ],
        ];
    }
}
