<?php

namespace App\Http\Responses;

use Filament\Auth\Http\Responses\Contracts\LogoutResponse as ContractsLogoutResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;

class LogoutResponse implements ContractsLogoutResponse
{
    /**
     * Create the response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function toResponse($request): RedirectResponse
    {
        // Mengarahkan ke halaman home (/) sesuai permintaan Anda
        return Redirect::to('/');
    }
}
