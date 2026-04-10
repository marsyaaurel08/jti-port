<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class NonAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        // Cek apakah user adalah admin (super_admin atau admin)
        if ($user->hasRole(['super_admin', 'admin', 'pengajar'])) {
            abort(403, 'Akses ditolak. Halaman ini hanya untuk student.');
        }

        return $next($request);
    }
}
