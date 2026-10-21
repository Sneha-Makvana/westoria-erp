<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthApiMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // Accept either a valid Passport Bearer token (API guard)
        // or an active web session — so pages like /leaveview work
        // without needing a token in localStorage.
        if (Auth::guard('api')->check() || Auth::guard('web')->check()) {
            return $next($request);
        }

        return response()->json(['status' => false, 'error' => 'Unauthorized'], 401);
    }
}
