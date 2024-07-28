<?php

namespace App\Http\Controllers\Albums;

use App\Http\Controllers\Controller;
use App\Http\Requests\Albums\AlbumRequest;
use App\Models\Album;

class UpdateAlbumController extends Controller
{
    //
    public function __invoke($albumId, AlbumRequest $request)
    {
        $dataAlbum = $request->validated();

        $album = Album::findOrFail($albumId);

        $album->update($dataAlbum);

        return responseWithMessage("Updated album successfully");
    }
}
