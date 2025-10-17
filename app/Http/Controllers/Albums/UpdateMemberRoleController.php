<?php

namespace App\Http\Controllers\Albums;

use App\Enums\Album_Media\AlbumRole;
use App\Enums\Album_Media\InvitationStatus;
use App\Http\Controllers\Controller;
use App\Models\Album;
use App\Models\UserAlbum;
use App\Http\Requests\Albums\UpdateMemberRoleRequest;
use Illuminate\Support\Facades\Auth;

class UpdateMemberRoleController extends Controller
{
    public function __invoke(UpdateMemberRoleRequest $request, string $albumId, string $userId)
    {
        $role = $request->input('role');
        $currentUserId = Auth::id();

        // Ensure current user is owner of the album
        Album::findOrFailWithPermission($albumId, $currentUserId, [AlbumRole::OWNER], [InvitationStatus::ACCEPTED]);

        $membership = UserAlbum::where('album_id', $albumId)
            ->where('user_id', $userId)
            ->where("album_role", '!=', AlbumRole::OWNER)
            ->where("invitation_status", InvitationStatus::ACCEPTED)
            ->firstOrFail();

        $membership->update(['album_role' => AlbumRole::getKey($role)]);
        return responseWithMessage('Role updated successfully');
    }
}


