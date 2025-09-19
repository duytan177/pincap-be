<?php

namespace App\Http\Controllers\Albums;

use App\Http\Controllers\Controller;
use App\Http\Requests\Albums\AddMediasToAlbumRequest;
use App\Models\AlbumMedia;
use App\Models\Album;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Exceptions\Albums\AlbumException;
use App\Exceptions\MediaException;

class RemoveMediasFromAlbumController extends Controller
{
    public function __invoke(AddMediasToAlbumRequest $request)
    {
        $data = $request->validated();
        $currentUserId = JWTAuth::user()->getAttribute("id");

        $isOwner = Album::query()->where('id', $data["album_id"])->ownedBy($currentUserId)->exists();

        if (!$isOwner) {
            throw AlbumException::notOwner();
        }

        $deleted = AlbumMedia::where("album_id", $data["album_id"])
            ->whereIn("media_id", $data["medias_id"])
            ->delete();

        if ($deleted === 0) {
            throw MediaException::deleteFaired();
        }

        return responseWithMessage("Remove medias from albums successfully");
    }
}
