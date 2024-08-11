<?php

namespace App\Http\Controllers\Users\Profiles;

use App\Http\Controllers\Controller;
use App\Http\Resources\Users\Profiles\ProfileResource;
use Tymon\JWTAuth\Facades\JWTAuth;

class GetMyProfileController extends Controller
{
    public function __invoke()
    {
        $user = JWTAuth::user();
        return ProfileResource::make($user);
    }
}
