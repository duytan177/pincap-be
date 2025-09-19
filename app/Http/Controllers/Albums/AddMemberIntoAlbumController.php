<?php

namespace App\Http\Controllers\Albums;

use App\Http\Controllers\Controller;
use App\Models\UserAlbum;
use App\Enums\Album_Media\InvitationStatus;
use App\Events\AlbumInvitationEvent;
use App\Models\Album;
use Tymon\JWTAuth\Facades\JWTAuth;

class AddMemberIntoAlbumController extends Controller
{
    public function __invoke($albumId, $userId)
    {
        $inviter = JWTAuth::user();
        $album = Album::findOrFail($albumId);

        $existing = UserAlbum::where('album_id', $albumId)
            ->where('user_id', $userId)
            ->first();

        if ($existing) {
            $status = $existing->getAttribute('invitation_status');
            if ($status == InvitationStatus::INVITED) {
                return responseWithMessage("User already invited");
            }
            if ($status == InvitationStatus::ACCEPTED) {
                return responseWithMessage("User already a member");
            }

            // Re-invite when previously rejected
            $existing->update(['invitation_status' => InvitationStatus::INVITED]);
            event(new AlbumInvitationEvent($album, $inviter, $userId));
            return responseWithMessage("Invite sent successfully");
        }

        UserAlbum::create([
            "user_id" => $userId,
            "album_id" => $albumId,
            "invitation_status" => InvitationStatus::INVITED,
        ]);

        event(new AlbumInvitationEvent($album, $inviter, $userId));

        return responseWithMessage("Add user successfully");
    }
}
