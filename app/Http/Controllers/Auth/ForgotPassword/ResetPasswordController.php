<?php

namespace App\Http\Controllers\Auth\ForgotPassword;

use App\Exceptions\AuthException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Models\PasswordReset;
use App\Models\User;
use Carbon\Carbon;

class ResetPasswordController extends Controller
{
    public function __invoke(ResetPasswordRequest $request)
    {
        $requestData = $request->validated();

        $passwordReset = PasswordReset::where("token", $requestData["token"])->firstOrFail();

        if (Carbon::parse($passwordReset->created_at)->addMinutes(60)->isPast()) {
            throw AuthException::tokenExpired();
        }

        $this->updatePasswordAndRemoveToken($passwordReset, $requestData["password"]);
        return responseWithMessage("Reset password successful");
    }

    private function updatePasswordAndRemoveToken($passwordResetToken, $password)
    {
        $user = User::where('email', $passwordResetToken->email)->firstOrFail();
        $user->update([
            "password" => $password,
        ]);
        $passwordResetToken->delete();
    }
}
