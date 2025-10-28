<?php

namespace App\Http\Controllers\Medias;

use App\Http\Controllers\Controller;
use App\Http\Resources\Medias\MediaDetail\MediaDetailResource;
use App\Models\Media;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GetDetailMediaByIdController extends Controller
{
    public function __invoke(Request $request, $mediaId)
    {
        $userId = Auth::user()->id;
        $query = Media::withCount(["reactionUser", "feelings"])
            ->with([
                'comments.userComment',
                "feelings",
                "comments.feelings",
                "comments.allFeelings",
                "comments.replies",
                "reactions" => function ($q) use ($userId) {
                    $q->where('user_id', $userId);
                }
            ]);

        if (filter_var($request->input('tag_flg'), FILTER_VALIDATE_BOOLEAN)) {
            $query->with(['tags']);
        }

        $media = $query->findOrFail($mediaId);

        return MediaDetailResource::make($media);
    }
}
