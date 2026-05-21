<?php

use App\Models\Setting;

if (! function_exists('setting')) {

    function setting(string $key, $default = null)
    {
        return cache()->remember(
            "setting_{$key}",
            now()->addHours(24),
            function () use ($key, $default) {

                return Setting::where('key', $key)
                    ->value('value') ?? $default;
            }
        );
    }
}
