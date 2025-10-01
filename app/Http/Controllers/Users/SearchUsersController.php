<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Http\Requests\Users\SearchUserOrTagNameRequest;
use App\Http\Resources\Users\Information\UserInfoCollection;
use App\Models\User;
use Tymon\JWTAuth\Facades\JWTAuth;

class SearchUsersController extends Controller
{
    public function __invoke(SearchUserOrTagNameRequest $request)
    {
        $target = $request->input("target");
        $textSearch = "%" . $target . "%";
        $userId = null;
        if ($token = $request->bearerToken()) {
            $userId = JWTAuth::setToken($token)->authenticate()->getAttribute("id");
        }

        $users = User::whereDoesntHave('blockedUsers', function ($query) use ($userId) {
            $query->where('follower_id', $userId);
        })->withCount("followers")->where(function ($query) use ($textSearch) {
            $query->where("first_name", "like", $textSearch)
                ->orWhere("last_name", "like", $textSearch)
                ->orWhere("email", "like", $textSearch)
                ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", [$textSearch]);
                })->get();


        return UserInfoCollection::make($users);
    }
}
