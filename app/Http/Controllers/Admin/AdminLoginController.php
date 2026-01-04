<?php

namespace App\Http\Controllers\Admin;

use App\Enums\User\Role;
use App\Exceptions\AuthException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use Tymon\JWTAuth\Facades\JWTAuth;

class AdminLoginController extends Controller
{
    public function __invoke(LoginRequest $request)
    {
        $credentials = $request->validated();

        $user = User::where('email', $credentials['email'])->first();

        if (!($token = JWTAuth::attempt($credentials))) {
            throw AuthException::invalidCredential();
        }

        // Check if authenticated user is admin
        $authenticatedUser = JWTAuth::user();
        if (!$authenticatedUser) {
            throw AuthException::invalidCredential();
        }

        $userRole = $authenticatedUser->getRawOriginal("role");
        if ($userRole !== Role::ADMIN && $userRole !== "0" && $userRole !== "ADMIN") {
            throw AuthException::notAdmin();
        }

        return response()->json([
            "token" => $token
        ]);
    }
}

