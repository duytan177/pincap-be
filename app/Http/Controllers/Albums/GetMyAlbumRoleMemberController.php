<?php

namespace App\Http\Controllers\Albums;

use App\Http\Controllers\Controller;
use App\Http\Resources\Albums\AlbumCollection;
use App\Models\Album;
use Illuminate\Support\Facades\Auth;

class GetMyAlbumRoleMemberController extends Controller
{
    //
    public function __invoke()
    {
        $userId = Auth::id();

        $albums = Album::whereHas("members", function ($query) use ($userId) {
            $query->where("user_id", $userId);
        })->get();

        return AlbumCollection::make($albums);
    }
}
