<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Auth\Shared\AuthHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;

class RegisterController extends Controller
{
    public function __invoke(RegisterRequest $request)
    {
        $registerData = $request->validated();
        $dataDefault = [
            "avatar" => config("common.avatar_default"),
            "background" => config("common.background_default")
        ];
        $registerData = array_merge($registerData, $dataDefault);

        $user = User::create($registerData);

        AuthHelper::sendEmailVerify($user);

        return responseWithMessage("Register successfully");

    }
}
