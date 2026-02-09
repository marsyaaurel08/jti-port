<?php

namespace App\Providers;

use App\Contract\CourseStudentContract;
use App\Services\CourseStudentService;
use Filament\Pages\Page;
use Filament\Support\Enums\Alignment;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;

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
        Blade::component('layouts.app', 'layouts.app');
        //
        // Page::formActionsAlignment(Alignment::Right);

        // ini dummy kalo udah pake yang bawah aja
        // View::share('categories', [
        //     (object)['id' => 1, 'name' => 'Web Development', 'slug' => 'web-dev'],
        //     (object)['id' => 2, 'name' => 'Mobile Development', 'slug' => 'mobile-dev'],
        //     (object)['id' => 3, 'name' => 'Data Science', 'slug' => 'data-science'],
        //     (object)['id' => 4, 'name' => 'Cloud Computing', 'slug' => 'cloud-computing'],
        // ]);

        View::share('categories', \App\Models\Category::all());
    }
}
