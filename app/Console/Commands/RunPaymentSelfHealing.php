<?php

namespace App\Console\Commands;

use App\Services\Payment\SelfHealingService;
use Illuminate\Console\Command;
use Throwable;

class RunPaymentSelfHealing extends Command
{
    protected $signature = 'payment:self-healing';

    protected $description = 'Cek ulang status pembayaran order pending yang belum menerima webhook.';

    public function handle(SelfHealingService $service): int
    {
        $this->info('[Self-Healing] Memulai proses...');

        try {
            $service->run();
            $this->info('[Self-Healing] Selesai.');

            return Command::SUCCESS;

        } catch (Throwable $e) {
            $this->error('[Self-Healing] Error tidak terduga: '.$e->getMessage());

            return Command::FAILURE;
        }
    }
}
