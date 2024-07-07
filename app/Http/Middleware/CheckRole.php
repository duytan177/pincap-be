<?php

namespace App\Http\Middleware;

use App\Enums\User\Role;
use Closure;
use Illuminate\Http\Request;

class CheckRole
{
    /**
     * Handle an incoming request.
     *

     */
    public function handle(Request $request, Closure $next)
    {
        if (Role::getValue($request->input("role")) == "0") {
            return $next($request);
        } else {
            return response()->json(['message' => 'You are not ADMIN'], 403);
        }
    }
}
