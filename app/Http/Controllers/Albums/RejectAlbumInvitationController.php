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

        $status = $invite->getAttribute('invitation_status');
        if ($status == InvitationStatus::ACCEPTED) {
            throw AlbumException::alreadyMember();
        }

        // Delete the record on reject to simplify future reinvitations
        $invite->delete();

        return response()->json(responseWithMessage('Invitation rejected'), 200);
    }
}


