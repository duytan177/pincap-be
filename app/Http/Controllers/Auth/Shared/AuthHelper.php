<?php

namespace App\Http\Controllers\Auth\Shared;

use App\Jobs\SendRegistrationEmail;
use Carbon\Carbon;
use Illuminate\Support\Str;

class AuthHelper
{
    const TOKEN_EXPIRATION_MINUTES = 5;

    public static function createVerificationToken()
    {
        return [
            'verification_token' => Str::random(64),
            "verification_token_expires_at" => Carbon::now()->addMinutes(self::TOKEN_EXPIRATION_MINUTES)->toDateTimeString(),
        ];
    }

    public static function sendEmailVerify($user)
    {
        $user->update(self::createVerificationToken());
        SendRegistrationEmail::dispatch($user);
    }
}
