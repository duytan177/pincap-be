<?php

namespace App\Http\Controllers\Admin\Albums;

use App\Http\Controllers\Controller;
use App\Models\Album;
use App\Models\AlbumMedia;
use App\Exceptions\Admin\AlbumException;
use Illuminate\Support\Facades\DB;

class ForceDeleteAdminAlbumController extends Controller
{
    public function __invoke(string $albumId)
    {
        $album = Album::withoutGlobalScopes()
            ->withTrashed()
            ->find($albumId);

        if (!$album) {
            throw AlbumException::albumNotFound();
        }

        // Use transaction to ensure data consistency
        DB::transaction(function () use ($album) {
            // Force delete all album_media relationships
            AlbumMedia::where('album_id', $album->id)->forceDelete();

            // Force delete the album
            $album->forceDelete();
        });

        return responseWithMessage("Album permanently deleted successfully");
    }
}

