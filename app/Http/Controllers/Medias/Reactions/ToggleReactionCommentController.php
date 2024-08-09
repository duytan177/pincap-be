<?php

namespace App\Http\Controllers\Medias\Reactions;

use App\Http\Controllers\Controller;
use App\Http\Requests\Medias\Reactions\ToggleReactionCommentRequest;
use App\Models\ReactionComment;
use Tymon\JWTAuth\Facades\JWTAuth;

class ToggleReactionCommentController extends Controller
{
    public function __invoke(ToggleReactionCommentRequest $request)
    {
        $reactionData = $request->validated();
        $userId = JWTAuth::user()->getAttribute("id");

        $reaction = ReactionComment::updateOrCreate([
            "user_id" => $userId,
            "comment_id" => $reactionData["commentId"]
        ], ["feeling_id" => $reactionData["feelingId"]]);

        if ($reaction->wasRecentlyCreated || $reaction->wasChanged("feeling_id")) {
            return responseWithMessage("Reaction successfully");
        }

        $reaction->delete();
        return responseWithMessage("Reaction deleted successfully");
    }
}
