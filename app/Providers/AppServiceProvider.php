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
    public function register(): void
    {
        $this->app->bind(ContractsLogoutResponse::class, LogoutResponse::class);
        $this->app->bind(CourseStudentContract::class, CourseStudentService::class);
        $this->app->bind(ClassStudentContract::class, ClassStudentService::class);
    }

    public function boot(): void
    {
        Blade::component('layouts.app', 'layouts.app');

        // Share categories untuk navbar
        View::share('categories', \App\Models\Category::all());

        View::composer('*', function ($view) {
            if (Auth::check()) {
                $user = Auth::user();

                // Hitung Cart
                $count = UserCart::where('user_id', $user->id)->count();

                $view->with([
                    'cartCount' => $count,
                    'user'      => $user,
                    // Tambahkan variabel isStaff (Admin & Pengajar)
                    'isStaff'   => $user->hasRole(['super_admin', 'admin', 'pengajar']),
                ]);
            } else {
                $view->with([
                    'cartCount' => 0,
                    'isStaff'   => false,
                    'user'      => null,
                ]);
            }
        });
    }
}
