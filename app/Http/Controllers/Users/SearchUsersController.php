<?php

namespace App\Http\Controllers\Users;

use App\Enums\Album_Media\AlbumRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Users\SearchUserOrTagNameRequest;
use App\Http\Resources\Users\Information\UserInfoCollection;
use App\Models\Media;
use App\Models\User;
use Tymon\JWTAuth\Facades\JWTAuth;

class SearchUsersController extends Controller
{
    public function __invoke(SearchUserOrTagNameRequest $request)
    {
        $target = $request->input("target");
        $textSearch = "%" . $target . "%";
        $userId = null;
        $albumId = $request->input("album_id");

        if ($token = $request->bearerToken()) {
            $userId = JWTAuth::setToken($token)->authenticate()->getAttribute("id");
        }

        $users = User::whereDoesntHave('blockedUsers', function ($query) use ($userId) {
            $query->where('followee_id', $userId);
        })->withCount("followers")->where(function ($query) use ($textSearch) {
            $query->where("first_name", "like", $textSearch)
                ->orWhere("last_name", "like", $textSearch)
                ->orWhere("email", "like", $textSearch)
                ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", [$textSearch]);
        })
            ->when(!empty($albumId), function ($query) use ($albumId) {
                $query->leftJoin('user_album as ua', function ($join) use ($albumId) {
                    $join->on('users.id', '=', 'ua.user_id')
                        ->where('ua.album_id', '=', $albumId)
                        ->whereNull('ua.deleted_at');
                })
                    ->addSelect('users.*', 'ua.invitation_status as status');
            })
            ->whereNot("users.id", "=", $userId)
            ->get();

        // Load top 4 latest medias for each user efficiently
        $userIds = $users->pluck('id')->toArray();
        if (!empty($userIds)) {
            $allMedias = Media::whereIn('media_owner_id', $userIds)
                ->with("reactions")
                ->orderBy('media_owner_id')
                ->orderBy('updated_at', 'desc')
                ->get()
                ->groupBy('media_owner_id')
                ->map(function ($medias) {
                    return $medias->take(4)->values();
                });

            // Attach medias to users
            $users->each(function ($user) use ($allMedias) {
                $user->setRelation('medias', $allMedias->get($user->id, collect()));
            });
        }

        // Get list of user IDs that current user is following (to avoid N+1 queries)
        $followingUserIds = [];
        if ($userId) {
            $currentUser = User::find($userId);
            if ($currentUser) {
                $followingUserIds = $currentUser->followees()->pluck('users.id')->toArray();
            }
        }

        // Merge following user IDs into request so resources can access them
        $request->merge(['following_user_ids' => $followingUserIds]);

        return UserInfoCollection::make($users);
    }
}
