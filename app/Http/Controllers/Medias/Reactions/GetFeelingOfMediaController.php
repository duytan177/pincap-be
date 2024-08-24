<?php

namespace App\Http\Controllers\Medias\Reactions;

use App\Http\Controllers\Controller;
use App\Http\Resources\Feelings\FeelingCollection;
use App\Models\Media;

class GetFeelingOfMediaController extends Controller
{
    public function __invoke($mediaId)
    {
        $media = Media::with("allFeelings")->findOrFail($mediaId);


        return FeelingCollection::make($media->allFeelings);
    }
}
