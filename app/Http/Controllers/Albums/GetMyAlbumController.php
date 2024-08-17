<?php

namespace App\Http\Controllers\Albums;

use App\Http\Controllers\Controller;
use App\Http\Requests\PaginateRequest;
use App\Http\Resources\Albums\AlbumCollection;
use App\Models\Album;
use Illuminate\Support\Facades\Auth;

class GetMyAlbumController extends Controller
{
    public function __invoke(PaginateRequest $request)
    {
        $perPage = $request->input("per_page");
        $page = $request->input("page");

        $userId = Auth::id();

        $albums = Album::whereHas("userOwner", function ($query) use ($userId) {
            $query->where("user_id", $userId);
        })->paginate($perPage, ['*'], 'page', $page);

        return AlbumCollection::make($albums);
    }
}
