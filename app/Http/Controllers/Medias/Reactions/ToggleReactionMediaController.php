<?php

namespace App\Http\Controllers\Medias\Reactions;

use App\Http\Controllers\Controller;
use App\Http\Requests\Reactions\Medias\ToggleReactionMediaRequest;
use App\Models\ReactionMedia;
use Tymon\JWTAuth\Facades\JWTAuth;

class ToggleReactionMediaController extends Controller
{
    public function __invoke(ToggleReactionMediaRequest $request)
    {
        $reactionData = $request->validated();
        $userId = JWTAuth::user()->getAttribute("id");

        $reaction = ReactionMedia::updateOrCreate([
            "user_id" => $userId,
            "media_id" => $reactionData["mediaId"]
        ], ["feeling_id" => $reactionData["feelingId"]]);

        if ($reaction->wasRecentlyCreated || $reaction->wasChanged("feeling_id")) {
            return responseWithMessage("Reaction successfully");
        }

        $reaction->delete();
        return responseWithMessage("Reaction deleted successfully");
    }
}
