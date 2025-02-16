<?php

namespace App\Http\Controllers\Albums;

use App\Http\Controllers\Controller;
use App\Http\Requests\Albums\AddMediasToAlbumRequest;
use App\Models\AlbumMedia;
use Ramsey\Uuid\Uuid;

class AddMediasToAlbumController extends Controller
{
    public function __invoke(AddMediasToAlbumRequest $request)
    {
        $data = $request->validated();

        $existingRecords = AlbumMedia::where('album_id', $data['album_id'])
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
                'created_at' => now(),
            ];
        }

        AlbumMedia::insert($records);

        return responseWithMessage("Add medias to albums successfully");
    }
}
