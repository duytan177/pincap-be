<?php

namespace App\Http\Controllers\Medias;

use App\Http\Controllers\Controller;
use App\Http\Resources\Medias\MediaDetail\MediaDetailResource;
use App\Models\Media;
use Illuminate\Http\Request;

class GetDetailMediaByIdController extends Controller
{
    public function __invoke(Request $request, $mediaId)
    {
        $query = Media::withCount(["reactionUser", "feelings"])
                ->with(['comments.userComment', "feelings", "comments.feelings", "comments.allFeelings", "comments.replies", "reactions"]);

        if (filter_var($request->input('tag_flg'), FILTER_VALIDATE_BOOLEAN)) {
            $query->with(['tags']);
        }

        $media = $query->findOrFail($mediaId);

        return MediaDetailResource::make($media);
    }
}
