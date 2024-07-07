<?php

namespace App\Http\Controllers\Auth;

use App\Components\Resources\SuccessResource;
use App\Http\Controllers\Controller;
use Tymon\JWTAuth\Facades\JWTAuth;

class LogoutController extends Controller
{
    public function __invoke()
    {
        JWTAuth::invalidate(JWTAuth::getToken());
        return SuccessResource::make(responseWithMessage("Logout successfully"));
    }
}
