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
     * @param  string  ...$roles
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $roles = $roles === [] ? [''] : $roles;

        // Pengecekan apakah user sudah login dan apakah rolenya sesuai dengan parameter
        if (Auth::check() && in_array(Auth::user()->role, $roles, true)) {
            return $next($request);
        }

        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json([
                'message' => 'Akses ditolak.',
            ], 403);
        }

        // Jika tidak sesuai, arahkan kembali ke dashboard dengan pesan error
        return redirect('/dashboard')->with('error', 'Akses ditolak.');
    }
}
