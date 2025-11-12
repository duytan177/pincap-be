<?php

namespace App\Http\Controllers\Users\SocialAccounts\Instagram;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Laravel\Socialite\Facades\Socialite;

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

        $user = $request->user();
        $payload = json_encode([
            'user_id' => $user->id,
            'ts' => time(),
        ]);
        $state = Crypt::encryptString($payload);

        // Dùng Socialite để tạo URL có scope + state
        $redirectUrl = Socialite::driver('facebook')
            ->scopes($scopes)
            ->stateless()
            ->with(['state' => $state])
            ->redirect()
            ->getTargetUrl();

        return response()->json([
            'url' => $redirectUrl,
        ], 200);
    }
}
