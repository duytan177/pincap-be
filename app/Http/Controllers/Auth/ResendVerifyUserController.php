<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Auth\Shared\AuthHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ResendVerifyUserRequest;
use App\Models\User;

class ResendVerifyUserController extends Controller
{
    public function __invoke(ResendVerifyUserRequest $request)
    {
        $email = $request->input("email");
        $user = User::withoutGlobalScopes()->where("email", $email)->firstOrFail();

        AuthHelper::sendEmailVerify($user);
        return responseWithMessage("Resend verify email for user");
    }
}
