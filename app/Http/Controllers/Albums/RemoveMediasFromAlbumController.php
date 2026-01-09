<?php

namespace App\Http\Controllers\Albums;

use App\Enums\Album_Media\AlbumRole;
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

        $isOwner = Album::query()->where('id', $data["album_id"])->ownedBy($currentUserId)->exists();

        // Validate ALL medias trước khi xóa
        // Allow removal if:
        // 1. User is album owner (can remove any media)
        // 2. User is the one who added the media (added_by_user_id)

        if (!$isOwner) {
            // Non-owner: Kiểm tra tất cả medias có phải do họ add không
            $userCanRemove = AlbumMedia::where("album_id", $data["album_id"])
                ->whereIn("media_id", $data["medias_id"])
                ->where("added_by_user_id", $currentUserId)
                ->pluck("media_id")
                ->toArray();

            // Check xem có media nào user không có quyền xóa không
            $cannotRemove = array_diff($data["medias_id"], $userCanRemove);
            if (!empty($userCanRemove) && !empty($cannotRemove)) {
                throw MediaException::cannotRemoveMediasNotCreatedByUser();
            }
        }

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
