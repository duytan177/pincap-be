<?php

namespace App\Http\Controllers\Users\SocialAccounts\Instagram;

use App\Http\Controllers\Controller;
use App\Services\FacebookInstagramService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

class GetMediaInstagramController extends Controller
{
    public function __invoke(Request $request)
    {
        $user = $request->user();

        // Lấy Instagram Social Account
        $socialInstagram = $user->socialInstagramFirstFull()->first();

        if (!$socialInstagram) {
            return response()->json(['message' => 'Instagram account not linked'], 404);
        }

        // Lấy limit & offset từ query param
        $limit = (int) $request->query('limit', 20);
        $next = $request->query('next', "");
        $fbService = new FacebookInstagramService($socialInstagram->refresh_token);

        if ($next) {
            try {
                // Giải mã next cursor
                $nextCursor = Crypt::decryptString($next);

                // Gọi API tiếp tục từ cursor
                $mediaData = $fbService->getInstagramMediaWithCursorAfter($nextCursor);
                return response()->json($fbService->formatMedia($mediaData));
            } catch (\Throwable $e) {
                return response()->json(['message' => 'Invalid next cursor'], 400);
            }
        }


        $firstPage = $fbService->getUserPages()[0] ?? null;
        $igBizId = $fbService->getInstagramBusinessId($firstPage['id'] ?? '');

        $mediaData = $fbService->getInstagramMediaWithCursor($igBizId, $limit);

        return response()->json($mediaData);
    }

}
