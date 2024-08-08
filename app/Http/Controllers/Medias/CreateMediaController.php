<?php

namespace App\Http\Controllers\Medias;

use App\Enums\Album_Media\MediaType;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Medias\Shared\MediaHandle;
use App\Http\Requests\Medias\CreateMediaRequest;
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
            MediaHandle::attachTagtoMedia($mediaData["tags_name"], $mediaId, $userId);
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
