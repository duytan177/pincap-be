<?php

namespace App\Http\Controllers\Albums;

use App\Enums\Album_Media\AlbumRole;
use App\Enums\Album_Media\InvitationStatus;
use App\Http\Controllers\Controller;
use App\Models\Album;
use Illuminate\Support\Facades\Auth;

class DeleteAlbumController extends Controller
{
    public function __invoke($albumId)
    {
        $userId = Auth::user()->id;
        $album = Album::findOrFailWithPermission($albumId, $userId, [AlbumRole::OWNER], [InvitationStatus::ACCEPTED]);

        $album->delete();

        return responseWithMessage("Deleted album successfully");
    }
}
