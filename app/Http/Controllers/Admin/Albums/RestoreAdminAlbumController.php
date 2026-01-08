<?php

namespace App\Http\Controllers\Admin\Albums;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\Albums\AdminAlbumResource;
use App\Models\Album;
use App\Exceptions\Admin\AlbumException;

class RestoreAdminAlbumController extends Controller
{
    public function __invoke(string $albumId)
    {
        $album = Album::withoutGlobalScopes()
            ->withTrashed()
            ->with(['userOwner:id,first_name,last_name,email,avatar'])
            ->find($albumId);

        if (!$album) {
            throw AlbumException::albumNotFound();
        }

        // Check if album is not deleted
        if (!$album->trashed()) {
            throw AlbumException::albumNotDeleted();
        }

        // Restore the album (set deleted_at to null)
        $album->restore();

        // Refresh counts
        $album->loadCount('medias');

        return new AdminAlbumResource($album->fresh());
    }
}

