<?php

namespace App\Http\Controllers\Admin\Medias;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\Medias\AdminMediaResource;
use App\Models\Media;
use App\Exceptions\Admin\MediaException;
use App\Services\ElasticsearchService;
use Illuminate\Support\Facades\Log;

class RestoreAdminMediaController extends Controller
{
    public function __invoke(string $mediaId)
    {
        $media = Media::withoutGlobalScopes()
            ->withTrashed()
            ->with(['userOwner:id,first_name,last_name,email,avatar', 'albums:id,album_name,description'])
            ->find($mediaId);

        if (!$media) {
            throw MediaException::mediaNotFound();
        }

        // Check if media is not deleted
        if (!$media->trashed()) {
            throw MediaException::mediaNotDeleted();
        }

        // Restore the media (set deleted_at to null)
        $media->restore();

        // Update Elasticsearch document to make it visible again
        try {
            $es = ElasticsearchService::getInstance();
            $index = config('services.elasticsearch.index');
            $es->updateDocument($index, $mediaId, [
                'is_deleted' => false,
                'updated_at' => now()->toDateTimeString(),
            ]);
        } catch (\Exception $e) {
            // Log error but don't fail the request if ES update fails
            Log::error("Failed to update media in Elasticsearch: " . $e->getMessage(), [
                'media_id' => $mediaId,
                'index' => config('services.elasticsearch.index')
            ]);
        }

        // Refresh counts
        $media->loadCount(['reactions', 'comments', 'albums']);

        return new AdminMediaResource($media->fresh());
    }
}

