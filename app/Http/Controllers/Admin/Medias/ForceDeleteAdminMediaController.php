<?php

namespace App\Http\Controllers\Admin\Medias;

use App\Http\Controllers\Controller;
use App\Models\Media;
use App\Exceptions\Admin\MediaException;
use App\Services\ElasticsearchService;
use Illuminate\Support\Facades\Log;

class ForceDeleteAdminMediaController extends Controller
{
    public function __invoke(string $mediaId)
    {
        $media = Media::withoutGlobalScopes()
            ->withTrashed()
            ->find($mediaId);

        if (!$media) {
            throw MediaException::mediaNotFound();
        }

        // Force delete from database
        $media->forceDelete();

        // Delete from Elasticsearch
        try {
            $es = ElasticsearchService::getInstance();
            $index = config('services.elasticsearch.index');
            $es->deleteDocument($index, $mediaId);
        } catch (\Exception $e) {
            // Log error but don't fail the request if ES delete fails
            Log::error("Failed to delete media from Elasticsearch: " . $e->getMessage(), [
                'media_id' => $mediaId,
                'index' => config('services.elasticsearch.index')
            ]);
        }

        return responseWithMessage("Media permanently deleted successfully");
    }
}

