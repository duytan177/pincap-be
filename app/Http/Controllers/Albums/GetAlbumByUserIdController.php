<?php

namespace App\Http\Controllers\Albums;

use App\Enums\Album_Media\Privacy;
use App\Http\Controllers\Controller;
use App\Http\Resources\Albums\AlbumCollection;
use App\Models\Album;
use App\Models\User;
use App\Traits\OrderableTrait;
use Illuminate\Http\Request;

class GetAlbumByUserIdController extends Controller
{
    use OrderableTrait;

    public function __invoke(Request $request)
    {
        $userId = $request->input("user_id");

        $user = User::findOrFail($userId);
        $searches = [
            "user_id" => $user->getAttribute("id"),
            "album_name" => $request->input("query"),
            "description" => $request->input("query")
        ];
        $order = $this->getAttributeOrder($request->input("order_key"), $request->input("order_type"));
        $albums = Album::getList($searches, Privacy::PUBLIC, false, $order);

        $albums = $albums->paginateOrAll($request);
        return AlbumCollection::make($albums);
    }
}
