<?php

namespace App\Http\Controllers\Admin\Albums;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Albums\UpdateAdminAlbumRequest;
use App\Http\Resources\Admin\Albums\AdminAlbumResource;
use App\Models\Album;
use App\Exceptions\Admin\AlbumException;

class UpdateAdminAlbumController extends Controller
{
    public function __invoke(UpdateAdminAlbumRequest $request, string $albumId)
    {
        $album = Album::withoutGlobalScopes()
            ->withTrashed()
            ->with(['userOwner:id,first_name,last_name,email,avatar'])
            ->find($albumId);

        if (!$album) {
            throw AlbumException::albumNotFound();
        }

        // Only allow updating album_name and description
        $albumData = $request->validated();
        
        // Update only allowed fields
        if (isset($albumData['album_name'])) {
            $album->album_name = $albumData['album_name'];
        }
        
        if (isset($albumData['description'])) {
            $album->description = $albumData['description'];
        }
        
        $album->save();

        // Refresh medias count
        $album->loadCount('medias');

        return new AdminAlbumResource($album->fresh());
    }
}

