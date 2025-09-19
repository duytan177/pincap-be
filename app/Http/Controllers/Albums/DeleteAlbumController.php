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
        $currentUserId = JWTAuth::user()->getAttribute("id");

        $isOwner = Album::query()->where('id', $albumId)->ownedBy($currentUserId)->exists();

        if (!$isOwner) {
            throw AlbumException::notOwner();
        }

        $deleted = Album::where("id", $albumId)->delete();
        if ($deleted === 0) {
            throw AlbumException::deleteFailed();
        }

        return responseWithMessage("Deleted album successfully");
    }
}
