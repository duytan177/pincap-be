<?php

namespace App\Http\Controllers\Medias;

use App\Events\MediaCreatedEvent;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Medias\Shared\MediaHandle;
use App\Exceptions\MediaException;
use App\Http\Requests\Medias\UpdateMediaRequest;
use App\Http\Resources\Medias\Media\MediaResource;
use App\Models\AlbumMedia;
use App\Models\Media;
use App\Services\KafkaProducerService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class UpdateMediaController extends Controller
{
    public const TOPIC = "user_behavior";
    public function __invoke($mediaId, UpdateMediaRequest $request)
    {
        $userId = Auth::user()->id;
        $media = Media::where("media_owner_id", $userId)->findOrFail($mediaId);
        $now = Carbon::now()->toDateTimeString();
        $mediaData = $request->validated();

        if (isset($mediaData["album_id"])) {
            $albumId = $mediaData["album_id"];
            AlbumMedia::updateOrCreate(
                [
                    'media_id' => $mediaId,
                    'album_id' => $albumId,
                ],
                [
                    'added_by_user_id' => $userId,
                    "created_at" => $now
                ]
            );
        }

        if (isset($mediaData["tags_name"])) {
            if ($media->getAttribute('is_created') === true) {
                throw MediaException::cannotUpdateTagsCreated();
            }
            MediaHandle::attachTagtoMedia($mediaData["tags_name"], $mediaId, $userId, $now);
        }

        $media->updateOrFail($mediaData);
        if ($media->getAttribute("is_created") || $media->wasChanged(['media_name', 'description'])) {
            $data = json_encode([
                'media_id' => $media->getAttribute("id"),
                "media_url" => $media->getAttribute("media_url"),
                "media_name" => $media->getAttribute("media_name"),
                "description" => $media->getAttribute("description"),
                "tag_name" => $media->tags->pluck("tag_name")->implode(', '),
                "user_id" => $media->getAttribute("media_owner_id"),
                'timestamp' => now()->toISOString(),
            ]);
            (new KafkaProducerService(self::TOPIC))->send($data);

            event(new MediaCreatedEvent($media));
        }
        return response()->json(["message" => "Update media successfully", "media" => MediaResource::make($media)], 201);
    }
}
