<?php

namespace App\Http\Controllers\Medias\Shared;

use App\Models\MediaTag;
use App\Models\Tag;
use Carbon\Carbon;
use Ramsey\Uuid\Uuid;

class MediaHandle
{
    public static function attachTagtoMedia($tags, $mediaId, $userId, $now = null)
    {
        $now = $now ?? Carbon::now()->toDateTimeString();
        $tags = array_map('strtolower', $tags);
        $tagsInDB = Tag::whereIn('tag_name', $tags)->pluck('tag_name', 'id')->toArray();
        $newTags = array_diff($tags, array_values($tagsInDB));
        $tagIds = array_keys($tagsInDB);
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
}
