<?php

namespace App\Http\Controllers\Auth;

use App\Exceptions\AuthException;
use App\Http\Controllers\Controller;
use App\Models\User;
use Carbon\Carbon;
use Tymon\JWTAuth\Facades\JWTAuth;

class VerifyUserController extends Controller
{
    private static $currentTime;
    public function __invoke($token)
    {
        self::$currentTime = Carbon::now()->toDateTimeString();

        $user = User::where('verification_token', $token)
            ->where('verification_token_expires_at', '>', self::$currentTime)
            ->whereNull("email_verified_at")->first();

        if (!$user) {
            throw AuthException::tokenExpired();
        }

        $user->email_verified_at = self::$currentTime;
        $user->verification_token = null;
        $user->verification_token_expires_at = null;
        $user->save();

        // Generate JWT token
        $token = JWTAuth::fromUser($user);

        $domainFELogin = config("frontend.web.domain") . config("frontend.web.login");
        return redirect()->away($domainFELogin . "?verified-token=$token");
    }
}
