<?php

namespace App\Http\Controllers\Medias;

use App\Enums\Album_Media\MediaType;
use App\Enums\Album_Media\Privacy;
use App\Http\Controllers\Controller;
use App\Http\Resources\Medias\Media\MediaCollection;
use App\Models\Media;
use App\Traits\OrderableTrait;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Http\Resources\Medias\Media\MyReactedMediaCollection;

class GetMyReactedMediaController extends Controller
{
    use OrderableTrait;

    public function __invoke(Request $request)
    {
        $userId = JWTAuth::user()->getAttribute("id");

        $query = $request->input("query");

        $params = [
            "tag_name" => $query,
            "title" => $query,
            "description" => $query,
            "user_name" => $query,
        ];

        $order = $this->getAttributeOrder($request->input("order_key"), $request->input("order_type"));
        $medias = Media::getList($params, true, Privacy::PUBLIC , false, $order)
            ->join('reaction_media', 'reaction_media.media_id', '=', 'medias.id')
            ->join('feelings', 'reaction_media.feeling_id', '=', 'feelings.id')
            ->where('reaction_media.user_id', $userId)
            ->select(
                'medias.*',
                'feelings.id as feeling_id',
                'feelings.feeling_type as feeling_type'
            );

        $medias = $medias->paginateOrAll($request);
        return MyReactedMediaCollection::make($medias);
    }
}
