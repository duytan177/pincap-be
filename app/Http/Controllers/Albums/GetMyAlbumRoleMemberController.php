<?php

namespace App\Http\Controllers\Albums;

use App\Http\Controllers\Controller;
use App\Http\Requests\PaginateRequest;
use App\Http\Resources\Albums\AlbumCollection;
use App\Models\Album;
use Illuminate\Http\Request;
use Illuminate\Log\Logger;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class GetMyAlbumRoleMemberController extends Controller
{
    //
    public function __invoke(Request $request)
    {
        $perPage = $request->input("per_page");
        $page = $request->input("page");
        $userId = Auth::id();
        $searches = [];
        $query = $request->input("query");
        if (!empty($query)) {
            $searches = [
                "album_name" => $query,
                "description" => $query
            ];
        }
        $albums = Album::getList($searches);

        $albums = $albums->withCount("medias")->whereHas("members", function ($query) use ($userId) {
            $query->where("user_id", $userId);
        })->whereDoesntHave('members.blockedUsers', function ($query) use ($userId) {
            $query->where('follower_id', $userId);
        })->paginate($perPage, ['*'], 'page', $page);

        return AlbumCollection::make($albums);
    }
}
