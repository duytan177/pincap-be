<?php

namespace App\Http\Controllers\Users\Profiles;

use App\Http\Controllers\Controller;
use App\Http\Resources\Users\Profiles\ProfileResource;
use App\Models\User;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

class GetProfileUserByIdController extends Controller
{
    public function __invoke($id, Request $request)
    {
        $user = User::withCount(["followers", "followees", "medias", "reactionMedia"])->findOrFail($id);
        if ($token = $request->bearerToken()) {
            JWTAuth::setToken($token)->authenticate();
            $user->with("followers");
        }

        return ProfileResource::make($user);
    }
}
