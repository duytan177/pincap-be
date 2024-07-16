<?php

namespace App\Http\Controllers\Auth\OAuth2\Google;

use App\Http\Controllers\Controller;
use Laravel\Socialite\Facades\Socialite;

class GetUrlRedirectController extends Controller
{
    public function __invoke()
    {
        return response()->json([
            "url" => Socialite::driver('google')->stateless()->redirect()->getTargetUrl()
        ], 200);
    }
}
