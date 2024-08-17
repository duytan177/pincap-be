<?php

namespace App\Http\Controllers\Medias;

use App\Http\Controllers\Controller;
use App\Http\Resources\Medias\MediaDetail\MediaDetailResource;
use App\Models\Media;

class GetDetailMediaByIdController extends Controller
{
    public function __invoke($mediaId)
    {
        $media = Media::withCount(["reactionUser", "feelings"])
                ->with(['comments.userComment', "feelings", "comments.feelings", "comments.allFeelings", "comments.replies"])
                ->findOrFail($mediaId);

        return MediaDetailResource::make($media);
    }
}
