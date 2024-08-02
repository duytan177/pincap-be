<?php

namespace App\Http\Controllers\Albums;

use App\Http\Controllers\Controller;
use App\Models\UserAlbum;

class AddMemberIntoAlbumController extends Controller
{
    public function __invoke($albumId, $userId)
    {
        UserAlbum::create([
            "user_id" => $userId,
            "album_id" => $albumId
        ]);
        return responseWithMessage("Add user successfully");
    }
}
