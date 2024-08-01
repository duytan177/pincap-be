<?php

namespace App\Http\Controllers\Albums;

use App\Http\Controllers\Controller;
use App\Http\Resources\Albums\DetailAlbumResource;
use App\Models\Album;

class GetDetailAlbumByIdController extends Controller
{
    public function __invoke($albumId)
    {
        $albumDetail = Album::with(["allUser", "medias"])->findOrFail($albumId);
        return DetailAlbumResource::make($albumDetail);
    }
}
