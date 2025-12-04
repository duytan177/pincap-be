<?php

namespace App\Http\Controllers\Medias;

use App\Enums\Album_Media\MediaType;
use App\Enums\Album_Media\Privacy;
use App\Http\Controllers\Controller;
use App\Http\Resources\Medias\Media\MediaCollection;
use App\Models\Media;
use App\Services\ElasticsearchService;
use App\Services\KafkaProducerService;
use App\Services\MediaIntegrateService;
use App\Traits\OrderableTrait;
use App\Traits\SharedTrait;
use Illuminate\Http\Request;

class GetAllMediaController extends Controller
{
    use SharedTrait, OrderableTrait;
    protected MediaIntegrateService $mediaIntegrateService;

    public function __construct(MediaIntegrateService $mediaIntegrateService)
    {
        $this->mediaIntegrateService = $mediaIntegrateService;
    }
    public function __invoke(Request $request)
    {
        $query = $request->input("query");
        $mediaType = $request->input("type");
        $mediaId = $request->input("media_id");
        $userId = $request->user()->getAttribute("id");
        $media = Media::with(relations: ["tags"])->find($mediaId);
        $es = ElasticsearchService::getInstance();
        $index = config('services.elasticsearch.index');

        if ($media) {
            $media_es = $es->getMediaById($index, $media->getAttribute("id"));
            if ($media_es) {
                $results = $es->searchEmbedding($index, $media_es['embedding'], null, null, 0.85, 0, 10000);
                $mediaIds = $es->formatMediaIds($results);
                $medias = Media::whereIn("id", $mediaIds)->where("privacy", Privacy::PUBLIC);
                $medias = $this->applyBlockedUsersFilter(
                    $medias,
                    blockedUserIds: $this->getBlockedUserIds($request)
                );
                if ($this->getBearerToken($request)) {

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
            $medias = $medias->with([
                "reactions" => function ($query) use ($userId) {
                    $query->where("user_id", $userId)->limit(1);
                }
            ]);
        }

        if (!empty($query)) {
            // Pagination parameters
            $page = (int) $request->input("page", 1);
            $perPage = (int) $request->input("per_page", 20);
            $from = ($page - 1) * $perPage;

            // Call MediaIntegrateService to search media by text
            $result = $this->mediaIntegrateService->searchMediaByText($userId, $query, $from, 10000);
            if ($result['error']) {
                return response()->json([
                    "error" => true,
                    "message" => $result['message'],
                    "detail" => $result['detail']
                ], 500);
            }

            $data = $result['data'];
            $mediaIds = $data["media_ids"] ?? [];
            if (!empty($mediaIds)) {
                $medias = $medias->orWhere("id", $mediaIds);
            }
        }

        $medias = $medias->paginateOrAll($request);

        return new MediaCollection($medias);
    }
}
