<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     * Return null for API routes to trigger JSON response instead of redirect.
     */
    protected function redirectTo(Request $request): ?string
    {
        // For API routes, return null to prevent redirect
        if ($request->is('api/*') || $request->expectsJson()) {
            return null;
        }
        
        // For web routes (if any in future)
        return route('login');
    }
}
