<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Http\Requests\Users\SearchUserOrTagNameRequest;
use App\Http\Resources\Users\Information\UserInfoCollection;
use App\Models\User;

class SearchUsersController extends Controller
{
    public function __invoke(SearchUserOrTagNameRequest $request)
    {
        $target = $request->input("target");
        $textSearch = "%" . $target . "%";
        $users = User::withCount("followers")->where("first_name", "like", $textSearch)
            ->orWhere("last_name", "like", $textSearch)
            ->get();

        return UserInfoCollection::make($users);
    }
}
