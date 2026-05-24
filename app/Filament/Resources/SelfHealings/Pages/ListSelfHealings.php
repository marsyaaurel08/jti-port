<?php

namespace App\Filament\Resources\SelfHealings\Pages;

use App\Filament\Resources\SelfHealings\SelfHealingResource;
use App\Filament\Widgets\CandidateSelfHealingExplanation;
use App\Models\Order;
use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Override;

class ListSelfHealings extends ListRecords
{
    protected static string $resource = SelfHealingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('logs_self_healings')
                ->label('Lihat Log Self-Healing')
                ->livewireClickHandlerEnabled(false)
                ->url(route('filament.admin.resources.log-self-healings.index')),
        ];
    }

    public function getTitle(): string
    {
        return 'Kandidat Self-Healing';
    }
    protected function getHeaderWidgets(): array
    {
        return [
            CandidateSelfHealingExplanation::make(),
        ];
    }

    /**
     * Override query untuk fetch self-healing candidates berdasarkan logic detectCandidates
     *
     * Backoff schedule untuk reference:
     * - Attempt 1 → 5 menit
     * - Attempt 2 → 30 menit
     * - Attempt 3 → 90 menit
     * - Attempt 4 → 300 menit
     * - Attempt 5 → 600 menit
     * - Attempt 6 → 1440 menit
     */
    #[Override]
    protected function getTableQuery(): Builder|Relation|null
    {
        $backoffMinutes = [
            1 => 5,
            2 => 30,
            3 => 90,
            4 => 300,
            5 => 600,
            6 => 1440,
        ];

        $maxAttempts = 6;

        // minimal umur order = 5 menit
        $minAge = now()->subMinutes($backoffMinutes[1]);

        // maksimal umur order = 24 jam + 1 jam buffer
        $maxAge = now()
            ->subMinutes($backoffMinutes[$maxAttempts])
            ->subHour();

        return Order::query()
            ->where('status', 'pending')
            ->whereIn('payment_gateway', ['bni', 'midtrans'])
            ->where('is_fallback_va', false)

            // belum ada webhook
            ->whereDoesntHave('paymentWebhooks')

            // umur order antara 5 menit - 25 jam
            ->whereBetween('created_at', [$maxAge, $minAge])

            // retry logic
            ->where(function ($q) use ($maxAttempts) {

                // belum pernah self healing
                $q->whereDoesntHave('selfHealingLogs')

                    // atau masih bisa retry
                    ->orWhereHas('selfHealingLogs', function ($sub) use ($maxAttempts) {

                        $sub->where('attempt', '<', $maxAttempts)
                            ->where(function ($s) {
                                $s->whereNull('result')
                                    ->orWhere('result', '!=', 'paid');
                            });
                    });
            })

            ->with([
                'user',
                'selfHealingLogs',
            ])

            ->withCount('selfHealingLogs')

            ->latest();
    }
}
