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

        $medias = $this->applyBlockedUsersFilter(
            Media::where([
                ["is_created", true],
                ["privacy", Privacy::PUBLIC],
            ]),
            $this->getBlockedUserIds($request)
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
