<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('payment:self-healing')
    ->everyFiveMinutes()
    ->withoutOverlapping()      // Cegah proses ganda jika satu siklus memakan > 5 menit
    ->runInBackground()         // Jangan blokir scheduler untuk command lain
    ->onFailure(function () {
        // Opsional: kirim alert ke Slack / email jika command gagal
        // Notification::route('slack', config('services.slack.webhook'))
        //     ->notify(new SelfHealingFailedNotification());
    });
