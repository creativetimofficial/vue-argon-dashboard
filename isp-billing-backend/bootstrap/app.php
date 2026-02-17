<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'role' => \App\Http\Middleware\CheckRole::class,
            'detect.tenant' => \App\Http\Middleware\DetectTenant::class,
        ]);
        
        // Add CORS middleware globally (before other middleware)
        $middleware->prepend(\App\Http\Middleware\HandleCors::class);
        
        // Removed IdentifyTenant - DetectTenant handles tenant detection
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Handle unauthenticated exceptions for API routes
        $exceptions->renderable(function (\Illuminate\Auth\AuthenticationException $e, $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'message' => 'Unauthenticated.'
                ], 401);
            }
        });
    })->create();
