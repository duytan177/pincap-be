<?php

namespace App\Http\Controllers\Medias;

use App\Enums\Album_Media\Privacy;
use App\Http\Controllers\Controller;
use App\Http\Resources\Medias\Media\MediaCollection;
use App\Models\Media;
use App\Traits\SharedTrait;
use Illuminate\Http\Request;

class GetAllMediaController extends Controller
{
    use SharedTrait;

    public function __invoke(Request $request)
    {
        $perPage = $request->input('per_page', 15);
        $page = $request->input('page', 1);
        $query = $request->input("query");
        $searches = [];
        if (!empty($query)){
            $searches = [
                "title" => $query,
                "description" => $query,
                "user_name" => $query,
                "tag_name" => $query
            ];
        }
        $medias = Media::getList($searches, true, Privacy::PUBLIC);
        $medias = $this->applyBlockedUsersFilter(
            $medias,
            blockedUserIds: $this->getBlockedUserIds($request)
        );

        if ($this->getBearerToken($request)) {
            $userId = $request->user()->getAttribute("id");
            $medias = $medias->with([
                "reactions" => function ($query) use ($userId) {
                    $query->where("user_id", $userId)->limit(1);
                }
            ]);
        }


        $medias = $medias->paginate($perPage, ['*'], 'page', $page);

        return new MediaCollection($medias);
    }
}
