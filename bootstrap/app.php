<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Exceptions\PostTooLargeException;
use App\Http\Middleware\RoleManager;
use App\Http\Middleware\NoCache; // TAMBAHKAN INI
use App\Http\Middleware\TrackVisits;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function ($middleware) {
        $middleware->alias([
            'role' => RoleManager::class,
            'no-cache' => NoCache::class, // TAMBAHKAN INI
            'track.visits' => TrackVisits::class,
        ]);

        $middleware->validateCsrfTokens(except: [
            'api/auth/login',
            'api/auth/logout',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (PostTooLargeException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Ukuran file melebihi batas upload server.',
                ], 413);
            }

            return back()->withInput()->withErrors([
                'file' => 'Ukuran file melebihi batas upload server. Silakan unggah file yang lebih kecil.',
            ]);
        });
    })->create();
