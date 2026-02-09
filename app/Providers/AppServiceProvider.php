<?php

namespace App\Providers;

use App\Contract\CourseStudentContract;
use App\Services\CourseStudentService;
use Filament\Pages\Page;
use Filament\Support\Enums\Alignment;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(CourseStudentContract::class, CourseStudentService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
        // Page::formActionsAlignment(Alignment::Right);
    }
}
