<?php

namespace App\Http\Controllers\Medias;

use App\Enums\Album_Media\MediaType;
use App\Enums\Album_Media\Privacy;
use App\Http\Controllers\Controller;
use App\Http\Resources\Medias\Media\MediaCollection;
use App\Models\Media;
use App\Services\ElasticsearchService;
use App\Services\KafkaProducerService;
use App\Traits\OrderableTrait;
use App\Traits\SharedTrait;
use Illuminate\Http\Request;

class GetAllMediaController extends Controller
{
    use SharedTrait, OrderableTrait;

    public function __invoke(Request $request)
    {
        $query = $request->input("query");
        $mediaType = $request->input("type");
        $mediaId = $request->input("media_id");

        $media = Media::with(relations: ["tags"])->find($mediaId);
        $es = ElasticsearchService::getInstance();
        $index = config('services.elasticsearch.index');

        if ($media) {
            $media_es = $es->getMediaById($index, $media->getAttribute("id"));
            if ($media_es) {
                $results = $es->searchEmbedding($index, $media_es['embedding'], null, null, 0.8, 0, 10000);
                $mediaIds = $es->formatMediaIds($results);
                $medias = Media::whereIn("id", $mediaIds)->where("privacy", Privacy::PUBLIC);
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
                return new MediaCollection($medias->paginateOrAll($request));
            }
        }

        $searches = [];
        if (!empty($query)) {
            $searches = [
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
        $order = $this->getAttributeOrder($request->input(key: "order_key"), $request->input("order_type"));
        $medias = Media::getList($searches, true, Privacy::PUBLIC , order: $order);
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


        $medias = $medias->paginateOrAll($request);

        return new MediaCollection($medias);
    }
}
