<?php

namespace App\Filament\Resources\OrdersHistories\Actions;

use Filament\Actions\Action;
use Filament\Notifications\Notification;

class VerifyPaymentAction extends Action
{
    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label('Verifikasi')
            ->icon('heroicon-m-check-badge')
            ->color('success')
            ->visible(fn ($record) => $record->status === 'pending' && $record->payment_proof)
            ->modalHeading('Verifikasi Pembayaran Manual')
            ->modalIcon('heroicon-m-check-badge')
            ->modalWidth('md')
            ->requiresConfirmation()
            ->modalSubmitActionLabel('Ya, Verifikasi')
            ->modalCancelActionLabel('Batal')
            ->modalDescription('Periksa bukti pembayaran di bawah, kemudian klik "Ya, Verifikasi" untuk mengkonfirmasi pembayaran ini.')
            ->action(function ($record) {
                try {
                    $record->update([
                        'status' => 'paid',
                        'paid_at' => now(),
                    ]);

                    Notification::make()
                        ->success()
                        ->title('Pembayaran Terverifikasi ✓')
                        ->body("Invoice {$record->invoice_number} telah diverifikasi. Status berubah menjadi Dibayar.")
                        ->send();
                } catch (\Exception $e) {
                    Notification::make()
                        ->danger()
                        ->title('Gagal Memverifikasi')
                        ->body('Terjadi kesalahan: '.$e->getMessage())
                        ->send();
                }
            });
    }

    public static function make(?string $name = null): static
    {
        return app(static::class, ['name' => $name ?? 'verifyPayment']);
    }
}
