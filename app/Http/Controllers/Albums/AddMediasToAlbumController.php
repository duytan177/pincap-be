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
                'added_by_user_id' => $userId, // Track who added this media
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
                $imageCoverUrl = null;

                // Extract first URL if media_url is an array (JSON string or PHP array)
                if (is_string($mediaUrl)) {
                    $decoded = json_decode($mediaUrl, true);
                    if (json_last_error() === JSON_ERROR_NONE && is_array($decoded) && !empty($decoded)) {
                        // If it's a JSON array, get the first element (must be a string)
                        $imageCoverUrl = is_string($decoded[0]) ? $decoded[0] : null;
                    } else {
                        // If it's a plain string URL, use it directly
                        $imageCoverUrl = $mediaUrl;
                    }
                } elseif (is_array($mediaUrl) && !empty($mediaUrl)) {
                    // If it's already a PHP array, get the first element (must be a string)
                    $imageCoverUrl = is_string($mediaUrl[0]) ? $mediaUrl[0] : null;
                }

                // Only set image_cover if we have a valid URL string
                if ($imageCoverUrl) {
                    $album->image_cover = $imageCoverUrl;
                    $album->save();
                }
            }
        }

        return responseWithMessage("Add medias to album successfully");
    }
}
