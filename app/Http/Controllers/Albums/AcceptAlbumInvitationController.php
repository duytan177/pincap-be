<?php

namespace App\Http\Controllers\Albums;

use App\Enums\Album_Media\InvitationStatus;
use App\Http\Controllers\Controller;
use App\Exceptions\Albums\AlbumException;
use App\Models\UserAlbum;
use Tymon\JWTAuth\Facades\JWTAuth;

class AcceptAlbumInvitationController extends Controller
{
    public function __invoke($albumId)
    {
        $userId = JWTAuth::user()->getAttribute('id');

        $invite = UserAlbum::where('album_id', $albumId)
            ->where('user_id', $userId)
            ->first();

        if (!$invite) {
            throw AlbumException::invitationNotFound();
        }

        if ($invite->getAttribute('invitation_status') == InvitationStatus::ACCEPTED) {
            throw AlbumException::alreadyMember();
        }

        $invite->update(['invitation_status' => InvitationStatus::ACCEPTED]);

        return response()->json(responseWithMessage('Invitation accepted'), 200);
    }
}


