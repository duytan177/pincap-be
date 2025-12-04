<?php

namespace App\Http\Controllers\Users\SocialAccounts\Instagram;

use App\Enums\User\SocialType;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserSocialAccount;
use App\Services\FacebookInstagramService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Laravel\Socialite\Facades\Socialite;

class HandleCallbackController extends Controller
{
    public function __invoke(Request $request)
    {
        $state = $request->get('state');
        try {
            $payload = Crypt::decryptString($state);
            $data = json_decode($payload, true);

            $userId = $data['user_id'] ?? null;
            $timestamp = $data['ts'] ?? null;

            // Optional: expire after 10 minutes
            if (!$userId || !$timestamp || $timestamp < time() - 600) {
                abort(403, 'Invalid or expired state.');
            }

            $user = User::findOrFail($userId);
        } catch (\Exception $e) {
            abort(403, 'Invalid or tampered state.');
        }
        DB::beginTransaction();

        try {
            $facebookUser = Socialite::driver('facebook')->stateless()->user();
            $accessToken = $facebookUser->token;
            $accessTokenExpiresAt = Carbon::now()->addHour();
            $fbService = new FacebookInstagramService($accessToken);

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
            $fbService->exchangeLongLivedToken($user->id, $igDetail["id"]);
            $tokenData = $fbService->getLongLivedToken();
            UserSocialAccount::UpdateOrCreate(
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

            // return responseWithMessage("integration instagram successfully");
            $domainFELogin = config( "frontend.app.domain") . config("frontend.app.paths.instagram_sync") . '?status=success';

            return redirect()->away($domainFELogin);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'error' => 'Facebook callback failed',
                'details' => $e->getMessage(),
            ], 500);
        }
    }
}
