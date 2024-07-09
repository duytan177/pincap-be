<?php

namespace App\Http\Controllers\Auth\Shared;

use App\Jobs\SendRegistrationEmail;
use Carbon\Carbon;
use Illuminate\Support\Str;

class AuthHelper
{
    public static function createVerificationToken()
    {
        return [
            'verification_token' => Str::random(64),
            "verification_token_expires_at" => Carbon::now()->addMinutes(5),
        ];
    }

    public static function sendEmailVerify($user)
    {
        SendRegistrationEmail::dispatch($user);
    }
}
