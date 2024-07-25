<?php

namespace App\Http\Controllers\Medias;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Medias\Shared\MediaHandle;
use App\Http\Requests\Medias\UpdateMediaRequest;
use App\Models\AlbumMedia;
use App\Models\Media;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class UpdateMediaController extends Controller
{
    public function __invoke($mediaId, UpdateMediaRequest $request)
    {
        $userId = Auth::user()->id;
        $media = Media::where("media_owner_id", $userId)->findOrFail($mediaId);
        $now = Carbon::now()->toDateTimeString();
        $mediaData = $request->validated();

        if (isset($mediaData["album_id"])) {
            $albumId = $mediaData["album_id"];
            AlbumMedia::updateOrCreate(
                [
                    'media_id' => $mediaId,
                    'album_id' => $albumId,
                ],
                [
                    "created_at" => $now
                ]
            );
        }

        if (isset($mediaData["tags_name"])) {
            MediaHandle::attachTagtoMedia($mediaData["tags_name"], $mediaId, $userId, $now);
        }

        $media->updateOrFail($mediaData);
        return responseWithMessage("Update media successfully");
    }
}
