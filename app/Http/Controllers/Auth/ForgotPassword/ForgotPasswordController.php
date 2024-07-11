<?php

namespace App\Http\Controllers\Auth\ForgotPassword;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ForgotPasswordRequest;
use App\Jobs\SendResetPasswordMail;
use App\Models\PasswordReset;
use App\Models\User;
use Illuminate\Support\Str;

class ForgotPasswordController extends Controller
{
    public function __invoke(ForgotPasswordRequest $request)
    {
        [$user, $token] = $this->createTokenPasswordReset($request->input("email"));

        dispatch(new SendResetPasswordMail($user, $token));

        return responseWithMessage("We have e-mailed your password reset link!");
    }

    private function createTokenPasswordReset($email)
    {
        $user = User::where('email', $email)->firstOrFail();

        $passwordReset = PasswordReset::updateOrCreate([
            'email' => $user->email,
        ], [
            'token' => Str::random(64),
            'created_at' => now(),
        ]);

        return [$user, $passwordReset->token];
    }
}
