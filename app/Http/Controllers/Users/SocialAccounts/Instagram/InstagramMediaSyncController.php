<?php

namespace App\Http\Controllers\Users\SocialAccounts\Instagram;

use App\Http\Controllers\Controller;
use App\Http\Requests\Users\SocialAccounts\Instagram\InstagramMediaSyncRequest;
use App\Jobs\SendInstagramMediaEventsJob;
use App\Models\Media;
use App\Services\FacebookInstagramService;
use Illuminate\Support\Facades\Log;
use Ramsey\Uuid\Uuid;

class InstagramMediaSyncController extends Controller
{
    const CHUNK = 20;
    public const TOPIC = "user_behavior";

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
        $events = [];

        foreach ($results as $item) {

            if (isset($item['error']))
                continue;

            $uuid = (string) Uuid::uuid4()->toString();

            $record = [
                'id' => $uuid,
                'media_social_id' => $item['media_social_id'],
                'media_name' => $item['media_name'],
                'type' => $item['type'],
                'media_url' => json_encode($item['media_url']),
                'permalink' => $item['permalink'],
                'media_owner_id' => $user->id,
                'is_created' => true,
            ];

            $records[] = $record;

            // Tạo event để gửi sau khi DB ok
            $events[] = [
                'media_id' => $uuid,
                'media_url' => $record['media_url'],
                'media_name' => $record['media_name'],
                'description' => null,
                'tag_name' => null,
                'user_id' => $user->id,
                'timestamp' => now()->toISOString(),
            ];
        }
        // ---------------------------
        // UPSERT
        // ---------------------------
        try {
            Media::upsert(
                $records,
                ['media_social_id'], // unique
                ['media_name', 'type', 'media_url', 'permalink', 'media_owner_id', 'updated_at']
            );
            SendInstagramMediaEventsJob::dispatch($events);
        } catch (\Throwable $e) {
            Log::error('Failed to send media sync events to Kafka', ['error' => $e->getMessage()]);
            return response()->json([
                "message" => "Upsert failed",
            ], 500);
        }

        // Return sync success message
        return response()->json([
            'message' => 'Sync success',
            'total' => count($records)
        ], 200);
    }
}
