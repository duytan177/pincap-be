<?php

namespace App\Http\Controllers\Admin\Medias;

use App\Http\Controllers\Controller;
use App\Models\Media;
use App\Exceptions\Admin\MediaException;
use App\Services\ElasticsearchService;
use Illuminate\Support\Facades\Log;

class DeleteAdminMediaController extends Controller
{
    public function __invoke(string $mediaId)
    {
        $media = Media::withoutGlobalScopes()
            ->withTrashed()
            ->find($mediaId);

        if (!$media) {
            throw MediaException::mediaNotFound();
        }

        // Check if media is already deleted
        if ($media->trashed()) {
            throw MediaException::mediaAlreadyDeleted();
        }

        // Soft delete the media
        $media->delete();

        // Update Elasticsearch document to mark as deleted
        try {
            $es = ElasticsearchService::getInstance();
            $index = config('services.elasticsearch.index');
            $es->updateDocument($index, $mediaId, [
                'is_deleted' => true,
                'updated_at' => now()->toDateTimeString(),
            ]);
        } catch (\Exception $e) {
            // Log error but don't fail the request if ES update fails
            Log::error("Failed to update media in Elasticsearch: " . $e->getMessage(), [
                'media_id' => $mediaId,
                'index' => config('services.elasticsearch.index')
            ]);
        }

        return responseWithMessage("Media deleted successfully");
    }
}

