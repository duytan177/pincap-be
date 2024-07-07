<?php

namespace App\Http\Controllers\Auth;

use App\Exceptions\AuthException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use Tymon\JWTAuth\Facades\JWTAuth;

class LoginController extends Controller
{
    public function __invoke(LoginRequest $request)
    {
        $credentials = $request->validated();

        $user = User::where('email', $credentials['email'])->first();
        if (!$user || !$user->hasVerifiedEmail()) {
            throw AuthException::emailNotVerified();
        }

        if (!($token = JWTAuth::attempt($credentials))) {
            throw AuthException::invalidCredential();
        }

        return response()->json([
            "token" => $token
        ]);
    }
}
