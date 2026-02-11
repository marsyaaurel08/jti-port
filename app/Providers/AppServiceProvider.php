<?php

namespace App\Providers;

use App\Contract\ClassStudentContract;
use App\Contract\CourseStudentContract;
use App\Http\Responses\LogoutResponse;
use App\Models\UserCart;
use App\Services\ClassStudentService;
use App\Services\CourseStudentService;
use Filament\Auth\Http\Responses\Contracts\LogoutResponse as ContractsLogoutResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
        $this->app->bind(ContractsLogoutResponse::class, LogoutResponse::class);
        $this->app->bind(CourseStudentContract::class, CourseStudentService::class);
        $this->app->bind(ClassStudentContract::class, ClassStudentService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Blade::component('layouts.app', 'layouts.app');

        View::share('categories', \App\Models\Category::all());
        View::composer('*', function ($view) {
            if (Auth::check()) {
                $count = UserCart::where('user_id', Auth::id())->count();
                $view->with('cartCount', $count);
            } else {
                $view->with('cartCount', 0);
            }
        });
        View::composer('*', function ($view) {
            if (Auth::check()) {
                $view->with('user', Auth::user());
            }
        });
    }
}
