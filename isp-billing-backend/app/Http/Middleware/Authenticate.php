<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    protected function redirectTo(Request $request)
    {
        // For API requests, return null to trigger 401 JSON response
        if ($request->expectsJson() || $request->is('api/*')) {
            return null;
        }
        // Default: you can set a web login route here if needed
        // return route('login');
        return null;
    }
}
