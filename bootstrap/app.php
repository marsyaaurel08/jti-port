<?php

use App\Http\Middleware\NonAdmin;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\{Exceptions, Middleware};


return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->redirectGuestsTo(fn() => route('filament.user.auth.login'));
        $middleware->alias([
            'non-admin' => NonAdmin::class,
        ]);
    })
    ->withSchedule(function (Schedule $schedule): void {
        // Clean up old activity logs (older than 365 days)
        $schedule->command('activitylog:clean')->daily();
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (Throwable $e) {
            if ($e instanceof \Illuminate\Auth\AuthenticationException) {
                return redirect()->route('home');
            }
        });
    })->create();
