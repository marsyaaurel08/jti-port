<?php

namespace App\Filament\Pages;

use Filament\Auth\Pages\Login as BaseLogin;

class CustomLogin extends BaseLogin
{
    protected function rateLimit($maxAttempts, $decaySeconds = 60, $method = null, $component = null)
    {
        // Disabled for load testing
    }
}
