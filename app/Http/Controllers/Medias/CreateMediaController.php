<?php

namespace App\Http\Controllers\Medias;

use App\Enums\Album_Media\MediaType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Medias\CreateMediaRequest;
use App\Models\Media;
use App\Models\MediaTag;
use App\Models\Tag;
use App\Traits\S3UploadTrait;
use Ramsey\Uuid\Guid\Guid;
use Ramsey\Uuid\Uuid;
use Tymon\JWTAuth\Facades\JWTAuth;

class CreateMediaController extends Controller
{
    use S3UploadTrait;

    const IMAGE = "IMAGE";
    const VIDEO = "VIDEO";

    public function __invoke(CreateMediaRequest $request)
    {
        $mediaData = $request->validated();
        $userId = JWTAuth::user()->getAttribute("id");
        $mediaData["media_owner_id"] = $userId;

        if (isset($mediaData["media"])) {
            $mediaData = array_merge($mediaData, $this->handleMediaFile($mediaData["media"]));
        }

        $mediaNew = Media::create($mediaData);
        $mediaId = $mediaNew->getAttribute("id");

        if (isset($mediaData["album_id"])) {
            $mediaNew->albums()->attach([
                $request->album_id => ['id' => Guid::uuid4()->toString(), "created_at" => now()]
            ]);
        }

        if (isset($mediaData["tags_name"])) {
            $this->attachTagtoMedia($mediaData["tags_name"], $mediaId, $userId);
        }

        return response()->json(["message" => "Created media successfully"], 201);
    }

    private function handleMediaFile($file)
    {
        [$type, $mediaType] = $this->getTypeMedia($file->getMimeType());
        $mediaUrl = $this->uploadToS3($file, $mediaType);

        return [
            'type' => $type,
            'media_url' => $mediaUrl,
        ];
    }
    private function attachTagtoMedia($tags, $mediaId, $userId)
    {
        $tagsInDB = Tag::whereIn('tag_name', $tags)->pluck('tag_name', 'id')->toArray();
        $newTags = array_diff($tags, array_values($tagsInDB));
        $tagIds = array_keys($tagsInDB);
        $now = now();
        if (!empty($newTags)) {
            $newTagsInsert = array_map(function ($tagName) use ($userId, $now) {
                return [
                    'id' => Uuid::uuid4()->toString(),
                    'tag_name' => $tagName,
                    "owner_user_created_id" => $userId,
                    'created_at' => $now
                ];
            }, $newTags);

            Tag::insert($newTagsInsert);

            $newTagsInDB = array_column($newTagsInsert, 'id');


            $tagIds = array_merge($tagIds, $newTagsInDB);
        }

        $mediaTagData = array_map(function ($tagId) use ($mediaId, $now) {
            return [
                'id' => Uuid::uuid4()->toString(),
                'media_id' => $mediaId,
                'tag_id' => $tagId,
                "created_at" => $now
            ];
        }, $tagIds);

        MediaTag::insert($mediaTagData);
    }

    private function getTypeMedia($mimeType)
    {
        $image = strtolower(self::IMAGE);
        $video = strtolower(self::VIDEO);

        if (str_starts_with($mimeType, $image)) {
            $type = MediaType::getValue(self::IMAGE);
            $typeName = $image;
        } else {
            $type = MediaType::getValue(self::VIDEO);
            $typeName = $video;
        }

        return [$type, $typeName];
    }
}
