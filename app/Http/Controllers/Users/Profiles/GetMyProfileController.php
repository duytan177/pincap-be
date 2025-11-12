<?php

namespace App\Http\Controllers\Users\Profiles;

use App\Http\Controllers\Controller;
use App\Http\Resources\Users\Profiles\ProfileResource;
use App\Services\FacebookInstagramService;
use Tymon\JWTAuth\Facades\JWTAuth;

class GetMyProfileController extends Controller
{
    public function __invoke()
    {
        $user = JWTAuth::user()->loadCount(["followers", "followees", "medias", "reactionMedia"])->load([
            "socialInstagram" => function ($query) {
                $query->select(['id', 'user_id', 'name', 'avatar', 'permalink', 'social_id']);
            }
        ]);
        if ($user->socialInstagram) {
            $social = $user->socialInstagram;
            // Only refresh if the token expires in the next 7 days
            if (\Carbon\Carbon::parse($social->refresh_token_expired)->isBefore(now()->addDays(7))) {
                $fbService = new FacebookInstagramService($social->refresh_token);
                $fbService->exchangeLongLivedToken($user->id, $social->social_id);
            }
        }
        return ProfileResource::make($user);
    }
}
