<?php

namespace App\Http\Controllers\Albums;

use App\Http\Controllers\Controller;
use App\Http\Requests\Albums\AddMediasToAlbumRequest;
use App\Models\AlbumMedia;

class RemoveMediasFromAlbumController extends Controller
{
    public function __invoke(AddMediasToAlbumRequest $request)
    {
        $data = $request->validated();

        AlbumMedia::where("album_id", $data["album_id"])->whereIn("media_id", $data["medias_id"])->delete();

        return responseWithMessage("Remove medias from albums successfully");
    }
}
