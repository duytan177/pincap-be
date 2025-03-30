<?php

namespace App\Http\Controllers\Medias;

use App\Enums\Album_Media\Privacy;
use App\Http\Controllers\Controller;
use App\Http\Requests\Medias\MediaByUserIdRequest;
use App\Http\Resources\Medias\Media\MediaCollection;
use App\Models\Media;
use App\Traits\OrderableTrait;
use Illuminate\Http\Request;

class GetListMediaByUserIdController extends Controller
{
    use OrderableTrait;
    public function __invoke(Request $request)
    {
        $userId = $request->input("user_id");
        $perPage = $request->input("per_page");
        $page = $request->input("page");
        $query = $request->input("query");
        $mediaType = $request->input("type");
        $searches = [
            "user_id" => $userId
        ];
        if (!empty($query)){
            $searches += [
                "title" => $query,
                "description" => $query,
                "user_name" => $query,
                "tag_name" => $query,
            ];
        }

        if (!empty($mediaType)) {
            $searches += [
                "type" => $mediaType
            ];
        }

        $order = $this->getAttributeOrder($request->input(key: "order_key"), $request->input("order_type"));
        $medias = Media::getList($searches, true, Privacy::PUBLIC, false, $order)->with("reactions")
                        ->paginate($perPage, ['*'], 'page', $page);

        return MediaCollection::make($medias);
    }
}
