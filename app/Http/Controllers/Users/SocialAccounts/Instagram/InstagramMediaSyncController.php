<?php

namespace App\Http\Controllers\Users\SocialAccounts\Instagram;

use App\Http\Controllers\Controller;
use App\Http\Requests\Users\SocialAccounts\Instagram\InstagramMediaSyncRequest;
use App\Models\Media;
use App\Services\FacebookInstagramService;

class InstagramMediaSyncController extends Controller
{
    const CHUNK = 20;
    public function __invoke(InstagramMediaSyncRequest $request)
    {
        $user = $request->user();
        $data = $request->validated();
        $ids = $data["ids"];
        // Get Instagram Social Account
        $socialInstagram = $user->socialInstagram()->first();
        if (!$socialInstagram) {
            return response()->json(['message' => 'Instagram account not linked'], 404);
        }

        $fbService = new FacebookInstagramService($socialInstagram->access_token);

        // Check existing media to avoid duplicates
        $allMediaSocialIds = Media::where('media_owner_id', $user->id)
            ->whereNotNull('media_social_id')
            ->where('media_social_id', '!=', '')
            ->pluck('media_social_id')->unique()->toArray();

        $ids = array_values(array_diff($ids, $allMediaSocialIds));

        $chunks = array_chunk($ids, self::CHUNK);
        $results = [];

        foreach ($chunks as $chunk) {
            $chunkResults = $fbService->getMultipleMediaDetailsUpdate($chunk);
            $results = array_merge($results, $chunkResults);
            usleep(100000); // 0.1s delay to avoid hitting API limits
        }

        // ---------------------------
        // FORMAT DATA FOR UPSERT
        // ---------------------------
        $records = [];

        foreach ($results as $media) {
            // Skip errored items
            if (isset($media['error'])) {
                continue;
            }
            $records[] = [
                'media_social_id' => $media['media_social_id'],
                'media_name' => $media['media_name'],
                'type' => $media['type'],
                'media_url' => json_encode($media['media_url']),
                'permalink' => $media['permalink'],
                'media_owner_id' => $user->id,
            ];
        }

        // ---------------------------
        // UPSERT
        // ---------------------------
        Media::upsert(
            $records,
            ['media_social_id'], // unique column
            ['media_name', 'type', 'media_url', 'permalink', 'media_owner_id', 'updated_at']
        );

        // Return sync success message
        return response()->json([
            'message' => 'Sync success',
            'total' => count($records)
        ], 200);
    }
}
