<?php

namespace App\Http\Controllers\Albums;

use App\Enums\Album_Media\AlbumRole;
use App\Http\Controllers\Controller;
use App\Models\UserAlbum;
use App\Enums\Album_Media\InvitationStatus;
use App\Events\AlbumInvitationEvent;
use App\Exceptions\Albums\AlbumException;
use App\Models\Album;
use Tymon\JWTAuth\Facades\JWTAuth;

class AddMemberIntoAlbumController extends Controller
{
    public function __invoke($albumId, $userId)
    {
        $inviter = JWTAuth::user();
        $album = Album::findOrFailWithPermission($albumId, $inviter->id, [AlbumRole::OWNER], [InvitationStatus::ACCEPTED]);

        $existing = UserAlbum::where('album_id', $albumId)
            ->where('user_id', $userId)
            ->first();

        if ($existing) {
            $status = $existing->getAttribute('invitation_status');
            if ($status == InvitationStatus::INVITED) {
                throw AlbumException::alreadyInvited();
            }
            if ($status == InvitationStatus::ACCEPTED) {
                throw AlbumException::alreadyMember();
            }

            // Re-invite when previously rejected
            $existing->update(['invitation_status' => InvitationStatus::INVITED]);
            event(new AlbumInvitationEvent($album, $inviter, $userId));
            return response()->json(responseWithMessage("Invite sent successfully"), 200);
        }

        UserAlbum::create([
            "user_id" => $userId,
            "album_id" => $albumId,
            "invitation_status" => InvitationStatus::INVITED,
        ]);

        event(new AlbumInvitationEvent($album, $inviter, $userId));

        return response()->json(responseWithMessage("Invite sent successfully"), 201);
    }
}
