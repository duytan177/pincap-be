<?php

namespace App\Http\Controllers\Albums;

use App\Http\Controllers\Controller;
use App\Http\Requests\PaginateRequest;
use App\Http\Resources\Albums\AlbumCollection;
use App\Models\Album;
use App\Traits\OrderableTrait;
use Illuminate\Http\Request;
use Illuminate\Log\Logger;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class GetMyAlbumRoleMemberController extends Controller
{
    use OrderableTrait;

    public function __invoke(Request $request)
    {
        $userId = Auth::id();
        $searches = [];
        $query = $request->input("query");
        if (!empty($query)) {
            $searches = [
                "album_name" => $query,
                "description" => $query
            ];
        }
        if ($request->input("media_id")) {
            $searches["media_id"] = $request->input("media_id");
        }
        $order = $this->getAttributeOrder($request->input("order_key"), $request->input("order_type"));

        $albums = Album::getList($searches, order: $order);

        $albums = $albums->withCount("medias")->whereHas("members", function ($query) use ($userId) {
            $query->where("user_id", $userId);
        })->whereDoesntHave('members.blockedUsers', function ($query) use ($userId) {
            $query->where('followee_id', $userId);
        })->paginateOrAll($request);

        return AlbumCollection::make($albums);
    }
}
