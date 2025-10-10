<?php

namespace App\Http\Controllers\Auth\OAuth2\Google;

use App\Exceptions\AuthException;
use App\Http\Controllers\Controller;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use Tymon\JWTAuth\Facades\JWTAuth;

class HandleCallbackController extends Controller
{
    public function __invoke(Request $request)
    {
        $socialiteUser = Socialite::driver('google')->stateless()->user();

        $user = User::withoutGlobalScope('verified')->firstOrCreate(
            [
                'email' => $socialiteUser->getEmail(),
            ],
            [
                'email_verified_at' => Carbon::now()->toDateTimeString(),
                'last_name' => $socialiteUser->user["family_name"] ?? null,
                'first_name' => $socialiteUser->user["given_name"] ?? null,
                'google_id' => $socialiteUser->getId(),
                "avatar" => config("common.avatar_default"),
                "background" => config("common.background_default"),
            ]
        );
        if (!$user->email_verified_at) {
            $user->email_verified_at = Carbon::now()->toDateTimeString();
            $user->verification_token = null;
            $user->verification_token_expires_at = null;
            $user->save();
        }

        $token = JWTAuth::fromUser($user);
        if (!$token) {
            throw AuthException::invalidCredential();
        }

        if (strpos($request->userAgent(), "Mobile")) {
            $domainFELogin = config("frontend.app.domain") . config("frontend.app.paths.login");
        } else {
            $domainFELogin = config("frontend.web.domain") . config("frontend.web.paths.login");
        }

        return redirect()->away($domainFELogin . "?google-token=$token");
    }
}
