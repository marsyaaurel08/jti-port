<?php

namespace App\Http\Responses;

use Filament\Auth\Http\Responses\Contracts\LoginResponse as ContractsLoginResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Livewire\Features\SupportRedirects\Redirector; // 1. Import Livewire's Redirector

class LoginResponse implements ContractsLoginResponse
{
    /**
     * Create the response.
     *
     * @param  \Illuminate\Http\Request  $request
     */
    // 2. Update the return type to allow both RedirectResponse and Redirector
    public function toResponse($request): RedirectResponse | Redirector
    {
        $user = auth()->user();

        if ($user->hasAnyRole(['admin', 'super_admin'])) {
            return Redirect::to('/staff');
        }

        return Redirect::to('/staff/class-results');
    }
}
