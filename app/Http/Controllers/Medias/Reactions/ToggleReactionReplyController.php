<?php

namespace App\Http\Controllers\Medias\Reactions;

use App\Http\Controllers\Controller;
use App\Http\Requests\Medias\Reactions\ToggleReactionReplyRequest;
use App\Models\ReactionReply;
use Tymon\JWTAuth\Facades\JWTAuth;

class ToggleReactionReplyController extends Controller
{
    public function __invoke(ToggleReactionReplyRequest $request)
    {
        $reactionData = $request->validated();
        $userId = JWTAuth::user()->getAttribute("id");

        $reaction = ReactionReply::updateOrCreate([
            "user_id" => $userId,
            "reply_id" => $reactionData["replyId"]
        ], ["feeling_id" => $reactionData["feelingId"]]);

        if ($reaction->wasRecentlyCreated || $reaction->wasChanged("feeling_id")) {
            return responseWithMessage("Reaction successfully");
        }

        $reaction->delete();
        return responseWithMessage("Reaction deleted successfully");
    }
}
