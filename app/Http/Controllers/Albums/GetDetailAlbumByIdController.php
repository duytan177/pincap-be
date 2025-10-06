<?php

namespace App\Http\Controllers\Albums;

use App\Http\Controllers\Controller;
use App\Http\Resources\Albums\DetailAlbumResource;
use App\Models\Album;

class GetDetailAlbumByIdController extends Controller
{
    public function __invoke($albumId)
    {
    $albumDetail = Album::with([
        'allUser' => function ($query) {
            $query->select('users.*', 'user_album.invitation_status as status', 'user_album.album_role');
        },
        'medias'
    ])->findOrFail($albumId);
    return DetailAlbumResource::make($albumDetail);
    }
}
