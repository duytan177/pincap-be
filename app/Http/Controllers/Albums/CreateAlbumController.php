<?php

namespace App\Http\Controllers\Albums;

use App\Enums\Album_Media\AlbumRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Albums\AlbumRequest;
use App\Models\Album;
use Illuminate\Support\Facades\Auth;
use Ramsey\Uuid\Uuid;

class CreateAlbumController extends Controller
{
    public function __invoke(AlbumRequest $request)
    {
        $album = Album::create($request->validated());

        $album->userOwner()->attach(Auth::id(), [
            'id' => Uuid::uuid4()->toString(),
            "invitation_status" => true,
            "album_role" => AlbumRole::OWNER
        ]);

        return $album;
    }
}
