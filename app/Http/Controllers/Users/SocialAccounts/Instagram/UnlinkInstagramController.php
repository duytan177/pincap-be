<?php

namespace App\Http\Controllers\Users\SocialAccounts\Instagram;

use App\Enums\User\SocialType;
use App\Http\Controllers\Controller;
use App\Models\UserSocialAccount;
use Illuminate\Http\Request;

class UnlinkInstagramController extends Controller
{
    public function __invoke(Request $request)
    {
        $user = $request->user();

        $socialInstagram = $user->socialInstagram()->first();

        if (!$socialInstagram) {
            return response()->json(['message' => 'Instagram account not linked'], 404);
        }

        UserSocialAccount::where('user_id', $user->id)
            ->where('social_type', SocialType::INSTAGRAM)
            ->delete();

        return responseWithMessage('Unlink instagram successfully');
    }
}


