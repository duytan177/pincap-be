<?php

namespace App\Http\Controllers\Albums;

use App\Enums\Album_Media\AlbumRole;
use App\Enums\Album_Media\InvitationStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Albums\AlbumRequest;
use App\Models\Album;
use Illuminate\Support\Facades\Auth;

class UpdateAlbumController extends Controller
{
    //
    public function __invoke($albumId, AlbumRequest $request)
    {
        $dataAlbum = $request->validated();
        $userId = Auth::user()->id;
        $album = Album::findOrFailWithPermission($albumId, $userId, [AlbumRole::OWNER], [InvitationStatus::ACCEPTED]);

        $album->update($dataAlbum);

        return responseWithMessage("Updated album successfully");
    }
}
