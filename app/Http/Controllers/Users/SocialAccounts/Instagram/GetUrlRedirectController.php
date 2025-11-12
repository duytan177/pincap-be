<?php

namespace App\Http\Controllers\Users\SocialAccounts\Instagram;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;

class GetUrlRedirectController extends Controller
{
    public function __invoke(Request $request)
    {
        // Danh sách permission (dấu phẩy ngăn cách)
        $scopes = [
            'public_profile',
            'email',
            // Instagram
            'instagram_basic',
            'instagram_manage_comments',
            'instagram_manage_insights',
            // Pages
            'pages_read_engagement',
            'pages_manage_metadata',
            'pages_manage_posts',
        ];

        // Dùng Socialite để tạo URL có scope + state
        $redirectUrl = Socialite::driver('facebook')
            ->scopes($scopes)
            ->stateless()
            ->with(['state' => $request->bearerToken()])
            ->redirect()
            ->getTargetUrl();

        return response()->json([
            'url' => $redirectUrl,
        ], 200);
    }
}
