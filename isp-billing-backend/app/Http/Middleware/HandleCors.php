<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class HandleCors
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $origin = $request->headers->get('Origin');

        // Determine the allowed origin to echo back.
        // Rules:
        //  1. Any exact origin in the static whitelist → allow it
        //  2. Any subdomain of localhost on any port   → allow it (multi-tenant dev)
        //  3. Everything else                          → use * (open API)
        $allowedOrigin = $this->resolveAllowedOrigin($origin);

        // Handle preflight OPTIONS requests
        if ($request->getMethod() === 'OPTIONS') {
            return response('', 200)
                ->header('Access-Control-Allow-Origin', $allowedOrigin)
                ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, PATCH, OPTIONS')
                ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With, Accept, Origin')
                ->header('Access-Control-Allow-Credentials', 'true')
                ->header('Access-Control-Max-Age', '86400');
        }

        try {
            $response = $next($request);
        } catch (\Exception $e) {
            $response = response()->json([
                'message' => $e->getMessage(),
                'error' => config('app.debug') ? [
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'trace' => $e->getTraceAsString()
                ] : null
            ], 500);
        }

        return $response
            ->header('Access-Control-Allow-Origin', $allowedOrigin)
            ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, PATCH, OPTIONS')
            ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With, Accept, Origin')
            ->header('Access-Control-Allow-Credentials', 'true')
            ->header('Content-Security-Policy', "script-src 'self' 'unsafe-eval' 'unsafe-inline' https://fonts.googleapis.com https://cdn.jsdelivr.net https://buttons.github.io https://cdnjs.cloudflare.com https://unpkg.com; object-src 'none';");
    }

    private function resolveAllowedOrigin(?string $origin): string
    {
        if (!$origin) {
            return '*';
        }

        // Static whitelist (exact match)
        $staticAllowed = [
            'http://localhost:5173',
            'http://127.0.0.1:5173',
            'http://localhost:8080',
            'http://127.0.0.1:8080',
            'http://localhost:3000',
            config('app.frontend_url', 'http://localhost:5173'),
        ];

        if (in_array($origin, $staticAllowed)) {
            return $origin;
        }

        // Allow ANY subdomain of localhost on any port (e.g. irvan1.localhost:5173)
        // Pattern: http://<subdomain>.localhost:<any-port>
        if (preg_match('/^https?:\/\/[a-zA-Z0-9-]+\.localhost(:\d+)?$/', $origin)) {
            return $origin;
        }

        // For all other origins, return wildcard (public API)
        return '*';
    }
}
