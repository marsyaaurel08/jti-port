<?php

namespace App\Providers;

use App\Contract\ClassStudentContract;
use App\Contract\CourseStudentContract;
use App\Http\Responses\LogoutResponse;
use App\Models\Category;
use App\Models\User;
use App\Models\UserCart;
use App\Observers\CategoryObserver;
use App\Observers\UserCartObserver;
use App\Observers\UserObserver;
use App\Services\ClassStudentService;
use App\Services\CourseStudentService;
use BezhanSalleh\LanguageSwitch\LanguageSwitch;
use Filament\Auth\Http\Responses\Contracts\LogoutResponse as ContractsLogoutResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Cache;
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
        LanguageSwitch::configureUsing(function (LanguageSwitch $switch) {
            $switch
                ->locales(['en','id']);
        });
        // Register Model Observers for cache invalidation
        Category::observe(CategoryObserver::class);
        UserCart::observe(UserCartObserver::class);
        User::observe(UserObserver::class);

        Blade::component('layouts.app', 'layouts.app');
        Blade::component('layouts.user', 'layouts.user');


        // Cache categories untuk navbar dengan durasi 24 jam - untuk semua user
        View::composer('*', function ($view) {
            // Categories untuk navbar (di-cache)
            $categories = Cache::remember('categories', 86400, function () {
                return \App\Models\Category::all();
            });

            if (Auth::check()) {
                $user = Auth::user();
                $isStaff = Cache::remember("user_staff_{$user->id}", 3600, function () use ($user) {
                    return $user->hasRole(['super_admin', 'admin', 'pengajar']);
                });

                // Hanya load cart data untuk user biasa (bukan staff)
                if (! $isStaff) {
                    // Cache cart count per user dengan durasi 1 jam
                    $cartCount = Cache::remember("user_cart_{$user->id}", 3600, function () use ($user) {
                        return UserCart::where('user_id', $user->id)->count();
                    });

                    $view->with([
                        'categories' => $categories,
                        'cartCount' => $cartCount,
                        'user' => $user,
                        'isStaff' => false,
                    ]);
                } else {
                    // Untuk staff, share user dan categories tapi tanpa cart count
                    $view->with([
                        'categories' => $categories,
                        'user' => $user,
                        'isStaff' => true,
                    ]);
                }
            } else {
                $view->with([
                    'categories' => $categories,
                    'cartCount' => 0,
                    'isStaff' => false,
                    'user' => null,
                ]);
            }
        });
    }
}
