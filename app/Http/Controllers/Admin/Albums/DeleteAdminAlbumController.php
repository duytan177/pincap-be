<?php

namespace App\Http\Controllers\Admin\Albums;

use App\Http\Controllers\Controller;
use App\Models\Album;
use App\Exceptions\Admin\AlbumException;

class DeleteAdminAlbumController extends Controller
{
    public function __invoke(string $albumId)
    {
        $album = Album::withoutGlobalScopes()
            ->withTrashed()
            ->find($albumId);

        if (!$album) {
            throw AlbumException::albumNotFound();
        }

        // Check if album is already deleted
        if ($album->trashed()) {
            throw AlbumException::albumAlreadyDeleted();
        }

        // Soft delete the album
        $album->delete();

        return responseWithMessage("Album deleted successfully");
    }
}

