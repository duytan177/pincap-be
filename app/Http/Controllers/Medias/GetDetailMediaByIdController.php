<?php

namespace App\Http\Controllers\Medias;

use App\Http\Controllers\Controller;
use App\Http\Resources\Medias\MediaDetail\MediaDetailResource;
use App\Models\Media;

class GetDetailMediaByIdController extends Controller
{
    public function __invoke($mediaId)
    {
        $media = Media::with(['userComments', "feelings"])->findOrFail($mediaId);

        return MediaDetailResource::make($media);
    }
}
