<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, $role)
    {
        if (!Auth::check() || Auth::user()->role !== $role) {
            return response()->json([
                'status' => 'error',
                'message' => 'Access denied: Invalid role',
            ], 403); // Forbidden response
        }

        return $next($request);
    }
}
