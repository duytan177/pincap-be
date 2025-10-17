<?php

namespace App\Http\Controllers\Albums;

use App\Enums\Album_Media\AlbumRole;
use App\Enums\Album_Media\InvitationStatus;
use App\Http\Controllers\Controller;
use App\Exceptions\Albums\AlbumException;
use App\Models\Album;
use App\Models\UserAlbum;
use Illuminate\Support\Facades\Auth;

class RemoveMemberFromAlbumController extends Controller
{
    public function __invoke(string $albumId, string $userId)
    {
        $currentUserId = Auth::id();

        // Ensure current user is owner of the album
        Album::findOrFailWithPermission($albumId, $currentUserId, [AlbumRole::OWNER], [InvitationStatus::ACCEPTED]);

        $membership = UserAlbum::where('album_id', $albumId)
            ->where('user_id', $userId)
            ->whereIn("album_role", [AlbumRole::EDIT, AlbumRole::VIEW])
            ->firstOrFail();

        $membership->delete();
        return responseWithMessage('User removed from album');
    }
}


