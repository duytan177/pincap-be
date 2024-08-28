<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Http\Requests\Users\SearchUserOrTagNameRequest;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Tymon\JWTAuth\Facades\JWTAuth;

class SearchUserOrTagNameController extends Controller
{
    public function __invoke(SearchUserOrTagNameRequest $request)
    {
        $target = $request->input('target');
        $textSearch = '%' . $target . '%';

        $userId = null;
        if ($token = $request->bearerToken()) {
            $userId = JWTAuth::setToken($token)->authenticate()->getAttribute("id");
        }

        $users = User::whereDoesntHave('blockedUsers', function ($query) use ($userId) {
            $query->where('follower_id', $userId);
        })->select("id", DB::raw("CONCAT(first_name, ' ', last_name) as name"), 'avatar')
            ->where("first_name", "like", $textSearch)
            ->orWhere("last_name", "like", $textSearch)
            ->get();

        $tags = Tag::select('id', 'tag_name as name')
            ->where("tag_name", "like", $textSearch)
            ->get();

        $dataSorted = $users->concat($tags)->sortBy("name")->values();

        return $dataSorted;
    }
}
