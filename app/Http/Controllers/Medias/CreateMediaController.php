<?php

namespace App\Http\Controllers\Medias;

use App\Enums\Album_Media\MediaType;
use App\Events\MediaCreatedEvent;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Medias\Shared\MediaHandle;
use App\Http\Requests\Medias\CreateMediaRequest;
use App\Http\Resources\Medias\Media\MediaResource;
use App\Models\Media;
use App\Services\GoogleVisionService;
use App\Services\KafkaProducerService;
use App\Traits\AWSS3Trait;
use Ramsey\Uuid\Guid\Guid;
use Tymon\JWTAuth\Facades\JWTAuth;

class CreateMediaController extends Controller
{
    use AWSS3Trait;

    private const TOPIC = "user_behavior";

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

        // Google Vision Safe Search Detection
        if (config("services.google_vision.enable")) {

            $base64Images = $this->covertMediaImageToBase64($mediaData["media"] ?? []);
            $visionService = new GoogleVisionService();
            $annotations = $visionService->detectSafeSearch($base64Images);

            // check policy violation
            $isViolation = $this->checkPolicyViolation($annotations);

            $mediaData["safe_search_data"] = $annotations;
            $mediaData["is_policy_violation"] = $isViolation;

            // Mark media as deleted if it violates policy
            if ($isViolation) {
                $mediaData["deleted_at"] = now();
            }
        }

        $mediaNew = Media::create($mediaData);
        $mediaId = $mediaNew->getAttribute("id");

        if (isset($mediaData["album_id"])) {
            $mediaNew->albums()->attach([
                $request->album_id => [
                    'id' => Guid::uuid4()->toString(),
                    'added_by_user_id' => $userId,
                    'created_at' => now()
                ]
            ]);
        }

        if (isset($mediaData["tags_name"])) {
            MediaHandle::attachTagtoMedia($mediaData["tags_name"], $mediaId, $userId);
        }

        if ($mediaNew->getAttribute("is_created")) {
            $data = json_encode([
                'media_id' => $mediaNew->getAttribute("id"),
                "media_url" => $mediaNew->getAttribute("media_url"),
                "media_name" => $mediaNew->getAttribute("media_name"),
                "description" => $mediaNew->getAttribute("description"),
                "tag_name" => $mediaNew->tags->pluck("tag_name")->implode(', '),
                "user_id" => $mediaNew->getAttribute("media_owner_id"),
                'timestamp' => now()->toISOString(),
            ]);
            (new KafkaProducerService(self::TOPIC))->send($data);
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

    private function covertMediaImageToBase64($medias): array
    {
        return collect($medias ?? [])
            ->filter(fn($file) => str_starts_with($file->getMimeType(), 'image/'))
            ->map(function ($file) {
                $mimeType = $file->getMimeType();
                $fileContents = file_get_contents($file->getRealPath());
                $base64 = base64_encode($fileContents);

                return $base64;
            })
            ->values()
            ->all();
    }

    /**
     * Kiểm tra xem có ảnh nào vi phạm chính sách không
     */
    private function checkPolicyViolation(array $annotations): bool
    {
        $violationLevels = ['LIKELY', 'VERY_LIKELY'];
        $labelSpoof = "spoof"; // just to initialize
        foreach ($annotations as $annotation) {
            foreach ($annotation as $label => $level) {
                if ($label != $labelSpoof && in_array(strtoupper($level), $violationLevels, true)) {
                    return true;
                }
            }
        }

        return false;
    }
}
