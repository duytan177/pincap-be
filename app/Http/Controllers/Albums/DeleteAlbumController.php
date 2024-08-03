<?php

namespace App\Http\Controllers\Albums;

use App\Http\Controllers\Controller;
use App\Models\Album;

class DeleteAlbumController extends Controller
{
    public function __invoke($albumId)
    {
        Album::findOrFail($albumId)->delete();

        return responseWithMessage("Deleted album successfully");
    }
}
