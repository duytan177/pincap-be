<?php

namespace App\Http\Controllers\Users\SocialAccounts\Instagram;

use App\Enums\User\SocialType;
use App\Http\Controllers\Controller;
use App\Models\UserSocialAccount;
use App\Services\FacebookInstagramService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Laravel\Socialite\Facades\Socialite;
use Tymon\JWTAuth\Facades\JWTAuth;

class HandleCallbackController extends Controller
{
    public function __invoke(Request $request)
    {
        DB::beginTransaction();

        try {
            $user = JWTAuth::parseToken()->authenticate();

            $facebookUser = Socialite::driver('facebook')->stateless()->user();
            $accessToken = $facebookUser->token;
            $accessTokenExpiresAt = Carbon::now()->addHour();
            $fbService = new FacebookInstagramService($accessToken);
            $fbService->exchangeLongLivedToken();

            $pages = $fbService->getUserPages();
            if (empty($pages)) {
                DB::rollBack();
                return response()->json(['error' => 'No Facebook pages found for this user.'], 404);
            }

            // get first page
            $firstPage = $pages[0];
            $igBizId = $fbService->getInstagramBusinessId($firstPage['id'] ?? '');

            if (!$igBizId) {
                DB::rollBack();
                return response()->json(['error' => 'Could not find an Instagram Business account linked to the Facebook page.'], 404);
            }

            $igDetail = $fbService->getInstagramDetails($igBizId);

            $tokenData = $fbService->getLongLivedToken();
            UserSocialAccount::firstOrCreate(
                [
                    'user_id' => $user->id,
                    'social_id' => $igDetail['id'],
                    'social_type' => SocialType::INSTAGRAM,
                ],
                [
                    'name' => $igDetail['name'] ?? $igDetail['username'] ?? '',
                    'avatar' => $igDetail['profile_picture_url'] ?? '',
                    'permalink' => 'https://instagram.com/' . ($igDetail['username'] ?? ''),
                    'access_token' => $accessToken,
                    'access_token_expired' => $accessTokenExpiresAt,
                    'refresh_token' => $tokenData['token'],
                    'refresh_token_expired' => $tokenData['expired_at'],
                ]
            );

            DB::commit();

            return responseWithMessage("integration instagram successfully");

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'error' => 'Facebook callback failed',
                'details' => $e->getMessage(),
            ], 500);
        }
    }
}
