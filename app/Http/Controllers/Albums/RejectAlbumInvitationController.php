<?php

namespace App\Http\Controllers\Albums;

use App\Enums\Album_Media\InvitationStatus;
use App\Http\Controllers\Controller;
use App\Exceptions\Albums\AlbumException;
use App\Models\UserAlbum;
use Tymon\JWTAuth\Facades\JWTAuth;

class RejectAlbumInvitationController extends Controller
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

        if ($invite->getAttribute('invitation_status') == InvitationStatus::REJECTED) {
            throw AlbumException::invitationAlreadyRejected();
        }

        $invite->update(['invitation_status' => InvitationStatus::REJECTED]);

        return response()->json(responseWithMessage('Invitation rejected'), 200);
    }
}


