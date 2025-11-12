<?php

namespace App\Http\Controllers\Users\SocialAccounts\Instagram;

use App\Http\Controllers\Controller;
use App\Models\Media;
use App\Services\FacebookInstagramService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

class GetMediaInstagramController extends Controller
{
    public function __invoke(Request $request)
    {
        $user = $request->user();

        // Lấy Instagram Social Account
        $socialInstagram = $user->socialInstagram()->first();
        if (!$socialInstagram) {
            return response()->json(['message' => 'Instagram account not linked'], 404);
        }

        // Lấy limit & offset từ query param
        $limit = (int) $request->query('limit', 20);
        $next = $request->query('next', "");
        $fbService = new FacebookInstagramService($socialInstagram->refresh_token);

        if ($next) {
            try {
                // Decode next cursor of instagram
                $nextCursor = Crypt::decryptString($next);
                $mediaData = $fbService->getInstagramMediaWithCursorAfter($nextCursor);
            } catch (\Throwable $e) {
                return response()->json(['message' => 'Invalid next cursor'], 400);
            }
        } else {
            $firstPage = $fbService->getUserPages()[0] ?? null;
            $igBizId = $fbService->getInstagramBusinessId($firstPage['id'] ?? '');
            $mediaData = $fbService->getInstagramMediaWithCursor($igBizId, $limit);
        }


        // Optimized check is_synced
        $mediaCollection = collect($mediaData['data']);
        $existingMediaIds = Media::whereIn('media_social_id', $mediaCollection->pluck('id'))
            ->pluck('media_social_id');

        $mediaCollection = $mediaCollection->map(function ($media) use ($existingMediaIds) {
            $media['is_synced'] = $existingMediaIds->contains($media['id']);
            return $media;
        });

        $formatted = $fbService->formatMedia($mediaData);
        $formatted['data'] = $mediaCollection;
        return response()->json($formatted);
    }

}
