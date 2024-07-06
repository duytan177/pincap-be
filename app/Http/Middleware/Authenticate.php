<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Support\Facades\Auth;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    public function handle($request, Closure $next, ...$guards)
    {
        // Kiểm tra xem token có tồn tại và hợp lệ hay không
        if ($request->header('Authorization')) {
            $token = str_replace('Bearer ', '', $request->header('Authorization'));
            // Kiểm tra tính hợp lệ của token
            if (Auth::guard('api')->check()) {
                // Token hợp lệ, lưu thông tin người dùng vào request để sử dụng trong controller
                $request->merge(['user' => Auth::guard('api')->user()]);
                return $next($request);
            }
        }
        return response()->json(["message" => "Unauthorized"],403);
    }
}
