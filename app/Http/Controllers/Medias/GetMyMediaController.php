<?php

namespace App\Http\Controllers\Medias;

use App\Enums\Album_Media\MediaType;
use App\Enums\Album_Media\Privacy;
use App\Http\Controllers\Controller;
use App\Http\Requests\Medias\MyMediaRequest;
use App\Http\Resources\Medias\Media\MediaCollection;
use App\Models\Media;
use App\Traits\OrderableTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Tymon\JWTAuth\Facades\JWTAuth;

class GetMyMediaController extends Controller
{
    use OrderableTrait;
    public function __invoke(Request $request)
    {
        $isCreated = $request->input("is_created") == "false" ? false : true;

        $perPage = $request->input('per_page', 15);
        $page = $request->input('page', 1);
        $mediaType = $request->input("type");
        $query = $request->input("query");
        $searches = [
            "my_media" => true
        ];
        if (!empty($query)){
            $searches += [
                "title" => $query,
                "description" => $query,
                "user_name" => $query,
                "tag_name" => $query
            ];
        }

        if (MediaType::hasValue($mediaType)) {
            $searches += [
                "type" => $mediaType
            ];
        }

        $order = $this->getAttributeOrder($request->input("order_key"), $request->input("order_type"));
        $medias = Media::getList($searches, $isCreated, "",true, $order)->with("reactions");

        $medias = $medias->paginateOrAll($request);

        return MediaCollection::make($medias);
    }
}
