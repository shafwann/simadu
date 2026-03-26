<?php

namespace App\Http\Middleware;

use App\Models\Visit;
use Closure;
use Illuminate\Http\Request;

class TrackVisits
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // Catat hanya request GET
        if ($request->isMethod('get')) {

            // (Optional) Abaikan asset / file statis
            $path = $request->path() ?: '/';
            if (str_starts_with($path, 'storage') || str_starts_with($path, 'build')) {
                return $response;
            }

            $ip = $request->ip();
            $userId = optional($request->user())->id;

            // Anti spam: 1 IP + 1 path dicatat maksimal 1x per 10 menit
            $recent = Visit::where('ip', $ip)
                ->where('path', $path)
                ->where('created_at', '>=', now()->subMinutes(10))
                ->exists();

            if (!$recent) {
                Visit::create([
                    'path' => $path,
                    'method' => $request->method(),
                    'ip' => $ip,
                    'user_agent' => substr((string) $request->userAgent(), 0, 500),
                    'user_id' => $userId,
                ]);
            }
        }

        return $response;
    }
}
