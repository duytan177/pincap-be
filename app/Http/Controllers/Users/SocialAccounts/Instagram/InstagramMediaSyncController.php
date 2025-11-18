<?php

namespace App\Http\Controllers\Users\SocialAccounts\Instagram;

use App\Http\Controllers\Controller;
use App\Http\Requests\Users\SocialAccounts\Instagram\InstagramMediaSyncRequest;
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

        $fbService = new FacebookInstagramService($socialInstagram->refresh_token);


        $chunks = array_chunk($ids, self::CHUNK);
        $results = [];

        foreach ($chunks as $chunk) {
            $results += $fbService->getMultipleMediaDetailsUpdate($chunk);
            usleep(500000); // 0.5s delay to avoid hitting API limits
        }

        return response()->json($results);
    }
}
