<?php

namespace App\Http\Controllers\Albums;

use App\Enums\Album_Media\AlbumRole;
use App\Enums\Album_Media\InvitationStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Albums\AddMediasToAlbumRequest;
use App\Models\AlbumMedia;
use App\Models\Album;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Exceptions\Albums\AlbumException;
use App\Exceptions\MediaException;

class RemoveMediasFromAlbumController extends Controller
{
    public function __invoke(AddMediasToAlbumRequest $request)
    {
        $data = $request->validated();
        $currentUserId = JWTAuth::user()->getAttribute("id");

        // Kiểm tra user phải là OWNER hoặc EDIT và có invitation_status là ACCEPTED
        Album::findOrFailWithPermission(
            $data["album_id"],
            $currentUserId,
            [AlbumRole::OWNER, AlbumRole::EDIT],
            [InvitationStatus::ACCEPTED]
        );

        // Nếu pass validation, thực hiện xóa
        $deleted = AlbumMedia::where("album_id", $data["album_id"])
            ->whereIn("media_id", $data["medias_id"])
            ->delete();

        if ($deleted === 0) {
            throw MediaException::deleteFaired();
        }

        return responseWithMessage("Remove medias from album successfully");
    }
}
