<?php

namespace App\Http\Middleware;

use App\Enums\User\Role;
use Closure;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     */
    public function handle(Request $request, Closure $next)
    {
        $user = JWTAuth::user();

        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        // Get raw role value from DB (bypass accessor)
        $userRole = $user->getRawOriginal("role");

        if ($userRole === Role::ADMIN || $userRole === "0") {
            return $next($request);
        }

        return response()->json(['message' => 'You are not ADMIN'], 403);
    }
}
