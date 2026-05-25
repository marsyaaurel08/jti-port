<?php

namespace App\Providers;

use App\Contract\{CourseStudentContract, ClassStudentContract};
use App\Http\Responses\LogoutResponse;
use App\Models\{Category, Order, User, UserCart};
use App\Observers\{CategoryObserver, OrderObserver, UserCartObserver, UserObserver};
use App\Services\{ClassStudentService, CourseStudentService};
use Filament\Auth\Http\Responses\Contracts\LogoutResponse as ContractsLogoutResponse;
use Illuminate\Support\Facades\{Auth, Blade, Cache, URL, View};
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        if ($this->app->environment('local', 'staging')) {  // atau hanya 'local'
            $this->app->register(\App\Providers\TelescopeServiceProviderLocal::class);        }
        $this->app->bind(ContractsLogoutResponse::class, LogoutResponse::class);
        $this->app->bind(CourseStudentContract::class, CourseStudentService::class);
        $this->app->bind(ClassStudentContract::class, ClassStudentService::class);
    }

    public function boot(): void
    {
        // Jika APP_URL menggunakan https, paksa semua link jadi https
        if (str_contains(config('app.env'), 'production') || str_contains(config('app.url'), 'https://')) {
            URL::forceScheme('https');
        }
        // Register Model Observers for cache invalidation
        Category::observe(CategoryObserver::class);
        UserCart::observe(UserCartObserver::class);
        Order::observe(OrderObserver::class);
        User::observe(UserObserver::class);

        Blade::component('layouts.app', 'layouts.app');
        Blade::component('layouts.user', 'layouts.user');

        // Cache categories untuk navbar dengan durasi 24 jam - untuk semua user
        View::composer('*', function ($view) {
            $categories = Cache::remember('categories', 86400, fn() => Category::all());

            if (!Auth::check()) {
                $view->with([
                    'categories' => $categories,
                    'cartCount' => 0,
                    'isStaff' => false,
                    'user' => null,
                ]);
                return;
            }

            $user = Auth::user();
            $user = Cache::remember("user_{$user->id}", 3600, fn() => $user->fresh());
            $isStaff = Cache::remember("user_staff_{$user->id}", 3600, fn() => $user->hasRole(['super_admin', 'admin', 'pengajar']));

            $view->with(array_merge(
                compact('categories', 'user'),
                ['isStaff' => $isStaff]
            ));
        });
    }
}
