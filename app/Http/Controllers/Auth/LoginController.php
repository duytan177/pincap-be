<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use Tymon\JWTAuth\Facades\JWTAuth;

class LoginController extends Controller
{
    public function __invoke(LoginRequest $request)
    {
        $credentials = $request->validated();

        // Kiểm tra xem người dùng đã xác minh email chưa
        $user = User::where('email', $credentials['email'])->first();
        if (!$user || !$user->hasVerifiedEmail()) {
            return response()->json(['error' => 'Email has not been verified'], 403);
        }

        $token = null;
        if (!($token = JWTAuth::attempt($credentials))) {
            return response()->json(['error' => 'Email or password is incorrect'], 401);
        }

        return response()->json([
            'token' => $token,
        ], 200);
    }
}
