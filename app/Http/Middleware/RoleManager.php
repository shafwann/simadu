<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class RoleManager
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $role  <-- Tambahkan parameter ini
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        // Pengecekan apakah user sudah login dan apakah rolenya sesuai dengan parameter
        if (Auth::check() && Auth::user()->role === $role) {
            return $next($request);
        }

        // Jika tidak sesuai, arahkan kembali ke dashboard dengan pesan error
        return redirect('/dashboard')->with('error', 'Akses ditolak. Anda bukan ' . $role);
    }
}
