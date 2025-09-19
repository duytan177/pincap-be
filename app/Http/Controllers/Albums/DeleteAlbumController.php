<?php

namespace App\Http\Controllers\Albums;

use App\Http\Controllers\Controller;
use App\Models\Album;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Exceptions\Albums\AlbumException;

class DeleteAlbumController extends Controller
{
    public function __invoke($albumId)
    {
        Album::findOrFail($albumId)->delete();

        return responseWithMessage("Deleted album successfully");
    }
}
