<?php

namespace App\Http\Controllers\Auth;

use App\Exceptions\AuthException;
use App\Http\Controllers\Controller;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

class VerifyUserController extends Controller
{
    private $currentTime;
    public function __invoke($token, Request $request)
    {
        $this->currentTime = Carbon::now()->toDateTimeString();

        $user = User::where('verification_token', $token)
            ->where('verification_token_expires_at', '>', $this->currentTime)
            ->whereNull("email_verified_at")->first();

        if (!$user) {
            throw AuthException::tokenExpired();
        }

        $user->email_verified_at = $this->currentTime;
        $user->verification_token = null;
        $user->verification_token_expires_at = null;
        $user->save();

        // Generate JWT token
        $token = JWTAuth::fromUser($user);

        if (strpos($request->userAgent(), "Mobile")) {
            $domainFELogin = config("frontend.app.domain") . config("frontend.app.paths.login");
        } else {
            $domainFELogin = config("frontend.web.domain") . config("frontend.web.paths.login");
        }

        return redirect()->away($domainFELogin . "?verified-token=$token");
    }
}
