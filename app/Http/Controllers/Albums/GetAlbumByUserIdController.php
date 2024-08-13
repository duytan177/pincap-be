<?php

namespace App\Http\Controllers\Albums;

use App\Http\Controllers\Controller;
use App\Http\Requests\PaginateRequest;
use App\Http\Resources\Albums\AlbumCollection;
use App\Models\User;

class GetAlbumByUserIdController extends Controller
{
    public function __invoke(PaginateRequest $request)
    {
        $userId = $request->input("user_id");
        $perPage = $request->input("per_page");
        $page = $request->input("page");

        $user = User::findOrFail($userId);

        $albums = $user->albums()->paginate($perPage, ['*'], 'page', $page);
        return AlbumCollection::make($albums);
    }
}
