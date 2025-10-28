<?php

namespace App\Http\Controllers\Albums;

use App\Enums\Album_Media\AlbumRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Albums\AddMediasToAlbumRequest;
use App\Models\Album;
use App\Models\AlbumMedia;
use App\Models\Media;
use Illuminate\Support\Facades\Auth;
use Ramsey\Uuid\Uuid;

class AddMediasToAlbumController extends Controller
{
    public function __invoke(AddMediasToAlbumRequest $request)
    {
        $data = $request->validated();
        $albumId = $data['album_id'];
        $userId = Auth::user()->id;
        $album = Album::findOrFailWithPermission($albumId, $userId, [AlbumRole::OWNER, AlbumRole::EDIT]);

        $existingRecords = AlbumMedia::where('album_id', $albumId)
            ->whereIn('media_id', $data['medias_id'])
            ->pluck('media_id')
            ->toArray();

        $newMediaIds = array_diff($data['medias_id'], $existingRecords);

        if (empty($newMediaIds)) {
            return response()->json([
                'message' => 'All provided media are already associated with this album.',
            ], 422);
        }

        $records = [];
        foreach ($newMediaIds as $mediaId) {
            $records[] = [
                'id' => Uuid::uuid4()->toString(),
                'album_id' => $data['album_id'],
                'media_id' => $mediaId,
                'user_created' => $userId, // Track who added this media
                'created_at' => now(),
            ];
        }

        AlbumMedia::insert($records);

        // If album has no image_cover, set it from the first provided media's URL
        if (empty($album->image_cover) && !empty($newMediaIds)) {
            $firstMediaId = reset($newMediaIds);
            $firstMedia = Media::find($firstMediaId);
            if ($firstMedia && !empty($firstMedia->media_url)) {
                $mediaUrl = $firstMedia->media_url;

                // If media_url is a JSON array string, pick the first element; otherwise use as-is
                if (is_string($mediaUrl)) {
                    $decoded = json_decode($mediaUrl, true);
                    if (json_last_error() === JSON_ERROR_NONE && is_array($decoded) && !empty($decoded)) {
                        $mediaUrl = $decoded[0];
                    }
                }

                $album->image_cover = $mediaUrl;
                $album->save();
            }
        }

        return responseWithMessage("Add medias to albums successfully");
    }
}
