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
        // Get allowed origins from config or env
        $allowedOrigins = [
            'http://localhost:8080',
            'http://127.0.0.1:8080',
            config('app.frontend_url', 'http://localhost:8080'),
        ];

        $origin = $request->headers->get('Origin');
        
        // Check if origin matches allowed origins or subdomain pattern
        $allowedOrigin = null;
        
        if (in_array($origin, $allowedOrigins)) {
            $allowedOrigin = $origin;
        } elseif ($origin && preg_match('/^http:\/\/[a-zA-Z0-9-]+\.localhost:8080$/', $origin)) {
            // Allow any subdomain of localhost:8080 (for multi-tenant)
            $allowedOrigin = $origin;
        } else {
            $allowedOrigin = $allowedOrigins[0] ?? '*';
        }

        // Handle preflight requests
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
            // Even on error, add CORS headers
            $response = response()->json([
                'message' => $e->getMessage(),
                'error' => config('app.debug') ? [
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'trace' => $e->getTraceAsString()
                ] : null
            ], 500);
        }

        // Add CORS headers to response (always, even on error)
        return $response
            ->header('Access-Control-Allow-Origin', $allowedOrigin)
            ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, PATCH, OPTIONS')
            ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With, Accept, Origin')
            ->header('Access-Control-Allow-Credentials', 'true');
    }
}
