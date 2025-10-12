<?php

namespace App\Http\Controllers\Medias;

use App\Enums\Album_Media\MediaType;
use App\Events\MediaCreatedEvent;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Medias\Shared\MediaHandle;
use App\Http\Requests\Medias\CreateMediaRequest;
use App\Http\Resources\Medias\Media\MediaResource;
use App\Models\Media;
use App\Traits\AWSS3Trait;
use Ramsey\Uuid\Guid\Guid;
use Tymon\JWTAuth\Facades\JWTAuth;

class CreateMediaController extends Controller
{
    use AWSS3Trait;

    public function __invoke(CreateMediaRequest $request)
    {
        $mediaData = $request->validated();
        $userId = JWTAuth::user()->getAttribute("id");
        $mediaData["media_owner_id"] = $userId;

        if (isset($mediaData["media"])) {
            if (count($mediaData["media"]) == 1) {
                $mediaData = array_merge($mediaData, $this->handleMediaFile($mediaData["media"][0]));
            } else {
                $results = $this->handleMediaFilesWithConcurrency($mediaData["media"], 3);
                $mediaData = array_merge($mediaData, $this->formatFinalResult($results));
            }
        }
        $mediaNew = Media::create($mediaData);
        $mediaId = $mediaNew->getAttribute("id");

        if (isset($mediaData["album_id"])) {
            $mediaNew->albums()->attach([
                $request->album_id => ['id' => Guid::uuid4()->toString(), "created_at" => now()]
            ]);
        }

        if (isset($mediaData["tags_name"])) {
            MediaHandle::attachTagtoMedia($mediaData["tags_name"], $mediaId, $userId);
        }

        if ($mediaNew->getAttribute("is_created")) {
            event(new MediaCreatedEvent($mediaNew));
        }

        return response()->json(["message" => "Created media successfully", "media" => MediaResource::make($mediaNew)], 201);
    }
    

    /**
     * 🧩 Format upload results for the final response
     *
     * Rules:
     * - If multiple files uploaded → return `type = null`
     *   and `media_url` as a JSON string of all URLs.
     */
    private function formatFinalResult(array $results): array
    {
        if (empty($results)) {
            return [
                'type' => null,
                'media_url' => null,
            ];
        }

        // ✅ Multiple files: collect all URLs into JSON array
        $urls = array_column($results, 'media_url');

        return [
            'type' => null,
            'media_url' => json_encode($urls), // ["url1","url2","url3"]
        ];
    }
}
