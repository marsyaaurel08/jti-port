<?php

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;

if (! function_exists('setting')) {

    function setting(string $key, $default = null)
    {
        return Cache::remember(
            "setting_{$key}",
            now()->addHours(24),
            function () use ($key, $default) {

                return Setting::where('key', $key)
                    ->value('value') ?? $default;
            }
        );
    }
}
